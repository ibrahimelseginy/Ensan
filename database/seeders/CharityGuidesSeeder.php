<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharityGuide;
use Illuminate\Database\Seeder;

final class CharityGuidesSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = '/tmp/charity_directory.csv';
        if (!file_exists($csvPath)) {
            return;
        }

        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            return;
        }

        // Header: 3, البلد, القرية, الدليل, رقم الهاتف, مبلغ المعرض + التاريخ, ملاحظات
        $header = fgetcsv($handle);
        $currentCenter = 'الحامول';

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $centerRaw = isset($row[1]) ? trim($row[1]) : '';
            if (!empty($centerRaw)) {
                $currentCenter = $centerRaw;
            }

            $village = isset($row[2]) ? trim($row[2]) : '';
            $guide = isset($row[3]) ? trim($row[3]) : '';
            $phone = isset($row[4]) ? trim($row[4]) : '';
            $amount = isset($row[5]) ? trim($row[5]) : '';
            $notes = isset($row[6]) ? trim($row[6]) : '';

            if (empty($village) && empty($guide) && empty($phone)) {
                continue;
            }

            // Normalise phone if multiple numbers exist with | or &
            $secondaryPhone = null;
            if (str_contains($phone, '|') || str_contains($phone, '&')) {
                $parts = preg_split('/[|&]/', $phone);
                $phone = trim($parts[0] ?? '');
                $secondaryPhone = trim($parts[1] ?? '');
            }

            CharityGuide::firstOrCreate(
                [
                    'center' => $currentCenter,
                    'village' => $village ?: 'مركز ' . $currentCenter,
                    'coordinator_name' => $guide ?: 'منسق القرية',
                ],
                [
                    'governorate' => 'كفر الشيخ',
                    'phone' => $phone,
                    'secondary_phone' => $secondaryPhone,
                    'exhibition_amount' => $amount ?: null,
                    'notes' => $notes ?: null,
                    'is_active' => true,
                ]
            );
        }

        fclose($handle);
    }
}

