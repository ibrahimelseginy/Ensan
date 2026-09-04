@php
    $reviewModelInfo = \App\Support\ChangeRequestPresentation::modelInfo($reviewNoticeRequest);
    $reviewActionInfo = \App\Support\ChangeRequestPresentation::actionInfo($reviewNoticeRequest);
    $reviewChanges = \App\Support\ChangeRequestPresentation::changes($reviewNoticeRequest);
    $reviewFields = \App\Support\ChangeRequestPresentation::fields($reviewNoticeRequest);
    $reviewSubjectName = \App\Support\ChangeRequestPresentation::subjectName($reviewNoticeRequest);
    $reviewIsDestructive = in_array($reviewNoticeRequest->action, ['delete', 'cancel'], true);
@endphp

<div
    class="modal fade review-action-modal"
    id="reviewRequestActionModal"
    tabindex="-1"
    aria-labelledby="review-request-action-title"
    aria-hidden="true"
>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content {{ $reviewIsDestructive ? 'review-modal--destructive' : '' }}">
            <div class="modal-header">
                <div class="review-modal-heading">
                    <span class="review-modal-icon review-modal-icon--{{ $reviewActionInfo['color'] }}">
                        <i class="bi bi-{{ $reviewActionInfo['icon'] }}"></i>
                    </span>
                    <div>
                        <span class="review-modal-eyebrow">طلب مراجعة #{{ $reviewNoticeRequest->id }}</span>
                        <h2 class="modal-title" id="review-request-action-title">{{ $reviewActionInfo['verb'] }}</h2>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <div class="modal-body">
                <section class="review-summary-grid" aria-label="ملخص طلب المراجعة">
                    <div class="review-summary-item">
                        <i class="bi bi-person-fill"></i>
                        <span>
                            <small>مقدم الطلب</small>
                            <strong>{{ $reviewNoticeRequest->user->name ?? 'غير معروف' }}</strong>
                        </span>
                    </div>
                    <div class="review-summary-item">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>
                            <small>السجل المتأثر</small>
                            <strong>{{ $reviewSubjectName }}</strong>
                        </span>
                    </div>
                    <div class="review-summary-item">
                        <i class="bi bi-tag-fill"></i>
                        <span>
                            <small>نوع الإجراء</small>
                            <strong>{{ $reviewActionInfo['label'] }} {{ $reviewModelInfo['label'] }}</strong>
                        </span>
                    </div>
                    <div class="review-summary-item">
                        <i class="bi bi-clock-history"></i>
                        <span>
                            <small>وقت الطلب</small>
                            <strong>{{ $reviewNoticeRequest->created_at?->format('Y-m-d H:i') }}</strong>
                        </span>
                    </div>
                </section>

                <div class="review-effect {{ $reviewIsDestructive ? 'review-effect--danger' : '' }}">
                    <i class="bi bi-{{ $reviewIsDestructive ? 'exclamation-triangle-fill' : 'info-circle-fill' }}"></i>
                    <div>
                        <strong>ماذا سيحدث عند التنفيذ؟</strong>
                        <p>{{ $reviewActionInfo['effect'] }}</p>
                    </div>
                </div>

                <section class="review-details-section" aria-labelledby="review-details-title">
                    <div class="review-section-title">
                        <span>
                            <i class="bi bi-card-list"></i>
                            <strong id="review-details-title">
                                {{ $reviewChanges ? 'التغييرات المطلوبة بالتفصيل' : 'بيانات الإجراء بالكامل' }}
                            </strong>
                        </span>
                        <span class="review-fields-count">
                            {{ count($reviewChanges ?: $reviewFields) }} {{ $reviewChanges ? 'تغييرات' : 'حقول' }}
                        </span>
                    </div>

                    @if($reviewChanges)
                        <div class="review-diff-list">
                            @foreach($reviewChanges as $change)
                                <article class="review-diff-card">
                                    <h3>{{ $change['label'] }}</h3>
                                    <div class="review-diff-values">
                                        <div class="review-value review-value--old">
                                            <small>القيمة الحالية</small>
                                            <span @class(['review-preformatted' => $change['multiline']])>{{ $change['from'] }}</span>
                                        </div>
                                        <i class="bi bi-arrow-left review-diff-arrow" aria-hidden="true"></i>
                                        <div class="review-value review-value--new">
                                            <small>القيمة الجديدة</small>
                                            <span @class(['review-preformatted' => $change['multiline']])>{{ $change['to'] }}</span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @elseif($reviewFields)
                        <div class="review-fields-grid">
                            @foreach($reviewFields as $field)
                                <article class="review-field-card">
                                    <small>{{ $field['label'] }}</small>
                                    <strong @class(['review-preformatted' => $field['multiline']])>{{ $field['value'] }}</strong>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="review-empty-details">
                            <i class="bi bi-info-circle"></i>
                            لا توجد قيم إضافية مطلوبة لتنفيذ هذا الإجراء.
                        </div>
                    @endif
                </section>

                <section class="review-execution-flow" aria-labelledby="review-flow-title">
                    <div class="review-section-title">
                        <span>
                            <i class="bi bi-diagram-3-fill"></i>
                            <strong id="review-flow-title">مسار تنفيذ الإجراء</strong>
                        </span>
                    </div>
                    <div class="review-flow-steps">
                        <div class="review-flow-step review-flow-step--done">
                            <span><i class="bi bi-check-lg"></i></span>
                            <div><strong>تم إرسال الطلب</strong><small>حُفظت البيانات ولم تُطبق بعد</small></div>
                        </div>
                        <div class="review-flow-line"></div>
                        <div class="review-flow-step review-flow-step--current">
                            <span>2</span>
                            <div><strong>مراجعة المسؤول</strong><small>التأكد من القيم والآثار</small></div>
                        </div>
                        <div class="review-flow-line"></div>
                        <div class="review-flow-step">
                            <span>3</span>
                            <div><strong>التنفيذ النهائي</strong><small>يتم فقط بعد الضغط على موافقة وتنفيذ</small></div>
                        </div>
                    </div>
                </section>

                @if($canExecuteReviewRequest)
                    <div class="collapse review-reject-panel" id="reviewRejectPanel">
                        <form method="POST" action="{{ route('change-requests.reject', $reviewNoticeRequest) }}">
                            @csrf
                            <label for="inline-rejection-reason" class="form-label">سبب رفض الطلب</label>
                            <textarea
                                id="inline-rejection-reason"
                                name="rejection_reason"
                                class="form-control"
                                rows="3"
                                maxlength="1000"
                                placeholder="اكتب سببًا واضحًا ليظهر لصاحب الطلب..."
                            ></textarea>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle-fill"></i>
                                    تأكيد الرفض
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#reviewRejectPanel">
                                    تراجع
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                @if($canExecuteReviewRequest)
                    <form
                        method="POST"
                        action="{{ route('change-requests.approve', $reviewNoticeRequest) }}"
                        onsubmit="return confirm('تأكيد الموافقة وتنفيذ الإجراء الآن؟');"
                    >
                        @csrf
                        <button type="submit" class="btn review-approve-button">
                            <i class="bi bi-check2-circle"></i>
                            موافقة وتنفيذ الآن
                        </button>
                    </form>
                    <button
                        type="button"
                        class="btn review-reject-button"
                        data-bs-toggle="collapse"
                        data-bs-target="#reviewRejectPanel"
                    >
                        <i class="bi bi-x-circle"></i>
                        رفض الطلب
                    </button>
                @else
                    <div class="review-waiting-message">
                        <i class="bi bi-hourglass-split"></i>
                        الطلب بانتظار قرار المسؤول، ولم يتم تطبيق التغيير بعد.
                    </div>
                @endif

                @if($reviewNoticeUser?->hasPermission('manage_change_requests'))
                    <a href="{{ route('change-requests.index') }}" class="btn review-history-button">
                        <i class="bi bi-clock-history"></i>
                        سجل المراجعات
                    </a>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
