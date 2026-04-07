@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-list-task text-primary"></i>
      تعديل المهمة
    </h4>
    <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('tasks.update', $task) }}">
        @csrf @method('PUT')
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">العنوان</label><input type="text" name="title"
              class="form-control" value="{{ $task->title }}"></div>
          <div class="col-md-6"><label class="form-label">اسم التطوع</label><input type="text"
              name="volunteer_activity_name" class="form-control" value="{{ $task->volunteer_activity_name }}"></div>
          <div class="col-md-6"><label class="form-label">الحالة</label><select name="status" class="form-select">
              <option value="pending" @selected($task->status === 'pending')>قيد الانتظار</option>
              <option value="in_progress" @selected($task->status === 'in_progress')>قيد التنفيذ</option>
              <option value="done" @selected($task->status === 'done')>منجزة</option>
            </select></div>
          <div class="col-12"><label class="form-label">الوصف</label><textarea name="description" class="form-control"
              rows="3">{{ $task->description }}</textarea></div>
          <div class="col-md-6"><label class="form-label">المكلّف</label><select name="assigned_to" class="form-select">
              <option value="">—</option>@foreach($users as $u)<option value="{{ $u->id }}"
              @selected($task->assigned_to == $u->id)>{{ $u->name }}</option>@endforeach
            </select></div>
          <div class="col-md-6"><label class="form-label">المكلِّف</label><select name="assigned_by" class="form-select">
              <option value="">—</option>@foreach($users as $u)<option value="{{ $u->id }}"
              @selected($task->assigned_by == $u->id)>{{ $u->name }}</option>@endforeach
            </select></div>
          <div class="col-md-6"><label class="form-label">تاريخ الاستحقاق</label><input type="date" name="due_date"
              class="form-control" value="{{ $task->due_date?->format('Y-m-d') }}"></div>

          <div class="col-md-6"><label class="form-label">ينتمي إلى</label>
            @php $rel = $task->project_id ? 'project' : ($task->campaign_id ? 'campaign' : ($task->guest_house_id ? 'guest_house' : '')); @endphp
            <select id="relType" class="form-select">
              <option value="" @selected($rel === '')>—</option>
              <option value="project" @selected($rel === 'project')>مشروع</option>
              <option value="campaign" @selected($rel === 'campaign')>حملة</option>
              <option value="guest_house" @selected($rel === 'guest_house')>دار الضيافة</option>
            </select>
          </div>
          <div class="col-md-6 rel rel-project" style="display:none"><label class="form-label">المشروع</label><select
              name="project_id" class="form-select" id="relProject" disabled>
              <option value="">—</option>@foreach($projects as $p)<option value="{{ $p->id }}"
              @selected($task->project_id == $p->id)>{{ $p->name }}</option>@endforeach
            </select></div>
          <div class="col-md-6 rel rel-campaign" style="display:none"><label class="form-label">الحملة</label><select
              name="campaign_id" class="form-select" id="relCampaign" disabled>
              <option value="">—</option>@foreach($campaigns as $c)<option value="{{ $c->id }}"
                @selected($task->campaign_id == $c->id)>{{ $c->name }}
              {{ $c->season_year ? '(' . $c->season_year . ')' : '' }}</option>@endforeach
            </select></div>
          <div class="col-md-6 rel rel-guest_house" style="display:none"><label class="form-label">دار
              الضيافة</label><select name="guest_house_id" class="form-select" id="relGuestHouse" disabled>
              <option value="">—</option>@foreach($guestHouses as $gh)<option value="{{ $gh->id }}"
                @selected($task->guest_house_id == $gh->id)>
              {{ $gh->name }}{{ $gh->location ? (' - ' . $gh->location) : '' }}</option>@endforeach
            </select></div>
        </div>
        <div class="mt-3"><button class="btn btn-primary">حفظ</button><a href="{{ route('tasks.index') }}"
            class="btn btn-light">رجوع</a></div>
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

