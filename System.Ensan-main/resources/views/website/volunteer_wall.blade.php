@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="volunteer-wall-page">
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #334155;"></div>
            <div class="glow-orb-2" style="background: #f59e0b;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">قادة العطاء</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-trophy-fill me-2"></i> تكريم للمتميزين في العطاء
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">جدار الشرف (قادة العطاء)</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        {{ $settings['volunteer_wall_description'] ?? 'نفخر بكل من ساهم بقلبه وماله في دعم المحتاجين. شكراً لكم على ثقتكم وعطائكم المستمر في بناء مجتمع متكافل.' }}
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left">
                    <button class="btn btn-action-glow rounded-pill px-5 py-3 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#addLeader">
                        <i class="bi bi-plus-circle-fill me-2"></i> إضافة قائد عطاء جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

<div class="container-fluid py-4">
    {{-- Success Partners Stats Management --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 border-0 shadow-sm animate-slide-up">
                <form action="{{ route('website.settings.update') }}" method="POST">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-handshake me-2 text-primary"></i> إدارة محتوى وإحصائيات شركاء النجاح</h5>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">حفظ التغييرات</button>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">وصف جدار الشرف (يظهر في الهيرو)</label>
                        <textarea name="volunteer_wall_description" class="form-control" rows="2">{{ $settings['volunteer_wall_description'] ?? 'نفخر بكل من ساهم بقلبه وماله في دعم المحتاجين. شكراً لكم على ثقتكم وعطائكم المستمر في بناء مجتمع متكافل.' }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded-4 bg-light border text-center statistics-box">
                                <input type="text" name="partners_stats_donors_label" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings['partners_stats_donors_label'] ?? 'متبرع' }}" placeholder="العنوان">
                                <input type="text" name="partners_stats_donors" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" value="{{ $settings['partners_stats_donors'] ?? '+5000' }}">
                                <i class="bi bi-people-fill text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded-4 bg-light border text-center statistics-box">
                                <input type="text" name="partners_stats_volunteers_label" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings['partners_stats_volunteers_label'] ?? 'متطوع' }}" placeholder="العنوان">
                                <input type="text" name="partners_stats_volunteers" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" value="{{ $settings['partners_stats_volunteers'] ?? '+1000' }}">
                                <i class="bi bi-person-heart text-success"></i>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded-4 bg-light border text-center statistics-box">
                                <input type="text" name="partners_stats_institutions_label" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings['partners_stats_institutions_label'] ?? 'مؤسسة شريكة' }}" placeholder="العنوان">
                                <input type="text" name="partners_stats_institutions" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" value="{{ $settings['partners_stats_institutions'] ?? '+10' }}">
                                <i class="bi bi-building text-info"></i>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded-4 bg-light border text-center statistics-box">
                                <input type="text" name="partners_stats_campaigns_label" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings['partners_stats_campaigns_label'] ?? 'حملة مكتملة' }}" placeholder="العنوان">
                                <input type="text" name="partners_stats_campaigns" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" value="{{ $settings['partners_stats_campaigns'] ?? '+100' }}">
                                <i class="bi bi-check-circle-fill text-warning"></i>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        {{-- Top 3 Spotlight --}}
        <div class="col-12 mb-5">
            <div class="d-flex justify-content-center align-items-end gap-3 gap-md-5 flex-wrap">
                @php $top3 = $leaders->whereIn('rank', [1, 2, 3])->sortBy('rank'); @endphp
                
                @foreach($top3 as $leader)
                    @if($leader->rank == 2)
                        <div class="rank-card rank-2 animate-up shadow-lg">
                            <div class="rank-badge">2</div>
                            <div class="avatar-box">
                                @if($leader->image_path)
                                    <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}">
                                @else
                                    <i class="bi bi-person-fill"></i>
                                @endif
                                <div class="medal"><i class="bi bi-award-fill text-secondary"></i></div>
                            </div>
                            <h5 class="fw-bold mt-3 mb-1">{{ $leader->name }}</h5>
                            <div class="small text-muted mb-2">{{ $leader->role }}</div>
                            <span class="hours-badge">{{ $leader->hours }} ساعة</span>
                            <div class="mt-3 d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-outline-primary border-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}"><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('حذف؟')"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endif
                    
                    @if($leader->rank == 1)
                        <div class="rank-card rank-1 animate-up shadow-lg">
                            <div class="crown"><i class="bi bi-crown-fill"></i></div>
                            <div class="rank-badge">1</div>
                            <div class="avatar-box">
                                @if($leader->image_path)
                                    <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}">
                                @else
                                    <i class="bi bi-person-fill"></i>
                                @endif
                                <div class="medal"><i class="bi bi-award-fill text-warning"></i></div>
                            </div>
                            <h4 class="fw-bold mt-3 mb-1 text-primary">{{ $leader->name }}</h4>
                            <div class="small text-muted mb-2 fw-bold text-uppercase">{{ $leader->role }}</div>
                            <span class="hours-badge primary">{{ $leader->hours }} ساعة</span>
                            <div class="mt-3 d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-outline-primary border-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}"><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('حذف؟')"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if($leader->rank == 3)
                        <div class="rank-card rank-3 animate-up shadow-lg">
                            <div class="rank-badge">3</div>
                            <div class="avatar-box">
                                @if($leader->image_path)
                                    <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}">
                                @else
                                    <i class="bi bi-person-fill"></i>
                                @endif
                                <div class="medal"><i class="bi bi-award-fill text-danger"></i></div>
                            </div>
                            <h5 class="fw-bold mt-3 mb-1">{{ $leader->name }}</h5>
                            <div class="small text-muted mb-2">{{ $leader->role }}</div>
                            <span class="hours-badge">{{ $leader->hours }} ساعة</span>
                            <div class="mt-3 d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-outline-primary border-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}"><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('حذف؟')"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Table for others --}}
        <div class="col-12">
            <div class="glass-card border-0 shadow-sm overflow-hidden animate-slide-up">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 100px;">الترتيب</th>
                                <th>المتطوع / القائد</th>
                                <th>الدور / الصفة</th>
                                <th>ساعات التطوع</th>
                                <th class="text-end pe-4">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaders->whereNotIn('rank', [1, 2, 3])->sortBy('rank') as $leader)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark rounded-pill px-3">{{ $leader->rank }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm">
                                            @if($leader->image_path)
                                                <img src="{{ $leader->image_url }}" class="rounded-circle">
                                            @else
                                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center w-100 h-100">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="fw-bold">{{ $leader->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-muted">{{ $leader->role }}</div>
                                </td>
                                <td><span class="text-success fw-bold">{{ $leader->hours }} ساعة</span></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($leaders as $leader)
{{-- Edit Modal --}}
<div class="modal fade" id="editLeader{{ $leader->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.volunteer-wall.update', $leader) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            @csrf @method('PUT')
            <div class="modal-header border-0 p-4">
                <h5 class="fw-bold mb-0">تعديل بيانات البطل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">اسم المتطوع</label>
                    <input type="text" name="name" class="form-control rounded-3" value="{{ $leader->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الدور / الصفة</label>
                    <input type="text" name="role" class="form-control rounded-3" value="{{ $leader->role }}" placeholder="مثلاً: متطوع متميز">
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">الترتيب الرقمي</label>
                        <input type="number" name="rank" class="form-control rounded-3" value="{{ $leader->rank }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">عدد الساعات</label>
                        <input type="text" name="hours" class="form-control rounded-3" value="{{ $leader->hours }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الصورة الشخصية</label>
                    @if($leader->image_path)
                        <div class="mb-2">
                            <img src="{{ $leader->image_url }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="image" class="form-control rounded-3">
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Add Modal --}}
<div class="modal fade" id="addLeader" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.volunteer-wall.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            @csrf
            <div class="modal-header border-0 p-4">
                <h5 class="fw-bold mb-0">إضافة قائد عطاء جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">اسم المتطوع</label>
                    <input type="text" name="name" class="form-control rounded-3" required placeholder="مثلاً: محمد ع.">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الدور / الصفة</label>
                    <input type="text" name="role" class="form-control rounded-3" placeholder="مثلاً: متطوع متميز / داعم">
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">الترتيب الرقمي</label>
                        <input type="number" name="rank" class="form-control rounded-3" required placeholder="1, 2, 3...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">عدد الساعات</label>
                        <input type="text" name="hours" class="form-control rounded-3" required placeholder="350">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الصورة الشخصية</label>
                    <input type="file" name="image" class="form-control rounded-3">
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5">إضافة للجدار</button>
            </div>
        </form>
    </div>
