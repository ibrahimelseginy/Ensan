
<div class="modal fade" id="iconPickerModal" tabindex="-1" aria-hidden="true" style="z-index: 3000;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-0 bg-slate-900 text-white shadow-2xl">
            <div class="modal-header border-bottom border-white border-opacity-10 bg-slate-800 p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-grid-3x3-gap fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">معرض الأيقونات</h5>
                        <p class="text-slate-400 x-small mb-0">اختر أيقونة من المجموعة المتوفرة لتمييز مشروعك</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4">
                {{-- Search Bar --}}
                <div class="position-relative mb-4">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-500"></i>
                    <input type="text" id="iconSearchInput" class="form-control bg-dark border-secondary text-white ps-5 py-2 rounded-pill" placeholder="ابحث عن أيقونة... (مثال: heart, star, check)" dir="rtl">
                </div>

                {{-- Icons Grid --}}
                <div class="icon-grid-container custom-scrollbar" style="max-height: 400px; overflow-y: auto;">
                    <div class="row g-2 text-center" id="iconsGrid">
                        {{-- Icons will be injected here via JS to keep the file clean --}}
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top border-white border-opacity-10 bg-slate-800">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-item {
        padding: 15px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .icon-item:hover {
        background: rgba(13, 110, 253, 0.1);
        border-color: rgba(13, 110, 253, 0.3);
        transform: translateY(-3px);
    }
    .icon-item i {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 5px;
    }
    .icon-item span {
        font-size: 0.6rem;
        color: #94a3b8;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .icon-item.active {
        background: #0d6efd;
        border-color: #0d6efd;
    }
    .icon-item.active i, .icon-item.active span {
        color: white;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>

<script>
    let currentIconTarget = null;
    const commonIcons = [
        'bi-star', 'bi-star-fill', 'bi-heart', 'bi-heart-fill', 'bi-check-circle', 'bi-check-circle-fill',
        'bi-info-circle', 'bi-info-circle-fill', 'bi-gear', 'bi-gear-fill', 'bi-people', 'bi-people-fill',
        'bi-geo-alt', 'bi-geo-alt-fill', 'bi-cash-stack', 'bi-coin', 'bi-award', 'bi-award-fill',
        'bi-mortarboard', 'bi-mortarboard-fill', 'bi-hospital', 'bi-hospital-fill', 'bi-house', 'bi-house-fill',
        'bi-building', 'bi-calendar-event', 'bi-cart', 'bi-cart-fill', 'bi-chat-dots', 'bi-chat-dots-fill',
        'bi-clock', 'bi-clock-fill', 'bi-cloud-sun', 'bi-cloud-sun-fill', 'bi-cursor', 'bi-cursor-fill',
        'bi-display', 'bi-envelope', 'bi-envelope-fill', 'bi-eye', 'bi-eye-fill', 'bi-file-earmark-text',
        'bi-flag', 'bi-flag-fill', 'bi-gift', 'bi-gift-fill', 'bi-globe', 'bi-graph-up', 'bi-hand-thumbs-up',
        'bi-image', 'bi-image-fill', 'bi-journal-text', 'bi-key', 'bi-key-fill', 'bi-lamp', 'bi-lightbulb',
        'bi-lightning', 'bi-lightning-fill', 'bi-link-45deg', 'bi-list', 'bi-lock', 'bi-lock-fill',
        'bi-map', 'bi-map-fill', 'bi-megaphone', 'bi-megaphone-fill', 'bi-mic', 'bi-mic-fill',
        'bi-moon', 'bi-moon-fill', 'bi-music-note-beamed', 'bi-palette', 'bi-palette-fill', 'bi-paperclip',
        'bi-pencil', 'bi-pencil-fill', 'bi-phone', 'bi-phone-fill', 'bi-play', 'bi-play-fill',
        'bi-printer', 'bi-printer-fill', 'bi-puzzle', 'bi-puzzle-fill', 'bi-question-circle', 'bi-receipt',
        'bi-search', 'bi-shield', 'bi-shield-fill', 'bi-shuffle', 'bi-signpost', 'bi-slack',
        'bi-speedometer', 'bi-sun', 'bi-sun-fill', 'bi-tag', 'bi-tag-fill', 'bi-telephone',
        'bi-terminal', 'bi-thermometer-half', 'bi-three-dots', 'bi-tools', 'bi-trash', 'bi-trash-fill',
        'bi-trophy', 'bi-trophy-fill', 'bi-tv', 'bi-tv-fill', 'bi-umbrella', 'bi-unlock', 'bi-upload',
        'bi-wallet', 'bi-wallet-fill', 'bi-watch', 'bi-wifi', 'bi-wrench', 'bi-activity', 'bi-alarm',
        'bi-archive', 'bi-arrow-repeat', 'bi-back', 'bi-bag', 'bi-balloon', 'bi-bank', 'bi-battery-full',
        'bi-bell', 'bi-bicycle', 'bi-binoculars', 'bi-bookmark', 'bi-book', 'bi-box', 'bi-briefcase',
        'bi-broadcast', 'bi-brush', 'bi-bug', 'bi-camera', 'bi-capsule', 'bi-card-list', 'bi-collection',
        'bi-compass', 'bi-controller', 'bi-cpu', 'bi-credit-card', 'bi-cup-hot', 'bi-database',
        'bi-diagram-3', 'bi-droplet', 'bi-earbuds', 'bi-egg', 'bi-eyeglasses', 'bi-fan', 'bi-file-earmark',
        'bi-filter', 'bi-fingerprint', 'bi-fire', 'bi-flower1', 'bi-folder', 'bi-fuel-pump', 'bi-funnel',
        'bi-gamepad', 'bi-gender-ambiguous', 'bi-gem', 'bi-hammer', 'bi-handbag', 'bi-hash', 'bi-headset',
        'bi-hourglass', 'bi-infinity', 'bi-input-cursor', 'bi-intersect', 'bi-kanban', 'bi-keyboard',
        'bi-ladder', 'bi-laptop', 'bi-layers', 'bi-life-preserver', 'bi-magic', 'bi-mask', 'bi-memory',
        'bi-mouse', 'bi-newspaper', 'bi-nut', 'bi-optical-audio', 'bi-outlet', 'bi-paint-bucket',
        'bi-patch-check', 'bi-pc', 'bi-peace', 'bi-pen', 'bi-person-badge', 'bi-pin', 'bi-plug',
        'bi-postage', 'bi-power', 'bi-projection-screen', 'bi-radioactive', 'bi-recycle', 'bi-robot',
        'bi-rocket', 'bi-safe', 'bi-save', 'bi-sd-card', 'bi-send', 'bi-server', 'bi-share',
        'bi-shield-check', 'bi-shop', 'bi-sign-stop', 'bi-snow', 'bi-speaker', 'bi-spellcheck',
        'bi-sticky', 'bi-stopwatch', 'bi-suit-diamond', 'bi-suit-spade', 'bi-sun', 'bi-symmetry-horizontal',
        'bi-table', 'bi-tablet', 'bi-ticket', 'bi-translate', 'bi-tree', 'bi-truck', 'bi-tsunami',
        'bi-ui-checks', 'bi-unity', 'bi-usb', 'bi-vignette', 'bi-voicemail', 'bi-water', 'bi-windows',
        'bi-wind', 'bi-xbox', 'bi-yield', 'bi-zoom-in'
    ];

    function renderIcons(filter = '') {
        const grid = document.getElementById('iconsGrid');
        grid.innerHTML = '';
        
        const filtered = commonIcons.filter(icon => icon.includes(filter.toLowerCase()));
        
        filtered.forEach(icon => {
            const col = document.createElement('div');
            col.className = 'col-3 col-md-2';
            col.innerHTML = `
                <div class="icon-item" onclick="selectIcon('${icon}')">
                    <i class="bi ${icon}"></i>
                    <span>${icon.replace('bi-', '')}</span>
                </div>
            `;
            grid.appendChild(col);
        });

        if (filtered.length === 0) {
            grid.innerHTML = '<div class="col-12 py-5 text-slate-500">لا توجد أيقونات تطابق بحثك</div>';
        }
    }

    function openIconPicker(inputElement) {
        currentIconTarget = inputElement;
        const modal = new bootstrap.Modal(document.getElementById('iconPickerModal'));
        modal.show();
        
        // Focus search input after modal opens
        document.getElementById('iconPickerModal').addEventListener('shown.bs.modal', function () {
            document.getElementById('iconSearchInput').focus();
        }, { once: true });
    }

    function selectIcon(icon) {
        if (currentIconTarget) {
            currentIconTarget.value = icon;
            
            // Update preview if exists
            const prevIcon = currentIconTarget.previousElementSibling;
            if (prevIcon && prevIcon.classList.contains('input-group-text')) {
                const iconTag = prevIcon.querySelector('i');
                if (iconTag) {
                    iconTag.className = 'bi ' + icon;
                }
            }

            // Close modal
            const modalElement = document.getElementById('iconPickerModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
        }
    }

    document.getElementById('iconSearchInput').addEventListener('input', function(e) {
        renderIcons(e.target.value);
    });

    // Initial render
    renderIcons();
</script>
