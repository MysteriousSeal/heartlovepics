<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Services\TagService;
use App\Support\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageApiController extends Controller
{
    private const PER_PAGE = 50;

    public function __construct(private TagService $tags) {}

    /**
     * Paginated listing of every post's details, newest first — same shape
     * as show(), 50 per page. Use ?page=N to move through the set.
     */
    public function index(): JsonResponse
    {
        $images = Image::query()
            ->with(['tags', 'artist', 'user', 'additionalImages'])
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE);

        return response()->json([
            'data' => $images->getCollection()->map(fn (Image $image) => $this->serialize($image)),
            'meta' => [
                'current_page' => $images->currentPage(),
                'last_page' => $images->lastPage(),
                'per_page' => $images->perPage(),
                'total' => $images->total(),
            ],
        ]);
    }

    /**
     * Looks up by numeric ID when the identifier is all digits, by slug
     * otherwise — so the same route works for either.
     */
    public function show(string $idOrSlug): JsonResponse
    {
        $image = ctype_digit($idOrSlug)
            ? Image::findOrFail($idOrSlug)
            : Image::where('slug', $idOrSlug)->firstOrFail();

        $image->load(['tags', 'artist', 'user', 'additionalImages']);

        return response()->json($this->serialize($image));
    }

    private function serialize(Image $image): array
    {
        return [
            'id' => $image->id,
            'slug' => $image->slug,
            'title' => $image->title,
            'description' => $image->description,
            'alt_text' => $image->alt_text,
            'is_published' => $image->is_published,
            'is_private' => $image->is_private,
            'is_nsfw' => $image->is_nsfw,
            'is_nsfw_locked' => $image->is_nsfw_locked,
            'content_warning' => $image->content_warning,
            'artist_name' => $image->artist_name,
            'artist_links' => $image->artist ? [
                'deviantart_url' => $image->artist->deviantart_url,
                'furaffinity_url' => $image->artist->furaffinity_url,
                'patreon_url' => $image->artist->patreon_url,
            ] : null,
            'tags' => $image->tags->pluck('name'),
            'user' => $image->user ? [
                'id' => $image->user->id,
                'username' => $image->user->username,
            ] : null,
            'url' => $image->direct_url,
            'thumbnail_url' => $image->thumbnail_direct_url,
            'width' => $image->width,
            'height' => $image->height,
            'file_extension' => $image->fileExtension(),
            'file_size' => $image->fileSize(),
            'views' => $image->views,
            'additional_images' => $image->additionalImages->map(fn ($file) => [
                'id' => $file->id,
                'url' => url('/storage/'.$file->file_path),
            ]),
            'created_at' => $image->created_at,
            'updated_at' => $image->updated_at,
        ];
    }

    /**
     * Partial metadata update for a post — description, tags, artist credit,
     * NSFW flagging. Not a replacement for the full admin edit form: this
     * intentionally can't touch the image files, title, or publish state.
     * Artist links live on the Artist record now (see ArtistApiController),
     * not per-post — this only sets which artist a post credits.
     */
    public function update(Request $request, Image $image): JsonResponse
    {
        $validated = $request->validate([
            'description' => ['sometimes', 'nullable', 'string'],
            'alt_text' => ['sometimes', 'nullable', 'string', 'max:125'],
            'is_nsfw' => ['sometimes', 'boolean'],
            'content_warning' => ['sometimes', 'nullable', 'string', 'max:100'],
            'artist_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'tags' => ['sometimes', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50', 'regex:/^[\pL\pN\s\-]+$/u'],
        ]);

        if (array_key_exists('description', $validated)) {
            $validated['description'] = HtmlSanitizer::clean($validated['description']);
        }

        $tags = $validated['tags'] ?? null;
        unset($validated['tags']);

        if ($validated !== []) {
            $image->forceFill($validated)->save();
        }

        if ($tags !== null) {
            $this->tags->syncForImage($image, $tags);
        }

        $image->refresh()->load(['tags', 'artist']);

        return response()->json([
            'id' => $image->id,
            'slug' => $image->slug,
            'title' => $image->title,
            'description' => $image->description,
            'alt_text' => $image->alt_text,
            'is_nsfw' => $image->is_nsfw,
            'content_warning' => $image->content_warning,
            'artist_name' => $image->artist_name,
            'artist_links' => $image->artist ? [
                'deviantart_url' => $image->artist->deviantart_url,
                'furaffinity_url' => $image->artist->furaffinity_url,
                'patreon_url' => $image->artist->patreon_url,
            ] : null,
            'tags' => $image->tags->pluck('name'),
        ]);
    }
}
