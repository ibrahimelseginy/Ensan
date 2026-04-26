<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>تسجيل دخول | مؤسسة انسان الخيرية</title>
  <link rel="icon" href="{{ asset('heart-icon.png') }}" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2ca87a;
      --primary-dark: #1e7e5a;
      --accent: #16a085;
      --bg: #f8f9fa;
      --text: #2c3e50;
    }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Tajawal', system-ui, -apple-system, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s, color 0.3s;
    }

    .login-card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      transition: background 0.3s, box-shadow 0.3s;
    }

    .login-header {
      background: linear-gradient(135deg, var(--primary), var(--accent));
      color: white;
      padding: 2rem;
      text-align: center;
      position: relative;
    }

    .btn-primary {
      background: var(--primary);
      border-color: var(--primary);
      padding: 0.6rem;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      border-color: var(--primary-dark);
      transform: translateY(-1px);
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(44, 168, 122, 0.1);
    }

    .logo-img {
      width: 90px;
      height: 90px;
      object-fit: contain;
      background: white;
      border-radius: 16px;
      padding: 5px;
      margin-bottom: 1rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Dark Mode Styles */
    body.dark-theme {
      --bg: #212529;
      --text: #f8f9fa;
    }

    body.dark-theme .login-card {
      background-color: #343a40;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    body.dark-theme .form-control {
      background-color: #2b3035;
      border-color: #495057;
      color: #fff;
    }

    body.dark-theme .form-control:focus {
      background-color: #2b3035;
      color: #fff;
    }

    body.dark-theme .text-muted {
      color: #adb5bd !important;
    }

    body.dark-theme .card-footer {
      background-color: #2b3035 !important;
      color: #adb5bd;
    }

    body.dark-theme .logo-img {
      background: #ffffff;
      filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.4));
    }
        /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
</style>
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="card login-card">
          <div class="login-header">
            <button id="themeToggle"
              class="btn btn-white btn-sm position-absolute top-0 end-0 m-3 rounded-circle shadow-sm bg-white text-primary d-flex align-items-center justify-content-center"
              style="width: 32px; height: 32px; border: none;" aria-label="Toggle Theme">
              <i class="bi bi-moon"></i>
            </button>
            @if(file_exists(public_path('heart-icon.png')) && file_exists(public_path('text-logo.png')))
              <div class="d-flex align-items-center justify-content-center gap-2 mb-3 mt-2">
                <img src="{{ asset('heart-icon.png') }}" alt="logo" class="logo-img mb-0 shadow-sm" style="width: 55px; height: 55px; padding: 5px;">
                <img src="{{ asset('text-logo.png') }}" alt="مؤسسة إنسان الخيرية" style="height: 35px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
              </div>
            @endif
            <p class="mb-0 opacity-75 small mt-1">نبني جيل .. يبني حياة</p>
          </div>
          <div class="card-body p-4 p-md-5">
            <h5 class="text-center mb-4 fw-bold text-muted">تسجيل الدخول</h5>
            @if(isset($errors) && $errors->any())
              <div class="alert alert-danger small rounded-3 border-0 shadow-sm mb-4">
                <ul class="mb-0 ps-3">
                  @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <form method="POST" action="{{ route('login.post') }}">
              @csrf
              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" required placeholder="name@example.com">
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">كلمة المرور</label>
                <div class="input-group">
                  <input type="password" name="password" id="password-input" class="form-control" required
                    placeholder="••••••••">
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                    <i class="bi bi-eye" id="toggle-icon"></i>
                  </button>
                </div>
              </div>
              <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small text-muted" for="remember">تذكّرني على هذا الجهاز</label>
              </div>
              <button class="btn btn-primary w-100 mb-3 rounded-pill">دخول</button>
            </form>
          </div>
          <div class="card-footer text-center bg-light border-0 py-3 small text-muted">
            جميع الحقوق محفوظة مؤسسة إنسان {{ date('Y') }} &copy;
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password-input');
      const toggleIcon = document.getElementById('toggle-icon');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      const themeToggle = document.getElementById('themeToggle');
      const icon = themeToggle.querySelector('i');
      const html = document.documentElement;

      // Load saved theme
      const savedTheme = localStorage.getItem('theme') || 'light';
      if (savedTheme === 'dark') {
        enableDarkMode();
      }

      themeToggle.addEventListener('click', function () {
        if (html.getAttribute('data-bs-theme') === 'dark') {
          disableDarkMode();
        } else {
          enableDarkMode();
        }
      });

      function enableDarkMode() {
        html.setAttribute('data-bs-theme', 'dark');
        document.body.classList.add('dark-theme');
        icon.classList.replace('bi-moon', 'bi-sun');
        themeToggle.classList.replace('btn-dark', 'btn-light');
        localStorage.setItem('theme', 'dark');
      }

      function disableDarkMode() {
        html.setAttribute('data-bs-theme', 'light');
        document.body.classList.remove('dark-theme');
        icon.classList.replace('bi-sun', 'bi-moon');
        themeToggle.classList.replace('btn-light', 'btn-dark');
        localStorage.setItem('theme', 'light');
      }
    });
  </script>
</body>

</html>

