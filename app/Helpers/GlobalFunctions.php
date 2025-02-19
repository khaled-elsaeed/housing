<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

function arabicNumbers($number)
{
    $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $englishDigits = range(0, 9);

    return str_replace($englishDigits, $arabicDigits, $number);
}

function formatLastUpdated($lastUpdated)
{
    if (!$lastUpdated) {
        return __('Never');
    }

    $lastUpdated = Carbon::parse($lastUpdated);
    $diffInMinutes = $lastUpdated->diffInMinutes(now());

    if ($diffInMinutes < 60) {
        return trans_choice(__('minute_ago'), $diffInMinutes, ['value' => $diffInMinutes]);
    }

    $diffInHours = (int) $lastUpdated->diffInHours(now());

    if ($diffInHours < 24) {
        $minutes = $diffInMinutes % 60;
        if ($minutes > 0) {
            return __('hour_minute_ago', ['hours' => $diffInHours, 'minutes' => $minutes]);
        } else {
            return trans_choice(__('hour_ago'), $diffInHours, ['value' => $diffInHours]);
        }
    }

    $diffInDays = (int) $lastUpdated->diffInDays(now());
    return trans_choice(__('day_ago'), $diffInDays, ['value' => $diffInDays]);
}






