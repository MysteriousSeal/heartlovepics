<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Tag;
use App\Services\ImageConversionService;
use App\Services\TagService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FakeImageSeeder extends Seeder
{
    private const COUNT = 200;

    private const SIZE_TEMPLATES = [
        [1200, 800],
        [800, 1200],
        [900, 900],
        [1400, 700],
        [600, 1000],
        [1000, 600],
        [750, 1100],
        [1100, 750],
        [500, 900],
        [1600, 600],
        [700, 700],
        [950, 1300],
        [1300, 850],
        [850, 500],
        [1050, 1050],
        [650, 950],
        [1150, 650],
        [800, 1400],
        [1500, 800],
        [720, 1080],
    ];

    private const PALETTES = [
        [235, 228, 220],
        [210, 218, 228],
        [228, 218, 210],
        [218, 228, 218],
        [228, 210, 218],
        [200, 210, 220],
        [220, 210, 200],
        [215, 225, 215],
        [225, 220, 230],
        [230, 225, 215],
        [198, 212, 226],
        [224, 214, 204],
        [206, 220, 210],
        [214, 206, 224],
        [220, 216, 208],
    ];

    private const TITLE_WORDS = [
        'Morning', 'Quiet', 'Soft', 'Golden', 'Still', 'Urban', 'Fading', 'Open',
        'Winter', 'Coastal', 'Hidden', 'Late', 'Misty', 'Stone', 'Forest', 'City',
        'Pale', 'Desert', 'River', 'Evening', 'Silent', 'Distant', 'Gentle', 'Warm',
        'Cool', 'Bright', 'Dim', 'Clear', 'Hazy', 'Deep', 'Light', 'Wild', 'Drifting',
        'Fleeting', 'Rustic', 'Polished', 'Weathered', 'Fragile', 'Bold', 'Tender',
    ];

    private const TITLE_NOUNS = [
        'light', 'horizon', 'shadows', 'hour', 'waters', 'calm', 'dusk', 'fields',
        'pause', 'breeze', 'garden', 'afternoon', 'valley', 'sky', 'edge', 'reflections',
        'bloom', 'haze', 'bend', 'glow', 'path', 'ridge', 'shore', 'meadow',
        'lane', 'view', 'tone', 'frame', 'scene', 'moment', 'study', 'fragment',
        'whisper', 'passage', 'current', 'distance', 'balance', 'silence', 'texture', 'color',
    ];

    public function run(ImageConversionService $imageConversion, TagService $tagService): void
    {
        Storage::disk('public')->makeDirectory('images');
        Storage::disk('public')->makeDirectory('images/thumbnails');

        $tagNames = Tag::query()->orderBy('name')->pluck('name')->all();

        if ($tagNames === []) {
            $this->call(TagSeeder::class);
            $tagNames = Tag::query()->orderBy('name')->pluck('name')->all();
        }

        $existingCount = Image::query()->count();

        for ($index = 0; $index < self::COUNT; $index++) {
            $seedIndex = $existingCount + $index;
            $title = $this->titleFor($seedIndex);
            $description = fake()->sentence(random_int(10, 18));
            [$width, $height] = $this->sizeFor($seedIndex);

            $tempPath = $this->generateTempImage($width, $height, $seedIndex);

            try {
                $uploaded = new UploadedFile(
                    $tempPath,
                    'fake-'.$seedIndex.'.jpg',
                    'image/jpeg',
                    null,
                    true,
                );

                $converted = $imageConversion->storeAsWebp($uploaded);

                $image = Image::create([
                    'title' => $title,
                    'slug' => Image::generateUniqueSlug($title),
                    'description' => $description,
                    'alt_text' => $title.' photograph',
                    'file_path' => $converted['path'],
                    'thumbnail_path' => $converted['thumbnail_path'],
                    'width' => $converted['width'],
                    'height' => $converted['height'],
                    'is_published' => true,
                    'is_nsfw' => false,
                ]);

                $selectedTags = collect($tagNames)
                    ->shuffle()
                    ->take(random_int(3, 5))
                    ->values()
                    ->all();

                $tagService->syncForImage($image, $selectedTags);
            } finally {
                @unlink($tempPath);
            }
        }
    }

    private function titleFor(int $index): string
    {
        $word = self::TITLE_WORDS[$index % count(self::TITLE_WORDS)];
        $noun = self::TITLE_NOUNS[intdiv($index, count(self::TITLE_WORDS)) % count(self::TITLE_NOUNS)];

        return $word.' '.$noun;
    }

    /** @return array{0: int, 1: int} */
    private function sizeFor(int $index): array
    {
        $template = self::SIZE_TEMPLATES[$index % count(self::SIZE_TEMPLATES)];
        $variation = ($index % 5) * 40;

        if ($template[0] >= $template[1]) {
            return [$template[0] + $variation, $template[1] + (int) ($variation / 2)];
        }

        return [$template[0] + (int) ($variation / 2), $template[1] + $variation];
    }

    private function generateTempImage(int $width, int $height, int $index): string
    {
        $canvas = imagecreatetruecolor($width, $height);
        $palette = self::PALETTES[$index % count(self::PALETTES)];
        $bg = imagecolorallocate($canvas, $palette[0], $palette[1], $palette[2]);
        imagefill($canvas, 0, 0, $bg);

        $accent = imagecolorallocate(
            $canvas,
            max(0, $palette[0] - 35),
            max(0, $palette[1] - 35),
            max(0, $palette[2] - 35),
        );

        $blockW = (int) ($width * 0.35);
        $blockH = (int) ($height * 0.25);
        $blockX = (int) (($width - $blockW) / 2);
        $blockY = (int) (($height - $blockH) / 2);
        imagefilledrectangle($canvas, $blockX, $blockY, $blockX + $blockW, $blockY + $blockH, $accent);

        $textColor = imagecolorallocate($canvas, 80, 80, 80);
        $label = $width.'×'.$height;
        $font = 5;
        $textW = imagefontwidth($font) * strlen($label);
        imagestring($canvas, $font, (int) (($width - $textW) / 2), $blockY + $blockH + 20, $label, $textColor);

        $tempPath = tempnam(sys_get_temp_dir(), 'hlp_fake_').'.jpg';
        imagejpeg($canvas, $tempPath, 88);
        imagedestroy($canvas);

        return $tempPath;
    }
}