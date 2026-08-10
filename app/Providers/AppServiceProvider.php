<?php

namespace App\Providers;

use App\Models\Tag;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private const FOOTER_POPULAR_TAGS_LIMIT = 12;

    private const FOOTER_POPULAR_TAGS_CACHE_TTL_SECONDS = 3600;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.site-auth', function ($view) {
            $user = auth()->user();
            $unreadNotificationCount = $user
                ? app(NotificationService::class)->unreadCount($user)
                : 0;

            $view->with('unreadNotificationCount', $unreadNotificationCount);
        });

        View::composer('partials.site-footer', function ($view) {
            $footerPopularTags = Cache::remember(
                'footer.popular_tags',
                self::FOOTER_POPULAR_TAGS_CACHE_TTL_SECONDS,
                function () {
                    return Tag::query()
                        ->withCount(['images' => fn ($query) => $query->publiclyVisible()])
                        ->whereHas('images', fn ($query) => $query->publiclyVisible())
                        ->orderByDesc('images_count')
                        ->orderBy('name')
                        ->limit(self::FOOTER_POPULAR_TAGS_LIMIT)
                        ->get(['id', 'name'])
                        ->map(fn (Tag $tag) => [
                            'name' => $tag->name,
                            'count' => (int) $tag->images_count,
                        ])
                        ->values()
                        ->all();
                }
            );

            $view->with('footerPopularTags', $footerPopularTags);
        });
    }
}
