<style>
    .admin-login-notifications-modal {
        --admin-notif-surface: #ffffff;
        --admin-notif-soft: #f8fafc;
        --admin-notif-border: #e2e8f0;
        --admin-notif-text: #0f172a;
        --admin-notif-muted: #64748b;
    }
    .theme-dark .admin-login-notifications-modal {
        --admin-notif-surface: #0f172a;
        --admin-notif-soft: #111c31;
        --admin-notif-border: #263449;
        --admin-notif-text: #f8fafc;
        --admin-notif-muted: #94a3b8;
    }
    .admin-login-notifications-modal .modal-dialog { max-width: min(1180px, 94vw); }
    .admin-login-notifications-modal .modal-content {
        max-height: 90vh;
        overflow: hidden;
        border: 1px solid var(--admin-notif-border);
        border-radius: 20px;
        background: var(--admin-notif-surface);
        color: var(--admin-notif-text);
        box-shadow: 0 30px 80px rgba(2, 6, 23, .38);
    }
    .admin-login-notifications-modal .modal-header,
    .admin-login-notifications-modal .modal-footer {
        border-color: var(--admin-notif-border);
        background: var(--admin-notif-surface);
    }
    .admin-login-notifications-modal .modal-body {
        overflow-y: auto;
        background: var(--admin-notif-soft);
    }
    .admin-notif-heading { display: flex; align-items: center; gap: .85rem; }
    .admin-notif-heading-icon {
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #2563eb;
        color: #fff;
        box-shadow: 0 8px 24px rgba(37, 99, 235, .28);
        font-size: 1.25rem;
    }
    .admin-notif-heading small,
    .admin-notif-intro { color: var(--admin-notif-muted); }
    .admin-notif-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        min-height: 28px;
        padding: .2rem .65rem;
        border-radius: 999px;
        background: #fee2e2;
        color: #dc2626;
        font-size: .78rem;
        font-weight: 800;
    }
    .admin-notif-suggestions {
        padding: 1rem;
        border: 1px solid rgba(37, 99, 235, .2);
        border-radius: 14px;
        background: rgba(37, 99, 235, .07);
    }
    .admin-notif-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--admin-notif-border);
        border-inline-start-width: 4px;
        border-radius: 15px;
        background: var(--admin-notif-surface);
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .admin-notif-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(15, 23, 42, .08); }
    .admin-notif-card--danger { border-inline-start-color: #dc3545; }
    .admin-notif-card--warning { border-inline-start-color: #ffc107; }
    .admin-notif-card--info { border-inline-start-color: #0dcaf0; }
    .admin-notif-card--success { border-inline-start-color: #198754; }
    .admin-notif-card--secondary { border-inline-start-color: #6c757d; }
    .admin-notif-card p { color: var(--admin-notif-text); }
    .admin-notif-icon {
        display: grid;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        flex: 0 0 38px;
    }
    .admin-notif-empty {
        padding: 3.5rem 1rem;
        border: 1px dashed var(--admin-notif-border);
        border-radius: 16px;
        color: var(--admin-notif-muted);
        text-align: center;
        background: var(--admin-notif-surface);
    }
    @media (max-width: 767.98px) {
        .admin-login-notifications-modal .modal-dialog { margin: .6rem; max-width: none; }
        .admin-login-notifications-modal .modal-content { max-height: 96vh; border-radius: 15px; }
        .admin-notif-heading-icon { width: 42px; height: 42px; }
    }
</style>

<div class="modal fade admin-login-notifications-modal" id="adminLoginNotificationsModal" tabindex="-1"
    aria-labelledby="adminLoginNotificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="admin-notif-heading">
                    <span class="admin-notif-heading-icon"><i class="bi bi-bell-fill"></i></span>
                    <div>
                        <small>مرحبًا {{ $navUser->name }}</small>
                        <h5 class="modal-title fw-bold mb-0" id="adminLoginNotificationsModalLabel">إشعارات الإدارة والإجراءات المطلوبة</h5>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="admin-notif-count" id="adminLoginNotificationsCount">...</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="admin-notif-intro d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                    <span>راجع كل التنبيهات الحالية، واضغط على زر الإجراء للانتقال مباشرة إلى الجزء المطلوب.</span>
                </div>

                <section id="adminLoginNotificationSuggestions" class="admin-notif-suggestions mb-4 d-none">
                    <div class="fw-bold mb-2"><i class="bi bi-lightning-charge-fill text-primary me-1"></i> إجراءات مقترحة</div>
                    <div id="adminLoginNotificationSuggestionActions" class="d-flex flex-wrap gap-2"></div>
                </section>

                <div id="adminLoginNotificationsLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">جارٍ التحميل...</span></div>
                    <div class="small mt-3 text-muted">جارٍ تحميل كل الإشعارات...</div>
                </div>
                <div id="adminLoginNotificationsError" class="alert alert-danger d-none mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    تعذر تحميل الإشعارات الآن. يمكنك فتح مركز الإشعارات من الزر بالأسفل.
                </div>
                <div id="adminLoginNotificationsList" class="row g-3 d-none"></div>
            </div>
            <div class="modal-footer justify-content-between">
                <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-bell me-1"></i> فتح مركز الإشعارات بالكامل
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
            danger: 'exclamation-octagon-fill', warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill', success: 'check-circle-fill', secondary: 'bell-fill'
        };
        const labelMap = { danger: 'هام', warning: 'تحذير', info: 'معلومة', success: 'مطمئن', secondary: 'عام' };
        const actionMap = {
            change_requests: 'مراجعة وتنفيذ', complaints: 'فتح الشكاوى', tasks: 'فتح المهام',
            attendance: 'مراجعة الحضور', finance: 'فتح المالية', payrolls: 'مراجعة الرواتب',
            audits: 'فحص السجلات', delegates: 'إدارة المندوبين', donations: 'مراجعة التبرعات',
            guest_houses: 'استكمال البيانات', users: 'إدارة المستخدمين', inventory: 'مراجعة المخزون',
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
            icon.className = 'admin-notif-icon bg-' + type + ' bg-opacity-10 text-' + type;
            icon.innerHTML = '<i class="bi bi-' + iconMap[type] + '"></i>';
            const badge = document.createElement('span');
            badge.className = 'badge rounded-pill bg-' + type + ' bg-opacity-10 text-' + type;
            badge.textContent = labelMap[type];
            header.append(icon, badge);

            const text = document.createElement('p');
            text.className = 'fw-semibold mb-3 lh-base';
            text.textContent = notification.text || 'إشعار جديد';

            const action = document.createElement('a');
            action.href = notification.link || notificationsUrl;
            action.className = 'btn btn-' + type + ' btn-sm rounded-pill px-3';
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
                    action.className = 'btn btn-primary btn-sm rounded-pill px-3';
                    action.innerHTML = '<i class="bi bi-lightning-charge me-1"></i>';
                    action.append(document.createTextNode(suggestion.text || 'تنفيذ الإجراء'));
                    suggestionActions.appendChild(action);
                });
            }
        }

        modal.show();
        fetch(notificationsUrl + '?format=json', {
            headers: { 'Accept': 'application/json' }, credentials: 'same-origin'
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
