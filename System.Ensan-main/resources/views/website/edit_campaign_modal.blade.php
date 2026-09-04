<div class="modal fade" id="editCampaignModal{{ $campaign->id }}" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('website.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">تعديل الحملة: {{ $campaign->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 small fw-bold">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">اسم الحملة</label>
                        <input type="text" name="name" class="form-control form-control-sm py-2 shadow-none" value="{{ $campaign->name }}" required>
                        
                        <label class="form-label small fw-bold mt-2">عنوان الموسم (مثال: شتاء 2024)</label>
                        <input type="text" name="season_title" class="form-control form-control-sm py-2 shadow-none" value="{{ $campaign->season_title }}">

                        <label class="form-label small fw-bold mt-2">سنة الموسم</label>
                        <input type="number" name="season_year" class="form-control form-control-sm py-2 shadow-none" value="{{ $campaign->season_year ?? date('Y') }}" required>

                        <div class="row g-2 mt-2">
                            <div class="col-6">
                                <label class="form-label x-small fw-bold">المبلغ المستهدف</label>
                                <input type="number" name="goal_amount" class="form-control form-control-sm" value="{{ $campaign->goal_amount }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label x-small fw-bold">وحدة الهدف (مثال: كرتونة)</label>
                                <input type="text" name="goal_unit" class="form-control form-control-sm" value="{{ $campaign->goal_unit }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label x-small fw-bold">المبلغ الحالي</label>
                                <input type="number" name="current_amount" class="form-control form-control-sm" value="{{ $campaign->current_amount }}">
                            </div>
                            <div class="col-6 mt-2">
                                <label class="form-label x-small fw-bold">عدد المستفيدين</label>
                                <input type="number" name="beneficiaries_count" class="form-control form-control-sm" value="{{ $campaign->beneficiaries_count }}">
                            </div>
                             <div class="col-6 mt-2">
                                <label class="form-label x-small fw-bold">سعر السهم</label>
                                <input type="number" name="share_price" class="form-control form-control-sm text-start" value="{{ $campaign->share_price }}">
                            </div>
                            <div class="col-12 mt-2">
                                <label class="form-label x-small fw-bold">حالة الحملة</label>
                                <select name="status" class="form-select form-select-sm ws-input">
                                    <option value="active" class="text-dark" {{ $campaign->status == 'active' ? 'selected' : '' }}>نشطة الآن</option>
                                    <option value="upcoming" class="text-dark" {{ $campaign->status == 'upcoming' ? 'selected' : '' }}>قادمة (تبدأ قريباً)</option>
                                    <option value="ended" class="text-dark" {{ $campaign->status == 'ended' ? 'selected' : '' }}>منتهية</option>
                                    <option value="archived" class="text-dark" {{ $campaign->status == 'archived' ? 'selected' : '' }}>مؤرشفة</option>
                                </select>
                            </div>
                            <div class="col-6 mt-2">
                                <label class="form-label x-small fw-bold">تاريخ البدء</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-6 mt-2">
                                <label class="form-label x-small fw-bold">تاريخ الانتهاء</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-12 mt-2">
                                <label class="form-label x-small fw-bold ws-label">أيقونة الحملة (Icon)</label>
                                <input type="file" name="icon" class="form-control form-control-sm ws-input" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center mt-3">
                        <label class="form-label d-block text-muted small fw-bold">بوستر الحملة الأساسي</label>
                        <div class="position-relative d-inline-block shadow-sm rounded-4 overflow-hidden" 
                                style="width: 100%; height: 200px; background: #f8fafc; border: 2px dashed #e2e8f0;">
                            @if($campaign->image_path)
                                <img src="{{ $campaign->image_url }}" class="w-100 h-100 object-fit-cover" id="editCampImg{{ $campaign->id }}">
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted d-none" id="editCampPlaceholder{{ $campaign->id }}">
                                    <i class="bi bi-megaphone display-5 mb-2"></i>
                                    <span class="x-small">تغيير بوستر الحملة</span>
                                </div>
                            @else
                                <img src="" class="w-100 h-100 object-fit-cover d-none" id="editCampImg{{ $campaign->id }}">
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted" id="editCampPlaceholder{{ $campaign->id }}">
                                    <i class="bi bi-megaphone display-5 mb-2"></i>
                                    <span class="x-small">ارفع بوستر الحملة</span>
                                </div>
                            @endif
                            <input type="file" name="image" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                    onchange="document.getElementById('editCampImg{{ $campaign->id }}').src = window.URL.createObjectURL(this.files[0]); document.getElementById('editCampImg{{ $campaign->id }}').classList.remove('d-none'); document.getElementById('editCampPlaceholder{{ $campaign->id }}').classList.add('d-none');">
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm fw-bold">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>




