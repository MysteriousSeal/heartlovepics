<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneTrashedImages extends Command
{
    protected $signature = 'images:prune-trashed {--hours=24 : Permanently delete images soft-deleted longer than this many hours ago}';

    protected $description = 'Permanently delete soft-deleted images (and their files) past the undo grace period';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $cutoff = now()->subHours($hours);

        $images = Image::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->with('additionalImages')
            ->get();

        foreach ($images as $image) {
            foreach ($image->additionalImages as $extra) {
                Storage::disk('public')->delete($extra->file_path);

                if ($extra->thumbnail_path && $extra->thumbnail_path !== $extra->file_path) {
                    Storage::disk('public')->delete($extra->thumbnail_path);
                }

                $extra->delete();
            }

            Storage::disk('public')->delete($image->file_path);

            if ($image->thumbnail_path && $image->thumbnail_path !== $image->file_path) {
                Storage::disk('public')->delete($image->thumbnail_path);
            }

            $image->forceDelete();
        }

        $this->info(count($images)." trashed image(s) permanently removed (older than {$hours}h).");

        return self::SUCCESS;
    }
}
