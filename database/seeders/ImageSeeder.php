<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\ImageLike;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageSeeder extends Seeder
{
    private const COUNT = 100;

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
    ];

    private const TITLE_WORDS = [
        'Morning', 'Quiet', 'Soft', 'Golden', 'Still', 'Urban', 'Fading', 'Open',
        'Winter', 'Coastal', 'Hidden', 'Late', 'Misty', 'Stone', 'Forest', 'City',
        'Pale', 'Desert', 'River', 'Evening', 'Silent', 'Distant', 'Gentle', 'Warm',
        'Cool', 'Bright', 'Dim', 'Clear', 'Hazy', 'Deep', 'Light', 'Wild',
    ];

    private const TITLE_NOUNS = [
        'light', 'horizon', 'shadows', 'hour', 'waters', 'calm', 'dusk', 'fields',
        'pause', 'breeze', 'garden', 'afternoon', 'valley', 'sky', 'edge', 'reflections',
        'bloom', 'haze', 'bend', 'glow', 'path', 'ridge', 'shore', 'meadow',
        'lane', 'view', 'tone', 'frame', 'scene', 'moment', 'study', 'fragment',
    ];

    public function run(): void
    {
        Storage::disk('public')->makeDirectory('images');

        for ($index = 0; $index < self::COUNT; $index++) {
            $number = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            $title = $this->titleFor($index);
            $slug = 'gallery-'.$number;
            $filename = 'seed-'.$slug.'.jpg';
            $path = 'images/'.$filename;
            [$width, $height] = $this->sizeFor($index);

            if (! Storage::disk('public')->exists($path)) {
                $this->generateImage($width, $height, $filename, $index);
                Storage::disk('public')->put($path, file_get_contents(sys_get_temp_dir().'/hlp_'.$filename));
                @unlink(sys_get_temp_dir().'/hlp_'.$filename);
            }

            $image = Image::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'description' => fake()->sentence(random_int(8, 16)),
                    'alt_text' => $title.' photograph',
                    'file_path' => $path,
                    'is_published' => true,
                ]
            );

            $this->seedLikes($image, $index);
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

    private function generateImage(int $width, int $height, string $filename, int $index): void
    {
        $canvas = imagecreatetruecolor($width, $height);
        $palette = self::PALETTES[$index % count(self::PALETTES)];
        $bg = imagecolorallocate($canvas, $palette[0], $palette[1], $palette[2]);
        imagefill($canvas, 0, 0, $bg);

        $accent = imagecolorallocate(
            $canvas,
            max(0, $palette[0] - 35),
            max(0, $palette[1] - 35),
            max(0, $palette[2] - 35)
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

        imagejpeg($canvas, sys_get_temp_dir().'/hlp_'.$filename, 88);
        imagedestroy($canvas);
    }

    private function seedLikes(Image $image, int $index): void
    {
        $likeCount = ($index * 3) % 17;

        ImageLike::query()->where('image_id', $image->id)->delete();

        for ($i = 0; $i < $likeCount; $i++) {
            ImageLike::create([
                'image_id' => $image->id,
                'fingerprint' => hash('sha256', 'seed-like-'.$image->id.'-'.$i),
            ]);
        }
    }
}