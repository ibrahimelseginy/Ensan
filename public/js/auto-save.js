/**
 * Ensan System - Global Auto-Save Drafts Engine (Full Auto-Restore)
 * Automatically saves all form progress per user/page/form (including Select2 labels
 * and conditional fields) and restores the entire form automatically on return.
 */
(function () {
  'use strict';

  const STORAGE_PREFIX = 'ensan_autosave_v1:';
  const DRAFT_TTL_MS = 7 * 24 * 60 * 60 * 1000; // 7 days
  const DEBOUNCE_MS = 600;

  const userId = document.querySelector('meta[name="ensan-user-id"]')?.getAttribute('content') || 'guest';

  const saveTimers = new WeakMap();
  const initializedForms = new WeakSet();
  const activeForms = new Set();
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
    const selectMeta = {};
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
      } else if (el.tagName === 'SELECT') {
        if (el.multiple) {
          const selected = Array.from(el.selectedOptions).map((opt) => ({
            value: opt.value,
            text: opt.text,
          }));
          if (selected.length > 0) hasValue = true;
          data[name] = selected.map((o) => o.value);
          selectMeta[name] = selected;
        } else {
          const val = el.value;
          if (val !== '' && val !== null && val !== undefined) {
            hasValue = true;
            const selectedOpt = el.options[el.selectedIndex];
            if (selectedOpt) {
              selectMeta[name] = [{ value: val, text: selectedOpt.text }];
            }
          }
          data[name] = val;
        }
      } else {
        const val = el.value;
        if (val !== '' && val !== null && val !== undefined) {
          hasValue = true;
        }
        data[name] = val;
      }
    });

    return { hasValue, data, selectMeta };
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
    if (!form || form.dataset.autosaveSubmitting === '1' || form.dataset.autosaveRestoring === '1' || form.dataset.autosaveDiscarded === '1') return;

    const { hasValue, data, selectMeta } = serializeForm(form);
    const key = getFormStorageKey(form);

    if (!hasValue) {
      try { localStorage.removeItem(key); } catch (e) {}
      return;
    }

    const payload = {
      savedAt: Date.now(),
      data,
      selectMeta,
    };

    try {
      localStorage.setItem(key, JSON.stringify(payload));
      if (!silent) {
        const timeStr = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
        showStatusPill(`تم الحفظ التلقائي للمسودة • ${timeStr}`, true);
      }
    } catch (e) {
      // Ignore storage quota errors
    }
  }

  function clearDraft(form) {
    const key = getFormStorageKey(form);
    try { localStorage.removeItem(key); } catch (e) {}
    const existingBanner = form.querySelector('.ensan-autosave-banner');
    if (existingBanner) existingBanner.remove();
  }

  function applyFieldValues(form, draftData, selectMeta) {
    const fields = getTrackableFields(form);

    fields.forEach((el) => {
      const name = el.name;
      if (!Object.prototype.hasOwnProperty.call(draftData, name)) return;

      const savedVal = draftData[name];
      const type = (el.type || '').toLowerCase();

      if (type === 'checkbox') {
        const shouldCheck = Array.isArray(savedVal)
          ? savedVal.includes(el.value || 'on')
          : Boolean(savedVal);
        if (el.checked !== shouldCheck) {
          el.checked = shouldCheck;
          el.dispatchEvent(new Event('change', { bubbles: true }));
        }
      } else if (type === 'radio') {
        const shouldCheck = el.value === String(savedVal);
        if (el.checked !== shouldCheck) {
          el.checked = shouldCheck;
          if (shouldCheck) {
            el.dispatchEvent(new Event('change', { bubbles: true }));
          }
        }
      } else if (el.tagName === 'SELECT') {
        const metaList = (selectMeta && Array.isArray(selectMeta[name])) ? selectMeta[name] : [];

        // Ensure AJAX / Select2 options exist in DOM before setting value
        metaList.forEach((meta) => {
          if (meta && meta.value !== '' && meta.value !== null && meta.value !== undefined) {
            const exists = Array.from(el.options).some((opt) => opt.value === String(meta.value));
            if (!exists) {
              const newOpt = new Option(meta.text || meta.value, meta.value, true, true);
              el.add(newOpt);
            }
          }
        });

        if (el.multiple && Array.isArray(savedVal)) {
          Array.from(el.options).forEach((opt) => {
            opt.selected = savedVal.includes(opt.value);
          });
        } else if (savedVal !== null && savedVal !== undefined && el.value !== String(savedVal)) {
          el.value = savedVal;
        }

        el.dispatchEvent(new Event('change', { bubbles: true }));
        if (window.jQuery && window.jQuery(el).data('select2')) {
          window.jQuery(el).trigger('change');
        }
      } else if (savedVal !== null && savedVal !== undefined) {
        if (el.value !== String(savedVal)) {
          el.value = savedVal;
          el.dispatchEvent(new Event('input', { bubbles: true }));
          el.dispatchEvent(new Event('change', { bubbles: true }));
        }
      }
    });
  }

  function autoRestoreDraft(form) {
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

    form.dataset.autosaveRestoring = '1';

    // Pass 1: Immediate full restore
    applyFieldValues(form, parsed.data, parsed.selectMeta || {});

    // Pass 2: Re-apply after conditional/JS-rendered fields finish opening
    setTimeout(() => {
      applyFieldValues(form, parsed.data, parsed.selectMeta || {});
      delete form.dataset.autosaveRestoring;
    }, 200);

    const savedDate = new Date(parsed.savedAt).toLocaleString('ar-EG', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });

    const banner = document.createElement('div');
    banner.className = 'ensan-autosave-banner alert alert-success d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3 mb-3 rounded-3 shadow-xs';
    banner.style.fontSize = '0.85rem';
    banner.innerHTML = `
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-cloud-check-fill fs-5 text-success"></i>
        <span><strong>تمت استعادة المسودة المحفوظة تلقائياً بالكامل</strong> (${savedDate})</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 ensan-autosave-discard">
          <i class="bi bi-trash me-1"></i>مسح المسودة والبدء من جديد
        </button>
      </div>
    `;

    banner.querySelector('.ensan-autosave-discard').addEventListener('click', () => {
      form.dataset.autosaveDiscarded = '1';
      activeForms.delete(form);
      const existingTimer = saveTimers.get(form);
      if (existingTimer) clearTimeout(existingTimer);
      clearDraft(form);
      window.location.reload();
    });

    form.insertBefore(banner, form.firstChild);
    showStatusPill('تمت استعادة المسودة المحفوظة تلقائياً بالكامل', true);
  }

  function bindForm(form) {
    if (initializedForms.has(form) || !isTrackableForm(form)) return;
    initializedForms.add(form);
    activeForms.add(form);

    autoRestoreDraft(form);

    const scheduleSave = () => {
      if (form.dataset.autosaveRestoring === '1') return;
      const existingTimer = saveTimers.get(form);
      if (existingTimer) clearTimeout(existingTimer);
      saveTimers.set(form, setTimeout(() => saveDraft(form, false), DEBOUNCE_MS));
    };

    form.addEventListener('input', scheduleSave);
    form.addEventListener('change', scheduleSave);

    // Also listen for jQuery/Select2 change events
    if (window.jQuery) {
      window.jQuery(form).on('change.ensanAutosave', 'select, input, textarea', scheduleSave);
    }

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

  // Flush any pending draft immediately before leaving or hiding tab
  window.addEventListener('beforeunload', () => {
    activeForms.forEach((form) => saveDraft(form, true));
  });
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') {
      activeForms.forEach((form) => saveDraft(form, true));
    }
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scanForms);
  } else {
    scanForms();
  }

  document.addEventListener('shown.bs.modal', scanForms);
})();
