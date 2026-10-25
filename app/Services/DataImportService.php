<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\MobileCaseApplication;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

final class DataImportService
{
    /**
     * Target Sections Configuration
     */
    public const SECTIONS = [
        'cases' => [
            'name' => 'الحالات وطلبات المساعدة',
            'icon' => 'bi-file-earmark-medical',
            'badge' => 'الرعاية والبحث الميداني',
            'color' => '#10b981',
            'model' => MobileCaseApplication::class,
            'description' => 'استيراد طلبات المساعدة والحالات الجديدة مباشرة مع بيانات العنوان ونوع الدعم',
            'fields' => [
                'applicant_name' => ['label' => 'اسم صاحب الحالة', 'required' => true, 'synonyms' => ['الاسم', 'اسم الحالة', 'اسم المستفيد', 'صاحب الطلب', 'اسم مقدم الطلب', 'الاسم بالكامل', 'name', 'full_name', 'applicant_name']],
                'applicant_phone' => ['label' => 'رقم الهاتف', 'required' => false, 'synonyms' => ['الهاتف', 'الموبايل', 'رقم الهاتف', 'رقم الموبايل', 'التليفون', 'phone', 'mobile', 'applicant_phone']],
                'applicant_id_number' => ['label' => 'الرقم القومي', 'required' => false, 'synonyms' => ['الرقم القومي', 'بطاقة الرقم القومي', 'رقم البطاقة', 'national_id', 'id_number']],
                'case_type' => ['label' => 'نوع المساعدة', 'required' => false, 'synonyms' => ['نوع الحالة', 'نوع المساعدة', 'تصنيف الحالة', 'نوع الطلب', 'case_type', 'type']],
                'governorate' => ['label' => 'المحافظة', 'required' => false, 'synonyms' => ['المحافظة', 'governorate']],
                'city' => ['label' => 'المركز / المدينة', 'required' => false, 'synonyms' => ['المدينة', 'المركز', 'القرية', 'city', 'center']],
                'address' => ['label' => 'العنوان بالتفصيل', 'required' => false, 'synonyms' => ['العنوان', 'محل الإقامة', 'عنوان السكن', 'address']],
                'family_members_count' => ['label' => 'عدد أفراد الأسرة', 'required' => false, 'synonyms' => ['عدد الأفراد', 'عدد أفراد الأسرة', 'عدد الأولاد', 'family_members_count', 'children_count']],
                'description' => ['label' => 'تفاصيل ووصف الحالة', 'required' => false, 'synonyms' => ['الوصف', 'وصف الحالة', 'ملاحظات', 'تفاصيل الطلب', 'description', 'notes']],
            ],
            'sample_data' => [
                ['محمد أحمد إبراهيم', '01012345678', '29001011234567', 'medical', 'الغربية', 'طنطا', 'شارع البحر، بجوار المحطة', '4', 'يحتاج مساعدة في إجراء جراحة وفحوصات'],
                ['فاطمة علي حسن', '01298765432', '28505051234568', 'zad', 'كفر الشيخ', 'دسوق', 'قرية كفر مجر', '3', 'أسرة أيتام تحتاج كفالة شهرية'],
            ],
        ],

        'donors' => [
            'name' => 'المتبرعين',
            'icon' => 'bi-heart-fill',
            'badge' => 'شبكة الخير والعطاء',
            'color' => '#f59e0b',
            'model' => Donor::class,
            'description' => 'استيراد المتبرعين الجدد مع أرقام التواصل والتصنيفات الدورية',
            'fields' => [
                'name' => ['label' => 'اسم المتبرع', 'required' => true, 'synonyms' => ['الاسم', 'اسم المتبرع', 'المتبرع', 'name', 'donor_name']],
                'phone' => ['label' => 'رقم الهاتف', 'required' => false, 'synonyms' => ['الهاتف', 'الموبايل', 'رقم الهاتف', 'phone', 'mobile']],
                'national_id' => ['label' => 'الرقم القومي', 'required' => false, 'synonyms' => ['الرقم القومي', 'بطاقة الرقم القومي', 'national_id']],
                'email' => ['label' => 'البريد الإلكتروني', 'required' => false, 'synonyms' => ['البريد', 'الإيميل', 'البريد الإلكتروني', 'email']],
                'type' => ['label' => 'نوع المتبرع (فرد/مؤسسة)', 'required' => false, 'synonyms' => ['النوع', 'نوع المتبرع', 'فرد/مؤسسة', 'type']],
                'address' => ['label' => 'العنوان', 'required' => false, 'synonyms' => ['العنوان', 'address']],
                'classification' => ['label' => 'التصنيف (A / B / C)', 'required' => false, 'synonyms' => ['التصنيف', 'الفئة', 'classification', 'category']],
            ],
            'sample_data' => [
                ['الحاج محمود الشناوي', '01123456789', '28001011234567', 'mahmoud@example.com', 'فرد', 'طنطا - الغربية', 'A'],
                ['شركة النور للتجارة', '01099887766', '', 'contact@alnoor.com', 'مؤسسة', 'القاهرة - المعادي', 'VIP'],
            ],
        ],

        'donations' => [
            'name' => 'التبرعات والمعاملات المالية',
            'icon' => 'bi-cash-coin',
            'badge' => 'الإيرادات والمساهمات',
            'color' => '#0ea5e9',
            'model' => Donation::class,
            'description' => 'استيراد سجلات التبرعات النقدية والعينية وربطها بالمتبرعين تلقائياً',
            'fields' => [
                'donor_name' => ['label' => 'اسم المتبرع', 'required' => true, 'synonyms' => ['المتبرع', 'اسم المتبرع', 'donor_name', 'donor']],
                'donor_phone' => ['label' => 'هاتف المتبرع', 'required' => false, 'synonyms' => ['هاتف المتبرع', 'موبايل المتبرع', 'donor_phone']],
                'amount' => ['label' => 'مبلغ التبرع', 'required' => true, 'synonyms' => ['المبلغ', 'القيمة', 'قيمة التبرع', 'amount', 'donation_amount']],
                'type' => ['label' => 'النوع (نقدي / عيني)', 'required' => false, 'synonyms' => ['نوع التبرع', 'النوع', 'نقدي/عيني', 'type']],
                'payment_method' => ['label' => 'طريقة الدفع', 'required' => false, 'synonyms' => ['طريقة الدفع', 'وسيلة الدفع', 'payment_method']],
                'purpose' => ['label' => 'الغرض / المشروع', 'required' => false, 'synonyms' => ['الغرض', 'الهدف', 'الباب', 'المشروع', 'الحملة', 'purpose']],
                'received_at' => ['label' => 'تاريخ الاستلام', 'required' => false, 'synonyms' => ['تاريخ الاستلام', 'التاريخ', 'تاريخ التبرع', 'date', 'received_at']],
                'allocation_note' => ['label' => 'ملاحظات وتفاصيل', 'required' => false, 'synonyms' => ['ملاحظات', 'ملاحظات التخصيص', 'notes']],
            ],
            'sample_data' => [
                ['الحاج محمود الشناوي', '01123456789', '5000', 'cash', 'تحويل بنكي', 'كفالة أيتام', '2026-10-25', 'دفعة شهر أكتوبر'],
                ['فاعل خير', '01011122233', '1500', 'cash', 'فودافون كاش', 'سقيا ماء', '2026-10-24', 'مساهمة في وصلة مياه'],
            ],
        ],

        'beneficiaries' => [
            'name' => 'المستفيدين والبحث الميداني',
            'icon' => 'bi-people-fill',
            'badge' => 'قاعدة بيانات الأسر',
            'color' => '#8b5cf6',
            'model' => Beneficiary::class,
            'description' => 'استيراد الأسر والمستفيدين المعتمدين مع الأرقام القومية وحالات الاستحقاق',
            'fields' => [
                'full_name' => ['label' => 'الاسم بالكامل', 'required' => true, 'synonyms' => ['الاسم', 'اسم المستفيد', 'الاسم بالكامل', 'full_name', 'name']],
                'national_id' => ['label' => 'الرقم القومي', 'required' => false, 'synonyms' => ['الرقم القومي', 'بطاقة الرقم القومي', 'رقم البطاقة', 'national_id']],
                'phone' => ['label' => 'رقم الهاتف', 'required' => false, 'synonyms' => ['الهاتف', 'الموبايل', 'رقم الهاتف', 'phone', 'mobile']],
                'birth_date' => ['label' => 'تاريخ الميلاد (YYYY-MM-DD)', 'required' => false, 'synonyms' => ['تاريخ الميلاد', 'birth_date', 'dob']],
                'address' => ['label' => 'العنوان بالتفصيل', 'required' => false, 'synonyms' => ['العنوان', 'محل الإقامة', 'address']],
                'assistance_type' => ['label' => 'نوع المساعدة المعتمدة', 'required' => false, 'synonyms' => ['نوع المساعدة', 'المساعدة', 'assistance_type']],
                'notes' => ['label' => 'ملاحظات البحث الاجتماعي', 'required' => false, 'synonyms' => ['ملاحظات', 'notes']],
            ],
            'sample_data' => [
                ['علي السيد مصطفى', '27503021234567', '01512345678', '1975-03-02', 'طنطا - سيجر', 'مساعدة علاجية شهرية', 'يعاني من مرض مزمن ويستحق الدعم'],
                ['زينب إبراهيم الشربيني', '28209151234568', '01198765432', '1982-09-15', 'المحلة الكبرى - الوراقة', 'كفالة أيتام', 'أم معيلة لثلاثة أطفال'],
            ],
        ],

        'tasks' => [
            'name' => 'مهام فريق العمل',
            'icon' => 'bi-kanban-fill',
            'badge' => 'إدارة الإنجاز والمتابعة',
            'color' => '#ec4899',
            'model' => Task::class,
            'description' => 'استيراد وتوزيع المهام المجدولة على الموظفين مع تواريخ الاستحقاق والأولويات',
            'fields' => [
                'title' => ['label' => 'عنوان المهمة', 'required' => true, 'synonyms' => ['عنوان المهمة', 'المهمة', 'العنوان', 'title', 'task_title']],
                'description' => ['label' => 'وصف وتفاصيل المهمة', 'required' => false, 'synonyms' => ['الوصف', 'تفاصيل المهمة', 'description']],
                'assignee_name_or_email' => ['label' => 'الموظف المسؤول (اسم أو إيميل)', 'required' => false, 'synonyms' => ['الموظف المسؤول', 'المسؤول', 'المعين له', 'assigned_to', 'assignee', 'employee']],
                'due_date' => ['label' => 'تاريخ الاستحقاق (YYYY-MM-DD)', 'required' => false, 'synonyms' => ['تاريخ الاستحقاق', 'التاريخ', 'آخر موعد', 'due_date']],
                'priority' => ['label' => 'الأولوية (عالية/متوسطة/منخفضة)', 'required' => false, 'synonyms' => ['الأولوية', 'درجة الأهمية', 'priority']],
                'status' => ['label' => 'الحالة (pending/in_progress/done)', 'required' => false, 'synonyms' => ['الحالة', 'status']],
            ],
            'sample_data' => [
                ['بحث ميداني لأسرة قرية محلة مرحوم', 'زيارة ميدانية وتدقيق الأوراق والبحث الاجتماعي للأسرة', 'احمد حسن', '2026-10-30', 'high', 'pending'],
                ['تجهيز تقرير قافلة مياه مدرار', 'حصر الوصلات المنفذة خلال الأسبوع الماضي وإرفاق الصور', 'سارة محمود', '2026-10-28', 'normal', 'in_progress'],
            ],
        ],
    ];

