<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtistApiController extends Controller
{
    /** Upsert by name — creates the artist record if it doesn't exist yet. */
    public function update(Request $request, string $name): JsonResponse
    {
        $validated = $request->validate([
            'deviantart_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'furaffinity_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'patreon_url' => ['sometimes', 'nullable', 'url', 'max:255'],
        ]);

        $artist = Artist::query()->firstOrNew(['name' => trim($name)]);
        $artist->forceFill($validated)->save();

        return response()->json([
            'id' => $artist->id,
            'name' => $artist->name,
            'deviantart_url' => $artist->deviantart_url,
            'furaffinity_url' => $artist->furaffinity_url,
            'patreon_url' => $artist->patreon_url,
        ]);
    }
}
