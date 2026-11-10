<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * واجهة الموقع العامة لاستقبال طلبات التوظيف ومتابعة حالتها بكود المتابعة.
 */
final class JobApplicationApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name'         => 'required|string|min:5|max:191',
            'age'               => 'required|integer|min:16|max:70',
            'phone'             => ['required', 'string', 'max:30', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'email'             => 'required|email|max:191',
            'residence'         => 'required|string|max:191',
            'position'          => 'required|string|max:191',
            'faculty'           => 'required|string|max:191',
            'has_volunteered'   => 'required|in:0,1,true,false,yes,no',
            'volunteer_details' => 'nullable|required_if:has_volunteered,1,true,yes|string|max:2000',
            'cv'                => 'required|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'full_name.required'            => 'الاسم بالكامل مطلوب.',
            'age.required'                  => 'السن مطلوب.',
            'age.min'                       => 'السن يجب أن يكون 16 سنة على الأقل.',
            'phone.required'                => 'رقم الهاتف (واتساب) مطلوب.',
            'phone.regex'                   => 'رقم الهاتف غير صحيح.',
            'email.required'                => 'البريد الإلكتروني مطلوب.',
            'email.email'                   => 'البريد الإلكتروني غير صحيح.',
            'residence.required'            => 'محل الإقامة مطلوب.',
            'position.required'             => 'الوظيفة المتقدم عليها مطلوبة.',
            'faculty.required'              => 'الكلية التي تخرجت منها مطلوبة.',
            'volunteer_details.required_if' => 'من فضلك اكتب أين تطوعت من قبل.',
            'cv.required'                   => 'يرجى رفع السيرة الذاتية (CV).',
            'cv.mimes'                      => 'السيرة الذاتية يجب أن تكون PDF أو Word.',
            'cv.max'                        => 'حجم السيرة الذاتية يجب ألا يتجاوز 5 ميجابايت.',
        ]);

        $hasVolunteered = in_array(strtolower((string) $data['has_volunteered']), ['1', 'true', 'yes'], true);
        $cleanPhone = preg_replace('/[^0-9+]/', '', (string) $data['phone']);

        // منع تكرار نفس الطلب (نفس الهاتف ونفس الوظيفة) وهو ما زال مفتوحاً
        $existing = JobApplication::where('phone', $cleanPhone)
            ->where('position', trim($data['position']))
            ->whereNotIn('status', [JobApplication::STATUS_ACCEPTED, JobApplication::STATUS_REJECTED])
            ->latest('id')
            ->first();

        if ($existing) {
            return response()->json([
                'status'        => 'exists',
                'message'       => 'لديك طلب توظيف سابق على نفس الوظيفة وما زال قيد المراجعة. يمكنك متابعته بكود المتابعة.',
                'tracking_code' => $existing->tracking_code,
                'data'          => $existing->toPublicArray(),
            ]);
        }

        try {
            $cvPath = $request->file('cv')->store('job_applications/cvs', 'public');
            if (!$cvPath) {
                throw new \RuntimeException('تعذر حفظ ملف السيرة الذاتية.');
            }

            $application = JobApplication::create([
                'full_name'         => trim($data['full_name']),
                'age'               => (int) $data['age'],
                'phone'             => $cleanPhone,
                'email'             => strtolower(trim($data['email'])),
                'residence'         => trim($data['residence']),
                'position'          => trim($data['position']),
                'faculty'           => trim($data['faculty']),
                'has_volunteered'   => $hasVolunteered,
                'volunteer_details' => $hasVolunteered ? trim((string) ($data['volunteer_details'] ?? '')) : null,
                'cv_path'           => $cvPath,
                'status'            => JobApplication::STATUS_NEW,
                'source'            => 'website',
            ]);

            return response()->json([
                'status'        => 'success',
                'message'       => 'تم استلام طلب التوظيف بنجاح. احتفظ بكود المتابعة لمعرفة حالة طلبك.',
                'tracking_code' => $application->tracking_code,
                'data'          => $application->toPublicArray(),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Job application submit failed: ' . $e->getMessage(), [
                'request' => $request->except(['cv']),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ أثناء إرسال طلب التوظيف، يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }

    public function track(string $query): JsonResponse
    {
        $q = strtoupper(trim(ltrim(trim($query), '#')));
        if ($q === '') {
            return response()->json([
                'status'  => 'error',
                'message' => 'يرجى إدخال كود المتابعة أو رقم الهاتف.',
            ], 400);
        }

        $application = JobApplication::where('tracking_code', $q)->first();

        if (!$application) {
            $digits = preg_replace('/\D+/', '', $q);
            if (strlen($digits) >= 9) {
                $tail = substr($digits, -10);
                $application = JobApplication::whereRaw(
                    "REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', '') LIKE ?",
                    ['%' . $tail]
                )->latest('id')->first();
            }
        }

        if (!$application) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لم يتم العثور على طلب توظيف بهذا الكود أو رقم الهاتف.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $application->toPublicArray(),
        ]);
    }
}
