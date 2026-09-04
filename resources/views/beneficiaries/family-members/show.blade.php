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
            <div class="col-md-6"><div class="info-row"><span class="info-label">تاريخ الميلاد</span><span class="info-value">{{ optional($familyMember->birth_date)->format('Y-m-d') ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الرقم القومي</span><span class="info-value font-monospace">{{ $familyMember->national_id ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الهاتف</span><span class="info-value font-monospace">{{ $familyMember->phone ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الهاتف الإضافي</span><span class="info-value font-monospace">{{ $familyMember->backup_phone ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">المستوى التعليمي</span><span class="info-value">{{ $familyMember->education_level ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">مبلغ الكفالة</span><span class="info-value">{{ $familyMember->sponsorship_amount !== null ? number_format((float) $familyMember->sponsorship_amount, 2) . ' ج.م' : '—' }}</span></div></div>
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
          <div class="section-title mb-3"><i class="bi bi-person-heart"></i><h5 class="mb-0">الكفلاء المرتبطون بهذا الفرد</h5></div>
          <div class="d-grid gap-2">
            @forelse($familyMember->sponsors as $sponsor)
              <a href="{{ route('donors.show', $sponsor) }}" class="border rounded-3 p-3 text-decoration-none">
                <div class="fw-bold text-success">{{ $sponsor->name }}</div>
                <div class="small text-muted font-monospace">{{ $sponsor->code ?? ('DON-' . $sponsor->id) }}</div>
              </a>
            @empty
              <div class="text-muted">لا يوجد كافل مرتبط بهذا الفرد حتى الآن.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
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
