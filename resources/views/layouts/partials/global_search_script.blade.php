{{-- Universal Instant Global Search Script & Styles --}}
<style>
.global-search-dropdown {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 14px;
  max-height: 480px;
  overflow-y: auto;
  z-index: 1080;
  box-shadow: 0 14px 40px rgba(0, 0, 0, 0.18) !important;
  font-family: inherit;
  direction: rtl;
  text-align: right;
  min-width: 320px;
}
html.theme-dark .global-search-dropdown,
[data-bs-theme="dark"] .global-search-dropdown,
body.theme-dark .global-search-dropdown {
  background: #1e293b !important;
  border-color: #334155 !important;
  color: #f8fafc !important;
  box-shadow: 0 14px 40px rgba(0, 0, 0, 0.5) !important;
}
.global-search-group-title {
  font-size: 0.76rem;
  font-weight: 700;
  padding: 8px 14px 5px;
  color: #475569;
  background: #f8fafc;
  border-bottom: 1px solid #edf2f7;
  border-top: 1px solid #edf2f7;
}
html.theme-dark .global-search-group-title,
[data-bs-theme="dark"] .global-search-group-title,
body.theme-dark .global-search-group-title {
  background: #0f172a !important;
  border-color: #334155 !important;
  color: #94a3b8 !important;
}
.global-search-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  text-decoration: none !important;
  color: #1e293b;
  border-bottom: 1px solid #f1f5f9;
  transition: background-color 0.15s ease, color 0.15s ease;
  cursor: pointer;
}
html.theme-dark .global-search-item,
[data-bs-theme="dark"] .global-search-item,
body.theme-dark .global-search-item {
  color: #f1f5f9 !important;
  border-color: #334155 !important;
}
.global-search-item:hover,
.global-search-item.active {
  background: #f0fdf4 !important;
  color: #0f172a !important;
}
html.theme-dark .global-search-item:hover,
html.theme-dark .global-search-item.active,
[data-bs-theme="dark"] .global-search-item:hover,
[data-bs-theme="dark"] .global-search-item.active,
body.theme-dark .global-search-item:hover,
body.theme-dark .global-search-item.active {
  background: #334155 !important;
  color: #ffffff !important;
}
.search-avatar-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 1.05rem;
}
.hero-search-wrap .global-search-dropdown {
  width: 100% !important;
  left: 0 !important;
  right: 0 !important;
}
</style>

