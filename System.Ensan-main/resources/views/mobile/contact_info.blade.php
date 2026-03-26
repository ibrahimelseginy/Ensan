@extends('layouts.app')

@section('content')
<div class="contact-info-page">
    {{-- Premium Header --}}
    <div class="premium-hero-sleek" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
        <div class="container-fluid hero-content-wrapper text-end">
            <h1 class="display-4 fw-800 text-white mb-2">إدارة تواصل معنا (App)</h1>
            <p class="lead text-white-50">تعديل الأسماء وأرقام الهواتف مباشرة في الصفحة.</p>
        </div>
    </div>

    <div class="container-fluid py-5 px-4">
        <div class="glass-card p-5 animate-up">
            <h4 class="fw-bold mb-4 border-bottom border-white border-opacity-10 pb-3">قائمة جهات الاتصال</h4>
            
            <div id="contacts-container">
                @foreach($contacts as $contact)
                <div class="contact-entry-row mb-5 pb-5 border-bottom border-white border-opacity-5" data-id="{{ $contact->id }}">
                    <form action="{{ route('mobile.contact_info.update', $contact) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <label class="label-lux">الاسم / الجهة</label>
                                <input type="text" name="name" class="field-lux fs-5 fw-bold" value="{{ $contact->name }}" required>
                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-success rounded-pill px-4 flex-grow-1 py-2 fw-bold">حفظ التغييرات <i class="bi bi-check-lg ms-1"></i></button>
                                    <button type="button" class="btn btn-outline-danger btn-delete-contact rounded-circle p-2" onclick="deleteContact({{ $contact->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <label class="label-lux">أرقام الهواتف</label>
                                <div class="phones-input-list d-flex flex-wrap gap-2">
                                    @foreach($contact->phones as $phone)
                                    <div class="phone-chip d-flex gap-2">
                                        <input type="text" name="phones[]" class="field-lux-small" value="{{ $phone->phone }}" required style="direction: ltr; width: 180px;">
                                        <button type="button" class="btn btn-link text-danger p-0 ms-n4 remove-phone-chip"><i class="bi bi-x-circle-fill"></i></button>
                                    </div>
                                    @endforeach
                                    <button type="button" class="btn btn-glass-indigo btn-add-phone-chip rounded-pill px-3 py-2">
                                        رقم جديد <i class="bi bi-phone-vibrate ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="delete-form-{{ $contact->id }}" action="{{ route('mobile.contact_info.destroy', $contact) }}" method="POST" class="d-none">
                        @csrf @method('DELETE')
                    </form>
                </div>
                @endforeach
            </div>

            {{-- Add New Entry Row --}}
            <div class="add-new-entry-box mt-4 p-4 rounded-4 bg-white bg-opacity-5 border border-dashed border-secondary">
                <h5 class="fw-bold text-white-50 mb-4"><i class="bi bi-plus-circle ms-2"></i> إضافة جهة اتصال جديدة</h5>
                <form action="{{ route('mobile.contact_info.store') }}" method="POST">
                    @csrf
                    <div class="row g-4 align-items-end">
                        <div class="col-lg-4">
                            <label class="label-lux">الاسم</label>
                            <input type="text" name="name" class="field-lux" placeholder="مثلاً: فرع القاهرة" required>
                        </div>
                        <div class="col-lg-6">
                            <label class="label-lux">رقم الهاتف الأول</label>
                            <div class="phones-input-list-new d-flex flex-wrap gap-2">
                                <input type="text" name="phones[]" class="field-lux" placeholder="01xxxxxxxxx" required style="direction: ltr;">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow">إضافة البيانات <i class="bi bi-plus-lg ms-1"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .premium-hero-sleek { padding: 40px 5%; border-radius: 0 0 30px 30px; }
    .glass-card { background: #0b111a !important; border: 1px solid #1e293b !important; border-radius: 30px; box-shadow: 0 30px 60px rgba(0,0,0,0.5); }
    
    .label-lux { color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; display: block; }
    .field-lux { background: #161e2b; border: 1px solid #1e293b; color: white; border-radius: 12px; padding: 12px 20px; transition: 0.3s; width: 100%; }
    .field-lux:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    
    .field-lux-small { background: #1a2332; border: 1px solid #334155; color: white; border-radius: 100px; padding: 8px 35px 8px 15px; font-weight: 700; transition: 0.3s; }
    .field-lux-small:focus { border-color: #10b981; background: #0f172a; outline: none; }
    
    .phone-chip { position: relative; }
    .remove-phone-chip { position: absolute; right: 10px; top: 10px; font-size: 1.1rem; opacity: 0.7; transition: 0.2s; }
    .remove-phone-chip:hover { opacity: 1; transform: scale(1.1); }
    
    .btn-glass-indigo { background: rgba(99, 102, 241, 0.1); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2); }
    
    .border-dashed { border-style: dashed !important; }
    .animate-up { animation: fadeInUp 0.6s both; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    
    .ms-n4 { margin-right: -28px; z-index: 10; }
</style>

<script>
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-add-phone-chip') || e.target.closest('.btn-add-phone-chip')) {
        const btn = e.target.classList.contains('btn-add-phone-chip') ? e.target : e.target.closest('.btn-add-phone-chip');
        const container = btn.parentElement;
        const newChip = document.createElement('div');
        newChip.className = 'phone-chip d-flex gap-2 animate-up';
        newChip.innerHTML = `
            <input type="text" name="phones[]" class="field-lux-small" value="" required style="direction: ltr; width: 180px;" placeholder="رقم جديد">
            <button type="button" class="btn btn-link text-danger p-0 ms-n4 remove-phone-chip"><i class="bi bi-x-circle-fill"></i></button>
        `;
        container.insertBefore(newChip, btn);
    }
    
    if (e.target.classList.contains('remove-phone-chip') || e.target.closest('.remove-phone-chip')) {
        const chip = e.target.closest('.phone-chip');
        chip.remove();
    }
});

function deleteContact(id) {
    if (confirm('هل أنت متأكد من حذف جهة الاتصال هذه نهائياً؟')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endsection
