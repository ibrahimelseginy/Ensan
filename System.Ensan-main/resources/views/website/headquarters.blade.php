@extends('layouts.app')

@section('content')
<div class="settings-page">
    <div class="premium-hero-sleek mb-4 shadow-sm animate-reveal-right">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1 bg-info"></div>
            <div class="glow-orb-2 bg-primary"></div>
        </div>
        <div class="container-fluid hero-content-wrapper text-white text-center">
            <div class="badge-glass-premium mb-3 pulse-animation">
                <i class="bi bi-geo-alt-fill me-2"></i> إدارة المقر والفروع
            </div>
            <h1 class="display-4 fw-800 mb-2">تخصيص بيانات المقر</h1>
            <p class="lead opacity-75 max-w-600 mx-auto">إدارة بيانات العنوان، التغطية الجغرافية، وإحصائيات المقر.</p>
        </div>
    </div>



    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-8">
                <form action="{{ route('website.headquarters.update') }}" method="POST">
                    @csrf
                    <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                        <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-geo-alt me-2 text-info"></i> محتوى صفحة الفروع</h5>
                            <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm">حفظ التغييرات</button>
                        </div>
                        <div class="p-4 bg-slate-900">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-400">عنوان القسم (يظهر في الهيرو)</label>
                                    <input type="text" name="headquarters_title" class="form-control bg-dark text-white border-secondary" value="{{ $settings['headquarters_title'] ?? 'كفر الشيخ - المقر الرئيسي' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-400">وصف القسم</label>
                                    <textarea name="headquarters_description" class="form-control bg-dark text-white border-secondary" rows="3">{{ $settings['headquarters_description'] ?? 'المقر الإداري الرئيسي للمؤسسة، يختص بإدارة كافة الأنشطة والمشاريع الخيرية.' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- HQ Stats --}}
                    <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark" style="animation-delay: 0.1s;">
                        <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-graph-up me-2 text-success"></i> إحصائيات التغطية (تغطيتنا)</h5>
                            <button type="submit" class="btn btn-sm btn-success text-white rounded-pill px-4 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> حفظ الإحصائيات
                            </button>
                        </div>
                        <div class="p-4 bg-slate-900">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-slate-400">محافظات</label>
                                    <input type="text" name="headquarters_stats_governorates" class="form-control bg-dark text-white border-secondary mb-2" value="{{ $settings['headquarters_stats_governorates'] ?? '2' }}">
                                    <input type="text" name="headquarters_stats_governorates_label" class="form-control form-control-sm bg-slate-800 text-slate-300 border-secondary text-center" value="{{ $settings['headquarters_stats_governorates_label'] ?? 'محافظات' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-slate-400">موظفين</label>
                                    <input type="text" name="headquarters_stats_employees" class="form-control bg-dark text-white border-secondary mb-2" value="{{ $settings['headquarters_stats_employees'] ?? '+200' }}">
                                    <input type="text" name="headquarters_stats_employees_label" class="form-control form-control-sm bg-slate-800 text-slate-300 border-secondary text-center" value="{{ $settings['headquarters_stats_employees_label'] ?? 'موظف' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-slate-400">متبرعين</label>
                                    <input type="text" name="headquarters_stats_donors" class="form-control bg-dark text-white border-secondary mb-2" value="{{ $settings['headquarters_stats_donors'] ?? '+10K' }}">
                                    <input type="text" name="headquarters_stats_donors_label" class="form-control form-control-sm bg-slate-800 text-slate-300 border-secondary text-center" value="{{ $settings['headquarters_stats_donors_label'] ?? 'متبرع' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Branches Table --}}
                <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark" style="animation-delay: 0.2s;">
                    <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-geo-alt me-2 text-warning"></i> قائمة الفروع</h5>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addBranch">
                            <i class="bi bi-plus-lg me-1"></i> إضافة فرع
                        </button>
                    </div>
                    <div class="p-0 bg-slate-900">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0 border-secondary">
                                <thead>
                                    <tr class="border-secondary">
                                        <th class="ps-4 bg-transparent text-slate-400">اسم الفرع</th>
                                        <th class="bg-transparent text-slate-400">العنوان</th>
                                        <th class="text-center bg-transparent text-slate-400">الرئيسي</th>
                                        <th class="text-center bg-transparent text-slate-400">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody class="border-secondary">
                                    @foreach($branches as $branch)
                                    <tr class="border-secondary">
                                        <td class="ps-4">
                                            <div class="fw-bold text-white">{{ $branch->name }}</div>
                                            <div class="small text-slate-400">{{ $branch->phone }}</div>
                                            @if($branch->description)
                                                <div class="x-small text-slate-500 mt-1"><i class="bi bi-info-circle me-1"></i>{{ $branch->description }}</div>
                                            @endif
                                        </td>
                                        <td><span class="small text-slate-300">{{ \Illuminate\Support\Str::limit($branch->address, 50) }}</span></td>
                                        <td class="text-center">
                                            @if($branch->is_main)
                                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill">رئيسي</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill">فرعي</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editBranch{{ $branch->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('website.headquarters.branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفرع؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($branches->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-slate-400">
                                            <i class="bi bi-geo-alt fs-1 d-block mb-3 opacity-25"></i>
                                            لم يتم إضافة أي فروع بعد.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Add Branch Modal --}}
    <div class="modal fade" id="addBranch" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('website.headquarters.branches.store') }}" method="POST" class="modal-content glass-card border-0 stats-card-dark bg-slate-900">
                @csrf
                <div class="modal-header border-bottom border-white border-opacity-10 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-white">إضافة فرع جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">اسم الفرع</label>
                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required placeholder="مثلاً: فرع كفر الشيخ">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">حالة الفرع</label>
                            <input type="text" name="status_text" class="form-control bg-dark text-white border-secondary" placeholder="مثلاً: مفتوح الآن، نشط، ..." value="مفتوح الآن">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" style="direction: ltr; text-align: left;" placeholder="012xxxxxxx">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">العنوان التفصيلي</label>
                            <textarea name="address" class="form-control bg-dark text-white border-secondary" rows="2" required placeholder="اكتب العنوان هنا..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">وصف الفرع (مثلاً: فرع من فروع مؤسسة إنسان لخدمة المجتمع)</label>
                            <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="اكتب وصفاً مختصراً للفرع هنا..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">ساعات العمل</label>
                            <input type="text" name="working_hours" class="form-control bg-dark text-white border-secondary" placeholder="مثلاً: 9 ص - 10 م">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">رابط خرائط جوجل</label>
                            <input type="url" name="google_maps_url" class="form-control bg-dark text-white border-secondary" placeholder="https://goo.gl/maps/...">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch custom-switch">
                                <input class="form-check-input" type="checkbox" name="is_main" id="isMainSwitch">
                                <label class="form-check-label ms-2 text-white" for="isMainSwitch">تعيين كفرع رئيسي</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 p-4 pt-0">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">حفظ الفرع</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Branch Modals --}}
    @foreach($branches as $branch)
    <div class="modal fade" id="editBranch{{ $branch->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('website.headquarters.branches.update', $branch) }}" method="POST" class="modal-content glass-card border-0 stats-card-dark bg-slate-900">
                @csrf @method('PUT')
                <div class="modal-header border-bottom border-white border-opacity-10 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-white">تعديل الفرع: {{ $branch->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">اسم الفرع</label>
                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required value="{{ $branch->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">حالة الفرع</label>
                            <input type="text" name="status_text" class="form-control bg-dark text-white border-secondary" value="{{ $branch->status_text }}" placeholder="مثلاً: مفتوح الآن، نشط، ...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" style="direction: ltr; text-align: left;" value="{{ $branch->phone }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">العنوان التفصيلي</label>
                            <textarea name="address" class="form-control bg-dark text-white border-secondary" rows="2" required>{{ $branch->address }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">وصف الفرع</label>
                            <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="2">{{ $branch->description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">ساعات العمل</label>
                            <input type="text" name="working_hours" class="form-control bg-dark text-white border-secondary" value="{{ $branch->working_hours }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-slate-400">رابط خرائط جوجل</label>
                            <input type="url" name="google_maps_url" class="form-control bg-dark text-white border-secondary" value="{{ $branch->google_maps_url }}">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch custom-switch">
                                <input class="form-check-input" type="checkbox" name="is_main" id="isMainSwitch{{ $branch->id }}" {{ $branch->is_main ? 'checked' : '' }}>
                                <label class="form-check-label ms-2 text-white" for="isMainSwitch{{ $branch->id }}">تعيين كفرع رئيسي</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 p-4 pt-0">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 shadow-sm">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

<style>
    body { background-color: #0b0e14 !important; }
    .settings-page { min-height: 100vh; }
    
    .display-4 { font-size: 2.5rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    
    .premium-hero-sleek { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 30px; padding: 60px 0; position: relative; overflow: hidden; }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.15; }
    .glow-orb-1 { width: 300px; height: 300px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 250px; height: 250px; bottom: -50px; left: -50px; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 50px; display: inline-block; border: 1px solid rgba(255,255,255,0.1); font-size: 0.8rem; }
    .x-small { font-size: 0.7rem; }
    
    /* Stats & Cards */
    .glass-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; }
    .stats-card-dark { background-color: #0f172a !important; border: 1px solid rgba(255,255,255,0.1); }
    .bg-slate-900 { background-color: #0f172a !important; }
    .text-slate-400 { color: #94a3b8 !important; }
    .border-secondary { border-color: rgba(255,255,255,0.1) !important; }

    .animate-reveal-right { animation: revealRight 0.8s ease-out forwards; }
    .animate-reveal-left { animation: revealLeft 0.8s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.8s ease-out forwards; opacity: 0; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    
    .table thead th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border-top: 0; }
    
    /* Custom Dark Select */
    .custom-select-dark {
        background-color: #1e293b !important;
        color: #fff !important;
        border-color: #10b981 !important; /* Greenish border as requested */
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
    }
    .custom-select-dark:focus {
        background-color: #0f172a !important;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
    .custom-select-dark option {
        background-color: #0f172a !important;
        color: #fff;
    }

    .custom-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .custom-switch .form-check-input:checked { background-color: #10b981; border-color: #10b981; }

    /* Modals Overrides */
    .modal-content.stats-card-dark {
        background-color: #0f172a !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
    }
    .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
</style>
@endsection
