<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DataImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DataImportWebController extends Controller
{
    public function __construct(
        private readonly DataImportService $importService
    ) {}

    /**
     * Display Data Import Hub (Dedicated Page)
     */
    public function index(Request $request)
    {
        $sections = DataImportService::SECTIONS;
        $activeSection = $request->query('section', 'cases');
        if (!isset($sections[$activeSection])) {
            $activeSection = 'cases';
        }

        return view('data_import.index', [
            'sections' => $sections,
            'activeSection' => $activeSection,
        ]);
    }

    /**
     * Download Ready-Made Excel/CSV Template with Headers and Samples
     */
    public function downloadTemplate(string $section): StreamedResponse
    {
        if (!isset(DataImportService::SECTIONS[$section])) {
            abort(404, 'القسم غير موجود');
        }

        $config = DataImportService::SECTIONS[$section];
        $filename = 'نموذج_استيراد_' . str_replace(' ', '_', $config['name']) . '.csv';
        $csvContent = $this->importService->generateTemplateCsv($section);

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . rawurlencode($filename) . '"',
        ]);
    }

    /**
     * Step 1: Upload File & Preview Columns with Smart Auto-Mapping
     */
    public function preview(Request $request): JsonResponse
    {
        $token = $request->input('token');
        $sheetIndex = (int) $request->input('sheet_index', 0);
        $section = $request->input('section');
        $tempDir = storage_path('app/imports');

        if ($token) {
            // Re-preview existing file (e.g. user selected another sheet or section)
            $filePath = null;
            $extension = 'xlsx';
            foreach (['xlsx', 'xls', 'csv', 'txt'] as $checkExt) {
                if (file_exists($tempDir . '/' . $token . '.' . $checkExt)) {
                    $filePath = $tempDir . '/' . $token . '.' . $checkExt;
                    $extension = $checkExt;
                    break;
                }
            }

            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'انتهت صلاحية جلسة رفع الملف، يرجى إعادة اختيار الملف والمحاولة مرة أخرى.',
                ], 419);
            }
        } else {
            // Fresh upload
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480', // Up to 20MB
            ], [
                'file.required' => 'يرجى اختيار ملف الإكسيل أو الـ CSV المراد استيراده.',
                'file.mimes' => 'صيغة الملف غير مدعومة. الصيغ المسموح بها هي: XLSX, XLS, CSV.',
                'file.max' => 'الحد الأقصى لحجم الملف هو 20 ميجابايت.',
            ]);

            $uploadedFile = $request->file('file');
            $extension = $uploadedFile->getClientOriginalExtension();

            if (!File::isDirectory($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $token = 'imp_' . date('ymd_His') . '_' . Str::random(12);
            $filePath = $tempDir . '/' . $token . '.' . $extension;
            $uploadedFile->move($tempDir, $token . '.' . $extension);
        }

        // 1. Run holistic multi-sheet analysis on the workbook
        $workbookAnalysis = $this->importService->analyzeWorkbook($filePath, $extension);

        // Get sheets list
        $sheets = [];
        if (in_array(strtolower($extension), ['xlsx', 'xls'], true)) {
            $sheets = $this->importService->getXlsxSheets($filePath);
        }
        if (empty($sheets)) {
            $sheets = [['index' => 0, 'name' => 'الورقة الرئيسية']];
        }

        // Active sheet analysis
        $activeSheetAnalysis = null;
        foreach ($workbookAnalysis['sheets'] as $sa) {
            if ($sa['sheet_index'] === $sheetIndex) {
                $activeSheetAnalysis = $sa;
                break;
            }
        }

        // If no section provided or set to 'auto', use the detected section for this sheet
        if (empty($section) || $section === 'auto' || !isset(DataImportService::SECTIONS[$section])) {
            $section = $activeSheetAnalysis['detected_section'] ?? 'donations';
        }

        // Parse file rows for chosen sheet
        $allRows = $this->importService->parseFile($filePath, $extension, $sheetIndex);

        if (empty($allRows) || count($allRows) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'الورقة المحددة فارغة أو لا تحتوي على صفوف بيانات بعد صف العناوين.',
                'sheets' => $sheets,
                'active_sheet_index' => $sheetIndex,
                'token' => $token,
                'workbook_analysis' => $workbookAnalysis,
            ], 422);
        }

        // Header row
        $headers = array_shift($allRows);

        // Translate headers to Arabic
        $headerTranslations = [];
        foreach ($headers as $colIdx => $hdr) {
            $headerTranslations[$colIdx] = $this->importService->translateHeaderToArabic((string) $hdr);
        }

        // Auto map columns for the active section
        $suggestedMapping = $this->importService->autoMapColumns($headers, $section);

        // First 5 rows for live preview
        $previewRows = array_slice($allRows, 0, 5);

        return response()->json([
            'success' => true,
            'token' => $token,
            'is_multi_sheet' => count($workbookAnalysis['sheets']) > 1,
            'workbook_analysis' => $workbookAnalysis,
            'section' => $section,
            'section_name' => DataImportService::SECTIONS[$section]['name'] ?? $section,
            'section_color' => DataImportService::SECTIONS[$section]['color'] ?? '#10b981',
            'section_icon' => DataImportService::SECTIONS[$section]['icon'] ?? 'bi-table',
            'sheets' => $sheets,
            'active_sheet_index' => $sheetIndex,
            'headers' => $headers,
            'header_translations' => $headerTranslations,
            'preview_rows' => $previewRows,
            'total_rows' => count($allRows),
            'suggested_mapping' => $suggestedMapping,
            'target_fields' => DataImportService::SECTIONS[$section]['fields'] ?? [],
        ]);
    }

    /**
     * Step 2: Confirm Mapping & Execute Database Import for a Single Sheet
     */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'section' => 'required|string|in:' . implode(',', array_keys(DataImportService::SECTIONS)),
            'mapping' => 'required|array',
            'sheet_index' => 'nullable|integer',
        ]);

        $token = $request->input('token');
        $section = $request->input('section');
        $mapping = $request->input('mapping');
        $sheetIndex = (int) $request->input('sheet_index', 0);

        $tempDir = storage_path('app/imports');

        // Look for file by token
        $filePath = null;
        $ext = 'xlsx';
        foreach (['xlsx', 'xls', 'csv', 'txt'] as $checkExt) {
            if (file_exists($tempDir . '/' . $token . '.' . $checkExt)) {
                $filePath = $tempDir . '/' . $token . '.' . $checkExt;
                $ext = $checkExt;
                break;
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية جلسة رفع الملف، يرجى إعادة اختيار الملف والمحاولة مرة أخرى.',
            ], 419);
        }

        $allRows = $this->importService->parseFile($filePath, $ext, $sheetIndex);
        if (empty($allRows) || count($allRows) < 2) {
            if (file_exists($filePath)) unlink($filePath);
            return response()->json([
                'success' => false,
                'message' => 'تعذر قراءة بيانات الملف أو الورقة المحددة.',
            ], 422);
        }

        // Drop headers row
        array_shift($allRows);

        $userId = (int) auth()->id();
        $result = $this->importService->executeImport($section, $allRows, $mapping, $userId);

        // Clean up temporary file
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        session()->forget('import_file_' . $token);

        return response()->json($result);
    }

    /**
     * Step 3 (Advanced): Batch Import and Distribute Multiple Sheets Across Sections in One Click
     */
    public function batchProcess(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'sheets' => 'required|array|min:1',
            'sheets.*.sheet_index' => 'required|integer',
            'sheets.*.section' => 'required|string|in:' . implode(',', array_keys(DataImportService::SECTIONS)),
            'sheets.*.mapping' => 'required|array',
        ]);

        $token = $request->input('token');
        $sheetsConfig = $request->input('sheets');
        $tempDir = storage_path('app/imports');

        $filePath = null;
        $ext = 'xlsx';
        foreach (['xlsx', 'xls', 'csv', 'txt'] as $checkExt) {
            if (file_exists($tempDir . '/' . $token . '.' . $checkExt)) {
                $filePath = $tempDir . '/' . $token . '.' . $checkExt;
                $ext = $checkExt;
                break;
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية جلسة رفع الملف، يرجى إعادة اختيار الملف والمحاولة مرة أخرى.',
            ], 419);
        }

        $userId = (int) auth()->id();
        $totalImported = 0;
        $totalDuplicates = 0;
        $totalSkipped = 0;
        $allErrors = [];
        $sheetResults = [];

        foreach ($sheetsConfig as $item) {
            $sheetIdx = (int) $item['sheet_index'];
            $section = $item['section'];
            $mapping = $item['mapping'];
            $sheetName = $item['sheet_name'] ?? ('ورقة #' . ($sheetIdx + 1));

            $allRows = $this->importService->parseFile($filePath, $ext, $sheetIdx);
            if (empty($allRows) || count($allRows) < 2) {
                continue;
            }

            array_shift($allRows); // drop header

            $res = $this->importService->executeImport($section, $allRows, $mapping, $userId);

            $imported = $res['imported'] ?? 0;
            $duplicates = $res['duplicates'] ?? 0;
            $skipped = $res['skipped'] ?? 0;

            $totalImported += $imported;
            $totalDuplicates += $duplicates;
            $totalSkipped += $skipped;

            if (!empty($res['errors'])) {
                foreach ($res['errors'] as $e) {
                    $allErrors[] = "[$sheetName] $e";
                }
            }

            $sheetResults[] = [
                'sheet_index' => $sheetIdx,
                'sheet_name' => $sheetName,
                'section' => $section,
                'section_name' => DataImportService::SECTIONS[$section]['name'] ?? $section,
                'total_rows' => count($allRows),
                'imported' => $imported,
                'duplicates' => $duplicates,
                'skipped' => $skipped,
            ];
        }

        // Clean up temporary file
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        session()->forget('import_file_' . $token);

        return response()->json([
            'success' => true,
            'is_batch' => true,
            'message' => "تم استيراد وتوزيع " . number_format($totalImported) . " سجلاً بنجاح عبر " . count($sheetResults) . " ورقة عمل في أقسام المنظومة.",
            'total_imported' => $totalImported,
            'total_duplicates' => $totalDuplicates,
            'total_skipped' => $totalSkipped,
            'total_rows' => $totalImported + $totalDuplicates + $totalSkipped,
            'errors' => $allErrors,
            'sheet_results' => $sheetResults,
        ]);
    }
}
