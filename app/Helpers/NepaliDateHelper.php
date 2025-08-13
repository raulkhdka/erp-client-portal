<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class NepaliDateHelper
{
    public static function formatBsInteger($bsInteger, $format = 'Y-m-d')
    {
        try {
            if (!$bsInteger || !is_numeric($bsInteger) || strlen((string)$bsInteger) !== 8) {
                throw new Exception('Invalid BS integer date provided.');
            }

            $dateStr = (string)$bsInteger;
            $year = substr($dateStr, 0, 4);
            $month = substr($dateStr, 4, 2);
            $day = substr($dateStr, 6, 2);
            $formatted = "$year-$month-$day";

            if ($format === 'Y-m-d') {
                return $formatted;
            }

            return Carbon::createFromFormat('Ymd', $dateStr)->format($format);
        } catch (Exception $e) {
            Log::error('BS integer formatting error: ' . $e->getMessage(), ['bsInteger' => $bsInteger]);
            return null;
        }
    }

    public static function autoConvertModelAttributes($model, array $attributes, $format = 'formatted')
    {
        foreach ($attributes as $attribute) {
            try {
                $value = $model->$attribute;

                if ($value instanceof Carbon) {
                    $formattedValue = $value->format('Y-m-d');
                    $model->{"{$attribute}_formatted"} = $formattedValue;
                    $model->{"{$attribute}_nepali_html"} = static::auto_nepali_date($formattedValue, $format);
                } elseif (is_numeric($value) && strlen((string)$value) === 8) {
                    $formattedValue = static::formatBsInteger($value, 'Y-m-d');
                    $model->{"{$attribute}_formatted"} = $formattedValue;
                    $model->{"{$attribute}_nepali_html"} = static::auto_nepali_date_bs_integer($value, $format);
                } elseif (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $model->{"{$attribute}_formatted"} = $value;
                    $model->{"{$attribute}_nepali_html"} = static::auto_nepali_date($value, $format);
                } else {
                    Log::warning("Invalid value for attribute $attribute in model", [
                        'model' => get_class($model),
                        'attribute' => $attribute,
                        'value' => $value
                    ]);
                    $model->{"{$attribute}_formatted"} = null;
                    $model->{"{$attribute}_nepali_html"} = 'N/A';
                }
            } catch (Exception $e) {
                Log::error("Error processing attribute $attribute in model: " . $e->getMessage(), [
                    'model' => get_class($model),
                    'attribute' => $attribute
                ]);
                $model->{"{$attribute}_formatted"} = null;
                $model->{"{$attribute}_nepali_html"} = 'N/A';
            }
        }
    }

    /**
     * Generate HTML for AD date conversion
     */
    public static function auto_nepali_date($date, $format = 'readable')
    {
        if (!$date) {
            return 'N/A';
        }

        $adDate = $date instanceof Carbon ? $date->format('Y-m-d') : $date;

        return "<span class='nepali-date-display' data-convert-date='$adDate' data-format='$format'>" .
               "<span class='bs-date' style='display: inline;'></span>" .
               "<span class='ad-date' style='display: none;'>$adDate</span>" .
               "<span class='date-toggle' style='cursor: pointer; margin-left: 5px; color: #007bff; text-decoration: underline;'>AD</span>" .
               "</span>";
    }

    /**
     * Generate HTML for BS integer date conversion
     */
    public static function auto_nepali_date_bs_integer($bsInteger, $format = 'readable')
    {
        if (!$bsInteger || !is_numeric($bsInteger) || strlen((string)$bsInteger) !== 8) {
            return 'N/A';
        }

        return "<span class='nepali-date-display' data-bs-integer='$bsInteger' data-format='$format'>" .
               "<span class='bs-date' style='display: inline;'></span>" .
               "<span class='ad-date' style='display: none;'></span>" .
               "<span class='date-toggle' style='cursor: pointer; margin-left: 5px; color: #007bff; text-decoration: underline;'>AD</span>" .
               "</span>";
    }

    /**
     * Auto detect date type and return appropriate HTML
     */
    public static function auto_detect_nepali_date($date, $format = 'readable')
    {
        if (!$date) {
            return 'N/A';
        }

        // Check if it's a BS integer (8 digits)
        if (is_numeric($date) && strlen((string)$date) === 8) {
            return static::auto_nepali_date_bs_integer($date, $format);
        }

        // Check if it's an AD date
        if ($date instanceof Carbon || (is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))) {
            return static::auto_nepali_date($date, $format);
        }

        return 'N/A';
    }
}