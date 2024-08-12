<?php

namespace App\Form\Helpers;

class CountryPhonePrefixes
{
    public static function getPrefixes(): array
    {
        return [
            'US' => '+1',
            'GB' => '+44',
            'DE' => '+49',
            'FR' => '+33',
            'PL' => '+48',
            'AT' => '+43',
            'BE' => '+32',
            'BG' => '+359',
            'HR' => '+385',
            'CY' => '+357',
            'CZ' => '+420',
            'DK' => '+45',
            'EE' => '+372',
            'FI' => '+358',
            'GR' => '+30',
            'HU' => '+36',
            'IE' => '+353',
            'IT' => '+39',
            'LV' => '+371',
            'LT' => '+370',
            'LU' => '+352',
            'MT' => '+356',
            'NL' => '+31',
            'PT' => '+351',
            'RO' => '+40',
            'SK' => '+421',
            'SI' => '+386',
            'ES' => '+34',
            'SE' => '+46',
        ];
    }
}