<script>
(function() {
  function initGlobalSearch() {
    const searchInputs = document.querySelectorAll('.ensan-global-search-input');
    if (!searchInputs.length) return;

    searchInputs.forEach(input => {
      // Find or create dropdown sibling
      let dropdown = input.parentElement.querySelector('.global-search-dropdown');
      if (!dropdown) {
        dropdown = document.createElement('div');
        dropdown.className = 'global-search-dropdown shadow-lg d-none';
        input.parentElement.appendChild(dropdown);
      }

      let debounceTimer = null;
      let activeItemIndex = -1;

      function escapeHtml(str) {
        if (!str) return '';
        return String(str)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function highlightText(text, q) {
        if (!text || !q) return escapeHtml(text);
        const safeText = escapeHtml(text);
        const safeQ = escapeHtml(q).trim();
        if (!safeQ) return safeText;
        const regex = new RegExp(`(${safeQ.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return safeText.replace(regex, '<span class="bg-warning-subtle text-dark fw-bold px-0.5 rounded">$1</span>');
      }

      function closeDropdown() {
        dropdown.classList.add('d-none');
        dropdown.innerHTML = '';
        activeItemIndex = -1;
      }

      function performSearch(q) {
        q = q.trim();
        if (q.length < 2) {
          closeDropdown();
          return;
        }

        dropdown.classList.remove('d-none');
        dropdown.innerHTML = `
          <div class="p-3 text-center text-muted small">
            <span class="spinner-border spinner-border-sm text-primary me-1" role="status"></span>
            جاري البحث الشامل في الحالات والمتبرعين والموظفين...
          </div>
        `;

        fetch(`{{ route('api.global.search') }}?q=${encodeURIComponent(q)}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          if (!data || data.total === 0) {
            dropdown.innerHTML = `
              <div class="p-3.5 text-center text-muted small">
                <i class="bi bi-search fs-5 d-block mb-1 text-secondary opacity-50"></i>
                <div>لم نجد أي حالة أو متبرع أو موظف مطابق لـ "<strong>${escapeHtml(q)}</strong>"</div>
                <div class="mt-2">
                  <a href="${data ? data.reports_url : '{{ route("reports.index") }}?q=' + encodeURIComponent(q)}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                    البحث الموسع في التقارير العامة &larr;
                  </a>
                </div>
              </div>
            `;
            return;
          }

          let html = `
            <div class="d-flex align-items-center justify-content-between p-2 px-3 border-bottom bg-light bg-opacity-75">
              <span class="small fw-bold text-dark">
                <i class="bi bi-stars text-warning me-1"></i> نتائج البحث الشامل (${data.total})
              </span>
              <a href="${data.reports_url}" class="small text-primary text-decoration-none fw-semibold">
                عرض بالتقارير &larr;
              </a>
            </div>
          `;

          data.categories.forEach(cat => {
            if (!cat.items || !cat.items.length) return;

            html += `
              <div class="global-search-group-title d-flex align-items-center justify-content-between">
                <span><i class="bi ${cat.icon} ${cat.color} me-1.5"></i> ${cat.title}</span>
                <span class="badge rounded-pill bg-light text-dark border font-monospace" style="font-size:0.7rem;">${cat.count}</span>
              </div>
            `;

            cat.items.forEach(item => {
              html += `
                <a href="${item.url}" class="global-search-item" data-url="${item.url}">
                  <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                    <div class="search-avatar-wrap ${item.badge_class}">
                      <i class="bi ${item.icon}"></i>
                    </div>
                    <div class="overflow-hidden">
                      <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <span class="fw-bold text-truncate" style="font-size: 0.9rem;">${highlightText(item.title, q)}</span>
                        <span class="badge ${item.badge_class} border border-opacity-25 font-monospace" style="font-size: 0.72rem;">${highlightText(item.code, q)}</span>
                      </div>
                      <div class="small text-muted text-truncate mt-0.5" style="font-size: 0.78rem;">
                        ${highlightText(item.subtitle, q)}
                      </div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                    ${item.action_url ? `<a href="${item.action_url}" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;" onclick="event.stopPropagation();">${item.action_label}</a>` : ''}
                    <i class="bi bi-chevron-left text-muted small opacity-50"></i>
                  </div>
                </a>
              `;
            });
          });

          html += `
            <div class="p-2 border-top bg-light text-center small">
              <a href="${data.reports_url}" class="text-primary text-decoration-none fw-semibold">
                عرض كافة التقارير والعمليات لـ "${escapeHtml(q)}" &larr;
              </a>
            </div>
          `;

          dropdown.innerHTML = html;
          activeItemIndex = -1;
        })
        .catch(err => {
          console.error('Global search error:', err);
          dropdown.innerHTML = `
            <div class="p-3 text-center text-muted small">
              <span class="text-danger">حدث خطأ أثناء البحث، يرجى المحاولة لاحقاً.</span>
            </div>
          `;
        });
      }

      // Input event listener
      input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const val = this.value;
        debounceTimer = setTimeout(() => {
          performSearch(val);
        }, 220);
      });

      // Focus listener: re-search if input already has text
      input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
          performSearch(this.value);
        }
      });

      // Keyboard navigation
      input.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.global-search-item');
        if (!items.length || dropdown.classList.contains('d-none')) {
          if (e.key === 'Escape') closeDropdown();
          return;
        }

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          activeItemIndex = (activeItemIndex + 1) % items.length;
          updateActiveItem(items);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          activeItemIndex = (activeItemIndex - 1 + items.length) % items.length;
          updateActiveItem(items);
        } else if (e.key === 'Enter') {
          if (activeItemIndex >= 0 && items[activeItemIndex]) {
            e.preventDefault();
            items[activeItemIndex].click();
          }
        } else if (e.key === 'Escape') {
          closeDropdown();
        }
      });

      function updateActiveItem(items) {
        items.forEach((item, idx) => {
          item.classList.toggle('active', idx === activeItemIndex);
          if (idx === activeItemIndex) {
            item.scrollIntoView({ block: 'nearest' });
          }
        });
      }
    });

    // Close all dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.search-wrapper-elite') && !e.target.closest('.hero-search-wrap')) {
        document.querySelectorAll('.global-search-dropdown').forEach(d => {
          d.classList.add('d-none');
        });
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGlobalSearch);
  } else {
    initGlobalSearch();
  }
})();
</script>
