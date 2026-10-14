<?php

namespace App\Helpers;

class ArabicSearchHelper
{
    /**
     * Clean Arabic diacritics and tatweel.
     */
    public static function cleanDiacritics(string $text): string
    {
        return preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{0640}]/u', '', $text);
    }

    /**
     * Normalize Arabic string to standard form (bare alef, haa, yaa).
     */
    public static function normalize(string $text): string
    {
        $text = self::cleanDiacritics($text);
        $text = preg_replace('/[أإآٱ]/u', 'ا', $text);
        $text = preg_replace('/[ة]/u', 'ه', $text);
        $text = preg_replace('/[ى]/u', 'ي', $text);
        return trim($text);
    }

    /**
     * Generate bidirectional search variants for a term or phrase.
     * E.g. 'احمد' -> ['احمد', 'أحمد', 'إحمد', 'آحمد']
     * E.g. 'أحمد' -> ['أحمد', 'احمد', 'إحمد', 'آحمد']
     * E.g. 'فاطمة' -> ['فاطمة', 'فاطمه', ...]
     * E.g. 'منى' -> ['منى', 'مني', ...]
     */
    public static function getVariants(string $term): array
    {
        $term = trim($term);
        if ($term === '') {
            return [];
        }

        $term = self::cleanDiacritics($term);
        $variants = [$term];

        // Alef variations
        if (strpos($term, 'ا') !== false) {
            $variants[] = preg_replace('/ا/u', 'أ', $term);
            $variants[] = preg_replace('/ا/u', 'إ', $term);
            $variants[] = preg_replace('/ا/u', 'آ', $term);
        }
        if (preg_match('/[أإآٱ]/u', $term)) {
            $variants[] = preg_replace('/[أإآٱ]/u', 'ا', $term);
            $variants[] = preg_replace('/[أإآٱ]/u', 'أ', $term);
            $variants[] = preg_replace('/[أإآٱ]/u', 'إ', $term);
            $variants[] = preg_replace('/[أإآٱ]/u', 'آ', $term);
        }

        // Taa marbouta vs Haa & Yaa vs Alef Maksoura
        $more = [];
        foreach ($variants as $v) {
            if (strpos($v, 'ة') !== false) {
                $more[] = preg_replace('/ة/u', 'ه', $v);
            }
            if (strpos($v, 'ه') !== false) {
                $more[] = preg_replace('/ه/u', 'ة', $v);
            }
            if (strpos($v, 'ى') !== false) {
                $more[] = preg_replace('/ى/u', 'ي', $v);
            }
            if (strpos($v, 'ي') !== false) {
                $more[] = preg_replace('/ي/u', 'ى', $v);
            }
        }

        return array_values(array_unique(array_filter(array_merge($variants, $more))));
    }

    /**
     * Parse raw search query into search tokens, extracted codes, phone numbers, and IDs.
     */
    public static function parseQuery(string $q): array
    {
        $raw = trim($q);
        if ($raw === '') {
            return [
                'raw' => '',
                'terms' => [],
                'variants' => [],
                'codes' => [],
                'numbers' => [],
            ];
        }

        // Clean out common prefixes like "مشروع: "
        $clean = preg_replace('/^مشروع:\s*/u', '', $raw);

        $terms = [$clean];
        $codes = [];

        // Check for bracketed codes like [DON-000029] or [DON-29] or [123]
        if (preg_match('/^(.*?)\s*\[(DON-\d+|\d+)\]/u', $clean, $matches)) {
            $namePart = trim($matches[1]);
            $codePart = trim($matches[2]);
            if ($namePart !== '') $terms[] = $namePart;
            if ($codePart !== '') {
                $terms[] = $codePart;
                $codes[] = $codePart;
            }
        }

        // Check if raw matches DON-XXX or REC-XXX
        if (preg_match('/(DON-\d+|REC-\d+)/i', $raw, $m)) {
            $codes[] = strtoupper($m[1]);
        }

        // Extract pure digits if available (phone number or ID)
        $digitsOnly = preg_replace('/[^\d]/', '', $raw);
        $numbers = [];
        if ($digitsOnly !== '') {
            $numbers[] = $digitsOnly;
        }

        // Collect all Arabic variants for all terms
        $allVariants = [];
        foreach ($terms as $t) {
            $vars = self::getVariants($t);
            foreach ($vars as $v) {
                $allVariants[] = $v;
            }
            // Also if multi-word (e.g. "محمد احمد"), include words individually
            $words = preg_split('/\s+/u', $t, -1, PREG_SPLIT_NO_EMPTY);
            if (count($words) > 1) {
                foreach ($words as $word) {
                    if (mb_strlen($word) >= 2) {
                        foreach (self::getVariants($word) as $wv) {
                            $allVariants[] = $wv;
                        }
                    }
                }
            }
        }

        return [
            'raw' => $raw,
            'terms' => array_values(array_unique(array_filter($terms))),
            'variants' => array_values(array_unique(array_filter($allVariants))),
            'codes' => array_values(array_unique(array_filter($codes))),
            'numbers' => array_values(array_unique(array_filter($numbers))),
        ];
    }
}
