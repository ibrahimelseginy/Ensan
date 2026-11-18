/**
 * Ensan System - Global Auto-Save Drafts Engine
 * Automatically saves form progress locally per user/page/form to prevent data loss
 * on accidental refresh, browser crash, or network drop.
 */
(function () {
  'use strict';

  const STORAGE_PREFIX = 'ensan_autosave_v1:';
  const DRAFT_TTL_MS = 7 * 24 * 60 * 60 * 1000; // 7 days
  const DEBOUNCE_MS = 800;

  const userId = document.querySelector('meta[name="ensan-user-id"]')?.getAttribute('content') || 'guest';

  const saveTimers = new WeakMap();
  const initializedForms = new WeakSet();
  let statusPill = null;
  let statusHideTimer = null;

  function isTrackableForm(form) {
    if (!form || form.tagName !== 'FORM') return false;
    if (form.hasAttribute('data-no-autosave')) return false;

    const method = (form.getAttribute('method') || 'GET').toUpperCase();
    if (method === 'GET') return false;

    const action = (form.getAttribute('action') || '').toLowerCase();
    if (action.includes('logout') || action.includes('login') || action.includes('password')) {
      return false;
    }

    const methodOverride = (form.querySelector('input[name="_method"]')?.value || '').toUpperCase();
    if (methodOverride === 'DELETE') return false;

    // Must have at least 2 interactive non-hidden/non-password/non-file fields
    const fields = getTrackableFields(form);
    return fields.length >= 2;
  }

  function getTrackableFields(form) {
    const elements = Array.from(form.querySelectorAll('input, textarea, select'));
    return elements.filter((el) => {
      if (!el.name) return false;
      if (el.name === '_token' || el.name === '_method') return false;
      if (el.disabled || el.readOnly) return false;
      if (el.hasAttribute('data-no-autosave')) return false;

      const type = (el.type || '').toLowerCase();
      if (['hidden', 'password', 'file', 'submit', 'button', 'reset', 'image'].includes(type)) {
        return false;
      }
      return true;
    });
  }

  function getFormStorageKey(form) {
    const formId = form.getAttribute('id') || '';
    const action = form.getAttribute('action') || window.location.pathname;
    const cleanAction = action.replace(/^https?:\/\/[^/]+/, '');
    const identifier = formId ? `#${formId}` : cleanAction;
    return `${STORAGE_PREFIX}${userId}:${window.location.pathname}:${identifier}`;
  }

  function serializeForm(form) {
    const fields = getTrackableFields(form);
    const data = {};
    let hasValue = false;

    fields.forEach((el) => {
      const name = el.name;
      const type = (el.type || '').toLowerCase();

      if (type === 'checkbox') {
        if (!Array.isArray(data[name])) {
          data[name] = [];
        }
        if (el.checked) {
          data[name].push(el.value || 'on');
          hasValue = true;
        }
      } else if (type === 'radio') {
        if (el.checked) {
          data[name] = el.value;
          hasValue = true;
        }
      } else if (el.tagName === 'SELECT' && el.multiple) {
        const selected = Array.from(el.selectedOptions).map((opt) => opt.value);
        if (selected.length > 0) hasValue = true;
        data[name] = selected;
      } else {
        const val = el.value;
        if (val !== '' && val !== null && val !== undefined) {
          hasValue = true;
        }
        data[name] = val;
      }
    });

    return { hasValue, data };
  }

  function showStatusPill(message, isSuccess = true) {
    if (!statusPill) {
      statusPill = document.createElement('div');
      statusPill.id = 'ensanAutoSavePill';
      statusPill.style.cssText = [
        'position:fixed',
        'bottom:18px',
        'left:18px',
        'z-index:1060',
        'display:inline-flex',
        'align-items:center',
        'gap:8px',
        'padding:8px 14px',
        'border-radius:999px',
        'font-size:12px',
        'font-weight:700',
        'box-shadow:0 8px 24px rgba(15,23,42,0.18)',
        'transition:opacity 0.25s ease, transform 0.25s ease',
        'pointer-events:none',
        'opacity:0',
        'transform:translateY(8px)',
        'direction:rtl',
      ].join(';');
      document.body.appendChild(statusPill);
    }

    statusPill.style.background = isSuccess ? '#065f46' : '#334155';
    statusPill.style.color = '#ffffff';
    statusPill.innerHTML = `<i class="bi ${isSuccess ? 'bi-cloud-check-fill' : 'bi-cloud-arrow-up-fill'}"></i><span>${message}</span>`;

    requestAnimationFrame(() => {
      statusPill.style.opacity = '1';
      statusPill.style.transform = 'translateY(0)';
    });

    clearTimeout(statusHideTimer);
    statusHideTimer = setTimeout(() => {
      if (statusPill) {
        statusPill.style.opacity = '0';
        statusPill.style.transform = 'translateY(8px)';
      }
    }, 2600);
  }

  function saveDraft(form, silent = false) {
    if (form.dataset.autosaveSubmitting === '1') return;

    const { hasValue, data } = serializeForm(form);
    const key = getFormStorageKey(form);

    if (!hasValue) {
      try { localStorage.removeItem(key); } catch (e) {}
      return;
    }

    const payload = {
      savedAt: Date.now(),
      data,
    };

    try {
      localStorage.setItem(key, JSON.stringify(payload));
      if (!silent) {
        const timeStr = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
        showStatusPill(`تم الحفظ التلقائي للمسودة • ${timeStr}`, true);
      }
    } catch (e) {
      // Ignore quota exceeded errors gracefully
    }
  }

  function clearDraft(form) {
    const key = getFormStorageKey(form);
    try { localStorage.removeItem(key); } catch (e) {}
    const existingBanner = form.querySelector('.ensan-autosave-banner');
    if (existingBanner) existingBanner.remove();
  }

  function restoreDraft(form, draftData) {
    const fields = getTrackableFields(form);

    fields.forEach((el) => {
      const name = el.name;
      if (!Object.prototype.hasOwnProperty.call(draftData, name)) return;

      const savedVal = draftData[name];
      const type = (el.type || '').toLowerCase();

      if (type === 'checkbox') {
        if (Array.isArray(savedVal)) {
          el.checked = savedVal.includes(el.value || 'on');
        } else {
          el.checked = Boolean(savedVal);
        }
      } else if (type === 'radio') {
        el.checked = el.value === String(savedVal);
      } else if (el.tagName === 'SELECT' && el.multiple && Array.isArray(savedVal)) {
        Array.from(el.options).forEach((opt) => {
          opt.selected = savedVal.includes(opt.value);
        });
      } else if (savedVal !== null && savedVal !== undefined) {
        el.value = savedVal;
      }

      el.dispatchEvent(new Event('input', { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
      if (window.jQuery && window.jQuery(el).data('select2')) {
        window.jQuery(el).trigger('change');
      }
    });

    showStatusPill('تمت استعادة المسودة المحفوظة بنجاح', true);
  }

  function checkAndOfferRestore(form) {
    if (form.querySelector('.ensan-autosave-banner')) return;

    const key = getFormStorageKey(form);
    let raw = null;
    try { raw = localStorage.getItem(key); } catch (e) { return; }
    if (!raw) return;

    let parsed = null;
    try { parsed = JSON.parse(raw); } catch (e) {
      try { localStorage.removeItem(key); } catch (err) {}
      return;
    }

    if (!parsed || !parsed.savedAt || !parsed.data || (Date.now() - parsed.savedAt) > DRAFT_TTL_MS) {
      try { localStorage.removeItem(key); } catch (e) {}
      return;
    }

    const savedDate = new Date(parsed.savedAt).toLocaleString('ar-EG', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });

    const banner = document.createElement('div');
    banner.className = 'ensan-autosave-banner alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3 mb-3 rounded-3 shadow-xs';
    banner.style.fontSize = '0.86rem';
    banner.innerHTML = `
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-clock-history fs-5 text-primary"></i>
        <span><strong>توجد مسودة غير مرسلة</strong> محفوظة تلقائياً (${savedDate}). هل ترغب في استعادتها؟</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-primary fw-bold rounded-pill px-3 ensan-autosave-restore">
          <i class="bi bi-arrow-counterclockwise me-1"></i>استعادة المسودة
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 ensan-autosave-discard">
          تجاهل وحذف
        </button>
      </div>
    `;

    banner.querySelector('.ensan-autosave-restore').addEventListener('click', () => {
      restoreDraft(form, parsed.data);
      banner.remove();
    });

    banner.querySelector('.ensan-autosave-discard').addEventListener('click', () => {
      clearDraft(form);
    });

    form.insertBefore(banner, form.firstChild);
  }

  function bindForm(form) {
    if (initializedForms.has(form) || !isTrackableForm(form)) return;
    initializedForms.add(form);

    checkAndOfferRestore(form);

    const scheduleSave = () => {
      const existingTimer = saveTimers.get(form);
      if (existingTimer) clearTimeout(existingTimer);
      saveTimers.set(form, setTimeout(() => saveDraft(form, false), DEBOUNCE_MS));
    };

    form.addEventListener('input', scheduleSave);
    form.addEventListener('change', scheduleSave);

    form.addEventListener('submit', () => {
      form.dataset.autosaveSubmitting = '1';
      const existingTimer = saveTimers.get(form);
      if (existingTimer) clearTimeout(existingTimer);
      clearDraft(form);
    });
  }

  function scanForms() {
    document.querySelectorAll('form').forEach(bindForm);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scanForms);
  } else {
    scanForms();
  }

  document.addEventListener('shown.bs.modal', scanForms);
})();
