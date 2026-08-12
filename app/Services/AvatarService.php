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

    /**
     * Store a portrait-cropped cover image (defaults to 1080×1920, 9:16) in
     * its own directory — used for parody covers, which read as a poster/
     * banner rather than a square avatar.
     */
    public function storeCover(UploadedFile $file, int $width = 1080, int $height = 1920): string
    {
        $source = $this->loadImage($file);
        $cover = $this->cropResize($source, $width, $height);
        $path = 'covers/'.Str::uuid().'.webp';

        Storage::disk('public')->makeDirectory('covers');

        if (! imagewebp($cover, Storage::disk('public')->path($path), self::WEBP_QUALITY)) {
            imagedestroy($cover);
            throw new RuntimeException('Failed to save cover image.');
        }

        imagedestroy($cover);

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

    /**
     * Center-crop the source to the target aspect ratio, then resize to
     * the exact target dimensions — the non-square counterpart to
     * squareCrop() above.
     */
    private function cropResize(GdImage $source, int $targetWidth, int $targetHeight): GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $targetRatio = $targetWidth / $targetHeight;
        $sourceRatio = $width / $height;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $height;
            $cropWidth = (int) round($height * $targetRatio);
        } else {
            $cropWidth = $width;
            $cropHeight = (int) round($width / $targetRatio);
        }

        $cropX = (int) (($width - $cropWidth) / 2);
        $cropY = (int) (($height - $cropHeight) / 2);

        $out = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($out, false);
        imagesavealpha($out, true);

        $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
        imagefill($out, 0, 0, $transparent);

        imagecopyresampled(
            $out,
            $source,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight,
        );

        imagedestroy($source);

        return $out;
    }
}