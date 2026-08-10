<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\Image;
use App\Models\ImageFile;
use App\Models\User;
use App\Services\ImageConversionService;
use App\Services\TagService;
use App\Support\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserImageController extends Controller
{
    public function __construct(
        private ImageConversionService $imageConversion,
        private TagService $tags,
    ) {}

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfCannotPost()) {
            return $redirect;
        }

        return view('users.images.create');
    }

    public function store(StoreImageRequest $request): RedirectResponse|JsonResponse
    {
        if ($redirect = $this->redirectIfCannotPost($request->user())) {
            return $redirect;
        }

        $converted = $this->imageConversion->storeAsWebp($request->file('image'));

        $isPublished = $this->resolvePublishedState($request);

        $image = Image::create([
            'user_id' => $request->user()?->id,
            'title' => $request->input('title'),
            'slug' => Image::generateUniqueSlug($request->input('title')),
            'description' => HtmlSanitizer::clean($request->input('description')),
            'alt_text' => $request->input('alt_text'),
            'file_path' => $converted['path'],
            'thumbnail_path' => $converted['thumbnail_path'],
            'width' => $converted['width'],
            'height' => $converted['height'],
            'is_published' => $isPublished,
            'is_private' => $request->boolean('is_private'),
            'is_nsfw' => $request->boolean('is_nsfw'),
            'content_warning' => $request->boolean('is_nsfw') ? $request->input('content_warning') : null,
            'artist_name' => $request->input('artist_name'),
        ]);

        $this->tags->syncForImage($image, $request->input('tags', []));
        $this->storeAdditionalImages($image, $request->file('additional_images', []));

        if (! $isPublished && $request->user()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'redirect' => route('users.drafts', $request->user()),
                    'message' => 'Draft saved.',
                ]);
            }

            return redirect()
                ->route('users.drafts', $request->user())
                ->with('success', 'Draft saved.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('profile.images.success', $image),
                'message' => 'Image uploaded successfully.',
            ]);
        }

        return redirect()
            ->route('profile.images.success', $image);
    }

    public function success(Image $image): View
    {
        abort_unless($image->user_id === auth()->id(), 403);

        $image->load('additionalImages');

        return view('users.images.success', compact('image'));
    }

    public function edit(Image $image): View
    {
        $this->authorizeOwner($image);
        $image->load('tags', 'additionalImages');

        return view('users.images.edit', compact('image'));
    }

    public function update(UpdateImageRequest $request, Image $image): RedirectResponse
    {
        $this->authorizeOwner($image);
        $this->authorizeCanPublishExisting($request, $image);

        $data = [
            'title' => $request->input('title'),
            'slug' => Image::generateUniqueSlug($request->input('title'), $image->id),
            'description' => HtmlSanitizer::clean($request->input('description')),
            'alt_text' => $request->input('alt_text'),
            'is_published' => $this->resolvePublishedState($request, $image),
            'is_private' => $request->boolean('is_private'),
            'artist_name' => $request->input('artist_name'),
        ];

        if ($image->is_nsfw_locked) {
            $data['is_nsfw'] = $image->is_nsfw;
            $data['content_warning'] = $image->content_warning;
        } else {
            $isNsfw = $request->boolean('is_nsfw');
            $data['is_nsfw'] = $isNsfw;
            $data['content_warning'] = $isNsfw ? $request->input('content_warning') : null;
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

        // 1) Drop extras the user marked for removal.
        $this->removeAdditionalImages($image, $request->input('remove_images', []));

        // 2) Reorder surviving existing images (and drop the old cover if remove_main).
        $this->applyImageOrder($image, $orderKeys, $removeMain, $oldMainPaths);

        // 3) New cover upload: either replace a removed cover, or demote the current
        //    cover into extras when the user put a new file first without deleting it.
        if ($newCover) {
            $image->refresh();
            $mainStillInOrder = in_array('main', $orderKeys, true) && ! $removeMain;

            if ($mainStillInOrder) {
                // Old cover stays in the set as an extra (user reordered a new file to front).
                $this->demoteCurrentMainToExtra($image);
            } elseif ($removeMain || $image->file_path === $oldMainPaths['file_path']) {
                // Cover was removed, or still the original file and is being replaced.
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

        // 4) Append any other newly uploaded files as extras.
        $this->storeAdditionalImages($image, $newExtras);

        return redirect()
            ->route('images.show', $image->slug)
            ->with('success', 'Image updated successfully.');
    }

    public function publish(Image $image): RedirectResponse
    {
        $this->authorizeOwner($image);
        abort_if(auth()->user()?->is_banned, 403, 'Your account is restricted from publishing images.');

        $image->update(['is_published' => true]);

        return redirect()
            ->route('images.show', $image->slug)
            ->with('success', 'Image published.');
    }

    public function destroy(Image $image): RedirectResponse
    {
        $this->authorizeOwner($image);
        $user = auth()->user();

        // Soft delete only — files and additional images stay on disk until
        // the trash is pruned, so the undo toast can bring everything back.
        $image->delete();

        return redirect()
            ->route('users.show', $user)
            ->with('toast', [
                'message' => 'Image deleted.',
                'action_label' => 'Undo',
                'action_url' => route('profile.images.restore', $image->id),
            ]);
    }

    public function restore(int $image): RedirectResponse
    {
        $trashedImage = Image::withTrashed()->findOrFail($image);

        abort_unless($trashedImage->user_id === auth()->id(), 403);

        $trashedImage->restore();

        return redirect()
            ->route('images.show', $trashedImage->slug)
            ->with('success', 'Image restored.');
    }

    private function resolvePublishedState(StoreImageRequest|UpdateImageRequest $request, ?Image $image = null): bool
    {
        if (! $request->user() && $request->input('publish_action') === 'draft') {
            return true;
        }

        if ($request->filled('publish_action')) {
            return $request->input('publish_action') === 'publish';
        }

        if ($request->has('is_published')) {
            return $request->boolean('is_published');
        }

        return $image?->is_published ?? true;
    }

    private function authorizeOwner(Image $image): void
    {
        abort_unless($image->user_id !== null && $image->user_id === auth()->id(), 403);
    }

    private function redirectIfCannotPost(?User $user = null): ?RedirectResponse
    {
        $user ??= auth()->user();

        if ($user?->is_banned) {
            return redirect()->route('home');
        }

        return null;
    }

    private function authorizeCanPublishExisting(UpdateImageRequest $request, Image $image): void
    {
        $user = $request->user();

        if (! $user?->is_banned || $image->is_published) {
            return;
        }

        if ($this->resolvePublishedState($request, $image)) {
            abort(403, 'Your account is restricted from publishing images.');
        }
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
     * @param  array{file_path: ?string, thumbnail_path: ?string}  $oldMainPaths
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
            // Main removed with no surviving existing extras — cover will come from a new upload.
            if ($removeMain && ! $mainIncluded) {
                $this->deleteStoredPaths($oldMainPaths['file_path'] ?? null, $oldMainPaths['thumbnail_path'] ?? null);
            }

            return;
        }

        $previousMainPath = $image->file_path;
        $previousMainThumb = $image->thumbnail_path;

        // Reassign paths: first becomes cover, remainder become ordered extras.
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

        // If the old cover was removed (not kept in the ordered set), delete its files.
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