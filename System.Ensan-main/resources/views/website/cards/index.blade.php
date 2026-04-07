@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="row g-4">
        {{-- Card Manager Form --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i> إضافة / تعديل بطاقة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('website.cards.store') }}" method="POST" enctype="multipart/form-data" id="cardForm" onsubmit="prepareData()">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="formMethod">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">عنوان البطاقة</label>
                            <input type="text" name="title" class="form-control" placeholder="مثال: كفالة يتيم" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">الوصف المختصر</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="وصف قصير..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">صورة/أيقونة</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="badge_visible" id="badgeVisible">
                                <label class="form-check-label small fw-bold" for="badgeVisible">إظهار الشارة العائمة</label>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <input type="text" name="badge_text" class="form-control form-control-sm" placeholder="نص الشارة">
                            </div>
                            <div class="col-6">
                                <input type="text" name="badge_icon" class="form-control form-control-sm" placeholder="أيقونة (bi-star)">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">وسم داخلي (Tag)</label>
                            <input type="text" name="tag_text" class="form-control form-control-sm" placeholder="مثال: عاجل">
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">الإحصائيات (Stats)</label>
                            <div id="statsWrapper">
                                <div class="row g-2 mb-2 stat-row">
                                    <div class="col-5"><input type="text" class="form-control form-control-sm stat-val" placeholder="قيمة (500)"></div>
                                    <div class="col-5"><input type="text" class="form-control form-control-sm stat-lbl" placeholder="تسمية (مستفيد)"></div>
                                    <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeRow(this)">X</button></div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-2" onclick="addStatRow()">+ إضافة إحصائية</button>
                            <input type="hidden" name="stats_data" id="statsDataInput">
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">الأزرار (Stack)</label>
                            <div id="buttonsWrapper">
                                <div class="row g-2 mb-2 btn-row">
                                    <div class="col-5"><input type="text" class="form-control form-control-sm btn-txt" placeholder="نص الزر"></div>
                                    <div class="col-5"><input type="text" class="form-control form-control-sm btn-act" placeholder="رابط/أكشن"></div>
                                    <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeRow(this)">X</button></div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-2" onclick="addBtnRow()">+ إضافة زر</button>
                            <input type="hidden" name="buttons_data" id="buttonsDataInput">
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">الزر الرئيسي السفلي</label>
                            <input type="text" name="main_button_text" class="form-control form-control-sm mb-2" placeholder="نص الزر">
                            <input type="text" name="main_button_action" class="form-control form-control-sm mb-2" placeholder="رابط الأكشن">
                            <input type="text" name="main_button_icon" class="form-control form-control-sm" placeholder="أيقونة (bi-heart-fill)">
                        </div>

                         <div class="mb-3">
                            <label class="form-label small fw-bold">الترتيب</label>
                            <input type="number" name="sort_order" class="form-control form-control-sm" value="0">
                        </div>

                         <div class="mb-3">
                            <label class="form-label small fw-bold">لون البطاقة (اختياري)</label>
                            <input type="color" name="card_color" class="form-control form-control-color w-100" value="#3b82f6" title="اختر لوناً">
                        </div>

                         <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label small fw-bold" for="isActive">البطاقة نشطة</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">حفظ البطاقة</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Preview Grid --}}
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4">معاينة البطاقات الحالية</h4>
            <div class="row g-4">
                @foreach($cards as $card)
                <div class="col-md-6 col-xl-6">
                    <div class="position-relative group-action">
                        <x-dynamic-card :card="$card" />
                        
                        {{-- Admin Controls Overlay --}}
                        <div class="admin-overlay position-absolute top-0 end-0 p-2 d-flex gap-2" style="z-index: 20;">
                            <button class="btn btn-sm btn-light shadow-sm rounded-circle" onclick='editCard(@json($card))'><i class="bi bi-pencil-fill text-primary"></i></button>
                            <form action="{{ route('website.cards.destroy', $card) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light shadow-sm rounded-circle text-danger"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function addStatRow() {
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 stat-row';
        div.innerHTML = `
            <div class="col-5"><input type="text" class="form-control form-control-sm stat-val" placeholder="قيمة"></div>
            <div class="col-5"><input type="text" class="form-control form-control-sm stat-lbl" placeholder="تسمية"></div>
            <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeRow(this)">X</button></div>
        `;
        document.getElementById('statsWrapper').appendChild(div);
    }

    function addBtnRow() {
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 btn-row';
        div.innerHTML = `
            <div class="col-5"><input type="text" class="form-control form-control-sm btn-txt" placeholder="نص"></div>
            <div class="col-5"><input type="text" class="form-control form-control-sm btn-act" placeholder="رابط"></div>
            <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeRow(this)">X</button></div>
        `;
        document.getElementById('buttonsWrapper').appendChild(div);
    }

    function removeRow(btn) {
        btn.closest('.row').remove();
    }

    function prepareData() {
        // Collect Stats
        const stats = [];
        document.querySelectorAll('.stat-row').forEach(row => {
            const val = row.querySelector('.stat-val').value;
            const lbl = row.querySelector('.stat-lbl').value;
            if(val) stats.push({value: val, label: lbl});
        });
        document.getElementById('statsDataInput').value = JSON.stringify(stats);

        // Collect Buttons
        const btns = [];
        document.querySelectorAll('.btn-row').forEach(row => {
            const txt = row.querySelector('.btn-txt').value;
            const act = row.querySelector('.btn-act').value;
            if(txt) btns.push({text: txt, action: act});
        });
        document.getElementById('buttonsDataInput').value = JSON.stringify(btns);
    }

    function editCard(card) {
        const form = document.getElementById('cardForm');
        form.action = `/admin/website/cards/${card.id}`;
        document.getElementById('formMethod').value = 'PUT';

        // Fill basic fields
        form.querySelector('[name="title"]').value = card.title;
        form.querySelector('[name="description"]').value = card.description || '';
        form.querySelector('[name="badge_text"]').value = card.badge_text || '';
        form.querySelector('[name="badge_icon"]').value = card.badge_icon || '';
        form.querySelector('[name="badge_visible"]').checked = card.badge_visible;
        form.querySelector('[name="tag_text"]').value = card.tag_text || '';
        form.querySelector('[name="main_button_text"]').value = card.main_button_text || '';
        form.querySelector('[name="main_button_action"]').value = card.main_button_action || '';
        form.querySelector('[name="main_button_icon"]').value = card.main_button_icon || '';
        form.querySelector('[name="sort_order"]').value = card.sort_order || 0;
        form.querySelector('[name="card_color"]').value = card.card_color || '#3b82f6';
        form.querySelector('[name="is_active"]').checked = (card.is_active == 1);

        // Clear and refill stats
        document.getElementById('statsWrapper').innerHTML = '';
        if(card.stats_data && card.stats_data.length) {
            card.stats_data.forEach(stat => {
                const div = document.createElement('div');
                div.className = 'row g-2 mb-2 stat-row';
                div.innerHTML = `
                    <div class="col-5"><input type="text" class="form-control form-control-sm stat-val" value="${stat.value}"></div>
                    <div class="col-5"><input type="text" class="form-control form-control-sm stat-lbl" value="${stat.label}"></div>
                    <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeRow(this)">X</button></div>
                `;
                document.getElementById('statsWrapper').appendChild(div);
            });
        }

        // Clear and refill buttons
        document.getElementById('buttonsWrapper').innerHTML = '';
        if(card.buttons_data && card.buttons_data.length) {
            card.buttons_data.forEach(btn => {
                const div = document.createElement('div');
                div.className = 'row g-2 mb-2 btn-row';
                div.innerHTML = `
                    <div class="col-5"><input type="text" class="form-control form-control-sm btn-txt" value="${btn.text}"></div>
                    <div class="col-5"><input type="text" class="form-control form-control-sm btn-act" value="${btn.action}"></div>
                    <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeRow(this)">X</button></div>
                `;
                document.getElementById('buttonsWrapper').appendChild(div);
            });
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endsection


