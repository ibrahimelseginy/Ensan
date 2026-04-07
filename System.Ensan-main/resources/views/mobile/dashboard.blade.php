@extends('layouts.app')

@section('title', 'إدارة الموبايل - لوحة التحكم')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --bg-main: #0f172a;
        --bg-secondary: #0b1120;
        --card: #1e293b;
        --border: #334155;
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --primary: #22c55e;
        --primary-hover: #16a34a;
        --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
    }

    body {
        background-color: var(--bg-main) !important;
        color: var(--text-main);
        font-family: 'Tajawal', sans-serif;
    }

    /* Premium Dashboard Header */
    .dashboard-hero-premium {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 4rem 2rem;
        border-bottom: 1px solid var(--border);
        position: relative;
        overflow: hidden;
        border-radius: 0 0 50px 50px;
        margin-bottom: 3rem;
        box-shadow: var(--card-shadow);
    }

    .hero-visuals div {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.15;
    }

    .orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; background: var(--primary); }
    .orb-2 { width: 300px; height: 300px; bottom: -50px; left: -50px; background: #3b82f6; }

    .hero-content-p { position: relative; z-index: 10; }

    /* Quick Access Cards */
    .quick-card-p {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 28px;
        padding: 1.75rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        display: block;
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    }

    .quick-card-p:hover {
        transform: translateY(-10px) rotate(1deg);
        border-color: var(--primary);
        box-shadow: 0 20px 30px -10px rgba(34, 197, 94, 0.3);
    }

    .q-icon-box {
        width: 65px;
        height: 65px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1.25rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Table Units */
    .unit-container-p {
        background: var(--card);
        border-radius: 32px;
        border: 1px solid var(--border);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 3rem;
    }

    .unit-header-p {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border);
        background: rgba(255, 255, 255, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .unit-header-p h5 { margin: 0; font-weight: 800; color: var(--text-main); font-size: 1.25rem; }

    .table-p { margin: 0; }
    .table-p thead th {
        background: rgba(255, 255, 255, 0.01);
        padding: 1.25rem 1rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.8rem;
        border-bottom: 1px solid var(--border);
    }

    .table-p tbody td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: var(--text-main);
    }

    .table-p tr:hover td { background: rgba(255, 255, 255, 0.02); }

    /* Badges & Status */
    .status-badge-p {
        padding: 0.5rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .badge-success-p { background: rgba(34, 197, 94, 0.1); color: #4ade80; border-color: rgba(34, 197, 94, 0.2); }
    .badge-secondary-p { background: rgba(148, 163, 184, 0.1); color: #94a3b8; border-color: rgba(148, 163, 184, 0.2); }

    /* Modals */
    .modal-content-p {
        background-color: var(--bg-secondary) !important;
        border: 1px solid var(--border) !important;
        border-radius: 35px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
        overflow: hidden;
    }

    .modal-header-p {
        background: rgba(255, 255, 255, 0.02);
        border-bottom: 1px solid var(--border);
        padding: 2rem;
    }

    .form-control-p {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        color: white;
        transition: all 0.3s ease;
    }

    .form-control-p:focus {
        background: rgba(255, 255, 255, 0.04);
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
        color: white;
    }

    /* Buttons */
    .btn-primary-p {
        background: var(--primary);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 16px;
        font-weight: 800;
        transition: all 0.3s ease;
    }

    .btn-primary-p:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(34, 197, 94, 0.4);
    }

    /* Animation Classes */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>

<div class="mobile-dashboard-dark">
    {{-- Hero Section --}}
    <div class="dashboard-hero-premium">
        <div class="hero-visuals">
            <div class="orb-1"></div>
            <div class="orb-2"></div>
        </div>
        <div class="container-fluid hero-content-p text-end">
            <div class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-3 border border-success border-opacity-20 animate-up">
                 API UNIT - إدارة الموبايل ًں“±
            </div>
            <h1 class="display-4 fw-800 text-main mb-2 animate-up" style="animation-delay: 0.1s">المشاريع والحملات</h1>
            <p class="lead text-muted animate-up" style="animation-delay: 0.2s">تخصيص المحتوى وإدارة التفعيل للمشاريع والحملات التي تظهر لمستخدمي التطبيق الفعليين.</p>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        {{-- Quick Access Grid --}}
        <div class="row g-4 mb-5">
            {{-- Home Content --}}
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('mobile.home_content.index') }}" class="quick-card-p animate-up" style="animation-delay: 0.3s">
                    <div class="q-icon-box text-primary">
                        <i class="bi bi-layout-wysiwyg"></i>
                    </div>
                    <h5 class="fw-800 text-main mb-1">واجهة التطبيق</h5>
                    <p class="text-muted small mb-0">إدارة البانرات، المبادرات، وأقسام الصفحة الرئيسية.</p>
                </a>
            </div>

            {{-- Volunteer Requests --}}
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('mobile.volunteer-requests.index') }}" class="quick-card-p animate-up" style="animation-delay: 0.4s">
                    <div class="q-icon-box text-info">
                        <i class="bi bi-person-heart"></i>
                    </div>
                    <h5 class="fw-800 text-main mb-1">طلبات التطوع</h5>
                    <p class="text-muted small mb-0">مراجعة والرد على طلبات التطوع القادمة من الموبايل.</p>
                </a>
            </div>

            {{-- Case Applications --}}
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('mobile.case-applications.index') }}" class="quick-card-p animate-up" style="animation-delay: 0.5s">
                    <div class="q-icon-box text-warning">
                        <i class="bi bi-file-earmark-medical"></i>
                    </div>
                    <h5 class="fw-800 text-main mb-1">دراسة الحالات</h5>
                    <p class="text-muted small mb-0">إدارة طلبات المساعدات (مشاريع زاد والأمل).</p>
                </a>
            </div>

            {{-- Bookings --}}
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('mobile.bookings.index') }}" class="quick-card-p animate-up" style="animation-delay: 0.6s">
                    <div class="q-icon-box text-success">
                        <i class="bi bi-building-check"></i>
                    </div>
                    <h5 class="fw-800 text-main mb-1">حجوزات المقر</h5>
                    <p class="text-muted small mb-0">مراجعة طلبات السكن والإقامة الواردة من التطبيق.</p>
                </a>
            </div>
        </div>

        {{-- Main Projects Table --}}
        <div class="unit-container-p animate-up" style="animation-delay: 0.7s">
            <div class="unit-header-p">
                <h5><i class="bi bi-grid-3x3-gap me-2 text-success"></i> مشاريع تطبيق الموبايل</h5>
                <span class="badge rounded-pill bg-white bg-opacity-5 px-3 py-2 fw-bold text-muted border border-white border-opacity-10">{{ $projects->count() }} مشروع</span>
            </div>
            <div class="table-responsive">
                <table class="table table-p align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">المشروع</th>
                            <th class="text-center">الحالة على التطبيق</th>
                            <th class="text-center">محتوى مخصص</th>
                            <th class="text-center pe-4">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 border border-white border-opacity-10 overflow-hidden shadow-sm" style="width: 50px; height: 50px;">
                                        @if($project->image_path)
                                            <img src="{{ $project->image_url }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                        @endif
                                    </div>
                                    <span class="fw-700 text-main fs-6">{{ $project->name }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($project->show_on_mobile)
                                    <span class="status-badge-p badge-success-p">نشط</span>
                                @else
                                    <span class="status-badge-p badge-secondary-p">مخفي</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($project->mobile_content)
                                    <span class="text-success"><i class="bi bi-file-earmark-check-fill me-1"></i> مخصص</span>
                                @else
                                    <span class="text-muted small">افتراضي (نفس الويب)</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold border-2" data-bs-toggle="modal" data-bs-target="#editProjectMobile{{ $project->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> تخصيص
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Campaigns Table --}}
        <div class="unit-container-p animate-up" style="animation-delay: 0.8s">
            <div class="unit-header-p">
                <h5><i class="bi bi-megaphone me-2 text-warning"></i> حملات التبرع (Mobile Feed)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-p align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">الحملة</th>
                            <th>مؤشر الإنجاز</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center pe-4">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $campaign)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 border border-white border-opacity-10 overflow-hidden" style="width: 50px; height: 50px;">
                                        @if($campaign->image_path)
                                            <img src="{{ $campaign->image_url }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-700 text-main fs-6">{{ $campaign->name }}</div>
                                        <div class="text-muted small font-outfit">{{ $campaign->remaining_days ?? 0 }} DAYS REMAINING</div>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 30%">
                                <div class="d-flex justify-content-between mb-1 text-muted small fw-bold font-outfit">
                                    <span>{{ number_format($campaign->current_amount ?? 0) }} EGP</span>
                                    <span>{{ $campaign->goal_amount > 0 ? floor(($campaign->current_amount / $campaign->goal_amount) * 100) : 0 }}%</span>
                                </div>
                                <div class="progress rounded-pill bg-white bg-opacity-5" style="height: 6px;">
                                    <div class="progress-bar bg-warning rounded-pill" style="width: {{ $campaign->goal_amount > 0 ? min(100, ($campaign->current_amount / $campaign->goal_amount) * 100) : 0 }}%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($campaign->show_on_mobile)
                                    <span class="status-badge-p badge-success-p">نشط</span>
                                @else
                                    <span class="status-badge-p badge-secondary-p">مخفي</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold border-2" data-bs-toggle="modal" data-bs-target="#editCampMobile{{ $campaign->id }}">
                                    تخصيص الموبايل
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modals for Projects --}}
@foreach($projects as $project)
    <div class="modal fade" id="editProjectMobile{{ $project->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('mobile.projects.update.mobile', $project) }}" method="POST" class="modal-content modal-content-p">
                @csrf @method('PUT')
                <div class="modal-header modal-header-p">
                    <h5 class="modal-title fw-800 text-main">
                        <i class="bi bi-phone-fill text-success me-2"></i> تخصيص للموبايل: {{ $project->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-check form-switch mb-4 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10 d-flex justify-content-between align-items-center">
                        <div>
                            <label class="form-check-label fw-800 text-main d-block" for="showProj{{ $project->id }}">تفعيل العرض في التطبيق</label>
                            <span class="text-muted small">عند التفعيل سيظهر المشروع في تبويب "المشاريع" لمستخدمي التطبيق.</span>
                        </div>
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="show_on_mobile" id="showProj{{ $project->id }}" {{ $project->show_on_mobile ? 'checked' : '' }} value="1" style="width: 50px; height: 26px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-800 text-success mb-2">وصف مخصص لمستخدمي التطبيق</label>
                        <textarea name="mobile_content" class="form-control form-control-p" rows="8" placeholder="اكتب وصفاً جذاباً ومختصراً يناسب تصفح الهواتف...">{{ $project->mobile_content }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary-p w-100 shadow-lg">حفظ إعدادات الموبايل</button>
                    <button type="button" class="btn btn-link text-muted w-100 mt-2 text-decoration-none" data-bs-dismiss="modal">إلغاء وتراجع</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- Modals for Campaigns --}}
@foreach($campaigns as $campaign)
    <div class="modal fade" id="editCampMobile{{ $campaign->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('mobile.campaigns.update.mobile', $campaign) }}" method="POST" class="modal-content modal-content-p">
                @csrf @method('PUT')
                <div class="modal-header modal-header-p">
                    <h5 class="modal-title fw-800 text-main">
                        <i class="bi bi-megaphone-fill text-warning me-2"></i> تخصيص الحملة: {{ $campaign->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-check form-switch mb-4 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10 d-flex justify-content-between align-items-center">
                        <div>
                            <label class="form-check-label fw-800 text-main d-block" for="showCamp{{ $campaign->id }}">تفعيل الظهور في الموبايل</label>
                            <span class="text-muted small">هذا الخيار يتحكم في ظهور الحملة داخل شاشة الموبايل فقط.</span>
                        </div>
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="show_on_mobile" id="showCamp{{ $campaign->id }}" {{ $campaign->show_on_mobile ? 'checked' : '' }} value="1" style="width: 50px; height: 26px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-800 text-warning mb-2">النص الترويجي (الظاهر في التطبيق)</label>
                        <textarea name="mobile_content" class="form-control form-control-p" rows="8" placeholder="رسالة قصيرة ومؤثرة تظهر لمستخدمي التطبيق للتفاعل مع هذه الحملة...">{{ $campaign->mobile_content }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary-p w-100 shadow-lg" style="background-color: #eab308;">حفظ وتفعيل</button>
                    <button type="button" class="btn btn-link text-muted w-100 mt-2 text-decoration-none" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

@endsection
