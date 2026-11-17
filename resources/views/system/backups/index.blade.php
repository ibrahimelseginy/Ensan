@extends('layouts.app')
@section('content')
<div class="container-fluid p-0">
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #0f766e 100%);">
        <div class="hero-content">
            <div class="hero-greeting">أمان النظام والبيانات 🛡️</div>
            <h1 class="hero-title">النسخ الاحتياطي والاحتفاظ بنسخة</h1>
            <p class="hero-subtitle">إنشاء وحفظ النسخ الاحتياطية المشفرة (AES-256-GCM + HMAC)، تنزيل نسخة للاحتفاظ الخارجي، وضبط سياسة التدوير والاستعادة الآمنة</p>
            <div class="hero-actions d-flex flex-wrap gap-2">
                <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-light text-dark fw-bold rounded-pill px-4">
                    <i class="bi bi-grid-fill text-success me-1"></i> الرئيسية
                </a>
                <a href="{{ route('audits.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-4">
                    <i class="bi bi-journal-text me-1"></i> سجل التدقيق
                </a>
            </div>
        </div>
        <i class="bi bi-shield-lock-fill hero-icon d-none d-md-block"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mt-3">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mt-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mt-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="row g-3 mt-1 mb-4">
        <div class="col-md-6 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="text-muted small mb-1"><i class="bi bi-archive-fill text-primary me-1"></i> إجمالي النسخ المكتملة</div>
                    <div class="h3 fw-bolder mb-0">{{ $stats['total_count'] }}</div>
                    <small class="text-muted">محفوظة في خزنة خاصة خارج الويب</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="text-muted small mb-1"><i class="bi bi-pin-angle-fill text-warning me-1"></i> النسخ المثبتة (محمية)</div>
                    <div class="h3 fw-bolder mb-0 text-warning">{{ $stats['pinned_count'] }}</div>
                    <small class="text-muted">لا تُحذف بالتدوير التلقائي</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Actions Grid --}}
    <div class="row g-4 mb-4">
        {{-- Create Backup Card --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle-fill text-success me-2"></i>إنشاء نسخة احتياطية جديدة</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('backups.store') }}" id="createBackupForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">نطاق النسخة الاحتياطية</label>
                            <select name="scope" class="form-select" required>
                                <option value="db_only">قاعدة البيانات فقط (سريعة — جميع الجداول والبيانات)</option>
                                <option value="full">نسخة شاملة للنظام (قاعدة البيانات + المرفقات والوثائق الخاصة والعامة + الإعدادات)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">كلمة مرور تشفير إضافية (اختياري)</label>
                            <input type="password" name="passphrase" class="form-control" minlength="8" autocomplete="new-password"
                                   placeholder="اتركه فارغاً لاستخدام مفتاح الخزنة المشفر التلقائي">
                            <div class="form-text">إذا أدخلت كلمة مرور خاصة، لن تُستعاد النسخة إلا بها (مفيدة للنسخ الخارجية المحمولة).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">ملاحظات / سبب أخذ النسخة</label>
                            <input type="text" name="notes" class="form-control" maxlength="500" placeholder="مثال: قبل إغلاق الشهر المالي أو قبل التحديث">
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="pin" value="1" id="pinNewBackup" checked>
                            <label class="form-check-label fw-semibold" for="pinNewBackup">
                                تثبيت النسخة (الاحتفاظ بها دائماً ومنع حذفها تلقائياً)
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold py-2 rounded-3" id="btnRunBackup">
                            <i class="bi bi-shield-fill-check me-1"></i> إنشاء النسخة الاحتياطية الآن
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Retention & Schedule Policy Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-sliders text-primary me-2"></i>سياسة الاحتفاظ والجدولة</h6>
                    <form method="POST" action="{{ route('backups.prune') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 0.75rem;"
                                title="تنفيذ التدوير وحذف النسخ غير المثبتة المنتهية">
                            <i class="bi bi-recycle me-1"></i> تدوير الآن
                        </button>
                    </form>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('backups.settings.update') }}">
                        @csrf
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="auto_enabled" value="1" id="autoEnabledSwitch" @checked($settings->auto_enabled)>
                            <label class="form-check-label fw-bold" for="autoEnabledSwitch">تفعيل النسخ الاحتياطي التلقائي المجدول</label>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">التكرار</label>
                                <select name="frequency" class="form-select form-select-sm">
                                    <option value="daily" @selected($settings->frequency === 'daily')>يومياً</option>
                                    <option value="weekly" @selected($settings->frequency === 'weekly')>أسبوعياً</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">وقت التنفيذ</label>
                                <input type="time" name="schedule_time" class="form-control form-control-sm" value="{{ $settings->schedule_time }}">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">النطاق الافتراضي</label>
                                <select name="default_scope" class="form-select form-select-sm">
                                    <option value="db_only" @selected($settings->default_scope === 'db_only')>قاعدة البيانات فقط</option>
                                    <option value="full" @selected($settings->default_scope === 'full')>نسخة شاملة</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">سقف الخزنة (MB)</label>
                                <input type="number" name="max_storage_mb" class="form-control form-control-sm" min="256" max="512000" value="{{ $settings->max_storage_mb }}">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label small fw-bold">يومية</label>
                                <input type="number" name="keep_daily_count" class="form-control form-control-sm" min="1" max="90" value="{{ $settings->keep_daily_count }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold">أسبوعية</label>
                                <input type="number" name="keep_weekly_count" class="form-control form-control-sm" min="0" max="52" value="{{ $settings->keep_weekly_count }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold">شهرية</label>
                                <input type="number" name="keep_monthly_count" class="form-control form-control-sm" min="0" max="36" value="{{ $settings->keep_monthly_count }}">
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="encrypt_backups" value="1" id="encryptBackupsSwitch" @checked($settings->encrypt_backups)>
                            <label class="form-check-label small fw-semibold" for="encryptBackupsSwitch">تشفير النسخ بـ AES-256-GCM + HMAC</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-3">
                            <i class="bi bi-save me-1"></i> حفظ سياسة الاحتفاظ
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Upload External Backup Card --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-cloud-upload-fill text-info me-2"></i>رفع نسخة محفوظة خارجياً</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('backups.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ملف النسخة (.ensanbak / .tar.gz)</label>
                            <input type="file" name="backup_file" class="form-control form-control-sm" accept=".ensanbak,.tar.gz,.tgz" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">كلمة مرور فك التشفير (إن وُجدت)</label>
                            <input type="password" name="passphrase" class="form-control form-control-sm" placeholder="لملفات الاسترداد الخاصة">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">كلمة مرور حسابك للتأكيد *</label>
                            <input type="password" name="admin_password" class="form-control form-control-sm" required placeholder="تأكيد هوية المشرف">
                        </div>
                        <div class="mb-3">
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="ملاحظة اختيارية">
                        </div>
                        <button type="submit" class="btn btn-outline-info w-100 fw-bold py-2 rounded-3">
                            <i class="bi bi-upload me-1"></i> فحص وإيداع في الخزنة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Snapshots Ledger Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-safe2-fill text-success me-2"></i>خزنة النسخ الاحتياطية المسجلة</h5>
                <small class="text-muted">يمكنك تثبيت أي نسخة للاحتفاظ بها أو تنزيلها خارج السيرفر أو فحص سلامتها التشفيرية أو استعادتها</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>كود النسخة</th>
                            <th>النطاق والمصدر</th>
                            <th>الحجم / المحتوى</th>
                            <th>الحالة والتحقق</th>
                            <th>بواسطة / التاريخ</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($snapshots as $snap)
                        <tr>
                            <td>
                                <div class="fw-bold font-monospace">{{ $snap->snapshot_code }}</div>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @if($snap->is_pinned)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-pin-angle-fill me-1"></i>مثبتة (محمية)</span>
                                    @endif
                                    @if($snap->is_encrypted)
                                        <span class="badge bg-dark-subtle text-dark"><i class="bi bi-lock-fill me-1"></i>مشفرة AES-256</span>
                                    @endif
                                </div>
                                @if($snap->notes)
                                    <div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($snap->notes, 60) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $snap->scope === 'full' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }}">
                                    {{ $snap->scope_label }}
                                </span>
                                <div class="small text-muted mt-1">{{ $snap->trigger_label }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $snap->formatted_size }}</div>
                                <small class="text-muted">{{ $snap->db_tables_count }} جدول @if($snap->files_count > 0) | {{ number_format($snap->files_count) }} ملف @endif</small>
                            </td>
                            <td>
                                @if($snap->status === 'completed')
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill me-1"></i>{{ $snap->status_label }}</span>
                                    @if($snap->verified_at)
                                        <div class="small text-success mt-1"><i class="bi bi-patch-check me-1"></i>مفحوصة {{ $snap->verified_at->format('Y-m-d H:i') }}</div>
                                    @endif
                                    @if($snap->sha256_checksum)
                                        <div class="text-muted font-monospace" style="font-size: 0.7rem;" title="{{ $snap->sha256_checksum }}">
                                            SHA256: {{ substr($snap->sha256_checksum, 0, 12) }}...
                                        </div>
                                    @endif
                                @elseif($snap->status === 'failed')
                                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle-fill me-1"></i>فشلت</span>
                                    @if($snap->error_message)
                                        <div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($snap->error_message, 60) }}</div>
                                    @endif
                                @else
                                    <span class="badge bg-warning-subtle text-warning">قيد التنفيذ</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $snap->creator?->name ?? 'الجدولة التلقائية (Cron)' }}</div>
                                <div class="small text-muted font-monospace">{{ $snap->created_at?->format('Y-m-d H:i:s') }}</div>
                            </td>
                            <td class="text-center">
                                @if($snap->status === 'completed')
                                <div class="d-flex justify-content-center flex-wrap gap-1">
                                    <form method="POST" action="{{ route('backups.pin', $snap) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $snap->is_pinned ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}"
                                                title="{{ $snap->is_pinned ? 'إلغاء التثبيت' : 'تثبيت للاحتفاظ الدائم' }}">
                                            <i class="bi bi-pin-angle{{ $snap->is_pinned ? '-fill' : '' }}"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('backups.verify', $snap) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="فحص السلامة والتوقيع">
                                            <i class="bi bi-shield-check"></i>
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-sm btn-outline-success" title="تنزيل للاحتفاظ بنسخة"
                                            onclick="openDownloadModal('{{ route('backups.download', $snap) }}', '{{ $snap->snapshot_code }}')">
                                        <i class="bi bi-download"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-outline-primary" title="استعادة هذه النسخة"
                                            onclick="openRestoreModal('{{ route('backups.restore', $snap) }}', '{{ $snap->snapshot_code }}', '{{ $snap->scope_label }}')">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>

                                    @if(!$snap->is_pinned)
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="حذف النسخة"
                                            onclick="openDeleteModal('{{ route('backups.destroy', $snap) }}', '{{ $snap->snapshot_code }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-safe2 fs-1 d-block mb-2 opacity-50"></i>
                                لا توجد نسخ احتياطية مسجلة حتى الآن. يمكنك إنشاء أول نسخة الآن من النموذج أعلاه.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($snapshots->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $snapshots->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Download Modal --}}
