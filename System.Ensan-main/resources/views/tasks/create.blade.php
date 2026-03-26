@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-list-task text-primary"></i>
      إضافة مهمة جديدة
    </h4>
    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">العنوان</label><input type="text" name="title"
              class="form-control" required
              value="{{ request('subject_beneficiary_id') ? ('متابعة مستفيد #' . request('subject_beneficiary_id')) : '' }}">
          </div>
          <div class="col-md-6"><label class="form-label">اسم التطوع</label><input type="text"
              name="volunteer_activity_name" class="form-control" placeholder="مثال: توزيع مساعدات، متابعة مستفيد"></div>
          <div class="col-md-6"><label class="form-label">الحالة</label><select name="status" class="form-select">
              <option value="pending">قيد الانتظار</option>
              <option value="in_progress">قيد التنفيذ</option>
              <option value="done">منجزة</option>
            </select></div>
          <div class="col-12"><label class="form-label">الوصف</label><textarea name="description" class="form-control"
              rows="3">{{ request('subject_beneficiary_id') ? ('مهمة مرتبطة بالمستفيد رقم ' . request('subject_beneficiary_id')) : '' }}</textarea>
          </div>
          <div class="col-md-6"><label class="form-label">المكلّف</label><select name="assigned_to" class="form-select">
              <option value="">—</option>@foreach($users as $u)<option value="{{ $u->id }}"
              @selected((string) request('assigned_to') === (string) $u->id)>{{ $u->name }}</option>@endforeach
            </select></div>
          <div class="col-md-6"><label class="form-label">المكلِّف</label><select name="assigned_by" class="form-select">
              <option value="">—</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>
              @endforeach
            </select></div>
          <div class="col-md-6"><label class="form-label">تاريخ الاستحقاق</label><input type="date" name="due_date"
              class="form-control"></div>

          <div class="col-md-6"><label class="form-label">ينتمي إلى</label>
            <select id="relType" class="form-select">
              <option value="">—</option>
              <option value="project">مشروع</option>
              <option value="campaign">حملة</option>
              <option value="guest_house">دار الضيافة</option>
            </select>
          </div>
          <div class="col-md-6 rel rel-project" style="display:none"><label class="form-label">المشروع</label><select
              name="project_id" class="form-select" id="relProject" disabled>
              <option value="">—</option>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select></div>
          <div class="col-md-6 rel rel-campaign" style="display:none"><label class="form-label">الحملة</label><select
              name="campaign_id" class="form-select" id="relCampaign" disabled>
              <option value="">—</option>@foreach($campaigns as $c)<option value="{{ $c->id }}">{{ $c->name }}
              {{ $c->season_year ? '(' . $c->season_year . ')' : '' }}</option>@endforeach
            </select></div>
          <div class="col-md-6 rel rel-guest_house" style="display:none"><label class="form-label">دار
              الضيافة</label><select name="guest_house_id" class="form-select" id="relGuestHouse" disabled>
              <option value="">—</option>@foreach($guestHouses as $gh)<option value="{{ $gh->id }}">
              {{ $gh->name }}{{ $gh->location ? (' - ' . $gh->location) : '' }}</option>@endforeach
            </select></div>
        </div>
        <div class="mt-3"><button class="btn btn-primary">حفظ</button><a href="{{ route('tasks.index') }}"
            class="btn btn-light">إلغاء</a></div>
      </form>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var sel = document.getElementById('relType');
        function toggle() { var v = sel ? sel.value : '';['project', 'campaign', 'guest_house'].forEach(function (k) { var box = document.querySelector('.rel.rel-' + k); if (!box) return; var input = box.querySelector('select'); box.style.display = (v === k) ? 'block' : 'none'; if (input) { input.disabled = (v !== k); if (v !== k) { input.value = ''; } } }); }
        if (sel) { sel.addEventListener('change', toggle); toggle(); }
      });
    </script>
@endsection