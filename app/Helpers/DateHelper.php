<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format a date from AD to BS (Bikram Sambat)
     *
     * @param mixed $date
     * @param string $format
     * @return string
     */
    public static function formatAdToBs($date, $format = 'Y-m-d')
    {
        if (!$date) {
            return '';
        }

        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        // For now, we'll just return the AD date formatted
        // TODO: Implement actual AD to BS conversion
        return $date->format($format);
    }

    /**
     * Convert BS date to AD date
     *
     * @param string $bsDate
     * @return Carbon|null
     */
    public static function bsToAd($bsDate)
    {
        // TODO: Implement BS to AD conversion
        return Carbon::parse($bsDate);
    }
}
