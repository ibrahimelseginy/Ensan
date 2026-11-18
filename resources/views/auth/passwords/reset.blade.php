<!DOCTYPE html>
<html lang="ar" dir="rtl" class="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'theme-dark' : '' }}" data-bs-theme="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'dark' : 'light' }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>تعيين كلمة المرور الجديدة | مؤسسة إنسان الخيرية</title>

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
      max-width: 520px;
      width: 100%;
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 10px 30px rgba(0,0,0,0.04);
      overflow: hidden;
    }
    .auth-header {
      background: linear-gradient(135deg, #059669 0%, #10b981 100%);
      padding: 25px;
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
    .strength-meter {
      height: 6px;
      border-radius: 3px;
      background-color: #e2e8f0;
      overflow: hidden;
      margin-top: 6px;
    }
    .strength-bar {
      height: 100%;
      width: 0%;
      transition: width 0.3s ease, background-color 0.3s ease;
    }
    .rule-item {
      font-size: 0.8rem;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 4px;
      transition: color 0.2s;
    }
    .rule-item.valid {
      color: #059669;
      font-weight: 600;
    }
    .rule-item.valid i {
      color: #059669;
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
        <i class="bi bi-key-fill fs-1"></i>
      </div>
      <h4 class="fw-bold mb-1">تعيين كلمة المرور الآمنة</h4>
      <p class="small mb-0 opacity-75">أدخل كود التحقق واكتب كلمة المرور الجديدة متوافقة مع الشروط</p>
    </div>

    <div class="p-4">
      @if (session('status'))
        <div class="alert alert-info d-flex align-items-center gap-2 small py-2 mb-3" role="alert">
          <i class="bi bi-info-circle-fill fs-5"></i>
          <div>{{ session('status') }}</div>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 small py-2 mb-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill fs-5"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger small py-2 mb-3">
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('password.update') }}" id="pwdResetForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? '' }}">

        {{-- Email --}}
        <div class="mb-3">
          <label for="email" class="form-label fw-bold small text-muted">البريد الإلكتروني</label>
          <input type="email" id="email" name="email" class="form-control font-monospace" value="{{ old('email', $email ?? '') }}" required placeholder="name@example.com">
        </div>

        {{-- OTP Input (Shown if token not in URL) --}}
        @if(empty($token))
          <div class="mb-3">
            <label for="otp_code" class="form-label fw-bold small text-muted">كود التحقق الرقمي (OTP من 6 أرقام)</label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-123"></i></span>
              <input type="text" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" id="otp_code" name="otp_code" class="form-control text-center font-monospace fw-bold fs-5 tracking-widest" placeholder="123456" value="{{ old('otp_code') }}" required autocomplete="one-time-code">
            </div>
            <div class="form-text small">الكود الواصل إليك في رسالة الجيميل (Gmail).</div>
          </div>
        @endif

        {{-- New Password --}}
        <div class="mb-3">
          <label for="password" class="form-label fw-bold small text-muted">كلمة المرور الجديدة</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
            <input type="password" id="password" name="password" class="form-control font-monospace" required autocomplete="new-password" placeholder="اكتب كلمة مرور قوية...">
            <button class="btn btn-outline-secondary" type="button" id="togglePwdBtn"><i class="bi bi-eye" id="toggleIcon"></i></button>
          </div>

          {{-- Live Strength Meter --}}
          <div class="strength-meter">
            <div class="strength-bar" id="strengthBar"></div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-1">
            <span class="small text-muted" id="strengthText">قوة كلمة المرور: بانتظار الإدخال</span>
            <span class="small font-monospace fw-bold" id="strengthScore">0%</span>
          </div>

          {{-- Live Checklist --}}
          <div class="p-2.5 rounded-3 bg-light border mt-2">
            <div class="rule-item" id="ruleLength"><i class="bi bi-circle"></i> 10 خانات على الأقل</div>
            <div class="rule-item" id="ruleUpper"><i class="bi bi-circle"></i> حرف كبير واحد على الأقل [A-Z]</div>
            <div class="rule-item" id="ruleLower"><i class="bi bi-circle"></i> حرف صغير واحد على الأقل [a-z]</div>
            <div class="rule-item" id="ruleNumber"><i class="bi bi-circle"></i> رقم واحد على الأقل [0-9]</div>
            <div class="rule-item" id="ruleSpecial"><i class="bi bi-circle"></i> رمز خاص واحد على الأقل مثل (!@#$%^&*)</div>
          </div>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
          <label for="password_confirmation" class="form-label fw-bold small text-muted">تأكيد كلمة المرور</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-shield-check"></i></span>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control font-monospace" required autocomplete="new-password" placeholder="أعد كتابة كلمة المرور للتأكيد">
          </div>
          <div id="matchAlert" class="small mt-1 d-none"></div>
        </div>

        <button type="submit" class="btn-submit mb-3" id="btnSubmitPwd">
          <i class="bi bi-check-circle-fill me-1"></i> حفظ كلمة المرور وتسجيل الدخول
        </button>

        <div class="text-center pt-2 border-top">
          <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-right me-1"></i> العودة لشاشة تسجيل الدخول
          </a>
        </div>
      </form>
    </div>
  </div>

  <script>
    const pwdInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const strengthScore = document.getElementById('strengthScore');
    const toggleBtn = document.getElementById('togglePwdBtn');
    const toggleIcon = document.getElementById('toggleIcon');
    const matchAlert = document.getElementById('matchAlert');

    // Toggle Visibility
    toggleBtn.addEventListener('click', function () {
      const type = pwdInput.getAttribute('type') === 'password' ? 'text' : 'password';
      pwdInput.setAttribute('type', type);
      toggleIcon.className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    });

    // Real-time Strength Meter
    pwdInput.addEventListener('input', function () {
      const val = pwdInput.value;
      let score = 0;

      const hasLength = val.length >= 10;
      const hasUpper = /[A-Z]/.test(val);
      const hasLower = /[a-z]/.test(val);
      const hasNumber = /[0-9]/.test(val);
      const hasSpecial = /[!@#$%^&*()_+=\-\[\]{}|:;\"'<>,.?\/~`]/.test(val);

      updateRule('ruleLength', hasLength);
      updateRule('ruleUpper', hasUpper);
      updateRule('ruleLower', hasLower);
      updateRule('ruleNumber', hasNumber);
      updateRule('ruleSpecial', hasSpecial);

      if (hasLength) score += 20;
      if (hasUpper) score += 20;
      if (hasLower) score += 20;
      if (hasNumber) score += 20;
      if (hasSpecial) score += 20;

      strengthBar.style.width = score + '%';
      strengthScore.textContent = score + '%';

      if (score === 0) {
        strengthBar.style.backgroundColor = '#e2e8f0';
        strengthText.textContent = 'قوة كلمة المرور: بانتظار الإدخال';
        strengthText.className = 'small text-muted';
      } else if (score < 60) {
        strengthBar.style.backgroundColor = '#ef4444';
        strengthText.textContent = 'قوة كلمة المرور: ضعيفة';
        strengthText.className = 'small text-danger fw-bold';
      } else if (score < 100) {
        strengthBar.style.backgroundColor = '#f59e0b';
        strengthText.textContent = 'قوة كلمة المرور: متوسطة';
        strengthText.className = 'small text-warning-emphasis fw-bold';
      } else {
        strengthBar.style.backgroundColor = '#10b981';
        strengthText.textContent = 'قوة كلمة المرور: قوية وممتازة';
        strengthText.className = 'small text-success fw-bold';
      }

      checkMatch();
    });

    confirmInput.addEventListener('input', checkMatch);

    function updateRule(elementId, isValid) {
      const el = document.getElementById(elementId);
      if (isValid) {
        el.classList.add('valid');
        el.querySelector('i').className = 'bi bi-check-circle-fill text-success';
      } else {
        el.classList.remove('valid');
        el.querySelector('i').className = 'bi bi-circle';
      }
    }

    function checkMatch() {
      if (!confirmInput.value) {
        matchAlert.className = 'small mt-1 d-none';
        return;
      }
      if (pwdInput.value === confirmInput.value) {
        matchAlert.className = 'small mt-1 text-success fw-bold';
        matchAlert.innerHTML = '<i class="bi bi-check2-all me-1"></i> كلمتا المرور متطابقتان';
      } else {
        matchAlert.className = 'small mt-1 text-danger';
        matchAlert.innerHTML = '<i class="bi bi-x-circle me-1"></i> كلمتا المرور غير متطابقتين';
      }
    }
  </script>
</body>
</html>
