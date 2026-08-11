<?php

namespace App\Providers;

use App\Listeners\LogScheduledTask;
use App\Models\Tag;
use App\Services\CronLogService;
use App\Services\NotificationService;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\NativeTransportFactory;

class AppServiceProvider extends ServiceProvider
{
    private const FOOTER_POPULAR_TAGS_LIMIT = 12;

    private const FOOTER_POPULAR_TAGS_CACHE_TTL_SECONDS = 3600;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Singleton so scheduled-task listeners and commands share open-run state.
        $this->app->singleton(CronLogService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PHP native mail transport (same stack as mail() / php.ini sendmail_path).
        Mail::extend('native', function (array $config = []) {
            return (new NativeTransportFactory)->create(
                new Dsn('native', 'default')
            );
        });

        // Persist every scheduled-task run for the admin Cron log.
        Event::listen(ScheduledTaskStarting::class, [LogScheduledTask::class, 'handleStarting']);
        Event::listen(ScheduledTaskFinished::class, [LogScheduledTask::class, 'handleFinished']);
        Event::listen(ScheduledTaskFailed::class, [LogScheduledTask::class, 'handleFailed']);

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
