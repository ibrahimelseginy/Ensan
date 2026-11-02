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
    <div class="d-flex gap-2 no-print">
      <button type="button" onclick="EnsanPrintPreview.open(null, {title: 'ملف الفرد المكفول — {{ $familyMember->full_name }}'})" class="btn btn-outline-info"><i class="bi bi-eye me-1"></i> معاينة قبل الطباعة</button>
      <button type="button" onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i> طباعة الملف</button>
      <a href="{{ route('beneficiaries.show', $familyMember->beneficiary) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i> ملف ولي الأمر</a>
    </div>
  </div>

  <div class="alert alert-info">
    <i class="bi bi-shield-lock me-1"></i>
    هذه الصفحة تعرض بيانات الفرد المكفول فقط، ولا تعرض بيانات باقي أفراد الأسرة.
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-body">
          <div class="section-title mb-3"><i class="bi bi-person-vcard"></i><h5 class="mb-0">بيانات الفرد</h5></div>
          <div class="row g-3">
            <div class="col-md-6"><div class="info-row"><span class="info-label">الاسم بالكامل</span><span class="info-value">{{ $familyMember->full_name }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الكود</span><span class="info-value font-monospace">{{ $familyMember->code }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">صلة القرابة</span><span class="info-value">{{ $familyMember->relationship_label }} @if($familyMember->is_patient) — مريض @endif</span></div></div>
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
