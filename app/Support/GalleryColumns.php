<?php

namespace App\Support;

class GalleryColumns
{
    public const COUNT = 4;

    /**
     * @param  iterable<int, mixed>  $images
     * @return array<int, array<int, mixed>>
     */
    public static function distribute(iterable $images, int $offset = 0, int $columnCount = self::COUNT): array
    {
        $columns = array_fill(0, $columnCount, []);

        foreach ($images as $index => $image) {
            $columns[($offset + $index) % $columnCount][] = $image;
        }

        return $columns;
    }
}