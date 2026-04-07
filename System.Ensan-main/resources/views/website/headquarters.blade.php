@extends('layouts.app')

@section('content')
<div class="settings-page">
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container hero-content-wrapper text-center">
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-geo-alt-fill me-2"></i> إدارة المقر والفروع
            </div>
            <h1 class="display-5 fw-800 mb-2 text-dark">تخصيص بيانات المقر</h1>
            <p class="lead text-muted max-w-600 mx-auto">إدارة بيانات العنوان، التغطية الجغرافية، وإحصائيات المقر.</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-8 mx-auto">
                {{-- Form for Titles and Stats --}}
                <form action="{{ route('website.headquarters.update') }}" method="POST">
                    @csrf
                    {{-- Main Content Section --}}
                    <div class="card mb-4 overflow-hidden shadow-sm animate-slide-up">
                        <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-geo-alt me-2 text-primary"></i> محتوى صفحة الفروع</h5>
                            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">حفظ التغييرات</button>
                        </div>
                        <div class="p-4 bg-white">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">عنوان القسم (يظهر في الهيرو)</label>
                                    <input type="text" name="headquarters_title" class="form-control" value="{{ $settings['headquarters_title'] ?? 'كفر الشيخ - المقر الرئيسي' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">وصف القسم</label>
                                    <textarea name="headquarters_description" class="form-control" rows="3">{{ $settings['headquarters_description'] ?? 'المقر الإداري الرئيسي للمؤسسة، يختص بإدارة كافة الأنشطة والمشاريع الخيرية.' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- HQ Stats --}}
                    <div class="card mb-4 overflow-hidden shadow-sm animate-slide-up" style="animation-delay: 0.1s; border-right: 4px solid var(--primary);">
                        <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-primary bg-opacity-5">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-graph-up me-2 text-primary"></i> إحصائيات التغطية (تغطيتنا)</h5>
                            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> حفظ الإحصائيات
                            </button>
                        </div>
                        <div class="p-4 bg-white">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="stats-box-premium p-3 rounded-4 text-center">
                                        <label class="form-label small fw-bold text-muted mb-1 d-block text-center">محافظات</label>
                                        <input type="text" name="headquarters_stats_governorates" class="form-control text-center fw-bold fs-4 border-0 bg-transparent mb-1" value="{{ $settings['headquarters_stats_governorates'] ?? '2' }}">
                                        <input type="text" name="headquarters_stats_governorates_label" class="form-control form-control-sm text-center text-muted border-0 bg-transparent" value="{{ $settings['headquarters_stats_governorates_label'] ?? 'محافظات' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-box-premium p-3 rounded-4 text-center">
                                        <label class="form-label small fw-bold text-muted mb-1 d-block text-center">موظفين</label>
                                        <input type="text" name="headquarters_stats_employees" class="form-control text-center fw-bold fs-4 border-0 bg-transparent mb-1" value="{{ $settings['headquarters_stats_employees'] ?? '+200' }}">
                                        <input type="text" name="headquarters_stats_employees_label" class="form-control form-control-sm text-center text-muted border-0 bg-transparent" value="{{ $settings['headquarters_stats_employees_label'] ?? 'موظف' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-box-premium p-3 rounded-4 text-center">
                                        <label class="form-label small fw-bold text-muted mb-1 d-block text-center">متبرعين</label>
                                        <input type="text" name="headquarters_stats_donors" class="form-control text-center fw-bold fs-4 border-0 bg-transparent mb-1" value="{{ $settings['headquarters_stats_donors'] ?? '+10K' }}">
                                        <input type="text" name="headquarters_stats_donors_label" class="form-control form-control-sm text-center text-muted border-0 bg-transparent" value="{{ $settings['headquarters_stats_donors_label'] ?? 'متبرع' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Branches Table --}}
                <div class="card mb-4 overflow-hidden shadow-sm animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-geo-alt me-2 text-primary"></i> قائمة الفروع</h5>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addBranch">
                            <i class="bi bi-plus-lg me-1"></i> إضافة فرع
                        </button>
                    </div>
                    <div class="p-0 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="ps-4 text-muted small fw-bold py-3">اسم الفرع</th>
                                        <th class="text-muted small fw-bold py-3">العنوان</th>
                                        <th class="text-center text-muted small fw-bold py-3">الحالة</th>
                                        <th class="text-center text-muted small fw-bold py-3">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($branches as $branch)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark">{{ $branch->name }}</div>
                                            <div class="small text-muted">{{ $branch->phone }}</div>
                                            @if($branch->description)
                                                <div class="x-small text-muted mt-1"><i class="bi bi-info-circle me-1"></i>{{ $branch->description }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3"><span class="small text-dark">{{ \Illuminate\Support\Str::limit($branch->address, 50) }}</span></td>
                                        <td class="text-center py-3">
                                            @if($branch->is_main)
                                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">رئيسي</span>
                                            @else
                                                <span class="badge bg-light text-muted px-3 py-2 rounded-pill border">فرعي</span>
                                            @endif
                                        </td>
                                        <td class="text-center py-3">
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
                                        <td colspan="4" class="text-center py-5 text-muted">
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
        <form action="{{ route('website.headquarters.branches.store') }}" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">إضافة فرع جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">اسم الفرع</label>
                        <input type="text" name="name" class="form-control" required placeholder="مثلاً: فرع كفر الشيخ">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">حالة الفرع</label>
                        <input type="text" name="status_text" class="form-control" placeholder="مثلاً: مفتوح الآن، نشط، ..." value="مفتوح الآن">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control" style="direction: ltr; text-align: left;" placeholder="012xxxxxxx">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">العنوان التفصيلي</label>
                        <textarea name="address" class="form-control" rows="2" required placeholder="اكتب العنوان هنا..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">وصف الفرع</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="اكتب وصفاً مختصراً للفرع هنا..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">ساعات العمل</label>
                        <input type="text" name="working_hours" class="form-control" placeholder="مثلاً: 9 ص - 10 م">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">رابط خرائط جوجل</label>
                        <input type="url" name="google_maps_url" class="form-control" placeholder="https://goo.gl/maps/...">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" name="is_main" id="isMainSwitch">
                            <label class="form-check-label ms-2 text-dark" for="isMainSwitch">تعيين كفرع رئيسي</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">حفظ الفرع</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Branch Modals --}}
@foreach($branches as $branch)
<div class="modal fade" id="editBranch{{ $branch->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('website.headquarters.branches.update', $branch) }}" method="POST" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">تعديل الفرع: {{ $branch->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">اسم الفرع</label>
                        <input type="text" name="name" class="form-control" required value="{{ $branch->name }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">حالة الفرع</label>
                        <input type="text" name="status_text" class="form-control" value="{{ $branch->status_text }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control" style="direction: ltr; text-align: left;" value="{{ $branch->phone }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">العنوان التفصيلي</label>
                        <textarea name="address" class="form-control" rows="2" required>{{ $branch->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">وصف الفرع</label>
                        <textarea name="description" class="form-control" rows="2">{{ $branch->description }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">ساعات العمل</label>
                        <input type="text" name="working_hours" class="form-control" value="{{ $branch->working_hours }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">رابط خرائط جوجل</label>
                        <input type="url" name="google_maps_url" class="form-control" value="{{ $branch->google_maps_url }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" name="is_main" id="isMainSwitch{{ $branch->id }}" {{ $branch->is_main ? 'checked' : '' }}>
                            <label class="form-check-label ms-2 text-dark" for="isMainSwitch{{ $branch->id }}">تعيين كفرع رئيسي</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">تحديث البيانات</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<style>
    .settings-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.7rem; }
    
    .premium-hero-sleek { 
        position: relative; 
        padding: 60px 0 80px; 
        background: white !important;
        border-bottom: 1px solid var(--border);
        overflow: hidden; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 300px; height: 300px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 250px; height: 250px; bottom: -50px; left: -50px; }
    .badge-glass-premium { 
        background: var(--primary-light); 
        padding: 8px 20px; 
        border-radius: 50px; 
        display: inline-block; 
        border: 1px solid rgba(34, 197, 94, 0.1); 
        color: var(--primary);
        font-size: 0.85rem; 
        font-weight: 600;
    }
    
    .stats-box-premium {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.3s ease;
    }
    .stats-box-premium:hover {
        border-color: var(--primary) !important;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .table thead th { border-bottom: 0; }
    .custom-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .custom-switch .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
</style>
@endsection
