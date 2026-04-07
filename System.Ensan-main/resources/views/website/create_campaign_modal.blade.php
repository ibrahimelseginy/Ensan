
<div class="modal fade" id="createCampaignModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('website.campaigns.store') }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">إضافة حملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">اسم الحملة</label>
                        <input type="text" name="name" class="form-control form-control-sm py-2 shadow-none" placeholder="اسم الحملة..." required>
                        
                        <label class="form-label small fw-bold mt-2">عنوان الموسم (مثال: شتاء 2024)</label>
                        <input type="text" name="season_title" class="form-control form-control-sm py-2 shadow-none" placeholder="شتاء 2024">

                        <label class="form-label small fw-bold mt-2">سنة الموسم</label>
                        <input type="number" name="season_year" class="form-control form-control-sm py-2 shadow-none" value="{{ date('Y') }}" required>

                        <div class="row g-2 mt-2">
                            <div class="col-6">
                                <label class="form-label x-small fw-bold">المبلغ المستهدف</label>
                                <input type="number" name="goal_amount" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label x-small fw-bold">وحدة الهدف (مثال: كرتونة)</label>
                                <input type="text" name="goal_unit" class="form-control form-control-sm" value="جنيه">
                            </div>
                            <div class="col-6">
                                <label class="form-label x-small fw-bold">المبلغ الحالي</label>
                                <input type="number" name="current_amount" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-6 mt-2">
                                <label class="form-label x-small fw-bold">عدد المستفيدين</label>
                                <input type="number" name="beneficiaries_count" class="form-control form-control-sm" value="0">
                            </div>
                             <div class="col-6 mt-2">
                                <label class="form-label x-small fw-bold">سعر السهم</label>
                                <input type="number" name="share_price" class="form-control form-control-sm text-start" value="0">
                            </div>
                            <div class="col-12 mt-2">
                                <label class="form-label x-small fw-bold">حالة الحملة</label>
                                <select name="status" class="form-select form-select-sm ws-input">
                                    <option value="active" class="text-dark" selected>نشطة الآن</option>
                                    <option value="upcoming" class="text-dark">قادمة (تبدأ قريباً)</option>
                                    <option value="ended" class="text-dark">منتهية</option>
                                    <option value="archived" class="text-dark">مؤرشفة</option>
                                </select>
                            </div>
                            <div class="col-6 mt-2">
                                <label class="form-label x-small fw-bold">تاريخ البدء</label>
                                <input type="date" name="start_date" class="form-control form-control-sm">
                            </div>
                            <div class="col-6 mt-2">
                                <label class="form-label x-small fw-bold">تاريخ الانتهاء</label>
                                <input type="date" name="end_date" class="form-control form-control-sm">
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
                            <img src="" class="w-100 h-100 object-fit-cover d-none" id="newCampImg">
                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted" id="newCampPlaceholder">
                                <i class="bi bi-megaphone display-5 mb-2"></i>
                                <span class="x-small">ارفع بوستر الحملة</span>
                            </div>
                            <input type="file" name="image" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                    onchange="document.getElementById('newCampImg').src = window.URL.createObjectURL(this.files[0]); document.getElementById('newCampImg').classList.remove('d-none'); document.getElementById('newCampPlaceholder').classList.add('d-none');">
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-sm btn-warning rounded-pill px-4 shadow-sm fw-bold">إضافة الحملة</button>
            </div>
        </form>
    </div>
</div>