<div class="modal fade" id="backupDownloadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="backupDownloadForm" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="bi bi-download text-success me-2"></i>تنزيل والاحتفاظ بنسخة (<span id="dlCode"></span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">صيغة التنزيل المطلوبة</label>
                    <select name="download_mode" id="dlModeSelect" class="form-select" onchange="togglePortablePassphrase()">
                        <option value="portable_passphrase">حزمة محمولة مشفرة بكلمة مرور خاصة (.ensanbak) — موصى بها للحفظ على Google Drive / فلاشة</option>
                        <option value="encrypted">نسخة مشفرة بمفتاح السيرفر الأصلي (.ensanbak)</option>
                        <option value="decrypted_tar">أرشيف قياسي مضغوط (.tar.gz) — للاحتفاظ المحلي المباشر</option>
                    </select>
                </div>

                <div class="mb-3" id="portablePassphraseGroup">
                    <label class="form-label fw-bold small">كلمة مرور حماية النسخة المحمولة (8 أحرف فأكثر) *</label>
                    <input type="password" name="export_passphrase" id="exportPassphraseInput" class="form-control" minlength="8"
                           placeholder="اكتب كلمة مرور تحفظها لاستعادة هذه النسخة على أي سيرفر مستقبلاً">
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold small">كلمة مرور حسابك الحالي للتأكيد *</label>
                    <input type="password" name="admin_password" class="form-control" required placeholder="تأكيد هوية المشرف">
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success fw-bold px-4"><i class="bi bi-download me-1"></i> بدء التنزيل</button>
            </div>
        </form>
    </div>
