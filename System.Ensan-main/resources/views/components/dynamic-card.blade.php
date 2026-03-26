@props(['card'])

@php
    $stats = $card->stats_data ?? [];
    $buttons = $card->buttons_data ?? [];
@endphp

<div class="dynamic-card-wrapper position-relative mx-auto" style="max-width: 380px; --card-primary: {{ $card->card_color ?? '#3b82f6' }}; --card-primary-light: {{ ($card->card_color ?? '#3b82f6').'15' }};">
    {{-- Floating Badge --}}
    @if($card->badge_visible)
    <div class="card-floating-badge badge-capsule-gradient">
        @if($card->badge_icon) <i class="{{ $card->badge_icon }} me-1"></i> @endif
        <span>{{ $card->badge_text }}</span>
    </div>
    @endif

    <div class="card-main-container bg-white rounded-5 border-primary-thin shadow-soft-elevation position-relative overflow-hidden p-4">
        {{-- Internal Tag --}}
        @if($card->tag_text)
        <div class="card-internal-tag position-absolute top-0 start-0 m-3 badge-soft-primary">
            {{ $card->tag_text }}
        </div>
        @endif

        <div class="d-flex flex-column align-items-center text-center mt-3">
            {{-- Header Icon --}}
            <div class="card-icon-neumorphic mb-3 d-flex align-items-center justify-content-center">
                @if($card->image)
                    <img src="{{ $card->getFileUrl('image') }}" class="img-fluid object-fit-contain" style="max-height: 40px;">
                @else
                    <i class="bi bi-star-fill text-primary fs-3"></i>
                @endif
            </div>

            {{-- Title & Description --}}
            <h3 class="card-title-modern fw-bold text-dark mb-2">{{ $card->title }}</h3>
            <p class="card-desc-text text-muted mb-4">{{ $card->description }}</p>

            {{-- Buttons Stack --}}
            @if(count($buttons) > 0)
            <div class="card-buttons-stack w-100 d-flex flex-column gap-2 mb-4">
                @foreach($buttons as $btn)
                <a href="{{ $btn['action'] ?? '#' }}" class="btn btn-capsule-tint w-100 d-flex align-items-center justify-content-center gap-2">
                    @if(isset($btn['icon'])) <i class="{{ $btn['icon'] }}"></i> @endif
                    <span>{{ $btn['text'] }}</span>
                </a>
                @endforeach
            </div>
            @endif

            {{-- Stats Grid --}}
            @if(count($stats) > 0)
            <div class="card-stats-grid w-100 p-3 rounded-4 bg-surface-subtle mb-4">
                <div class="row g-2">
                    @foreach($stats as $stat)
                    <div class="col text-center">
                        <div class="stat-value fw-bold text-primary">{{ $stat['value'] }}</div>
                        <div class="stat-label x-small text-muted">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Main Bottom Button --}}
            @if($card->main_button_text)
            <a href="{{ $card->main_button_action ?? '#' }}" class="btn btn-primary-soft-full w-100 rounded-pill py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                @if($card->main_button_icon) <i class="{{ $card->main_button_icon }}"></i> @endif
                {{ $card->main_button_text }}
            </a>
            @endif
        </div>
    </div>
</div>

<style>
    :root {
        /* Default fallback, overridden inline */
        --card-primary: #3b82f6; 
        --card-primary-light: #eff6ff;
        --card-surface-light: #f8fafc;
        --card-surface-subtle: #f1f5f9;
        --card-border-radius: 2rem;
        --card-elevation: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
    }
    
    .text-primary { color: var(--card-primary) !important; }

    .border-primary-thin { border: 1px solid var(--card-primary-light); }
    .shadow-soft-elevation { box-shadow: var(--card-elevation); }
    .rounded-5 { border-radius: var(--card-border-radius) !important; }

    /* Floating Badge */
    .card-floating-badge {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        background: var(--card-primary);
        color: white;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Internal Tag */
    .badge-soft-primary {
        background: var(--card-primary-light);
        color: var(--card-primary);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    /* Neumorphic Icon */
    .card-icon-neumorphic {
        width: 70px;
        height: 70px;
        background: var(--card-surface-light);
        border-radius: 20px;
        box-shadow: inset 2px 2px 5px rgba(0,0,0,0.03), inset -2px -2px 5px rgba(255,255,255,0.8);
    }

    /* Typography */
    .card-title-modern { font-family: 'Tajawal', sans-serif; font-size: 1.4rem; }
    .card-desc-text { font-size: 0.9rem; line-height: 1.5; }

    /* Buttons Stack */
    .btn-capsule-tint {
        background: var(--card-surface-light);
        color: var(--card-primary);
        border: 1px solid var(--card-primary-light);
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-capsule-tint:hover {
        background: var(--card-primary);
        color: white;
        transform: translateY(-2px);
    }

    /* Stats */
    .bg-surface-subtle { background: var(--card-surface-subtle); }
    .stat-value { font-size: 1.2rem; }

    /* Main Button */
    .btn-primary-soft-full {
        background: var(--card-primary);
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: 0.3s;
    }
    .btn-primary-soft-full:hover {
        filter: brightness(90%);
        transform: translateY(-2px);
        color: white;
    }
</style>

