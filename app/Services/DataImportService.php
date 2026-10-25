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
                'applicant_name' => ['label' => 'اسم صاحب الحالة', 'required' => true, 'synonyms' => ['اسم صاحب الحالة', 'الاسم', 'اسم الحالة', 'اسم المستفيد', 'صاحب الطلب', 'اسم مقدم الطلب', 'الاسم بالكامل', 'اسم الطفل 1', 'اسم الطفل', 'صاحب الحالة', 'الإسم', 'name', 'full_name', 'applicant_name']],
                'applicant_phone' => ['label' => 'رقم الهاتف', 'required' => false, 'synonyms' => ['الهاتف', 'الموبايل', 'رقم الهاتف', 'رقم الموبايل', 'التليفون', 'التلفون', 'رقم التلفون', 'تليفون', 'phone', 'mobile', 'applicant_phone']],
                'applicant_id_number' => ['label' => 'الرقم القومي', 'required' => false, 'synonyms' => ['الرقم القومي', 'بطاقة الرقم القومي', 'رقم البطاقة', 'كود الطفل 1', 'كود الطفل', 'رقم الهوية', 'national_id', 'id_number']],
                'case_type' => ['label' => 'نوع المساعدة', 'required' => false, 'synonyms' => ['نوع الحالة', 'نوع المساعدة', 'المشروع', 'تصنيف الحالة', 'نوع الطلب', 'المصدر', 'اسم المصدر', 'case_type', 'type']],
                'governorate' => ['label' => 'المحافظة', 'required' => false, 'synonyms' => ['المحافظة', 'governorate']],
                'city' => ['label' => 'المركز / المدينة', 'required' => false, 'synonyms' => ['المدينة', 'المركز', 'القرية', 'city', 'center']],
                'address' => ['label' => 'العنوان بالتفصيل', 'required' => false, 'synonyms' => ['العنوان', 'محل الإقامة', 'عنوان السكن', 'address']],
                'family_members_count' => ['label' => 'عدد أفراد الأسرة', 'required' => false, 'synonyms' => ['عدد الأفراد', 'عدد أفراد الأسرة', 'عدد الأولاد', 'family_members_count', 'children_count']],
                'description' => ['label' => 'تفاصيل ووصف الحالة', 'required' => false, 'synonyms' => ['الوصف', 'وصف الحالة', 'ملاحظات', 'ملاحظه', 'تفاصيل الطلب', 'عايزه تحصل اي ؟', 'description', 'notes', 'note']],
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
                'name' => ['label' => 'اسم المتبرع', 'required' => true, 'synonyms' => ['الاسم', 'الإسم', 'اسم المتبرع', 'المتبرع', 'اسم الكافل', 'الكافل', 'المصدر', 'اسم المصدر', 'كافل', 'تبرع بواسطة', 'donor_name', 'name']],
                'phone' => ['label' => 'رقم الهاتف', 'required' => false, 'synonyms' => ['الهاتف', 'الموبايل', 'رقم الهاتف', 'التليفون', 'التلفون', 'رقم التلفون', 'تليفون', 'موبايل', 'phone', 'mobile']],
                'national_id' => ['label' => 'الرقم القومي', 'required' => false, 'synonyms' => ['الرقم القومي', 'بطاقة الرقم القومي', 'national_id']],
                'email' => ['label' => 'البريد الإلكتروني', 'required' => false, 'synonyms' => ['البريد', 'الإيميل', 'البريد الإلكتروني', 'email']],
                'type' => ['label' => 'نوع المتبرع (فرد/مؤسسة)', 'required' => false, 'synonyms' => ['النوع', 'نوع المتبرع', 'فرد/مؤسسة', 'type']],
                'address' => ['label' => 'العنوان', 'required' => false, 'synonyms' => ['العنوان', 'محل الإقامة', 'عنوان المتبرع', 'address']],
                'classification' => ['label' => 'التصنيف (A / B / C)', 'required' => false, 'synonyms' => ['التصنيف', 'الفئة', 'المشروع', 'كود الكافل', 'كود المتبرع', 'classification', 'category']],
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
                'donor_name' => ['label' => 'اسم المتبرع', 'required' => true, 'synonyms' => ['المتبرع', 'اسم المتبرع', 'اسم الكافل', 'الكافل', 'المصدر', 'اسم المصدر', 'الاسم', 'الإسم', 'donor_name', 'donor', 'name']],
                'donor_phone' => ['label' => 'هاتف المتبرع', 'required' => false, 'synonyms' => ['هاتف المتبرع', 'موبايل المتبرع', 'التليفون', 'التلفون', 'رقم التلفون', 'الهاتف', 'الموبايل', 'phone', 'donor_phone']],
                'amount' => ['label' => 'مبلغ التبرع', 'required' => true, 'synonyms' => ['المبلغ', 'القيمة', 'قيمة التبرع', 'المبلغ المدفوع', 'amount', 'donation_amount', 'value']],
                'type' => ['label' => 'النوع (نقدي / عيني)', 'required' => false, 'synonyms' => ['نوع التبرع', 'النوع', 'نقدي/عيني', 'type']],
                'payment_method' => ['label' => 'طريقة الدفع', 'required' => false, 'synonyms' => ['طريقة الدفع', 'طريقه الدفع', 'طريقة التحصيل', 'طريقه التحصيل', 'وسيلة الدفع', 'نوع الدفع', 'payment_method', 'method']],
                'purpose' => ['label' => 'الغرض / المشروع', 'required' => false, 'synonyms' => ['الغرض', 'الهدف', 'الباب', 'المشروع', 'الحملة', 'اسم الصنف', 'اسم الطفل 1', 'عايزه تحصل اي ؟', 'purpose', 'project']],
                'received_at' => ['label' => 'تاريخ الاستلام', 'required' => false, 'synonyms' => ['تاريخ الاستلام', 'التاريخ', 'تاريخ التبرع', 'تاريخ التوريد', 'date', 'received_at']],
                'allocation_note' => ['label' => 'ملاحظات وتفاصيل', 'required' => false, 'synonyms' => ['ملاحظات', 'ملاحظه', 'ملاحظات التخصيص', 'الوصف', 'رقم الايصال', 'كود الكافل', 'notes', 'note']],
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
                'full_name' => ['label' => 'الاسم بالكامل', 'required' => true, 'synonyms' => ['الاسم', 'اسم المستفيد', 'الاسم بالكامل', 'اسم الطفل 1', 'اسم الطفل', 'الإسم', 'full_name', 'name']],
                'national_id' => ['label' => 'الرقم القومي', 'required' => false, 'synonyms' => ['الرقم القومي', 'بطاقة الرقم القومي', 'رقم البطاقة', 'كود الطفل 1', 'كود الطفل', 'national_id']],
                'phone' => ['label' => 'رقم الهاتف', 'required' => false, 'synonyms' => ['الهاتف', 'الموبايل', 'رقم الهاتف', 'التليفون', 'التلفون', 'رقم التلفون', 'phone', 'mobile']],
                'birth_date' => ['label' => 'تاريخ الميلاد (YYYY-MM-DD)', 'required' => false, 'synonyms' => ['تاريخ الميلاد', 'birth_date', 'dob']],
                'address' => ['label' => 'العنوان بالتفصيل', 'required' => false, 'synonyms' => ['العنوان', 'محل الإقامة', 'address']],
                'assistance_type' => ['label' => 'نوع المساعدة المعتمدة', 'required' => false, 'synonyms' => ['نوع المساعدة', 'المساعدة', 'المشروع', 'assistance_type']],
                'notes' => ['label' => 'ملاحظات البحث الاجتماعي', 'required' => false, 'synonyms' => ['ملاحظات', 'ملاحظه', 'notes']],
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
     * Dictionary for translating English and technical column headers to Arabic
     */
    public const ENGLISH_HEADER_TRANSLATIONS = [
        'id' => 'المعرّف / الرقم التسلسلي',
        'created_at' => 'تاريخ ووقت الإنشاء',
        'updated_at' => 'تاريخ آخر تحديث',
        'deleted_at' => 'تاريخ الحذف',
        'user_id' => 'معرّف المستخدم / الموظف',
        'employee_id' => 'معرّف الموظف',
        'donor_id' => 'معرّف المتبرع',
        'beneficiary_id' => 'معرّف المستفيد',
        'case_id' => 'معرّف الحالة',
        'method' => 'طريقة الطلب (Method)',
        'path' => 'مسار الرابط / الصفحة (Path)',
        'url' => 'الرابط الإلكتروني',
        'status_code' => 'رمز حالة الاستجابة',
        'status' => 'الحالة',
        'ip' => 'عنوان الآي بي (IP)',
        'ip_address' => 'عنوان الآي بي',
        'user_agent' => 'بيانات المتصفح ونظام التشغيل',
        'payload' => 'بيانات ومدخلات الطلب (Payload)',
        'action' => 'الإجراء / العملية',
        'name' => 'الاسم',
        'full_name' => 'الاسم بالكامل',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'applicant_name' => 'اسم صاحب الحالة',
        'donor_name' => 'اسم المتبرع',
        'title' => 'العنوان / المسمى',
        'phone' => 'رقم الهاتف',
        'mobile' => 'الموبايل',
        'telephone' => 'التليفون',
        'email' => 'البريد الإلكتروني',
        'national_id' => 'الرقم القومي',
        'id_number' => 'رقم الهوية',
        'address' => 'العنوان السكني',
        'governorate' => 'المحافظة',
        'city' => 'المدينة / المركز',
        'amount' => 'المبلغ المالي',
        'value' => 'القيمة',
        'total' => 'الإجمالي',
        'price' => 'السعر',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'due_date' => 'تاريخ الاستحقاق',
        'received_at' => 'تاريخ الاستلام',
        'notes' => 'الملاحظات',
        'note' => 'ملاحظة',
        'description' => 'الوصف والتفاصيل',
        'details' => 'التفاصيل',
        'type' => 'النوع / التصنيف',
        'category' => 'التصنيف',
        'purpose' => 'الغرض / المشروع',
        'project' => 'المشروع',
        'priority' => 'درجة الأولوية',
        'gender' => 'النوع / الجنس',
        'birth_date' => 'تاريخ الميلاد',
        'payment_method' => 'طريقة الدفع',
        'receipt_number' => 'رقم الإيصال',
        'code' => 'الكود',
        'family_members_count' => 'عدد أفراد الأسرة',
        'count' => 'العدد / الكمية',
        'quantity' => 'الكمية',
    ];

    /**
     * Get all sheet names and targets from an XLSX file
     */
    public function getXlsxSheets(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        $wbXml = $zip->getFromName('xl/workbook.xml');
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');

        $rIdToTarget = [];
        if ($relsXml) {
            $rels = @simplexml_load_string($relsXml);
            if ($rels) {
                foreach ($rels->Relationship as $rel) {
                    $rIdToTarget[(string) $rel['Id']] = (string) $rel['Target'];
                }
            }
        }

        $sheets = [];
        if ($wbXml) {
            $wb = @simplexml_load_string($wbXml);
            if ($wb && isset($wb->sheets->sheet)) {
                $idx = 0;
                foreach ($wb->sheets->sheet as $s) {
                    $name = trim((string) $s['name']);
                    $rId = (string) $s->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                    $target = $rIdToTarget[$rId] ?? ('worksheets/sheet' . ($idx + 1) . '.xml');
                    if (!str_starts_with($target, 'xl/')) {
                        $target = 'xl/' . ltrim($target, '/');
                    }
                    $sheets[] = [
                        'index' => $idx,
                        'name' => $name,
                        'target' => $target,
                    ];
                    $idx++;
                }
            }
        }

        $zip->close();
        return $sheets;
    }

    /**
     * Parse any uploaded file (XLSX, XLS, CSV) into raw array of rows
     */
    public function parseFile(string $filePath, string $extension, int $sheetIndex = 0): array
    {
        $ext = strtolower($extension);

        if ($ext === 'csv' || $ext === 'txt') {
            return $this->parseCsv($filePath);
        }

        if ($ext === 'xlsx') {
            return $this->parseXlsx($filePath, $sheetIndex);
        }

        // Fallback for .xls or plain text attempt
        return $this->parseCsv($filePath);
    }

    /**
     * Pure PHP High-Performance XLSX Parser using ZipArchive and SimpleXML
     */
    private function parseXlsx(string $filePath, int $sheetIndex = 0): array
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
            $xml = @simplexml_load_string($sharedStringsXml);
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

        // 2. Discover sheets and choose target sheet XML
        $sheets = $this->getXlsxSheets($filePath);
        $targetSheetXml = 'xl/worksheets/sheet1.xml';

        if (!empty($sheets)) {
            if (isset($sheets[$sheetIndex])) {
                $targetSheetXml = $sheets[$sheetIndex]['target'];
            } else {
                $targetSheetXml = $sheets[0]['target'];
            }
        }

        $sheetXml = $zip->getFromName($targetSheetXml);
        if ($sheetXml === false) {
            // Try fallback to sheet1.xml or any existing sheet
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            if ($sheetXml === false) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (str_starts_with($filename, 'xl/worksheets/sheet') && str_ends_with($filename, '.xml')) {
                        $sheetXml = $zip->getFromName($filename);
                        break;
                    }
                }
            }
        }
        $zip->close();

        if ($sheetXml === false) {
            return [];
        }

        $xml = @simplexml_load_string($sheetXml);
        if ($xml === false || !isset($xml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $rowNode) {
            $row = [];

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

            if (!empty($row) && array_filter($row, fn($c) => trim((string)$c) !== '') !== []) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Translate an English/technical header into clear Arabic
     */
    public function translateHeaderToArabic(string $header): array
    {
        $raw = trim($header);
        if ($raw === '') {
            return ['main' => 'عمود بدون عنوان', 'sub' => ''];
        }

        // If it already has Arabic letters, return as is
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $raw)) {
            return ['main' => $raw, 'sub' => ''];
        }

        $key = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', $raw)));
        $translations = self::ENGLISH_HEADER_TRANSLATIONS;

        if (isset($translations[$key])) {
            return ['main' => $translations[$key], 'sub' => $raw];
        }

        // Check if ends or starts with known keys
        foreach ($translations as $eng => $ar) {
            if (str_ends_with($key, '_' . $eng) || str_starts_with($key, $eng . '_')) {
                return ['main' => $ar, 'sub' => $raw];
            }
        }

        $formatted = ucwords(str_replace('_', ' ', $key));
        return ['main' => $formatted, 'sub' => $raw];
    }

    /**
     * Clean date values, converting Excel serial numbers (e.g. 46296.0) to standard Y-m-d
     */
    public function cleanDateValue(?string $val): ?string
    {
        if (!$val || trim($val) === '') return null;
        $val = trim($val);
        // Excel serial date number
        if (is_numeric($val) && (float) $val > 10000 && (float) $val < 90000) {
            $days = (int) $val;
            $timestamp = ($days - 25569) * 86400;
            return gmdate('Y-m-d', $timestamp);
        }
        $ts = strtotime($val);
        if ($ts !== false && $ts > 0) {
            return date('Y-m-d', $ts);
        }
        return null;
    }

    /**
     * Clean and format phone numbers, converting scientific notation (1.018167843E9) to Egyptian standard 01018167843
     */
    public function cleanPhoneValue(?string $val): ?string
    {
        if (!$val || trim($val) === '') return null;
        $val = trim($val);
        if (stripos($val, 'E') !== false && is_numeric($val)) {
            $val = sprintf('%.0f', (float) $val);
        }
        $arabicNumerals = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $englishNumerals = ['0','1','2','3','4','5','6','7','8','9'];
        $val = str_replace($arabicNumerals, $englishNumerals, $val);
        $val = preg_replace('/[^0-9]/', '', $val);
        if (strlen($val) === 10 && in_array(substr($val, 0, 2), ['10', '11', '12', '15'], true)) {
            $val = '0' . $val;
        }
        return $val ?: null;
    }

    /**
     * Clean financial and numeric amount values
     */
    public function cleanAmountValue($val): float
    {
        if (is_numeric($val)) {
            return abs((float) $val);
        }
        $str = trim((string) $val);
        $arabicNumerals = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $englishNumerals = ['0','1','2','3','4','5','6','7','8','9'];
        $str = str_replace($arabicNumerals, $englishNumerals, $str);
        $str = preg_replace('/[^0-9\.]/', '', $str);
        return (float) $str;
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
        $mapping = array_fill(0, count($headers), null);

        // Technical names to ignore completely
        $technicalIgnore = ['id', 'ip', 'status_code', 'method', 'path', 'user_agent', 'payload'];

        $candidates = [];

        foreach ($headers as $colIdx => $header) {
            $rawHeader = trim((string) $header);
            $normalizedHeader = $this->normalizeString($rawHeader);

            if ($normalizedHeader === '' || is_numeric($normalizedHeader) || is_numeric(str_replace('.', '', $normalizedHeader))) {
                continue;
            }

            if (in_array(strtolower($rawHeader), $technicalIgnore, true)) {
                continue;
            }

            foreach ($fields as $fieldKey => $fieldMeta) {
                $normFieldKey = $this->normalizeString($fieldKey);
                $normLabel = $this->normalizeString($fieldMeta['label']);

                // Exact match on field key or label
                if ($normFieldKey === $normalizedHeader || $normLabel === $normalizedHeader) {
                    $candidates[] = [
                        'score' => 100,
                        'col' => $colIdx,
                        'field' => $fieldKey,
                    ];
                    continue;
                }

                // Check synonyms
                $synonyms = $fieldMeta['synonyms'] ?? [];
                foreach ($synonyms as $synIdx => $syn) {
                    $normSyn = $this->normalizeString($syn);
                    if ($normSyn === '') continue;

                    if ($normSyn === $normalizedHeader) {
                        // Higher score for primary synonyms earlier in the list
                        $score = ($synIdx < 4) ? 90 : 70;
                        $candidates[] = [
                            'score' => $score,
                            'col' => $colIdx,
                            'field' => $fieldKey,
                        ];
                        break;
                    }

                    // Partial / substring match (ONLY if string is at least 3 chars long)
                    if (mb_strlen($normalizedHeader) >= 3 && mb_strlen($normSyn) >= 3) {
                        if (str_contains($normalizedHeader, $normSyn) || str_contains($normSyn, $normalizedHeader)) {
                            $candidates[] = [
                                'score' => 40,
                                'col' => $colIdx,
                                'field' => $fieldKey,
                            ];
                            break;
                        }
                    }
                }
            }
        }

        // Sort candidates by score descending
        usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);

        $usedCols = [];
        $usedFields = [];

        foreach ($candidates as $cand) {
            $c = $cand['col'];
            $f = $cand['field'];

            if (!isset($usedCols[$c]) && !isset($usedFields[$f])) {
                $mapping[$c] = $f;
                $usedCols[$c] = true;
                $usedFields[$f] = true;
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
        $phone = $this->cleanPhoneValue($data['applicant_phone'] ?? null) ?? '';
        $idNumber = !empty($data['applicant_id_number']) ? trim((string)$data['applicant_id_number']) : null;

        // Check duplicate by national ID or phone if provided
        if (!empty($idNumber)) {
            $exists = MobileCaseApplication::where('applicant_id_number', $idNumber)->first();
            if ($exists) {
                return ['status' => 'duplicate', 'message' => "حالة مكررة بنفس الرقم القومي ({$idNumber})"];
            }
        }

        $app = new MobileCaseApplication();
        $app->applicant_name = $data['applicant_name'];
        $app->applicant_phone = $phone;
        $app->applicant_id_number = $idNumber;
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
        $phone = $this->cleanPhoneValue($data['phone'] ?? null);

        // Check duplicate donor by phone or national id
        if (!empty($phone)) {
            $exists = Donor::where('phone', $phone)->first();
            if ($exists) {
                return ['status' => 'duplicate', 'message' => "متبرع مسجل مسبقاً بنفس رقم الهاتف ({$phone})"];
            }
        }

        $donor = new Donor();
        $donor->name = $data['name'];
        $donor->phone = $phone;
        $donor->national_id = $data['national_id'] ?? null;
        $donor->email = $data['email'] ?? null;
        $donor->type = in_array(strtolower((string)($data['type'] ?? '')), ['مؤسسة', 'institution', 'company']) ? 'institution' : 'individual';
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
        $amount = $this->cleanAmountValue($data['amount'] ?? 0);
        if ($amount <= 0) {
            return ['status' => 'error', 'message' => 'مبلغ التبرع يجب أن يكون أكبر من صفر'];
        }

        $donorPhone = $this->cleanPhoneValue($data['donor_phone'] ?? null);
        $donorName = trim((string)($data['donor_name'] ?? ''));

        // Find or create donor
        $donor = null;
        if (!empty($donorPhone)) {
            $donor = Donor::where('phone', $donorPhone)->first();
        }
        if (!$donor && !empty($donorName)) {
            $donor = Donor::where('name', $donorName)->first();
        }
        if (!$donor) {
            $donor = new Donor();
            $donor->name = $donorName ?: 'متبرع إكسيل';
            $donor->phone = $donorPhone;
            $donor->type = 'individual';
            $donor->active = true;
            $donor->save();
        }

        $receivedAt = $this->cleanDateValue($data['received_at'] ?? null) ?: now()->toDateString();

        $donation = new Donation();
        $donation->donor_id = $donor->id;
        $donation->amount = $amount;
        $donation->type = in_array(strtolower((string)($data['type'] ?? '')), ['عيني', 'in_kind']) ? 'in_kind' : 'cash';
        $donation->payment_method = $data['payment_method'] ?? 'cash';
        $donation->purpose = $data['purpose'] ?? 'عام';
        $donation->status = 'collected';
        $donation->source = 'excel_import';
        $donation->received_at = $receivedAt;
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
        $phone = $this->cleanPhoneValue($data['phone'] ?? null);
        $nationalId = !empty($data['national_id']) ? trim((string)$data['national_id']) : null;

        if (!empty($nationalId)) {
            $exists = Beneficiary::where('national_id', $nationalId)->first();
            if ($exists) {
                return ['status' => 'duplicate', 'message' => "مستفيد مسجل مسبقاً بنفس الرقم القومي ({$nationalId})"];
            }
        }

        $birthDate = $this->cleanDateValue($data['birth_date'] ?? null);

        $b = new Beneficiary();
        $b->full_name = $data['full_name'];
        $b->national_id = $nationalId;
        $b->phone = $phone;
        $b->birth_date = $birthDate;
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

        $dueDate = $this->cleanDateValue($data['due_date'] ?? null) ?: now()->addDays(3)->toDateString();

        $task = new Task();
        $task->title = $data['title'];
        $task->description = $data['description'] ?? 'تم الاستيراد من ملف Excel';
        $task->assigned_to = $assigneeId;
        $task->assigned_by = $userId;
        $task->due_date = $dueDate;
        $task->priority = $priority;
        $task->status = $status;
        $task->save();

        return ['status' => 'imported'];
    }
}
