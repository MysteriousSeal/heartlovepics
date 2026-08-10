<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class BannerService
{
    private const DIRECTORY = 'banners';

    private const WIDTH = 1600;

    private const HEIGHT = 800;

    private const WEBP_QUALITY = 85;

    public function store(UploadedFile $file): string
    {
        $source = $this->loadImage($file);
        $banner = $this->wideCrop($source);
        $path = self::DIRECTORY.'/'.Str::uuid().'.webp';

        Storage::disk('public')->makeDirectory(self::DIRECTORY);

        if (! imagewebp($banner, Storage::disk('public')->path($path), self::WEBP_QUALITY)) {
            imagedestroy($banner);
            throw new RuntimeException('Failed to save banner.');
        }

        imagedestroy($banner);

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
            throw new RuntimeException('Failed to read banner upload.');
        }

        $image = match (strtolower($file->getClientOriginalExtension())) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($absolutePath),
            'png' => @imagecreatefrompng($absolutePath),
            'gif' => @imagecreatefromgif($absolutePath),
            'webp' => @imagecreatefromwebp($absolutePath),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unsupported or invalid banner image.');
        }

        return $image;
    }

    /**
     * Centre-crop to the banner's 2:1 aspect ratio, then resize down to the
     * target dimensions (never up, to avoid upscaling a small source image
     * into visible blur).
     */
    private function wideCrop(GdImage $source): GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $targetRatio = self::WIDTH / self::HEIGHT;
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

        $outWidth = min(self::WIDTH, $cropWidth);
        $outHeight = (int) round($outWidth / $targetRatio);

        $banner = imagecreatetruecolor($outWidth, $outHeight);
        imagealphablending($banner, false);
        imagesavealpha($banner, true);

        $transparent = imagecolorallocatealpha($banner, 0, 0, 0, 127);
        imagefill($banner, 0, 0, $transparent);

        imagecopyresampled(
            $banner,
            $source,
            0,
            0,
            $cropX,
            $cropY,
            $outWidth,
            $outHeight,
            $cropWidth,
            $cropHeight,
        );

        imagedestroy($source);

        return $banner;
    }
}
