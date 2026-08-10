<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AvatarService
{
    private const DIRECTORY = 'avatars';

    private const SIZE = 256;

    private const WEBP_QUALITY = 85;

    public function store(UploadedFile $file): string
    {
        $source = $this->loadImage($file);
        $avatar = $this->squareCrop($source);
        $path = self::DIRECTORY.'/'.Str::uuid().'.webp';

        Storage::disk('public')->makeDirectory(self::DIRECTORY);

        if (! imagewebp($avatar, Storage::disk('public')->path($path), self::WEBP_QUALITY)) {
            imagedestroy($avatar);
            throw new RuntimeException('Failed to save avatar.');
        }

        imagedestroy($avatar);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function loadImage(UploadedFile $file): GdImage
    {
        $absolutePath = $file->getRealPath();

        if ($absolutePath === false) {
            throw new RuntimeException('Failed to read avatar upload.');
        }

        $image = match (strtolower($file->getClientOriginalExtension())) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($absolutePath),
            'png' => @imagecreatefrompng($absolutePath),
            'gif' => @imagecreatefromgif($absolutePath),
            'webp' => @imagecreatefromwebp($absolutePath),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unsupported or invalid avatar image.');
        }

        return $image;
    }

    private function squareCrop(GdImage $source): GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $cropSize = min($width, $height);
        $cropX = (int) (($width - $cropSize) / 2);
        $cropY = (int) (($height - $cropSize) / 2);

        $avatar = imagecreatetruecolor(self::SIZE, self::SIZE);

        imagealphablending($avatar, false);
        imagesavealpha($avatar, true);

        $transparent = imagecolorallocatealpha($avatar, 0, 0, 0, 127);
        imagefill($avatar, 0, 0, $transparent);

        imagecopyresampled(
            $avatar,
            $source,
            0,
            0,
            $cropX,
            $cropY,
            self::SIZE,
            self::SIZE,
            $cropSize,
            $cropSize,
        );

        imagedestroy($source);

        return $avatar;
    }
}