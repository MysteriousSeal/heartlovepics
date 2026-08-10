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
    public function __construct(private TagService $tags) {}

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
