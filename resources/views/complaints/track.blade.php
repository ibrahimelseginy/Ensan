<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>تتبع الشكوى | المنظومة المركزية - مؤسسة إنسان الخيرية</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap RTL & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --brand-emerald: #059669;
            --brand-emerald-dark: #047857;
            --brand-emerald-deep: #064e3b;
            --brand-emerald-light: #10b981;
            --brand-emerald-subtle: rgba(5, 150, 105, 0.12);
            --brand-emerald-glow: rgba(16, 185, 129, 0.25);
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
        }

        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background: #0b1322 radial-gradient(circle at 50% 0%, #13332b 0%, #0b1322 75%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        /* Ambient Glow Grid */
        .ambient-grid {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 480px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 40%, transparent 100%);
            pointer-events: none;
            z-index: 0;
        }

        /* Header Branding */
        .hero-section {
            position: relative;
            z-index: 1;
            padding: 3rem 1rem 2rem;
            text-align: center;
        }

        .brand-logo-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.05) 100%);
            border: 1px solid rgba(16, 185, 129, 0.4);
            box-shadow: 0 0 35px var(--brand-emerald-glow), inset 0 0 15px rgba(16, 185, 129, 0.2);
            margin-bottom: 1.25rem;
            backdrop-filter: blur(8px);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .brand-logo-pill:hover {
            transform: scale(1.08) rotate(-3deg);
        }

        .brand-logo-pill img {
            width: 46px;
            height: auto;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.4));
        }

        .hero-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 0.4rem;
        }

        .hero-subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
            max-width: 520px;
            margin: 0 auto 1.25rem;
            line-height: 1.6;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #34d399;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 999px;
            letter-spacing: 0.2px;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(17, 24, 39, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.09);
            box-shadow: 0 20px 45px -15px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        /* Search Bar */
        .search-box-wrap {
            background: rgba(15, 23, 42, 0.9);
            border: 2px solid rgba(51, 65, 85, 0.8);
            border-radius: 16px;
            padding: 6px 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
        }

        .search-box-wrap:focus-within {
            border-color: var(--brand-emerald-light);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2), 0 10px 25px -5px rgba(0,0,0,0.5);
            background: #0f172a;
        }

        .search-input {
            background: transparent !important;
            border: none !important;
            color: #ffffff !important;
            font-family: 'JetBrains Mono', 'IBM Plex Sans Arabic', monospace;
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: 2px;
            box-shadow: none !important;
            padding: 0.65rem 0.75rem;
            text-transform: uppercase;
        }

        .search-input::placeholder {
            color: #475569;
            letter-spacing: 1px;
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .paste-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            border-radius: 10px;
            padding: 0.5rem 0.85rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .paste-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .btn-track-action {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 700;
            padding: 0.7rem 1.6rem;
            border-radius: 12px;
            font-size: 0.95rem;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-track-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
            background: linear-gradient(135deg, #34d399 0%, #059669 100%) !important;
        }

        /* Stepper Visual Component */
        .stepper-timeline {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin: 2.25rem 0 2rem;
            padding: 0 10px;
        }

        .stepper-timeline::before {
            content: '';
            position: absolute;
            top: 22px;
            right: 36px;
            left: 36px;
            height: 3px;
            background: #1e293b;
            z-index: 1;
        }

        .stepper-bar-fill {
            position: absolute;
            top: 22px;
            right: 36px;
            height: 3px;
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.6);
            z-index: 2;
            transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stepper-node {
            position: relative;
            z-index: 3;
            text-align: center;
            flex: 1;
        }

        .stepper-circle {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #0f172a;
            border: 3px solid #334155;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 0.6rem;
            transition: all 0.35s ease;
        }

        .stepper-node.active .stepper-circle {
            border-color: #10b981;
            background: #064e3b;
            color: #34d399;
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.2), 0 0 20px rgba(16, 185, 129, 0.4);
            animation: pulse-glow 2s infinite;
        }

        .stepper-node.completed .stepper-circle {
            border-color: #10b981;
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 0 14px rgba(16, 185, 129, 0.35);
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.2), 0 0 20px rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0.1), 0 0 26px rgba(16, 185, 129, 0.6); }
        }

        .stepper-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 2px;
        }

        .stepper-node.active .stepper-title {
            color: #34d399;
        }

        .stepper-node.completed .stepper-title {
            color: #e2e8f0;
        }

        .stepper-time {
            font-size: 0.72rem;
            color: #94a3b8;
        }

        /* Tracking Code Highlight Box */
        .code-display-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 1rem 1.25rem;
        }

        .tracking-code-text {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            font-size: 1.45rem;
            color: #34d399;
            letter-spacing: 2px;
        }

        /* Resolution Card */
        .resolution-glow-card {
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.45) 0%, rgba(15, 23, 42, 0.8) 100%);
            border: 1px solid rgba(52, 211, 153, 0.35);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.12);
            position: relative;
            overflow: hidden;
        }

        .resolution-glow-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 4px;
            height: 100%;
            background: #10b981;
        }

        /* Metadata Pill */
        .meta-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .meta-label {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .meta-val {
            font-size: 0.92rem;
            font-weight: 700;
            color: #f1f5f9;
        }

        /* Pulsing Dot */
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-left: 6px;
        }
        .pulse-dot.warning { background: #f59e0b; box-shadow: 0 0 10px #f59e0b; }
        .pulse-dot.primary { background: #3b82f6; box-shadow: 0 0 10px #3b82f6; }
        .pulse-dot.success { background: #10b981; box-shadow: 0 0 10px #10b981; }

        /* Print Styling */
        @media print {
            body { background: #ffffff !important; color: #000000 !important; }
            .ambient-grid, .search-box-wrap, .btn-track-action, .footer-wrap, .no-print { display: none !important; }
            .glass-card { border: 1px solid #ccc !important; background: transparent !important; box-shadow: none !important; }
            .tracking-code-text { color: #000 !important; }
        }
    </style>
</head>
<body>

<div class="ambient-grid"></div>

<!-- Hero Branding Header -->
<header class="hero-section">
    <div class="container" style="max-width: 760px;">
        <div class="brand-logo-pill">
            @if(file_exists(public_path('images/heart-icon.png')))
                <img src="{{ asset('images/heart-icon.png') }}" alt="مؤسسة إنسان">
            @else
                <i class="bi bi-heart-fill text-success fs-1"></i>
            @endif
        </div>
        <h1 class="hero-title">مؤسسة إنسان الخيرية</h1>
        <p class="hero-subtitle">المنظومة المركزية لتتبع الشكاوى والمقترحات — متابعة فورية لحالة البلاغ والإجراءات المتخذة بأعلى معايير السرية والأمان.</p>
        <div>
            <span class="security-badge">
                <i class="bi bi-shield-check"></i> نظام مشفر ومؤمن بالكامل ضد التخمين
            </span>
        </div>
    </div>
</header>

<main class="container mb-5" style="max-width: 760px; position: relative; z-index: 2;">

    <!-- Search Box Card -->
    <div class="glass-card p-4 p-md-5 mb-4">
        <form method="POST" action="{{ route('complaint.track.search') }}" id="trackForm">
            @csrf
            <label for="trackingCodeInput" class="form-label d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-light fs-6">
                    <i class="bi bi-upc-scan text-success me-1"></i> أدخل كود التتبع المعتمد:
                </span>
                <span class="text-muted small">صيغة الكود: ENS-XXXXXX</span>
            </label>

            <div class="search-box-wrap">
                <span class="ps-2 text-success">
                    <i class="bi bi-hash fs-4"></i>
                </span>
                <input type="text" 
                       name="tracking_code" 
                       id="trackingCodeInput" 
                       class="form-control search-input" 
                       placeholder="مثال: ENS-AB1C2D"
                       value="{{ old('tracking_code', $code ?? request('code', '')) }}"
                       maxlength="25"
                       autocomplete="off"
                       autofocus
                       required>
                
                <button type="button" class="btn paste-btn d-none d-sm-inline-flex align-items-center gap-1" id="pasteBtn" title="لصق الكود من الحافظة">
                    <i class="bi bi-clipboard"></i> لصق
                </button>

                <button class="btn btn-track-action" type="submit" id="searchSubmitBtn">
                    <i class="bi bi-search"></i>
                    <span>تتبع الشكوى</span>
                </button>
            </div>

            @if(session('error') || (isset($error) && $error))
                <div class="text-warning small mt-2 ps-2 d-flex align-items-center gap-1">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('error') ?? $error }}</span>
                </div>
            @endif

            @error('tracking_code')
                <div class="text-danger small mt-2 ps-2 d-flex align-items-center gap-1">
                    <i class="bi bi-shield-x"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </form>
    </div>

    <!-- Results Section -->
    @isset($code)
        @if(!isset($complaint) || !$complaint)
            <!-- NOT FOUND STATE -->
            <div class="glass-card p-4 p-md-5 text-center mb-4 border border-warning border-opacity-25">
                <div class="mb-3">
                    <span class="d-inline-flex p-3 rounded-circle bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                        <i class="bi bi-search display-6"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-2">تعذر العثور على شكوى مسجلة بهذا الكود</h4>
                <p class="text-muted fs-6 mb-3" style="max-width: 500px; margin: 0 auto;">
                    لم نتمكن من العثور على أي بيانات مطابقة للكود <code class="text-warning fw-bold px-2 py-1 rounded bg-dark">{{ $code }}</code>. يرجى التأكد من كتابة الكود بشكل صحيح بدون مسافات إضافية.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('complaint.track') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i> إعادة البحث
                    </a>
                </div>
            </div>
        @else
            <!-- COMPLAINT FOUND STATE -->
            @php
                // Status definitions
                [$statusKey, $statusLabel, $statusBadgeClass, $statusDot, $progressWidth, $stepIdx] = match($complaint->status) {
                    'open'        => ['open',        'مفتوحة — قيد الفحص والمراجعة',      'text-warning bg-warning bg-opacity-10 border border-warning border-opacity-25', 'warning', '33%', 1],
                    'in_progress' => ['in_progress', 'جارية المتابعة واتخاذ الإجراء',    'text-info bg-info bg-opacity-10 border border-info border-opacity-25',          'primary', '66%', 2],
                    'closed'      => ['closed',      'مكتملة ومغلقة — تم الحل النهائي', 'text-success bg-success bg-opacity-10 border border-success border-opacity-25', 'success', '100%', 3],
                    default       => ['other',       $complaint->status,                 'text-secondary bg-secondary bg-opacity-10 border border-secondary',            'primary', '20%', 1],
                };
            @endphp

            <div class="glass-card p-4 p-md-5 mb-4 animate-fade-in">

                <!-- Header Info Bar -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 border-bottom border-secondary border-opacity-25 mb-4">
                    <div>
                        <div class="text-muted small fw-bold mb-1">كود التتبع المعتمد للشكوى:</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="tracking-code-text">{{ $complaint->tracking_code }}</span>
                            <button type="button" class="btn btn-sm btn-outline-light border-opacity-25 rounded-pill px-2.5 py-1" id="copyCodeBtn" onclick="copyTrackingCode('{{ $complaint->tracking_code }}')" title="نسخ الكود">
                                <i class="bi bi-copy me-1"></i> <span id="copyBtnText">نسخ الكود</span>
                            </button>
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="text-muted small fw-bold mb-1">حالة الشكوى الحالية:</div>
                        <span class="badge {{ $statusBadgeClass }} px-3 py-2 rounded-pill fs-6 fw-bold d-inline-flex align-items-center">
                            <span class="pulse-dot {{ $statusDot }}"></span>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <!-- 4-Step Interactive Stepper -->
                <div class="stepper-timeline">
                    <div class="stepper-bar-fill" style="width: {{ $progressWidth }};"></div>

                    <!-- Step 1: Received -->
                    <div class="stepper-node completed">
                        <div class="stepper-circle"><i class="bi bi-file-earmark-check-fill"></i></div>
                        <div class="stepper-title">1. استلام الشكوى</div>
                        <div class="stepper-time">{{ $complaint->created_at->format('d/m/Y') }}</div>
                    </div>

                    <!-- Step 2: Under Review -->
                    <div class="stepper-node {{ $stepIdx >= 1 ? ($stepIdx == 1 ? 'active' : 'completed') : '' }}">
                        <div class="stepper-circle"><i class="bi bi-search"></i></div>
                        <div class="stepper-title">2. الفحص والمراجعة</div>
                        <div class="stepper-time">{{ $stepIdx >= 1 ? 'مكتمل' : 'قيد الانتظار' }}</div>
                    </div>

                    <!-- Step 3: Action In Progress -->
                    <div class="stepper-node {{ $stepIdx >= 2 ? ($stepIdx == 2 ? 'active' : 'completed') : '' }}">
                        <div class="stepper-circle"><i class="bi bi-gear-wide-connected"></i></div>
                        <div class="stepper-title">3. اتخاذ الإجراء</div>
                        <div class="stepper-time">{{ $stepIdx >= 2 ? 'جاري المعالجة' : 'في الانتظار' }}</div>
                    </div>

                    <!-- Step 4: Resolution & Closure -->
                    <div class="stepper-node {{ $stepIdx == 3 ? 'completed' : '' }}">
                        <div class="stepper-circle"><i class="bi bi-check-circle-fill"></i></div>
                        <div class="stepper-title">4. الحل والإغلاق</div>
                        <div class="stepper-time">
                            {{ $complaint->resolved_at ? $complaint->resolved_at->format('d/m/Y') : 'بانتظار الإغلاق' }}
                        </div>
                    </div>
                </div>

                <!-- Complaint Subject -->
                <div class="code-display-box mb-4">
                    <div class="text-muted small fw-bold mb-1">
                        <i class="bi bi-card-text text-success me-1"></i> موضوع الشكوى / البلاغ
                    </div>
                    <div class="fs-5 fw-bold text-white">{{ $complaint->subject }}</div>
                </div>

                <!-- Metadata Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-4">
                        <div class="meta-card">
                            <div class="meta-label">تصنيف مقدم الشكوى</div>
                            <div class="meta-val">
                                <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 px-2.5 py-1 rounded-pill">
                                    {{ match($complaint->source_type) {
                                        'donor'       => 'متبرع كريم',
                                        'beneficiary' => 'مستفيد من الخدمات',
                                        'employee'    => 'كادر المؤسسة',
                                        default       => $complaint->source_type
                                    } }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-4">
                        <div class="meta-card">
                            <div class="meta-label">اسم صاحب الشكوى (محمي)</div>
                            <div class="meta-val text-truncate" title="بيانات الهوية محمية للخصوصية">
                                <i class="bi bi-shield-lock-fill text-success me-1"></i>
                                {{ $maskedName ?? $complaint->source_name }}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-4">
                        <div class="meta-card">
                            <div class="meta-label">رقم الهاتف للتواصل</div>
                            <div class="meta-val font-monospace">
                                @if(!empty($maskedPhone))
                                    <i class="bi bi-telephone text-muted me-1"></i> {{ $maskedPhone }}
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-6">
                        <div class="meta-card">
                            <div class="meta-label">تاريخ وتوقيت التسجيل</div>
                            <div class="meta-val">
                                <i class="bi bi-clock-history text-muted me-1"></i>
                                {{ $complaint->created_at->format('d/m/Y - h:i A') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-6">
                        <div class="meta-card">
                            <div class="meta-label">تاريخ الاعتماد / الإغلاق</div>
                            <div class="meta-val">
                                @if($complaint->resolved_at)
                                    <span class="text-success fw-bold">
                                        <i class="bi bi-patch-check-fill me-1"></i>
                                        {{ $complaint->resolved_at->format('d/m/Y - h:i A') }}
                                    </span>
                                @else
                                    <span class="text-warning">
                                        <i class="bi bi-hourglass-split me-1"></i> قيد المتابعة النشطة
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resolution Card or In-Progress Notice -->
                @if($complaint->resolution)
                    <div class="resolution-glow-card mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-patch-check-fill text-success fs-4"></i>
                                <h5 class="fw-bold text-success mb-0">القرار والإجراء المتخذ المعتمد</h5>
                            </div>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-check2-circle"></i> تم الحل رسمياً
                            </span>
                        </div>
                        <p class="text-light mb-0 fs-6" style="line-height: 1.85; white-space: pre-line;">{{ $complaint->resolution }}</p>
                    </div>
                @else
                    <div class="p-3 rounded-3 bg-secondary bg-opacity-10 border border-secondary border-opacity-25 mb-4 d-flex align-items-center gap-3">
                        <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-info-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white">إفادة المتابعة</div>
                            <div class="text-muted small">
                                @if($complaint->status === 'open')
                                    تم استلام الشكوى بنجاح وتوجيهها لقسم المتابعة والجودة للتدقيق الأولي.
                                @elseif($complaint->status === 'in_progress')
                                    يقوم الفريق المختص حالياً باتخاذ التدابير المباشرة لمعالجة الشكوى وسنوافيكم بالحل فور اعتماده.
                                @else
                                    الشكوى مسجلة ومحفوظة بالمنظومة.
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Direct Actions Row (Copy Link, WhatsApp, Print) -->
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-25 no-print">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1.5" onclick="copyTrackingUrl()">
                            <i class="bi bi-link-45deg me-1"></i> <span id="copyLinkText">نسخ رابط التتبع</span>
                        </button>

                        @php
                            $whatsAppMsg = rawurlencode("مرحباً، يمكنك تتبع حالة الشكوى الخاصة بك لدى مؤسسة إنسان عبر الرابط التالي: " . route('complaint.track', ['code' => $complaint->tracking_code]));
                        @endphp
                        <a href="https://wa.me/?text={{ $whatsAppMsg }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5">
                            <i class="bi bi-whatsapp me-1"></i> مشاركة بالواتساب
                        </a>

                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 text-light" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> طباعة الإفادة
                        </button>
                    </div>

                    <a href="{{ route('complaint.track') }}" class="btn btn-sm btn-link text-decoration-none text-muted">
                        <i class="bi bi-arrow-right me-1"></i> تتبع شكوى أخرى
                    </a>
                </div>

            </div>
        @endif
    @endisset

    <!-- Quick Staff Portal Link -->
    <div class="text-center mt-3 no-print">
        <a href="{{ route('login') }}" class="text-muted text-decoration-none small hover-light">
            <i class="bi bi-lock me-1"></i> بوابة دخول موظفي وكادر المؤسسة
        </a>
    </div>

</main>

<!-- Modern Minimal Footer -->
<footer class="mt-auto py-3 text-center border-top border-secondary border-opacity-10 no-print" style="background: rgba(11, 19, 34, 0.95); font-size: 0.85rem; color: #64748b;">
    <div class="container">
        جميع الحقوق محفوظة &copy; مؤسسة إنسان الخيرية {{ date('Y') }} — منظومة الجودة وتتبع الشكاوى
    </div>
</footer>

<script>
    // Live input formatting
    const codeInput = document.getElementById('trackingCodeInput');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/\s+/g, '');
        });
    }

    // Paste button support
    const pasteBtn = document.getElementById('pasteBtn');
    if (pasteBtn && navigator.clipboard && navigator.clipboard.readText) {
        pasteBtn.addEventListener('click', async () => {
            try {
                const text = await navigator.clipboard.readText();
                if (text && codeInput) {
                    codeInput.value = text.trim().toUpperCase();
                    codeInput.focus();
                }
            } catch (err) {
                console.log('Clipboard permission denied');
            }
        });
    }

    // Copy Tracking Code
    function copyTrackingCode(code) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(() => {
                const textEl = document.getElementById('copyBtnText');
                if (textEl) {
                    textEl.innerText = 'تم النسخ!';
                    setTimeout(() => textEl.innerText = 'نسخ الكود', 2500);
                }
            });
        }
    }

    // Copy Full Tracking URL
    function copyTrackingUrl() {
        const url = window.location.href;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                const textEl = document.getElementById('copyLinkText');
                if (textEl) {
                    textEl.innerText = 'تم نسخ الرابط بنجاح!';
                    setTimeout(() => textEl.innerText = 'نسخ رابط التتبع', 2500);
                }
            });
        }
    }
</script>

</body>
</html>
