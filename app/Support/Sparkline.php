<?php

namespace App\Support;

class Sparkline
{
    /**
     * Build an SVG polyline "points" attribute from a flat list of values,
     * scaled to fit a width x height viewBox with a small top/bottom pad so
     * a max-value point never touches the very edge.
     *
     * @param  list<int>  $values
     */
    public static function points(array $values, int $width = 220, int $height = 48, int $pad = 4): string
    {
        $count = count($values);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            $y = $height / 2;

            return "0,{$y} {$width},{$y}";
        }

        $max = max($values);
        $usableHeight = $height - ($pad * 2);
        $step = $width / ($count - 1);

        $points = [];

        foreach ($values as $index => $value) {
            $x = round($index * $step, 1);
            $y = $max > 0
                ? round($height - $pad - (($value / $max) * $usableHeight), 1)
                : round($height - $pad, 1);

            $points[] = "{$x},{$y}";
        }

        return implode(' ', $points);
    }

    /**
     * Same scaling as points(), closed into a fillable area path (down to
     * the baseline and back) for the shaded region under the line.
     *
     * @param  list<int>  $values
     */
    public static function areaPath(array $values, int $width = 220, int $height = 48, int $pad = 4): string
    {
        $linePoints = self::points($values, $width, $height, $pad);

        if ($linePoints === '') {
            return '';
        }

        return "M0,{$height} L{$linePoints} L{$width},{$height} Z";
    }
}
