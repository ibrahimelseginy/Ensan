<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استمارة الدعم النفسي الشهري — {{ $patientName }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #fdf2f8 0%, #f8fafc 100%);
            min-height: 100vh;
            padding: 24px 12px;
        }
        .form-card {
            max-width: 720px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            border: 0;
            box-shadow: 0 15px 40px rgba(236, 72, 153, 0.12);
        }
        .form-header {
            background: linear-gradient(135deg, #ec4899 0%, #e11d48 100%);
            color: #fff;
            padding: 28px 24px;
        }
    </style>
</head>
<body>
    <div class="card form-card bg-white">
        <div class="form-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-heart-pulse-fill fs-3 text-white"></i>
                    </div>
                    <div>
                        <div class="small opacity-75 fw-semibold">مؤسسة إنسان الخيرية — بعثاء الأمل</div>
                        <h4 class="fw-bold mb-0">استمارة متابعة الدعم النفسي الشهري</h4>
                    </div>
                </div>
                <span class="badge bg-white text-danger font-monospace px-3 py-2 rounded-pill fw-bold">
                    كود الحالة: {{ $patientCode }}
                </span>
            </div>
            <div class="mt-3 pt-2 border-top border-white border-opacity-25 small">
                <i class="bi bi-person-heart me-1"></i> اسم الحالة / المريض: <strong class="fs-6">{{ $patientName }}</strong>
                @if($beneficiary && $beneficiary->full_name !== $patientName)
                    <span class="ms-2 opacity-75">(ملف الأسرة: {{ $beneficiary->full_name }})</span>
                @endif
            </div>
        </div>

        <div class="card-body p-4">
            @if(session('submitted_success'))
                <div class="text-center py-5">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="bi bi-check-lg"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold text-success mb-2">تم تسجيل الاستمارة بنجاح!</h4>
                    <p class="text-muted mb-4">{{ session('submitted_success') }}</p>
                    <a href="{{ request()->url() }}" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                        <i class="bi bi-arrow-repeat me-1"></i> إرسال تقرير آخر
                    </a>
                </div>
            @else
                @if($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.psychological-support.submit', ['type' => $type, 'id' => $id, 'token' => $token]) }}">
                    @csrf
                    <div class="row g-3">
                        @if($type === 'beneficiary' && $beneficiary && $beneficiary->familyMembers->isNotEmpty())
                            <div class="col-12">
                                <label class="form-label small fw-bold">الفرد المريض داخل الأسرة (اختياري)</label>
                                <select name="family_member_id" class="form-select">
                                    <option value="">— الحالة الأساسية ({{ $patientName }}) —</option>
                                    @foreach($beneficiary->familyMembers as $fm)
                                        <option value="{{ $fm->id }}" @selected($fm->is_patient)>{{ $fm->full_name }} ({{ $fm->relationship_label }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">الشهر المستهدف <span class="text-danger">*</span></label>
                            <input type="month" name="report_month" class="form-control fw-bold font-monospace" value="{{ old('report_month', now()->format('Y-m')) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">اسم المتطوع ثلاثي <span class="text-danger">*</span></label>
                            <input type="text" name="volunteer_name" class="form-control" value="{{ old('volunteer_name') }}" required placeholder="اكتب اسمك ثلاثي">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">الحالة الصحية / نوع الورم</label>
                            <input type="text" name="medical_condition" class="form-control" value="{{ old('medical_condition', $medicalCondition) }}" placeholder="مثال: ورم بالمخ، أورام دم، مستقرة...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">المرحلة العلاجية الحالية</label>
                            <input type="text" name="treatment_stage" class="form-control" value="{{ old('treatment_stage') }}" placeholder="مثال: جلسات كيماوي مكثفة، إشعاعي، متابعة شهرية...">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-danger">الحالة النفسية العامة للمريض <span class="text-danger">*</span></label>
                            <select name="psychological_state" class="form-select fw-bold" required>
                                <option value="ممتاز" @selected(old('psychological_state') === 'ممتاز')>ممتاز (متفائل ومتقبل للعلاج بروح عالية)</option>
                                <option value="كويس" @selected(old('psychological_state', 'كويس') === 'كويس')>كويس (مستقر نفسياً ومتعاون)</option>
                                <option value="راضي" @selected(old('psychological_state') === 'راضي')>راضي (صابر ومتقبل لكن يمر بفتور)</option>
                                <option value="حزين" @selected(old('psychological_state') === 'حزين')>حزين (يمر بضيق أو قلق أو مخاوف)</option>
                                <option value="يشعر بالضجر" @selected(old('psychological_state') === 'يشعر بالضجر')>يشعر بالضجر (متعب جداً ومحبط ويحتاج تدخل معنوي عاجل)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-warning">التقييم العام للحالة النفسية (من 1 إلى 5) <span class="text-danger">*</span></label>
                            <select name="rating" class="form-select fw-bold font-monospace" required>
                                <option value="5" @selected(old('rating', 5) == 5)>★★★★★ (5 نجوم - ممتاز جداً)</option>
                                <option value="4" @selected(old('rating') == 4)>★★★★☆ (4 نجوم - جيد جداً)</option>
                                <option value="3" @selected(old('rating') == 3)>★★★☆☆ (3 نجوم - متوسط ومستقر)</option>
                                <option value="2" @selected(old('rating') == 2)>★★☆☆☆ (نجمتان - ضعيف ويحتاج اهتمام)</option>
                                <option value="1" @selected(old('rating') == 1)>★☆☆☆☆ (نجمة واحدة - حرج نفسياً)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">ظروف وسياق المكالمة / المقابلة</label>
                            <textarea name="call_condition" rows="2" class="form-control" placeholder="مدة المكالمة، تجاوب الحالة، من قام بالرد (المريض أم المرافق)...">{{ old('call_condition') }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">النظام الغذائي والتغذية</label>
                            <textarea name="nutrition_diet" rows="2" class="form-control" placeholder="مدى التزام المريض بتعليمات الأطباء والتغذية السليمة...">{{ old('nutrition_diet') }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">ما يحتاجه المريض لتحسين حالته النفسية</label>
                            <textarea name="needs_improvement" rows="2" class="form-control" placeholder="احتياجات معنوية، زيارة ميدانية، أدوية، توفير متطلبات...">{{ old('needs_improvement') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">رأي متطوع الدعم النفسي والتوصيات</label>
                            <textarea name="supporter_opinion" rows="2" class="form-control" placeholder="رأيك وتوصياتك الطبية والمعنوية تجاه هذه الحالة للمرحلة القادمة...">{{ old('supporter_opinion') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">ملاحظات عامة إضافية</label>
                            <input type="text" name="general_notes" class="form-control" value="{{ old('general_notes') }}" placeholder="أي ملاحظات أخرى تود تسجيلها...">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-grid">
                        <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-send-check-fill me-1"></i> حفظ وإرسال استمارة الدعم النفسي للنظام
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</body>
</html>

