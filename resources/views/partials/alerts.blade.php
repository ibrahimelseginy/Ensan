@php $hasErrors = isset($errors) && $errors->any(); @endphp

<style>
    .premium-alert {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        padding: 16px 24px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        animation: slideDownFade 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        background: var(--ws-bg-card, #ffffff);
    }

    @keyframes slideDownFade {
        from { opacity: 0; transform: translateY(-15px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .premium-alert::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        width: 6px;
    }

    /* Success Theme */
    .premium-alert.alert-success { border-right: 6px solid #10b981; background: rgba(16, 185, 129, 0.05); }
    .premium-alert.alert-success .alert-icon { color: #10b981; background: rgba(16, 185, 129, 0.15); }

    /* Error Theme */
    .premium-alert.alert-danger { border-right: 6px solid #ef4444; background: rgba(239, 68, 68, 0.05); }
    .premium-alert.alert-danger .alert-icon { color: #ef4444; background: rgba(239, 68, 68, 0.15); }

    /* Info Theme */
    .premium-alert.alert-info { border-right: 6px solid #3b82f6; background: rgba(59, 130, 246, 0.05); }
    .premium-alert.alert-info .alert-icon { color: #3b82f6; background: rgba(59, 130, 246, 0.15); }

    .premium-alert .alert-icon {
        font-size: 1.4rem;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-left: 20px; /* RTL margin */
        flex-shrink: 0;
    }

    .premium-alert .alert-content {
        flex-grow: 1;
        text-align: right; /* RTL text */
    }

    .premium-alert .alert-title {
        font-weight: 800;
        font-size: 1rem;
        margin-bottom: 3px;
        color: var(--ws-text-primary, #1e293b);
    }

    .premium-alert .alert-text {
        font-size: 0.88rem;
        color: var(--ws-text-secondary, #64748b);
        margin: 0;
        line-height: 1.5;
    }

    .premium-alert .btn-close-custom {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--ws-text-secondary, #94a3b8);
        cursor: pointer;
        transition: 0.3s;
        padding: 5px 10px;
        margin-right: 15px;
        opacity: 0.6;
    }

    .premium-alert .btn-close-custom:hover {
        opacity: 1;
        color: var(--ws-text-primary, #1e293b);
        transform: rotate(90deg) scale(1.1);
    }

    /* Dark Mode Overrides */
    body.theme-dark .premium-alert {
        background: var(--ws-bg-card, #1a1d21);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-right-width: 6px;
    }
    body.theme-dark .premium-alert .alert-title { color: #f8fafc; }
    body.theme-dark .premium-alert .alert-text { color: #cbd5e1; }
</style>

@if(session('success'))
  <div class="premium-alert alert-success" role="alert">
    <div class="alert-icon">
        <i class="bi bi-check-lg"></i>
    </div>
    <div class="alert-content">
        <div class="alert-title">نجاح العملية</div>
        <p class="alert-text">{{ session('success') }}</p>
    </div>
    <button type="button" class="btn-close-custom" onclick="this.parentElement.style.display='none'">&times;</button>
  </div>
@endif

@if(session('error'))
  <div class="premium-alert alert-danger" role="alert">
    <div class="alert-icon">
        <i class="bi bi-x-octagon"></i>
    </div>
    <div class="alert-content">
        <div class="alert-title">تنبيه بالخطأ</div>
        <p class="alert-text">{{ session('error') }}</p>
    </div>
    <button type="button" class="btn-close-custom" onclick="this.parentElement.style.display='none'">&times;</button>
  </div>
@endif

@if(session('status'))
  <div class="premium-alert alert-info" role="alert">
    <div class="alert-icon">
        <i class="bi bi-info-circle"></i>
    </div>
    <div class="alert-content">
        <div class="alert-title">توضيح إضافي</div>
        <p class="alert-text">{{ session('status') }}</p>
    </div>
    <button type="button" class="btn-close-custom" onclick="this.parentElement.style.display='none'">&times;</button>
  </div>
@endif

@if($hasErrors)
  <div class="premium-alert alert-danger" role="alert">
    <div class="alert-icon">
        <i class="bi bi-exclamation-triangle"></i>
    </div>
    <div class="alert-content">
        <div class="alert-title">يرجى مراجعة المدخلات</div>
        <ul class="alert-text mb-0 ps-3 mt-1">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
    </div>
    <button type="button" class="btn-close-custom" onclick="this.parentElement.style.display='none'">&times;</button>
  </div>
@endif
