@php
    $toastModel = \App\Support\ChangeRequestPresentation::modelInfo($reviewNoticeRequest);
    $toastAction = \App\Support\ChangeRequestPresentation::actionInfo($reviewNoticeRequest);
@endphp

<div class="toast-elite info review-request-toast animate-toast-in" data-persistent="true">
    <div class="toast-icon-wrapper">
        <i class="bi bi-{{ $toastAction['icon'] }} toast-icon"></i>
    </div>
    <div class="toast-text">
        <div class="toast-title">طلب قيد المراجعة #{{ $reviewNoticeRequest->id }}</div>
        <span class="toast-msg">
            {{ $toastAction['verb'] }}: {{ \App\Support\ChangeRequestPresentation::subjectName($reviewNoticeRequest) }}
        </span>
        <button
            type="button"
            class="toast-review-action"
            data-bs-toggle="modal"
            data-bs-target="#reviewRequestActionModal"
        >
            <i class="bi bi-eye-fill"></i>
            عرض التفاصيل {{ $canExecuteReviewRequest ? 'وتنفيذ الإجراء' : '' }}
        </button>
    </div>
    <button type="button" class="btn-close-toast" onclick="this.parentElement.remove()" aria-label="إغلاق الإشعار">&times;</button>
</div>