</div>

<style>
    body { background-color: #0b0e14 !important; }
    .volunteer-wall-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #fde68a; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .btn-action-glow { background: #f59e0b; color: #1a1a2e; border: none; font-weight: 700; box-shadow: 0 0 20px rgba(245,158,11,0.4); transition: 0.4s; }
    .btn-action-glow:hover { background: #d97706; transform: translateY(-5px); box-shadow: 0 15px 30px rgba(245,158,11,0.6); color: #1a1a2e; }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @media (max-width: 991px) { .premium-hero-sleek { border-radius: 0 0 30px 30px; padding: 60px 0 80px; } .display-4 { font-size: 2.2rem; } }
    .rank-card {
        background: white;
        border-radius: 30px;
        padding: 40px 20px;
        text-align: center;
        position: relative;
        min-width: 220px;
        transition: 0.3s;
    }
    .rank-card:hover { transform: translateY(-10px); }
    .rank-card.rank-1 { border: 2px solid #fbbf24; height: 320px; z-index: 3; }
    .rank-card.rank-2 { border: 2px solid #94a3b8; height: 280px; }
    .rank-card.rank-3 { border: 2px solid #ea580c; height: 280px; }
    
    .rank-badge {
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border: 2px solid white;
    }
    
    .avatar-box {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        position: relative;
    }
    .avatar-box img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid #f1f5f9; }
    .avatar-box i { font-size: 3rem; color: #cbd5e1; }
    
    .medal {
        position: absolute;
        bottom: 0;
        right: 0;
        background: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        font-size: 1.2rem;
    }
    
    .crown {
        position: absolute;
        top: -25px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 2.5rem;
        color: #fbbf24;
        filter: drop-shadow(0 4px 10px rgba(251,191,36,0.5));
    }
    
    .hours-badge {
        background: #f1f5f9;
        color: #059669;
        padding: 4px 16px;
        border-radius: 100px;
        font-weight: bold;
        font-size: 0.85rem;
    }
    .hours-badge.primary { background: #ecfdf5; color: #059669; }
    
    .avatar-sm { width: 40px; height: 40px; }
    
    [data-bs-theme="dark"] .rank-card { background: #1e293b; }
    [data-bs-theme="dark"] .hours-badge { background: #334155; }
</style>
@endsection

