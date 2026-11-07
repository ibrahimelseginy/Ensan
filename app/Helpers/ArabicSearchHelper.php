<?php

namespace App\Helpers;

class ArabicSearchHelper
{
    /**
     * Convert Arabic-Indic and Eastern Arabic digits to ASCII 0-9.
     */
    public static function normalizeDigits(string $text): string
    {
        return strtr($text, [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        ]);
    }

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
        $text = self::normalizeDigits($text);
        $text = self::cleanDiacritics($text);
        $text = preg_replace('/[أإآٱ]/u', 'ا', $text);
        $text = preg_replace('/[ة]/u', 'ه', $text);
        $text = preg_replace('/[ى]/u', 'ي', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }

    /**
     * Generate bidirectional search variants for a term or phrase.
     * Handles word-initial hamzas (e.g. ابراهيم -> إبراهيم, أبراهيم),
     * taa marbouta / haa (مروه <-> مروة), alef maksoura / yaa (منى <-> مني),
     * and compound names (عبد الله <-> عبدالله).
     */
    public static function getVariants(string $term): array
    {
        $term = trim(preg_replace('/\s+/u', ' ', self::normalizeDigits($term)));
        if ($term === '') {
            return [];
        }

        $term = self::cleanDiacritics($term);
        $baseBare = preg_replace('/[أإآٱ]/u', 'ا', $term);

        $seeds = array_unique([$term, $baseBare]);

        // Handle compound Arabic names (عبد الله <-> عبدالله, ابو بكر <-> ابوبكر)
        foreach ($seeds as $s) {
            if (preg_match('/\b(عبد)\s+(الله|الرحمن|العزيز|الحميد|الفتاح|القادر|السلام|المنعم|الوهاب|اللطيف|الكريم|الجواد|الحليم|الرزاق|السميع|البصير|التواب|الحكيم|الخبير|الشافي|العليم|الغفار|الغفور|الغني|القدوس|المجيد|المعطي|المغني|المهيمن|الناصر|الهادي|الوارث|الودود|الوكيل)\b/u', $s)) {
                $seeds[] = preg_replace('/\b(عبد)\s+/u', '$1', $s);
            } elseif (preg_match('/\b(عبد)(الله|الرحمن|العزيز|الحميد|الفتاح|القادر|السلام|المنعم|الوهاب|اللطيف|الكريم|الجواد|الحليم|الرزاق|السميع|البصير|التواب|الحكيم|الخبير|الشافي|العليم|الغفار|الغفور|الغني|القدوس|المجيد|المعطي|المغني|المهيمن|الناصر|الهادي|الوارث|الودود|الوكيل)\b/u', $s)) {
                $seeds[] = preg_replace('/\b(عبد)(ال)/u', '$1 $2', $s);
            }
        }

        $variants = $seeds;

        foreach ($seeds as $seed) {
            // 1. Word-initial Alef variations (crucial for words like ابراهيم / اسماعيل / احمد)
            if (preg_match('/(^|\s)[أإآٱا]/u', $seed)) {
                $variants[] = preg_replace('/(^|\s)[أإآٱا]/u', '$1ا', $seed);
                $variants[] = preg_replace('/(^|\s)[أإآٱا]/u', '$1أ', $seed);
                $variants[] = preg_replace('/(^|\s)[أإآٱا]/u', '$1إ', $seed);
                $variants[] = preg_replace('/(^|\s)[أإآٱا]/u', '$1آ', $seed);
            }

            // 2. First-character-only Alef variations if multi-word or single word
            if (preg_match('/^[أإآٱا]/u', $seed)) {
                $rest = mb_substr($seed, 1);
                $variants[] = 'ا' . $rest;
                $variants[] = 'أ' . $rest;
                $variants[] = 'إ' . $rest;
                $variants[] = 'آ' . $rest;
            }
        }

        $variants = array_values(array_unique($variants));

        // 3. Taa marbouta vs Haa & Yaa vs Alef Maksoura
        $more = [];
        foreach ($variants as $v) {
            if (mb_strpos($v, 'ة') !== false) {
                $more[] = preg_replace('/ة/u', 'ه', $v);
            }
            if (mb_strpos($v, 'ه') !== false) {
                $more[] = preg_replace('/ه(\s|$)/u', 'ة$1', $v);
                $more[] = preg_replace('/ه/u', 'ة', $v);
            }
            if (mb_strpos($v, 'ى') !== false) {
                $more[] = preg_replace('/ى/u', 'ي', $v);
            }
            if (mb_strpos($v, 'ي') !== false) {
                $more[] = preg_replace('/ي(\s|$)/u', 'ى$1', $v);
            }
        }

        $all = array_values(array_unique(array_filter(array_merge($variants, $more))));

        // Cap variants to avoid overly huge SQL queries while keeping the most accurate ones first
        return array_slice($all, 0, 24);
    }

