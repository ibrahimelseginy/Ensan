@extends('layouts.app')
@section('content')
    <style>
        .gh-metric-card {
            background: var(--bg-card, #fff);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: transform 0.2s;
        }

        .gh-metric-card:hover {
            transform: translateY(-5px);
        }

        .gh-metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .gh-section-title {
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .gh-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .gh-list-item:last-child {
            border-bottom: none;
        }

        .theme-dark .gh-metric-card {
            background: var(--bg-card);
        }
    </style>

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-house-heart text-primary"></i>
                {{ $guest_house->name }}
            </h4>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted"><i class="bi bi-geo-alt me-1"></i>
                    {{ $guest_house->location ?? 'غير محدد' }}</span>
                <span class="text-muted">•</span>
                <span
                    class="badge {{ $guest_house->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $guest_house->status === 'active' ? 'نشط' : 'مؤرشف' }}</span>
            </div>
        </div>
        <div class="btn-group">
            <a href="{{ route('guest-houses.edit', $guest_house) }}" class="btn btn-outline-primary"><i
                    class="bi bi-pencil me-1"></i> تعديل</a>
            <a href="{{ route('guest-houses.index') }}" class="btn btn-outline-secondary"><i
                    class="bi bi-arrow-right me-1"></i> عودة</a>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="text-muted small">اجمالي التبرعات</div>
                <h3 class="fw-bold mb-0">{{ number_format($donationsTotal) }}</h3>
                <div class="small text-success mt-1">
                    <i class="bi bi-arrow-up"></i> {{ $donationsCount }} عملية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="text-muted small">اجمالي المصروفات</div>
                <h3 class="fw-bold mb-0">{{ number_format($expensesTotal) }}</h3>
                <div class="small text-danger mt-1">
                    <i class="bi bi-arrow-down"></i> {{ $expensesCount }} عملية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="text-muted small">صافي الحسابات</div>
                <h3 class="fw-bold mb-0">{{ number_format($netBalance) }}</h3>
                <div class="small text-muted mt-1">
                    الرصيد الحالي
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
                <div class="text-muted small">المستفيدون</div>
                <h3 class="fw-bold mb-0">{{ number_format($beneficiariesCount) }}</h3>
                <div class="small text-muted mt-1">
                    مستفيد مسجل
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Main Lists -->
        <div class="col-lg-8">

            <!-- Latest Donations -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span><i class="bi bi-heart text-danger me-2"></i> احدث التبرعات</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>المتبرع</th>
                                    <th>المبلغ/القيمة</th>
                                    <th>النوع</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestDonations as $d)
                                    <tr>
                                        <td>{{ $d->donor->name ?? 'فاعل خير' }}</td>
                                        <td class="fw-bold text-success">
                                            {{ number_format($d->type == 'cash' ? $d->amount : $d->estimated_value) }}
                                        </td>
                                        <td>
                                            @if($d->type == 'cash') <span
                                                class="badge bg-success bg-opacity-10 text-success">نقدي</span>
                                            @else <span class="badge bg-info bg-opacity-10 text-info">عيني</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ optional($d->created_at)->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">لا توجد تبرعات مسجلة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Latest Expenses -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span><i class="bi bi-receipt text-warning me-2"></i> اخر المصروفات</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>البند</th>
                                    <th>المبلغ</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestExpenses as $e)
                                    <tr>
                                        <td>{{ $e->description ?? 'بدون وصف' }}</td>
                                        <td class="fw-bold text-danger">{{ number_format($e->amount) }}</td>
                                        <td class="text-muted small">{{ optional($e->created_at)->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">لا توجد مصروفات مسجلة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Latest Beneficiaries -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span><i class="bi bi-person-check text-primary me-2"></i> المستفيدون الجدد</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>رقم الهاتف</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestBeneficiaries as $b)
                                    <tr>
                                        <td>{{ $b->name }}</td>
                                        <td>{{ $b->phone ?? '—' }}</td>
                                        <td><span class="badge bg-secondary">{{ $b->status ?? 'نشط' }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">لا يوجد مستفيدون مسجلون</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Manager, Stats, Volunteers -->
        <div class="col-lg-4">

            <!-- Manager Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="gh-section-title justify-content-center">مدير الدار</div>
                    @if($guest_house->manager)
                        <div class="mb-3">
                            @if($guest_house->manager_photo_url)
                                <img src="{{ $guest_house->manager_photo_url }}" class="rounded-circle mb-2"
                                    style="width:80px;height:80px;object-fit:cover">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2"
                                    style="width:80px;height:80px;font-size:2rem">
                                    {{ mb_substr($guest_house->manager->name, 0, 1) }}
                                </div>
                            @endif
                            <h5 class="fw-bold mb-0">{{ $guest_house->manager->name }}</h5>
                            <div class="text-muted small">{{ $guest_house->manager->email }}</div>
                        </div>
                        @if(request()->user()?->hasPermission('guest_houses.set_manager'))
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                data-bs-target="#managerModal">تغيير المدير</button>
                        @endif
                    @else
                        <div class="text-muted mb-3">لم يتم تعيين مدير بعد</div>
                        @if(request()->user()?->hasPermission('guest_houses.set_manager'))
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#managerModal">تعيين
                                مدير</button>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Donation Details (Chart) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">تفصيل التبرعات</div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>نقدي ({{ $cashPct }}%)</span>
                                <span>{{ number_format($cashSum) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $cashPct }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>عيني ({{ 100 - $cashPct }}%)</span>
                                <span>{{ number_format($inKindSum) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ 100 - $cashPct }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Volunteers -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span>متطوعو الشهر</span>
                        @if(request()->user()?->hasPermission('guest_houses.manage_monthly_volunteers'))
                            <button class="btn btn-sm btn-primary rounded-circle" data-bs-toggle="modal"
                                data-bs-target="#monthlyVolunteerModal"><i class="bi bi-plus"></i></button>
                        @endif
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($monthlyVolunteers as $mv)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $mv->user->name }}</div>
                                    <div class="small text-muted">{{ $mv->month }}/{{ $mv->year }}</div>
                                </div>
                                @if(request()->user()?->hasPermission('guest_houses.manage_monthly_volunteers'))
                                    <form action="{{ route('guest-houses.destroyMonthlyVolunteer', [$guest_house, $mv]) }}"
                                        method="POST" onsubmit="return confirm('حذف؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted small py-2">لا يوجد متطوعين لهذا الشهر</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Guest House Volunteers -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span>متطوعو الدار</span>
                        @if(request()->user()?->hasPermission('guest_houses.manage_volunteers'))
                            <button class="btn btn-sm btn-primary rounded-circle" data-bs-toggle="modal"
                                data-bs-target="#volunteerModal"><i class="bi bi-plus"></i></button>
                        @endif
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($guestHouseVolunteers as $v)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $v->name }}</div>
                                    <div class="small text-muted">{{ $v->pivot->role ?? 'متطوع' }}</div>
                                </div>
                                @if(request()->user()?->hasPermission('guest_houses.manage_volunteers'))
                                    <form action="{{ route('guest-houses.detachVolunteer', [$guest_house, $v]) }}" method="POST"
                                        onsubmit="return confirm('إزالة؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted small py-2">لا يوجد متطوعين مسجلين</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Manager Modal -->
    <div class="modal fade" id="managerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('guest-houses.setManager', $guest_house) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">تعيين مدير الدار</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المدير</label>
                        <select name="manager_user_id" class="form-select">
                            <option value="">اختر مستخدم...</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" @selected($guest_house->manager_user_id == $u->id)>{{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">صورة المدير (اختياري)</label>
                        <input type="file" name="manager_photo" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Volunteer Modal -->
    <div class="modal fade" id="volunteerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('guest-houses.attachVolunteer', $guest_house) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة متطوع للدار</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المتطوع</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">اختر متطوع...</option>
                            @foreach($volunteers as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الدور</label>
                        <input type="text" name="role" class="form-control" placeholder="مثال: مشرف، مساعد...">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">ساعات</label>
                            <input type="number" step="0.5" name="hours" class="form-control" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">تاريخ البدء</label>
                            <input type="date" name="started_at" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Volunteer Modal -->
    <div class="modal fade" id="monthlyVolunteerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('guest-houses.storeMonthlyVolunteer', $guest_house) }}"
                method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة متطوع للشهر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المتطوع</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">اختر متطوع...</option>
                            @foreach($volunteers as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">الشهر</label>
                            <select name="month" class="form-select">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected($m == date('n'))>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">السنة</label>
                            <select name="year" class="form-select">
                                @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" @selected($y == date('Y'))>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

@endsection