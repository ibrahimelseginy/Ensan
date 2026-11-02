@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-chat-dots text-primary me-2"></i>الشكاوى
            </h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('complaint.track') }}" target="_blank" class="btn btn-outline-success shadow-sm">
                <i class="bi bi-search me-1"></i> تتبع شكوى
            </a>
            <a href="{{ route('complaints.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> إضافة شكوى
            </a>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('complaints.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="ابحث بكود التتبع (ENS-...) أو العنوان أو الاسم..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <select name="source_type" class="form-select" onchange="this.form.submit()">
                        <option value="">جميع المصادر</option>
                        <option value="website" @selected(request('source_type') === 'website')>🌐 الموقع الإلكتروني</option>
                        <option value="donor" @selected(request('source_type') === 'donor')>متبرع</option>
                        <option value="beneficiary" @selected(request('source_type') === 'beneficiary')>مستفيد</option>
                        <option value="employee" @selected(request('source_type') === 'employee')>موظف</option>
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">جميع الحالات</option>
                        <option value="open" @selected(request('status') === 'open')>مفتوحة</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>جارية</option>
                        <option value="resolved" @selected(request('status') === 'resolved')>تم الحل</option>
                        <option value="closed" @selected(request('status') === 'closed')>مغلقة</option>
                    </select>
                </div>
                <div class="col-md-2 col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3">
                        <i class="bi bi-funnel me-1"></i> تصفية
                    </button>
                    @if(request()->hasAny(['search', 'status', 'source_type']))
                        <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary px-3">
                            <i class="bi bi-x-circle me-1"></i> إعادة ضبط
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Complaints Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="table-responsive" style="min-height: 400px; overflow-x: auto; overflow-y: visible;">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>كود التتبع</th>
                        <th>المصدر والشاكي</th>
                        <th>العنوان والموضوع</th>
                        <th>الحالة</th>
                        <th class="text-end">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $c)
                        <tr>
                            <td class="text-muted">{{ $c->id }}</td>
                            <td>
                                @if(!empty($c->tracking_code))
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('complaint.track', ['code' => $c->tracking_code]) }}" target="_blank" 
                                           class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 text-decoration-none px-2.5 py-1.5 fw-bold"
                                           style="font-family: monospace; letter-spacing: 1px;"
                                           title="اضغط للتتبع المباشر للشكوى">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>{{ $c->tracking_code }}
                                        </a>
                                        <button type="button" class="btn btn-sm btn-link text-muted p-1" title="نسخ الكود" onclick="navigator.clipboard.writeText('{{ $c->tracking_code }}'); this.innerHTML='<i class=\'bi bi-check-lg text-success\'></i>'; setTimeout(() => this.innerHTML='<i class=\'bi bi-clipboard\'></i>', 1500);">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="fw-bold text-dark">{{ $c->source_name }}</span>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        @if($c->source_type === 'website')
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.72rem;">
                                                <i class="bi bi-globe me-1"></i>الموقع الإلكتروني
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.72rem;">
                                                {{ $c->source_type_name }} {{ $c->source_id > 0 ? '#'.$c->source_id : '' }}
                                            </span>
                                        @endif
                                        @if(!empty($c->complainant_phone))
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-telephone ms-1"></i>{{ $c->complainant_phone }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium">{{ $c->subject }}</td>
                            <td>
                                @php
                                    $statusClass = match ($c->status) {
                                        'open' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10',
                                        'in_progress' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10',
                                        'resolved' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-10',
                                        'pending' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10',
                                        'closed' => 'bg-dark bg-opacity-10 text-secondary border border-secondary border-opacity-10',
                                        default => 'bg-secondary bg-opacity-10 text-secondary'
                                    };
                                    $statusText = match ($c->status) {
                                        'open' => 'مفتوحة',
                                        'in_progress' => 'جارية',
                                        'resolved' => 'تم الحل',
                                        'pending' => 'قيد الانتظار',
                                        'closed' => 'مغلقة',
                                        default => $c->status
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1" 
                                            data-bs-toggle="modal" data-bs-target="#replyModal{{ $c->id }}" 
                                            title="الرد على الشكوى">
                                        <i class="bi bi-reply-fill me-1"></i> رد
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1">
                                            <li>
                                                <a class="dropdown-item rounded-2 text-success fw-bold" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#replyModal{{ $c->id }}">
                                                    <i class="bi bi-reply-fill me-2"></i> الرد على الشكوى
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item rounded-2" href="{{ route('complaints.show', $c) }}">
                                                    <i class="bi bi-eye me-2 opacity-50"></i> تفاصيل
                                                </a>
                                            </li>
                                            @if(!empty($c->tracking_code))
                                                <li>
                                                    <a class="dropdown-item rounded-2 text-success" href="{{ route('complaint.track', ['code' => $c->tracking_code]) }}" target="_blank">
                                                        <i class="bi bi-box-arrow-up-right me-2 opacity-75"></i> تتبع الشكوى مباشرة
                                                    </a>
                                                </li>
                                            @endif
                                            @if($c->pendingRequest)
                                                <li>
                                                    <span class="dropdown-item rounded-2 text-warning bg-warning bg-opacity-10">
                                                        <i class="bi bi-hourglass-split me-2"></i> قيد المراجعة
                                                    </span>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item rounded-2" href="{{ route('complaints.edit', $c) }}">
                                                        <i class="bi bi-pencil me-2 opacity-50"></i> تعديل
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('complaints.destroy', $c) }}" onsubmit="return confirm('حذف الشكوى؟');">
                                                        @csrf @method('DELETE')
                                                        <button class="dropdown-item rounded-2 text-danger">
                                                            <i class="bi bi-trash me-2"></i> حذف
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                {{-- Reply Modal --}}
                                <div class="modal fade text-start" id="replyModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form method="POST" action="{{ route('complaints.reply', $c) }}">
                                                @csrf
                                                <div class="modal-header bg-success text-white py-2.5 px-3">
                                                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                                                        <i class="bi bi-reply-fill"></i>
                                                        الرد على الشكوى #{{ $c->id }} ({{ $c->tracking_code }})
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-3">
                                                    <div class="mb-2 p-2.5 bg-light rounded text-muted small">
                                                        <strong>الشاكي:</strong> {{ $c->source_name }} | <strong>الموضوع:</strong> {{ $c->subject }}
                                                        <div class="mt-1 text-dark">{{ Str::limit($c->message, 140) }}</div>
                                                    </div>

                                                    @if(!empty($c->resolution))
                                                        <div class="mb-2 p-2 rounded bg-success bg-opacity-10 border border-success border-opacity-25 small">
                                                            <strong class="text-success"><i class="bi bi-chat-left-text me-1"></i> الردود السابقة:</strong>
                                                            <div class="text-dark mt-1" style="white-space: pre-line; max-height: 100px; overflow-y: auto;">{{ $c->resolution }}</div>
                                                        </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small">نص الرد / الإجراء المتخذ *</label>
                                                        <textarea name="reply" rows="4" class="form-control" placeholder="اكتب رد المؤسسة أو الحل المتخذ للشاكي..." required></textarea>
                                                        <div class="form-text small">سيظهر هذا الرد للشاكي فور تتبع الشكوى بكوده المعتمد.</div>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label fw-bold small">تحديث حالة الشكوى</label>
                                                        <select name="status" class="form-select form-select-sm">
                                                            <option value="resolved" @selected($c->status === 'resolved' || $c->status === 'closed')>تم الحل (Resolved)</option>
                                                            <option value="in_progress" @selected($c->status === 'in_progress')>جارية المعالجة (In Progress)</option>
                                                            <option value="closed">إغلاق الشكوى نهائياً (Closed)</option>
                                                            <option value="open" @selected($c->status === 'open')>إبقاء مفتوحة (Open)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2 px-3 bg-light">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-sm btn-success px-3 fw-bold">
                                                        <i class="bi bi-send-fill me-1"></i> حفظ وإرسال الرد
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-chat-left-dots"></i>
                                    <h5>لا توجد شكاوى مسجلة</h5>
                                    <p>اضغط على "إضافة شكوى" لتسجيل شكوى جديدة</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $complaints->links() }}</div>
@endsection