    /**
     * Parse raw search query into search tokens, words, phrase variants, codes, and numbers.
     */
    public static function parseQuery(string $q): array
    {
        $raw = trim(self::normalizeDigits($q));
        $raw = preg_replace('/\s+/u', ' ', $raw);

        if ($raw === '') {
            return [
                'raw' => '',
                'terms' => [],
                'words' => [],
                'variants' => [],
                'codes' => [],
                'numbers' => [],
            ];
        }

        // Clean out common prefixes like "مشروع: "
        $clean = trim(preg_replace('/^(مشروع|حالة|متبرع|كود|إيصال|ايصال):\s*/u', '', $raw));

        $terms = [$clean];
        $codes = [];

        // Check for bracketed codes like [DON-000029] or [DON-29] or [123]
        if (preg_match('/^(.*?)\s*\[([A-Za-z0-9_-]+)\]/u', $clean, $matches)) {
            $namePart = trim($matches[1]);
            $codePart = trim($matches[2]);
            if ($namePart !== '') {
                $terms = [$namePart];
            }
            if ($codePart !== '') {
                $codes[] = $codePart;
            }
        }

        // Check if raw matches DON-XXX, REC-XXX, B26..., G-...
        if (preg_match('/([A-Za-z]{1,4}-?\d{3,})/i', $raw, $m)) {
            $codes[] = strtoupper($m[1]);
        }

        // Extract pure digits if available (phone number or national ID)
        $digitsOnly = preg_replace('/[^\d]/', '', $raw);
        $numbers = [];
        if ($digitsOnly !== '') {
            $numbers[] = $digitsOnly;
            // If user searched with +20 or 0020 for an Egyptian number, also add local 01... format
            if (str_starts_with($digitsOnly, '201') && strlen($digitsOnly) === 12) {
                $numbers[] = '0' . substr($digitsOnly, 2);
            } elseif (str_starts_with($digitsOnly, '00201') && strlen($digitsOnly) === 14) {
                $numbers[] = '0' . substr($digitsOnly, 4);
            }
        }

        // Phrase variants (full search phrase only, NOT split words OR-ed together)
        $phraseVariants = [];
        foreach ($terms as $t) {
            foreach (self::getVariants($t) as $v) {
                $phraseVariants[] = $v;
            }
        }

        // Individual words for multi-word AND matching
        $words = array_values(array_filter(
            preg_split('/\s+/u', $terms[0] ?? $clean, -1, PREG_SPLIT_NO_EMPTY),
            fn ($w) => mb_strlen($w) >= 2
        ));

        return [
            'raw' => $raw,
            'terms' => array_values(array_unique(array_filter($terms))),
            'words' => $words,
            'variants' => array_values(array_unique(array_filter($phraseVariants))),
            'codes' => array_values(array_unique(array_filter($codes))),
            'numbers' => array_values(array_unique(array_filter($numbers))),
        ];
    }

    /**
     * Apply smart Arabic multi-word & variant search + numeric/code search on a query builder.
     */
    public static function applySmartSearch($query, string $rawQuery, array $textColumns, array $codeOrPhoneColumns = []): void
    {
        $parsed = self::parseQuery($rawQuery);
        if ($parsed['raw'] === '') {
            return;
        }

        $variants = $parsed['variants'];
        $words = $parsed['words'];
        $numbers = $parsed['numbers'];
        $codes = $parsed['codes'];
        $cleanQ = $parsed['raw'];

        $query->where(function ($w) use ($textColumns, $codeOrPhoneColumns, $variants, $words, $numbers, $codes, $cleanQ) {
            // 1. Full phrase variants across text columns
            foreach ($textColumns as $col) {
                foreach ($variants as $var) {
                    $w->orWhere($col, 'like', "%{$var}%");
                }
            }

            // 2. Multi-word AND search (e.g. "مروه احمد" matches "مروه مجدي عياد احمد")
            if (count($words) > 1 && !empty($textColumns)) {
                foreach ($textColumns as $col) {
                    $w->orWhere(function ($andQuery) use ($col, $words) {
                        foreach ($words as $word) {
                            $wordVariants = self::getVariants($word);
                            $andQuery->where(function ($wordOr) use ($col, $wordVariants) {
                                foreach ($wordVariants as $wv) {
                                    $wordOr->orWhere($col, 'like', "%{$wv}%");
                                }
                            });
                        }
                    });
                }
            }

            // 3. Code / Phone / National ID columns
            foreach ($codeOrPhoneColumns as $col) {
                $w->orWhere($col, 'like', "%{$cleanQ}%");
                foreach ($numbers as $num) {
                    if (strlen($num) >= 2) {
                        $w->orWhere($col, 'like', "%{$num}%");
                    }
                }
                foreach ($codes as $code) {
                    $w->orWhere($col, 'like', "%{$code}%");
                }
            }
        });
    }
}
