<?php


function arabicNumbers($number)
{
    $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $englishDigits = range(0, 9);

    return str_replace($englishDigits, $arabicDigits, $number);
}