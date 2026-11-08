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
      @php
        $isGhCase = $beneficiary->is_guest_house_case;
        $printTitle = $isGhCase ? ('إستمارة دخول دار الضيافة — ' . $beneficiary->full_name) : ('استمارة وملف المستفيد — ' . $beneficiary->full_name);
        $previewLabel = $isGhCase ? 'معاينة إستمارة الدخول' : 'معاينة الاستمارة الرسمية';
        $printLabel = $isGhCase ? 'طباعة إستمارة الدخول' : 'طباعة الاستمارة';
      @endphp
      <button type="button" onclick="window.EnsanPrintPreview ? window.EnsanPrintPreview.open('#printableBeneficiaryDossier', {title: '{{ $printTitle }}'}) : window.print()" class="btn btn-outline-info" title="{{ $previewLabel }}">
        <i class="bi bi-eye me-1"></i> {{ $previewLabel }}
      </button>
      <button type="button" onclick="window.EnsanPrintPreview ? window.EnsanPrintPreview.open('#printableBeneficiaryDossier', {title: '{{ $printTitle }}'}) : window.print()" class="btn btn-outline-primary" title="{{ $printLabel }}">
        <i class="bi bi-printer me-1"></i> {{ $printLabel }}
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
      <button type="button" class="btn btn-teal text-white fw-bold" style="background:#0d9488;" data-bs-toggle="modal" data-bs-target="#reassessmentModal" title="إعادة بحث وتحديث دوري كل 6 أشهر مع الحفاظ على البيانات الأساسية">
        <i class="bi bi-arrow-repeat me-1"></i> إعادة بحث (6 شهور)
      </button>
      <button type="button" class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#beneficiaryPsychSupportModal" title="توثيق ومشاركة استمارة الدعم النفسي الشهري">
        <i class="bi bi-heart-pulse-fill me-1"></i> الدعم النفسي
      </button>
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
                  <div class="border rounded-3 p-3 h-100 bg-white shadow-xs position-relative">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                      <div>
                        <div class="fw-bold fs-6 text-dark">{{ $member->full_name }}</div>
                        <div class="small text-muted">{{ $member->relationship_label }} @if($member->is_patient) <span class="badge bg-danger-subtle text-danger ms-1">مريض أورام/علاج</span> @endif</div>
                      </div>
                      <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-primary-subtle text-primary font-monospace">{{ $member->code }}</span>
                        <a href="{{ route('beneficiary-family-members.show', $member) }}" class="btn btn-sm btn-outline-primary py-0.5 px-2" title="فتح الملف المستقل للحالة"><i class="bi bi-box-arrow-up-left"></i> ملف الحالة</a>
                      </div>
                    </div>
                    <div class="row g-2 small mb-3">
                      <div class="col-6"><span class="text-muted">العمر:</span> {{ $member->birth_date ? $member->birth_date->age . ' سنة' : ($member->age ? $member->age . ' سنة' : '—') }}</div>
                      <div class="col-6"><span class="text-muted">الميلاد:</span> {{ optional($member->birth_date)->format('Y-m-d') ?? ($member->age ? \Carbon\Carbon::now()->subYears((int) $member->age)->format('Y') . ' (تقديري)' : '—') }}</div>
                      <div class="col-6"><span class="text-muted">التعليم:</span> {{ $member->education_level ?? '—' }}</div>
                      <div class="col-6">
                        <span class="text-muted">الكفالة:</span> 
                        @if($member->sponsorship_amount && (float)$member->sponsorship_amount > 0)
                          <span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-0.5">{{ number_format((float)$member->sponsorship_amount, 2) }} ج.م</span>
                        @else
                          <span class="text-muted small">بدون كفالة خاصة</span>
                        @endif
                      </div>
                      @if($member->phone)<div class="col-12"><span class="text-muted">الهاتف:</span> {{ $member->phone }}</div>@endif
                      @if($member->case_details)<div class="col-12"><span class="text-muted">التفاصيل:</span> {{ $member->case_details }}</div>@endif
                    </div>
                    <div class="border-top pt-2">
                      <div class="d-flex justify-content-between align-items-center mb-1.5 flex-wrap gap-1">
                        <span class="small fw-bold text-dark"><i class="bi bi-person-heart text-success me-1"></i>كفلاء هذا الفرد:</span>
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                          @php
                            $memPsychShareUrl = \App\Http\Controllers\PsychologicalSupportController::getPublicUrl('member', $member->id);
                          @endphp
                          <a href="{{ route('beneficiary-family-members.show', $member) }}#psychologicalSupportSection" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill font-monospace" style="font-size: 0.75rem;" title="الدعم النفسي الشهري للحالة">
                            <i class="bi bi-heart-pulse-fill me-0.5"></i> الدعم النفسي
                          </a>
                          <button type="button" class="btn btn-sm btn-light border text-danger py-0 px-2 rounded-pill" style="font-size: 0.75rem;" onclick="navigator.clipboard.writeText('{{ $memPsychShareUrl }}'); alert('تم نسخ رابط استمارة الدعم النفسي للمتطوع:\n{{ $memPsychShareUrl }}');" title="نسخ رابط استمارة الدعم النفسي لإرساله لمتطوع">
                            <i class="bi bi-share-fill"></i> رابط للمتطوع
                          </button>
                          <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill font-monospace" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#attachMemberSponsorModal{{ $member->id }}">
                            <i class="bi bi-plus-circle me-0.5"></i> ربط كافل
                          </button>
                        </div>
                      </div>
                      <div class="d-flex flex-wrap gap-1">
                        @forelse($member->sponsors as $sponsor)
                          <div class="badge bg-success-subtle text-success border border-success p-1 d-inline-flex align-items-center gap-1">
                            <a href="{{ route('donors.show', $sponsor) }}" class="text-success text-decoration-none fw-bold small">
                              {{ $sponsor->name }}
                              @if($sponsor->pivot->monthly_amount)
                                ({{ number_format((float)$sponsor->pivot->monthly_amount) }} ج)
                              @endif
                            </a>
                            <form action="{{ route('beneficiary-family-members.detach-sponsor', [$member, $sponsor]) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد إلغاء ربط الكافل بهذا الطفل؟');" class="d-inline m-0">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-link p-0 text-danger border-0" style="font-size: 0.75rem; line-height: 1;" title="إلغاء الربط"><i class="bi bi-x-circle-fill"></i></button>
                            </form>
                          </div>
                        @empty
                          <span class="small text-muted fst-italic">لم يتم ربط كافل خاص بهذا الفرد حتى الآن.</span>
                        @endforelse
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Modal for attaching sponsor to this specific member --}}
                <div class="modal fade" id="attachMemberSponsorModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('beneficiary-family-members.attach-sponsor', $member) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                      @csrf
                      <div class="modal-header py-3 px-4 text-white border-0" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);">
                        <div class="d-flex align-items-center gap-2">
                          <i class="bi bi-person-heart fs-5"></i>
                          <h5 class="modal-title fw-bold mb-0 text-white">ربط كافل بالطفل: {{ $member->full_name }}</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body p-4 bg-white text-dark">
                        <div class="alert alert-success border-0 rounded-3 small mb-3">
                          <i class="bi bi-shield-check me-1"></i> يتم ربط الكافل بملف الطفل <strong>{{ $member->full_name }}</strong> مباشرة، ويسمّع مبلغ كفالته تلقائياً في إجمالي كفالات الأسرة.
                        </div>

                        <div class="mb-3">
                          <label class="form-label fw-bold text-dark mb-1">اختر الكافل / المتبرع <span class="text-danger">*</span></label>
                          <select name="donor_id" class="form-select rounded-3" required>
                            <option value="">-- اختر المتبرع من القائمة --</option>
                            @foreach($allDonors ?? [] as $dn)
                              <option value="{{ $dn->id }}" @selected($member->sponsors->contains($dn->id))>
                                {{ $dn->name }} ({{ $dn->code ?? ('D-'.$dn->id) }}) {{ $dn->phone ? '— '.$dn->phone : '' }}
                              </option>
                            @endforeach
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label fw-bold text-dark mb-1">مبلغ الكفالة الشهري للطفل (ج.م)</label>
                          <input type="number" step="0.01" min="0" name="monthly_amount" value="{{ $member->sponsorship_amount }}" class="form-control rounded-3" placeholder="مثال: 200 أو 1500">
                          <div class="form-text text-muted small">سيتم تحديث مبلغ كفالة الطفل ويُضاف إلى إجمالي كفالات الأسرة.</div>
                        </div>

                        <div class="mb-2">
                          <label class="form-label fw-bold text-muted small mb-1">ملاحظات الكفالة (اختياري)</label>
                          <input type="text" name="notes" class="form-control rounded-3" placeholder="مثال: كفالة يتيم، علاج أورام، شهرية...">
                        </div>
                      </div>
                      <div class="modal-footer py-2.5 px-4 bg-light border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                          <i class="bi bi-link-45deg me-1"></i> اعتماد وربط الكافل بالطفل
                        </button>
                      </div>
                    </form>
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


      {{-- ── Comprehensive Aid & Services Ledger (سجل المساعدات والكفالات والخدمات الشامل) ── --}}
      <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4 border-bottom">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
              <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="bi bi-journal-check fs-5"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold text-dark">سجل المساعدات والكفالات والخدمات المستلمة للحالة</h5>
                <small class="text-muted">توثيق زمني شامل لكل ما قُدّم للحالة من إقامات دار الضيافة، كفالات وتبرعات، ومساعدات المشاريع والحملات</small>
              </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <a href="{{ route('donations.create', ['beneficiary_id' => $beneficiary->id]) }}" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1.5">
                <i class="bi bi-plus-circle"></i>
                <span>إضافة تبرع / كفالة نقدية</span>
              </a>
              <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#recordDirectAidModal">
                <i class="bi bi-box-seam"></i>
                <span>تسجيل مساعدة / كرتونة / خدمة</span>
              </button>
              <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1.5 fw-bold d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#attachSponsorModal">
                <i class="bi bi-person-heart text-danger"></i>
                <span>ربط كفيل بالحالة</span>
              </button>
              @if(!empty($beneficiary->guest_house_id) || $activeStay)
                <a href="{{ route('guest-houses.show', $beneficiary->guest_house_id ?? $activeStay?->guest_house_id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1.5 fw-bold d-flex align-items-center gap-1.5" title="الانتقال لدار الضيافة">
                  <i class="bi bi-hospital"></i>
                  <span>دار الضيافة</span>
                </a>
              @endif
            </div>
          </div>
        </div>

        <div class="card-body p-4 bg-light bg-opacity-50">
          {{-- ── 1. Hero Highlights (Active Stay & Sponsorship Status) ── --}}
          <div class="row g-3 mb-4">
            {{-- Guest House Resident Status Card --}}
            <div class="col-lg-6">
              @if($activeStay)
                <div class="p-3 rounded-4 border border-success border-opacity-50 h-100 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <span class="p-2 rounded-circle bg-success text-white shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-hospital fs-6"></i>
                      </span>
                      <div>
                        <div class="fw-bold text-success fs-6 d-flex align-items-center gap-1.5">
                          <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 9px; height: 9px;"></span>
                          مقيمة حالياً بدار الضيافة لمرضى الأورام
                        </div>
                        <small class="text-muted fw-semibold">شاملة الإقامة والإعاشة الكاملة والانتقالات الطبية لمركز الأورام</small>
                      </div>
                    </div>
                    <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill fw-bold shadow-2xs">إقامة جارية</span>
                  </div>
                  <div class="row g-2 mt-2 pt-2 border-top border-success border-opacity-25 small">
                    <div class="col-sm-6">
                      <span class="text-muted">الدار:</span>
                      <strong class="text-dark">{{ $activeStay->guestHouse?->name ?? 'دار الضيافة' }}</strong>
                    </div>
                    <div class="col-sm-6">
                      <span class="text-muted">مكان التسكين:</span>
                      <strong class="text-dark">{{ $activeStay->bed?->wing?->name ?? 'جناح الإقامة' }} @if($activeStay->bed) (سرير: {{ $activeStay->bed->code ?? $activeStay->bed->bed_number }}) @endif</strong>
                    </div>
                    <div class="col-sm-6">
                      <span class="text-muted">تاريخ الدخول:</span>
                      <strong class="text-dark font-monospace">{{ $activeStay->arrival_date ? $activeStay->arrival_date->format('Y-m-d') : ($activeStay->admitted_at ? $activeStay->admitted_at->format('Y-m-d') : 'اليوم') }}</strong>
                    </div>
                    <div class="col-sm-6">
                      <span class="text-muted">مدة الإقامة:</span>
                      <strong class="text-success fw-bold">{{ $activeStay->actual_days }} يوم (مستمرة)</strong>
                    </div>
                  </div>
                </div>
              @elseif($guestHouseStays->isNotEmpty())
                <div class="p-3 rounded-4 border border-secondary border-opacity-25 h-100 bg-white shadow-sm">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="p-2 rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                      <i class="bi bi-hospital fs-6"></i>
                    </span>
                    <div>
                      <div class="fw-bold text-dark fs-6">سجل استضافات دار الضيافة</div>
                      <small class="text-muted">إجمالي فترات الاستضافة السابقة: <strong>{{ $guestHouseStays->count() }} فترات</strong> (غير مقيمة بالدار حالياً)</small>
                    </div>
                  </div>
                  <div class="small text-muted mt-2 pt-2 border-top">
                    آخر إقامة: {{ $guestHouseStays->first()->guestHouse?->name ?? 'دار الضيافة' }} — غادرت بتاريخ {{ optional($guestHouseStays->first()->departed_at)->format('Y-m-d') ?? '—' }}
                  </div>
                </div>
              @else
                <div class="p-3 rounded-4 border border-dashed h-100 bg-white d-flex align-items-center gap-3">
                  <span class="p-2 rounded-circle bg-light text-muted d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-hospital fs-6"></i>
                  </span>
                  <div>
                    <div class="fw-bold text-muted fs-6">دار الضيافة لمرضى الأورام</div>
                    <small class="text-muted">لا توجد إقامات مسجلة للحالة بدار الضيافة حالياً.</small>
                  </div>
                </div>
              @endif
            </div>

            {{-- Sponsorship & Project Status Card --}}
            <div class="col-lg-6">
              <div class="p-3 rounded-4 border border-primary border-opacity-25 h-100 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <span class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                      <i class="bi bi-diagram-3 fs-6"></i>
                    </span>
                    <div>
                      <div class="fw-bold text-primary fs-6">
                        مشروع الحالة والكفالات
                      </div>
                      <small class="text-muted">
                        المشروع: <strong>{{ $beneficiary->project?->name ?? 'مشروع عام' }}</strong> — نوع المساعدة: <strong>{{ $typeText }}</strong>
                      </small>
                    </div>
                  </div>
                  @if($allBeneficiarySponsors->isNotEmpty() || $beneficiary->total_family_sponsorship_amount > 0)
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill fw-bold">
                      <i class="bi bi-patch-check-fill me-1"></i>مكفولة
                    </span>
                  @else
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5" data-bs-toggle="modal" data-bs-target="#attachSponsorModal">
                      <i class="bi bi-plus me-1"></i>ربط كفيل
                    </button>
                  @endif
                </div>

                {{-- Financial & Child Breakdown --}}
                <div class="d-flex align-items-center justify-content-between bg-light rounded-3 p-2.5 my-2 border">
                  <div>
                    <span class="text-muted small">إجمالي الكفالة الشهرية للأسرة:</span>
                    <strong class="text-success fs-6 font-monospace d-block">{{ number_format($beneficiary->total_family_sponsorship_amount, 2) }} ج.م</strong>
                  </div>
                  @if($beneficiary->sponsored_children_count_calculated > 0)
                    <div class="text-end">
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold">
                        <i class="bi bi-people-fill me-1"></i>{{ $beneficiary->sponsored_children_count_calculated }} أفراد/أطفال مكفولين
                      </span>
                    </div>
                  @endif
                </div>

                <div class="mt-2 pt-2 border-top small">
                  @if($allBeneficiarySponsors->isNotEmpty())
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="text-muted">الكفلاء المعتمدون:</span>
                      @foreach($allBeneficiarySponsors as $sp)
                        <a href="{{ route('donors.show', $sp) }}" class="badge bg-success-subtle text-success text-decoration-none px-2.5 py-1.5 rounded-pill border border-success-subtle">
                          <i class="bi bi-person-heart me-1"></i>{{ $sp->name }} <span class="font-monospace opacity-75">({{ $sp->display_code }})</span>
                        </a>
                      @endforeach
                    </div>
                  @else
                    <div class="text-muted d-flex justify-content-between align-items-center">
                      <span>الحالة مسجلة تحت مشروع <strong>{{ $beneficiary->project?->name ?? 'العام' }}</strong> وبانتظار ربط كفيل مباشر لاستحقاق الكفالة الدورية.</span>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          {{-- ── 2. Metric Pills Summary ── --}}
          @php
            $cashDonationsCount = $donations->where('type', 'cash')->count();
            $cashTotalSum = $donations->where('type', 'cash')->sum('amount');
            $inKindDonationsCount = $donations->where('type', 'in_kind')->count();
            $inKindTotalSum = $donations->where('type', 'in_kind')->sum('estimated_value');
            $totalStaysCount = $guestHouseStays->count();
            $totalMealsCount = $mealServings->sum('total_meals_count') ?: $mealServings->count();
            $totalRamadanCount = $ramadanBags->count();
            $grandTotalOperations = $donations->count() + $totalStaysCount + $mealServings->count() + $totalRamadanCount;
          @endphp

          <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
              <div class="p-2.5 rounded-3 bg-white border text-center shadow-2xs">
                <div class="text-muted small">إجمالي التبرعات النقدية</div>
                <div class="fw-bold fs-6 text-success font-monospace">{{ number_format($cashTotalSum, 2) }} <small class="text-muted">ج.م</small></div>
                <small class="text-muted" style="font-size: 11px;">{{ $cashDonationsCount }} إيصالات مسددة</small>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="p-2.5 rounded-3 bg-white border text-center shadow-2xs">
                <div class="text-muted small">المساعدات العينية ورمضان</div>
                <div class="fw-bold fs-6 text-primary font-monospace">{{ $inKindDonationsCount + $totalRamadanCount }} <small class="text-muted">مساعدات</small></div>
                <small class="text-muted" style="font-size: 11px;">كراتين، سلات، أدوية، كسوة</small>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="p-2.5 rounded-3 bg-white border text-center shadow-2xs">
                <div class="text-muted small">استضافات دار الضيافة</div>
                <div class="fw-bold fs-6 text-info font-monospace">{{ $totalStaysCount }} <small class="text-muted">إقامات</small></div>
                <small class="text-muted" style="font-size: 11px;">@if($activeStay)<span class="text-success fw-bold">مقيمة حالياً بالدار</span>@else إقامات سابقة @endif</small>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="p-2.5 rounded-3 bg-white border text-center shadow-2xs">
                <div class="text-muted small">وجبات الإعاشة المقدمة</div>
                <div class="fw-bold fs-6 text-warning font-monospace">{{ $totalMealsCount }} <small class="text-muted">وجبة</small></div>
                <small class="text-muted" style="font-size: 11px;">إعاشة دار الضيافة</small>
              </div>
            </div>
          </div>

          {{-- ── 3. Tabs / Filter Bar ── --}}
          <ul class="nav nav-pills mb-3 gap-2" id="aidLedgerTabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active rounded-pill px-3 py-1.5 fw-bold text-dark bg-white border shadow-2xs" id="tab-all-btn" type="button" onclick="filterAidLedger('all', this)">
                <i class="bi bi-collection me-1"></i> السجل الشامل ({{ $grandTotalOperations }})
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold text-dark bg-white border" id="tab-gh-btn" type="button" onclick="filterAidLedger('gh', this)">
                <i class="bi bi-hospital me-1 text-info"></i> دار الضيافة ({{ $totalStaysCount + $mealServings->count() }})
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold text-dark bg-white border" id="tab-cash-btn" type="button" onclick="filterAidLedger('cash', this)">
                <i class="bi bi-cash-coin me-1 text-success"></i> التبرعات والكفالات المالية ({{ $cashDonationsCount }})
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold text-dark bg-white border" id="tab-inkind-btn" type="button" onclick="filterAidLedger('inkind', this)">
                <i class="bi bi-box-seam me-1 text-primary"></i> المساعدات العينية والمشاريع ({{ $inKindDonationsCount + $totalRamadanCount }})
              </button>
            </li>
          </ul>

          {{-- ── 4. Unified Ledger Table ── --}}
          @if($grandTotalOperations > 0)
            <div class="table-responsive bg-white rounded-3 border shadow-2xs">
              <table class="table table-hover align-middle mb-0" id="aidLedgerTable">
                <thead class="table-light">
                  <tr class="small text-muted">
                    <th style="width: 150px;">النوع والتصنيف</th>
                    <th>البيان والتفاصيل</th>
                    <th>المصدر / الجهة / المتبرع</th>
                    <th>القيمة / المقابل</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th class="text-center" style="width: 120px;">الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- 1. Active Stay (Always on top if resident) --}}
                  @if($activeStay)
                    <tr class="aid-ledger-row table-success" data-category="gh">
                      <td>
                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill shadow-2xs">
                          <i class="bi bi-hospital me-1"></i> إقامة دار ضيافة
                        </span>
                      </td>
                      <td>
                        <strong class="text-dark">إقامة نشطة حالياً بدار الضيافة لمرضى الأورام</strong>
                        <div class="small text-muted">
                          {{ $activeStay->bed?->wing?->name ?? 'جناح الإقامة' }} @if($activeStay->bed) — سرير: {{ $activeStay->bed->code ?? $activeStay->bed->bed_number }} @endif
                          — شاملة الإعاشة والرعاية
                        </div>
                      </td>
                      <td>
                        <span class="fw-semibold text-dark">{{ $activeStay->guestHouse?->name ?? 'دار الضيافة' }}</span>
                      </td>
                      <td>
                        <span class="badge bg-success bg-opacity-15 text-success fw-bold">خدمة استضافة مجانية</span>
                      </td>
                      <td>
                        <span class="font-monospace text-dark">{{ $activeStay->arrival_date ? $activeStay->arrival_date->format('Y-m-d') : ($activeStay->admitted_at ? $activeStay->admitted_at->format('Y-m-d') : 'اليوم') }}</span>
                        <div class="small text-success fw-bold">منذ {{ $activeStay->actual_days }} يوم</div>
                      </td>
                      <td>
                        <span class="badge bg-success text-white px-2 py-1 rounded-pill fw-bold">
                          <i class="bi bi-check-circle-fill me-1"></i> مقيمة حالياً بالدار
                        </span>
                      </td>
                      <td class="text-center">
                        <a href="{{ route('guest-houses.show', $activeStay->guest_house_id) }}" class="btn btn-sm btn-outline-success px-2 py-1" title="فتح دار الضيافة">
                          <i class="bi bi-box-arrow-up-left"></i> فتح الدار
                        </a>
                      </td>
                    </tr>
                  @endif

                  {{-- 2. Past Guest House Stays --}}
                  @foreach($guestHouseStays as $stay)
                    @if($activeStay && $stay->id === $activeStay->id) @continue @endif
                    <tr class="aid-ledger-row" data-category="gh">
                      <td>
                        <span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill border border-info-subtle">
                          <i class="bi bi-hospital me-1"></i> إقامة سابقة
                        </span>
                      </td>
                      <td>
                        <strong class="text-dark">استضافة بدار الضيافة لمرضى الأورام</strong>
                        <div class="small text-muted">
                          {{ $stay->bed?->wing?->name ?? 'الجناح' }} @if($stay->bed) — سرير {{ $stay->bed->bed_number }} @endif
                          — المدة: {{ $stay->actual_days }} يوم
                        </div>
                      </td>
                      <td>
                        <span class="text-dark">{{ $stay->guestHouse?->name ?? 'دار الضيافة' }}</span>
                      </td>
                      <td>
                        <span class="badge bg-light text-muted border">استضافة مجانية</span>
                      </td>
                      <td>
                        <span class="font-monospace text-muted">{{ $stay->arrival_date ? $stay->arrival_date->format('Y-m-d') : ($stay->admitted_at ? $stay->admitted_at->format('Y-m-d') : '—') }}</span>
                        @if($stay->departed_at)
                          <div class="small text-muted">المغادرة: {{ $stay->departed_at->format('Y-m-d') }}</div>
                        @endif
                      </td>
                      <td>
                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">مكتملة</span>
                      </td>
                      <td class="text-center">
                        <a href="{{ route('guest-houses.show', $stay->guest_house_id) }}" class="btn btn-sm btn-outline-secondary px-2 py-1" title="عرض الدار">
                          <i class="bi bi-eye"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach

                  {{-- 3. Donations & Sponsorships --}}
                  @foreach($donations as $d)
                    <tr class="aid-ledger-row" data-category="{{ $d->type === 'cash' ? 'cash' : 'inkind' }}">
                      <td>
                        @if($d->type === 'cash')
                          <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill border border-success-subtle">
                            <i class="bi bi-cash-coin me-1"></i> كفالة / مساعدة نقدية
                          </span>
                        @else
                          <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill border border-primary-subtle">
                            <i class="bi bi-box-seam me-1"></i> مساعدة عينية
                          </span>
                        @endif
                      </td>
                      <td>
                        <div class="fw-bold text-dark">{{ $d->purpose ?: ($d->purpose_label ?: 'مساعدة للحالة') }}</div>
                        <div class="small text-muted font-monospace">إيصال: {{ $d->receipt_number ?: ('#' . $d->id) }}</div>
                        @if(!empty($d->paid_months) && is_array($d->paid_months))
                          <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($d->paid_months as $pm)
                              <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 10px;">{{ $pm }}</span>
                            @endforeach
                          </div>
                        @endif
                      </td>
                      <td>
                        @if($d->donor)
                          <a href="{{ route('donors.show', $d->donor_id) }}" class="text-decoration-none fw-bold text-dark">
                            <i class="bi bi-person-heart text-success me-1"></i>{{ $d->donor->name }}
                          </a>
                          <div class="small text-muted font-monospace">{{ $d->donor->display_code }}</div>
                        @else
                          <span class="text-muted"><i class="bi bi-person me-1"></i>فاعل خير / عام</span>
                        @endif
                        @if($d->project)
                          <div class="small text-primary"><i class="bi bi-rocket me-1"></i>{{ $d->project->name }}</div>
                        @endif
                      </td>
                      <td>
                        @if($d->type === 'cash')
                          <span class="fw-bold text-success font-monospace fs-6">{{ number_format($d->amount, 2) }}</span>
                          <span class="text-muted small">{{ $d->currency }}</span>
                        @else
                          <span class="fw-bold text-primary font-monospace">{{ $d->estimated_value > 0 ? number_format($d->estimated_value, 2) . ' ج.م' : 'مساعدة عينية' }}</span>
                        @endif
                      </td>
                      <td>
                        <span class="font-monospace text-dark">{{ $d->received_at?->format('Y-m-d') ?? $d->created_at->format('Y-m-d') }}</span>
                      </td>
                      <td>
                        <span class="badge bg-success bg-opacity-15 text-success px-2 py-1 rounded-pill fw-bold">
                          <i class="bi bi-check2 me-1"></i> مسدد ومستلم
                        </span>
                      </td>
                      <td class="text-center">
                        <div class="d-inline-flex gap-1">
                          <a href="{{ route('donations.show', $d) }}" class="btn btn-sm btn-outline-primary px-2 py-1" title="عرض التفاصيل">
                            <i class="bi bi-eye"></i>
                          </a>
                          <a href="{{ route('donations.show', $d) }}?print=1" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1" title="طباعة الإيصال">
                            <i class="bi bi-printer"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  @endforeach

                  {{-- 4. Ramadan Bags & Food Parcels --}}
                  @foreach($ramadanBags as $bag)
                    <tr class="aid-ledger-row" data-category="inkind">
                      <td>
                        <span class="badge bg-warning-subtle text-dark px-2 py-1 rounded-pill border border-warning-subtle">
                          <i class="bi bi-basket me-1 text-warning"></i> كرتونة مواد غذائية
                        </span>
                      </td>
                      <td>
                        <strong class="text-dark">توزيع كرتونة رمضان / سلة غذائية</strong>
                        @if(!empty($bag->bag_contents))
                          <div class="small text-muted">{{ $bag->bag_contents }}</div>
                        @endif
                      </td>
                      <td>
                        <span class="text-dark">مشروع زاد / حملة رمضان</span>
                      </td>
                      <td>
                        <span class="badge bg-light text-muted border">كرتونة تموينية</span>
                      </td>
                      <td>
                        <span class="font-monospace text-muted">{{ $bag->created_at->format('Y-m-d') }}</span>
                      </td>
                      <td>
                        <span class="badge bg-success bg-opacity-15 text-success px-2 py-1 rounded-pill">تم التسليم</span>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-light text-muted">—</span>
                      </td>
                    </tr>
                  @endforeach

                  {{-- 5. Guest House Meal Servings (Grouped or listed) --}}
                  @foreach($mealServings->take(10) as $serving)
                    <tr class="aid-ledger-row" data-category="gh">
                      <td>
                        <span class="badge bg-light text-dark px-2 py-1 rounded-pill border">
                          <i class="bi bi-egg-fried me-1 text-warning"></i> وجبة إعاشة
                        </span>
                      </td>
                      <td>
                        <span class="text-dark">وجبة إعاشة بدار الضيافة ({{ $serving->total_meals_count ?? 1 }} وجبة)</span>
                        @if($serving->notes)
                          <div class="small text-muted">{{ $serving->notes }}</div>
                        @endif
                      </td>
                      <td>
                        <span class="text-muted">{{ ($serving->guestHouseMeal ?? $serving->meal)?->guestHouse?->name ?? 'دار الضيافة' }}</span>
                      </td>
                      <td>
                        <span class="text-muted small">إعاشة مجانية</span>
                      </td>
                      <td>
                        <span class="font-monospace text-muted">{{ $serving->received_at ? \Carbon\Carbon::parse($serving->received_at)->format('Y-m-d') : $serving->created_at->format('Y-m-d') }}</span>
                      </td>
                      <td>
                        <span class="badge bg-light text-success border border-success-subtle">تم التقديم</span>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-light text-muted">—</span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="p-5 text-center bg-white rounded-3 border">
              <i class="bi bi-journal-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
              <h6 class="fw-bold text-dark mb-1">لا توجد مساعدات أو كفالات مسجلة لهذه الحالة حتى الآن</h6>
              <p class="text-muted small mb-3">يمكنك تسجيل استلام مساعدة عينية أو نقدية أو ربط كفيل بالحالة مباشرة عبر الأزرار أدناه.</p>
              <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ route('donations.create', ['beneficiary_id' => $beneficiary->id]) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                  <i class="bi bi-plus-lg me-1"></i> إضافة تبرع / كفالة نقدية
                </a>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#recordDirectAidModal">
                  <i class="bi bi-box-seam me-1"></i> تسجيل مساعدة عينية أو كرتونة
                </button>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- ── 6-Month Periodic Reassessment & Psychological Support Hub ── --}}
      @php
        $benPsychPublicUrl = \App\Http\Controllers\PsychologicalSupportController::getPublicUrl('beneficiary', $beneficiary->id);
        $benReassessPublicUrl = \App\Http\Controllers\BeneficiaryReassessmentController::getPublicUrl($beneficiary->id);
        $latestReassessment = isset($reassessments) ? $reassessments->first() : null;
      @endphp

      {{-- 1. 6-Month Family & Patient Reassessment Card (استمارة إعادة بحث وتحديث بيانات الأسرة كل 6 أشهر) --}}
      <div class="card mb-4 border-0 shadow-sm" id="reassessmentSection" style="border-radius: 16px; overflow: hidden; border-top: 4px solid #0d9488 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2.5">
            <div class="p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: linear-gradient(135deg, #0d9488, #0f766e);">
              <i class="bi bi-arrow-repeat fs-5"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold text-dark">سجل إعادة البحث وتحديث بيانات الأسرة (كل 6 أشهر — بعثاء الأمل)</h5>
              <small class="text-muted">تحديث دوري للوضع الاجتماعي والطبي والاقتصادي للأسرة مع الحفاظ الكامل على البيانات الأساسية</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($latestReassessment && $latestReassessment->next_reassessment_due_at)
              <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1.5 rounded-pill font-monospace">
                <i class="bi bi-calendar-check me-1"></i> التحديث القادم: {{ $latestReassessment->next_reassessment_due_at->format('Y-m-d') }}
              </span>
            @endif
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-bold" onclick="navigator.clipboard.writeText('{{ $benReassessPublicUrl }}'); alert('تم نسخ رابط استمارة إعادة البحث لإرساله للباحث/المتطوع:\n{{ $benReassessPublicUrl }}');">
              <i class="bi bi-share-fill me-1"></i> مشاركة رابط الاستمارة
            </button>
            <button type="button" class="btn btn-sm text-white rounded-pill px-3 py-1.5 fw-bold shadow-sm" style="background:#0d9488;" data-bs-toggle="modal" data-bs-target="#reassessmentModal">
              <i class="bi bi-plus-circle me-1"></i> تسجيل استمارة إعادة بحث جديدة
            </button>
          </div>
        </div>
        <div class="card-body p-4 bg-light bg-opacity-50">
          @if(isset($reassessments) && $reassessments->isNotEmpty())
            <div class="row g-3">
              @foreach($reassessments as $reassess)
                <div class="col-12">
                  <div class="card border-0 shadow-2xs rounded-3 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom pb-2 mb-3">
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge text-white rounded-pill px-3 py-1 font-monospace" style="background:#0d9488;">
                          <i class="bi bi-calendar-event me-1"></i> تاريخ التحديث: {{ $reassess->reassessed_at?->format('Y-m-d') ?? $reassess->created_at?->format('Y-m-d') }}
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold">
                          التوصية: {{ $reassess->researcher_recommendation ?: 'غير محدد' }}
                        </span>
                        @if($reassess->condition_progress)
                          <span class="badge bg-primary-subtle text-primary px-2.5 py-1 rounded-pill">
                            تطور الحالة: {{ $reassess->condition_progress }}
                          </span>
                        @endif
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <small class="text-muted"><i class="bi bi-person-check me-1"></i>الباحث: <strong>{{ $reassess->researcher_name ?: ($reassess->creator?->name ?? '—') }}</strong></small>
                        <form action="{{ route('beneficiaries.reassessments.destroy', $reassess) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا السجل؟');" class="d-inline m-0">
                          @csrf @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                        </form>
                      </div>
                    </div>
                    <div class="row g-2 small">
                      <div class="col-md-3"><strong>المريض:</strong> {{ $reassess->patient_name ?: '—' }}</div>
                      <div class="col-md-3"><strong>التشخيص الحالي:</strong> {{ $reassess->current_diagnosis ?: '—' }}</div>
                      <div class="col-md-3"><strong>المرحلة / العلاج:</strong> {{ $reassess->diagnosis_stage ?: '—' }} ({{ $reassess->current_treatment_type ?: '—' }})</div>
                      <div class="col-md-3"><strong>المستشفى:</strong> {{ $reassess->current_hospital ?: '—' }}</div>
                      <div class="col-md-3"><strong>أفراد الأسرة الحاليين:</strong> {{ $reassess->current_family_count ?? '—' }}</div>
                      <div class="col-md-3"><strong>دخل الأسرة الحالي:</strong> {{ $reassess->current_family_income !== null ? number_format((float)$reassess->current_family_income, 2).' ج.م' : '—' }}</div>
                      <div class="col-md-3"><strong>تكلفة العلاج الشهرية:</strong> {{ $reassess->monthly_treatment_cost !== null ? number_format((float)$reassess->monthly_treatment_cost, 2).' ج.م' : '—' }}</div>
                      <div class="col-md-3"><strong>المساعدة المطلوبة:</strong> <span class="text-success fw-bold">{{ $reassess->requested_monthly_aid !== null ? number_format((float)$reassess->requested_monthly_aid, 2).' ج.م' : '—' }}</span></div>
                      @if(!empty($reassess->most_needed_support))
                        <div class="col-md-6"><strong>الدعم الأكثر احتياجاً:</strong> {{ is_array($reassess->most_needed_support) ? implode('، ', $reassess->most_needed_support) : $reassess->most_needed_support }}</div>
                      @endif
                      @if($reassess->family_status_changes)
                        <div class="col-md-6 text-danger"><strong>مستجدات الأسرة:</strong> {{ $reassess->family_status_changes }}</div>
                      @endif
                      @if($reassess->yearly_progress_summary)
                        <div class="col-12 mt-1 pt-1 border-top"><strong>ملخص تطور الحالة:</strong> {{ $reassess->yearly_progress_summary }}</div>
                      @endif
                      @if($reassess->recommendation_reasons)
                        <div class="col-12"><strong>مبررات التوصية:</strong> {{ $reassess->recommendation_reasons }}</div>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-4">
              <i class="bi bi-clipboard2-pulse fs-1 text-muted opacity-50 d-block mb-2"></i>
              <h6 class="fw-bold text-dark mb-1">لا توجد استمارات إعادة بحث مسجلة بعد</h6>
              <p class="text-muted small mb-3">يتم تحديث بيانات الأسرة والمريض كل 6 أشهر مع الاحتفاظ الكامل بالبيانات الأساسية للملف.</p>
              <button type="button" class="btn btn-sm text-white rounded-pill px-4 fw-bold" style="background:#0d9488;" data-bs-toggle="modal" data-bs-target="#reassessmentModal">
                <i class="bi bi-plus-circle me-1"></i> تسجيل أول استمارة إعادة بحث الآن
              </button>
            </div>
          @endif
        </div>
      </div>

      {{-- 2. Psychological Support Section on Beneficiary Profile (سجل الدعم النفسي الشهري ومشاركة الرابط للمتطوع) --}}
      <div class="card mb-4 border-0 shadow-sm" id="psychologicalSupportSection" style="border-radius: 16px; overflow: hidden; border-top: 4px solid #ec4899 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2.5">
            <div class="p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: linear-gradient(135deg, #ec4899, #f43f5e);">
              <i class="bi bi-heart-pulse-fill fs-5"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold text-dark">سجل تقارير الدعم النفسي الشهري</h5>
              <small class="text-muted">متابعة الحالة النفسية والمعنوية شهرياً وإمكانية مشاركة الرابط للمتطوع ليملاها وتسمّع تلقائياً في السيستم</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-bold" onclick="navigator.clipboard.writeText('{{ $benPsychPublicUrl }}'); alert('تم نسخ رابط استمارة الدعم النفسي للمتطوع بنجاح:\n{{ $benPsychPublicUrl }}');">
              <i class="bi bi-share-fill me-1"></i> نسخ رابط الاستمارة للمتطوع
            </button>
            <a href="https://wa.me/?text={{ urlencode('استمارة الدعم النفسي الشهري للحالة (' . $beneficiary->full_name . ' - كود ' . $beneficiary->code . '): ' . $benPsychPublicUrl) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold">
              <i class="bi bi-whatsapp me-1"></i> إرسال واتساب للمتطوع
            </a>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1.5 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#beneficiaryPsychSupportModal">
              <i class="bi bi-plus-circle me-1"></i> توثيق تقرير دعم نفسي
            </button>
          </div>
        </div>
        <div class="card-body p-4 bg-light bg-opacity-50">
          @if(isset($psychReports) && $psychReports->isNotEmpty())
            <div class="row g-3">
              @foreach($psychReports as $pReport)
                <div class="col-12 col-md-6">
                  <div class="card border-0 shadow-2xs rounded-3 p-3 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <span class="badge text-white rounded-pill px-2.5 py-1 font-monospace" style="background-color: #ec4899;">
                          شهر {{ $pReport->report_month }}
                        </span>
                        <div class="fw-bold text-dark mt-1">
                          <i class="bi bi-person-heart text-danger me-1"></i>المتطوع: {{ $pReport->volunteer_name ?: 'غير محدد' }}
                        </div>
                      </div>
                      <div class="text-end">
                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill small">{{ $pReport->psychological_state }}</span>
                        <div class="text-warning small font-monospace mt-1">{{ str_repeat('★', (int)$pReport->rating) }}</div>
                      </div>
                    </div>
                    @if($pReport->medical_condition || $pReport->treatment_stage)
                      <div class="small text-muted border-top pt-2">
                        {{ $pReport->medical_condition }} {{ $pReport->treatment_stage ? '— ' . $pReport->treatment_stage : '' }}
                      </div>
                    @endif
                    @if($pReport->supporter_opinion)
                      <div class="small text-dark mt-1"><strong>التوصية:</strong> {{ $pReport->supporter_opinion }}</div>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-3 text-muted small">
              لا توجد تقارير دعم نفسي مسجلة بعد. يمكنك توثيق التقرير الآن أو نسخ الرابط وإرساله للمتطوع المتابع للحالة.
            </div>
          @endif
        </div>
      </div>

      {{-- Attachments & Official Documents Section --}}
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          @php
            $ghUploadedCount = collect($guestHouseDocs ?? [])->where('is_uploaded', true)->count();
            $mobileDocsCount = ($linkedCaseApplications?->filter(fn($app) => !empty($app->id_image_path) || !empty($app->medical_report_path))->count() ?? 0);
            $bookingDocsCount = ($linkedBookings?->filter(fn($bk) => !empty($bk->patient_id_path) || !empty($bk->companion_id_path) || !empty($bk->medical_transfer_path) || !empty($bk->followup_card_path) || !empty($bk->medical_report_path))->count() ?? 0);
            $directAttsCount = $beneficiary->attachments->count();
            $totalAttsCount = $ghUploadedCount + $mobileDocsCount + $bookingDocsCount + $directAttsCount;
          @endphp

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title mb-0">
              <i class="bi bi-paperclip text-primary fs-5"></i>
              <h5 class="mb-0 fw-bold">الأوراق والمرفقات الرسمية</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
              @if(!empty($isGuestHouseCase))
                <span class="badge bg-info-subtle text-info fw-bold px-3 py-2 rounded-pill font-monospace">
                  <i class="bi bi-hospital me-1"></i> دار الضيافة: {{ $ghUploadedCount }}/6
                </span>
              @endif
              <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill font-monospace">
                إجمالي الملفات: {{ $totalAttsCount }}
              </span>
            </div>
          </div>

          {{-- Upload Form --}}
          <form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded-3 border" id="quickUploadForm">
            @csrf
            <input type="hidden" name="entity_type" value="{{ \App\Models\Beneficiary::class }}">
            <input type="hidden" name="entity_id" value="{{ $beneficiary->id }}">
            <div class="row g-2 align-items-center">
              @if(!empty($isGuestHouseCase))
                <div class="col-md-3">
                  <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-tag me-1"></i> نوع المستند</label>
                  <select name="target_doc_type" id="quickDocTypeSelect" class="form-select form-select-sm" onchange="toggleQuickUploadTarget(this.value)">
                    <option value="general">مرفق عام للملف</option>
                    <option value="patient_id_front">بطاقة المريض (الوجه)</option>
                    <option value="patient_id_back">بطاقة المريض (الظهر)</option>
                    <option value="companion_id_front">بطاقة المرافق (الوجه)</option>
                    <option value="companion_id_back">بطاقة المرافق (الظهر)</option>
                    <option value="followup_card">كارت المتابعة (جرعات الكيماوي)</option>
                    <option value="referral_letter">خطاب التحويل / التقرير الطبي</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-cloud-arrow-up me-1"></i> رفع مستند أو صورة جديدة</label>
                  <input type="file" name="file" class="form-control form-control-sm" required accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.zip" capture="environment">
                  <div class="form-text small text-muted">يدعم الصور وملفات PDF حتى 15 ميجابايت (يمكنك تصوير البطاقة أو رفعها).</div>
                </div>
              @else
                <div class="col-md-9">
                  <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-cloud-arrow-up me-1"></i> رفع مستند أو صورة جديدة للملف</label>
                  <input type="file" name="file" class="form-control form-control-sm" required accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.zip">
                  <div class="form-text small text-muted">يدعم الصور (JPG, PNG, WEBP) وملفات PDF وWord حتى 10 ميجابايت.</div>
                </div>
              @endif
              <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-bold shadow-sm">
                  <i class="bi bi-upload me-1"></i> رفع المستند
                </button>
              </div>
            </div>
          </form>

          {{-- 1. Official Guest House Documents & Cards (خاص بدار الضيافة) --}}
          @if(!empty($isGuestHouseCase))
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h6 class="fw-bold text-dark mb-0">
                  <i class="bi bi-hospital text-info me-1"></i> أوراق وبطاقات دار الضيافة الرسمية (المريض والمرافق)
                </h6>
                <div class="d-flex gap-2 align-items-center">
                  @if($ghUploadedCount === 6)
                    <span class="badge bg-success-subtle text-success small"><i class="bi bi-check-circle me-1"></i> كامل الأوراق الرسمية متوفرة</span>
                  @else
                    <span class="badge bg-warning-subtle text-warning small"><i class="bi bi-exclamation-triangle me-1"></i> متوفر {{ $ghUploadedCount }} من 6 مستندات</span>
                  @endif
                  <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadGuestDocModal">
                    <i class="bi bi-cloud-arrow-up me-1"></i> رفع / تحديث مستند
                  </button>
                </div>
              </div>

              <div class="row g-3">
                @foreach(($guestHouseDocs ?? []) as $docKey => $doc)
                  <div class="col-md-6 col-lg-4">
                    @if($doc['is_uploaded'])
                      <div class="border rounded-3 p-3 bg-white h-100 shadow-sm d-flex flex-column justify-content-between border-start border-4 border-primary">
                        <div class="d-flex align-items-start gap-3 mb-2">
                          @if($doc['is_image'])
                            <a href="{{ $doc['url'] }}" target="_blank" class="d-block text-decoration-none flex-shrink-0">
                              <img src="{{ $doc['url'] }}" alt="{{ $doc['title'] }}" class="rounded border shadow-xs" style="width: 70px; height: 70px; object-fit: cover;">
                            </a>
                          @else
                            <a href="{{ $doc['url'] }}" target="_blank" class="rounded border bg-light d-flex align-items-center justify-content-center text-danger flex-shrink-0 text-decoration-none" style="width: 70px; height: 70px; font-size: 2rem;">
                              <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                          @endif
                          <div class="flex-grow-1 overflow-hidden">
                            <span class="badge {{ $doc['badge_class'] ?? 'bg-primary-subtle text-primary' }} small mb-1">{{ $doc['badge'] ?? 'مستند رسمي' }}</span>
                            <div class="fw-bold text-truncate small" title="{{ $doc['title'] }}">{{ $doc['title'] }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $doc['subtitle'] }}</div>
                          </div>
                        </div>
                        <div class="d-flex gap-2 mt-2 pt-2 border-top align-items-center">
                          <a href="{{ $doc['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-eye me-1"></i> عرض
                          </a>
                          <a href="{{ $doc['url'] }}" download class="btn btn-sm btn-light border" title="تحميل">
                            <i class="bi bi-download"></i>
                          </a>
                          <button type="button" class="btn btn-sm btn-light border text-warning" title="استبدال" onclick="openDocUploadModal('{{ $doc['key'] }}', '{{ $doc['title'] }}')">
                            <i class="bi bi-arrow-repeat"></i>
                          </button>
                          <form method="POST" action="{{ route('beneficiaries.delete-patient-doc', ['beneficiary' => $beneficiary->id, 'docType' => $doc['key']]) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستند؟');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="حذف">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </div>
                    @else
                      <div class="border border-dashed rounded-3 p-3 bg-light-subtle h-100 d-flex flex-column justify-content-between text-center">
                        <div>
                          <div class="rounded-circle bg-white border d-inline-flex align-items-center justify-content-center mb-2 text-muted shadow-xs" style="width: 44px; height: 44px;">
                            <i class="bi {{ $doc['icon'] }} fs-5"></i>
                          </div>
                          <div class="fw-bold text-dark small mb-1">{{ $doc['title'] }}</div>
                          <div class="text-muted small mb-2" style="font-size: 0.72rem;">{{ $doc['subtitle'] }}</div>
                          <span class="badge bg-secondary-subtle text-secondary small">غير مرفوع حتى الآن</span>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                          <button type="button" class="btn btn-sm btn-outline-primary w-100 fw-bold" onclick="openDocUploadModal('{{ $doc['key'] }}', '{{ $doc['title'] }}')">
                            <i class="bi bi-cloud-arrow-up me-1"></i> رفع هذا المستند
                          </button>
                        </div>
                      </div>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          {{-- 2. Documents from Linked Web Room Bookings if any --}}
          @if(isset($linkedBookings) && $linkedBookings->isNotEmpty())
            @php
              $bookingWithDocs = $linkedBookings->filter(fn($bk) => $bk->patient_id_path || $bk->companion_id_path || $bk->medical_transfer_path || $bk->followup_card_path || $bk->medical_report_path);
            @endphp
            @if($bookingWithDocs->isNotEmpty())
              <div class="mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                  <i class="bi bi-calendar2-check text-info me-1"></i> المستندات المرفوعة من الحجز الإلكتروني للدار
                </h6>
                <div class="row g-3">
                  @foreach($bookingWithDocs as $bk)
                    @foreach([
                      'patient_id_path' => ['بطاقة المريض (الحجز)', 'bg-primary-subtle text-primary'],
                      'companion_id_path' => ['بطاقة المرافق (الحجز)', 'bg-secondary-subtle text-secondary'],
                      'followup_card_path' => ['كارت المتابعة (الحجز)', 'bg-info-subtle text-info'],
                      'medical_transfer_path' => ['خطاب التحويل (الحجز)', 'bg-warning-subtle text-warning'],
                      'medical_report_path' => ['التقرير الطبي (الحجز)', 'bg-danger-subtle text-danger'],
                    ] as $bkCol => [$bkTitle, $bkBadge])
                      @if(!empty($bk->{$bkCol}))
                        @php
                          $bkUrl = app(\App\Services\ImageUploadService::class)->url($bk->{$bkCol}) ?? asset('storage/' . ltrim($bk->{$bkCol}, '/'));
                          $isBkImg = in_array(strtolower(pathinfo($bk->{$bkCol}, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp'], true);
                        @endphp
                        <div class="col-md-6 col-lg-4">
                          <div class="border rounded-3 p-3 bg-white h-100 shadow-sm d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center gap-3 mb-2">
                              @if($isBkImg)
                                <a href="{{ $bkUrl }}" target="_blank" class="d-block text-decoration-none">
                                  <img src="{{ $bkUrl }}" alt="{{ $bkTitle }}" class="rounded border shadow-xs" style="width: 65px; height: 65px; object-fit: cover;">
                                </a>
                              @else
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center text-primary" style="width: 65px; height: 65px; font-size: 1.8rem;">
                                  <i class="bi bi-file-earmark-medical"></i>
                                </div>
                              @endif
                              <div class="flex-grow-1 overflow-hidden">
                                <span class="badge {{ $bkBadge }} small mb-1">{{ $bkTitle }}</span>
                                <div class="fw-bold text-truncate small" title="{{ basename($bk->{$bkCol}) }}">{{ basename($bk->{$bkCol}) }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">طلب حجز #{{ $bk->id }}</div>
                              </div>
                            </div>
                            <div class="d-flex gap-2 mt-2 pt-2 border-top">
                              <a href="{{ $bkUrl }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-eye me-1"></i> فتح
                              </a>
                              <a href="{{ $bkUrl }}" download class="btn btn-sm btn-light border" title="تحميل">
                                <i class="bi bi-download"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      @endif
                    @endforeach
                  @endforeach
                </div>
              </div>
            @endif
          @endif

          {{-- 3. Documents from Linked Mobile Application if any --}}
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
                        $idUrl = $caseApp->id_image_url ?? app(\App\Services\ImageUploadService::class)->url($caseApp->id_image_path) ?? asset('storage/' . ltrim($caseApp->id_image_path, '/'));
                      @endphp
                      <div class="col-md-6 col-lg-4">
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
                        $medUrl = $caseApp->medical_report_url ?? app(\App\Services\ImageUploadService::class)->url($caseApp->medical_report_path) ?? asset('storage/' . ltrim($caseApp->medical_report_path, '/'));
                        $isMedImage = in_array(strtolower(pathinfo($caseApp->medical_report_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp'], true);
                      @endphp
                      <div class="col-md-6 col-lg-4">
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

          {{-- 4. Direct File Attachments --}}
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
                <div class="col-md-6 col-lg-4">
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
            @if($totalAttsCount == 0 && empty($isGuestHouseCase))
              <div class="empty-state py-4 text-center text-muted">
                <i class="bi bi-folder2-open display-4 d-block mb-2 text-secondary opacity-50"></i>
                <p class="mb-0">لا توجد ملفات أو أوراق مرفقة حالياً لهذا المستفيد.</p>
              </div>
            @endif
          @endif
        </div>
      </div>

      {{-- Modal to Upload/Replace Guest House Patient Document --}}
      @if(!empty($isGuestHouseCase))
        <div class="modal fade" id="uploadGuestDocModal" tabindex="-1" aria-labelledby="uploadGuestDocModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
              <form method="POST" action="{{ route('beneficiaries.upload-patient-doc', $beneficiary) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                  <h5 class="modal-title fw-bold" id="uploadGuestDocModalLabel">
                    <i class="bi bi-cloud-arrow-up text-primary me-1"></i> رفع مستند لدار الضيافة
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">نوع المستند</label>
                    <select name="doc_type" id="modal_doc_type" class="form-select" required onchange="handleModalDocTypeChange(this.value)">
                      <option value="patient_id_front">بطاقة المريض (الوجه)</option>
                      <option value="patient_id_back">بطاقة المريض (الظهر)</option>
                      <option value="companion_id_front">بطاقة المرافق (الوجه)</option>
                      <option value="companion_id_back">بطاقة المرافق (الظهر)</option>
                      <option value="followup_card">كارت المتابعة (جرعات الكيماوي)</option>
                      <option value="referral_letter">خطاب التحويل / التقرير الطبي</option>
                    </select>
                  </div>

                  {{-- Companion extra info (if companion doc selected) --}}
                  <div id="modalCompanionFields" style="display:none;" class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="small fw-bold text-secondary mb-2"><i class="bi bi-person-badge me-1"></i> بيانات المرافق (اختياري)</div>
                    <div class="row g-2">
                      <div class="col-6">
                        <input type="text" name="companion_name" class="form-control form-control-sm" placeholder="اسم المرافق" value="{{ $patientProfile?->companion_name }}">
                      </div>
                      <div class="col-6">
                        <input type="text" name="companion_relation" class="form-control form-control-sm" placeholder="صلة القرابة" value="{{ $patientProfile?->companion_relation }}">
                      </div>
                      <div class="col-6">
                        <input type="text" name="companion_national_id" maxlength="14" class="form-control form-control-sm" placeholder="الرقم القومي للمرافق" value="{{ $patientProfile?->companion_national_id }}">
                      </div>
                      <div class="col-6">
                        <input type="text" name="companion_phone" class="form-control form-control-sm" placeholder="هاتف المرافق" value="{{ $patientProfile?->companion_phone }}">
                      </div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">اختر الملف أو التقط صورة بالكاميرا</label>
                    <input type="file" name="file" class="form-control" required accept="image/*,.pdf,.doc,.docx" capture="environment">
                    <div class="form-text small">يدعم تصوير البطاقات والمستندات بالكاميرا أو رفع صور وملفات PDF حتى 15 ميجابايت.</div>
                  </div>
                </div>
                <div class="modal-footer bg-light py-2">
                  <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                  <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                    <i class="bi bi-upload me-1"></i> حفظ ورفع المستند
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      @endif

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

@section('scripts')
<script>
function openDocUploadModal(docType, docTitle) {
  var select = document.getElementById('modal_doc_type');
  if (select) {
    select.value = docType;
    handleModalDocTypeChange(docType);
  }
  var label = document.getElementById('uploadGuestDocModalLabel');
  if (label && docTitle) {
    label.innerHTML = '<i class="bi bi-cloud-arrow-up text-primary me-1"></i> رفع / تحديث: ' + docTitle;
  }
  var modalEl = document.getElementById('uploadGuestDocModal');
  if (modalEl) {
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  }
}

function handleModalDocTypeChange(val) {
  var compBox = document.getElementById('modalCompanionFields');
  if (compBox) {
    if (val === 'companion_id_front' || val === 'companion_id_back') {
      compBox.style.display = 'block';
    } else {
      compBox.style.display = 'none';
    }
  }
}

function toggleQuickUploadTarget(val) {
  var form = document.getElementById('quickUploadForm');
  if (!form) return;
  if (val === 'general') {
    form.action = "{{ route('attachments.store') }}";
    var hidden = form.querySelector('input[name="doc_type"]');
    if (hidden) hidden.remove();
  } else {
    form.action = "{{ route('beneficiaries.upload-patient-doc', $beneficiary) }}";
    var hidden = form.querySelector('input[name="doc_type"]');
    if (!hidden) {
      hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'doc_type';
      form.appendChild(hidden);
    }
    hidden.value = val;
  }
}
</script>

{{-- Modal: Quick Record Direct Aid / Assistance --}}
<div class="modal fade" id="recordDirectAidModal" tabindex="-1" aria-labelledby="recordDirectAidModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form action="{{ route('beneficiaries.record-direct-aid', $beneficiary) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      @csrf
      <div class="modal-header py-3 px-4 text-white border-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="d-flex align-items-center gap-2">
          <div class="p-2 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-box-seam fs-6"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0 text-white" id="recordDirectAidModalLabel">تسجيل استلام مساعدة أو خدمة للحالة</h5>
            <small class="text-white-50">سيتم توثيق المساعدة برقم إيصال رسمي وإدراجها فوراً في سجل الحالة وملفها</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>

      <div class="modal-body p-4 bg-white text-dark">
        {{-- Target Beneficiary Notice --}}
        <div class="p-3 rounded-3 bg-light border d-flex justify-content-between align-items-center mb-3">
          <div>
            <div class="text-muted small">الحالة المستفيدة:</div>
            <strong class="fs-6 text-dark">{{ $beneficiary->full_name }}</strong>
            <span class="badge bg-success-subtle text-success font-monospace ms-2">{{ $beneficiary->code }}</span>
          </div>
          <div class="text-end small">
            <span class="text-muted">المشروع:</span>
            <strong class="text-primary">{{ $beneficiary->project?->name ?? 'مشروع عام' }}</strong>
          </div>
        </div>

        <div class="row g-3">
          {{-- Aid Type --}}
          <div class="col-md-6">
            <label class="form-label fw-bold small text-dark mb-1">نوع المساعدة <span class="text-danger">*</span></label>
            <select name="aid_type" id="directAidTypeSelect" class="form-select rounded-3 fw-bold" required onchange="handleAidTypeChange(this.value)">
              <option value="in_kind" selected>📦 مساعدة عينية (كرتونة مواد غذائية، سلة، أجهزة، كسوة)</option>
              <option value="cash">💵 مساعدة نقدية / كفالة مالية</option>
              <option value="service">🏨 خدمة رعاية / إعاشة / علاج</option>
            </select>
          </div>

          {{-- Purpose (بند الإيصال / الغرض) --}}
          <div class="col-md-6">
            <label class="form-label fw-bold small text-dark mb-1">بند الإيصال / الغرض <span class="text-danger">*</span></label>
            <select name="purpose" id="directAidPurposeSelect" class="form-select rounded-3 fw-bold" onchange="handlePurposeSelectChange(this.value)">
              <option value="كرتونة مواد غذائية شهرية">📦 كرتونة مواد غذائية شهرية</option>
              <option value="سلة تموين ومواد غذائية">🛒 سلة تموين ومواد غذائية</option>
              <option value="sadaqat">🤝 صدقات (مساعدة عامة)</option>
              <option value="kafalat_aytam">👶 كفالات أيتام</option>
              <option value="kafalat_awram">🎗️ كفالات أورام</option>
              <option value="zakat_maal">💰 زكاة مال</option>
              <option value="sadaqa_jariya">🌱 صدقة جارية</option>
              <option value="مساعدة علاجية وأدوية">💊 مساعدة علاجية وأدوية</option>
              <option value="كسوة وملابس">👕 كسوة وملابس</option>
              <option value="أجهزة ومستلزمات منزلية">🛋️ أجهزة ومستلزمات منزلية</option>
              <option value="other">✏️ بند آخر (اكتبه في الخانة التالية)...</option>
            </select>
          </div>

          {{-- Category Title (بيان أو مسمى المساعدة التفصيلي) --}}
          <div class="col-md-6">
            <label class="form-label fw-bold small text-dark mb-1">بيان المساعدة التفصيلي</label>
            <input type="text" name="category_title" id="directAidCategoryTitle" class="form-control rounded-3" placeholder="مثال: كرتونة مواد غذائية شهرية، أو كفالة شهرية" value="كرتونة مواد غذائية شهرية">
          </div>

          {{-- Amount / Estimated Value --}}
          <div class="col-md-6">
            <label class="form-label fw-bold small text-dark mb-1" id="aidAmountLabel">القيمة التقديرية أو المبلغ (ج.م)</label>
            <input type="number" step="0.01" min="0" name="amount" class="form-control rounded-3 font-monospace" placeholder="0.00" value="0.00">
          </div>

          {{-- Project Assignment --}}
          <div class="col-md-6">
            <label class="form-label fw-bold small text-dark mb-1">تخصيص المشروع</label>
            <select name="project_id" class="form-select rounded-3">
              @if($beneficiary->project)
                <option value="{{ $beneficiary->project_id }}" selected>مشروع الحالة: {{ $beneficiary->project->name }}</option>
              @endif
              <option value="none" @selected(!$beneficiary->project_id)>-- عام / بدون تخصيص لمشروع --</option>
              @if(!empty($allProjects))
                @foreach($allProjects as $proj)
                  @if($proj->id !== $beneficiary->project_id)
                    <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                  @endif
                @endforeach
              @endif
            </select>
            <div class="form-text small text-muted">يمكنك ربط المساعدة بمشروع الحالة أو جعلها عامة بدون مشروع.</div>
          </div>

          {{-- Date --}}
          <div class="col-md-6">
            <label class="form-label fw-bold small text-dark mb-1">تاريخ التسليم / الاستلام <span class="text-danger">*</span></label>
            <input type="date" name="received_at" class="form-control rounded-3 font-monospace" required value="{{ date('Y-m-d') }}">
          </div>

          {{-- Donor / Sponsor (Optional) --}}
          <div class="col-md-12">
            <label class="form-label fw-bold small text-dark mb-1">المتبرع أو الكفيل المموّل (اختياري)</label>
            <select name="donor_id" class="form-select rounded-3">
              <option value="">-- فاعل خير / تبرع عام / المؤسسة --</option>
              @foreach($allDonors as $dn)
                <option value="{{ $dn->id }}" @selected($beneficiary->sponsors->contains($dn->id))>
                  {{ $dn->name }} ({{ $dn->code ?? ('D-'.$dn->id) }}) {{ $dn->phone ? '— '.$dn->phone : '' }}
                </option>
              @endforeach
            </select>
            <div class="form-text small text-muted">في حال اختيار متبرع، سيتم ربطه تلقائياً ككافل في ملف الحالة إن لم يكن مرتبطاً مسبقاً.</div>
          </div>

          {{-- Notes --}}
          <div class="col-12">
            <label class="form-label fw-bold small text-dark mb-1">ملاحظات وبيان التسليم</label>
            <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="اكتب أي ملاحظات إضافية بخصوص تسليم المساعدة للحالة..."></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer py-2.5 px-4 bg-light border-top d-flex justify-content-between">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-1.5">
          <i class="bi bi-check2-circle fs-6"></i>
          <span>حفظ وتوثيق المساعدة في السجل</span>
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: Attach Sponsor to Beneficiary --}}
<div class="modal fade" id="attachSponsorModal" tabindex="-1" aria-labelledby="attachSponsorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('beneficiaries.attach-sponsor', $beneficiary) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      @csrf
      <div class="modal-header py-3 px-4 text-white border-0" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-person-heart fs-5"></i>
          <h5 class="modal-title fw-bold mb-0 text-white" id="attachSponsorModalLabel">ربط كفيل بملف الحالة</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>

      <div class="modal-body p-4 bg-white text-dark">
        <div class="alert alert-info border-0 rounded-3 small mb-3">
          <i class="bi bi-info-circle-fill me-1"></i> يمكنك توجيه الكفالة لطفل/فرد محدد في الأسرة أو للأسرة ككل. في حال ربط الكفيل بطفل، يتم تسجيل الكفالة باسمه وتُجمع تلقائياً في إجمالي كفالات الأسرة.
        </div>

        {{-- Family Member (Child) Selection --}}
        <div class="mb-3">
          <label class="form-label fw-bold text-dark mb-1">المستفيد من الكفالة (الطفل أو الأسرة ككل)</label>
          <select name="family_member_id" class="form-select rounded-3">
            <option value="">🏠 ملف الأسرة بالكامل (كفالة عامة للأسرة)</option>
            @if($beneficiary->familyMembers->isNotEmpty())
              <optgroup label="أطفال وأفراد الأسرة:">
                @foreach($beneficiary->familyMembers as $m)
                  <option value="{{ $m->id }}">
                    👶 {{ $m->full_name }} ({{ $m->relationship ?? 'ابن/ابنة' }}) @if($m->sponsorship_amount > 0) — الكفالة الحالية: {{ number_format($m->sponsorship_amount, 2) }} ج.م @endif
                  </option>
                @endforeach
              </optgroup>
            @endif
          </select>
          <div class="form-text small text-muted">اختر الطفل لتخصيص الكفالة له باسمه، أو اتركها لملف الأسرة ككل.</div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark mb-1">اختر المتبرع / الكفيل <span class="text-danger">*</span></label>
          <select name="donor_id" class="form-select rounded-3" required>
            <option value="">-- اختر المتبرع من القائمة --</option>
            @foreach($allDonors as $dn)
              <option value="{{ $dn->id }}" @selected($beneficiary->sponsors->contains($dn->id))>
                {{ $dn->name }} ({{ $dn->code ?? ('D-'.$dn->id) }}) {{ $dn->phone ? '— '.$dn->phone : '' }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark mb-1">قيمة الكفالة الشهرية (ج.م) <small class="text-muted">(اختياري للتحديث)</small></label>
          <input type="number" step="0.01" min="0" name="monthly_amount" class="form-control rounded-3 font-monospace" placeholder="مثال: 500.00">
        </div>

        <div class="mb-2">
          <label class="form-label fw-bold text-muted small mb-1">ملاحظات الكفالة (اختياري)</label>
          <input type="text" name="notes" class="form-control rounded-3" placeholder="مثال: كفالة أورام للطفل، كفالة يتيم شهرية">
        </div>
      </div>

      <div class="modal-footer py-2.5 px-4 bg-light border-top d-flex justify-content-between">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
          <i class="bi bi-link-45deg me-1"></i> اعتماد وربط الكفيل
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: 6-Month Periodic Reassessment (استمارة إعادة بحث وتحديث بيانات الأسرة كل 6 أشهر) --}}
<div class="modal fade" id="reassessmentModal" tabindex="-1" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <form action="{{ route('beneficiaries.reassessments.store', $beneficiary) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
      @csrf
      <div class="modal-header text-white px-4 py-3 border-0" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
        <div>
          <h5 class="modal-title fw-bold mb-0 text-white"><i class="bi bi-arrow-repeat me-1"></i> استمارة إعادة بحث وتحديث بيانات الأسرة (كل 6 أشهر — بعثاء الأمل)</h5>
          <small class="text-white-50">تحديث دوري للحالة ({{ $beneficiary->full_name }} — {{ $beneficiary->code }}) مع الاحتفاظ الكامل بالبيانات الأساسية</small>
        </div>
        <button type="button" class="btn-close btn-close-white ms-0 me-auto" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 bg-white">
        @include('beneficiaries.partials.reassessment_form_fields', ['beneficiary' => $beneficiary])
      </div>
      <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background:#0d9488;">
          <i class="bi bi-check2-circle me-1"></i> حفظ وتوثيق استمارة إعادة البحث
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: Beneficiary Psychological Support Report --}}
<div class="modal fade" id="beneficiaryPsychSupportModal" tabindex="-1" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <form action="{{ route('psychological-support.store', $beneficiary) }}" method="POST">
        @csrf
        <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #ec4899, #f43f5e);">
              <i class="bi bi-heart-pulse-fill fs-5"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold text-dark mb-0">تقرير الدعم النفسي الشهري</h5>
              <small class="text-muted">الحالة: <strong class="text-dark">{{ $beneficiary->full_name }}</strong> ({{ $beneficiary->code }})</small>
            </div>
          </div>
          <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="alert alert-light border d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <span class="small text-muted"><i class="bi bi-share-fill text-danger me-1"></i> يمكنك إرسال الاستمارة كرابط للمتطوع ليملأها مباشرة:</span>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="navigator.clipboard.writeText('{{ $benPsychPublicUrl ?? '' }}'); alert('تم نسخ رابط استمارة الدعم النفسي للمتطوع!');">
              <i class="bi bi-clipboard me-1"></i> نسخ الرابط للمتطوع
            </button>
          </div>
          <div class="row g-3">
            @if($beneficiary->familyMembers->isNotEmpty())
              <div class="col-12">
                <label class="form-label small fw-bold">الفرد المريض داخل الأسرة (اختياري)</label>
                <select name="family_member_id" class="form-select">
                  <option value="">— الحالة الأساسية ({{ $beneficiary->full_name }}) —</option>
                  @foreach($beneficiary->familyMembers as $fm)
                    <option value="{{ $fm->id }}" @selected($fm->is_patient)>{{ $fm->full_name }} ({{ $fm->relationship_label }})</option>
                  @endforeach
                </select>
              </div>
            @endif
            <div class="col-md-6">
              <label class="form-label small fw-bold">الشهر المستهدف <span class="text-danger">*</span></label>
              <input type="month" name="report_month" class="form-control fw-bold font-monospace" value="{{ now()->format('Y-m') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">اسم المتطوع ثلاثي <span class="text-danger">*</span></label>
              <input type="text" name="volunteer_name" class="form-control" value="{{ auth()->user()->name ?? '' }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">الحالة الصحية / نوع الورم</label>
              <input type="text" name="medical_condition" class="form-control" value="{{ $beneficiary->tumor_location }}">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">المرحلة العلاجية الحالية</label>
              <input type="text" name="treatment_stage" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-danger">الحالة النفسية العامة للمريض <span class="text-danger">*</span></label>
              <select name="psychological_state" class="form-select fw-bold" required>
                <option value="ممتاز">ممتاز</option>
                <option value="كويس" selected>كويس</option>
                <option value="راضي">راضي</option>
                <option value="حزين">حزين</option>
                <option value="يشعر بالضجر">يشعر بالضجر</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-warning">التقييم (من 1 إلى 5) <span class="text-danger">*</span></label>
              <select name="rating" class="form-select fw-bold font-monospace" required>
                <option value="5" selected>★★★★★ (5)</option>
                <option value="4">★★★★☆ (4)</option>
                <option value="3">★★★☆☆ (3)</option>
                <option value="2">★★☆☆☆ (2)</option>
                <option value="1">★☆☆☆☆ (1)</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold">ظروف وسياق المكالمة</label>
              <textarea name="call_condition" rows="2" class="form-control"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">النظام الغذائي والتغذية</label>
              <textarea name="nutrition_diet" rows="2" class="form-control"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">ما يحتاجه المريض لتحسين حالته النفسية</label>
              <textarea name="needs_improvement" rows="2" class="form-control"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold">رأي مسؤول الدعم النفسي والتوصيات</label>
              <textarea name="supporter_opinion" rows="2" class="form-control"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold">ملاحظات عامة</label>
              <input type="text" name="general_notes" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-danger px-4 fw-bold">حفظ وتوثيق التقرير</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
function filterAidLedger(cat, btn) {
  var rows = document.querySelectorAll('.aid-ledger-row');
  rows.forEach(function(row) {
    if (cat === 'all') {
      row.style.display = '';
    } else if (row.getAttribute('data-category') === cat) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
  var buttons = document.querySelectorAll('#aidLedgerTabs .nav-link');
  buttons.forEach(function(b) {
    b.classList.remove('active', 'bg-white', 'shadow-2xs');
    b.classList.add('bg-light');
  });
  if (btn) {
    btn.classList.add('active', 'bg-white', 'shadow-2xs');
    btn.classList.remove('bg-light');
  }
}

function handleAidTypeChange(type) {
  var label = document.getElementById('aidAmountLabel');
  var purposeSel = document.getElementById('directAidPurposeSelect');
  var titleInput = document.getElementById('directAidCategoryTitle');

  if (type === 'cash') {
    if (label) label.innerText = 'مبلغ المساعدة النقدية (ج.م) *';
    if (purposeSel && (purposeSel.value.includes('كرتونة') || purposeSel.value.includes('سلة'))) {
      purposeSel.value = 'sadaqat';
      if (titleInput) titleInput.value = 'مساعدة نقدية للحالة';
    }
  } else if (type === 'service') {
    if (label) label.innerText = 'التكلفة أو القيمة التقديرية للخدمة (اختياري)';
    if (purposeSel && (purposeSel.value === 'sadaqat' || purposeSel.value.includes('كرتونة'))) {
      purposeSel.value = 'مساعدة علاجية وأدوية';
      if (titleInput) titleInput.value = 'خدمة رعاية وعلاج للحالة';
    }
  } else {
    if (label) label.innerText = 'القيمة التقديرية للمساعدة العينية (ج.م)';
    if (purposeSel && (purposeSel.value === 'sadaqat' || purposeSel.value === 'kafalat_aytam')) {
      purposeSel.value = 'كرتونة مواد غذائية شهرية';
      if (titleInput) titleInput.value = 'كرتونة مواد غذائية شهرية';
    }
  }
}

function handlePurposeSelectChange(val) {
  var titleInput = document.getElementById('directAidCategoryTitle');
  if (!titleInput) return;
  if (val === 'other') {
    titleInput.value = '';
    titleInput.focus();
  } else {
    var sel = document.getElementById('directAidPurposeSelect');
    if (sel && sel.selectedIndex >= 0) {
      var optText = sel.options[sel.selectedIndex].text.replace(/^[^\s]+\s*/, '');
      titleInput.value = optText;
    }
  }
}
</script>
@endsection


