<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Comment;
use App\Models\Image;
use App\Models\ImageLike;
use App\Services\GalleryActivityService;
use App\Services\VisitCounterService;
use App\Support\Sparkline;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(GalleryActivityService $activity, VisitCounterService $visits): View
    {
        $stats = [
            'total' => Image::count(),
            'published' => Image::where('is_published', true)->count(),
            'draft' => Image::where('is_published', false)->count(),
            'comments' => Comment::count(),
            'likes' => ImageLike::count(),
            'visits' => $visits->total(),
        ];

        $activityStats = $activity->statsForLast24Hours();
        $recentImages = Image::with('user')->latest()->take(6)->get();
        $recentActivity = AdminActivityLog::with('admin')->latest()->take(6)->get();

        $trend = $activity->dailyTrend(30);
        $trendCharts = [
            'posts' => $this->sparklineFor($trend['posts']),
            'likes' => $this->sparklineFor($trend['likes']),
            'comments' => $this->sparklineFor($trend['comments']),
            'views' => $this->sparklineFor($trend['views']),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'activityStats',
            'recentImages',
            'recentActivity',
            'trendCharts',
        ));
    }

    /** @param  list<int>  $series
     *  @return array{points: string, area: string, total: int, max: int} */
    private function sparklineFor(array $series): array
    {
        return [
            'points' => Sparkline::points($series),
            'area' => Sparkline::areaPath($series),
            'total' => array_sum($series),
            'max' => $series === [] ? 0 : max($series),
        ];
    }
}