<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>تسجيل دخول | مؤسسة إنسان</title>
  <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
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
      background: #e9ecef;
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
            @if(file_exists(public_path('logo.png')))
              <img src="{{ asset('logo.png') }}" alt="logo" class="logo-img">
            @endif
            <h4 class="mb-0 fw-bold">مؤسسة إنسان</h4>
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