/**
 * Ensan System - Universal Print Preview Engine
 * المحرك الشامل لمعاينة الطباعة لجميع صفحات ومستندات مؤسسة إنسان الخيرية
 */
(function (window, document) {
    'use strict';

    function showModalElement(modalEl) {
        if (!modalEl) return;

        // Try Bootstrap 5 first
        var openedWithBootstrap = false;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                var inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                if (inst) {
                    inst.show();
                    openedWithBootstrap = true;
                }
            } catch (err) {
                console.warn('Bootstrap modal show failed, falling back to direct display:', err);
            }
        }

        // Vanilla CSS fallback (ensures modal displays 100% of the time, even if Bootstrap fails)
        if (!openedWithBootstrap || !modalEl.classList.contains('show')) {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.style.backgroundColor = 'rgba(15, 23, 42, 0.75)';
            modalEl.style.zIndex = '99999';
            modalEl.removeAttribute('aria-hidden');
            modalEl.setAttribute('aria-modal', 'true');
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            // Ensure backdrop exists
            if (!document.querySelector('.modal-backdrop')) {
                var backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.style.zIndex = '99998';
                document.body.appendChild(backdrop);
            }
        }
    }

    function hideModalElement(modalEl) {
        if (!modalEl) return;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                var inst = bootstrap.Modal.getInstance(modalEl);
                if (inst) {
                    inst.hide();
                }
            } catch (e) {}
        }

        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        modalEl.style.backgroundColor = '';
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';

        var backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function (b) {
            b.remove();
        });
    }

    var EnsanPrintPreview = {
        currentScale: 1.0,
        currentOrientation: 'portrait',

        getModalElement: function () {
            return document.getElementById('universalPrintPreviewModal');
        },

        open: function (targetSelector, options) {
            options = options || {};

            // 1. If page has dedicated specialized modal (#printPreviewModal or #receiptPreviewModal) and no explicit target is given
            if (!targetSelector) {
                var donorModal = document.getElementById('printPreviewModal');
                if (donorModal) {
                    showModalElement(donorModal);
                    return;
                }

                var receiptModal = document.getElementById('receiptPreviewModal');
                if (receiptModal) {
                    showModalElement(receiptModal);
                    return;
                }
            }

            // 2. Otherwise use the universal modal
            var modalEl = this.getModalElement();
            if (!modalEl) {
                window.print();
                return;
            }

            // Locate target element
            var sourceEl = null;
            if (targetSelector) {
                sourceEl = document.querySelector(targetSelector);
            }

            if (!sourceEl) {
                sourceEl = document.querySelector('[data-printable]') ||
                           document.getElementById('printableDonorSheet') ||
                           document.getElementById('printableDonationVoucher') ||
                           document.getElementById('printableArea') ||
                           document.getElementById('printCard') ||
                           document.getElementById('tableContainer') ||
                           document.getElementById('donationsTableContainer') ||
                           document.querySelector('.table-responsive') ||
                           document.querySelector('.chart-container') ||
                           document.querySelector('.content-wrapper') ||
                           document.querySelector('main') ||
                           document.body;
            }

            if (!sourceEl) {
                window.print();
                return;
            }

            // Clone and sanitize
            var cloned = sourceEl.cloneNode(true);
            this.sanitizeClonedContent(cloned);

            // Update title / heading
            var heading = options.title;
            if (!heading) {
                var pageH1 = document.querySelector('h1, h2, h4, .page-header h4');
                heading = pageH1 ? pageH1.textContent.trim() : document.title;
            }
            heading = heading.replace(/\s+/g, ' ');

            var headingEl = document.getElementById('universalPreviewHeading');
            if (headingEl) {
                headingEl.textContent = heading;
            }

            var subtitleEl = document.getElementById('universalPreviewDocSubtitle');
            if (subtitleEl) {
                subtitleEl.textContent = 'معاينة رسمية — ' + heading;
            }

            // Inject into paper sheet body
            var paperBody = document.getElementById('universalPrintPaperBody');
            if (paperBody) {
                paperBody.innerHTML = '';
                paperBody.appendChild(cloned);
            }

            // Check Best Orientation
            var hasWideTable = cloned.querySelector('table') && (cloned.querySelectorAll('table th').length > 5);
            var initialOrientation = options.orientation || (hasWideTable ? 'landscape' : 'portrait');
            this.setOrientation(initialOrientation);

            // Reset zoom to 100%
            this.resetZoom();

            // Display modal
            showModalElement(modalEl);
        },

        close: function () {
            var modalEl = this.getModalElement();
            if (modalEl) {
                hideModalElement(modalEl);
            }
        },

        sanitizeClonedContent: function (el) {
            var selectorsToRemove = [
                '#universalPrintPreviewModal',
                '#receiptPreviewModal',
                '#printPreviewModal',
                '#toast-container',
                '.sidebar-fixed',
                '.navbar',
                '.btn',
                '.btn-group',
                'button',
                '.action-buttons',
                '.filter-card',
                '.chart-container form',
                '.no-print',
                '.d-print-none',
                '.dropdown',
                '.dropdown-menu',
                '.pagination',
                'footer',
                'nav',
                '.offcanvas'
            ];

            selectorsToRemove.forEach(function (sel) {
                var items = el.querySelectorAll(sel);
                items.forEach(function (item) {
                    item.remove();
                });
            });

            // Clean scroll and fixed heights on tables and wrappers so all rows show
            var scrollElements = el.querySelectorAll('.table-responsive, [style*="overflow"], [style*="max-height"]');
            scrollElements.forEach(function (item) {
                item.style.overflow = 'visible';
                item.style.maxHeight = 'none';
                item.style.height = 'auto';
            });

            // Remove box-shadows and ensure text visibility
            var cards = el.querySelectorAll('.card, .kpi-card, .stat-card, .chart-container');
            cards.forEach(function (card) {
                card.style.boxShadow = 'none';
                card.style.background = '#ffffff';
                card.style.color = '#0f172a';
            });
        },

        setOrientation: function (orientation) {
            this.currentOrientation = orientation;
            var paper = document.getElementById('universalPrintPaper');
            var btnPortrait = document.getElementById('btnPreviewPortrait');
            var btnLandscape = document.getElementById('btnPreviewLandscape');

            if (!paper) return;

            if (orientation === 'landscape') {
                paper.classList.remove('portrait');
                paper.classList.add('landscape');
                if (btnPortrait) btnPortrait.classList.remove('active');
                if (btnLandscape) btnLandscape.classList.add('active');
            } else {
                paper.classList.remove('landscape');
                paper.classList.add('portrait');
                if (btnPortrait) btnPortrait.classList.add('active');
                if (btnLandscape) btnLandscape.classList.remove('active');
            }
        },

        zoom: function (delta) {
            this.currentScale = Math.min(Math.max(0.5, this.currentScale + delta), 1.6);
            this.applyZoom();
        },

        resetZoom: function () {
            this.currentScale = 1.0;
            this.applyZoom();
        },

        applyZoom: function () {
            var paper = document.getElementById('universalPrintPaper');
            var zoomLabel = document.getElementById('previewZoomLevel');
            if (paper) {
                paper.style.transform = 'scale(' + this.currentScale + ')';
            }
            if (zoomLabel) {
                zoomLabel.textContent = Math.round(this.currentScale * 100) + '%';
            }
        },

        executePrint: function () {
            var donorSheet = document.getElementById('printableDonorSheet');
            if (donorSheet) {
                printHtmlDocument(donorSheet.outerHTML, 'تقرير ملف وكشف حساب المتبرع', false);
                return;
            }

            var voucher = document.getElementById('printableDonationVoucher');
            if (voucher) {
                printHtmlDocument(voucher.outerHTML, 'إيصال استلام تبرع', true);
                return;
            }

            var paper = document.getElementById('universalPrintPaper');
            var modalEl = this.getModalElement();
            if (paper && modalEl && modalEl.classList.contains('show')) {
                printHtmlDocument(paper.outerHTML, document.title || 'وثيقة رسمية - مؤسسة إنسان', this.currentOrientation === 'landscape');
                return;
            }

            var isUniversalOpen = false;
            if (modalEl && modalEl.classList.contains('show')) {
                isUniversalOpen = true;
            }

            if (isUniversalOpen) {
                document.body.classList.add('printing-universal-preview');
                if (this.currentOrientation === 'landscape') {
                    document.body.classList.add('is-landscape');
                }
            }

            var cleanup = function () {
                document.body.classList.remove('printing-universal-preview');
                document.body.classList.remove('is-landscape');
            };

            window.addEventListener('afterprint', cleanup, { once: true });
            setTimeout(cleanup, 4000);

            window.print();
        }
    };

    function printHtmlDocument(htmlContent, title, isLandscape) {
        title = title || document.title || 'مؤسسة إنسان الخيرية - طباعة';
        isLandscape = Boolean(isLandscape);

        var iframe = document.getElementById('ensan-isolated-print-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'ensan-isolated-print-iframe';
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            iframe.style.visibility = 'hidden';
            iframe.style.zIndex = '-99999';
            document.body.appendChild(iframe);
        }

        var doc = iframe.contentWindow.document;
        doc.open();
        doc.write('<!DOCTYPE html>' +
            '<html dir="rtl" lang="ar">' +
            '<head>' +
            '<meta charset="utf-8">' +
            '<title>' + title + '</title>' +
            '<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">' +
            '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">' +
            '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">' +
            '<style>' +
            '@page { size: ' + (isLandscape ? 'A4 landscape' : 'A4 portrait') + '; margin: 6mm 8mm; }' +
            '* { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; box-sizing: border-box !important; }' +
            'html, body { margin: 0 !important; padding: 0 !important; background: #ffffff !important; color: #0f172a !important; font-family: "IBM Plex Sans Arabic", system-ui, -apple-system, sans-serif !important; direction: rtl; font-size: 12.5px; line-height: 1.5; }' +
            '/* Standalone Grid & Flex Rules (Independent of external CDN) */' +
            '.d-flex { display: flex !important; }' +
            '.justify-content-between { justify-content: space-between !important; }' +
            '.justify-content-center { justify-content: center !important; }' +
            '.align-items-center { align-items: center !important; }' +
            '.align-items-end { align-items: flex-end !important; }' +
            '.row { display: flex !important; flex-wrap: wrap !important; margin-left: -6px !important; margin-right: -6px !important; }' +
            '.row > * { box-sizing: border-box !important; padding-left: 6px !important; padding-right: 6px !important; }' +
            '.col-4 { flex: 0 0 33.333333% !important; max-width: 33.333333% !important; width: 33.333333% !important; }' +
            '.col-6 { flex: 0 0 50% !important; max-width: 50% !important; width: 50% !important; }' +
            '.col-12 { flex: 0 0 100% !important; max-width: 100% !important; width: 100% !important; }' +
            '.text-center { text-align: center !important; }' +
            '.text-start { text-align: right !important; }' +
            '.text-end { text-align: left !important; }' +
            '#printableDonorSheet, .a4-paper-sheet, #printableDonationVoucher { width: 100% !important; max-width: 100% !important; margin: 0 auto !important; padding: 18px !important; border: 2px solid #059669 !important; border-radius: 6px !important; box-shadow: none !important; background: #ffffff !important; color: #0f172a !important; transform: none !important; }' +
            '.text-dark { color: #0f172a !important; }' +
            '.text-muted { color: #64748b !important; }' +
            '.text-success { color: #047857 !important; }' +
            '.text-primary { color: #1d4ed8 !important; }' +
            '.text-warning { color: #b45309 !important; }' +
            '.bg-light { background-color: #f8fafc !important; }' +
            '.table { color: #0f172a !important; border-color: #cbd5e1 !important; width: 100% !important; margin-bottom: 0 !important; border-collapse: collapse !important; }' +
            '.table th, .table td { padding: 6px 8px !important; border: 1px solid #cbd5e1 !important; }' +
            '.table-light th, .table-light td { background-color: #f1f5f9 !important; color: #0f172a !important; font-weight: 700 !important; }' +
            '.table-bordered th, .table-bordered td { border: 1px solid #cbd5e1 !important; }' +
            '.badge { display: inline-block; padding: 0.35em 0.65em; font-size: 0.75em; font-weight: 700; line-height: 1; text-align: center; border-radius: 0.375rem; }' +
            '.bg-success-subtle { background-color: #d1fae5 !important; color: #065f46 !important; }' +
            '.bg-info-subtle { background-color: #e0f2fe !important; color: #075985 !important; }' +
            '</style>' +
            '</head>' +
            '<body style="padding: 4px;">' +
            htmlContent +
            '</body>' +
            '</html>');
        doc.close();

        setTimeout(function () {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }, 450);
    }

    // Global Click Delegation for Modals & Preview Buttons
    document.addEventListener('click', function (e) {
        // 1. Close buttons inside modals
        var dismissBtn = e.target.closest('[data-bs-dismiss="modal"], .btn-close');
        if (dismissBtn) {
            var parentModal = dismissBtn.closest('.modal');
            if (parentModal) {
                hideModalElement(parentModal);
            }
        }

        // 2. Preview Triggers
        var previewBtn = e.target.closest('.btn-print-preview, [data-print-preview]');
        if (previewBtn) {
            e.preventDefault();
            var target = previewBtn.getAttribute('data-print-target') || previewBtn.getAttribute('data-bs-target');
            if (target && target.startsWith('#') && document.querySelector(target)) {
                var el = document.querySelector(target);
                if (el.classList.contains('modal')) {
                    showModalElement(el);
                    return;
                }
            }
            EnsanPrintPreview.open(target);
        }

        // 3. Fallback for data-bs-toggle="modal" if Bootstrap failed to initialize
        var modalToggle = e.target.closest('[data-bs-toggle="modal"]');
        if (modalToggle) {
            var modalTargetId = modalToggle.getAttribute('data-bs-target') || modalToggle.getAttribute('href');
            if (modalTargetId && modalTargetId.startsWith('#')) {
                var targetModal = document.querySelector(modalTargetId);
                if (targetModal) {
                    // Give Bootstrap a tiny chance, and if not shown, show it
                    setTimeout(function () {
                        if (!targetModal.classList.contains('show')) {
                            showModalElement(targetModal);
                        }
                    }, 50);
                }
            }
        }
    });

    // Keyboard shortcut: Ctrl+P / Cmd+P interception to open preview
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P')) {
            if (e.shiftKey) return; // Allow native print with Shift
            e.preventDefault();
            EnsanPrintPreview.open();
        }
    });

    // Expose helpers globally
    window.showModalElement = showModalElement;
    window.hideModalElement = hideModalElement;
    window.EnsanPrintPreview = EnsanPrintPreview;
    window.printHtmlDocument = printHtmlDocument;
    window.openPrintPreview = function (selector, options) {
        EnsanPrintPreview.open(selector, options);
    };

})(window, document);
