<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AdminActivityLog::query()
            ->with('admin')
            ->latest();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_label', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        $logs = $query->paginate(30)->withQueryString();

        $actions = [
            AdminActivityLog::ACTION_USER_BANNED => 'User banned',
            AdminActivityLog::ACTION_USER_UNBANNED => 'User unbanned',
            AdminActivityLog::ACTION_USER_DELETED => 'User deleted',
            AdminActivityLog::ACTION_IMAGE_UPDATED => 'Image updated',
            AdminActivityLog::ACTION_IMAGE_DELETED => 'Image deleted',
            AdminActivityLog::ACTION_COMMENT_DELETED => 'Comment deleted',
            AdminActivityLog::ACTION_ARTIST_CREATED => 'Artist created',
            AdminActivityLog::ACTION_ARTIST_UPDATED => 'Artist updated',
            AdminActivityLog::ACTION_ARTIST_DELETED => 'Artist deleted',
            AdminActivityLog::ACTION_CONFIGURATION_UPDATED => 'Configuration updated',
            AdminActivityLog::ACTION_CONTACT_MESSAGE_DELETED => 'Contact message deleted',
        ];

        return view('admin.activity.index', compact('logs', 'actions'));
    }
}
