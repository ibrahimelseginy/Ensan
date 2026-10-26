@extends('layouts.app')
@section('content')
<div class="container-fluid">
  {{-- Page Header --}}
  <div class="page-header">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar avatar-lg avatar-primary">
        {{ mb_substr($beneficiary->full_name, 0, 1) }}
      </div>
      <div>
        <h4 class="mb-1">{{ $beneficiary->full_name }}</h4>
        <div class="d-flex gap-2">
          @php
            $statusClass = match ($beneficiary->status) {
              'pending' => 'bg-info-subtle text-info',
              'new' => 'bg-info-subtle text-info',
              'under_review' => 'bg-warning-subtle text-warning',
              'accepted' => 'bg-success-subtle text-success',
              'rejected' => 'bg-danger-subtle text-danger',
              'archived_improved' => 'bg-secondary-subtle text-secondary',
              'archived_deceased' => 'bg-dark text-white',
              default => 'bg-secondary-subtle text-secondary'
            };
            $statusText = match ($beneficiary->status) {
              'pending' => 'تحت التقديم',
              'new' => 'جديد',
              'under_review' => 'تحت المراجعة',
              'accepted' => 'مقبول',
              'rejected' => 'مرفوض',
              'archived_improved' => 'أرشيف — تحسن / شفاء',
              'archived_deceased' => 'أرشيف — توفي',
              default => $beneficiary->status
            };
          @endphp
          <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
          <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-1 fw-bold fs-6">
            <i class="bi bi-award-fill me-1"></i> تصنيف ({{ $beneficiary->classification ?? 'A' }})
          </span>
          @if(!empty($isDup) && $isDup)
            <span class="badge bg-danger">مكرر محتمل</span>
          @endif
        </div>
      </div>
    </div>
    <div class="btn-group">
      <button type="button" onclick="window.EnsanPrintPreview ? window.EnsanPrintPreview.open('#printableBeneficiaryDossier', {title: 'استمارة وملف المستفيد — {{ $beneficiary->full_name }}'}) : window.print()" class="btn btn-outline-info" title="معاينة الاستمارة الرسمية قبل الطباعة">
        <i class="bi bi-eye me-1"></i> معاينة الاستمارة الرسمية
      </button>
      <button type="button" onclick="window.EnsanPrintPreview ? window.EnsanPrintPreview.open('#printableBeneficiaryDossier', {title: 'استمارة وملف المستفيد — {{ $beneficiary->full_name }}'}) : window.print()" class="btn btn-outline-primary" title="طباعة الاستمارة الرسمية">
        <i class="bi bi-printer me-1"></i> طباعة الاستمارة
      </button>
      @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
        <a class="btn btn-outline-primary" href="{{ route('beneficiaries.edit', $beneficiary) }}">
          <i class="bi bi-pencil me-1"></i> تعديل
        </a>
      @else
        <a class="btn btn-outline-warning" href="{{ route('beneficiaries.edit', $beneficiary) }}">
          <i class="bi bi-pencil me-1"></i> طلب تعديل
        </a>
      @endif
      <a class="btn btn-outline-success" href="{{ route('visits.create', ['beneficiary_id' => $beneficiary->id]) }}" title="تسجيل استمارة بحث ميداني واجتماعي لهذه الحالة">
        <i class="bi bi-file-earmark-medical me-1"></i> استمارة بحث ميداني
      </a>
      <a href="{{ route('beneficiaries.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i> رجوع
      </a>
    </div>
  </div>

  {{-- Success/Error Messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-4">
    {{-- Main Info --}}
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-person-lines-fill"></i>
            <h5 class="mb-0">المعلومات الأساسية</h5>
          </div>

          <div class="row g-3">
            <div class="col-md-4">
              <div class="info-row">
                <span class="info-label">كود المستفيد</span>
                <span class="info-value font-monospace">{{ $beneficiary->code ?? '—' }}</span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-row">
                <span class="info-label">تصنيف الملف</span>
                <span class="info-value">
                  <span class="badge bg-primary text-white px-2 py-1 fw-bold">تصنيف ({{ $beneficiary->classification ?? 'A' }})</span>
                </span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-row">
                <span class="info-label">الحالة</span>
                <span class="info-value">
                  <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-row bg-light rounded-3 p-2">
                <span class="info-label text-success fw-bold"><i class="bi bi-person-plus-fill me-1"></i> أضيف بواسطة</span>
                <span class="info-value">
                  @php
                    $actualCreator = $beneficiary->actual_creator ?? $beneficiary->creator;
                    $actualApprover = $beneficiary->actual_approver;
                  @endphp
                  @if($actualCreator)
                    <strong class="text-dark">{{ $actualCreator->name }}</strong>
                    @if($actualApprover && $actualApprover->id !== $actualCreator->id)
                      <span class="badge bg-white text-secondary border px-2 py-0.5 small ms-1" title="تم اعتماد وقيد الملف بواسطة {{ $actualApprover->name }}">
                        <i class="bi bi-check2-circle text-success me-1"></i>اعتماد: {{ $actualApprover->name }}
                      </span>
                    @endif
                    <span class="text-muted small d-block font-monospace"><i class="bi bi-clock me-1"></i>{{ $beneficiary->created_at?->format('Y-m-d H:i') }}</span>
                  @else
                    <span class="text-muted">النظام الأساسي ({{ $beneficiary->created_at?->format('Y-m-d') }})</span>
                  @endif
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-row bg-light rounded-3 p-2">
                <span class="info-label text-primary fw-bold"><i class="bi bi-pencil-square me-1"></i> آخر تعديل بواسطة</span>
                <span class="info-value">
                  @php
                    $actualUpdater = $beneficiary->actual_updater ?? $beneficiary->updater;
                    $actualUpdateApprover = $beneficiary->actual_update_approver;
                  @endphp
                  @if($actualUpdater)
                    <strong class="text-dark">{{ $actualUpdater->name }}</strong>
                    @if($actualUpdateApprover && $actualUpdateApprover->id !== $actualUpdater->id)
                      <span class="badge bg-white text-secondary border px-2 py-0.5 small ms-1" title="تم اعتماد التعديل بواسطة {{ $actualUpdateApprover->name }}">
                        <i class="bi bi-check2-circle text-primary me-1"></i>اعتماد: {{ $actualUpdateApprover->name }}
                      </span>
                    @endif
                    <span class="text-muted small d-block font-monospace"><i class="bi bi-clock me-1"></i>{{ $beneficiary->updated_at?->format('Y-m-d H:i') }}</span>
                  @else
                    <span class="text-muted">لم يُعدل بعد ({{ $beneficiary->updated_at?->format('Y-m-d') }})</span>
                  @endif
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">نوع المساعدة</span>
                <span class="info-value">
                  @php
                    $typeText = match ($beneficiary->assistance_type) {
                      'financial' => 'مالية',
                      'monthly' => 'كفالة شهرية',
                      'one_time' => 'مساعدة مؤقتة',
                      'in_kind' => 'عينية',
                      'service' => 'خدمية',
                      default => $beneficiary->assistance_type
                    };
                  @endphp
                  {{ $typeText }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">الهاتف</span>
                <span class="info-value font-monospace">{{ $beneficiary->phone ?? '—' }}</span>
              </div>
            </div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الهاتف الإضافي</span><span class="info-value font-monospace">{{ $beneficiary->backup_phone ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الرقم القومي</span><span class="info-value font-monospace">{{ $beneficiary->national_id ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">رقم الفيزا</span><span class="info-value font-monospace">{{ $beneficiary->visa_card_number ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">المشروع</span><span class="info-value">{{ $beneficiary->project?->name ?? '—' }}</span></div></div>
            <div class="col-12">
              <div class="info-row">
                <span class="info-label">العنوان</span>
                <span class="info-value">{{ $beneficiary->address ?? '—' }}</span>
              </div>
            </div>
            @if(!empty($beneficiary->notes))
              <div class="col-12">
                <div class="p-3 bg-light rounded">
                  <div class="text-muted small mb-2">ملاحظات</div>
                  <div>{{ $beneficiary->notes }}</div>
                </div>
              </div>
            @endif
            @if(in_array($beneficiary->status, ['rejected', 'archived_improved', 'archived_deceased'], true))
              <div class="col-12"><div class="alert alert-secondary mb-0"><strong>سبب الحالة:</strong> {{ $beneficiary->status === 'rejected' ? ($beneficiary->rejection_reason ?? '—') : ($beneficiary->archived_reason ?? '—') }}</div></div>
            @endif
          </div>
        </div>
      </div>

      @php
        $activeFamilyMembers = $beneficiary->familyMembers->where('active', true);
      @endphp
      @if($activeFamilyMembers->isNotEmpty())
        <div class="card mb-4">
          <div class="card-body">
            <div class="section-title mb-3"><i class="bi bi-people-fill"></i><h5 class="mb-0">ملفات أفراد الأسرة</h5></div>
            <div class="alert alert-info py-2"><i class="bi bi-shield-lock me-1"></i> كل فرد ملف مستقل، والكفلاء الظاهرون أمامه مرتبطون بهذا الفرد فقط.</div>
            <div class="row g-3">
              @foreach($activeFamilyMembers as $member)
                <div class="col-md-6">
                  <div class="border rounded-3 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                      <div><div class="fw-bold fs-6">{{ $member->full_name }}</div><div class="small text-muted">{{ $member->relationship_label }} @if($member->is_patient) — مريض @endif</div></div>
                      <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-primary-subtle text-primary font-monospace">{{ $member->code }}</span>
                        <a href="{{ route('beneficiary-family-members.show', $member) }}" class="btn btn-sm btn-outline-primary" title="فتح الملف المستقل"><i class="bi bi-box-arrow-up-left"></i></a>
                      </div>
                    </div>
                    <div class="row g-2 small mb-3">
                      <div class="col-6"><span class="text-muted">العمر:</span> {{ $member->birth_date ? $member->birth_date->age . ' سنة' : ($member->age ? $member->age . ' سنة' : '—') }}</div>
                      <div class="col-6"><span class="text-muted">الميلاد:</span> {{ optional($member->birth_date)->format('Y-m-d') ?? '—' }}</div>
                      <div class="col-6"><span class="text-muted">التعليم:</span> {{ $member->education_level ?? '—' }}</div>
                      <div class="col-6"><span class="text-muted">الكفالة:</span> {{ $member->sponsorship_amount ? number_format((float)$member->sponsorship_amount, 2) . ' ج' : '—' }}</div>
                      @if($member->phone)<div class="col-12"><span class="text-muted">الهاتف:</span> {{ $member->phone }}</div>@endif
                      @if($member->case_details)<div class="col-12"><span class="text-muted">التفاصيل:</span> {{ $member->case_details }}</div>@endif
                    </div>
                    <div class="border-top pt-2">
                      <div class="small fw-bold mb-2">الكفلاء / المتبرعون</div>
                      <div class="d-flex flex-wrap gap-1">
                        @forelse($member->sponsors as $sponsor)
                          <a href="{{ route('donors.show', $sponsor) }}" class="badge bg-success-subtle text-success text-decoration-none p-2"><i class="bi bi-person-heart me-1"></i>{{ $sponsor->name }}</a>
                        @empty
                          <span class="small text-muted">لم يتم ربط كافل بهذا الفرد حتى الآن.</span>
                        @endforelse
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif

      {{-- Allocation & Sponsorship --}}
      @if($beneficiary->allocation_type || $beneficiary->sponsors->isNotEmpty())
        <div class="card mb-4">
          <div class="card-body">
            <div class="section-title mb-3">
              <i class="bi bi-diagram-3"></i>
              <h5 class="mb-0">التخصيص والكفالة</h5>
            </div>
            <div class="row g-3">
              @if($beneficiary->allocation_type)
                <div class="col-md-6">
                  <div class="info-row align-items-start">
                    <span class="info-label">نوع التخصيص</span>
                    <span class="info-value">{{ $beneficiary->allocation_type }}</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-row align-items-start">
                    <span class="info-label">المستفيدون</span>
                    <span class="info-value d-flex flex-wrap gap-1">
                      @forelse($beneficiary->allocatedBeneficiaries as $allocatedBeneficiary)
                        <a href="{{ route('beneficiaries.show', $allocatedBeneficiary) }}" class="badge bg-primary-subtle text-primary text-decoration-none">
                          {{ $allocatedBeneficiary->full_name }}
                        </a>
                      @empty
                        —
                      @endforelse
                    </span>
                  </div>
                </div>
              @endif
              @if($beneficiary->sponsors->isNotEmpty())
                <div class="col-12">
                  <div class="info-row align-items-start">
                    <span class="info-label">المتبرعون / الكفلاء</span>
                    <span class="info-value d-flex flex-wrap gap-2">
                      @foreach($beneficiary->sponsors as $sponsor)
                        <a href="{{ route('donors.show', $sponsor) }}" class="badge bg-success-subtle text-success text-decoration-none p-2">
                          <i class="bi bi-person-heart me-1"></i>{{ $sponsor->name }} — {{ $sponsor->code ?? ('DON-'.$sponsor->id) }}
                        </a>
                      @endforeach
                    </span>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endif

      {{-- Donations History --}}
      <div class="card mb-4">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-gift"></i>
            <h5 class="mb-0">سجل التبرعات الموجهة</h5>
          </div>
          @if(isset($donations) && $donations->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>المتبرع</th>
                    <th>القيمة</th>
                    <th>التاريخ</th>
                    <th class="text-center" style="min-width: 150px;">الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($donations as $d)
                    <tr>
                      <td>{{ $d->id }}</td>
                      <td>
                        <a href="{{ route('donors.show', $d->donor_id) }}" class="text-decoration-none">
                          {{ $d->donor->name ?? 'فاعل خير' }}
                        </a>
                      </td>
                      <td>
                        <span
                          class="fw-bold">{{ $d->type == 'cash' ? number_format($d->amount, 2) : number_format($d->estimated_value, 2) }}</span>
                        <span class="text-muted small">{{ $d->currency }}</span>
                      </td>
                      <td>{{ $d->received_at?->format('Y-m-d') }}</td>
                      <td class="text-center">
                        <div class="d-inline-flex gap-1">
                          <a href="{{ route('donations.show', $d) }}" class="btn btn-sm btn-outline-primary px-2 py-1" title="عرض التفاصيل">
                            <i class="bi bi-eye me-1"></i> عرض
                          </a>
                          <a href="{{ route('donations.show', $d) }}?print=1" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1" title="طباعة الإيصال">
                            <i class="bi bi-printer me-1"></i> طباعة
                          </a>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-muted text-center py-3">لا توجد تبرعات مسجلة لهذا المستفيد</p>
          @endif
        </div>
      </div>

      {{-- Attachments & Official Documents Section --}}
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title mb-0">
              <i class="bi bi-paperclip text-primary fs-5"></i>
              <h5 class="mb-0 fw-bold">الأوراق والمرفقات الرسمية</h5>
            </div>
            @php
              $totalAttsCount = $beneficiary->attachments->count() + ($linkedCaseApplications?->filter(fn($app) => $app->id_image_path || $app->medical_report_path)->count() ?? 0);
            @endphp
            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill font-monospace">
              {{ $totalAttsCount }} مستند / ملف
            </span>
          </div>

          {{-- Upload Form --}}
          <form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded-3 border">
            @csrf
            <input type="hidden" name="entity_type" value="{{ \App\Models\Beneficiary::class }}">
            <input type="hidden" name="entity_id" value="{{ $beneficiary->id }}">
            <div class="row g-2 align-items-center">
              <div class="col-md-9">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-cloud-arrow-up me-1"></i> رفع مستند أو صورة جديدة للملف</label>
                <input type="file" name="file" class="form-control form-control-sm" required accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.zip">
                <div class="form-text small text-muted">يدعم الصور (JPG, PNG, WEBP) وملفات PDF وWord حتى 10 ميجابايت.</div>
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-bold shadow-sm">
                  <i class="bi bi-upload me-1"></i> رفع المستند
                </button>
              </div>
            </div>
          </form>

          {{-- 1. Documents from Linked Mobile Application if any --}}
          @if(isset($linkedCaseApplications) && $linkedCaseApplications->isNotEmpty())
            @php
              $hasLinkedDocs = $linkedCaseApplications->contains(fn($app) => !empty($app->id_image_path) || !empty($app->medical_report_path));
            @endphp
            @if($hasLinkedDocs)
              <div class="mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                  <i class="bi bi-file-earmark-person-fill text-success me-1"></i> الأوراق والمستندات المرفوعة من استمارة التقديم والبحث
                </h6>
                <div class="row g-3">
                  @foreach($linkedCaseApplications as $caseApp)
                    @if($caseApp->id_image_path)
                      @php
                        $idUrl = $caseApp->id_image_url ?? (Storage::disk('public')->exists($caseApp->id_image_path) ? Storage::disk('public')->url($caseApp->id_image_path) : asset('storage/' . ltrim($caseApp->id_image_path, '/')));
                      @endphp
                      <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-white h-100 shadow-sm d-flex flex-column justify-content-between">
                          <div class="d-flex align-items-center gap-3 mb-2">
                            <a href="{{ $idUrl }}" target="_blank" class="d-block text-decoration-none">
                              <img src="{{ $idUrl }}" alt="صورة البطاقة" class="rounded border shadow-xs" style="width: 70px; height: 70px; object-fit: cover;">
                            </a>
                            <div class="flex-grow-1 overflow-hidden">
                              <span class="badge bg-success-subtle text-success small mb-1">صورة بطاقة الرقم القومي</span>
                              <div class="fw-bold text-truncate small" title="{{ basename($caseApp->id_image_path) }}">{{ basename($caseApp->id_image_path) }}</div>
                              <div class="text-muted" style="font-size: 0.75rem;">استمارة تقديم #{{ $caseApp->reference_number ?? $caseApp->id }}</div>
                            </div>
                          </div>
                          <div class="d-flex gap-2 mt-2 pt-2 border-top">
                            <a href="{{ $idUrl }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                              <i class="bi bi-eye me-1"></i> عرض الصورة
                            </a>
                            <a href="{{ $idUrl }}" download class="btn btn-sm btn-light border" title="تحميل">
                              <i class="bi bi-download"></i>
                            </a>
                          </div>
                        </div>
                      </div>
                    @endif

                    @if($caseApp->medical_report_path)
                      @php
                        $medUrl = $caseApp->medical_report_url ?? (Storage::disk('public')->exists($caseApp->medical_report_path) ? Storage::disk('public')->url($caseApp->medical_report_path) : asset('storage/' . ltrim($caseApp->medical_report_path, '/')));
                        $isMedImage = in_array(strtolower(pathinfo($caseApp->medical_report_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp'], true);
                      @endphp
                      <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-white h-100 shadow-sm d-flex flex-column justify-content-between">
                          <div class="d-flex align-items-center gap-3 mb-2">
                            @if($isMedImage)
                              <a href="{{ $medUrl }}" target="_blank" class="d-block text-decoration-none">
                                <img src="{{ $medUrl }}" alt="التقرير الطبي" class="rounded border shadow-xs" style="width: 70px; height: 70px; object-fit: cover;">
                              </a>
                            @else
                              <div class="rounded border bg-light d-flex align-items-center justify-content-center text-danger" style="width: 70px; height: 70px; font-size: 2rem;">
                                <i class="bi bi-file-earmark-medical"></i>
                              </div>
                            @endif
                            <div class="flex-grow-1 overflow-hidden">
                              <span class="badge bg-danger-subtle text-danger small mb-1">التقرير الطبي / أوراق الحالة</span>
                              <div class="fw-bold text-truncate small" title="{{ basename($caseApp->medical_report_path) }}">{{ basename($caseApp->medical_report_path) }}</div>
                              <div class="text-muted" style="font-size: 0.75rem;">استمارة تقديم #{{ $caseApp->reference_number ?? $caseApp->id }}</div>
                            </div>
                          </div>
                          <div class="d-flex gap-2 mt-2 pt-2 border-top">
                            <a href="{{ $medUrl }}" target="_blank" class="btn btn-sm btn-outline-danger flex-fill">
                              <i class="bi bi-eye me-1"></i> فتح المستند
                            </a>
                            <a href="{{ $medUrl }}" download class="btn btn-sm btn-light border" title="تحميل">
                              <i class="bi bi-download"></i>
                            </a>
                          </div>
                        </div>
                      </div>
                    @endif
                  @endforeach
                </div>
              </div>
            @endif
          @endif

          {{-- 2. Direct File Attachments --}}
          @php
            $atts = $beneficiary->attachments;
          @endphp
          <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-folder2-open text-primary me-1"></i> مرفقات الملف المباشرة
          </h6>
          @if($atts->count() > 0)
            <div class="row g-3">
              @foreach($atts as $a)
                @php
                  $isImg = $a->is_image;
                  $fileUrl = route('attachments.show', $a);
                  $fileName = $a->original_name ?? basename($a->path);
                @endphp
                <div class="col-md-6">
                  <div class="border rounded-3 p-3 bg-white h-100 shadow-sm d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center gap-3 mb-2">
                      @if($isImg)
                        <a href="{{ $fileUrl }}" target="_blank" class="d-block text-decoration-none">
                          <img src="{{ $fileUrl }}" alt="{{ $fileName }}" class="rounded border shadow-xs" style="width: 65px; height: 65px; object-fit: cover;">
                        </a>
                      @else
                        <div class="rounded border bg-light d-flex align-items-center justify-content-center text-primary" style="width: 65px; height: 65px; font-size: 1.8rem;">
                          <i class="bi bi-file-earmark-text"></i>
                        </div>
                      @endif
                      <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-bold text-truncate small" title="{{ $fileName }}">{{ $fileName }}</div>
                        <div class="text-muted small font-monospace"><i class="bi bi-clock me-1"></i>{{ $a->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                      </div>
                    </div>
                    <div class="d-flex gap-2 mt-2 pt-2 border-top align-items-center">
                      <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="bi bi-eye me-1"></i> فتح
                      </a>
                      <a href="{{ $fileUrl }}" download class="btn btn-sm btn-light border" title="تحميل">
                        <i class="bi bi-download"></i>
                      </a>
                      <form method="POST" action="{{ route('attachments.destroy', $a) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف نهائياً؟');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="حذف">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            @if(!isset($hasLinkedDocs) || !$hasLinkedDocs)
              <div class="empty-state py-4 text-center text-muted">
                <i class="bi bi-folder2-open display-4 d-block mb-2 text-secondary opacity-50"></i>
                <p class="mb-0">لا توجد ملفات أو أوراق مرفقة حالياً لهذا المستفيد.</p>
              </div>
            @endif
          @endif
        </div>
      </div>

      {{-- Activity & Audit Log Section (سجل الحركات والتعديلات) --}}
      @php
        $crList = isset($changeRequests) ? $changeRequests : collect();
        $auditList = isset($audits) ? $audits : collect();
        $creationCr = $crList->firstWhere('action', 'create');
        $initialCreator = $beneficiary->actual_creator ?? ($creationCr?->user ?? $beneficiary->creator);
        $initialApprover = $beneficiary->actual_approver ?? $creationCr?->reviewer;
        $totalRegisteredEvents = $crList->count() + $auditList->count();
        if ($totalRegisteredEvents === 0) {
            $totalRegisteredEvents = 1;
        }
      @endphp
      <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <span class="p-2 bg-info-subtle text-info rounded-3">
              <i class="bi bi-clock-history fs-5"></i>
            </span>
            <div>
              <h5 class="mb-0 fw-bold">سجل الحركات والتعديلات على الملف</h5>
              <div class="text-muted small">تتبع دقيق لجميع الإجراءات والتحديثات والمستخدمين المسؤولين عنها</div>
            </div>
          </div>
          <span class="badge bg-secondary-subtle text-secondary font-monospace px-3 py-2 rounded-pill">
            {{ $totalRegisteredEvents }} إجراء مسجل
          </span>
        </div>
        <div class="card-body p-4">
          <div class="activity-timeline position-relative ps-4" style="border-right: 2px dashed #cbd5e1; margin-right: 15px;">
            
            {{-- Change Requests (Workflow Movements & Updates) --}}
            @foreach($crList as $cr)
              @php
                $isCreate = $cr->action === 'create';
                $isUpdate = $cr->action === 'update';
                $isDelete = $cr->action === 'delete';

                $badgeClass = match($cr->action) {
                  'create' => 'bg-success',
                  'update' => 'bg-primary',
                  'delete' => 'bg-danger',
                  default => 'bg-info'
                };
                $actionLabel = match($cr->action) {
                  'create' => 'إضافة وقيد جديد',
                  'update' => 'تعديل وتحديث بيانات',
                  'delete' => 'طلب إلغاء / حذف الملف',
                  default => $cr->action
                };
                $dotColor = match($cr->action) {
                  'create' => '#059669',
                  'update' => '#0284c7',
                  'delete' => '#dc2626',
                  default => '#0ea5e9'
                };

                $requesterName = $cr->user?->name ?? 'مستخدم غير محدد';
                $reviewerName = $cr->reviewer?->name;

                // Extract diff / modified fields
                $diff = [];
                if (is_array($cr->payload)) {
                    if (isset($cr->payload['diff']) && is_array($cr->payload['diff'])) {
                        $diff = $cr->payload['diff'];
                    } elseif (isset($cr->payload['data']) && is_array($cr->payload['data'])) {
                        $diff = $cr->payload['data'];
                    } elseif ($isUpdate) {
                        $diff = $cr->payload;
                    }
                }
              @endphp

              <div class="timeline-item mb-4 position-relative">
                <div class="position-absolute translate-middle-y" style="right: -24px; top: 14px; width: 16px; height: 16px; border-radius: 50%; background: {{ $dotColor }}; border: 3px solid #fff; box-shadow: 0 0 0 2px {{ $dotColor }};"></div>
                <div class="p-3 bg-light rounded-3 border">
                  
                  {{-- Header Info --}}
                  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="badge {{ $badgeClass }} text-white px-2 py-1 small">{{ $actionLabel }}</span>
                      
                      {{-- The user who actually did or requested the action --}}
                      <span class="d-inline-flex align-items-center bg-white border px-2 py-1 rounded-2 shadow-xs">
                        <i class="bi bi-person-fill text-primary me-1"></i>
                        <span class="text-muted small me-1">المسؤول عن الإجراء:</span>
                        <strong class="text-dark">{{ $requesterName }}</strong>
                      </span>

                      {{-- Approval / Review status & Reviewer --}}
                      @if($cr->status === 'approved')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                          <i class="bi bi-check-circle-fill me-1"></i>معتمد
                          @if($reviewerName && $reviewerName !== $requesterName)
                            <span class="ms-1 fw-normal text-dark">(بواسطة: {{ $reviewerName }})</span>
                          @endif
                        </span>
                      @elseif($cr->status === 'pending')
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">
                          <i class="bi bi-hourglass-split me-1"></i>قيد المراجعة والاعتماد
                        </span>
                      @elseif($cr->status === 'rejected')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">
                          <i class="bi bi-x-circle-fill me-1"></i>مرفوض
                          @if($reviewerName)
                            <span class="ms-1 fw-normal text-dark">(بواسطة: {{ $reviewerName }})</span>
                          @endif
                        </span>
                      @endif
                    </div>

                    {{-- Timestamps --}}
                    <div class="text-muted small font-monospace">
                      <i class="bi bi-clock me-1"></i>{{ $cr->created_at?->format('Y-m-d H:i:s') ?? '—' }}
                      @if($cr->created_at)
                        <span class="badge bg-secondary-subtle text-secondary ms-1">({{ $cr->created_at->diffForHumans() }})</span>
                      @endif
                    </div>
                  </div>

                  {{-- Details Description --}}
                  @if($isCreate)
                    <div class="text-secondary small">
                      <i class="bi bi-file-earmark-plus text-success me-1"></i>
                      قام الموظف <strong>{{ $requesterName }}</strong> بإنشاء وقيد ملف الحالة برقم كود (<strong>{{ $cr->payload['code'] ?? $beneficiary->code }}</strong>)
                      @if($reviewerName)
                        ، وتمت مراجعته واعتماده رسمياً بواسطة <strong>{{ $reviewerName }}</strong>.
                      @else
                        .
                      @endif
                    </div>
                  @elseif($isDelete)
                    <div class="text-danger small">
                      <i class="bi bi-trash text-danger me-1"></i>
                      طلب حذف الملف بواسطة <strong>{{ $requesterName }}</strong>
                      @if(!empty($cr->payload['note']))
                        — سبب الحذف: {{ $cr->payload['note'] }}
                      @endif
                    </div>
                  @elseif($isUpdate && !empty($diff))
                    <div class="mt-2">
                      <div class="small fw-bold text-dark mb-1">
                        <i class="bi bi-pencil-square text-primary me-1"></i>التعديلات التي قام بها الموظف ({{ $requesterName }}):
                      </div>
                      <div class="table-responsive bg-white rounded border">
                        <table class="table table-sm table-bordered mb-0 small">
                          <thead class="table-light">
                            <tr>
                              <th style="width: 28%">الحقل المعدل</th>
                              <th style="width: 36%" class="text-danger">القيمة السابقة</th>
                              <th style="width: 36%" class="text-success">القيمة الجديدة</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($diff as $key => $val)
                              @php
                                if (in_array($key, ['__is_wrapped', 'updated_at', 'created_at', 'id'], true)) continue;
                                $fieldLabel = \App\Support\ChangeRequestPresentation::fieldLabel((string)$key);
                                $fromVal = is_array($val) && array_key_exists('from', $val) ? $val['from'] : null;
                                $toVal = is_array($val) && array_key_exists('to', $val) ? $val['to'] : $val;

                                $fromFormatted = \App\Support\ChangeRequestPresentation::formatValue((string)$key, $fromVal);
                                $toFormatted = \App\Support\ChangeRequestPresentation::formatValue((string)$key, $toVal);
                              @endphp
                              <tr>
                                <td class="fw-bold text-secondary">{{ $fieldLabel }}</td>
                                <td class="text-danger text-break">{{ $fromFormatted ?: '—' }}</td>
                                <td class="text-success fw-bold text-break">{{ $toFormatted ?: '—' }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  @endif

                  {{-- Rejection reason if any --}}
                  @if($cr->rejection_reason)
                    <div class="mt-2 p-2 bg-danger-subtle text-danger rounded small">
                      <strong>سبب الرفض: </strong>{{ $cr->rejection_reason }}
                    </div>
                  @endif

                </div>
              </div>
            @endforeach

            {{-- Fallback: Additional Direct Audits if any --}}
            @if(isset($audits) && $audits->isNotEmpty())
              @foreach($audits as $audit)
                @php
                  $auditUser = $audit->user?->name ?? 'مستخدم غير محدد';
                  $action = $audit->action ?? 'تعديل';
                  $badgeClass = match(strtolower($action)) {
                    'created', 'insert' => 'bg-success',
                    'updated', 'update' => 'bg-primary',
                    'deleted', 'delete' => 'bg-danger',
                    default => 'bg-info'
                  };
                  $actionLabel = match(strtolower($action)) {
                    'created', 'insert' => 'إضافة وقيد جديد',
                    'updated', 'update' => 'تعديل وحفظ بيانات',
                    'deleted', 'delete' => 'حذف بيانات',
                    default => $action
                  };
                @endphp
                <div class="timeline-item mb-4 position-relative">
                  <div class="position-absolute translate-middle-y" style="right: -24px; top: 12px; width: 16px; height: 16px; border-radius: 50%; background: #0284c7; border: 3px solid #fff; box-shadow: 0 0 0 2px #0284c7;"></div>
                  <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $badgeClass }} text-white px-2 py-1 small">{{ $actionLabel }}</span>
                        <strong class="text-dark"><i class="bi bi-person-fill text-muted me-1"></i>المسؤول: {{ $auditUser }}</strong>
                      </div>
                      <div class="text-muted small font-monospace">
                        <i class="bi bi-clock me-1"></i>{{ $audit->created_at?->format('Y-m-d H:i:s') ?? '—' }}
                        @if($audit->created_at)
                          <span class="badge bg-secondary-subtle text-secondary ms-1">({{ $audit->created_at->diffForHumans() }})</span>
                        @endif
                      </div>
                    </div>
                    @if($audit->description)
                      <div class="text-secondary small">{{ $audit->description }}</div>
                    @endif
                    @if(!empty($audit->new_values) && is_array($audit->new_values))
                      <div class="mt-2 pt-2 border-top small">
                        <span class="text-muted fw-bold">الحقول المعدلة: </span>
                        @foreach(array_keys($audit->new_values) as $key)
                          <span class="badge bg-white text-dark border me-1 font-monospace">{{ \App\Support\ChangeRequestPresentation::fieldLabel((string)$key) }}</span>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>
              @endforeach
            @endif

            {{-- Initial Creation Event (Always anchored at base) --}}
            <div class="timeline-item position-relative">
              <div class="position-absolute translate-middle-y" style="right: -24px; top: 14px; width: 16px; height: 16px; border-radius: 50%; background: #059669; border: 3px solid #fff; box-shadow: 0 0 0 2px #059669;"></div>
              <div class="p-3 bg-success-subtle bg-opacity-25 rounded-3 border border-success-subtle">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-success text-white px-2 py-1 small">إنشاء وقيد الملف</span>
                    <span class="d-inline-flex align-items-center bg-white border px-2 py-1 rounded-2 shadow-xs">
                      <i class="bi bi-person-plus-fill text-success me-1"></i>
                      <span class="text-muted small me-1">أضاف الحالة:</span>
                      <strong class="text-success">{{ $initialCreator?->name ?? 'النظام الأساسي' }}</strong>
                    </span>
                    @if($initialApprover && $initialApprover->id !== $initialCreator?->id)
                      <span class="badge bg-white text-secondary border px-2 py-1 small">
                        <i class="bi bi-check2-circle text-success me-1"></i>اعتمد القيد: <strong class="text-dark">{{ $initialApprover->name }}</strong>
                      </span>
                    @endif
                  </div>
                  <div class="text-muted small font-monospace">
                    <i class="bi bi-calendar-event me-1"></i>{{ $beneficiary->created_at?->format('Y-m-d H:i:s') ?? '—' }}
                    @if($beneficiary->created_at)
                      <span class="badge bg-secondary-subtle text-secondary ms-1">({{ $beneficiary->created_at->diffForHumans() }})</span>
                    @endif
                  </div>
                </div>
                <div class="text-muted small">
                  تم تسجيل ملف الحالة وإصدار الكود الرسمي (<strong class="font-monospace text-dark">{{ $beneficiary->code }}</strong>) بنجاح.
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
      {{-- Quick Actions --}}
      <div class="card">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-lightning"></i>
            <h6 class="mb-0">إجراءات سريعة</h6>
          </div>
          <div class="d-grid gap-2">
            @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
              <a href="{{ route('beneficiaries.edit', $beneficiary) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> تعديل البيانات
              </a>
            @else
              <a href="{{ route('beneficiaries.edit', $beneficiary) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i> طلب تعديل بيانات
              </a>
            @endif
            <form method="POST" action="{{ route('beneficiaries.destroy', $beneficiary) }}"
              onsubmit="return confirm('{{ optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager') ? 'هل أنت متأكد من حذف هذا المستفيد؟' : 'هل أنت متأكد من طلب حذف هذا المستفيد؟' }}');">
              @csrf @method('DELETE')
              @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
                <button class="btn btn-outline-danger w-100">
                  <i class="bi bi-trash me-1"></i> حذف المستفيد
                </button>
              @else
                 <button class="btn btn-outline-warning w-100">
                  <i class="bi bi-x-circle me-1"></i> طلب إلغاء المستفيد
                </button>
              @endif
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Official Printable Beneficiary Dossier (Hidden in DOM, used for Print & Preview) --}}
@include('beneficiaries.partials.printable_dossier')

{{-- Print Styles --}}
<style>
  @media print {
    /* Hide everything on page by default */
    body > *,
    .navbar,
    .sidebar-fixed,
    .container-fluid,
    .modal-backdrop,
    #universalPrintPreviewModal,
    header,
    nav,
    footer {
      display: none !important;
      visibility: hidden !important;
    }

    /* Display ONLY the official printable dossier */
    #printableBeneficiaryDossier {
      display: block !important;
      visibility: visible !important;
      position: absolute !important;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      background: #ffffff !important;
      color: #0f172a !important;
    }

    #printableBeneficiaryDossier * {
      visibility: visible !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    #printableBeneficiaryDossier .official-voucher-card {
      border: 2px solid #059669 !important;
      box-shadow: none !important;
      max-width: 100% !important;
      padding: 20px !important;
    }
  }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
</style>

@endsection


