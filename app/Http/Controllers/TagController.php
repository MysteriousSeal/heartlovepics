<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function suggest(Request $request, TagService $tags): JsonResponse
    {
        $title = $request->string('title')->trim()->toString();
        $description = $request->string('description')->trim()->toString();

        return response()->json([
            'tags' => $tags->suggest($title, $description),
        ]);
    }
}