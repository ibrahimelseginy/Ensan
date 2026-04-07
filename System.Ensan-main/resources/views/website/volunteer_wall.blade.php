@extends('layouts.app')

@section('content')
<div class="volunteer-wall-page">
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container hero-content-wrapper text-center">
            <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">قادة العطاء</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-trophy-fill me-2"></i> تكريم للمتميزين في العطاء
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">جدار الشرف (قادة العطاء)</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                {{ $settings['volunteer_wall_description'] ?? 'نفخر بكل من ساهم بقلبه وماله في دعم المحتاجين. شكراً لكم على ثقتكم وعطائكم المستمر في بناء مجتمع متكافل.' }}
            </p>
            <div class="mt-4">
                <button class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addLeader">
                    <i class="bi bi-plus-circle-fill me-2"></i> إضافة قائد عطاء جديد
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        {{-- Success Partners Stats Management --}}
        <div class="row mb-5">
            <div class="col-12 px-lg-5">
                <div class="card p-4 border-0 shadow-sm animate-slide-up">
                    <form action="{{ route('website.settings.update') }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-handshake me-2 text-primary"></i> إدارة محتوى وإحصائيات شركاء النجاح</h5>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">حفظ التغييرات</button>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">وصف جدار الشرف (يظهر في الهيرو)</label>
                            <textarea name="volunteer_wall_description" class="form-control" rows="2">{{ $settings['volunteer_wall_description'] ?? 'نفخر بكل من ساهم بقلبه وماله في دعم المحتاجين. شكراً لكم على ثقتكم وعطائكم المستمر في بناء مجتمع متكافل.' }}</textarea>
                        </div>
                        <div class="row g-4">
                            @php
                                $stats = [
                                    ['label_key' => 'partners_stats_donors_label', 'val_key' => 'partners_stats_donors', 'def_label' => 'متبرع', 'def_val' => '+5000', 'icon' => 'bi-people-fill', 'color' => 'primary'],
                                    ['label_key' => 'partners_stats_volunteers_label', 'val_key' => 'partners_stats_volunteers', 'def_label' => 'متطوع', 'def_val' => '+1000', 'icon' => 'bi-person-heart', 'color' => 'success'],
                                    ['label_key' => 'partners_stats_institutions_label', 'val_key' => 'partners_stats_institutions', 'def_label' => 'مؤسسة شريكة', 'def_val' => '+10', 'icon' => 'bi-building', 'color' => 'info'],
                                    ['label_key' => 'partners_stats_campaigns_label', 'val_key' => 'partners_stats_campaigns', 'def_label' => 'حملة مكتملة', 'def_val' => '+100', 'icon' => 'bi-check-circle-fill', 'color' => 'warning'],
                                ];
                            @endphp
                            @foreach($stats as $stat)
                            <div class="col-md-6 col-lg-3 text-center">
                                <div class="p-3 rounded-4 bg-light border stats-box-minimal">
                                    <input type="text" name="{{ $stat['label_key'] }}" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings[$stat['label_key']] ?? $stat['def_label'] }}" placeholder="العنوان">
                                    <input type="text" name="{{ $stat['val_key'] }}" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0 text-dark" value="{{ $settings[$stat['val_key']] ?? $stat['def_val'] }}">
                                    <div class="mt-2 text-{{ $stat['color'] }} opacity-50"><i class="bi {{ $stat['icon'] }} fs-5"></i></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row px-lg-5">
            {{-- Top 3 Spotlight --}}
            <div class="col-12 mb-5">
                <div class="d-flex justify-content-center align-items-end gap-3 gap-md-4 flex-wrap">
                    @php $top3 = $leaders->whereIn('rank', [1, 2, 3])->sortBy('rank'); @endphp
                    
                    @foreach($top3 as $leader)
                        @if($leader->rank == 2)
                            <div class="rank-card rank-2 shadow-sm order-2 order-md-1">
                                <div class="rank-badge">2</div>
                                <div class="avatar-box">
                                    @if($leader->image_path)
                                        <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}">
                                    @else
                                        <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
                                    @endif
                                    <div class="medal gold-sub"><i class="bi bi-award-fill"></i></div>
                                </div>
                                <h5 class="fw-bold mt-3 mb-1 text-dark">{{ $leader->name }}</h5>
                                <div class="small text-muted mb-2">{{ $leader->role }}</div>
                                <span class="hours-badge">{{ $leader->hours }} ساعة</span>
                                <div class="mt-3 d-flex justify-content-center gap-1 action-btns">
                                    <button class="btn btn-sm btn-outline-light text-muted border" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}"><i class="bi bi-pencil-square"></i></button>
                                    <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-light text-danger border" onclick="return confirm('حذف؟')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endif
                        
                        @if($leader->rank == 1)
                            <div class="rank-card rank-1 shadow-md order-1 order-md-2">
                                <div class="crown"><i class="bi bi-crown-fill text-primary"></i></div>
                                <div class="rank-badge bg-primary">1</div>
                                <div class="avatar-box border-primary">
                                    @if($leader->image_path)
                                        <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}" class="border-primary">
                                    @else
                                        <div class="avatar-placeholder bg-primary bg-opacity-10 text-primary"><i class="bi bi-person-fill"></i></div>
                                    @endif
                                    <div class="medal bg-primary text-white"><i class="bi bi-award-fill"></i></div>
                                </div>
                                <h4 class="fw-bold mt-3 mb-1 text-primary">{{ $leader->name }}</h4>
                                <div class="small text-muted mb-2 fw-bold text-uppercase">{{ $leader->role }}</div>
                                <span class="hours-badge primary">{{ $leader->hours }} ساعة</span>
                                <div class="mt-3 d-flex justify-content-center gap-1 action-btns">
                                    <button class="btn btn-sm btn-outline-light text-muted border" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}"><i class="bi bi-pencil-square"></i></button>
                                    <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-light text-danger border" onclick="return confirm('حذف؟')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if($leader->rank == 3)
                            <div class="rank-card rank-3 shadow-sm order-3 order-md-3">
                                <div class="rank-badge">3</div>
                                <div class="avatar-box">
                                    @if($leader->image_path)
                                        <img src="{{ $leader->image_url }}" alt="{{ $leader->name }}">
                                    @else
                                        <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
                                    @endif
                                    <div class="medal bronze-sub"><i class="bi bi-award-fill"></i></div>
                                </div>
                                <h5 class="fw-bold mt-3 mb-1 text-dark">{{ $leader->name }}</h5>
                                <div class="small text-muted mb-2">{{ $leader->role }}</div>
                                <span class="hours-badge">{{ $leader->hours }} ساعة</span>
                                <div class="mt-3 d-flex justify-content-center gap-1 action-btns">
                                    <button class="btn btn-sm btn-outline-light text-muted border" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}"><i class="bi bi-pencil-square"></i></button>
                                    <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-light text-danger border" onclick="return confirm('حذف؟')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Table for others --}}
            <div class="col-12 px-lg-5">
                <div class="card border-0 shadow-sm overflow-hidden animate-slide-up">
                    <div class="p-3 bg-light border-bottom d-flex align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ol me-2 text-primary"></i> قائمة الأبطال الأخرى</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th class="ps-4 text-muted small fw-bold py-3" style="width: 100px;">الترتيب</th>
                                    <th class="text-muted small fw-bold py-3">المتطوع / القائد</th>
                                    <th class="text-muted small fw-bold py-3">الدور / الصفة</th>
                                    <th class="text-muted small fw-bold py-3">ساعات التطوع</th>
                                    <th class="text-end pe-4 text-muted small fw-bold py-3">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaders->whereNotIn('rank', [1, 2, 3])->sortBy('rank') as $leader)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <span class="badge bg-light text-dark rounded-pill px-3 py-2 border">{{ $leader->rank }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm">
                                                @if($leader->image_path)
                                                    <img src="{{ $leader->image_url }}" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm">
                                                @else
                                                    <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center w-100 h-100 border">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="fw-bold text-dark">{{ $leader->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="small fw-bold text-muted">{{ $leader->role }}</div>
                                    </td>
                                    <td class="py-3"><span class="text-primary fw-bold">{{ $leader->hours }} ساعة</span></td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-sm btn-outline-light text-muted border rounded-circle p-2" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}">
                                                <i class="bi bi-pencil" style="position: relative; top: -2px;"></i>
                                            </button>
                                            <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-light text-danger border rounded-circle p-2" style="width: 32px; height: 32px;" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    <i class="bi bi-trash" style="position: relative; top: -2px;"></i>
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
</div>

@foreach($leaders as $leader)
{{-- Edit Modal --}}
<div class="modal fade" id="editLeader{{ $leader->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.volunteer-wall.update', $leader) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            @csrf @method('PUT')
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark">تعديل بيانات البطل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">اسم المتطوع</label>
                    <input type="text" name="name" class="form-control" value="{{ $leader->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">الدور / الصفة</label>
                    <input type="text" name="role" class="form-control" value="{{ $leader->role }}" placeholder="مثلاً: متطوع متميز">
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">الترتيب الرقمي</label>
                        <input type="number" name="rank" class="form-control" value="{{ $leader->rank }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">عدد الساعات</label>
                        <input type="text" name="hours" class="form-control" value="{{ $leader->hours }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">الصورة الشخصية</label>
                    @if($leader->image_path)
                        <div class="mb-2">
                            <img src="{{ $leader->image_url }}" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="image" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
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
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark">إضافة قائد عطاء جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">اسم المتطوع</label>
                    <input type="text" name="name" class="form-control" required placeholder="مثلاً: محمد ع.">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">الدور / الصفة</label>
                    <input type="text" name="role" class="form-control" placeholder="مثلاً: متطوع متميز / داعم">
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">الترتيب الرقمي</label>
                        <input type="number" name="rank" class="form-control" required placeholder="1, 2, 3...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">عدد الساعات</label>
                        <input type="text" name="hours" class="form-control" required placeholder="350">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">الصورة الشخصية</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5">إضافة للجدار</button>
            </div>
        </form>
    </div>
</div>

<style>
    .volunteer-wall-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 80px 0 100px; 
        background: white !important; 
        border-bottom: 1px solid var(--border); 
        overflow: hidden; 
        z-index: 10; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }
    .badge-glass-premium { 
        background: var(--primary-light); 
        border: 1px solid rgba(34, 197, 94, 0.1); 
        padding: 8px 22px; 
        border-radius: 100px; 
        color: var(--primary); 
        font-weight: 700; 
        font-size: 0.85rem; 
        display: inline-block;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Rank Cards System */
    .rank-card {
        background: white;
        border-radius: 30px;
        padding: 40px 25px;
        text-align: center;
        position: relative;
        min-width: 220px;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border);
    }
    .rank-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-md); border-color: var(--primary); }
    .rank-card.rank-1 { border: 2px solid var(--primary); height: 340px; z-index: 3; background: #fff; }
    .rank-card.rank-2, .rank-card.rank-3 { height: 290px; }
    
    .rank-badge {
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--secondary);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border: 2px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .avatar-box {
        width: 110px;
        height: 110px;
        margin: 0 auto;
        position: relative;
        border: 3px solid var(--border);
        border-radius: 50%;
        padding: 3px;
    }
    .avatar-box img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .avatar-placeholder { width: 100%; height: 100%; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #cbd5e1; }
    
    .medal {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        font-size: 1.3rem;
        background: #fff;
    }
    .medal.gold-sub { color: #94a3b8; }
    .medal.bronze-sub { color: #d97706; }
    
    .crown {
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 2.8rem;
        filter: drop-shadow(0 4px 10px rgba(34,197,94,0.3));
    }
    
    .hours-badge {
        background: #f8fafc;
        color: #64748b;
        padding: 5px 18px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 0.85rem;
        border: 1px solid #e2e8f0;
    }
    .hours-badge.primary { background: var(--primary-light); color: var(--primary); border-color: rgba(34,197,94,0.2); }
    
    .avatar-sm { width: 45px; height: 45px; }
    .avatar-sm img { border: 1px solid var(--border); }

    .stats-box-minimal {
        transition: all 0.2s ease;
    }
    .stats-box-minimal:hover {
        background: #fff !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-color: var(--primary) !important;
    }
    
    .action-btns .btn {
        transition: all 0.2s ease;
    }
    .action-btns .btn:hover {
        background: var(--bg-soft) !important;
        border-color: var(--primary) !important;
        color: var(--primary) !important;
    }
</style>
@endsection
