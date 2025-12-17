<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;

class DateHelper
{

    public static function formatTimeWithPeriod($time)
    {
        try {
           // it converts 24h to 12h with AM/PM in English)
            $carbonTime = Carbon::parse($time);
            return $carbonTime->format('g:i A');
        } catch (Exception $e) {
            return __('Invalid time format');
        }
    }

    /**
     *  function for displaying time in 12h format with Arabic "صباحاً" / "مساءً"
     */
    public static function formatTimeWithPeriodForDisplay($time)
    {
        try {
            // Replace Arabic strings with English to parse them
            $converted = str_replace(['صباحاً', 'مساءً'], ['AM', 'PM'], $time);
            $carbonTime = Carbon::parse($converted);

            // Format as 12-hour (e.g. "9:30") plus Arabic period
            $formattedTime = $carbonTime->format('g:i');
            $period        = $carbonTime->format('A') === 'AM' ? translate('am') : translate('pm');

            return "$formattedTime $period";
        } catch (Exception $e) {
            return __('Invalid time format');
        }
    }

    /**
     *  function to handle a time range using Arabic display
     */
    public static function formatDeliveryTimeForDisplay($timeRange)
    {
        try {
            // If it contains a dash, split and format each side
            if (strpos($timeRange, '-') !== false) {
                $times = explode('-', $timeRange);
                $start = self::formatTimeWithPeriodForDisplay(trim($times[0]));
                $end   = self::formatTimeWithPeriodForDisplay(trim($times[1]));
                return "$start - $end";
            }

            // Otherwise, treat it as a single time
            return self::formatTimeWithPeriodForDisplay($timeRange);
        } catch (Exception $e) {
            return __('Invalid time format');
        }
    }

    /**
     *  function for formatting delivery date
     */
    public static function formatDeliveryDate($date)
    {
        try {
            $deliveryDate  = Carbon::parse($date);
            $formattedDate = $deliveryDate->translatedFormat('d M Y');

            if ($deliveryDate->isToday()) {
                return translate('today') . ' [ ' . $formattedDate  .' ] ' ;
            } elseif ($deliveryDate->isTomorrow()) {
                return translate('tomorrow') . ' [ ' . $formattedDate  .' ] ';
            } else {
                return $formattedDate;
            }
        } catch (Exception $e) {
            return __('Invalid time format');
        }
    }
}