    /**
     * Parse any uploaded file (XLSX, XLS, CSV) into raw array of rows
     */
    public function parseFile(string $filePath, string $extension): array
    {
        $ext = strtolower($extension);

        if ($ext === 'csv' || $ext === 'txt') {
            return $this->parseCsv($filePath);
        }

        if ($ext === 'xlsx') {
            return $this->parseXlsx($filePath);
        }

        // Fallback for .xls or plain text attempt
        return $this->parseCsv($filePath);
    }

    /**
     * Pure PHP High-Performance XLSX Parser using ZipArchive and SimpleXML
     */
    private function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            // Fallback to CSV if not a valid zip
            return $this->parseCsv($filePath);
        }

        // 1. Read shared strings if present
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml !== false) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string) $val->t;
                    } elseif (isset($val->r)) {
                        $text = '';
                        foreach ($val->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read first worksheet
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            // Try looking for any sheet in xl/worksheets/
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (str_starts_with($filename, 'xl/worksheets/sheet') && str_ends_with($filename, '.xml')) {
                    $sheetXml = $zip->getFromName($filename);
                    break;
                }
            }
        }
        $zip->close();

        if ($sheetXml === false) {
            return [];
        }

        $xml = simplexml_load_string($sheetXml);
        if ($xml === false || !isset($xml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $rowNode) {
            $row = [];
            $maxColIdx = 0;

            foreach ($rowNode->c as $cell) {
                $cellRef = (string) $cell['r'];
                $colLetters = preg_replace('/[0-9]/', '', $cellRef);
                $colIdx = $this->columnLetterToIndex($colLetters);

                $val = '';
                $type = (string) $cell['t'];

                if ($type === 's') {
                    // Shared string index
                    $sIndex = (int) $cell->v;
                    $val = $sharedStrings[$sIndex] ?? '';
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string) $cell->is->t;
                } elseif (isset($cell->v)) {
                    $val = (string) $cell->v;
                }

                // Ensure row has padding up to current index
                while (count($row) < $colIdx) {
                    $row[] = '';
                }
                $row[$colIdx] = trim((string) $val);
            }

            // Clean empty trailing columns
            while (!empty($row) && end($row) === '') {
                array_pop($row);
            }

            if (!empty($row) && array_filter($row, fn($c) => $c !== '') !== []) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Convert Excel column letters (A, B, AA...) to zero-based index
     */
    private function columnLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $index = 0;
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return max(0, $index - 1);
    }

    /**
     * Robust CSV Parser with UTF-8 BOM removal and delimiter sniffing
     */
    private function parseCsv(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        // Remove UTF-8 BOM if present
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^$bom/", '', $content);

        // Detect line breaks and normalize to \n
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Detect delimiter: comma, semicolon, tab
        $firstLine = strtok($content, "\n") ?: '';
        $delimiters = [',', ';', "\t", '|'];
        $bestDelim = ',';
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $count = substr_count($firstLine, $d);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelim = $d;
            }
        }

        $lines = explode("\n", $content);
        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            $parsed = str_getcsv($line, $bestDelim);
            if (is_array($parsed) && array_filter($parsed, fn($c) => trim((string)$c) !== '') !== []) {
                $rows[] = array_map(fn($v) => trim((string)$v), $parsed);
            }
        }

        return $rows;
    }

    /**
     * Smart Column Auto-Mapping: Suggests mapping between sheet columns and model fields
     */
    public function autoMapColumns(array $headers, string $section): array
    {
        $sectionConfig = self::SECTIONS[$section] ?? null;
        if (!$sectionConfig) {
            return [];
        }

        $fields = $sectionConfig['fields'];
        $mapping = [];
        $usedFields = [];

        foreach ($headers as $colIdx => $header) {
            $normalizedHeader = $this->normalizeString($header);
            $bestMatchField = null;

            foreach ($fields as $fieldKey => $fieldMeta) {
                if (in_array($fieldKey, $usedFields, true)) {
                    continue;
                }

                // Check exact field key match
                if ($this->normalizeString($fieldKey) === $normalizedHeader) {
                    $bestMatchField = $fieldKey;
                    break;
                }

                // Check label match
                if ($this->normalizeString($fieldMeta['label']) === $normalizedHeader) {
                    $bestMatchField = $fieldKey;
                    break;
                }

                // Check synonyms
                foreach ($fieldMeta['synonyms'] as $syn) {
                    $normSyn = $this->normalizeString($syn);
                    if ($normSyn === $normalizedHeader || str_contains($normalizedHeader, $normSyn) || str_contains($normSyn, $normalizedHeader)) {
                        $bestMatchField = $fieldKey;
                        break 2;
                    }
                }
            }

            $mapping[$colIdx] = $bestMatchField;
            if ($bestMatchField) {
                $usedFields[] = $bestMatchField;
            }
        }

        return $mapping;
    }

    /**
     * Normalize Arabic & English text for fuzzy header matching
     */
    private function normalizeString(string $str): string
    {
        $str = mb_strtolower(trim($str), 'UTF-8');
        // Strip Arabic diacritics
        $str = preg_replace('/[\x{064B}-\x{0652}]/u', '', $str);
        // Normalize Arabic Alef variants
        $str = str_replace(['أ', 'إ', 'آ'], 'ا', $str);
        // Normalize Arabic Taa Marbuta & Haa
        $str = str_replace('ة', 'ه', $str);
        // Normalize Yaa
        $str = str_replace(['ى', 'ئ'], 'ي', $str);
        // Remove spaces and punctuation
        $str = preg_replace('/[_\-\s\(\)\:\.\/\,\#]+/u', '', $str);

        return $str ?: '';
    }

    /**
     * Generate CSV Template Content for Download
     */
    public function generateTemplateCsv(string $section): string
    {
        $config = self::SECTIONS[$section] ?? null;
        if (!$config) {
            return '';
        }

        $headers = [];
        foreach ($config['fields'] as $meta) {
            $headers[] = $meta['label'] . ($meta['required'] ? ' * (مطلوب)' : '');
        }

        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM so Excel opens Arabic correctly
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, $headers);

        foreach ($config['sample_data'] as $sampleRow) {
            fputcsv($output, $sampleRow);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent ?: '';
    }

    /**
     * Execute Database Import with Transaction Safety and Detailed Logging
     */
    public function executeImport(string $section, array $rows, array $columnMapping, int $userId): array
    {
        $config = self::SECTIONS[$section] ?? null;
        if (!$config) {
            return ['success' => false, 'message' => 'القسم المحدد غير مدعوم'];
        }

        $imported = 0;
        $skipped = 0;
        $duplicates = 0;
        $errors = [];

        // Invert mapping: fieldKey => colIdx
        $fieldToCol = [];
        foreach ($columnMapping as $colIdx => $fieldKey) {
            if ($fieldKey && $fieldKey !== 'skip') {
                $fieldToCol[$fieldKey] = (int) $colIdx;
            }
        }

        // Validate required fields are mapped
        foreach ($config['fields'] as $fKey => $fMeta) {
            if ($fMeta['required'] && !isset($fieldToCol[$fKey])) {
                return [
                    'success' => false,
                    'message' => "حقل '{$fMeta['label']}' مطلوب، يرجى ربطه بأحد أعمدة الملف.",
                ];
            }
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +1 for 0-index, +1 for header

                // Extract values by mapped field
                $data = [];
                foreach ($fieldToCol as $fieldKey => $colIdx) {
                    $data[$fieldKey] = isset($row[$colIdx]) ? trim((string) $row[$colIdx]) : null;
                }

                // Check required fields value
                $missingRequired = false;
                foreach ($config['fields'] as $fKey => $fMeta) {
                    if ($fMeta['required'] && (!isset($data[$fKey]) || $data[$fKey] === '')) {
                        $missingRequired = true;
                        $errors[] = "السطر #{$rowNumber}: الحقل المطلوب '{$fMeta['label']}' فارغ.";
                        break;
                    }
                }
                if ($missingRequired) {
                    $skipped++;
                    continue;
                }

                // Section specific processing
                switch ($section) {
                    case 'cases':
                        $res = $this->importCaseRow($data, $userId, $rowNumber);
                        break;
                    case 'donors':
                        $res = $this->importDonorRow($data, $userId, $rowNumber);
                        break;
                    case 'donations':
                        $res = $this->importDonationRow($data, $userId, $rowNumber);
                        break;
                    case 'beneficiaries':
                        $res = $this->importBeneficiaryRow($data, $userId, $rowNumber);
                        break;
                    case 'tasks':
                        $res = $this->importTaskRow($data, $userId, $rowNumber);
                        break;
                    default:
                        $res = ['status' => 'error', 'message' => 'قسم غير مدعوم'];
                }

                if ($res['status'] === 'imported') {
                    $imported++;
                } elseif ($res['status'] === 'duplicate') {
                    $duplicates++;
                    if (!empty($res['message'])) $errors[] = "السطر #{$rowNumber}: {$res['message']}";
                } else {
                    $skipped++;
                    if (!empty($res['message'])) $errors[] = "السطر #{$rowNumber}: {$res['message']}";
                }
            }

            DB::commit();

            return [
                'success' => true,
                'total_rows' => count($rows),
                'imported' => $imported,
                'duplicates' => $duplicates,
                'skipped' => $skipped,
                'errors' => array_slice($errors, 0, 30), // Max 30 display errors
                'message' => "تم استيراد {$imported} سجل بنجاح إلى قسم {$config['name']}.",
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة البيانات: ' . $e->getMessage(),
                'errors' => [$e->getMessage()],
            ];
        }
    }

    /**
     * Import a single Case Application row
     */
    private function importCaseRow(array $data, int $userId, int $rowNumber): array
    {
        // Check duplicate by national ID or phone if provided
        if (!empty($data['applicant_id_number'])) {
            $exists = MobileCaseApplication::where('applicant_id_number', $data['applicant_id_number'])->first();
            if ($exists) {
                return ['status' => 'duplicate', 'message' => "حالة مكررة بنفس الرقم القومي ({$data['applicant_id_number']})"];
            }
        }

        $app = new MobileCaseApplication();
        $app->applicant_name = $data['applicant_name'];
        $app->applicant_phone = $data['applicant_phone'] ?? '';
        $app->applicant_id_number = $data['applicant_id_number'] ?? null;
        $app->case_type = $data['case_type'] ?? 'general';
        $app->governorate = $data['governorate'] ?? 'الغربية';
        $app->city = $data['city'] ?? null;
        $app->address = $data['address'] ?? null;
        $app->family_members_count = isset($data['family_members_count']) && is_numeric($data['family_members_count']) ? (int) $data['family_members_count'] : 1;
        $app->description = $data['description'] ?? 'تم الاستيراد عبر ملف Excel';
        $app->status = MobileCaseApplication::STATUS_PENDING;
        $app->source = 'excel_import';
        $app->source_name = 'ملف إكسيل خارجي';
        $app->reference_number = 'IMP-' . date('ymd') . '-' . strtoupper(Str::random(4));
        $app->save();

        return ['status' => 'imported'];
    }

    /**
     * Import a single Donor row
     */
    private function importDonorRow(array $data, int $userId, int $rowNumber): array
    {
        // Check duplicate donor by phone or national id
        if (!empty($data['phone'])) {
            $exists = Donor::where('phone', $data['phone'])->first();
            if ($exists) {
                return ['status' => 'duplicate', 'message' => "متبرع مسجل مسبقاً بنفس رقم الهاتف ({$data['phone']})"];
            }
        }

        $donor = new Donor();
        $donor->name = $data['name'];
        $donor->phone = $data['phone'] ?? null;
        $donor->national_id = $data['national_id'] ?? null;
        $donor->email = $data['email'] ?? null;
        $donor->type = in_array(strtolower((string)$data['type']), ['مؤسسة', 'institution', 'company']) ? 'institution' : 'individual';
        $donor->address = $data['address'] ?? null;
        $donor->classification = $data['classification'] ?? 'B';
        $donor->active = true;
        $donor->save();

        return ['status' => 'imported'];
    }

    /**
     * Import a single Donation row
     */
    private function importDonationRow(array $data, int $userId, int $rowNumber): array
    {
        $amount = (float) str_replace([',', ' '], '', (string) $data['amount']);
        if ($amount <= 0) {
            return ['status' => 'error', 'message' => 'مبلغ التبرع يجب أن يكون أكبر من صفر'];
        }

        // Find or create donor
        $donor = null;
        if (!empty($data['donor_phone'])) {
            $donor = Donor::where('phone', $data['donor_phone'])->first();
        }
        if (!$donor && !empty($data['donor_name'])) {
            $donor = Donor::where('name', $data['donor_name'])->first();
        }
        if (!$donor) {
            $donor = new Donor();
            $donor->name = $data['donor_name'];
            $donor->phone = $data['donor_phone'] ?? null;
            $donor->type = 'individual';
            $donor->active = true;
            $donor->save();
        }

        $donation = new Donation();
        $donation->donor_id = $donor->id;
        $donation->amount = $amount;
        $donation->type = in_array(strtolower((string)$data['type']), ['عيني', 'in_kind']) ? 'in_kind' : 'cash';
        $donation->payment_method = $data['payment_method'] ?? 'cash';
        $donation->purpose = $data['purpose'] ?? 'عام';
        $donation->status = 'collected';
        $donation->source = 'excel_import';
        $donation->received_at = !empty($data['received_at']) ? date('Y-m-d', strtotime($data['received_at'])) : now()->toDateString();
        $donation->allocation_note = $data['allocation_note'] ?? 'تم الاستيراد عبر ملف Excel';
        $donation->created_by = $userId;
        $donation->receipt_number = 'RCP-IMP-' . date('ymd') . '-' . mt_rand(1000, 9999);
        $donation->save();

        return ['status' => 'imported'];
    }

    /**
     * Import a single Beneficiary row
     */
    private function importBeneficiaryRow(array $data, int $userId, int $rowNumber): array
    {
        if (!empty($data['national_id'])) {
            $exists = Beneficiary::where('national_id', $data['national_id'])->first();
            if ($exists) {
                return ['status' => 'duplicate', 'message' => "مستفيد مسجل مسبقاً بنفس الرقم القومي ({$data['national_id']})"];
            }
        }

        $b = new Beneficiary();
        $b->full_name = $data['full_name'];
        $b->national_id = $data['national_id'] ?? null;
        $b->phone = $data['phone'] ?? null;
        $b->birth_date = !empty($data['birth_date']) ? date('Y-m-d', strtotime($data['birth_date'])) : null;
        $b->address = $data['address'] ?? null;
        $b->assistance_type = $data['assistance_type'] ?? 'مالية';
        $b->notes = $data['notes'] ?? 'تم الاستيراد عبر ملف Excel';
        $b->status = 'active';
        $b->code = 'BEN-' . date('ymd') . '-' . mt_rand(100, 999);
        $b->save();

        return ['status' => 'imported'];
    }

    /**
     * Import a single Task row
     */
    private function importTaskRow(array $data, int $userId, int $rowNumber): array
    {
        $assigneeId = $userId;
        if (!empty($data['assignee_name_or_email'])) {
            $search = trim((string) $data['assignee_name_or_email']);
            $user = User::where('email', $search)
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', $search)
                ->first();
            if ($user) {
                $assigneeId = $user->id;
            }
        }

        $priority = 'normal';
        if (!empty($data['priority'])) {
            $p = mb_strtolower((string) $data['priority']);
            if (str_contains($p, 'عالي') || str_contains($p, 'urgent') || str_contains($p, 'high')) {
                $priority = 'high';
            } elseif (str_contains($p, 'منخفض') || str_contains($p, 'low')) {
                $priority = 'low';
            }
        }

        $status = 'pending';
        if (!empty($data['status'])) {
            $s = mb_strtolower((string) $data['status']);
            if (str_contains($s, 'تنفيذ') || str_contains($s, 'progress')) {
                $status = 'in_progress';
            } elseif (str_contains($s, 'مكتمل') || str_contains($s, 'done')) {
                $status = 'done';
            }
        }

        $task = new Task();
        $task->title = $data['title'];
        $task->description = $data['description'] ?? 'تم الاستيراد من ملف Excel';
        $task->assigned_to = $assigneeId;
        $task->assigned_by = $userId;
        $task->due_date = !empty($data['due_date']) ? date('Y-m-d', strtotime($data['due_date'])) : now()->addDays(3)->toDateString();
        $task->priority = $priority;
        $task->status = $status;
        $task->save();

        return ['status' => 'imported'];
    }
}
