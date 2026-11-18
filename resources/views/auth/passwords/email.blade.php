<!DOCTYPE html>
<html lang="ar" dir="rtl" class="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'theme-dark' : '' }}" data-bs-theme="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'dark' : 'light' }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>استرداد كلمة المرور | مؤسسة إنسان الخيرية</title>

  <link rel="icon" href="{{ asset('images/heart-icon.png') }}" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #059669;
      --primary-hover: #047857;
      --bg-page: #f8fafc;
      --font-family: 'IBM Plex Sans Arabic', -apple-system, sans-serif;
    }
    body {
      font-family: var(--font-family);
      background-color: var(--bg-page);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .auth-card {
      max-width: 480px;
      width: 100%;
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 10px 30px rgba(0,0,0,0.04);
      overflow: hidden;
    }
    .auth-header {
      background: linear-gradient(135deg, #059669 0%, #10b981 100%);
      padding: 30px 25px;
      text-align: center;
      color: #ffffff;
    }
    .btn-submit {
      background: #059669;
      color: #ffffff;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 600;
      width: 100%;
      transition: all 0.2s ease;
    }
    .btn-submit:hover {
      background: #047857;
      color: #ffffff;
      transform: translateY(-1px);
    }
    [data-bs-theme="dark"] body {
      background-color: #0b1329;
      color: #f1f5f9;
    }
    [data-bs-theme="dark"] .auth-card {
      background: #131d36;
      border-color: #1e293b;
    }
    [data-bs-theme="dark"] .form-control {
      background-color: #0f172a;
      border-color: #334155;
      color: #f8fafc;
    }
  </style>
</head>

<body>
  <div class="auth-card">
    <div class="auth-header">
      <div class="mb-2">
        <i class="bi bi-shield-lock-fill fs-1"></i>
      </div>
      <h4 class="fw-bold mb-1">استعادة كلمة المرور</h4>
      <p class="small mb-0 opacity-75">أدخل بريدك الإلكتروني لاستلام كود التحقق ورابط التعيين الفوري</p>
    </div>

    <div class="p-4">
      @if (session('status'))
        <div class="alert alert-success d-flex align-items-center gap-2 small py-2.5 mb-3" role="alert">
          <i class="bi bi-check-circle-fill fs-5"></i>
          <div>{{ session('status') }}</div>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 small py-2.5 mb-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill fs-5"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
          <label for="email" class="form-label fw-bold small text-muted">البريد الإلكتروني المسجل في النظام</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
            <input type="email" id="email" name="email" class="form-control font-monospace @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
          </div>
          @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
          <div class="form-text small">سيصلك كود OTP مكون من 6 أرقام ورابط مباشر على حسابك في الجيميل (Gmail).</div>
        </div>

        <button type="submit" class="btn-submit mb-3">
          <i class="bi bi-send-fill me-1"></i> إرسال رمز التحقق والرابط
        </button>

        <div class="text-center pt-2 border-top">
          <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-right me-1"></i> العودة لشاشة تسجيل الدخول
          </a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
