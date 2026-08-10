<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function toggle(Request $request, User $user, FollowService $follows): JsonResponse
    {
        return response()->json($follows->toggle($user, $request));
    }
}