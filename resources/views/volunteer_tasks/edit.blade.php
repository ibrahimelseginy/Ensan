@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                    <h4 class="fw-bold text-dark mb-0">تعديل مهمة متطوع</h4>
                    <p class="text-muted small mt-1">تحديث تفاصيل المهمة وحالتها.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('volunteer-tasks.update', $task) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="form-label fw-medium text-secondary">عنوان المهمة <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg fs-6" value="{{ $task->title }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium text-secondary">حالة المهمة</label>
                                <select name="status" class="form-select form-select-lg fs-6">
                                    <option value="pending" @selected($task->status==='pending')>قيد الانتظار</option>
                                    <option value="in_progress" @selected($task->status==='in_progress')>قيد التنفيذ</option>
                                    <option value="done" @selected($task->status==='done')>منجزة</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium text-secondary">وصف المهمة</label>
                                <textarea name="description" class="form-control form-control-lg fs-6" rows="4">{{ $task->description }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">اسم نشاط التطوع</label>
                                <input type="text" name="volunteer_activity_name" class="form-control form-control-lg fs-6" value="{{ $task->volunteer_activity_name }}" placeholder="مثال: حملة إفطار صائم">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">تاريخ الاستحقاق</label>
                                <input type="date" name="due_date" class="form-control form-control-lg fs-6" value="{{ $task->due_date?->format('Y-m-d') }}">
                            </div>

                            <div class="col-12"><hr class="text-muted opacity-25"></div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">المتطوع (المكلّف)</label>
                                <select name="assigned_to" class="form-select form-select-lg fs-6">
                                    <option value="">-- اختر المتطوع --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" @selected($task->assigned_to==$u->id)>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">المكلِّف</label>
                                <select name="assigned_by" class="form-select form-select-lg fs-6">
                                    <option value="">-- اختر الموظف --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" @selected($task->assigned_by==$u->id)>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12"><hr class="text-muted opacity-25"></div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">ارتباط المهمة بـ</label>
                                @php $rel = $task->project_id ? 'project' : ($task->campaign_id ? 'campaign' : ($task->guest_house_id ? 'guest_house' : '')); @endphp
                                <select id="relType" class="form-select form-select-lg fs-6">
                                    <option value="" @selected($rel==='')>-- لا يوجد ارتباط --</option>
                                    <option value="project" @selected($rel==='project')>مشروع</option>
                                    <option value="campaign" @selected($rel==='campaign')>حملة</option>
                                    <option value="guest_house" @selected($rel==='guest_house')>دار ضيافة</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="rel rel-project" style="display:none">
                                    <label class="form-label fw-medium text-secondary">المشروع</label>
                                    <select name="project_id" class="form-select form-select-lg fs-6" id="relProject" disabled>
                                        <option value="">اختر المشروع...</option>
                                        @foreach($projects as $p)
                                            <option value="{{ $p->id }}" @selected($task->project_id==$p->id)>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="rel rel-campaign" style="display:none">
                                    <label class="form-label fw-medium text-secondary">الحملة</label>
                                    <select name="campaign_id" class="form-select form-select-lg fs-6" id="relCampaign" disabled>
                                        <option value="">اختر الحملة...</option>
                                        @foreach($campaigns as $c)
                                            <option value="{{ $c->id }}" @selected($task->campaign_id==$c->id)>{{ $c->name }} {{ $c->season_year ? '(' . $c->season_year . ')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="rel rel-guest_house" style="display:none">
                                    <label class="form-label fw-medium text-secondary">دار الضيافة</label>
                                    <select name="guest_house_id" class="form-select form-select-lg fs-6" id="relGuestHouse" disabled>
                                        <option value="">اختر دار الضيافة...</option>
                                        @foreach($guestHouses as $gh)
                                            <option value="{{ $gh->id }}" @selected($task->guest_house_id==$gh->id)>{{ $gh->name }} {{ $gh->location ? ' - ' . $gh->location : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">تقييم الأداء</h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-medium text-secondary d-block">التقييم (من 5)</label>
                                        <div class="btn-group" role="group" aria-label="Rating">
                                            @for($i=1; $i<=5; $i++)
                                                <input type="radio" class="btn-check" name="rating" id="rating{{$i}}" value="{{$i}}" {{ $task->rating == $i ? 'checked' : '' }} autocomplete="off">
                                                <label class="btn btn-outline-warning" for="rating{{$i}}">{{$i}} <i class="bi bi-star-fill"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-medium text-secondary">ملاحظات التقييم</label>
                                        <textarea name="evaluation_notes" class="form-control" rows="2" placeholder="أضف ملاحظات حول أداء المتطوع في هذه المهمة...">{{ $task->evaluation_notes }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center mt-5">
                            <a href="{{ route('volunteer-tasks.index') }}" class="btn btn-light btn-lg px-4 me-2 rounded-pill text-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">حفظ التعديلات</button>
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
            
            // Adjust ID construction if simple camelCase didn't match
            if(k=='project') inp=document.getElementById('relProject');
            if(k=='campaign') inp=document.getElementById('relCampaign');
            if(k=='guest_house') inp=document.getElementById('relGuestHouse');

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


