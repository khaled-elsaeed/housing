<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Convert numbers to Arabic numerals
 * @param mixed $number
 * @return string
 */
function arabicNumbers($number)
{
    try {
        $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishDigits = range(0, 9);
        
        if (!is_numeric($number)) {
            logError('Invalid input for Arabic number conversion', [
                'input' => $number,
                'type' => gettype($number)
            ]);
            return $number;
        }
        
        $result = str_replace($englishDigits, $arabicDigits, $number);

        
        return $result;
        
    } catch (\Exception $e) {
        logError($e, [
            'input' => $number
        ]);
        return $number;
    }
}






