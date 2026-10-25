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
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480', // Up to 20MB
            'section' => 'required|string|in:' . implode(',', array_keys(DataImportService::SECTIONS)),
        ], [
            'file.required' => 'يرجى اختيار ملف الإكسيل أو الـ CSV المراد استيراده.',
            'file.mimes' => 'صيغة الملف غير مدعومة. الصيغ المسموح بها هي: XLSX, XLS, CSV.',
            'file.max' => 'الحد الأقصى لحجم الملف هو 20 ميجابايت.',
            'section.required' => 'يرجى تحديد القسم المستهدف.',
        ]);

        $uploadedFile = $request->file('file');
        $section = $request->input('section');
        $extension = $uploadedFile->getClientOriginalExtension();

        // Save file to temporary import cache
        $tempDir = storage_path('app/imports');
        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $token = 'imp_' . date('ymd_His') . '_' . Str::random(12);
        $tempFilePath = $tempDir . '/' . $token . '.' . $extension;
        $uploadedFile->move($tempDir, $token . '.' . $extension);

        // Parse file rows
        $allRows = $this->importService->parseFile($tempFilePath, $extension);

        if (empty($allRows) || count($allRows) < 2) {
            if (file_exists($tempFilePath)) unlink($tempFilePath);
            return response()->json([
                'success' => false,
                'message' => 'الملف المرفوع فارغ أو لا يحتوي على صفوف بيانات بعد صف العناوين.',
            ], 422);
        }

        // Header row
        $headers = array_shift($allRows);

        // Auto map columns
        $suggestedMapping = $this->importService->autoMapColumns($headers, $section);

        // Store rows in session/cache for step 2 execution
        session(['import_file_' . $token => [
            'path' => $tempFilePath,
            'extension' => $extension,
            'headers' => $headers,
            'rows_count' => count($allRows),
            'section' => $section,
        ]]);

        // First 5 rows for live preview
        $previewRows = array_slice($allRows, 0, 5);

        return response()->json([
            'success' => true,
            'token' => $token,
            'section' => $section,
            'section_name' => DataImportService::SECTIONS[$section]['name'],
            'headers' => $headers,
            'preview_rows' => $previewRows,
            'total_rows' => count($allRows),
            'suggested_mapping' => $suggestedMapping,
            'target_fields' => DataImportService::SECTIONS[$section]['fields'],
        ]);
    }

    /**
     * Step 2: Confirm Mapping & Execute Database Import
     */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'section' => 'required|string|in:' . implode(',', array_keys(DataImportService::SECTIONS)),
            'mapping' => 'required|array',
        ]);

        $token = $request->input('token');
        $section = $request->input('section');
        $mapping = $request->input('mapping');

        $sessionData = session('import_file_' . $token);
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

        $allRows = $this->importService->parseFile($filePath, $ext);
        if (empty($allRows) || count($allRows) < 2) {
            if (file_exists($filePath)) unlink($filePath);
            return response()->json([
                'success' => false,
                'message' => 'تعذر قراءة بيانات الملف.',
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
}
