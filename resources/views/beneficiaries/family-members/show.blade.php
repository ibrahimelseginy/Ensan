@extends('layouts.app')

@section('content')
<div class="container-fluid family-member-profile" dir="rtl">
  <div class="page-header">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar avatar-lg avatar-primary"><i class="bi bi-person-badge"></i></div>
      <div>
        <h4 class="mb-1">{{ $familyMember->full_name }}</h4>
        <div class="text-muted">ملف مستقل — {{ $familyMember->relationship_label }} — <span class="font-monospace">{{ $familyMember->code }}</span></div>
      </div>
    </div>
    <div class="d-flex gap-2 no-print flex-wrap">
      @php
        $memberPsychPublicUrl = \App\Http\Controllers\PsychologicalSupportController::getPublicUrl('member', $familyMember->id);
      @endphp
      <button type="button" class="btn btn-danger d-inline-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#psychologicalSupportModal" title="توثيق ومتابعة تقرير الدعم النفسي الشهري للحالة">
        <i class="bi bi-heart-pulse-fill"></i> توثيق دعم نفسي
      </button>
      <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1.5" onclick="navigator.clipboard.writeText('{{ $memberPsychPublicUrl }}'); alert('تم نسخ رابط استمارة الدعم النفسي للمتطوع بنجاح:\n{{ $memberPsychPublicUrl }}');" title="نسخ رابط استمارة الدعم النفسي لإرساله لمتطوع">
        <i class="bi bi-share-fill"></i> مشاركة استمارة الدعم النفسي للمتطوع
      </button>
      <a href="https://wa.me/?text={{ urlencode('استمارة الدعم النفسي الشهري للحالة (' . $familyMember->full_name . ' - كود ' . $familyMember->code . '): ' . $memberPsychPublicUrl) }}" target="_blank" class="btn btn-success d-inline-flex align-items-center gap-1" title="إرسال رابط الاستمارة عبر واتساب">
        <i class="bi bi-whatsapp"></i>
      </a>
      <button type="button" onclick="EnsanPrintPreview.open(null, {title: 'ملف الفرد المكفول — {{ $familyMember->full_name }}'})" class="btn btn-outline-info"><i class="bi bi-eye me-1"></i> معاينة قبل الطباعة</button>
      <button type="button" onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i> طباعة الملف</button>
      <a href="{{ route('beneficiaries.show', $familyMember->beneficiary) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i> ملف ولي الأمر</a>
    </div>
  </div>

  <div class="alert alert-info">
    <i class="bi bi-shield-lock me-1"></i>
    هذه الصفحة تعرض بيانات الفرد المكفول / الحالة المريضة فقط، ولا تعرض بيانات باقي أفراد الأسرة.
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-body">
          <div class="section-title mb-3"><i class="bi bi-person-vcard"></i><h5 class="mb-0">بيانات الفرد</h5></div>
          <div class="row g-3">
            <div class="col-md-6"><div class="info-row"><span class="info-label">الاسم بالكامل</span><span class="info-value">{{ $familyMember->full_name }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الكود</span><span class="info-value font-monospace">{{ $familyMember->code }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">صلة القرابة</span><span class="info-value">{{ $familyMember->relationship_label }} @if($familyMember->is_patient) <span class="badge bg-danger-subtle text-danger ms-1">مريض أورام/علاج</span> @endif</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">العمر</span><span class="info-value">{{ $familyMember->birth_date ? $familyMember->birth_date->age . ' سنة' : ($familyMember->age !== null ? $familyMember->age . ' سنة' : '—') }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">تاريخ الميلاد</span><span class="info-value">{{ optional($familyMember->birth_date)->format('Y-m-d') ?? ($familyMember->age ? \Carbon\Carbon::now()->subYears((int) $familyMember->age)->format('Y') . ' (تقديري)' : '—') }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الرقم القومي</span><span class="info-value font-monospace">{{ $familyMember->national_id ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الهاتف</span><span class="info-value font-monospace">{{ $familyMember->phone ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الهاتف الإضافي</span><span class="info-value font-monospace">{{ $familyMember->backup_phone ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">المستوى التعليمي</span><span class="info-value">{{ $familyMember->education_level ?? '—' }}</span></div></div>
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">مبلغ الكفالة</span>
                <span class="info-value d-flex align-items-center justify-content-between">
                  <span>{{ $familyMember->sponsorship_amount !== null ? number_format((float) $familyMember->sponsorship_amount, 2) . ' ج.م' : '—' }}</span>
                  <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 ms-2 no-print" data-bs-toggle="modal" data-bs-target="#editSponsorshipAmountModal" title="تعديل كفالة الطفل">
                    <i class="bi bi-pencil-square me-1"></i>تعديل
                  </button>
                </span>
              </div>
            </div>
            @if($familyMember->case_details)
              <div class="col-12"><div class="p-3 rounded border"><div class="small text-muted mb-2">تفاصيل الحالة والمتابعة</div><div>{{ $familyMember->case_details }}</div></div></div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-body">
          <div class="section-title mb-3"><i class="bi bi-house-heart"></i><h5 class="mb-0">مرجع الأسرة</h5></div>
          <div class="info-row"><span class="info-label">ولي الأمر</span><span class="info-value">{{ $familyMember->beneficiary->full_name }}</span></div>
          <div class="info-row"><span class="info-label">كود الأسرة</span><span class="info-value font-monospace">{{ $familyMember->beneficiary->code ?? '—' }}</span></div>
          <div class="info-row"><span class="info-label">المشروع</span><span class="info-value">{{ $familyMember->beneficiary->project?->name ?? '—' }}</span></div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title mb-0"><i class="bi bi-person-heart"></i><h5 class="mb-0">الكفلاء المرتبطون بهذا الفرد</h5></div>
            <button type="button" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 no-print fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#attachMemberSponsorModal">
              <i class="bi bi-plus-circle me-1"></i> ربط كافل
            </button>
          </div>
          <div class="d-grid gap-2">
            @forelse($familyMember->sponsors as $sponsor)
              <div class="border rounded-3 p-3 position-relative bg-white shadow-xs">
                <div class="d-flex justify-content-between align-items-start gap-2">
                  <div>
                    <a href="{{ route('donors.show', $sponsor) }}" class="fw-bold text-success text-decoration-none d-block">
                      <i class="bi bi-person-heart me-1"></i>{{ $sponsor->name }}
                    </a>
                    <div class="small text-muted font-monospace">{{ $sponsor->code ?? ('DON-' . $sponsor->id) }} {{ $sponsor->phone ? '— ' . $sponsor->phone : '' }}</div>
                    @if($sponsor->pivot->monthly_amount)
                      <div class="mt-1"><span class="badge bg-success-subtle text-success border border-success">{{ number_format((float)$sponsor->pivot->monthly_amount, 2) }} ج.م شهرياً</span></div>
                    @endif
                    @if($sponsor->pivot->notes)
                      <div class="small text-muted mt-1 fst-italic">{{ $sponsor->pivot->notes }}</div>
                    @endif
                  </div>
                  <form action="{{ route('beneficiary-family-members.detach-sponsor', [$familyMember, $sponsor]) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد إلغاء ربط هذا الكافل بهذا الطفل؟');" class="no-print">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger py-0.5 px-2 rounded-pill" title="إلغاء ربط الكافل">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </form>
                </div>
              </div>
            @empty
              <div class="text-muted small p-2 bg-light rounded text-center">لا يوجد كافل مرتبط بهذا الفرد حتى الآن.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Monthly Psychological Support Reports Section (الدعم النفسي والمتابعة الشهرية للحالة المريضة) ── --}}
  <div class="card mt-4 border-0 shadow-sm" id="psychologicalSupportSection" style="border-radius: 16px; overflow: hidden; border-top: 4px solid #ec4899 !important;">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: linear-gradient(135deg, #ec4899, #f43f5e);">
          <i class="bi bi-heart-pulse-fill fs-5"></i>
        </div>
        <div>
          <h5 class="mb-0 fw-bold text-dark">سجل تقارير الدعم النفسي الشهري (بعثاء الأمل)</h5>
          <small class="text-muted">متابعة شهرية دورية للحالة النفسية والمعنوية والتغذية ونسبة التجاوب مع العلاج للحالة: <strong class="text-dark">{{ $familyMember->full_name }}</strong></small>
        </div>
      </div>
      <div class="d-flex gap-2 flex-wrap no-print">
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-bold d-flex align-items-center gap-1.5" onclick="navigator.clipboard.writeText('{{ $memberPsychPublicUrl }}'); alert('تم نسخ رابط استمارة الدعم النفسي للمتطوع بنجاح!');">
          <i class="bi bi-link-45deg"></i>
          <span>نسخ رابط الاستمارة للمتطوع</span>
        </button>
        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#psychologicalSupportModal">
          <i class="bi bi-plus-circle"></i>
          <span>توثيق تقرير دعم نفسي</span>
        </button>
      </div>
    </div>

    <div class="card-body p-4 bg-light bg-opacity-50">
      @if(isset($psychReports) && $psychReports->isNotEmpty())
        <div class="row g-3">
          @foreach($psychReports as $pReport)
            @php
              $pStateClass = match($pReport->psychological_state) {
                  'ممتاز' => 'success',
                  'كويس' => 'primary',
                  'راضي' => 'info',
                  'حزين' => 'warning',
                  'يشعر بالضجر' => 'danger',
                  default => 'secondary',
              };
            @endphp
            <div class="col-12 col-md-6">
              <div class="card border-0 shadow-2xs rounded-3 p-3 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <span class="badge bg-pink text-white rounded-pill px-2.5 py-1 font-monospace" style="background-color: #ec4899;">
                      <i class="bi bi-calendar-event me-1"></i>شهر {{ $pReport->report_month }}
                    </span>
                    <div class="fw-bold text-dark mt-1">
                      <i class="bi bi-person-heart text-danger me-1"></i>{{ $pReport->volunteer_name ?: 'المتطوع' }}
                    </div>
                  </div>
                  <div class="text-end">
                    <span class="badge bg-{{ $pStateClass }}-subtle text-{{ $pStateClass }} px-2 py-1 rounded-pill small">
                      {{ $pReport->psychological_state }}
                    </span>
                    <div class="text-warning small font-monospace mt-1">
                      {{ str_repeat('★', $pReport->rating) . str_repeat('☆', 5 - $pReport->rating) }}
                    </div>
                  </div>
                </div>

                @if($pReport->medical_condition || $pReport->treatment_stage)
                  <div class="small text-muted mb-2 border-top pt-2">
                    @if($pReport->medical_condition)<div><strong>الحالة الطبية:</strong> {{ $pReport->medical_condition }}</div>@endif
                    @if($pReport->treatment_stage)<div><strong>المرحلة:</strong> {{ $pReport->treatment_stage }}</div>@endif
                  </div>
                @endif

                @if($pReport->call_condition)
                  <div class="small text-secondary mb-1">
                    <strong>سياق المكالمة:</strong> {{ Str::limit($pReport->call_condition, 80) }}
                  </div>
                @endif

                @if($pReport->nutrition_diet)
                  <div class="small text-secondary mb-1">
                    <strong>التغذية:</strong> {{ Str::limit($pReport->nutrition_diet, 80) }}
                  </div>
                @endif

                @if($pReport->needs_improvement)
                  <div class="small text-secondary mb-1">
                    <strong>احتياجات التحسين:</strong> {{ Str::limit($pReport->needs_improvement, 80) }}
                  </div>
                @endif

                @if($pReport->supporter_opinion)
                  <div class="small text-dark fw-medium border-top pt-2 mt-2" style="background: rgba(254, 242, 242, 0.5); padding: 6px; border-radius: 6px;">
                    <i class="bi bi-chat-quote-fill text-danger me-1"></i>{{ $pReport->supporter_opinion }}
                  </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top extra-small text-muted" style="font-size: 0.75rem;">
                  <span>سجل بواسطة: {{ $pReport->creator?->name ?? 'النظام' }}</span>
                  <span>{{ $pReport->created_at?->format('Y-m-d') }}</span>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-4">
          <div class="text-muted mb-2">
            <i class="bi bi-chat-heart fs-1 text-danger-subtle"></i>
          </div>
          <h6 class="fw-bold text-dark">لا توجد تقارير دعم نفسي مسجلة بعد لهذا المريض</h6>
          <p class="text-muted small mb-3">يتم تجديد تقارير الدعم النفسي شهرياً للاطمئنان على حالة المريض المعنوية وتغذيته واستجابته للعلاج.</p>
          <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1.5 rounded-pill no-print" data-bs-toggle="modal" data-bs-target="#psychologicalSupportModal">
            <i class="bi bi-plus-circle me-1"></i> توثيق أول تقرير لهذا الشهر الآن
          </button>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- ── Psychological Support Modal (مودال توثيق الدعم النفسي الشهري للحالة المريضة) ── --}}
