<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $visits = SiteVisit::query()->with('user')->latest('created_at')->paginate(50);

        return view('admin.analytics.index', compact('visits'));
    }
}
