<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateImageRequest;
use App\Models\AdminActivityLog;
use App\Models\Image;
use App\Models\ImageFile;
use App\Services\ImageConversionService;
use App\Services\TagService;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ImageController extends Controller
{
    public function __construct(
        private TagService $tags,
        private ImageConversionService $imageConversion,
    ) {}

    public function index(Request $request): View
    {
        $query = Image::query()
            ->with(['user', 'artist', 'additionalImages'])
            ->withCount(['likes', 'comments', 'collections']);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->input('status') === 'published');
        }

        if ($request->filled('nsfw')) {
            $query->where('is_nsfw', $request->input('nsfw') === 'yes');
        }

        $sort = $request->input('sort');

        match ($sort) {
            'oldest' => $query->oldest(),
            'views' => $query->orderByDesc('views'),
            'likes' => $query->orderByDesc('likes_count'),
            'comments' => $query->orderByDesc('comments_count'),
            default => $query->latest(),
        };

        $images = $query->paginate(30)->withQueryString();

        return view('admin.images.index', compact('images', 'sort'));
    }

    public function edit(Image $image): View
    {
        $image->load(['tags', 'additionalImages']);

        return view('admin.images.edit', compact('image'));
    }

    public function update(UpdateImageRequest $request, Image $image): RedirectResponse
    {
        $isNsfw = $request->boolean('is_nsfw');
        $wasPublished = $image->is_published;
        $wasNsfw = $image->is_nsfw;

        $data = [
            'title' => $request->input('title'),
            'slug' => Image::generateUniqueSlug($request->input('title'), $image->id),
            'description' => HtmlSanitizer::clean($request->input('description')),
            'alt_text' => $request->input('alt_text'),
            'is_published' => $request->boolean('is_published'),
            'is_nsfw' => $isNsfw,
            'is_nsfw_locked' => $image->is_nsfw_locked || $isNsfw !== $image->is_nsfw,
            'content_warning' => $isNsfw ? $request->input('content_warning') : null,
            'artist_name' => $request->input('artist_name'),
        ];

        if ($request->boolean('is_private') || $request->has('is_private')) {
            $data['is_private'] = $request->boolean('is_private');
        }

        $oldMainPaths = [
            'file_path' => $image->file_path,
            'thumbnail_path' => $image->thumbnail_path,
            'width' => $image->width,
            'height' => $image->height,
        ];
        $removeMain = $request->boolean('remove_main');
        $orderKeys = $this->parseImageOrder($request->input('image_order'));
        $newCover = $request->file('image');
        $newExtras = array_values(array_filter($request->file('additional_images', []) ?? []));

        $image->update($data);

        $this->tags->syncForImage($image, $request->input('tags', []));
        $this->removeAdditionalImages($image, $request->input('remove_images', []));
        $this->applyImageOrder($image, $orderKeys, $removeMain, $oldMainPaths);

        if ($newCover) {
            $image->refresh();
            $mainStillInOrder = in_array('main', $orderKeys, true) && ! $removeMain;

            if ($mainStillInOrder) {
                $this->demoteCurrentMainToExtra($image);
            } elseif ($removeMain || $image->file_path === $oldMainPaths['file_path']) {
                if ($image->file_path === $oldMainPaths['file_path']) {
                    $this->deleteStoredPaths($oldMainPaths['file_path'], $oldMainPaths['thumbnail_path']);
                }
            }

            $converted = $this->imageConversion->storeAsWebp($newCover);
            $image->forceFill([
                'file_path' => $converted['path'],
                'thumbnail_path' => $converted['thumbnail_path'],
                'width' => $converted['width'],
                'height' => $converted['height'],
            ])->save();
        }

        $this->storeAdditionalImages($image, $newExtras);

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_IMAGE_UPDATED,
            $this->describeImageUpdate($image->fresh(), $wasPublished, $wasNsfw, $request),
            'image',
            $image->id,
            $image->title,
        );

        return redirect()
            ->route('admin.images.index')
            ->with('success', 'Image updated successfully.');
    }

    public function destroy(Image $image): RedirectResponse
    {
        $title = $image->title;
        $imageId = $image->id;

        $this->removeAdditionalImages($image, $image->additionalImages()->pluck('id')->all());
        $this->deleteImageFiles($image);
        // Files are already gone at this point, so this must be a real, final
        // delete — a soft delete here would leave a trashed row with no files
        // behind, which the original owner could then "restore" into a broken post.
        $image->forceDelete();

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_IMAGE_DELETED,
            'Deleted image "'.$title.'"',
            'image',
            $imageId,
            $title,
        );

        return redirect()
            ->route('admin.images.index')
            ->with('success', 'Image deleted successfully.');
    }

    private function describeImageUpdate(Image $image, bool $wasPublished, bool $wasNsfw, UpdateImageRequest $request): string
    {
        $changes = [];

        if ($wasPublished !== $image->is_published) {
            $changes[] = $image->is_published ? 'published' : 'unpublished';
        }

        if ($wasNsfw !== $image->is_nsfw) {
            $changes[] = $image->is_nsfw ? 'marked NSFW' : 'marked SFW';
        }

        if ($request->hasFile('image') || $request->hasFile('additional_images') || $request->filled('remove_images') || $request->filled('image_order')) {
            $changes[] = 'updated images';
        }

        $summary = $changes === [] ? 'edited' : implode(', ', $changes);

        return 'Updated image "'.$image->title.'" ('.$summary.')';
    }

    private function deleteImageFiles(Image $image): void
    {
        Storage::disk('public')->delete($image->file_path);

        if ($image->thumbnail_path && $image->thumbnail_path !== $image->file_path) {
            Storage::disk('public')->delete($image->thumbnail_path);
        }
    }

    /** @param  array<int, \Illuminate\Http\UploadedFile|null>  $files */
    private function storeAdditionalImages(Image $image, array $files): void
    {
        $files = array_values(array_filter($files));

        if ($files === []) {
            return;
        }

        $position = (int) $image->additionalImages()->max('position');

        foreach ($files as $file) {
            $position++;
            $converted = $this->imageConversion->storeAsWebp($file);

            ImageFile::create([
                'image_id' => $image->id,
                'file_path' => $converted['path'],
                'thumbnail_path' => $converted['thumbnail_path'],
                'width' => $converted['width'],
                'height' => $converted['height'],
                'position' => $position,
            ]);
        }
    }

    /** @return array<int, string> */
    private function parseImageOrder(mixed $order): array
    {
        if (is_array($order)) {
            return array_values(array_filter(array_map('strval', $order)));
        }

        if (! is_string($order) || trim($order) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $order))));
    }

    /**
     * @param  array<int, string>  $orderKeys
     * @param  array{file_path: ?string, thumbnail_path: ?string, width?: ?int, height?: ?int}  $oldMainPaths
     */
    private function applyImageOrder(Image $image, array $orderKeys, bool $removeMain = false, array $oldMainPaths = []): void
    {
        if ($orderKeys === [] && ! $removeMain) {
            return;
        }

        $image->refresh();
        $existing = $image->additionalImages()->get()->keyBy(fn (ImageFile $file) => (string) $file->id);

        $slots = [];
        $mainIncluded = false;

        foreach ($orderKeys as $key) {
            if ($key === 'main') {
                if ($removeMain) {
                    continue;
                }

                $mainIncluded = true;
                $slots[] = [
                    'file_path' => $image->file_path,
                    'thumbnail_path' => $image->thumbnail_path,
                    'width' => $image->width,
                    'height' => $image->height,
                ];

                continue;
            }

            if (! $existing->has($key)) {
                continue;
            }

            $file = $existing->get($key);
            $slots[] = [
                'file_path' => $file->file_path,
                'thumbnail_path' => $file->thumbnail_path,
                'width' => $file->width,
                'height' => $file->height,
            ];
        }

        if ($slots === []) {
            if ($removeMain && ! $mainIncluded) {
                $this->deleteStoredPaths($oldMainPaths['file_path'] ?? null, $oldMainPaths['thumbnail_path'] ?? null);
            }

            return;
        }

        $previousMainPath = $image->file_path;
        $previousMainThumb = $image->thumbnail_path;

        $image->additionalImages()->delete();

        $cover = array_shift($slots);
        $image->forceFill([
            'file_path' => $cover['file_path'],
            'thumbnail_path' => $cover['thumbnail_path'],
            'width' => $cover['width'],
            'height' => $cover['height'],
        ])->save();

        foreach (array_values($slots) as $index => $slot) {
            ImageFile::create([
                'image_id' => $image->id,
                'file_path' => $slot['file_path'],
                'thumbnail_path' => $slot['thumbnail_path'],
                'width' => $slot['width'],
                'height' => $slot['height'],
                'position' => $index + 1,
            ]);
        }

        if ($removeMain && ! $mainIncluded) {
            $image->refresh();
            $pathsInUse = collect([$image->file_path])
                ->merge($image->additionalImages()->pluck('file_path'))
                ->all();

            if (! in_array($previousMainPath, $pathsInUse, true)) {
                $this->deleteStoredPaths($previousMainPath, $previousMainThumb);
            }
        }
    }

    private function demoteCurrentMainToExtra(Image $image): void
    {
        $position = (int) $image->additionalImages()->max('position') + 1;

        ImageFile::create([
            'image_id' => $image->id,
            'file_path' => $image->file_path,
            'thumbnail_path' => $image->thumbnail_path,
            'width' => $image->width,
            'height' => $image->height,
            'position' => max(1, $position),
        ]);
    }

    private function deleteStoredPaths(?string $filePath, ?string $thumbnailPath): void
    {
        if ($filePath) {
            Storage::disk('public')->delete($filePath);
        }

        if ($thumbnailPath && $thumbnailPath !== $filePath) {
            Storage::disk('public')->delete($thumbnailPath);
        }
    }

    /** @param  array<int, int|string>  $ids */
    private function removeAdditionalImages(Image $image, array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $files = $image->additionalImages()->whereIn('id', $ids)->get();

        foreach ($files as $file) {
            Storage::disk('public')->delete($file->file_path);

            if ($file->thumbnail_path && $file->thumbnail_path !== $file->file_path) {
                Storage::disk('public')->delete($file->thumbnail_path);
            }

            $file->delete();
        }
    }
}