</div>

{{-- Restore Modal --}}
<div class="modal fade" id="backupRestoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="backupRestoreForm" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-bottom bg-danger-subtle">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i>تأكيد استعادة النسخة (<span id="rsCode"></span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning small">
                    <strong>تنبيه مهم:</strong> سيقوم النظام تلقائياً بأخذ <strong>نسخة أمان احتياطية قبل الاستعادة</strong>، ثم استبدال بيانات (<span id="rsScope"></span>) بمحتوى هذه النسخة.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">كلمة مرور فك التشفير (اتركها فارغة للنسخ التلقائية)</label>
                    <input type="password" name="passphrase" class="form-control" placeholder="فقط إذا كانت النسخة محمية بكلمة مرور استرداد خاصة">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">كلمة مرور حسابك الحالي للتأكيد *</label>
                    <input type="password" name="admin_password" class="form-control" required placeholder="تأكيد هوية المشرف">
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="confirm_restore" value="1" id="confirmRestoreCheck" required>
                    <label class="form-check-label small fw-bold text-danger" for="confirmRestoreCheck">
                        أقر بأني أفهم أن الاستعادة ستستبدل البيانات الحالية بعد أخذ نسخة الأمان التلقائية.
                    </label>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-danger fw-bold px-4"><i class="bi bi-arrow-counterclockwise me-1"></i> تنفيذ الاستعادة الآمنة</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="backupDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="backupDeleteForm" class="modal-content rounded-4 border-0 shadow">
            @csrf
            @method('DELETE')
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-trash-fill me-2"></i>حذف النسخة (<span id="delCode"></span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted">سيتم حذف ملف النسخة الاحتياطية نهائياً من قرص السيرفر.</p>
                <div class="mb-2">
                    <label class="form-label fw-bold small">كلمة مرور حسابك للتأكيد *</label>
                    <input type="password" name="admin_password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-danger fw-bold px-4">تأكيد الحذف</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePortablePassphrase() {
    var mode = document.getElementById('dlModeSelect').value;
    var grp = document.getElementById('portablePassphraseGroup');
    var inp = document.getElementById('exportPassphraseInput');
    if (mode === 'portable_passphrase') {
        grp.style.display = 'block';
        inp.required = true;
    } else {
        grp.style.display = 'none';
        inp.required = false;
        inp.value = '';
    }
}

function openDownloadModal(actionUrl, code) {
    document.getElementById('backupDownloadForm').action = actionUrl;
    document.getElementById('dlCode').textContent = code;
    togglePortablePassphrase();
    new bootstrap.Modal(document.getElementById('backupDownloadModal')).show();
}

function openRestoreModal(actionUrl, code, scopeLabel) {
    document.getElementById('backupRestoreForm').action = actionUrl;
    document.getElementById('rsCode').textContent = code;
    document.getElementById('rsScope').textContent = scopeLabel;
    new bootstrap.Modal(document.getElementById('backupRestoreModal')).show();
}

function openDeleteModal(actionUrl, code) {
    document.getElementById('backupDeleteForm').action = actionUrl;
    document.getElementById('delCode').textContent = code;
    new bootstrap.Modal(document.getElementById('backupDeleteModal')).show();
}

document.getElementById('createBackupForm')?.addEventListener('submit', function() {
    var btn = document.getElementById('btnRunBackup');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري إنشاء وضغط وتشفير النسخة...';
    }
});
</script>
@endsection
