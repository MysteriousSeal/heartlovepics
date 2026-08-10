<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ImageViewEvent;

class ImageViewService
{
    public function recordDetailView(Image $image): void
    {
        $image->increment('views');
        $this->recordViewEvents([$image->id]);
    }

    /** @param  array<int>  $imageIds */
    private function recordViewEvents(array $imageIds): void
    {
        if ($imageIds === []) {
            return;
        }

        $timestamp = now();

        ImageViewEvent::query()->insert(
            collect($imageIds)->map(fn (int $imageId) => [
                'image_id' => $imageId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->all()
        );
    }
}