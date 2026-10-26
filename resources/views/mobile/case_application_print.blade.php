<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استمارة بحث مبدئي #{{ $application->id }} - {{ $application->applicant_name }} | مؤسسة إنسان الخيرية</title>
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --border-color: #cbd5e1;
            --bg-light: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Cairo', system-ui, -apple-system, sans-serif;
            background: #e2e8f0;
            color: var(--text-dark);
            line-height: 1.5;
            padding: 24px;
            direction: rtl;
        }

        .print-actions-bar {
            max-width: 820px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .printable-sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px 36px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
        }

        /* Header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .doc-brand h2 {
            font-size: 1.4rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .doc-brand p {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .doc-meta {
            text-align: left;
        }

        .doc-badge {
            display: inline-block;
            background: #ecfdf5;
            color: var(--primary);
            border: 1px solid #a7f3d0;
            padding: 4px 12px;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 0.82rem;
            margin-bottom: 4px;
        }

        .doc-id {
            font-family: 'Outfit', sans-serif;
            font-size: 0.92rem;
            font-weight: 800;
            color: var(--text-muted);
        }

        /* Section */
        .section-box {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .section-title {
            background: var(--bg-light);
            padding: 8px 14px;
            font-size: 0.88rem;
            font-weight: 800;
            color: var(--primary-dark);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-body {
            padding: 12px 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 16px;
            font-size: 0.84rem;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }

        .info-label {
            font-weight: 700;
            color: var(--text-muted);
            min-width: 110px;
        }

        .info-val {
            font-weight: 700;
            color: var(--text-dark);
            flex-grow: 1;
        }

        .font-mono {
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.5px;
        }

        .narrative-text {
            font-size: 0.84rem;
            line-height: 1.6;
            color: #1e293b;
            background: #f8fafc;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px dashed #cbd5e1;
            white-space: pre-line;
        }

        /* Status Banner */
        .status-box {
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-approved {
            background: #ecfdf5;
            color: #065f46;
            border: 1.5px solid #a7f3d0;
        }
        .status-rejected {
            background: #fef2f2;
            color: #991b1b;
            border: 1.5px solid #fecaca;
        }
        .status-pending {
            background: #fffbeb;
            color: #92400e;
            border: 1.5px solid #fde68a;
        }
        .status-research {
            background: #f0f9ff;
            color: #0369a1;
            border: 1.5px solid #bae6fd;
        }

        /* Signatures */
        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px dashed var(--border-color);
            text-align: center;
            font-size: 0.8rem;
        }

        .sig-title {
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 36px;
        }

        .sig-line {
            font-weight: 700;
            color: var(--text-dark);
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
        }

        .stamp-box {
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 700;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-actions-bar {
                display: none !important;
            }
            .printable-sheet {
                max-width: 100% !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
            }
            .section-box, .signatures-grid {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }
        }
    </style>
</head>
<body>

    {{-- Top Action Bar (Hidden on Print) --}}
    <div class="print-actions-bar">
        <div>
            <strong>استمارة بحث مبدئي رقم: #{{ $application->id }}</strong>
            <span class="text-muted" style="font-size: 0.82rem; margin-right: 8px;">— {{ $application->applicant_name }}</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer-fill"></i> طباعة فورية (Print)
            </button>
            <button type="button" class="btn btn-secondary" onclick="window.close()">
                <i class="bi bi-x-lg"></i> إغلاق النافذة
            </button>
        </div>
    </div>

    {{-- Official Sheet --}}
    <div class="printable-sheet">
        {{-- Document Header --}}
        <div class="doc-header">
            <div class="doc-brand">
                <h2>مؤسسة إنسان الخيرية</h2>
                <p>إدارة البحث الميداني والرعاية الاجتماعية — استمارة تسجيل وبحث مبدئي</p>
            </div>
            <div class="doc-meta">
                <div class="doc-badge">{{ $application->case_type_label }}</div>
                <div class="doc-id">رقم القيد: #{{ $application->id }}</div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">تاريخ التقديم: {{ $application->created_at->format('Y-m-d') }}</div>
            </div>
        </div>

        {{-- Section 1: Demographics & Contact --}}
        <div class="section-box">
            <div class="section-title">
                <i class="bi bi-person-vcard"></i> البيانات الشخصية ومعلومات الاتصال
            </div>
            <div class="section-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">اسم صاحب الحالة:</span>
                        <span class="info-val">{{ $application->applicant_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">الرقم القومي:</span>
                        <span class="info-val font-mono">{{ $application->national_id ?? 'غير مسجل' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">رقم الهاتف الأساسي:</span>
                        <span class="info-val font-mono">{{ $application->applicant_phone }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">هاتف بديل / إضافي:</span>
                        <span class="info-val font-mono">{{ $application->backup_phone ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">المحافظة والمركز:</span>
                        <span class="info-val">{{ $application->governorate ?? '—' }} - {{ $application->city ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">مصدر التقديم:</span>
                        <span class="info-val">{{ $application->source_badge['label'] }}</span>
                    </div>
                    <div class="info-item" style="grid-column: span 2;">
                        <span class="info-label">العنوان بالتفصيل:</span>
                        <span class="info-val">{{ $application->address ?? 'غير محدد' }}</span>
                    </div>
                </div>

                @if($application->submitter_name)
                    <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #cbd5e1; font-size: 0.8rem; color: #334155;">
                        <strong>مُقدم الطلب بالنيابة:</strong> {{ $application->submitter_name }} ({{ $application->submitter_relation ?? 'صلة القرابة غير محددة' }}) — هاتف: {{ $application->submitter_phone ?? '—' }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Section 2: Narrative & Family Circumstances --}}
        <div class="section-box">
            <div class="section-title">
                <i class="bi bi-chat-left-text"></i> شرح الحالة وظروف الأسرة المعيشية
            </div>
            <div class="section-body">
                <div class="narrative-text">{{ $application->description }}</div>
            </div>
        </div>

        {{-- Section 3: Specialized Project Data (If Available) --}}
        @if($application->case_type === 'zad' || $application->deceased_husband_name || $application->children_count)
            <div class="section-box">
                <div class="section-title">
                    <i class="bi bi-basket2"></i> بيانات استمارة مشروع زاد (كفالة ورعاية الأيتام)
                </div>
                <div class="section-body">
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">اسم الأم:</span><span class="info-val">{{ $application->mother_name ?? $application->applicant_name }}</span></div>
                        <div class="info-item"><span class="info-label">الزوج المتوفي:</span><span class="info-val">{{ $application->deceased_husband_name ?? '—' }}</span></div>
                        <div class="info-item"><span class="info-label">عدد الأطفال:</span><span class="info-val">{{ $application->children_count ?? '—' }}</span></div>
                        <div class="info-item"><span class="info-label">تفاصيل الوفاة:</span><span class="info-val">{{ $application->death_details ?? '—' }}</span></div>
                        @if($application->children_details)
                            <div class="info-item" style="grid-column: span 2;"><span class="info-label">بيانات الأطفال:</span><span class="info-val">{{ $application->children_details }}</span></div>
                        @endif
                        @if($application->income_aid_sources)
                            <div class="info-item" style="grid-column: span 2;"><span class="info-label">المعاشات والدخل:</span><span class="info-val">{{ $application->income_aid_sources }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($application->case_type === 'hope' || $application->tumor_location || $application->hospital_name)
            <div class="section-box">
                <div class="section-title">
                    <i class="bi bi-heart-pulse"></i> بيانات استمارة مشروع بعثاء الأمل (رعاية مرضى الأورام)
                </div>
                <div class="section-body">
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">المستشفى المتابع بها:</span><span class="info-val">{{ $application->hospital_name ?? '—' }}</span></div>
                        <div class="info-item"><span class="info-label">مكان ونوع الورم:</span><span class="info-val">{{ $application->tumor_location ?? '—' }}</span></div>
                        @if($application->treatment_protocol)
                            <div class="info-item" style="grid-column: span 2;"><span class="info-label">بروتوكول العلاج:</span><span class="info-val">{{ $application->treatment_protocol }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($application->case_type === 'midrar' || $application->need_type)
            <div class="section-box">
                <div class="section-title">
                    <i class="bi bi-droplet"></i> بيانات استمارة مشروع مدرار (وصلات مياه وأسقف)
                </div>
                <div class="section-body">
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">نوع الاحتياج:</span><span class="info-val">{{ $application->need_type ?? '—' }}</span></div>
                        <div class="info-item"><span class="info-label">عدد أفراد المنزل:</span><span class="info-val">{{ $application->family_members_count ?? '—' }}</span></div>
                        @if($application->guide_name)
                            <div class="info-item" style="grid-column: span 2;"><span class="info-label">بيانات الدليل:</span><span class="info-val">{{ $application->guide_name }} ({{ $application->guide_phone ?? '—' }})</span></div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Section 4: Initial Research Decision & Status --}}
        @php
            $statusClass = match($application->status) {
                'approved', 'completed', 'تم البحث', 'accepted' => 'status-approved',
                'rejected', 'مرفوض' => 'status-rejected',
                'under_research', 'تحت البحث', 'reviewed' => 'status-research',
                default => 'status-pending'
            };
        @endphp
        <div class="section-box">
            <div class="section-title">
                <i class="bi bi-shield-check"></i> نتيجة وقرار البحث المبدئي
            </div>
            <div class="section-body">
                <div class="status-box {{ $statusClass }}">
                    <span>حالة الطلب: <strong>{{ $application->status_label }}</strong></span>
                    <span>كود المرجع: #{{ $application->id }}</span>
                </div>

                @if($application->status === 'rejected' && $application->rejection_reason)
                    <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 10px 12px; border-radius: 6px; font-size: 0.84rem; margin-bottom: 10px;">
                        <strong style="color: #dc2626;">سبب عدم القبول / الرفض:</strong>
                        <div style="color: #7f1d1d; margin-top: 4px;">{{ $application->rejection_reason }}</div>
                    </div>
                @endif

                @if($application->admin_notes)
                    <div style="font-size: 0.82rem; color: #475569; background: #f8fafc; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <strong>ملاحظات وتوجيهات إدارية:</strong> {{ $application->admin_notes }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signatures-grid">
            <div>
                <div class="sig-title">باحث الحالة / المستلم</div>
                <div class="sig-line">التوقيع والاعتماد</div>
            </div>
            <div>
                <div class="sig-title">مدير إدارة البحث الاجتماعي</div>
                <div class="sig-line">التوقيع والاعتماد</div>
            </div>
            <div>
                <div class="stamp-box">
                    خاتم مؤسسة إنسان
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto open print dialog if requested
            if (window.location.search.includes('autoprint=1')) {
                window.print();
            }
        });
    </script>
</body>
</html>
