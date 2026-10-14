<?php

declare(strict_types=1);

namespace App\Helpers;

final class TafqeetHelper
{
    private static array $ones = [
        0 => '',
        1 => 'واحد',
        2 => 'اثنان',
        3 => 'ثلاثة',
        4 => 'أربعة',
        5 => 'خمسة',
        6 => 'ستة',
        7 => 'سبعة',
        8 => 'ثمانية',
        9 => 'تسعة',
        10 => 'عشرة',
        11 => 'أحد عشر',
        12 => 'اثنا عشر',
        13 => 'ثلاثة عشر',
        14 => 'أربعة عشر',
        15 => 'خمسة عشر',
        16 => 'ستة عشر',
        17 => 'سبعة عشر',
        18 => 'ثمانية عشر',
        19 => 'تسعة عشر',
    ];

    private static array $tens = [
        2 => 'عشرون',
        3 => 'ثلاثون',
        4 => 'أربعون',
        5 => 'خمسون',
        6 => 'ستون',
        7 => 'سبعون',
        8 => 'ثمانون',
        9 => 'تسعون',
    ];

    private static array $hundreds = [
        0 => '',
        1 => 'مائة',
        2 => 'مائتان',
        3 => 'ثلاثمائة',
        4 => 'أربعمائة',
        5 => 'خمسمائة',
        6 => 'ستمائة',
        7 => 'سبعمائة',
        8 => 'ثمانمائة',
        9 => 'تسعمائة',
    ];

    public static function inWords(float|int|string $number, string $currency = 'جنيه مصري'): string
    {
        $num = (float)$number;
        if ($num == 0) {
            return 'صفر ' . $currency . ' لا غير';
        }

        $integerPart = (int)floor($num);
        $fractionPart = (int)round(($num - $integerPart) * 100);

        $words = self::convertNumber($integerPart);
        $result = 'فقط وقدره ' . $words . ' ' . $currency;

        if ($fractionPart > 0) {
            $fractionWords = self::convertNumber($fractionPart);
            $result .= ' و' . $fractionWords . ' قرشاً';
        }

        $result .= ' لا غير';
        return $result;
    }

    private static function convertNumber(int $num): string
    {
        if ($num === 0) {
            return '';
        }

        if ($num < 20) {
            return self::$ones[$num];
        }

        if ($num < 100) {
            $ten = (int)floor($num / 10);
            $rem = $num % 10;
            if ($rem === 0) {
                return self::$tens[$ten];
            }
            return self::$ones[$rem] . ' و' . self::$tens[$ten];
        }

        if ($num < 1000) {
            $hundred = (int)floor($num / 100);
            $rem = $num % 100;
            if ($rem === 0) {
                return self::$hundreds[$hundred];
            }
            return self::$hundreds[$hundred] . ' و' . self::convertNumber($rem);
        }

        if ($num < 1000000) {
            $thousands = (int)floor($num / 1000);
            $rem = $num % 1000;

            $thousandsText = '';
            if ($thousands === 1) {
                $thousandsText = 'ألف';
            } elseif ($thousands === 2) {
                $thousandsText = 'ألفان';
            } elseif ($thousands >= 3 && $thousands <= 10) {
                $thousandsText = self::convertNumber($thousands) . ' آلاف';
            } else {
                $thousandsText = self::convertNumber($thousands) . ' ألفاً';
            }

            if ($rem === 0) {
                return $thousandsText;
            }
            return $thousandsText . ' و' . self::convertNumber($rem);
        }

        if ($num < 1000000000) {
            $millions = (int)floor($num / 1000000);
            $rem = $num % 1000000;

            $millionsText = '';
            if ($millions === 1) {
                $millionsText = 'مليون';
            } elseif ($millions === 2) {
                $millionsText = 'مليونان';
            } elseif ($millions >= 3 && $millions <= 10) {
                $millionsText = self::convertNumber($millions) . ' ملايين';
            } else {
                $millionsText = self::convertNumber($millions) . ' مليوناً';
            }

            if ($rem === 0) {
                return $millionsText;
            }
            return $millionsText . ' و' . self::convertNumber($rem);
        }

        return (string)$num;
    }
}