<div class="modal fade" id="psychologicalSupportModal" tabindex="-1" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <form action="{{ route('beneficiary-family-members.psychological-support.store', $familyMember) }}" method="POST">
        @csrf
        <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #ec4899, #f43f5e);">
              <i class="bi bi-heart-pulse-fill fs-5"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold text-dark mb-0">تقرير الدعم النفسي الشهري (بعثاء الأمل)</h5>
              <small class="text-muted">توثيق تقرير المتابعة الدورية للحالة المريضة: <strong class="text-dark">{{ $familyMember->full_name }}</strong> (كود: {{ $familyMember->code }})</small>
            </div>
          </div>
          <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">الشهر المستهدف <span class="text-danger">*</span></label>
              <input type="month" name="report_month" class="form-control fw-bold font-monospace" value="{{ now()->format('Y-m') }}" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">اسم المتطوع ثلاثي <span class="text-danger">*</span></label>
              <input type="text" name="volunteer_name" class="form-control" value="{{ auth()->user()->name ?? '' }}" required placeholder="اسم المتطوع المتواصل">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">الحالة الصحية / نوع الورم</label>
              <input type="text" name="medical_condition" class="form-control" value="{{ $familyMember->case_details ?: ($familyMember->beneficiary?->tumor_location ?? '') }}" placeholder="مثال: ورم بالمخ، أورام دم، مستقرة...">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">المرحلة العلاجية الحالية</label>
              <input type="text" name="treatment_stage" class="form-control" placeholder="مثال: جلسات كيماوي مكثفة، إشعاعي، متابعة شهرية...">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold text-danger">الحالة النفسية العامة للمريض <span class="text-danger">*</span></label>
              <select name="psychological_state" class="form-select fw-bold" required>
                <option value="ممتاز">ممتاز (متفائل ومتقبل للعلاج بروح عالية)</option>
                <option value="كويس" selected>كويس (مستقر نفسياً ومتعاون)</option>
                <option value="راضي">راضي (صابر ومتقبل لكن يمر بفتور)</option>
                <option value="حزين">حزين (يمر بضيق أو قلق أو مخاوف)</option>
                <option value="يشعر بالضجر">يشعر بالضجر (متعب جداً ومحبط ويحتاج تدخل معنوي عاجل)</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold text-warning">التقييم العام للحالة النفسية (من 1 إلى 5) <span class="text-danger">*</span></label>
              <select name="rating" class="form-select fw-bold font-monospace" required>
                <option value="5" selected>★★★★★ (5 نجوم - ممتاز جداً)</option>
                <option value="4">★★★★☆ (4 نجوم - جيد جداً)</option>
                <option value="3">★★★☆☆ (3 نجوم - متوسط ومستقر)</option>
                <option value="2">★★☆☆☆ (نجمتان - ضعيف ويحتاج اهتمام)</option>
                <option value="1">★☆☆☆☆ (نجمة واحدة - حرج نفسياً)</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label small fw-bold">ظروف وسياق المكالمة / المقابلة</label>
              <textarea name="call_condition" rows="2" class="form-control" placeholder="مدة المكالمة، تجاوب الحالة، من قام بالرد (المريض أم المرافق)..."></textarea>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">النظام الغذائي والتغذية</label>
              <textarea name="nutrition_diet" rows="2" class="form-control" placeholder="مدى التزام المريض بتعليمات الأطباء والتغذية السليمة..."></textarea>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">ما يحتاجه المريض لتحسين حالته النفسية</label>
              <textarea name="needs_improvement" rows="2" class="form-control" placeholder="احتياجات معنوية، زيارة ميدانية، أدوية، توفير متطلبات..."></textarea>
            </div>

            <div class="col-12">
              <label class="form-label small fw-bold">رأي مسؤول الدعم النفسي والتوصيات</label>
              <textarea name="supporter_opinion" rows="2" class="form-control" placeholder="رأيك وتوصياتك الطبية والمعنوية تجاه هذه الحالة للمرحلة القادمة..."></textarea>
            </div>

            <div class="col-12">
              <label class="form-label small fw-bold">ملاحظات عامة إضافية</label>
              <input type="text" name="general_notes" class="form-control" placeholder="أي ملاحظات أخرى تود تسجيلها...">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">
            <i class="bi bi-check2-circle me-1"></i> حفظ وتوثيق التقرير الشهري
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

