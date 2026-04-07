@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    <div class="d-flex justify-content-between align-items-center mb-5 animate-reveal-down px-2">
        <div>
            <h1 class="h2 fw-800 text-stats-main mb-1">طلبات التطوع <span class="text-primary">(تطبيق الموبايل)</span></h1>
            <p class="text-muted-theme small mb-0">إدارة ومتابعة طلبات الانضمام القادمة حصرياً من تطبيق الهاتف</p>
        </div>
        <div class="glass-badge-theme px-4 py-2">
            <i class="bi bi-people-fill me-2 text-primary"></i>
            <span class="fw-bold">إجمالي الطلبات:</span> {{ $requests->count() }}
        </div>
    </div>

    <div class="row g-4">
        @forelse($requests as $request)
        <div class="col-md-6 col-lg-4 col-xl-4 animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
            <div class="premium-volunteer-card bg-stats-card-main border-light-subtle">
                <div class="card-inner-top">
                    <div class="card-meta mb-4">
                        <span class="badge-premium @if($request->status == 'new') status-new @elseif($request->status == 'contacted') status-warn @elseif($request->status == 'accepted') status-success @else status-danger @endif">
                            {{ $request->status == 'new' ? 'جديد' : ($request->status == 'contacted' ? 'تم التواصل' : ($request->status == 'accepted' ? 'مقبول' : 'مرفوض')) }}
                        </span>
                        <div class="card-date text-muted-theme">
                            <i class="bi bi-calendar3"></i> {{ $request->created_at->format('Y-m-d') }}
                        </div>
                    </div>
                    
                    <div class="card-user-info mb-4">
                        <div class="user-avatar-placeholder mb-3">
                            {{ mb_substr($request->name, 0, 1) }}
                        </div>
                        <h4 class="user-name text-truncate text-stats-main" title="{{ $request->name }}">{{ $request->name }}</h4>
                        <p class="user-phone font-outfit text-primary fw-bold">{{ $request->phone }}</p>
                    </div>

                    @if($request->area_of_interest)
                    <div class="interest-tag bg-stats-inner-item border-light-subtle text-muted-theme mb-4">
                        <i class="bi bi-bookmark-star me-2 text-warning"></i> {{ $request->area_of_interest }}
                    </div>
                    @endif

                    <div class="mt-4">
                        <button class="btn btn-details-glow w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#modal{{ $request->id }}">
                            <i class="bi bi-eye me-2"></i> عرض كامل التفاصيل
                        </button>
                    </div>
                </div>

                <div class="card-inner-bottom bg-stats-inner-item border-top border-light-subtle">
                    <div class="row g-2">
                        <div class="col-6">
                            @if($request->id_card_path)
                            <a href="{{ Storage::disk('public')->url($request->id_card_path) }}" target="_blank" class="btn btn-action-card id-card-btn w-100">
                                <i class="bi bi-person-badge"></i> الهوية
                            </a>
                            @else
                            <button disabled class="btn btn-action-card disabled-btn w-100 x-small text-muted-theme">لا توجد صورة</button>
                            @endif
                        </div>
                        <div class="col-6">
                            @if($request->cv_path)
                            <a href="{{ route('mobile.volunteer-requests.cv', $request->id) }}" target="_blank" class="btn btn-action-card cv-btn w-100">
                                <i class="bi bi-file-earmark-pdf"></i> السيرة
                            </a>
                            @else
                            <button disabled class="btn btn-action-card disabled-btn w-100 x-small text-muted-theme">لا توجد سيرة</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal{{ $request->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 24px; overflow: hidden;">
                    <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                        <h5 class="modal-title fw-bold text-stats-title">
                            <i class="bi bi-person-lines-fill me-2 text-primary"></i> تفاصيل مقدم طلب التطوع
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-stats-card-main">
                        <div class="row g-4">
                            {{-- Basic Info --}}
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">الإسم بالكامل</label>
                                <div class="text-stats-main fw-bold">{{ $request->name }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">رقم الهاتف</label>
                                <div class="font-outfit text-primary fw-bold">{{ $request->phone }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">البريد الإلكتروني</label>
                                <div class="text-truncate text-stats-main fw-bold">{{ $request->email ?? '-' }}</div>
                            </div>

                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">الرقم القومي</label>
                                <div class="font-outfit text-stats-main fw-bold">{{ $request->national_id ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">تاريخ الميلاد</label>
                                <div class="text-stats-main fw-bold">{{ $request->birth_date ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">النوع</label>
                                <div class="text-stats-main fw-bold">{{ $request->gender == 'male' ? 'ذكر' : ($request->gender == 'female' ? 'أنثى' : ($request->gender ?? '-')) }}</div>
                            </div>

                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">العنوان الأصلي</label>
                                <div class="text-stats-main fw-bold">{{ $request->address ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">العنوان الحالي</label>
                                <div class="text-stats-main fw-bold">{{ $request->current_address ?? '-' }}</div>
                            </div>

                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">المؤهل الدراسي</label>
                                <div class="text-stats-main fw-bold">{{ $request->education_level ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">الكلية</label>
                                <div class="text-stats-main fw-bold">{{ $request->faculty ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">الجامعة</label>
                                <div class="text-stats-main fw-bold">{{ $request->university ?? '-' }}</div>
                            </div>

                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">الوظيفة الحالية</label>
                                <div class="text-stats-main fw-bold">{{ $request->current_job ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">اهتمامات التطوع</label>
                                <div class="text-primary fw-bold">{{ $request->area_of_interest ?? '-' }}</div>
                            </div>

                            <div class="col-12 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">الهدف من الانضمام</label>
                                <div class="message-box bg-stats-inner-item border border-light-subtle rounded-3 p-3 text-muted-theme italic">
                                    "{{ $request->goal ?? '-' }}"
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel mt-5 p-4 rounded-4 bg-stats-inner-item border border-light-subtle">
                            <h6 class="mb-3 text-stats-main fw-bold border-start border-primary border-4 ps-3"><i class="bi bi-shield-lock me-2 text-primary"></i> لوحة الإدارة</h6>
                            <form action="{{ route('mobile.volunteer-requests.update', $request->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label x-small text-muted-theme fw-bold">تحديث الحالة</label>
                                        <select name="status" class="form-select bg-stats-card-main border-light-subtle text-stats-main rounded-3">
                                            <option value="new" {{ $request->status == 'new' ? 'selected' : '' }}>جديد (New)</option>
                                            <option value="contacted" {{ $request->status == 'contacted' ? 'selected' : '' }}>تم التواصل (Contacted)</option>
                                            <option value="accepted" {{ $request->status == 'accepted' ? 'selected' : '' }}>مقبول (Accepted)</option>
                                            <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-between gap-3">
                                        <button type="submit" class="btn btn-success flex-grow-1 rounded-pill fw-bold py-2 shadow-sm">حفظ التغييرات</button>
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold py-2" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب؟')) document.getElementById('del-form-{{ $request->id }}').submit()">حذف</button>
                                    </div>
                                </div>
                            </form>
                            <form id="del-form-{{ $request->id }}" action="{{ route('mobile.volunteer-requests.destroy', $request->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 animate-up">
            <div class="glass-card text-center py-5">
                <i class="bi bi-inbox display-4 text-white-50"></i>
                <h5 class="text-white mt-4">لا يوجد طلبات حالياً</h5>
                <p class="text-white-50">لم يقم أي مستخدم بإرسال طلبات تطوع عبر تطبيق الموبايل بعد.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    body { background-color: var(--ws-bg-page) !important; color: var(--ws-text-primary) !important; font-family: 'Tajawal', 'Outfit', sans-serif; }
    .bg-theme-page { background-color: var(--ws-bg-page); }
    .fw-800 { font-weight: 800; }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    /* Theme-Aware Stats Styling */
    .bg-stats-card-main { background-color: #ffffff; }
    .bg-stats-inner-item { background-color: var(--gray-50); }
    .text-stats-main { color: var(--dark); }
    .text-muted-theme { color: var(--gray-500); }
    .bg-stats-header { background-color: var(--gray-50); }
    .text-stats-title { color: var(--dark); }

    body.theme-dark .bg-stats-card-main { background-color: var(--bg-card); }
    body.theme-dark .bg-stats-inner-item { background-color: rgba(255, 255, 255, 0.03); }
    body.theme-dark .text-stats-main { color: #ffffff; }
    body.theme-dark .text-muted-theme { color: var(--gray-400); }
    body.theme-dark .bg-stats-header { background-color: rgba(255, 255, 255, 0.05); }
    body.theme-dark .text-stats-title { color: #ffffff; }

    /* Custom Elements */
    .glass-badge-theme { background: var(--bg-stats-header); border: 1px solid var(--ws-border); border-radius: 100px; color: var(--ws-text-primary); }
    
    .premium-volunteer-card { border-radius: 24px; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid var(--ws-border); background: var(--ws-bg-card); }
    .premium-volunteer-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: 0 20px 50px rgba(59, 130, 246, 0.15); }

    .card-inner-top { padding: 24px; flex-grow: 1; }
    .card-meta { display: flex; justify-content: space-between; align-items: center; }
    
    .badge-premium { padding: 6px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; border: 1px solid transparent; }
    .status-new { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); }
    .status-warn { background: rgba(245, 158, 11, 0.1); color: #d97706; border-color: rgba(245, 158, 11, 0.2); }
    .status-success { background: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.2); }
    .status-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border-color: rgba(239, 68, 68, 0.2); }

    .card-user-info { text-align: center; }
    .user-avatar-placeholder { width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary), #1e40af); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 1.8rem; font-weight: 800; color: #ffffff; box-shadow: 0 8px 16px rgba(59, 130, 246, 0.2); border: 3px solid #ffffff; }
    body.theme-dark .user-avatar-placeholder { border-color: var(--bg-card); }

    .interest-tag { border-radius: 12px; padding: 10px 15px; font-size: 0.85rem; text-align: center; }

    .btn-details-glow { background: var(--gray-100); color: var(--dark); border: 1px solid var(--ws-border); border-radius: 12px; padding: 12px; transition: 0.3s; }
    body.theme-dark .btn-details-glow { background: rgba(255,255,255,0.05); color: #ffffff; }
    .btn-details-glow:hover { background: var(--primary); border-color: var(--primary); color: #ffffff; box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); }

    .btn-action-card { border-radius: 12px; padding: 10px; font-weight: 700; font-size: 0.85rem; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s; color: #ffffff; }
    .id-card-btn { background: #3b82f6; }
    .id-card-btn:hover { background: #2563eb; transform: scale(1.03); color: #ffffff; }
    .cv-btn { background: #ef4444; }
    .cv-btn:hover { background: #dc2626; transform: scale(1.03); color: #ffffff; }

    /* Modal Perfection */
    .modal-glass-theme { background-color: var(--ws-bg-card) !important; }
    body.theme-dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

    /* Animations */
    .animate-reveal-down { animation: revealDown 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }
    @keyframes revealDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

    .x-small { font-size: 0.7rem; }
    .italic { font-style: italic; }
</style>
@endsection



