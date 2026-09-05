<style>
    /* Admin Login Notifications Modal - High-End Solid Dark Theme */
    .admin-login-notifications-modal {
        background-color: rgba(2, 6, 23, 0.82) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
    }

    .admin-login-notifications-modal .modal-dialog {
        max-width: min(1180px, 94vw);
        margin: 1.75rem auto;
    }

    .admin-login-notifications-modal .modal-content,
    .admin-login-notifications-modal.modal .modal-content,
    .admin-notif-content {
        max-height: 90vh;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 20px !important;
        background-color: #0f172a !important; /* Solid Slate 900 - completely opaque */
        background: #0f172a !important;
        color: #f8fafc !important;
        opacity: 1 !important;
        box-shadow: 0 30px 80px -10px rgba(0, 0, 0, 0.95), 0 0 0 1px rgba(255, 255, 255, 0.08) !important;
    }

    .admin-login-notifications-modal .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        background-color: #1e293b !important; /* Solid Dark Slate */
        background: #1e293b !important;
        padding: 1.25rem 1.75rem !important;
    }

    .admin-login-notifications-modal .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        background-color: #1e293b !important; /* Solid Dark Slate */
        background: #1e293b !important;
        padding: 1.15rem 1.75rem !important;
    }

    .admin-login-notifications-modal .modal-body {
        overflow-y: auto;
        background-color: #0b1120 !important; /* Solid Deep Slate - 100% non-transparent */
        background: #0b1120 !important;
        padding: 1.75rem !important;
    }

    .admin-notif-heading {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .admin-notif-heading-icon {
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(5, 150, 105, 0.35);
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .admin-notif-user-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.65rem;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.08) !important;
        color: #94a3b8 !important;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .admin-notif-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        min-height: 28px;
        padding: 0.2rem 0.75rem;
        border-radius: 9999px;
        background: #ef4444 !important;
        color: #ffffff !important;
        font-size: 0.85rem;
        font-weight: 800;
        box-shadow: 0 0 14px rgba(239, 68, 68, 0.45);
    }

    .admin-login-notifications-modal .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
        opacity: 0.75;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .admin-login-notifications-modal .btn-close:hover {
        opacity: 1;
        transform: scale(1.1);
    }

    /* Intro Alert Box */
    .admin-notif-intro {
        background: #152033 !important;
        border: 1px solid rgba(59, 130, 246, 0.25) !important;
        border-inline-start: 4px solid #3b82f6 !important;
        border-radius: 14px !important;
        color: #e2e8f0 !important;
        padding: 0.95rem 1.25rem !important;
        font-size: 0.925rem;
        line-height: 1.5;
    }

    /* Suggested Actions Box */
    .admin-notif-suggestions {
        padding: 1.15rem 1.35rem !important;
        border: 1px solid rgba(255, 255, 255, 0.09) !important;
        border-radius: 16px !important;
        background: #141e30 !important; /* Solid Dark */
    }

    .admin-notif-suggestions .suggestions-title {
        color: #f8fafc !important;
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .admin-notif-suggestions a.btn {
        background: #1e293b !important;
        color: #f1f5f9 !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        padding: 0.45rem 1.1rem !important;
        border-radius: 9999px !important;
        font-size: 0.825rem !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.4rem !important;
        text-decoration: none;
    }

    .admin-notif-suggestions a.btn:hover {
        background: #059669 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.4);
    }

    /* Notification Cards Grid */
    .admin-notif-card {
        height: 100%;
        padding: 1.25rem !important;
        border: 1px solid rgba(255, 255, 255, 0.09) !important;
        border-inline-start-width: 4px !important;
        border-radius: 16px !important;
        background: #162032 !important; /* Solid Dark Card - 100% opaque */
        background-color: #162032 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .admin-notif-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45) !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }

    .admin-notif-card--danger { border-inline-start-color: #ef4444 !important; }
    .admin-notif-card--warning { border-inline-start-color: #f59e0b !important; }
    .admin-notif-card--info { border-inline-start-color: #3b82f6 !important; }
    .admin-notif-card--success { border-inline-start-color: #10b981 !important; }
    .admin-notif-card--secondary { border-inline-start-color: #64748b !important; }

    .admin-notif-card p {
        color: #f8fafc !important;
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        line-height: 1.6 !important;
        margin-bottom: 1.15rem !important;
        min-height: 48px;
        display: flex;
        align-items: center;
    }

    .admin-notif-icon {
        display: grid !important;
        place-items: center !important;
        width: 40px !important;
        height: 40px !important;
        border-radius: 12px !important;
        flex: 0 0 40px !important;
        font-size: 1.2rem !important;
    }

    .admin-notif-card--danger .admin-notif-icon {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #f87171 !important;
        border: 1px solid rgba(239, 68, 68, 0.3) !important;
    }
    .admin-notif-card--warning .admin-notif-icon {
        background: rgba(245, 158, 11, 0.15) !important;
        color: #fbbf24 !important;
        border: 1px solid rgba(245, 158, 11, 0.3) !important;
    }
    .admin-notif-card--info .admin-notif-icon {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #60a5fa !important;
        border: 1px solid rgba(59, 130, 246, 0.3) !important;
    }
    .admin-notif-card--success .admin-notif-icon {
        background: rgba(16, 185, 129, 0.15) !important;
        color: #34d399 !important;
        border: 1px solid rgba(16, 185, 129, 0.3) !important;
    }
    .admin-notif-card--secondary .admin-notif-icon {
        background: rgba(148, 163, 184, 0.15) !important;
        color: #cbd5e1 !important;
        border: 1px solid rgba(148, 163, 184, 0.3) !important;
    }

    .admin-notif-card .badge {
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        padding: 0.35rem 0.75rem !important;
        border-radius: 9999px !important;
        letter-spacing: 0.2px !important;
    }

    .admin-notif-card--danger .badge {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #fca5a5 !important;
        border: 1px solid rgba(239, 68, 68, 0.4) !important;
    }
    .admin-notif-card--warning .badge {
        background: rgba(245, 158, 11, 0.2) !important;
        color: #fde68a !important;
        border: 1px solid rgba(245, 158, 11, 0.4) !important;
    }
    .admin-notif-card--info .badge {
        background: rgba(59, 130, 246, 0.2) !important;
        color: #93c5fd !important;
        border: 1px solid rgba(59, 130, 246, 0.4) !important;
    }
    .admin-notif-card--success .badge {
        background: rgba(16, 185, 129, 0.2) !important;
        color: #6ee7b7 !important;
        border: 1px solid rgba(16, 185, 129, 0.4) !important;
    }
    .admin-notif-card--secondary .badge {
        background: rgba(148, 163, 184, 0.2) !important;
        color: #e2e8f0 !important;
        border: 1px solid rgba(148, 163, 184, 0.4) !important;
    }

    /* Card Action Buttons */
    .admin-notif-card .btn {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.45rem !important;
        padding: 0.55rem 1.15rem !important;
        border-radius: 9999px !important;
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
        text-decoration: none;
    }

    .admin-notif-card .btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4) !important;
    }

    .admin-notif-card--danger .btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .admin-notif-card--warning .btn {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: #0f172a !important;
        border: none !important;
        font-weight: 800 !important;
    }
    .admin-notif-card--info .btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .admin-notif-card--success .btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .admin-notif-card--secondary .btn {
        background: #1e293b !important;
        color: #f1f5f9 !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
    }
    .admin-notif-card--secondary .btn:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }

    /* Footer Buttons */
    .admin-notif-btn-all {
        background: rgba(59, 130, 246, 0.12) !important;
        border: 1px solid rgba(59, 130, 246, 0.4) !important;
        color: #93c5fd !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        padding: 0.6rem 1.35rem !important;
        transition: all 0.2s ease !important;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .admin-notif-btn-all:hover {
        background: #2563eb !important;
        color: #ffffff !important;
        border-color: #3b82f6 !important;
        transform: translateY(-1px);
    }

    .admin-notif-btn-dismiss {
        background: #334155 !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        color: #f8fafc !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        padding: 0.6rem 1.6rem !important;
        transition: all 0.2s ease !important;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .admin-notif-btn-dismiss:hover {
        background: #475569 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .admin-notif-empty {
        padding: 3.5rem 1rem;
        border: 1px dashed rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        color: #94a3b8;
        text-align: center;
        background: #162032;
    }

    @media (max-width: 767.98px) {
        .admin-login-notifications-modal .modal-dialog { margin: 0.5rem; max-width: none; }
        .admin-login-notifications-modal .modal-content { max-height: 96vh; border-radius: 16px; }
        .admin-notif-heading-icon { width: 40px; height: 40px; font-size: 1.15rem; }
    }
</style>

<div class="modal fade admin-login-notifications-modal" id="adminLoginNotificationsModal" tabindex="-1"
    aria-labelledby="adminLoginNotificationsModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content admin-notif-content">
            <div class="modal-header">
                <div class="admin-notif-heading">
                    <span class="admin-notif-heading-icon"><i class="bi bi-bell-fill"></i></span>
                    <div>
                        <div class="admin-notif-user-pill">
                            <i class="bi bi-person-fill"></i> مرحبًا {{ $navUser->name }}
                        </div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="adminLoginNotificationsModalLabel">
                            إشعارات الإدارة والإجراءات المطلوبة
                        </h5>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="admin-notif-count" id="adminLoginNotificationsCount">...</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="admin-notif-intro d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-info-circle-fill text-primary fs-5 mt-0 flex-shrink-0"></i>
                    <span>راجع كل التنبيهات الحالية، واضغط على زر الإجراء للانتقال مباشرة إلى الجزء المطلوب.</span>
                </div>

                <section id="adminLoginNotificationSuggestions" class="admin-notif-suggestions mb-4 d-none">
                    <div class="suggestions-title">
                        <i class="bi bi-lightning-charge-fill text-warning"></i>
                        <span>إجراءات مقترحة</span>
                    </div>
                    <div id="adminLoginNotificationSuggestionActions" class="d-flex flex-wrap gap-2"></div>
                </section>

                <div id="adminLoginNotificationsLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">جارٍ التحميل...</span></div>
                    <div class="small mt-3 text-muted">جارٍ تحميل كل الإشعارات...</div>
                </div>
                <div id="adminLoginNotificationsError" class="alert alert-danger d-none mb-0 bg-danger bg-opacity-25 text-white border-danger">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    تعذر تحميل الإشعارات الآن. يمكنك فتح مركز الإشعارات من الزر بالأسفل.
                </div>
                <div id="adminLoginNotificationsList" class="row g-3 d-none"></div>
            </div>
            <div class="modal-footer justify-content-between">
                <a href="{{ route('notifications.index') }}" class="admin-notif-btn-all">
                    <i class="bi bi-bell me-1"></i> فتح مركز الإشعارات بالكامل
                </a>
                <button type="button" class="admin-notif-btn-dismiss" data-bs-dismiss="modal">
                    <i class="bi bi-check2 me-1"></i> تمت المراجعة
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('adminLoginNotificationsModal');
        if (!modalElement || !window.bootstrap) return;
        const notificationsUrl = @json(route('notifications.index'));

        const loading = document.getElementById('adminLoginNotificationsLoading');
        const errorBox = document.getElementById('adminLoginNotificationsError');
        const list = document.getElementById('adminLoginNotificationsList');
        const count = document.getElementById('adminLoginNotificationsCount');
        const suggestions = document.getElementById('adminLoginNotificationSuggestions');
        const suggestionActions = document.getElementById('adminLoginNotificationSuggestionActions');
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);

        const iconMap = {
            danger: 'exclamation-octagon-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill',
            success: 'check-circle-fill',
            secondary: 'bell-fill'
        };
        const labelMap = { danger: 'هام', warning: 'تحذير', info: 'معلومة', success: 'مطمئن', secondary: 'عام' };
        const actionMap = {
            change_requests: 'مراجعة وتنفيذ',
            complaints: 'فتح الشكاوى',
            tasks: 'فتح المهام',
            attendance: 'مراجعة الحضور',
            finance: 'فتح المالية',
            payrolls: 'مراجعة الرواتب',
            audits: 'فحص السجلات',
            delegates: 'إدارة المندوبين',
            donations: 'مراجعة التبرعات',
            guest_houses: 'استكمال البيانات',
            users: 'إدارة المستخدمين',
            inventory: 'مراجعة المخزون',
            beneficiaries: 'فتح المستفيدين'
        };

        function makeNotificationCard(notification) {
            const type = Object.prototype.hasOwnProperty.call(iconMap, notification.type) ? notification.type : 'secondary';
            const column = document.createElement('div');
            column.className = 'col-md-6 col-xl-4';

            const card = document.createElement('article');
            card.className = 'admin-notif-card admin-notif-card--' + type;

            const header = document.createElement('div');
            header.className = 'd-flex align-items-center justify-content-between gap-2 mb-3';
            const icon = document.createElement('span');
            icon.className = 'admin-notif-icon';
            icon.innerHTML = '<i class="bi bi-' + iconMap[type] + '"></i>';
            const badge = document.createElement('span');
            badge.className = 'badge';
            badge.textContent = labelMap[type];
            header.append(icon, badge);

            const text = document.createElement('p');
            text.textContent = notification.text || 'إشعار جديد';

            const action = document.createElement('a');
            action.href = notification.link || notificationsUrl;
            action.className = 'btn';
            action.innerHTML = '<i class="bi bi-arrow-left-circle me-1"></i>';
            action.append(document.createTextNode(actionMap[notification.category] || 'فتح واتخاذ إجراء'));

            card.append(header, text, action);
            column.appendChild(card);
            return column;
        }

        function render(data) {
            const items = Array.isArray(data.items) ? data.items : [];
            const suggested = Array.isArray(data.suggestions) ? data.suggestions : [];
            loading.classList.add('d-none');
            list.classList.remove('d-none');
            count.textContent = items.length;

            if (!items.length) {
                const empty = document.createElement('div');
                empty.className = 'col-12';
                empty.innerHTML = '<div class="admin-notif-empty"><i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i><strong>لا توجد إشعارات تحتاج مراجعة حاليًا</strong><div class="small mt-1">كل الأمور تبدو مستقرة.</div></div>';
                list.appendChild(empty);
            } else {
                items.forEach(item => list.appendChild(makeNotificationCard(item)));
            }

            if (suggested.length) {
                suggestions.classList.remove('d-none');
                suggested.forEach(suggestion => {
                    const action = document.createElement('a');
                    action.href = suggestion.link || notificationsUrl;
                    action.className = 'btn';
                    action.innerHTML = '<i class="bi bi-lightning-charge-fill text-warning me-1"></i>';
                    action.append(document.createTextNode(suggestion.text || 'تنفيذ الإجراء'));
                    suggestionActions.appendChild(action);
                });
            }
        }

        modal.show();
        fetch(notificationsUrl + '?format=json', {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) throw new Error('notification-load-failed');
                return response.json();
            })
            .then(render)
            .catch(() => {
                loading.classList.add('d-none');
                errorBox.classList.remove('d-none');
                count.textContent = '!';
            });
    });
</script>
