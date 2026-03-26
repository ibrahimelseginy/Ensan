@extends('layouts.app')
@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                    <h4 class="fw-bold text-dark mb-0">إضافة مهمة متطوع</h4>
                    <p class="text-muted small mt-1">قم بتعيين مهمة جديدة لمتطوع وتحديد تفاصيلها.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('volunteer-tasks.store') }}">
                        @csrf
                        <div class="row g-4">
                            <!-- Basic Info -->
                            <div class="col-md-8">
                                <label class="form-label fw-medium text-secondary">عنوان المهمة <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg fs-6" required placeholder="مثال: زيارة ميدانية، توزيع وجبات..." value="{{ request('subject_beneficiary_id') ? ('متابعة مستفيد #' . request('subject_beneficiary_id')) : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium text-secondary">الحالة</label>
                                <select name="status" class="form-select form-select-lg fs-6">
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="in_progress">قيد التنفيذ</option>
                                    <option value="done">منجزة</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">اسم النشاط التطوعي</label>
                                <input type="text" name="volunteer_activity_name" class="form-control form-control-lg fs-6" placeholder="مثال: حملة رمضان، زيارة دور أيتام">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">تاريخ الاستحقاق</label>
                                <input type="date" name="due_date" class="form-control form-control-lg fs-6">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium text-secondary">الوصف والتفاصيل</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="اكتب تفاصيل المهمة هنا...">{{ request('subject_beneficiary_id') ? ('مهمة مرتبطة بالمستفيد رقم ' . request('subject_beneficiary_id')) : '' }}</textarea>
                            </div>

                            <div class="col-12"><hr class="text-muted opacity-25"></div>

                            <!-- Assignments -->
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">المتطوع (المكلّف)</label>
                                <select name="assigned_to" class="form-select form-select-lg fs-6">
                                    <option value="">— اختر متطوع —</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" @selected((string)request('assigned_to')===(string)$u->id)>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">المكلِّف (المشرف)</label>
                                <select name="assigned_by" class="form-select form-select-lg fs-6">
                                    <option value="">— اختر مشرف —</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12"><hr class="text-muted opacity-25"></div>

                            <!-- Relations -->
                            <div class="col-md-4">
                                <label class="form-label fw-medium text-secondary">تبعية المهمة (اختياري)</label>
                                <select id="relType" class="form-select form-select-lg fs-6">
                                    <option value="">— عام —</option>
                                    <option value="project">مشروع</option>
                                    <option value="campaign">حملة</option>
                                    <option value="guest_house">دار ضيافة</option>
                                </select>
                            </div>
                            <div class="col-md-8 rel rel-project" style="display:none">
                                <label class="form-label fw-medium text-secondary">اختر المشروع</label>
                                <select name="project_id" class="form-select form-select-lg fs-6" id="relProject" disabled>
                                    <option value="">—</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 rel rel-campaign" style="display:none">
                                <label class="form-label fw-medium text-secondary">اختر الحملة</label>
                                <select name="campaign_id" class="form-select form-select-lg fs-6" id="relCampaign" disabled>
                                    <option value="">—</option>
                                    @foreach($campaigns as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} {{ $c->season_year ? '(' . $c->season_year . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 rel rel-guest_house" style="display:none">
                                <label class="form-label fw-medium text-secondary">اختر دار الضيافة</label>
                                <select name="guest_house_id" class="form-select form-select-lg fs-6" id="relGuestHouse" disabled>
                                    <option value="">—</option>
                                    @foreach($guestHouses as $gh)
                                        <option value="{{ $gh->id }}">{{ $gh->name }}{{ $gh->location ? (' - ' . $gh->location) : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">تقييم الأداء</h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-medium text-secondary d-block">التقييم (من 5)</label>
                                        <div class="btn-group" role="group" aria-label="Rating">
                                            @for($i=1; $i<=5; $i++)
                                                <input type="radio" class="btn-check" name="rating" id="rating{{$i}}" value="{{$i}}" autocomplete="off">
                                                <label class="btn btn-outline-warning" for="rating{{$i}}">{{$i}} <i class="bi bi-star-fill"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-medium text-secondary">ملاحظات التقييم</label>
                                        <textarea name="evaluation_notes" class="form-control" rows="2" placeholder="أضف ملاحظات حول أداء المتطوع في هذه المهمة..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end align-items-center mt-5">
                            <a href="{{ route('volunteer-tasks.index') }}" class="btn btn-light btn-lg px-4 me-2 rounded-pill text-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">حفظ المهمة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var sel=document.getElementById('relType');
    function toggle(){
        var v=sel?sel.value:'';
        ['project','campaign','guest_house'].forEach(function(k){
            var box=document.querySelector('.rel.rel-'+k);
            var inp=document.getElementById('rel'+k.replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); }).replace('_',''));
            if(!inp && k=='guest_house') inp=document.getElementById('relGuestHouse'); 
            
            // Adjust ID construction if simple camelCase didn't match
            if(k=='project') inp=document.getElementById('relProject');
            if(k=='campaign') inp=document.getElementById('relCampaign');

            if(box){
                if(k==v){
                    box.style.display='block';
                    if(inp) inp.disabled=false;
                }else{
                    box.style.display='none';
                    if(inp) inp.disabled=true;
                }
            }
        });
    }
    if(sel){
        sel.addEventListener('change',toggle);
        toggle();
    }
});
</script>
@endsection
