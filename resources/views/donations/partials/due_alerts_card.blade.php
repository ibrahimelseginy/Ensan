{{-- Smart Alerts: Monthly Due Donors & WhatsApp Reminder (تنبيهات الاستحقاق الشهري والتذكير عبر واتساب) --}}
@if(isset($upcomingDueDonors) && $upcomingDueDonors->isNotEmpty())
<style>
.due-alert-compact-row {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.due-alert-compact-row:hover {
    background: #0f1c3d !important;
    border-color: rgba(255, 255, 255, 0.16) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
}
.due-alert-btn-action {
    transition: all 0.15s ease-in-out;
}
.due-alert-btn-action:hover {
    transform: scale(1.02);
    filter: brightness(1.1);
}
</style>

<div class="card border-0 mb-3 shadow-lg text-white animate-slide-up" 
     style="background: linear-gradient(145deg, #0b1120 0%, #060b17 100%); border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 14px;">
    <div class="card-body p-2 p-md-3">
        
        {{-- Card Header (Compact & Crisp) --}}
        <div class="d-flex justify-content-between align-items-center mb-2 px-1 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" 
                     style="width: 32px; height: 32px; background: rgba(234, 179, 8, 0.12); border: 1px solid rgba(234, 179, 8, 0.25);">
                    <i class="bi bi-bell-fill text-warning fs-6"></i>
                </div>
                <h5 class="mb-0 fw-bold text-white fs-6">تنبيهات الاستحقاق الشهري</h5>
                <span class="badge rounded-pill px-2.5 py-0.5 font-monospace" 
                      style="background: #eab308; color: #000; font-size: 0.75rem; font-weight: 800;">
                    {{ $upcomingDueDonors->count() }} هذا الأسبوع
                </span>
                <span class="text-secondary small d-none d-md-inline" style="color: #94a3b8 !important; font-size: 0.78rem;">
                    (تذكير مباشر للمتبرعين المستحقين)
                </span>
            </div>

            @if(Route::has('donors.index'))
            <a href="{{ route('donors.index') }}?classification=recurring" 
               class="btn btn-sm text-white rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm due-alert-btn-action"
               style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.14); font-size: 0.78rem; font-weight: 600;">
                <span>عرض الكل</span>
                <i class="bi bi-arrow-left"></i>
            </a>
            @endif
        </div>

        {{-- Donors List (Streamlined Compact Rows) --}}
        <div class="d-flex flex-column gap-2" style="max-height: 380px; overflow-y: auto;">
            @foreach($upcomingDueDonors as $dueDonor)
                @php
                    $isToday = $dueDonor->days_left === 0;
                    $isTomorrow = $dueDonor->days_left === 1;
                    $accentColor = $isToday ? '#ef4444' : ($isTomorrow ? '#f59e0b' : '#3b82f6');
                    $statusBg = $isToday ? 'rgba(239, 68, 68, 0.16)' : ($isTomorrow ? 'rgba(245, 158, 11, 0.16)' : 'rgba(59, 130, 246, 0.16)');
                    $statusColor = $isToday ? '#f87171' : ($isTomorrow ? '#fbbf24' : '#60a5fa');
                    $statusBorder = $isToday ? 'rgba(239, 68, 68, 0.35)' : ($isTomorrow ? 'rgba(245, 158, 11, 0.35)' : 'rgba(59, 130, 246, 0.35)');
                    $amount = $dueDonor->sponsorship_monthly_amount ?: 2500;
                @endphp
                <div class="due-alert-compact-row rounded-3 p-2 px-2-5 position-relative shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-2" 
                     style="background: #0b1329; border: 1px solid rgba(255, 255, 255, 0.07); border-right: 4px solid {{ $accentColor }} !important;">
                    
                    {{-- 1. Identity & Due Status --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-2.5 py-1 font-monospace" 
                              style="background: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusBorder }}; font-size: 0.76rem; font-weight: 700;">
                            <i class="bi bi-clock me-1"></i>{{ $dueDonor->due_status }}
                        </span>
                        
                        <span class="badge font-monospace rounded-pill px-2 py-0.5 text-secondary" 
                              style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.74rem;">
                            {{ $dueDonor->code ?? ('DON-' . str_pad($dueDonor->id, 6, '0', STR_PAD_LEFT)) }}
                        </span>

                        <a href="{{ route('donors.show', $dueDonor) }}" class="fw-bold text-white text-decoration-none hover-primary" style="font-size: 0.92rem;">
                            {{ $dueDonor->name }}
                        </a>
                    </div>

                    {{-- 2. Financial & Date Highlights --}}
                    <div class="d-flex align-items-center gap-2 gap-md-3 flex-wrap">
                        <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(56, 189, 248, 0.08); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.18); font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-calendar-event me-1 text-info"></i>
                            استحقاق: <strong class="text-white font-monospace">{{ $dueDonor->monthly_donation_day }}</strong> من الشهر
                        </span>

                        <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(16, 185, 129, 0.08); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.18); font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-cash-stack me-1 text-success"></i>
                            المبلغ: <strong class="text-white font-monospace fw-bold">{{ number_format((float) $amount, 0) }} ج.م</strong>
                        </span>
                    </div>

                    {{-- 3. Compact Action Buttons (Auto-sized, no stretch) --}}
                    <div class="d-flex align-items-center gap-1-5 flex-wrap ms-auto ms-lg-0" style="gap: 6px;">
                        @if($dueDonor->whatsapp_url)
                        <a href="{{ $dueDonor->whatsapp_url }}" target="_blank" 
                           class="btn btn-sm text-white fw-bold d-inline-flex align-items-center justify-content-center text-nowrap rounded-pill px-3 py-1 shadow-sm due-alert-btn-action" 
                           style="background: #10b981; font-size: 0.8rem; gap: 5px; border: none; height: 31px;"
                           title="إرسال رسالة تذكير مجهزة عبر واتساب">
                            <i class="bi bi-whatsapp"></i>
                            <span>تذكير واتساب</span>
                        </a>
                        @endif

                        @if($dueDonor->phone)
                        <a href="tel:{{ $dueDonor->phone }}" 
                           class="btn btn-sm d-inline-flex align-items-center justify-content-center text-nowrap font-monospace rounded-pill px-2.5 py-1 due-alert-btn-action" 
                           style="background: rgba(255, 255, 255, 0.06); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.14); font-size: 0.78rem; gap: 5px; height: 31px;"
                           title="اتصال هاتفي سريع">
                            <i class="bi bi-telephone text-secondary" style="font-size: 0.72rem;"></i>
                            <span>{{ $dueDonor->phone }}</span>
                        </a>
                        @endif

                        <a href="{{ route('donations.create', ['donor_id' => $dueDonor->id, 'amount' => $amount]) }}" 
                           class="btn btn-sm fw-bold d-inline-flex align-items-center justify-content-center text-nowrap rounded-pill px-2.5 py-1 text-white shadow-sm due-alert-btn-action" 
                           style="background: #0284c7; font-size: 0.78rem; gap: 5px; border: none; height: 31px;"
                           title="تسجيل تبرع مباشر">
                            <i class="bi bi-plus-circle"></i>
                            <span>تسجيل التبرع</span>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</div>
@endif

