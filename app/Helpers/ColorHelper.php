<?php

namespace App\Helpers;

class ColorHelper
{
    public static function getTextColor($hexColor)
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');

        // If a shorthand hex, expand it
        if (strlen($hexColor) == 3) {
            $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
        }

        // Get RGB values
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // Return white for dark colors, black for light colors
        return ($luminance > 0.5) ? '#000000' : '#ffffff';
    }
}