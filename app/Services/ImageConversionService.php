<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageConversionService
{
    private const WEBP_QUALITY = 85;

    private const DIRECTORY = 'images';

    private const THUMBNAIL_DIRECTORY = 'images/thumbnails';

    private const THUMBNAIL_MAX_WIDTH = 600;

    /** @return array{path: string, thumbnail_path: string, width: int, height: int} */
    public function storeAsWebp(UploadedFile $file): array
    {
        if (strtolower($file->getClientOriginalExtension()) === 'gif') {
            return $this->storeGif($file);
        }

        $source = $this->loadImageFromUpload($file);

        return $this->processImage($source, [
            'filename' => Str::uuid().'.webp',
            'save_full' => true,
        ]);
    }

    /**
     * GIFs are kept as-is instead of being re-encoded to WebP: GD only reads the
     * first frame of a GIF, so converting would silently strip its animation. The
     * thumbnail is still a static WebP made from that first frame, which is fine
     * since thumbnails never need to animate.
     *
     * @return array{path: string, thumbnail_path: string, width: int, height: int}
     */
    private function storeGif(UploadedFile $file): array
    {
        $filename = Str::uuid().'.gif';
        $path = self::DIRECTORY.'/'.$filename;

        Storage::disk('public')->makeDirectory(self::DIRECTORY);
        Storage::disk('public')->putFileAs(self::DIRECTORY, $file, $filename);

        $image = $this->prepareForWebp($this->loadImageFromUpload($file));
        $width = imagesx($image);
        $height = imagesy($image);

        // storeThumbnail() writes actual WebP bytes when it generates a resized
        // thumbnail, so it needs a .webp filename here — not the .gif one — or the
        // resulting file's extension won't match its real content.
        $thumbnailFilename = pathinfo($filename, PATHINFO_FILENAME).'.webp';
        $thumbnailPath = $this->storeThumbnail($image, $width, $height, $thumbnailFilename, $path);
        imagedestroy($image);

        return [
            'path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'width' => $width,
            'height' => $height,
        ];
    }

    /** @return array{thumbnail_path: string, width: int, height: int} */
    public function generateThumbnailForFile(string $filePath, ?string $filename = null): array
    {
        $filename ??= basename($filePath);
        $source = $this->loadImageFromStoragePath($filePath);
        $result = $this->processImage($source, [
            'filename' => pathinfo($filename, PATHINFO_FILENAME).'.webp',
            'save_full' => false,
            'source_file_path' => $filePath,
        ]);

        return [
            'thumbnail_path' => $result['thumbnail_path'],
            'width' => $result['width'],
            'height' => $result['height'],
        ];
    }

    /**
     * @param  array{filename: string, save_full: bool, source_file_path?: string}  $options
     * @return array{path?: string, thumbnail_path: string, width: int, height: int}
     */
    private function processImage(GdImage $source, array $options): array
    {
        $image = $this->prepareForWebp($source);
        $width = imagesx($image);
        $height = imagesy($image);
        $path = null;

        if ($options['save_full']) {
            $path = self::DIRECTORY.'/'.$options['filename'];
            $this->writeWebp($image, $path, self::DIRECTORY);
        }

        $thumbnailPath = $this->storeThumbnail(
            $image,
            $width,
            $height,
            $options['filename'],
            $path ?? ($options['source_file_path'] ?? null),
        );

        imagedestroy($image);

        $result = [
            'thumbnail_path' => $thumbnailPath,
            'width' => $width,
            'height' => $height,
        ];

        if ($path !== null) {
            $result['path'] = $path;
        }

        return $result;
    }

    private function loadImageFromUpload(UploadedFile $file): GdImage
    {
        $path = $file->getRealPath();

        if ($path === false) {
            throw new RuntimeException('Failed to read uploaded image.');
        }

        return $this->loadImageFromAbsolutePath($path, strtolower($file->getClientOriginalExtension()));
    }

    private function loadImageFromStoragePath(string $relativePath): GdImage
    {
        $absolutePath = Storage::disk('public')->path($relativePath);

        if (! is_file($absolutePath)) {
            throw new RuntimeException("Image file not found: {$relativePath}");
        }

        return $this->loadImageFromAbsolutePath(
            $absolutePath,
            strtolower(pathinfo($relativePath, PATHINFO_EXTENSION)),
        );
    }

    private function loadImageFromAbsolutePath(string $absolutePath, string $extension): GdImage
    {
        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($absolutePath),
            'png' => @imagecreatefrompng($absolutePath),
            'gif' => @imagecreatefromgif($absolutePath),
            'webp' => @imagecreatefromwebp($absolutePath),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unsupported or invalid image format.');
        }

        return $image;
    }

    private function writeWebp(GdImage $image, string $relativePath, string $directory): void
    {
        Storage::disk('public')->makeDirectory($directory);

        if (! imagewebp($image, Storage::disk('public')->path($relativePath), self::WEBP_QUALITY)) {
            throw new RuntimeException('Failed to save image as WebP.');
        }
    }

    private function storeThumbnail(GdImage $image, int $width, int $height, string $filename, ?string $sourceFilePath = null): string
    {
        if ($width <= self::THUMBNAIL_MAX_WIDTH) {
            return $sourceFilePath ?? self::DIRECTORY.'/'.$filename;
        }

        $thumbWidth = self::THUMBNAIL_MAX_WIDTH;
        $thumbHeight = (int) round($height * ($thumbWidth / $width));
        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);

        $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
        imagefill($thumbnail, 0, 0, $transparent);

        imagealphablending($thumbnail, true);
        imagecopyresampled(
            $thumbnail,
            $image,
            0,
            0,
            0,
            0,
            $thumbWidth,
            $thumbHeight,
            $width,
            $height
        );
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);

        $thumbnailPath = self::THUMBNAIL_DIRECTORY.'/'.$filename;
        $this->writeWebp($thumbnail, $thumbnailPath, self::THUMBNAIL_DIRECTORY);
        imagedestroy($thumbnail);

        return $thumbnailPath;
    }

    private function prepareForWebp(GdImage $source): GdImage
    {
        if (! imageistruecolor($source)) {
            $width = imagesx($source);
            $height = imagesy($source);
            $image = imagecreatetruecolor($width, $height);

            imagealphablending($image, false);
            imagesavealpha($image, true);

            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);

            imagealphablending($image, true);
            imagecopy($image, $source, 0, 0, 0, 0, $width, $height);
            imagedestroy($source);

            imagealphablending($image, false);
            imagesavealpha($image, true);

            return $image;
        }

        imagealphablending($source, false);
        imagesavealpha($source, true);

        return $source;
    }
}