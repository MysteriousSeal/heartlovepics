<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /** @var list<string> */
    private const TAGS = [
        'nature',
        'sunset',
        'portrait',
        'landscape',
        'urban',
        'architecture',
        'street',
        'wildlife',
        'flowers',
        'ocean',
        'mountains',
        'forest',
        'desert',
        'night',
        'golden hour',
        'monochrome',
        'abstract',
        'minimal',
        'vintage',
        'travel',
        'candid',
        'moody',
        'bright',
        'cozy',
        'summer',
        'winter',
        'spring',
        'autumn',
        'macro',
        'love',
    ];

    public function run(): void
    {
        $tagIds = collect(self::TAGS)
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => Tag::normalizeName($name)])->id)
            ->values()
            ->all();

        Image::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->each(function (Image $image) use ($tagIds): void {
                $count = random_int(3, 5);

                $selectedTagIds = collect($tagIds)
                    ->shuffle()
                    ->take($count)
                    ->values()
                    ->all();

                $image->tags()->sync($selectedTagIds);
            });
    }
}