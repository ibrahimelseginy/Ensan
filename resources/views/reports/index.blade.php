@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3" style="width: 42px; height: 42px;">
                    <i class="bi bi-bar-chart-fill fs-4"></i>
                </span>
                <span>مركز التقارير والإحصائيات الشاملة</span>
            </h4>
            <p class="text-muted mb-0 small">
                منظومة متكاملة لتقارير وإحصائيات كافة قطاعات مؤسسة إنسان الخيرية — استخراج جماعي أو منفصل
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'all']) }}" target="_blank" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-printer-fill"></i>
                <span class="fw-bold">طباعة التقرير الشامل الجماعي</span>
            </a>

            @if($tab !== 'all')
                <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => $tab]) }}" target="_blank" class="btn btn-outline-primary shadow-sm d-flex align-items-center gap-1">
                    <i class="bi bi-printer"></i>
                    <span>طباعة هذا القسم منفصلاً</span>
                </a>
            @endif

            <button type="button" onclick="EnsanPrintPreview.open('#printableReportsContainer', {title: 'معاينة التقارير والإحصائيات'})" class="btn btn-outline-info shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-eye"></i>
                <span>معاينة للطباعة</span>
            </button>

            <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-outline-success shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-excel"></i>
                <span>تصدير Excel (CSV)</span>
            </a>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('reports.index') }}" id="reportsFilterForm">
                <input type="hidden" name="tab" id="activeTabInput" value="{{ $tab }}">

                <div class="row g-2 align-items-center">
                    {{-- Quick Ranges --}}
                    <div class="col-12 col-xl-5">
                        <label class="form-label small fw-bold text-muted mb-1 d-block">الفترة الزمنية السريعة:</label>
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('reports.index', array_merge(request()->except(['range', 'from_date', 'to_date']), ['tab' => $tab, 'range' => 'all'])) }}" class="btn btn-sm {{ $range === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">الكل</a>
                            <a href="{{ route('reports.index', array_merge(request()->except(['range', 'from_date', 'to_date']), ['tab' => $tab, 'range' => 'today'])) }}" class="btn btn-sm {{ $range === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">اليوم</a>
                            <a href="{{ route('reports.index', array_merge(request()->except(['range', 'from_date', 'to_date']), ['tab' => $tab, 'range' => 'this_week'])) }}" class="btn btn-sm {{ $range === 'this_week' ? 'btn-primary' : 'btn-outline-secondary' }}">هذا الأسبوع</a>
                            <a href="{{ route('reports.index', array_merge(request()->except(['range', 'from_date', 'to_date']), ['tab' => $tab, 'range' => 'this_month'])) }}" class="btn btn-sm {{ $range === 'this_month' ? 'btn-primary' : 'btn-outline-secondary' }}">هذا الشهر</a>
                            <a href="{{ route('reports.index', array_merge(request()->except(['range', 'from_date', 'to_date']), ['tab' => $tab, 'range' => 'this_year'])) }}" class="btn btn-sm {{ $range === 'this_year' ? 'btn-primary' : 'btn-outline-secondary' }}">هذا العام</a>
                        </div>
                    </div>

                    {{-- Custom Date From --}}
                    <div class="col-6 col-sm-4 col-xl-2">
                        <label class="form-label small fw-bold text-muted mb-1">من تاريخ:</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $from ?? '' }}">
                    </div>

                    {{-- Custom Date To --}}
                    <div class="col-6 col-sm-4 col-xl-2">
                        <label class="form-label small fw-bold text-muted mb-1">إلى تاريخ:</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $to ?? '' }}">
                    </div>

                    {{-- Governorate Filter --}}
                    <div class="col-6 col-sm-4 col-xl-2">
                        <label class="form-label small fw-bold text-muted mb-1">الفرع / المحافظة:</label>
                        <select name="governorate" class="form-select form-select-sm">
                            <option value="all" {{ $governorate === 'all' ? 'selected' : '' }}>كافة الفروع والمحافظات</option>
                            <option value="كفر الشيخ" {{ $governorate === 'كفر الشيخ' ? 'selected' : '' }}>كفر الشيخ</option>
                            <option value="الغربية" {{ $governorate === 'الغربية' ? 'selected' : '' }}>الغربية (طنطا)</option>
                        </select>
                    </div>

                    {{-- Apply & Reset --}}
                    <div class="col-6 col-sm-12 col-xl-1 d-flex gap-1 align-items-end pt-sm-2 pt-xl-4">
                        <button type="submit" class="btn btn-sm btn-primary w-100" title="تطبيق الفلتر">
                            <i class="bi bi-filter"></i> تصفية
                        </button>
                        <a href="{{ route('reports.index', ['tab' => $tab]) }}" class="btn btn-sm btn-light border" title="إعادة الضبط">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .reports-tabs-card {
            background: #ffffff;
            border-radius: 14px;
        }
        .theme-dark .reports-tabs-card {
            background: #111827 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .reports-nav-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)) !important;
            gap: 0.65rem !important;
            padding: 0 !important;
            margin: 0 !important;
            list-style: none !important;
        }
        @media (max-width: 576px) {
            .reports-nav-grid {
                grid-template-columns: 1fr !important;
            }
        }
        .report-tab-btn {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 0.65rem !important;
            padding: 0.75rem 1rem !important;
            border-radius: 10px !important;
            font-size: 0.92rem !important;
            font-weight: 500 !important;
            color: #64748b !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            text-decoration: none !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            height: 100% !important;
        }
        .report-tab-btn i {
            font-size: 1.15rem;
            color: inherit;
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }
        .report-tab-btn:hover {
            color: #10b981 !important;
            background: #ecfdf5 !important;
            border-color: #a7f3d0 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
        }
        .report-tab-btn:hover i {
            transform: scale(1.15);
        }
        .report-tab-btn.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border-color: #059669 !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35) !important;
        }
        .report-tab-btn.active i {
            color: #ffffff !important;
        }
        .theme-dark .report-tab-btn {
            color: #94a3b8 !important;
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .theme-dark .report-tab-btn:hover {
            color: #34d399 !important;
            background: rgba(16, 185, 129, 0.15) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
        }
        .theme-dark .report-tab-btn.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border-color: #10b981 !important;
        }
    </style>

    {{-- Tabs Navigation - Responsive Grid Under Each Other --}}
    <div class="card border-0 shadow-sm mb-4 reports-tabs-card">
        <div class="card-body p-2.5">
            <ul class="reports-nav-grid" id="reportsNavTabs">
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'all' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>التقرير الشامل العام</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'donations' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'donations'])) }}">
                        <i class="bi bi-cash-coin"></i>
                        <span>التبرعات والمتبرعين</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'beneficiaries' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'beneficiaries'])) }}">
                        <i class="bi bi-people-fill"></i>
                        <span>المستفيدين والمساعدات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'guest_houses' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'guest_houses'])) }}">
                        <i class="bi bi-hospital-fill"></i>
                        <span>دور الضيافة والأسرة</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'inventory' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'inventory'])) }}">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>المخازن والمستودعات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'finance' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'finance'])) }}">
                        <i class="bi bi-wallet2"></i>
                        <span>المالية والمصروفات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'campaigns' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'campaigns'])) }}">
                        <i class="bi bi-megaphone-fill"></i>
                        <span>الحملات والمشاريع</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'volunteers' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'volunteers'])) }}">
                        <i class="bi bi-person-workspace"></i>
                        <span>المتطوعين والمهام</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link report-tab-btn {{ $tab === 'complaints' ? 'active' : '' }}" href="{{ route('reports.index', array_merge(request()->except('tab'), ['tab' => 'complaints'])) }}">
                        <i class="bi bi-chat-left-dots-fill"></i>
                        <span>الشكاوى والمقترحات</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Main Container for Print / View --}}
    <div id="printableReportsContainer">

        {{-- ========================================================================= --}}
        {{-- TAB: ALL (التقرير الجماعي العام) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'all')
            {{-- Top KPI Cards Grid --}}
            <div class="row g-3 mb-4">
                {{-- 1. Total Donations --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <div class="card-body p-3 text-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold opacity-75">إجمالي التبرعات المحصلة</span>
                                <span class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                    <i class="bi bi-cash-stack fs-5"></i>
                                </span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ number_format($donationsTotalAmount, 2) }} <small class="fs-6 opacity-75">ج.م</small></h3>
                            <div class="d-flex justify-content-between small opacity-90 pt-2 border-top border-white border-opacity-25">
                                <span>نقدي: {{ number_format($donationsCash, 0) }}</span>
                                <span>عيني: {{ number_format($donationsInKind, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Beneficiaries --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        <div class="card-body p-3 text-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold opacity-75">المستفيدين المخدومين</span>
                                <span class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                    <i class="bi bi-people-fill fs-5"></i>
                                </span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $beneficiariesTotal }} <small class="fs-6 opacity-75">مستفيد</small></h3>
                            <div class="d-flex justify-content-between small opacity-90 pt-2 border-top border-white border-opacity-25">
                                <span>كفالات نشطة: {{ number_format($totalSponsorshipAmount, 0) }} ج.م</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Guest Houses Occupancy --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
                        <div class="card-body p-3 text-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold opacity-75">إشغال دور الضيافة</span>
                                <span class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                    <i class="bi bi-hospital fs-5"></i>
                                </span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $overallOccupancyRate }}% <small class="fs-6 opacity-75">نسبة الإشغال</small></h3>
                            <div class="d-flex justify-content-between small opacity-90 pt-2 border-top border-white border-opacity-25">
                                <span>محجوز: {{ $occupiedBeds }} سرير</span>
                                <span>متاح: {{ $availableBeds }} سرير</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Financial Net Balance --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);">
                        <div class="card-body p-3 text-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold opacity-75">المصروفات والتدفق المالي</span>
                                <span class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                    <i class="bi bi-wallet2 fs-5"></i>
                                </span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ number_format($totalExpenses, 2) }} <small class="fs-6 opacity-75">ج.م</small></h3>
                            <div class="d-flex justify-content-between small opacity-90 pt-2 border-top border-white border-opacity-25">
                                <span>الصافي: {{ number_format($netFinancialResult, 0) }} ج.م</span>
                                <span>المخزون: {{ number_format($netInventoryQty, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Collective Sector Summary Cards --}}
            <div class="row g-4 mb-4">
                {{-- Sector 1: Donations & Donors Summary --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-3 pb-0 px-4">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-cash-stack text-success fs-5"></i> قطاع التبرعات والمتبرعين
                            </h6>
                            <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'donations'])) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                تقرير تفصيلي <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                        <div class="card-body px-4">
                            <div class="row g-2 text-center mb-3">
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">إجمالي المتبرعين</small>
                                    <strong class="fs-5 text-dark">{{ $donorsTotal }}</strong>
                                </div>
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">متبرعون دائمون</small>
                                    <strong class="fs-5 text-success">{{ $donorsRecurring }}</strong>
                                </div>
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">عدد العمليات</small>
                                    <strong class="fs-5 text-primary">{{ $donationsCount }}</strong>
                                </div>
                            </div>
                            <h6 class="small fw-bold text-muted mb-2">أبرز أغراض وتصنيفات التبرعات:</h6>
                            <div class="list-group list-group-flush small">
                                @forelse($donationsByPurpose->take(4) as $dp)
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span>{{ $dp->purpose ?: 'تبرع عام' }}</span>
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ number_format($dp->total, 2) }} ج.م</span>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-2">لا توجد تبرعات مسجلة في هذه الفترة</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sector 2: Beneficiaries & Aid Summary --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-3 pb-0 px-4">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-people text-primary fs-5"></i> قطاع المستفيدين والمساعدات
                            </h6>
                            <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'beneficiaries'])) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                تقرير تفصيلي <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                        <div class="card-body px-4">
                            <div class="row g-2 text-center mb-3">
                                <div class="col-6 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">إجمالي المسجلين</small>
                                    <strong class="fs-5 text-dark">{{ $beneficiariesTotal }}</strong>
                                </div>
                                <div class="col-6 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">مجموع الكفالات الشهرية</small>
                                    <strong class="fs-5 text-primary">{{ number_format($totalSponsorshipAmount, 0) }} ج.م</strong>
                                </div>
                            </div>
                            <h6 class="small fw-bold text-muted mb-2">توزيع المستفيدين حسب الحالة:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($beneficiariesByStatus as $bs)
                                    <div class="border rounded-2 px-3 py-2 text-center flex-grow-1">
                                        <span class="d-block small text-muted">{{ $bs->status ?: 'غير محدد' }}</span>
                                        <strong class="fw-bold text-dark">{{ $bs->count }}</strong>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-2 w-100">لا توجد حالات مسجلة</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sector 3: Guest Houses & Beds Summary --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-3 pb-0 px-4">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-hospital text-purple fs-5"></i> قطاع دور الضيافة وإيواء المرضى
                            </h6>
                            <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'guest_houses'])) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                تقرير تفصيلي <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                        <div class="card-body px-4">
                            <div class="row g-2 text-center mb-3">
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">عدد الدور</small>
                                    <strong class="fs-5 text-dark">{{ $totalGuestHouses }}</strong>
                                </div>
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">إجمالي الأجنحة</small>
                                    <strong class="fs-5 text-dark">{{ $totalWings }}</strong>
                                </div>
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">نسبة الإشغال</small>
                                    <strong class="fs-5 text-danger">{{ $overallOccupancyRate }}%</strong>
                                </div>
                            </div>
                            <div class="list-group list-group-flush small">
                                @foreach($guestHousesList as $gh)
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold text-dark">{{ $gh->name }}</span>
                                            <small class="text-muted ms-2">({{ $gh->governorate ?? 'غير محدد' }})</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary-subtle text-dark">{{ $gh->occupied_beds }} / {{ $gh->beds_count }} سرير</span>
                                            <span class="badge {{ $gh->occupancy_rate > 80 ? 'bg-danger' : 'bg-success' }}">{{ $gh->occupancy_rate }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sector 4: Inventory & Warehouses Summary --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-3 pb-0 px-4">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam text-warning fs-5"></i> قطاع المخازن والمستودعات
                            </h6>
                            <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'inventory'])) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                تقرير تفصيلي <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                        <div class="card-body px-4">
                            <div class="row g-2 text-center mb-3">
                                <div class="col-3 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">المستودعات</small>
                                    <strong class="fs-6 text-dark">{{ $warehousesCount }}</strong>
                                </div>
                                <div class="col-3 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">الأصناف</small>
                                    <strong class="fs-6 text-dark">{{ $itemsCount }}</strong>
                                </div>
                                <div class="col-3 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">صافي الرصيد</small>
                                    <strong class="fs-6 text-success">{{ number_format($netInventoryQty, 0) }}</strong>
                                </div>
                                <div class="col-3 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">أصناف منخفضة</small>
                                    <strong class="fs-6 text-danger">{{ $lowStockCount }}</strong>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded-2 bg-light border">
                                <span class="small text-muted">إجمالي الوارد التراكمي: <strong class="text-dark">{{ number_format($totalInQty, 1) }}</strong></span>
                                <span class="small text-muted">إجمالي المنصرف: <strong class="text-dark">{{ number_format($totalOutQty, 1) }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sector 5: Campaigns & Projects Summary --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-3 pb-0 px-4">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-megaphone text-info fs-5"></i> الحملات والمشاريع
                            </h6>
                            <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'campaigns'])) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                تقرير تفصيلي <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                        <div class="card-body px-4">
                            <div class="row g-2 text-center mb-3">
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">المشاريع</small>
                                    <strong class="fs-5 text-dark">{{ $projectsCount }}</strong>
                                </div>
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">الحملات</small>
                                    <strong class="fs-5 text-dark">{{ $campaignsCount }}</strong>
                                </div>
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">نسبة الإنجاز</small>
                                    <strong class="fs-5 text-info">{{ $overallCampaignProgress }}%</strong>
                                </div>
                            </div>
                            <div class="list-group list-group-flush small">
                                @forelse($campaignsList->take(3) as $c)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark">{{ $c->name }}</span>
                                            <span class="text-muted">{{ number_format($c->current_amount, 0) }} / {{ number_format($c->goal_amount, 0) }} ج.م</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-info" style="width: {{ min(100, $c->progress_percentage) }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-2">لا توجد حملات مسجلة</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sector 6: Volunteers & HR Summary --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-3 pb-0 px-4">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-person-workspace text-secondary fs-5"></i> المتطوعين والمهام
                            </h6>
                            <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'volunteers'])) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                تقرير تفصيلي <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                        <div class="card-body px-4">
                            <div class="row g-2 text-center mb-3">
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">إجمالي المتطوعين</small>
                                    <strong class="fs-5 text-dark">{{ $volunteersCount }}</strong>
                                </div>
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">ساعات التطوع</small>
                                    <strong class="fs-5 text-dark">{{ number_format($volunteerHoursTotal, 1) }}</strong>
                                </div>
                                <div class="col-4 p-2 bg-light rounded-3">
                                    <small class="text-muted d-block">المهام المنجزة</small>
                                    <strong class="fs-5 text-success">{{ $tasksCompleted }} / {{ $tasksTotal }}</strong>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded-2 bg-light border small">
                                <span>سجلات حضور المتطوعين: <strong>{{ $volunteerAttendanceCount }}</strong></span>
                                <span>الشكاوى المغلقة: <strong>{{ $complaintsClosed }} من {{ $complaintsTotal }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Collective KPI Master Table --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">جدول المؤشرات التنفيذية الشاملة لجميع القطاعات</h5>
                    <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'all']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-printer me-1"></i> طباعة الجدول الشامل
                    </a>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>المؤشر التنفيذي</th>
                                    <th class="text-end">القيمة الإحصائية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach($kpis as $title => $val)
                                    <tr>
                                        <td class="text-muted small">{{ $i++ }}</td>
                                        <td class="fw-bold">{{ $title }}</td>
                                        <td class="text-end fw-bold text-primary">{{ $val }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- ========================================================================= --}}
        {{-- TAB: DONATIONS & DONORS (تقرير التبرعات والمتبرعين المنفصل) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'donations')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-success d-flex align-items-center gap-2">
                            <i class="bi bi-cash-stack"></i> تقرير التبرعات والمتبرعين التفصيلي
                        </h5>
                        <p class="text-muted small mb-0">كشف شامل بالتبرعات النقدية والعينية وحركات المتبرعين</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'donations']) }}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-printer me-1"></i> طباعة تقرير التبرعات
                        </a>
                        <a href="{{ route('reports.export', request()->all() + ['tab' => 'donations']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-excel me-1"></i> تصدير إكسل
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    {{-- KPI Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي التبرعات المحصلة</small>
                                <h4 class="fw-bold text-success mb-0">{{ number_format($donationsTotalAmount, 2) }} ج.م</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">التبرعات النقدية</small>
                                <h4 class="fw-bold text-dark mb-0">{{ number_format($donationsCash, 2) }} ج.م</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">التبرعات العينية (التقديرية)</small>
                                <h4 class="fw-bold text-info mb-0">{{ number_format($donationsInKind, 2) }} ج.م</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">عدد عمليات التبرع</small>
                                <h4 class="fw-bold text-primary mb-0">{{ $donationsCount }}</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Data Table --}}
                    <h6 class="fw-bold text-dark mb-3">سجل عمليات التبرع الأخيرة:</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>رقم الإيصال</th>
                                    <th>المتبرع</th>
                                    <th>المبلغ / القيمة</th>
                                    <th>النوع</th>
                                    <th>الغرض / التصنيف</th>
                                    <th>طريقة الدفع</th>
                                    <th>الحملة / المشروع</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDonations as $d)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $d->receipt_number ?? ('#' . $d->id) }}</td>
                                        <td>
                                            <span class="fw-bold d-block">{{ $d->donor->name ?? 'فاعل خير' }}</span>
                                            <small class="text-muted">{{ $d->donor->phone ?? '-' }}</small>
                                        </td>
                                        <td class="fw-bold text-success">
                                            {{ number_format($d->type === 'cash' ? $d->amount : $d->estimated_value, 2) }} ج.م
                                        </td>
                                        <td>
                                            <span class="badge {{ $d->type === 'cash' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info' }}">
                                                {{ $d->type === 'cash' ? 'نقدي' : 'عيني' }}
                                            </span>
                                        </td>
                                        <td>{{ $d->purpose_label ?? $d->category ?? 'عام' }}</td>
                                        <td>{{ $d->payment_method_label ?? $d->payment_method ?? 'نقدي' }}</td>
                                        <td>{{ $d->campaign->name ?? $d->project->name ?? 'عام' }}</td>
                                        <td class="text-muted small">{{ $d->received_at ? $d->received_at->format('Y-m-d') : ($d->created_at ? $d->created_at->format('Y-m-d') : '-') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا توجد عمليات تبرع مسجلة في الفترة المحددة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- ========================================================================= --}}
        {{-- TAB: BENEFICIARIES (تقرير المستفيدين والمساعدات المنفصل) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'beneficiaries')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-primary d-flex align-items-center gap-2">
                            <i class="bi bi-people"></i> تقرير المستفيدين والحالات والمساعدات
                        </h5>
                        <p class="text-muted small mb-0">بيانات المستفيدين المخدومين وتوزيع المساعدات والكفالات</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'beneficiaries']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-printer me-1"></i> طباعة تقرير المستفيدين
                        </a>
                        <a href="{{ route('reports.export', request()->all() + ['tab' => 'beneficiaries']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-excel me-1"></i> تصدير إكسل
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    {{-- KPI Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي المستفيدين</small>
                                <h4 class="fw-bold text-primary mb-0">{{ $beneficiariesTotal }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي الكفالات الشهرية</small>
                                <h4 class="fw-bold text-success mb-0">{{ number_format($totalSponsorshipAmount, 0) }} ج.م</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">الحالات المعتمدة</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $beneficiariesByStatus->where('status', 'approved')->first()->count ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">الحالات قيد الدراسة</small>
                                <h4 class="fw-bold text-warning mb-0">{{ $beneficiariesByStatus->where('status', 'new')->first()->count ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Data Table --}}
                    <h6 class="fw-bold text-dark mb-3">كشف المستفيدين المسجلين:</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>كود المستفيد</th>
                                    <th>الاسم الكامل</th>
                                    <th>الرقم القومي</th>
                                    <th>الهاتف</th>
                                    <th>المحافظة / العنوان</th>
                                    <th>نوع المساعدة</th>
                                    <th>مبلغ الكفالة</th>
                                    <th>دار الضيافة</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBeneficiaries as $b)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $b->code ?? ('#' . $b->id) }}</td>
                                        <td class="fw-bold">{{ $b->full_name }}</td>
                                        <td class="text-muted">{{ $b->national_id ?? '-' }}</td>
                                        <td>{{ $b->phone ?? '-' }}</td>
                                        <td>{{ $b->address ?? '-' }}</td>
                                        <td><span class="badge bg-primary-subtle text-primary">{{ $b->assistance_type ?? 'عام' }}</span></td>
                                        <td class="fw-bold text-success">{{ number_format((float)($b->monthly_sponsorship_amount ?? 0), 0) }} ج.م</td>
                                        <td>{{ $b->guestHouse->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $b->status === 'approved' ? 'bg-success' : ($b->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                {{ $b->status ?? 'نشط' }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $b->created_at ? $b->created_at->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">لا يوجد مستفيدون مسجلون في الفترة المحددة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- ========================================================================= --}}
        {{-- TAB: GUEST HOUSES (تقرير دور الضيافة وإشغال الأسرة المنفصل) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'guest_houses')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-purple d-flex align-items-center gap-2">
                            <i class="bi bi-hospital"></i> تقرير دور الضيافة وإشغال الأسرة والنزلاء
                        </h5>
                        <p class="text-muted small mb-0">إحصاءات استيعاب وإشغال المرضى ومرافقيهم بدار كفر الشيخ ودار طنطا</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'guest_houses']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-printer me-1"></i> طباعة تقرير دور الضيافة
                        </a>
                        <a href="{{ route('reports.export', request()->all() + ['tab' => 'guest_houses']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-excel me-1"></i> تصدير إكسل
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    {{-- KPI Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي دور الضيافة</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $totalGuestHouses }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي الأسرة</small>
                                <h4 class="fw-bold text-primary mb-0">{{ $totalBeds }} سرير</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">الأسرة المشغولة</small>
                                <h4 class="fw-bold text-danger mb-0">{{ $occupiedBeds }} سرير</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">نسبة الإشغال الإجمالية</small>
                                <h4 class="fw-bold text-success mb-0">{{ $overallOccupancyRate }}%</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Guest Houses Comparison Table --}}
                    <h6 class="fw-bold text-dark mb-3">بيان دور الضيافة والأجنحة والطاقة الاستيعابية:</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم دار الضيافة</th>
                                    <th>المحافظة</th>
                                    <th>الأجنحة الداخلية</th>
                                    <th>إجمالي الأسرة</th>
                                    <th>الأسرة المشغولة</th>
                                    <th>الأسرة المتاحة</th>
                                    <th>نسبة الإشغال</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guestHousesList as $gh)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $gh->name }}</td>
                                        <td>{{ $gh->governorate ?? 'غير محدد' }}</td>
                                        <td><span class="badge bg-secondary-subtle text-dark">{{ $gh->wings_count }} أجنحة</span></td>
                                        <td class="fw-bold">{{ $gh->beds_count }}</td>
                                        <td class="fw-bold text-danger">{{ $gh->occupied_beds }}</td>
                                        <td class="fw-bold text-success">{{ $gh->available_beds }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar {{ $gh->occupancy_rate > 80 ? 'bg-danger' : 'bg-success' }}" style="width: {{ $gh->occupancy_rate }}%"></div>
                                                </div>
                                                <span class="small fw-bold">{{ $gh->occupancy_rate }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $gh->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $gh->status === 'active' ? 'نشط' : 'مؤرشف' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا توجد دور ضيافة مسجلة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Current Patients in Guest Houses --}}
                    <h6 class="fw-bold text-dark mb-3">النزلاء الحاليين بدور الضيافة:</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>كود المستفيد</th>
                                    <th>الاسم</th>
                                    <th>دار الضيافة</th>
                                    <th>الهاتف</th>
                                    <th>نوع الدعم</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentGuestHousePatients as $p)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $p->code ?? ('#' . $p->id) }}</td>
                                        <td class="fw-bold">{{ $p->full_name }}</td>
                                        <td><span class="badge bg-primary-subtle text-primary">{{ $p->guestHouse->name ?? '-' }}</span></td>
                                        <td>{{ $p->phone ?? '-' }}</td>
                                        <td>{{ $p->assistance_type ?? 'علاج وإيواء' }}</td>
                                        <td class="text-muted small">{{ $p->created_at ? $p->created_at->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">لا يوجد نزلاء مسجلين حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- ========================================================================= --}}
        {{-- TAB: INVENTORY (تقرير المخازن والمستودعات المنفصل) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'inventory')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-warning text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam"></i> تقرير المخازن وحركة المواد والأصناف
                        </h5>
                        <p class="text-muted small mb-0">بيانات الوارد والمنصرف والأرصدة المتبقية وتنبيهات النواقص</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'inventory']) }}" target="_blank" class="btn btn-sm btn-outline-warning text-dark">
                            <i class="bi bi-printer me-1"></i> طباعة تقرير المخزون
                        </a>
                        <a href="{{ route('reports.export', request()->all() + ['tab' => 'inventory']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-excel me-1"></i> تصدير إكسل
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    {{-- KPI Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">عدد المستودعات</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $warehousesCount }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي الأصناف المعرفة</small>
                                <h4 class="fw-bold text-primary mb-0">{{ $itemsCount }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">صافي الرصيد المخزني</small>
                                <h4 class="fw-bold text-success mb-0">{{ number_format($netInventoryQty, 1) }} وحدة</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">أصناف منخفضة أو سالبة</small>
                                <h4 class="fw-bold text-danger mb-0">{{ $lowStockCount }}</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Stock Ledger Table --}}
                    <h6 class="fw-bold text-dark mb-3">رصيد الأصناف وحركتها:</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>كود الصنف</th>
                                    <th>اسم الصنف</th>
                                    <th>الوحدة</th>
                                    <th>إجمالي الوارد</th>
                                    <th>إجمالي المنصرف</th>
                                    <th>الرصيد المتاح</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($itemsStockList as $item)
                                    <tr>
                                        <td class="fw-bold text-secondary">{{ $item->sku ?? ('#' . $item->id) }}</td>
                                        <td class="fw-bold">{{ $item->name }}</td>
                                        <td>{{ $item->unit ?? 'قطعة' }}</td>
                                        <td class="text-success fw-bold">{{ number_format((float)$item->total_in, 1) }}</td>
                                        <td class="text-danger fw-bold">{{ number_format((float)$item->total_out, 1) }}</td>
                                        <td class="fw-bold {{ $item->net_qty <= 0 ? 'text-danger' : 'text-primary' }}">
                                            {{ number_format((float)$item->net_qty, 1) }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->net_qty <= 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $item->net_qty <= 0 ? 'منخفض / نافذ' : 'متوفر' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">لا توجد أصناف معرفة بالمخازن</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- ========================================================================= --}}
        {{-- TAB: FINANCE (تقرير المالية والمصروفات المنفصل) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'finance')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-danger d-flex align-items-center gap-2">
                            <i class="bi bi-wallet2"></i> تقرير المالية والمصروفات والتدفقات
                        </h5>
                        <p class="text-muted small mb-0">بيانات الإيرادات والمصروفات وبنود الصرف والقيود المحاسبية</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'finance']) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-printer me-1"></i> طباعة تقرير المالية
                        </a>
                        <a href="{{ route('reports.export', request()->all() + ['tab' => 'finance']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-excel me-1"></i> تصدير إكسل
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    {{-- KPI Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي الإيرادات (التبرعات)</small>
                                <h4 class="fw-bold text-success mb-0">{{ number_format($donationsCash, 2) }} ج.م</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي المصروفات</small>
                                <h4 class="fw-bold text-danger mb-0">{{ number_format($totalExpenses, 2) }} ج.م</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">صافي الفائض / التدفق</small>
                                <h4 class="fw-bold {{ $netFinancialResult >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                    {{ number_format($netFinancialResult, 2) }} ج.م
                                </h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي القيود المحاسبية</small>
                                <h4 class="fw-bold text-primary mb-0">{{ $journalEntriesCount }}</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Expenses Table --}}
                    <h6 class="fw-bold text-dark mb-3">سجل أحدث المصروفات وسندات الصرف:</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>رقم السند</th>
                                    <th>البيان والتفاصيل</th>
                                    <th>نوع المصروف</th>
                                    <th>المبلغ</th>
                                    <th>المشروع / الحملة / الدار</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExpenses as $ex)
                                    <tr>
                                        <td class="fw-bold text-secondary">#{{ $ex->id }}</td>
                                        <td class="fw-bold">{{ $ex->description ?? 'مصروف تشغيلي' }}</td>
                                        <td><span class="badge bg-danger-subtle text-danger">{{ $ex->type ?? 'عام' }}</span></td>
                                        <td class="fw-bold text-danger">{{ number_format($ex->amount, 2) }} ج.م</td>
                                        <td>{{ $ex->guestHouse->name ?? $ex->campaign->name ?? $ex->project->name ?? 'المركز الرئيسي' }}</td>
                                        <td class="text-muted small">{{ $ex->paid_at ? $ex->paid_at->format('Y-m-d') : ($ex->created_at ? $ex->created_at->format('Y-m-d') : '-') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">لا توجد مصروفات مسجلة في الفترة المحددة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- ========================================================================= --}}
        {{-- TAB: CAMPAIGNS & PROJECTS (الحملات والمشاريع) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'campaigns')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-info d-flex align-items-center gap-2">
                            <i class="bi bi-megaphone"></i> تقرير الحملات الموسمية والمشاريع
                        </h5>
                        <p class="text-muted small mb-0">معدلات تحقيق المستهدفات وإنجاز الحملات والمشاريع الخيرية</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'campaigns']) }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-printer me-1"></i> طباعة تقرير الحملات
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم الحملة / المشروع</th>
                                    <th>الموسم</th>
                                    <th>المبلغ المستهدف</th>
                                    <th>المحصل الفعلي</th>
                                    <th>نسبة الإنجاز</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaignsList as $c)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $c->name }}</td>
                                        <td>{{ $c->season_title ?? '-' }}</td>
                                        <td class="fw-bold">{{ number_format($c->goal_amount, 2) }} ج.م</td>
                                        <td class="fw-bold text-success">{{ number_format($c->current_amount, 2) }} ج.م</td>
                                        <td style="min-width: 150px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-info" style="width: {{ min(100, $c->progress_percentage) }}%"></div>
                                                </div>
                                                <span class="small fw-bold">{{ $c->progress_percentage }}%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success">{{ $c->status ?? 'نشط' }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">لا توجد حملات مسجلة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- ========================================================================= --}}
        {{-- TAB: VOLUNTEERS (تقرير المتطوعين والمهام) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'volunteers')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-secondary d-flex align-items-center gap-2">
                            <i class="bi bi-person-workspace"></i> تقرير المتطوعين وساعات العمل والمهام
                        </h5>
                        <p class="text-muted small mb-0">إحصاءات المتطوعين والأنشطة المنفذة وسجلات الحضور</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'volunteers']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-printer me-1"></i> طباعة تقرير المتطوعين
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي المتطوعين المسجلين</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $volunteersCount }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي ساعات التطوع</small>
                                <h4 class="fw-bold text-primary mb-0">{{ number_format($volunteerHoursTotal, 1) }} ساعة</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">المهام المنجزة</small>
                                <h4 class="fw-bold text-success mb-0">{{ $tasksCompleted }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">المهام الجارية / الانتظار</small>
                                <h4 class="fw-bold text-warning mb-0">{{ $tasksInProgress + $tasksPending }}</h4>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3">سجل أحدث المهام والتكليفات:</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>عنوان المهمة</th>
                                    <th>المسند إليه</th>
                                    <th>المسند بواسطة</th>
                                    <th>الحملة / المشروع</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTasks as $task)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $task->title }}</td>
                                        <td>{{ $task->assignee->name ?? '-' }}</td>
                                        <td>{{ $task->assigner->name ?? '-' }}</td>
                                        <td>{{ $task->campaign->name ?? $task->project->name ?? '-' }}</td>
                                        <td>{{ $task->due_date ? $task->due_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            <span class="badge {{ $task->status === 'done' ? 'bg-success' : ($task->status === 'in_progress' ? 'bg-info' : 'bg-warning text-dark') }}">
                                                {{ $task->status === 'done' ? 'منجز' : ($task->status === 'in_progress' ? 'قيد التنفيذ' : 'انتظار') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">لا توجد مهام مسجلة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- ========================================================================= --}}
        {{-- TAB: COMPLAINTS (تقرير الشكاوى والمقترحات) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'complaints')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-danger d-flex align-items-center gap-2">
                            <i class="bi bi-chat-left-dots"></i> تقرير الشكاوى والمقترحات وخدمة المستفيدين
                        </h5>
                        <p class="text-muted small mb-0">معدلات الاستجابة وحل شكاوى المتبرعين والمستفيدين</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.print', request()->all() + ['mode' => 'print', 'tab' => 'complaints']) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-printer me-1"></i> طباعة تقرير الشكاوى
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">إجمالي الشكاوى الواردة</small>
                                <h4 class="fw-bold text-dark mb-0">{{ $complaintsTotal }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">شكاوى مفتوحة (جديدة)</small>
                                <h4 class="fw-bold text-danger mb-0">{{ $complaintsOpen }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">قيد المعالجة</small>
                                <h4 class="fw-bold text-warning mb-0">{{ $complaintsInProgress }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">تم حلها وإغلاقها</small>
                                <h4 class="fw-bold text-success mb-0">{{ $complaintsClosed }}</h4>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3">سجل الشكاوى والمقترحات الأخيرة:</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>كود المتابعة</th>
                                    <th>مقدم الشكوى</th>
                                    <th>رقم الهاتف</th>
                                    <th>موضوع الشكوى</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التقديم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentComplaints as $cmp)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $cmp->tracking_code ?? ('#' . $cmp->id) }}</td>
                                        <td class="fw-bold">{{ $cmp->complainant_name ?? 'غير محدد' }}</td>
                                        <td>{{ $cmp->complainant_phone ?? '-' }}</td>
                                        <td>{{ $cmp->subject }}</td>
                                        <td>
                                            <span class="badge {{ $cmp->status === 'closed' ? 'bg-success' : ($cmp->status === 'in_progress' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                {{ $cmp->status === 'closed' ? 'مغلقة ومحلولة' : ($cmp->status === 'in_progress' ? 'قيد المعالجة' : 'مفتوحة') }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $cmp->created_at ? $cmp->created_at->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">لا توجد شكاوى مسجلة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div> {{-- End printableReportsContainer --}}

</div>
@endsection
