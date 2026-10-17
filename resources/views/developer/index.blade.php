<!DOCTYPE html>
<html lang="ar" dir="rtl" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>معلومات المطور | Ibrahim Elseginy</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --primary-color: #2563eb;
      --primary-dark: #1d4ed8;
      --accent-color: #10b981;
      --bg-surface: #ffffff;
      --bg-canvas: #f8fafc;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
    }

    [data-bs-theme="dark"], html.theme-dark, body.theme-dark {
      --bg-surface: #1e293b;
      --bg-canvas: #0f172a;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --border-color: #334155;
      --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
    }

    body {
      font-family: 'IBM Plex Sans Arabic', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: var(--bg-canvas);
      color: var(--text-main);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      margin: 0;
      padding: 0;
    }

    .developer-navbar {
      background-color: var(--bg-surface) !important;
      border-bottom: 1px solid var(--border-color);
      padding: 0.85rem 1.5rem;
    }

    .solid-card {
      background-color: var(--bg-surface) !important;
      border: 1px solid var(--border-color) !important;
      border-radius: 1.25rem;
      box-shadow: var(--card-shadow);
      opacity: 1 !important;
    }

    .profile-hero-card {
      position: relative;
      overflow: hidden;
      border-top: 5px solid var(--primary-color) !important;
    }

    .avatar-wrapper {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1e40af, #3b82f6);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      font-size: 2.8rem;
      font-weight: 700;
      box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
      border: 4px solid var(--bg-surface);
      margin: 0 auto;
      position: relative;
    }

    .status-badge {
      position: absolute;
      bottom: 4px;
      left: 6px;
      width: 18px;
      height: 18px;
      background-color: var(--accent-color);
      border-radius: 50%;
      border: 3px solid var(--bg-surface);
    }

    .btn-whatsapp-custom {
      background-color: #25d366;
      color: #ffffff;
      font-weight: 600;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 0.75rem;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      box-shadow: 0 4px 14px rgba(37, 211, 102, 0.3);
    }
    .btn-whatsapp-custom:hover {
      background-color: #20ba59;
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
    }

    .btn-call-custom {
      background-color: var(--primary-color);
      color: #ffffff;
      font-weight: 600;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 0.75rem;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
    }
    .btn-call-custom:hover {
      background-color: var(--primary-dark);
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
    }

    .btn-copy-custom {
      background-color: transparent;
      border: 1px dashed var(--border-color);
      color: var(--text-muted);
      border-radius: 0.75rem;
      padding: 0.75rem 1rem;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    .btn-copy-custom:hover {
      background-color: rgba(37, 99, 235, 0.08);
      color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .feature-item-card {
      background-color: var(--bg-canvas) !important;
      border: 1px solid var(--border-color);
      border-radius: 1rem;
      padding: 1.25rem;
      transition: transform 0.2s ease;
    }
    .feature-item-card:hover {
      transform: translateY(-3px);
    }

    .tech-pill {
      background-color: var(--bg-canvas);
      border: 1px solid var(--border-color);
      color: var(--text-main);
      padding: 0.45rem 1rem;
      border-radius: 9999px;
      font-size: 0.85rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }

    .copy-toast {
      position: fixed;
      bottom: 2rem;
      right: 50%;
      transform: translateX(50%);
      background-color: #0f172a;
      color: #ffffff;
      padding: 0.75rem 1.5rem;
      border-radius: 9999px;
      font-size: 0.9rem;
      font-weight: 600;
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
      z-index: 9999;
      display: none;
    }
  </style>
</head>
<body>

  <!-- Top Navbar -->
  <nav class="developer-navbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <div class="fw-bold fs-5 text-primary d-flex align-items-center gap-2">
        <i class="bi bi-code-slash fs-4"></i>
        <span>معلومات المطور</span>
      </div>
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">نظام إنسان السحابي</span>
    </div>

    <div>
      @if(auth()->check())
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">
          <i class="bi bi-arrow-right me-1"></i> لوحة التحكم
        </a>
      @else
        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">
          <i class="bi bi-box-arrow-in-right me-1"></i> تسجيل الدخول
        </a>
      @endif
    </div>
  </nav>

  <!-- Main Content Container -->
  <div class="container py-5 flex-grow-1">
    <div class="row justify-content-center g-4">
      
      <!-- Profile Card -->
      <div class="col-lg-5 col-md-6">
        <div class="solid-card profile-hero-card p-4 text-center">
          
          <!-- Avatar -->
          <div class="avatar-wrapper mb-3">
            <span>IE</span>
            <div class="status-badge" title="متاح للاستشارات والتطوير"></div>
          </div>

          <!-- Name & Title -->
          <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">م/ إبراهيم السجيني</h3>
          <div class="text-primary fw-bold mb-2">@Ibrahim Elseginy &bull; فرصة Dev</div>
          <p class="text-muted small mb-3">
            رائد أعمال &bull; صاحب ومؤسس شركة فرصة Dev
            <br>
            Entrepreneur & Founder of Forsah Dev
          </p>

          <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-success-subtle text-success small fw-semibold mb-4 border border-success-subtle">
            <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 8px; height: 8px;"></span>
            صاحب ومؤسس شركة فرصة Dev للحلول البرمجية
          </div>

          <!-- Contact Buttons -->
          <div class="d-flex flex-column gap-2 mb-3">
            <!-- WhatsApp -->
            <a href="https://wa.me/201556592017?text={{ urlencode('مرحباً م. إبراهيم، بخصوص نظام إنسان السحابي...') }}" target="_blank" rel="noopener" class="btn-whatsapp-custom justify-content-center">
              <i class="bi bi-whatsapp fs-5"></i>
              <span>مراسلة عبر واتساب (01556592017)</span>
            </a>

            <!-- Direct Call -->
            <a href="tel:01069388721" class="btn-call-custom justify-content-center">
              <i class="bi bi-telephone-fill fs-5"></i>
              <span>اتصال هاتفي مباشر (01069388721)</span>
            </a>

            <!-- Copy Number -->
            <button type="button" class="btn-copy-custom" onclick="copyPhoneNumber('01069388721')">
              <i class="bi bi-clipboard me-1"></i> نسخ رقم الاتصال (01069388721)
            </button>
          </div>

          <div class="text-muted small border-top pt-3 mt-3">
            <i class="bi bi-geo-alt me-1"></i> جمهورية مصر العربية | متاح للمشاريع والأنظمة السحابية
          </div>

        </div>
      </div>

      <!-- System & Tech Details -->
      <div class="col-lg-7 col-md-6">
        <div class="solid-card p-4 h-100 d-flex flex-column justify-content-between">
          
          <div>
            <div class="d-flex align-items-center gap-3 mb-4">
              <div class="p-3 rounded-4 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                <i class="bi bi-rocket-takeoff-fill fs-3"></i>
              </div>
              <div>
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mb-1 fw-bold">القصة والمسيرة الريادية</div>
                <h4 class="fw-bold mb-0 text-main">م/ إبراهيم السجيني</h4>
                <div class="text-primary fw-bold small">رائد أعمال &bull; صاحب ومؤسس شركة فرصة Dev</div>
              </div>
            </div>

            <div class="p-3 rounded-4 mb-4 border" style="background: rgba(37, 99, 235, 0.04); border-color: rgba(37, 99, 235, 0.15) !important;">
              <p class="text-secondary leading-relaxed mb-2" style="font-size: 1.02rem; line-height: 1.85;">
                رحلة شغف وابتكار تقني تصنع فارقاً حقيقياً في عالم الأعمال الرقمي. تأسست رؤية <strong>م/ إبراهيم السجيني</strong> على الإيمان العميق بأن البرمجيات الذكية ليست مجرد كود، بل هي القوة الدافعة لتمكين المؤسسات والشركات من إطلاق كامل إمكاناتها وتحقيق قفزات نوعية في الأداء والنمو.
              </p>
              <p class="text-secondary leading-relaxed mb-0" style="font-size: 0.95rem; line-height: 1.8;">
                من خلال تأسيس وإدارة <strong>شركة فرصة Dev</strong>، يقود م/ إبراهيم منظومة لتطوير الحلول السحابية المتقدمة والأنظمة المؤسسية المتكاملة، مقدماً نموذجاً ريادياً يجمع بين الهندسة البرمجية المتينة، الرؤية الاقتصادية، والتركيز المطلق على تجربة العميل والنجاح المستدام.
              </p>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-sm-6">
                <div class="feature-item-card">
                  <div class="text-primary fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase-fill"></i>
                    <span>شركة فرصة Dev</span>
                  </div>
                  <small class="text-muted">بيت خبرة تقني متخصص في هندسة وبناء الحلول الرقمية والأنظمة السحابية المتطورة للشركات والمؤسسات.</small>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="feature-item-card">
                  <div class="text-success fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-lightbulb-fill"></i>
                    <span>ريادة الأعمال والابتكار</span>
                  </div>
                  <small class="text-muted">تطوير نماذج عمل تكنولوجية مبتكرة تحول الأفكار والمشاريع الطموحة إلى قصص نجاح وواقع ملموس.</small>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="feature-item-card">
                  <div class="text-info fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-cpu-fill"></i>
                    <span>التحول الرقمي المؤسسي</span>
                  </div>
                  <small class="text-muted">أتمتة العمليات الإدارية والمالية المعقدة لتسهيل الإدارة ورفع كفاءة وصنع القرار باحترافية.</small>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="feature-item-card">
                  <div class="text-warning fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check"></i>
                    <span>حلول مخصصة وشراكات</span>
                  </div>
                  <small class="text-muted">شراكات عمل موثوقة طويلة الأمد تضمن الدعم الفني، الجودة العالية، والتطوير المستمر للأنظمة.</small>
                </div>
              </div>
            </div>

            <div class="p-3 rounded-3 mb-4 border-start border-4 border-primary" style="background: rgba(15, 23, 42, 0.03);">
              <div class="fst-italic text-secondary small">
                <i class="bi bi-quote fs-5 text-primary me-1"></i>
                "في عالم الأعمال المتسارع، لا نكتفي بمواكبة التقنية، بل نصنع منها <strong>فرصة</strong> حقيقية تعيد تعريف النجاح والريادة."
              </div>
              <div class="text-end fw-bold text-primary small mt-1">— م/ إبراهيم السجيني</div>
            </div>

            <h6 class="fw-bold mb-2 text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">مجالات الخبرة والتخصص الريادي</h6>
            <div class="d-flex flex-wrap gap-2 mb-4">
              <span class="tech-pill"><i class="bi bi-stars text-primary"></i> ريادة الأعمال والشركات الناشئة</span>
              <span class="tech-pill"><i class="bi bi-code-slash text-success"></i> فرصة Dev (Forsah Dev)</span>
              <span class="tech-pill"><i class="bi bi-cloud-check text-info"></i> أنظمة سحابية متقدمة (Cloud ERP)</span>
              <span class="tech-pill"><i class="bi bi-diagram-3 text-warning"></i> استشارات التحول الرقمي</span>
              <span class="tech-pill"><i class="bi bi-phone text-danger"></i> حلول الويب وتطبيقات الجوال</span>
            </div>
          </div>

          <!-- Bottom Action -->
          <div class="border-top pt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="text-muted small">لأي استفسار أو شراكات عمل وتطوير أنظمة:</span>
            <div class="d-flex gap-2">
              <a href="https://wa.me/201556592017" target="_blank" class="btn btn-sm btn-outline-success fw-bold">
                <i class="bi bi-whatsapp me-1"></i> تواصل مع فرصة Dev
              </a>
              @if(auth()->check())
                <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-primary fw-bold">
                  الرئيسية
                </a>
              @endif
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>

  <!-- Footer -->
  <footer class="text-center py-4 border-top text-muted small mt-auto">
    <div class="container">
      تمت برمجة وتطوير النظام بواسطة 
      <strong class="text-primary">Ibrahim Elseginy</strong> &copy; {{ date('Y') }}
      <span class="mx-2">&bull;</span>
      اتصال هاتفي: <a href="tel:01069388721" class="text-decoration-none fw-bold text-secondary">01069388721</a>
      <span class="mx-2">&bull;</span>
      واتساب: <a href="https://wa.me/201556592017" target="_blank" rel="noopener" class="text-decoration-none fw-bold text-success">01556592017</a>
    </div>
  </footer>

  <!-- Copy Toast Notification -->
  <div class="copy-toast" id="copyToast">
    <i class="bi bi-check2-circle me-1 text-success"></i> تم نسخ رقم الاتصال (01069388721) بنجاح!
  </div>

  <script>
    function copyPhoneNumber(number) {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(number).then(() => {
          showToast();
        }).catch(() => {
          fallbackCopy(number);
        });
      } else {
        fallbackCopy(number);
      }
    }

    function fallbackCopy(text) {
      const tempInput = document.createElement("input");
      tempInput.value = text;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);
      showToast();
    }

    function showToast() {
      const toast = document.getElementById('copyToast');
      toast.style.display = 'block';
      setTimeout(() => {
        toast.style.display = 'none';
      }, 3000);
    }
  </script>
</body>
</html>
