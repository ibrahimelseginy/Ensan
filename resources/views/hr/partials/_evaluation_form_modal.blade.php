{{-- Shared HR Evaluation Form Modal (Employees & Volunteers) --}}
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ $actionRoute }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="bi bi-star-fill text-warning me-2"></i>{{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Live Summary Header --}}
                <div class="row g-3 align-items-center bg-light border rounded-4 p-3 mb-4 text-center">
                    <div class="col-4">
                        <div class="small text-muted">المجموع المحسوب</div>
                        <div class="h3 fw-bolder text-primary mb-0"><span id="{{ $modalId }}Total">100.0</span></div>
                        <div class="x-small text-muted">من 100 درجة</div>
                    </div>
                    <div class="col-4 border-start border-end">
                        <div class="small text-muted">النسبة المئوية</div>
                        <div class="h3 fw-bolder text-dark mb-0"><span id="{{ $modalId }}Pct">100.0%</span></div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">التقدير المتوقع</div>
                        <span id="{{ $modalId }}Rating" class="badge bg-success fs-6 px-3 py-1 rounded-pill">متميز</span>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">{{ $personLabel }} <span class="text-danger">*</span></label>
                        <select name="user_id" id="{{ $modalId }}UserSelect" class="form-select" required>
                            <option value="">— اختر {{ $personLabel }} —</option>
                            @foreach($people as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code ?: ($p->volunteer_code ?? '') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">تاريخ التقييم <span class="text-danger">*</span></label>
                        <input type="date" name="evaluation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">فترة التقييم</label>
                        <input type="text" name="evaluation_period" class="form-control" value="{{ date('Y-m') }}" placeholder="مثال: 2026-11">
                    </div>
                </div>

                <hr class="my-3">
                <h6 class="fw-bold mb-3 text-dark">بنود التقييم المعتمدة (أدخل درجة من 0.5 إلى 5.0 لكل بند)</h6>

                <div class="row g-3">
                    @foreach($criteria as $key => $info)
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-white">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <label class="form-label fw-bold mb-0 small">{{ $info['label'] }}</label>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">وزن {{ (int)($info['weight'] * 100) }}%</span>
                                </div>
                                <div class="text-muted x-small mb-2">{{ $info['desc'] }}</div>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" min="0.5" max="5" value="5"
                                           name="{{ $key }}"
                                           class="form-control text-center fw-bold {{ $modalId }}-score-input"
                                           data-weight="{{ $info['weight'] }}"
                                           data-key="{{ $key }}"
                                           required>
                                    <span class="input-group-text">من 5</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">نقاط القوة (اختياري)</label>
                        <textarea name="strengths" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">نقاط التحسين والتطوير (اختياري)</label>
                        <textarea name="improvements" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">ملاحظات عامة (اختياري)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-check2-circle me-1"></i> حفظ واعتماد التقييم
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const modalId = @json($modalId);
        function recalc() {
            let weighted = 0;
            document.querySelectorAll('.' + modalId + '-score-input').forEach(function (inp) {
                let v = parseFloat(inp.value) || 0;
                if (v < 0) v = 0;
                if (v > 5) v = 5;
                weighted += v * (parseFloat(inp.dataset.weight) || 0);
            });
            const score = Math.round((weighted * 20) * 10) / 10;
            let label = 'ضعيف', cls = 'badge bg-danger fs-6 px-3 py-1 rounded-pill';
            if (score >= 90) { label = 'متميز'; cls = 'badge bg-success fs-6 px-3 py-1 rounded-pill'; }
            else if (score >= 80) { label = 'جيد جداً'; cls = 'badge bg-primary fs-6 px-3 py-1 rounded-pill'; }
            else if (score >= 65) { label = 'جيد'; cls = 'badge bg-info text-dark fs-6 px-3 py-1 rounded-pill'; }
            else if (score >= 50) { label = 'مقبول'; cls = 'badge bg-warning text-dark fs-6 px-3 py-1 rounded-pill'; }

            const totalEl = document.getElementById(modalId + 'Total');
            const pctEl = document.getElementById(modalId + 'Pct');
            const ratEl = document.getElementById(modalId + 'Rating');
            if (totalEl) totalEl.textContent = score.toFixed(1);
            if (pctEl) pctEl.textContent = score.toFixed(1) + '%';
            if (ratEl) { ratEl.textContent = label; ratEl.className = cls; }
        }

        document.querySelectorAll('.' + modalId + '-score-input').forEach(el => el.addEventListener('input', recalc));
        recalc();

        window['open_' + modalId] = function (userId) {
            const sel = document.getElementById(modalId + 'UserSelect');
            if (sel && userId) sel.value = String(userId);
            const modalEl = document.getElementById(modalId);
            if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        };
    })();
</script>

