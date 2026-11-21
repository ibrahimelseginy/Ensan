@extends('layouts.app')

@section('title', 'التوريدات والتحويلات النقدية والبنكية')

@section('content')
<div class="container-fluid py-4 px-lg-4" style="direction: rtl; text-align: right;">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Top Action Row --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-right text-primary"></i>
                التوريدات والتحويلات النقدية والعمليات البنكية
            </h2>
            <p class="text-muted mb-0 small">إدارة توريدات الخزائن، إيداعات البنك، صرف الشيكات، والتحويلات المباشرة لكفالات الأيتام</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('treasuries.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-right me-1"></i> العودة للخزائن
            </a>
            <button type="button" class="btn btn-primary rounded-pill px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#newSupplyModal">
                <i class="bi bi-box-arrow-in-left"></i>
                <span>تسجيل طلب توريد نقدية</span>
            </button>
            <button type="button" class="btn btn-info text-white rounded-pill px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#chequeModal">
                <i class="bi bi-credit-card-2-front"></i>
                <span>صرف شيك بنكي</span>
            </button>
            <button type="button" class="btn btn-success rounded-pill px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#directTransferModal">
                <i class="bi bi-bank"></i>
                <span>تحويل بنكي مباشر (كفالات أيتام)</span>
            </button>
        </div>
    </div>

    {{-- Section 1: التوريدات النقدية بين الخزائن --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-arrow-repeat text-success"></i>
                سجل التوريدات والتحويلات النقدية بين الخزائن
            </h5>
            <span class="badge bg-light text-muted border px-2.5 py-1">اعتماد مزدوج</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th>رقم السند</th>
                            <th>التاريخ</th>
                            <th>الخزينة الموردة</th>
                            <th>الخزينة المستلمة</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>المسلم / المستلم</th>
                            <th>المرجع والملاحظات</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $trf)
                            <tr>
                                <td class="font-monospace fw-bold text-primary">{{ $trf->transfer_number }}</td>
                                <td class="font-monospace small">{{ $trf->requested_on ? $trf->requested_on->format('Y-m-d') : '—' }}</td>
                                <td class="fw-semibold">{{ $trf->fromAccount->name ?? '—' }}</td>
                                <td class="fw-semibold text-success">{{ $trf->toAccount->name ?? '—' }}</td>
                                <td class="fw-bold font-monospace text-dark">{{ number_format($trf->amount, 2) }} ج.م</td>
                                <td>
                                    @if($trf->status === 'received')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">تم الاستلام والمطابقة</span>
                                    @elseif($trf->status === 'submitted')
                                        <span class="badge bg-warning bg-opacity-10 text-warning text-dark border border-warning border-opacity-25 px-2.5 py-1">بانتظار تأكيد الاستلام</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">{{ $trf->status }}</span>
                                    @endif
                                </td>
                                <td class="small">
                                    <div><span class="text-muted">مُسلّم:</span> {{ $trf->requester->name ?? '—' }}</div>
                                    @if($trf->receiver)
                                        <div><span class="text-success">مُستلم:</span> {{ $trf->receiver->name }}</div>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $trf->reference ?? ($trf->note ?? '—') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($trf->status === 'submitted')
                                            <form method="POST" action="{{ route('transfers.confirmReceipt', $trf) }}" onsubmit="return confirm('هل تؤكد استلام المبلغ فعلياً وعدّه؟');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success px-2.5 py-1" title="تأكيد الاستلام">
                                                    <i class="bi bi-check-lg"></i> تأكيد الاستلام
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('transfers.printVoucher', $trf) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1" title="طباعة سند التوريد">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">لا توجد طلبات توريد مسجلة حتى الآن</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transfers->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

    {{-- Section 2: الشيكات والتحويلات البنكية المباشرة --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-bank text-primary"></i>
                الشيكات المصروفة والتحويلات البنكية المباشرة (كفالات أيتام / مساعدات)
            </h5>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1">صرف بنكي مباشر</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th>رقم العملية / الشيك</th>
                            <th>النوع</th>
                            <th>تاريخ الصرف</th>
                            <th>المستفيد</th>
                            <th>المشروع المرتبط</th>
                            <th>المبلغ</th>
                            <th>حساب البنك المصروف منه</th>
                            <th>البيان والملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentInstruments as $pi)
                            <tr>
                                <td class="font-monospace fw-bold text-dark">{{ $pi->instrument_number }}</td>
                                <td>
                                    @if($pi->instrument_type === 'cheque')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">شيك بنكي</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">تحويل بنكي مباشر</span>
                                    @endif
                                </td>
                                <td class="font-monospace small">{{ $pi->issued_on ? $pi->issued_on->format('Y-m-d') : '—' }}</td>
                                <td class="fw-semibold">{{ $pi->beneficiary_name ?? '—' }}</td>
                                <td class="small">{{ $pi->project->name ?? 'عام' }}</td>
                                <td class="fw-bold font-monospace text-primary">{{ number_format($pi->amount, 2) }} ج.م</td>
                                <td class="small text-muted">{{ $pi->sourceAccount->name ?? 'حساب البنك الرئيسي' }}</td>
                                <td class="small text-muted">{{ $pi->note ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">لا توجد عمليات صرف شيكات أو تحويلات بنكية مباشرة مسجلة بعد</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL 1: تسجيل طلب توريد نقدية --}}
<div class="modal fade" id="newSupplyModal" tabindex="-1" aria-labelledby="newSupplyModalLabel" aria-hidden="true" style="direction: rtl; text-align: right;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST" action="{{ route('transfers.storeSupply') }}">
                @csrf
                <div class="modal-header bg-light rounded-top-4 px-4 py-3">
                    <h5 class="modal-title fw-bold" id="newSupplyModalLabel">
                        <i class="bi bi-box-arrow-in-left text-primary me-2"></i>
                        تسجيل طلب توريد نقدية بين الخزائن
                    </h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">الخزينة الموردة (المصدر) <span class="text-danger">*</span></label>
                        <select name="from_treasury_id" class="form-select rounded-3" required>
                            <option value="">-- اختر الخزينة الموردة --</option>
                            @foreach($treasuries as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} (الرصيد: {{ number_format($t->current_balance, 2) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">الخزينة المستلمة (الهدف) <span class="text-danger">*</span></label>
                        <select name="to_treasury_id" class="form-select rounded-3" required>
                            <option value="">-- اختر الخزينة المستلمة --</option>
                            @foreach($treasuries as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">المبلغ المراد توريده <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="1" name="amount" class="form-control rounded-start-3" placeholder="0.00" required>
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">نوع التوريد</label>
                            <select name="transfer_type" class="form-select rounded-3">
                                <option value="supply">توريد دوري للخزينة المركزية</option>
                                <option value="bank_deposit">إيداع بنكي مباشر</option>
                                <option value="internal_transfer">تحويل نقدي داخلي</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">رقم الإيصال / المرجع البنكي</label>
                        <input type="text" name="reference" class="form-control rounded-3" placeholder="رقم إيصال الإيداع أو الحافظة">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">ملاحظات التوريد</label>
                        <textarea name="note" class="form-control rounded-3" rows="2" placeholder="ملاحظات أو أسماء إيصالات التبرع الموردة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">إصدار طلب التوريد</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: صرف شيك بنكي وتحديد جهة الاستلام --}}
<div class="modal fade" id="chequeModal" tabindex="-1" aria-labelledby="chequeModalLabel" aria-hidden="true" style="direction: rtl; text-align: right;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST" action="{{ route('transfers.disburseCheque') }}">
                @csrf
                <div class="modal-header bg-light rounded-top-4 px-4 py-3">
                    <h5 class="modal-title fw-bold" id="chequeModalLabel">
                        <i class="bi bi-credit-card-2-front text-info me-2"></i>
                        صرف شيك بنكي وتحديد مسار الاستلام
                    </h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">رقم الشيك <span class="text-danger">*</span></label>
                            <input type="text" name="cheque_number" class="form-control rounded-3 font-monospace" placeholder="CHQ-00123" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">تاريخ الشيك <span class="text-danger">*</span></label>
                            <input type="date" name="issued_on" value="{{ date('Y-m-d') }}" class="form-control rounded-3" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">حساب البنك المسحوب عليه <span class="text-danger">*</span></label>
                            <select name="bank_treasury_id" class="form-select rounded-3" required>
                                @foreach($treasuries as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ number_format($t->current_balance, 2) }} ج.م)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">قيمة الشيك (ج.م) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" name="amount" class="form-control rounded-3 font-monospace" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">جهة صرف الشيك <span class="text-danger">*</span></label>
                        <select name="destination_type" id="chequeDestType" class="form-select rounded-3" required onchange="toggleChequeDest(this.value)">
                            <option value="internal_treasury">1. الصرف إلى خزينة داخل المؤسسة (الحسابات / المركزية / فرع)</option>
                            <option value="external_party">2. الصرف إلى شخص أو جهة خارجية (مورد / مقاول / مستفيد)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="internalTreasuryGroup">
                        <label class="form-label fw-bold small">الخزينة المستلمة للسيولة</label>
                        <select name="target_treasury_id" class="form-select rounded-3">
                            <option value="">-- اختر الخزينة المستلمة --</option>
                            @foreach($treasuries as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="externalPartyGroup">
                        <label class="form-label fw-bold small">اسم المستفيد أو الجهة الخارجية</label>
                        <input type="text" name="beneficiary_name" class="form-control rounded-3" placeholder="اسم المورد أو المقاول أو الشخص">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">المشروع المرتبط بالمصروف (إن وجد)</label>
                        <select name="project_id" class="form-select rounded-3">
                            <option value="">-- بدون ارتباط بمشروع محدد --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">سبب الصرف وملاحظات</label>
                        <textarea name="note" class="form-control rounded-3" rows="2" placeholder="بيان صرف الشيك وتفاصيل الفاتورة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">تأكيد صرف الشيك</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 3: تحويل بنكي مباشر للمستفيدين (كفالات الأيتام) --}}
<div class="modal fade" id="directTransferModal" tabindex="-1" aria-labelledby="directTransferModalLabel" aria-hidden="true" style="direction: rtl; text-align: right;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST" action="{{ route('transfers.directBankTransfer') }}">
                @csrf
                <div class="modal-header bg-light rounded-top-4 px-4 py-3">
                    <h5 class="modal-title fw-bold" id="directTransferModalLabel">
                        <i class="bi bi-bank text-success me-2"></i>
                        تحويل بنكي مباشر لحساب المستفيدين (كفالات أيتام / مساعدات)
                    </h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        يتم خصم التحويل مباشرة من رصيد البنك وتحميله على المشروع دون المرور بأي خزينة نقدية داخلية.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">اسم المستفيد / اليتيم <span class="text-danger">*</span></label>
                        <input type="text" name="beneficiary_name" class="form-control rounded-3" placeholder="الاسم الرباعي للمستفيد" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">رقم الحساب / الآيبان (IBAN)</label>
                            <input type="text" name="bank_account_iban" class="form-control rounded-3 font-monospace" placeholder="EG...">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">اسم بنك المستفيد</label>
                            <input type="text" name="bank_name" class="form-control rounded-3" placeholder="البنك الأهلي، مصر...">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">قيمة التحويل (ج.م) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" name="amount" class="form-control rounded-3 font-monospace" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">تاريخ التحويل <span class="text-danger">*</span></label>
                            <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" class="form-control rounded-3" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">نوع الصرف</label>
                            <select name="disbursal_type" class="form-select rounded-3">
                                <option value="orphan_sponsorship">كفالة شهرية لأيتام</option>
                                <option value="medical_aid">مساعدة علاجية مباشرة</option>
                                <option value="family_support">دعم مالي لأسرة</option>
                                <option value="general">مساعدة عامة</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">حساب البنك المنفذ <span class="text-danger">*</span></label>
                            <select name="bank_treasury_id" class="form-select rounded-3" required>
                                @foreach($treasuries as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ number_format($t->current_balance, 2) }} ج.م)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">المشروع المرتبط بالصرف</label>
                        <select name="project_id" class="form-select rounded-3">
                            <option value="">-- بدون مشروع محدد --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">ملاحظات ورقم المعاملة البنكية</label>
                        <textarea name="note" class="form-control rounded-3" rows="2" placeholder="رقم إشعار التحويل البنكي أو الشهر المستحق عنه الصرف..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">تأكيد وتنفيذ التحويل المباشر</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleChequeDest(val) {
        var internal = document.getElementById('internalTreasuryGroup');
        var external = document.getElementById('externalPartyGroup');
        if (val === 'internal_treasury') {
            internal.classList.remove('d-none');
            external.classList.add('d-none');
        } else {
            internal.classList.add('d-none');
            external.classList.remove('d-none');
        }
    }
</script>
@endpush
@endsection

