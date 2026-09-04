@extends('layouts.app', ['hideGlobalAlerts' => true])

@section('content')
<div class="donor-accounts-mgmt">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container hero-content-wrapper text-center">
            <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">حسابات المتبرعين</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-people-fill me-2"></i> إدارة هويات المتبرعين 👤
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">إدارة حسابات المتبرعين</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                التحكم في بيانات الدخول، توثيق الهوية، ومتابعة سجلات النشاط لمتبرعي المنصة.
            </p>
        </div>
    </div>

    <div class="container-fluid py-4 px-lg-5">
        @if(Session::has('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>{{ Session::get('success') }}</div>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate-slide-up">
            <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ul me-2 text-primary"></i> قائمة المتبرعين المسجلين</h6>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm x-small" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                    <i class="bi bi-person-plus-fill me-1"></i> إضافة حساب يدوي
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-end">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted x-small fw-bold">المتبرع</th>
                            <th class="px-4 py-3 text-muted x-small fw-bold">رقم التواصل</th>
                            <th class="px-4 py-3 text-muted x-small fw-bold text-center">الإحصائيات</th>
                            <th class="px-4 py-3 text-muted x-small fw-bold text-center">إجمالي التبرع</th>
                            <th class="px-4 py-3 text-muted x-small fw-bold text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($accounts as $account)
                            @php $displayName = $account->display_name ?? $account->name; @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle-sm bg-primary bg-opacity-10 text-primary">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0">{{ $displayName }}</div>
                                            @if($account->is_temporary_name ?? (strpos($displayName, 'Donor') !== false))
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded x-small mt-1">حساب مؤقت</span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded x-small mt-1">حساب موثق</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-dark font-monospace x-small">{{ $account->phone }}</span>
                                    <div class="x-small text-muted mt-1">
                                        <i class="bi bi-calendar-event me-1"></i> {{ $account->created_at ? $account->created_at->format('Y-m-d') : '---' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="badge bg-light text-primary border rounded-pill px-3 py-1 x-small fw-bold">
                                        {{ $account->total_donations_count ?? 0 }} تبرعات
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="fw-bold text-success">
                                        {{ number_format($account->total_donations_amount ?? 0, 2) }}
                                        <span class="x-small fw-normal">ج.م</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($account->donor_id)
                                            <a href="{{ route('website.donation-accounts.show', $account->donor_id) }}" class="btn btn-icon-light rounded-pill" title="عرض السجل">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-icon-light rounded-pill" data-bs-toggle="modal" data-bs-target="#editAccountModal"
                                                data-account-id="{{ $account->id }}"
                                                data-account-name="{{ $displayName }}"
                                                data-account-phone="{{ $account->phone }}">
                                            <i class="bi bi-pencil-fill text-warning"></i>
                                        </button>
                                        <form action="{{ route('website.accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-icon-light rounded-pill">
                                                <i class="bi bi-trash-fill text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 mb-3">
                                        <i class="bi bi-people display-4"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted">لا يوجد حسابات مسجلة حالياً</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($accounts->hasPages())
                <div class="p-4 border-top bg-light">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals --}}
<div class="modal fade" id="createAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">إنشاء حساب متبرع جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('website.accounts.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-end">
                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" placeholder="ادخل الاسم الكامل..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted">رقم الهاتف (للتواصل والدخول)</label>
                        <input type="text" name="phone" class="form-control" placeholder="01xxxxxxxxx" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted">البريد الإلكتروني (اختياري)</label>
                        <input type="email" name="email" class="form-control" placeholder="example@mail.com">
                    </div>
                    <div class="p-3 bg-light rounded-4 border">
                        <p class="x-small text-muted mb-0 lh-base">
                            <i class="bi bi-info-circle-fill text-primary me-1"></i> سيتمكن المتبرع من الدخول عبر رقم هاتفه باستخدام رمز التحقق (OTP) الذي يصله عبر الرسائل القصيرة.
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold x-small" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold x-small shadow-sm">حفظ الحساب</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">تعديل بيانات الحساب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAccountForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4 text-end">
                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted">الاسم المعروض</label>
                        <input id="editAccountName" type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label x-small fw-bold text-muted">رقم الهاتف المسجل</label>
                        <input id="editAccountPhone" type="text" class="form-control bg-light" readonly disabled>
                        <div class="form-text x-small text-muted mt-2">لا يمكن تعديل رقم الهاتف من هنا لأسباب أمنية ولارتباطه بسجلات مالية.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold x-small" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold x-small shadow-sm">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editAccountModal = document.getElementById('editAccountModal');
    if (!editAccountModal) return;

    const form = document.getElementById('editAccountForm');
    const nameInput = document.getElementById('editAccountName');
    const phoneInput = document.getElementById('editAccountPhone');
    const updateBaseUrl = @json(url('admin/website/accounts'));

    editAccountModal.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        const accountId = trigger.getAttribute('data-account-id');
        nameInput.value = trigger.getAttribute('data-account-name') || '';
        phoneInput.value = trigger.getAttribute('data-account-phone') || '';
        form.action = `${updateBaseUrl}/${accountId}`;
    });
});
</script>

<style>
    .donor-accounts-mgmt { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .x-small { font-size: 0.75rem; }
    .max-w-600 { max-width: 600px; }
    .transition-all { transition: all 0.3s ease; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 80px 0 100px; 
        background: white !important; 
        border-bottom: 1px solid var(--border); 
        overflow: hidden; 
        z-index: 10; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }

    .badge-glass-premium { 
        background: var(--primary-light); 
        border: 1px solid rgba(34, 197, 94, 0.1); 
        padding: 8px 18px; 
        border-radius: 100px; 
        color: var(--primary); 
        font-weight: 700; 
        font-size: 0.85rem; 
        display: inline-block;
    }

    .avatar-circle-sm {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        border: 1.5px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .btn-icon-light {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #eee;
    }
    .btn-icon-light:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary-light); }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .table thead th { border-bottom: none; }
    .table tbody td { border-bottom: 1px solid #f2f2f2; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.01); }
</style>
@endsection
