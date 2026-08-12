<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parody;
use App\Support\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParodyApiController extends Controller
{
    /** Upsert by name — creates the parody record if it doesn't exist yet. */
    public function update(Request $request, string $name): JsonResponse
    {
        $validated = $request->validate([
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        if (array_key_exists('description', $validated)) {
            $validated['description'] = HtmlSanitizer::clean($validated['description']);
        }

        $parody = Parody::query()->firstOrNew(['name' => trim($name)]);
        $parody->forceFill($validated)->save();

        return response()->json([
            'id' => $parody->id,
            'name' => $parody->name,
            'description' => $parody->description,
        ]);
    }
}
