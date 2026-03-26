<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Audit;
use Illuminate\Support\Facades\Schema;

class AuditLogger
{
    /**
     * الحقول الحساسة التي لا يجب تسجيلها
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        '_token',
        'token',
        'secret',
        'api_key',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $write = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);

        if ($write) {
            try {
                // تنظيف البيانات من الحقول الحساسة
                $payload = $this->sanitizePayload($request->except($this->sensitiveFields));

                // إضافة مسارات الملفات المرفوعة
                $uploadedFiles = $this->getUploadedFiles($request);
                if (!empty($uploadedFiles)) {
                    $payload['_uploaded_files'] = $uploadedFiles;
                }

                // استخراج نوع الـ Entity والـ ID من المسار
                [$entityType, $entityId] = $this->extractEntityInfo($request);

                $data = [
                    'user_id' => optional($request->user())->id,
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'payload' => $payload,
                ];

                if (Schema::hasColumn('audits', 'status_code')) {
                    $data['status_code'] = method_exists($response, 'getStatusCode')
                        ? $response->getStatusCode()
                        : null;
                }
                if (Schema::hasColumn('audits', 'ip')) {
                    $data['ip'] = $request->ip();
                }
                if (Schema::hasColumn('audits', 'user_agent')) {
                    $data['user_agent'] = $request->userAgent();
                }
                if (Schema::hasColumn('audits', 'entity_type') && $entityType) {
                    $data['entity_type'] = $entityType;
                }
                if (Schema::hasColumn('audits', 'entity_id') && $entityId) {
                    $data['entity_id'] = $entityId;
                }

                Audit::create($data);

            }
            catch (\Throwable $e) {
                // لا نوقف التطبيق بسبب فشل تسجيل الـ Audit
                \Illuminate\Support\Facades\Log::warning('AuditLogger failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * تنظيف البيانات من الحقول الحساسة بشكل متعمق
     */
    protected function sanitizePayload(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $this->sensitiveFields)) {
                unset($data[$key]);
            }
            elseif (is_array($value)) {
                $data[$key] = $this->sanitizePayload($value);
            }
        }
        return $data;
    }

    /**
     * استخراج معلومات الملفات المرفوعة
     */
    protected function getUploadedFiles(Request $request): array
    {
        $files = [];
        foreach ($request->allFiles() as $key => $file) {
            if (is_array($file)) {
                foreach ($file as $f) {
                    if ($f && $f->isValid()) {
                        $files[$key][] = $f->getClientOriginalName();
                    }
                }
            }
            elseif ($file && $file->isValid()) {
                $files[$key] = $file->getClientOriginalName();
            }
        }
        return $files;
    }

    /**
     * استخراج نوع الـ Entity والـ ID من route parameters
     */
    protected function extractEntityInfo(Request $request): array
    {
        $route = $request->route();
        if (!$route) {
            return [null, null];
        }

        $routeName = $route->getName() ?? '';
        $parts = explode('.', $routeName);
        $entityType = $parts[0] ?? null;

        // محاولة استخراج الـ ID من route parameters
        $entityId = null;
        $params = $route->parameters();
        if (!empty($params)) {
            $firstParam = reset($params);
            if (is_object($firstParam) && isset($firstParam->id)) {
                $entityId = $firstParam->id;
            }
            elseif (is_numeric($firstParam)) {
                $entityId = $firstParam;
            }
        }

        return [$entityType, $entityId];
    }
}
