<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\ImageConversionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateImageThumbnails extends Command
{
    protected $signature = 'images:generate-thumbnails';

    protected $description = 'Generate thumbnails and sync dimensions for existing images';

    public function handle(ImageConversionService $conversion): int
    {
        $images = Image::query()->orderBy('id')->get();
        $processed = 0;
        $failed = 0;

        foreach ($images as $image) {
            if (! Storage::disk('public')->exists($image->file_path)) {
                $this->warn("Skipping missing file: {$image->file_path} (image #{$image->id})");
                $failed++;

                continue;
            }

            try {
                if ($image->thumbnail_path && $image->thumbnail_path !== $image->file_path) {
                    Storage::disk('public')->delete($image->thumbnail_path);
                }

                $result = $conversion->generateThumbnailForFile(
                    $image->file_path,
                    basename($image->file_path)
                );

                $image->update([
                    'thumbnail_path' => $result['thumbnail_path'],
                    'width' => $result['width'],
                    'height' => $result['height'],
                ]);

                $processed++;
            } catch (\Throwable $exception) {
                $this->error("Failed for image #{$image->id}: {$exception->getMessage()}");
                $failed++;
            }
        }

        $this->info("Processed {$processed} images.".($failed > 0 ? " {$failed} failed." : ''));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}