{{-- Modal: Edit Child Sponsorship Amount --}}
<div class="modal fade" id="editSponsorshipAmountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <form action="{{ route('beneficiary-family-members.update-sponsorship', $familyMember) }}" method="POST" class="modal-content border-0 shadow rounded-4">
      @csrf
      @method('PATCH')
      <div class="modal-header py-2.5 px-3 bg-primary text-white">
        <h6 class="modal-title fw-bold mb-0 text-white"><i class="bi bi-cash-stack me-1"></i>كفالة: {{ $familyMember->full_name }}</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-3">
        <label class="form-label fw-bold small text-muted">مبلغ الكفالة الشهري للطفل (ج.م)</label>
        <input type="number" step="0.01" min="0" name="sponsorship_amount" value="{{ $familyMember->sponsorship_amount }}" class="form-control" placeholder="مثال: 200 أو 1500" required>
        <div class="form-text small text-muted">هذا المبلغ يخص هذا الفرد فقط، ويسمّع تلقائياً في إجمالي كفالات الأسرة.</div>
      </div>
      <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between">
        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">حفظ الكفالة</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: Attach Sponsor to Member --}}
<div class="modal fade" id="attachMemberSponsorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('beneficiary-family-members.attach-sponsor', $familyMember) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      @csrf
      <div class="modal-header py-3 px-4 text-white border-0" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-person-heart fs-5"></i>
          <h5 class="modal-title fw-bold mb-0 text-white">ربط كافل بالطفل: {{ $familyMember->full_name }}</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 bg-white text-dark">
        <div class="alert alert-success border-0 rounded-3 small mb-3">
          <i class="bi bi-shield-check me-1"></i> يتم ربط الكافل بهذا الطفل/الفرد خصيصاً، وسيتم توثيق الكفالة له، كما ستسمّع تلقائياً في إجمالي الأسرة.
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark mb-1">اختر الكافل / المتبرع <span class="text-danger">*</span></label>
          <select name="donor_id" class="form-select rounded-3" required>
            <option value="">-- اختر المتبرع من القائمة --</option>
            @foreach($allDonors ?? [] as $dn)
              <option value="{{ $dn->id }}" @selected($familyMember->sponsors->contains($dn->id))>
                {{ $dn->name }} ({{ $dn->code ?? ('D-'.$dn->id) }}) {{ $dn->phone ? '— '.$dn->phone : '' }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark mb-1">مبلغ الكفالة الشهري لهذا الطفل (ج.م)</label>
          <input type="number" step="0.01" min="0" name="monthly_amount" value="{{ $familyMember->sponsorship_amount }}" class="form-control rounded-3" placeholder="مثال: 200 أو 1500">
          <div class="form-text text-muted small">سيتم تحديث مبلغ كفالة هذا الطفل بهذا الرقم تلقائياً.</div>
        </div>

        <div class="mb-2">
          <label class="form-label fw-bold text-muted small mb-1">ملاحظات الكفالة (اختياري)</label>
          <input type="text" name="notes" class="form-control rounded-3" placeholder="مثال: كفالة يتيم زاد، كفالة علاج أورام للطفل">
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
@endsection

@section('styles')
<style>
  @media print {
    .navbar, .sidebar-fixed, .no-print, .page-header .btn { display: none !important; }
    body { padding-top: 0 !important; }
    .family-member-profile { max-width: 100%; }
    .card { box-shadow: none !important; border: 1px solid #bbb !important; page-break-inside: avoid; }
  }
</style>
@endsection
