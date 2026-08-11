<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Models\ImageLike;
use App\Models\ImageViewEvent;
use App\Services\CronLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

/**
 * Cron job: sprinkle a small batch of synthetic views and likes across
 * public posts so the gallery doesn't look frozen.
 */
class SeedRandomEngagement extends Command
{
    protected $signature = 'engagement:seed-random
                            {--views-min=10 : Minimum total views to add this run}
                            {--views-max=20 : Maximum total views to add this run}
                            {--likes-min=1 : Minimum total likes to add this run}
                            {--likes-max=5 : Maximum total likes to add this run}
                            {--dry-run : Show what would happen without writing}';

    protected $description = 'Add a random batch of views (10–20) and likes (1–5) across public posts';

    public function handle(CronLogService $cronLogs): int
    {
        $viewsMin = max(0, (int) $this->option('views-min'));
        $viewsMax = max($viewsMin, (int) $this->option('views-max'));
        $likesMin = max(0, (int) $this->option('likes-min'));
        $likesMax = max($likesMin, (int) $this->option('likes-max'));
        $dryRun = (bool) $this->option('dry-run');

        $imageIds = Image::query()
            ->publiclyVisible()
            ->pluck('id')
            ->all();

        if ($imageIds === []) {
            $message = 'No public posts found — nothing to boost.';
            $this->warn($message);
            $cronLogs->appendOutput($message, 'engagement:seed-random');

            return self::SUCCESS;
        }

        $viewTotal = random_int($viewsMin, $viewsMax);
        $likeTotal = random_int($likesMin, $likesMax);

        $viewHits = $this->distribute($viewTotal, $imageIds);
        $likeHits = $this->distribute($likeTotal, $imageIds);

        if ($dryRun) {
            $message = "[dry-run] Would add {$viewTotal} view(s) and {$likeTotal} like(s) across ".count($imageIds).' public post(s).';
            $this->info($message);
            $this->line('Views by image: '.json_encode($viewHits));
            $this->line('Likes by image: '.json_encode($likeHits));
            $cronLogs->appendOutput($message, 'engagement:seed-random');

            return self::SUCCESS;
        }

        $viewsAdded = $this->applyViews($viewHits);
        $likesAdded = $this->applyLikes($likeHits);

        $message = "Added {$viewsAdded} view(s) and {$likesAdded} like(s) across public posts.";
        $this->info($message);
        $cronLogs->appendOutput($message, 'engagement:seed-random');

        return self::SUCCESS;
    }

    /**
     * Randomly assign $total hits across the given image IDs.
     *
     * @param  list<int>  $imageIds
     * @return array<int, int> image_id => hit count
     */
    private function distribute(int $total, array $imageIds): array
    {
        $hits = [];

        if ($total <= 0 || $imageIds === []) {
            return $hits;
        }

        for ($i = 0; $i < $total; $i++) {
            $id = $imageIds[array_rand($imageIds)];
            $hits[$id] = ($hits[$id] ?? 0) + 1;
        }

        return $hits;
    }

    /**
     * @param  array<int, int>  $hits
     */
    private function applyViews(array $hits): int
    {
        $added = 0;
        $now = now();
        $events = [];

        foreach ($hits as $imageId => $count) {
            Image::query()->whereKey($imageId)->increment('views', $count);
            $added += $count;

            for ($i = 0; $i < $count; $i++) {
                $events[] = [
                    'image_id' => $imageId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($events !== []) {
            // Chunk in case a future run is larger.
            foreach (array_chunk($events, 500) as $chunk) {
                ImageViewEvent::query()->insert($chunk);
            }
        }

        return $added;
    }

    /**
     * Synthetic likes use unique guest fingerprints so they never collide
     * with real user likes and never trigger notifications.
     *
     * @param  array<int, int>  $hits
     */
    private function applyLikes(array $hits): int
    {
        $added = 0;
        $now = now();

        foreach ($hits as $imageId => $count) {
            for ($i = 0; $i < $count; $i++) {
                try {
                    ImageLike::query()->create([
                        'image_id' => $imageId,
                        'user_id' => null,
                        'fingerprint' => 'cron-'.Str::lower(Str::ulid()),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $added++;
                } catch (Throwable $e) {
                    // Unique fingerprint collision is vanishingly rare; skip and continue.
                    report($e);
                }
            }
        }

        return $added;
    }
}
