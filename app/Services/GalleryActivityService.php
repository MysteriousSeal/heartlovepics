<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Image;
use App\Models\ImageLike;
use App\Models\ImageViewEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GalleryActivityService
{
    private const CACHE_TTL_SECONDS = 300;

    /**
     * Short-lived on purpose: this widget is shown on every gallery page load and
     * is meant to feel live. A few seconds of caching just absorbs concurrent
     * requests without making the counters look frozen.
     */
    private const ACTIVITY_24H_CACHE_TTL_SECONDS = 30;

    public function totalPublishedImages(): int
    {
        return Image::query()
            ->where('is_published', true)
            ->count();
    }

    /** @return array{posts: int, likes: int, comments: int, views: int} */
    public function statsForLast24Hours(): array
    {
        return Cache::remember('gallery.activity_24h', self::ACTIVITY_24H_CACHE_TTL_SECONDS, function () {
            $since = now()->subDay();

            return [
                'posts' => Image::query()
                    ->where('is_published', true)
                    ->where('created_at', '>=', $since)
                    ->count(),
                'likes' => ImageLike::query()
                    ->where('created_at', '>=', $since)
                    ->count(),
                'comments' => Comment::query()
                    ->where('created_at', '>=', $since)
                    ->count(),
                'views' => ImageViewEvent::query()
                    ->where('created_at', '>=', $since)
                    ->count(),
            ];
        });
    }

    /**
     * Daily counts for the last N days (oldest first, today included), zero
     * filled so every day has an entry even with no activity.
     *
     * @return array{posts: list<int>, likes: list<int>, comments: list<int>, views: list<int>, labels: list<string>}
     */
    public function dailyTrend(int $days = 30): array
    {
        return Cache::remember("gallery.trend_{$days}", self::CACHE_TTL_SECONDS, function () use ($days) {
            $since = now()->subDays($days - 1)->startOfDay();

            return [
                'posts' => $this->fillDailyCounts(
                    Image::query()->where('is_published', true),
                    $since,
                    $days,
                ),
                'likes' => $this->fillDailyCounts(ImageLike::query(), $since, $days),
                'comments' => $this->fillDailyCounts(Comment::query(), $since, $days),
                'views' => $this->fillDailyCounts(ImageViewEvent::query(), $since, $days),
                'labels' => $this->dayLabels($since, $days),
            ];
        });
    }

    /** @return list<int> */
    private function fillDailyCounts(Builder $query, Carbon $since, int $days): array
    {
        $counts = $query
            ->where('created_at', '>=', $since)
            ->selectRaw("strftime('%Y-%m-%d', created_at) as day, count(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        $series = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $since->copy()->addDays($i)->format('Y-m-d');
            $series[] = (int) ($counts[$day] ?? 0);
        }

        return $series;
    }

    /** @return list<string> */
    private function dayLabels(Carbon $since, int $days): array
    {
        $labels = [];

        for ($i = 0; $i < $days; $i++) {
            $labels[] = $since->copy()->addDays($i)->format('M j');
        }

        return $labels;
    }

    /**
     * Same shape as dailyTrend(), scoped to a single creator's own images —
     * powers the per-user stats page. Not cached: personalized and cheap
     * compared to the site-wide aggregate.
     *
     * @return array{posts: list<int>, likes: list<int>, comments: list<int>, views: list<int>, labels: list<string>}
     */
    public function dailyTrendForUser(User $user, int $days = 30): array
    {
        $since = now()->subDays($days - 1)->startOfDay();

        return [
            'posts' => $this->fillDailyCounts(
                Image::query()->where('user_id', $user->id),
                $since,
                $days,
            ),
            'likes' => $this->fillDailyCounts(
                ImageLike::query()->whereHas('image', fn (Builder $query) => $query->where('user_id', $user->id)),
                $since,
                $days,
            ),
            'comments' => $this->fillDailyCounts(
                Comment::query()->whereHas('image', fn (Builder $query) => $query->where('user_id', $user->id)),
                $since,
                $days,
            ),
            'views' => $this->fillDailyCounts(
                ImageViewEvent::query()->whereHas('image', fn (Builder $query) => $query->where('user_id', $user->id)),
                $since,
                $days,
            ),
            'labels' => $this->dayLabels($since, $days),
        ];
    }

    /** @return array{uploads: int, views: int, likes: int, comments: int} */
    public function allTimeStatsForUser(User $user): array
    {
        return [
            'uploads' => Image::where('user_id', $user->id)->where('is_published', true)->count(),
            'views' => (int) Image::where('user_id', $user->id)->sum('views'),
            'likes' => ImageLike::whereHas('image', fn (Builder $query) => $query->where('user_id', $user->id))->count(),
            'comments' => Comment::whereHas('image', fn (Builder $query) => $query->where('user_id', $user->id))->count(),
        ];
    }

    public function defaultOgImageUrl(): ?string
    {
        return Cache::remember('gallery.default_og_image', self::CACHE_TTL_SECONDS, function () {
            $image = Image::query()
                ->where('is_published', true)
                ->where('is_private', false)
                ->latest()
                ->first();

            return $image?->direct_url;
        });
    }
}