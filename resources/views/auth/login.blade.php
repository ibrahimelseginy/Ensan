<!DOCTYPE html>
<html lang="ar" dir="rtl" class="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'theme-dark' : '' }}" data-bs-theme="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'dark' : 'light' }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>تسجيل الدخول | مؤسسة إنسان الخيرية</title>
  
  {{-- Anti-FOUC Theme Script --}}
  <script>
    (function () {
      try {
        var theme = localStorage.getItem('ui.theme') || localStorage.getItem('theme');
        if (!theme) {
          var m = document.cookie.match(/(?:^|; )ui_theme=([^;]*)/);
          if (m) theme = decodeURIComponent(m[1]);
        }
        if (theme === 'dark' || (!theme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('theme-dark', 'dark-theme');
          document.documentElement.setAttribute('data-bs-theme', 'dark');
          document.documentElement.style.backgroundColor = '#0b1329';
        } else {
          document.documentElement.classList.remove('theme-dark', 'dark-theme');
          document.documentElement.setAttribute('data-bs-theme', 'light');
          document.documentElement.style.backgroundColor = '#ffffff';
        }
      } catch (e) {}
    })();
  </script>

  <link rel="icon" href="{{ asset('images/heart-icon.png') }}" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary-color: #059669;
      --primary-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
      --primary-hover: #047857;
      --primary-glow: rgba(16, 185, 129, 0.35);
      --bg-surface: #ffffff;
      --bg-page: #f8fafc;
      --border-subtle: #e2e8f0;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --transition-base: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    [data-bs-theme="dark"], html.theme-dark {
      --bg-surface: #0b1329;
      --bg-page: #060b18;
      --border-subtle: #1e293b;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --primary-glow: rgba(16, 185, 129, 0.2);
    }

    * {
      box-sizing: border-box;
    }

    html, body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
      scrollbar-width: none !important;
      -ms-overflow-style: none !important;
    }

    body {
      font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, sans-serif;
      background-color: var(--bg-surface);
      color: var(--text-main);
      overflow-x: hidden;
      transition: background-color 0.3s ease, color 0.3s ease;
      scrollbar-width: none !important;
      -ms-overflow-style: none !important;
    }

    *::-webkit-scrollbar {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }

    /* Edge-to-edge Fullscreen Split Wrapper */
    .fullscreen-wrapper {
      display: flex;
      width: 100vw;
      min-height: 100vh;
      height: 100vh;
    }

    /* ===================================================
       Right Pane: Showcase / Brand (In RTL DOM: First child)
       =================================================== */
    .showcase-pane {
      flex: 1.15;
      background: linear-gradient(145deg, #022c22 0%, #064e3b 45%, #047857 100%);
      color: #ffffff;
      padding: 3rem 4rem;
      position: relative;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
    }

    /* Ambient lighting & mesh inside showcase */
    .showcase-pane::before {
      content: '';
      position: absolute;
      inset: 0;
      background: 
        radial-gradient(circle at 15% 15%, rgba(16, 185, 129, 0.35) 0%, transparent 45%),
        radial-gradient(circle at 85% 85%, rgba(6, 182, 212, 0.2) 0%, transparent 40%);
      pointer-events: none;
    }

    .showcase-grid-pattern {
      position: absolute;
      inset: 0;
      opacity: 0.06;
      background-size: 28px 28px;
      background-image: 
        linear-gradient(to right, #ffffff 1px, transparent 1px),
        linear-gradient(to bottom, #ffffff 1px, transparent 1px);
      pointer-events: none;
    }

    .showcase-content-box {
      max-width: 580px;
      width: 100%;
      margin: 0 auto;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      z-index: 2;
    }

    /* Brand Header */
    .brand-badge {
      display: inline-flex;
      align-items: center;
      gap: 1rem;
    }

    .brand-logo-wrap {
      width: 62px;
      height: 62px;
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.22);
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 9px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease;
    }

    .brand-logo-wrap:hover {
      transform: scale(1.06) rotate(-2deg);
    }

    .brand-logo-wrap img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
    }

    .brand-name {
      font-size: 1.6rem;
      font-weight: 800;
      letter-spacing: -0.5px;
      margin: 0;
      line-height: 1.2;
      color: #ffffff;
    }

    .brand-slogan {
      font-size: 0.88rem;
      color: rgba(255, 255, 255, 0.85);
      margin: 0;
      font-weight: 400;
    }

    /* Center Showcase Content */
    .showcase-body {
      margin: 2rem 0;
    }

    .showcase-title {
      font-size: 2.25rem;
      font-weight: 800;
      line-height: 1.35;
      margin-bottom: 1.25rem;
      color: #ffffff;
      letter-spacing: -0.5px;
    }

    .showcase-title span {
      background: linear-gradient(120deg, #6ee7b7, #a7f3d0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .showcase-desc {
      font-size: 1.02rem;
      line-height: 1.75;
      color: rgba(255, 255, 255, 0.88);
      margin-bottom: 2rem;
    }

    /* Feature Cards */
    .feature-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 1.15rem;
      padding: 1rem 1.35rem;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 16px;
      transition: transform 0.25s ease, background 0.25s ease;
    }

    .feature-item:hover {
      transform: translateX(-6px);
      background: rgba(255, 255, 255, 0.14);
    }

    .feature-icon-box {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: rgba(16, 185, 129, 0.35);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: #a7f3d0;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .feature-text-title {
      font-weight: 700;
      font-size: 1rem;
      margin-bottom: 2px;
      color: #ffffff;
    }

    .feature-text-sub {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.78);
      margin: 0;
    }

    /* Showcase Quote Footer */
    .showcase-footer {
      padding-top: 1.5rem;
      border-top: 1px solid rgba(255, 255, 255, 0.16);
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.85);
      display: flex;
      align-items: center;
      gap: 0.65rem;
    }

    /* ===================================================
       Left Pane: Form Container (In RTL DOM: Second child)
       =================================================== */
    .form-pane {
      flex: 1;
      background: var(--bg-surface);
      padding: 3rem 4rem;
      position: relative;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      border-inline-start: 1px solid var(--border-subtle);
    }

    .form-content-box {
      max-width: 440px;
      width: 100%;
      margin: 0 auto;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* Top Action Bar inside Form */
    .form-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }

    .track-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--primary-color);
      text-decoration: none;
      padding: 0.5rem 1rem;
      background: rgba(5, 150, 105, 0.08);
      border: 1px solid rgba(5, 150, 105, 0.18);
      border-radius: 30px;
      transition: var(--transition-base);
    }

    .track-btn:hover {
      background: var(--primary-color);
      color: #ffffff;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px var(--primary-glow);
    }

    .theme-toggle-btn {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      border: 1.5px solid var(--border-subtle);
      background: var(--bg-page);
      color: var(--text-main);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1.15rem;
      transition: var(--transition-base);
    }

    .theme-toggle-btn:hover {
      transform: rotate(15deg) scale(1.05);
      border-color: var(--primary-color);
      color: var(--primary-color);
    }

    /* Form Center Wrapper */
    .form-center-section {
      margin: auto 0;
      padding: 1rem 0;
    }

    .form-header {
      margin-bottom: 2rem;
    }

    .form-header h2 {
      font-size: 1.95rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      letter-spacing: -0.5px;
    }

    .form-header p {
      color: var(--text-muted);
      font-size: 0.95rem;
      margin: 0;
    }

    /* Custom Inputs */
    .input-field-group {
      margin-bottom: 1.45rem;
    }

    .field-label {
      display: block;
      font-size: 0.88rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: var(--text-main);
    }

    .field-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .field-icon {
      position: absolute;
      right: 1.25rem;
      color: var(--text-muted);
      font-size: 1.2rem;
      pointer-events: none;
      transition: color 0.2s ease;
      z-index: 3;
    }

    .form-control-custom {
      width: 100%;
      height: 54px;
      padding: 0.75rem 3rem 0.75rem 1rem;
      font-size: 0.98rem;
      font-family: inherit;
      border: 1.5px solid var(--border-subtle);
      border-radius: 14px;
      background: var(--bg-page);
      color: var(--text-main);
      transition: var(--transition-base);
      outline: none;
    }

    .field-wrapper-password .form-control-custom {
      padding-left: 3.25rem;
    }

    .form-control-custom::placeholder {
      color: var(--text-muted);
      opacity: 0.65;
    }

    .form-control-custom:focus {
      border-color: var(--primary-color);
      background: var(--bg-surface);
      box-shadow: 0 0 0 4px var(--primary-glow);
    }

    .field-wrapper:focus-within .field-icon {
      color: var(--primary-color);
    }

    /* Toggle Password Button */
    .toggle-pass-btn {
      position: absolute;
      left: 0.85rem;
      background: none;
      border: none;
      color: var(--text-muted);
      font-size: 1.2rem;
      padding: 0.4rem;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border-radius: 8px;
      transition: var(--transition-base);
      z-index: 3;
    }

    .toggle-pass-btn:hover {
      color: var(--primary-color);
      background: rgba(5, 150, 105, 0.08);
    }

    /* Caps Lock Warning Alert */
    .caps-indicator {
      display: none;
      font-size: 0.8rem;
      color: #d97706;
      background: rgba(245, 158, 11, 0.1);
      border: 1px solid rgba(245, 158, 11, 0.2);
      border-radius: 8px;
      padding: 0.4rem 0.75rem;
      margin-top: 0.5rem;
      align-items: center;
      gap: 0.4rem;
      animation: fadeIn 0.2s ease-in-out;
    }

    /* Remember Option */
    .form-options-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.85rem;
    }

    .custom-check-wrap {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      cursor: pointer;
      user-select: none;
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    .custom-check-wrap input[type="checkbox"] {
      width: 19px;
      height: 19px;
      border-radius: 6px;
      border: 1.5px solid var(--border-subtle);
      accent-color: var(--primary-color);
      cursor: pointer;
      transition: var(--transition-base);
    }

    .custom-check-wrap:hover span {
      color: var(--text-main);
    }

    /* Submit Button */
    .submit-btn {
      width: 100%;
      height: 54px;
      background: var(--primary-gradient);
      border: none;
      border-radius: 14px;
      color: #ffffff;
      font-size: 1.08rem;
      font-weight: 700;
      letter-spacing: -0.2px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.65rem;
      cursor: pointer;
      box-shadow: 0 10px 24px -4px var(--primary-glow);
      transition: var(--transition-base);
      position: relative;
      overflow: hidden;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 28px -4px rgba(16, 185, 129, 0.5);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .submit-btn:disabled {
      opacity: 0.75;
      cursor: not-allowed;
      transform: none;
    }

    /* Alerts */
    .alert-custom-error {
      background: rgba(239, 68, 68, 0.08);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 12px;
      padding: 0.9rem 1.2rem;
      margin-bottom: 1.6rem;
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      color: #ef4444;
      font-size: 0.9rem;
      animation: shake 0.4s ease-in-out;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-6px); }
      40%, 80% { transform: translateX(6px); }
    }

    /* Footer Security Badge */
    .form-footer {
      padding-top: 1.5rem;
      border-top: 1px solid var(--border-subtle);
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .security-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      color: var(--primary-color);
      font-weight: 500;
    }

    /* Mobile Brand Bar (visible on < 992px) */
    .mobile-brand-bar {
      display: none;
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .mobile-brand-bar img {
      width: 64px;
      height: 64px;
      object-fit: contain;
      background: white;
      padding: 6px;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      margin-bottom: 0.5rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-4px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
      .fullscreen-wrapper {
        flex-direction: column;
        height: auto;
        min-height: 100vh;
      }
      .showcase-pane {
        display: none;
      }
      .mobile-brand-bar {
        display: block;
      }
      .form-pane {
        min-height: 100vh;
        height: auto;
        padding: 2.5rem 1.75rem;
        border-inline-start: none;
      }
    }
  </style>
</head>

<body>
  <div class="fullscreen-wrapper">
    
    <!-- Right Showcase Pane (Desktop: First in RTL DOM) -->
    <div class="showcase-pane">
      <div class="showcase-grid-pattern"></div>
      
      <div class="showcase-content-box">
        <!-- Brand Header -->
        <div class="brand-badge">
          <div class="brand-logo-wrap">
            <img src="{{ asset('images/heart-icon.png') }}" alt="شعار مؤسسة إنسان">
          </div>
          <div>
            <h1 class="brand-name">مؤسسة إنسان الخيرية</h1>
            <p class="brand-slogan">نبني جيل .. يبني حياة</p>
          </div>
        </div>

        <!-- Center Showcase Content -->
        <div class="showcase-body">
          <h2 class="showcase-title">
            نظام رقمي موحّد<br>
            <span>لإدارة العمل الإنساني والخيري</span>
          </h2>
          <p class="showcase-desc">
            منظومة سحابية متقدمة تهدف إلى رقمنة كافة العمليات الميدانية والمالية، وحوكمة التبرعات، وإدارة البرامج التنموية بأعلى معايير الشفافية والسرعة.
          </p>

          <!-- Feature Cards -->
          <div class="feature-list">
            <div class="feature-item">
              <div class="feature-icon-box">
                <i class="bi bi-pie-chart-fill"></i>
              </div>
              <div>
                <div class="feature-text-title">إدارة مالية وتبرعات شفافة</div>
                <div class="feature-text-sub">تتبع لحظي للتدفقات النقدية وسندات الصرف والتقارير</div>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon-box">
                <i class="bi bi-people-fill"></i>
              </div>
              <div>
                <div class="feature-text-title">كفالات ومستفيدين وتطوع</div>
                <div class="feature-text-sub">قواعد بيانات دقيقة لدعم الأسر وتنسيق جهود المتطوعين</div>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon-box">
                <i class="bi bi-shield-lock-fill"></i>
              </div>
              <div>
                <div class="feature-text-title">أمان عالي وصلاحيات دقيقة</div>
                <div class="feature-text-sub">حماية مشددة لبيانات المتبرعين وتدقيق شامل للعمليات</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quote Footer -->
        <div class="showcase-footer">
          <i class="bi bi-quote fs-5 text-warning"></i>
          <span>«نبني جيل .. يبني حياة» — بوابة إدارة المنظومة v1.0</span>
        </div>
      </div>
    </div>

    <!-- Left Form Pane (Desktop: Second in RTL DOM) -->
    <div class="form-pane">
      <div class="form-content-box">
        
        <!-- Top Action Bar -->
        <div class="form-topbar">
          <a href="{{ route('complaint.track') }}" class="track-btn">
            <i class="bi bi-search"></i>
            <span>تتبع الشكاوى</span>
          </a>

          <button id="themeToggle" class="theme-toggle-btn" type="button" title="تبديل المظهر (فاتح / داكن)" aria-label="Toggle Theme">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
          </button>
        </div>

        <!-- Form Center Section -->
        <div class="form-center-section">
          <!-- Mobile Brand (shown only on small screens) -->
          <div class="mobile-brand-bar">
            <img src="{{ asset('images/heart-icon.png') }}" alt="Logo">
            <h4 class="fw-bold mb-1">مؤسسة إنسان الخيرية</h4>
            <p class="text-muted small">نبني جيل .. يبني حياة</p>
          </div>

          <!-- Form Intro -->
          <div class="form-header">
            <h2>تسجيل الدخول 👋</h2>
            <p>أهلاً بك مجدداً، أدخل بياناتك للوصول إلى لوحة التحكم</p>
          </div>

          <!-- Validation / Server Errors -->
          @if(isset($errors) && $errors->any())
            <div class="alert-custom-error">
              <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 flex-shrink-0"></i>
              <div>
                <div class="fw-bold mb-1">تعذر إتمام الدخول:</div>
                <ul class="mb-0 ps-3">
                  @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

          <!-- Login Form -->
          <form method="POST" action="{{ route('login.post') }}" id="loginForm" autocomplete="on">
            @csrf

            <!-- Email Field -->
            <div class="input-field-group">
              <label for="email" class="field-label">البريد الإلكتروني</label>
              <div class="field-wrapper">
                <i class="bi bi-envelope-at field-icon"></i>
                <input 
                  type="email" 
                  name="email" 
                  id="email" 
                  class="form-control-custom" 
                  value="{{ old('email') }}" 
                  required 
                  autofocus 
                  placeholder="name@ensan.org" 
                  autocomplete="email"
                >
              </div>
            </div>

            <!-- Password Field -->
            <div class="input-field-group">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password-input" class="field-label mb-0">كلمة المرور</label>
              </div>
              <div class="field-wrapper field-wrapper-password">
                <i class="bi bi-shield-lock field-icon"></i>
                <input 
                  type="password" 
                  name="password" 
                  id="password-input" 
                  class="form-control-custom" 
                  required 
                  placeholder="••••••••••••" 
                  autocomplete="current-password"
                >
                <button 
                  type="button" 
                  class="toggle-pass-btn" 
                  id="togglePasswordBtn" 
                  aria-label="إظهار / إخفاء كلمة المرور"
                >
                  <i class="bi bi-eye" id="toggle-icon"></i>
                </button>
              </div>

              <!-- Caps Lock Alert -->
              <div id="capsLockAlert" class="caps-indicator">
                <i class="bi bi-capslock-fill"></i>
                <span>تنبيه: زر الحروف الكبيرة (Caps Lock) مفعّل</span>
              </div>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="form-options-row">
              <label class="custom-check-wrap">
                <input type="checkbox" name="remember" id="remember" value="1" checked>
                <span>تذكّر هذا الجهاز تلقائياً</span>
              </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submitBtn">
              <span id="btnText">تسجيل الدخول</span>
              <i class="bi bi-arrow-left-short fs-4" id="btnArrow"></i>
              <span class="spinner-border spinner-border-sm d-none" id="btnSpinner" role="status" aria-hidden="true"></span>
            </button>
          </form>
        </div>

        <!-- Footer -->
        <div class="form-footer">
          <div class="security-badge">
            <i class="bi bi-shield-check"></i>
            <span>اتصال مشفر 256-bit</span>
          </div>
          <div>
            جميع الحقوق محفوظة &copy; {{ date('Y') }}
          </div>
        </div>

      </div>
    </div>

  </div>

  <script>
    // 0. Auto-restore saved email
    const emailInput = document.getElementById('email');
    if (emailInput && !emailInput.value) {
      try {
        const savedEmail = localStorage.getItem('ensan_saved_email');
        if (savedEmail) {
          emailInput.value = savedEmail;
        }
      } catch (e) {}
    }

    // 1. Password Visibility Toggle
    const togglePassBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password-input');
    const toggleIcon = document.getElementById('toggle-icon');

    if (togglePassBtn && passwordInput) {
      togglePassBtn.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        toggleIcon.classList.toggle('bi-eye', !isPassword);
        toggleIcon.classList.toggle('bi-eye-slash', isPassword);
        togglePassBtn.setAttribute('title', isPassword ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
      });
    }

    // 2. Caps Lock Detection
    const capsLockAlert = document.getElementById('capsLockAlert');
    if (passwordInput && capsLockAlert) {
      ['keydown', 'keyup'].forEach(eventType => {
        passwordInput.addEventListener(eventType, function (e) {
          if (e.getModifierState && e.getModifierState('CapsLock')) {
            capsLockAlert.style.display = 'inline-flex';
          } else {
            capsLockAlert.style.display = 'none';
          }
        });
      });
      passwordInput.addEventListener('blur', function () {
        capsLockAlert.style.display = 'none';
      });
    }

    // 3. Form Submit Spinner & Credential Manager Friendly
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnArrow = document.getElementById('btnArrow');
    const btnSpinner = document.getElementById('btnSpinner');

    if (loginForm && submitBtn) {
      loginForm.addEventListener('submit', function () {
        if (emailInput && emailInput.value) {
          try {
            localStorage.setItem('ensan_saved_email', emailInput.value.trim());
          } catch (e) {}
        }
        btnText.textContent = 'جارٍ التحقق والدخول...';
        btnArrow.classList.add('d-none');
        btnSpinner.classList.remove('d-none');
        submitBtn.style.pointerEvents = 'none';
        setTimeout(function () {
          submitBtn.disabled = true;
        }, 500);
      });
    }

    // 4. Dark/Light Theme Switcher Sync with System & App
    document.addEventListener('DOMContentLoaded', function () {
      const themeToggle = document.getElementById('themeToggle');
      const themeIcon = document.getElementById('themeIcon');
      const html = document.documentElement;

      function updateThemeUI(isDark) {
        if (isDark) {
          html.setAttribute('data-bs-theme', 'dark');
          html.classList.add('theme-dark', 'dark-theme');
          document.body.classList.add('theme-dark', 'dark-theme');
          if (themeIcon) {
            themeIcon.className = 'bi bi-sun-fill text-warning';
          }
        } else {
          html.setAttribute('data-bs-theme', 'light');
          html.classList.remove('theme-dark', 'dark-theme');
          document.body.classList.remove('theme-dark', 'dark-theme');
          if (themeIcon) {
            themeIcon.className = 'bi bi-moon-stars-fill';
          }
        }
      }

      // Check current theme
      var savedTheme = localStorage.getItem('ui.theme') || localStorage.getItem('theme');
      if (!savedTheme) {
        var m = document.cookie.match(/(?:^|; )ui_theme=([^;]*)/);
        savedTheme = m ? decodeURIComponent(m[1]) : (html.classList.contains('theme-dark') ? 'dark' : 'light');
      }

      var isDark = savedTheme === 'dark';
      updateThemeUI(isDark);

      if (themeToggle) {
        themeToggle.addEventListener('click', function () {
          isDark = !isDark;
          updateThemeUI(isDark);
          var mode = isDark ? 'dark' : 'light';
          localStorage.setItem('theme', mode);
          localStorage.setItem('ui.theme', mode);
          document.cookie = 'ui_theme=' + mode + '; path=/; max-age=31536000; SameSite=Lax';
        });
      }
    });
  </script>
</body>

</html>
