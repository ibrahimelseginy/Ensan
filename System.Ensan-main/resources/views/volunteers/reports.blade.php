@extends('layouts.app')
@section('content')
<div class="card p-4">
  <div class="d-flex justify-content-between align-items-center">
    <h5 class="mb-0">تقارير المتطوعين</h5>
  </div>
  <form method="GET" class="card p-3 mt-3">
    <div class="row g-2 align-items-end">
      <div class="col-md-6">
        <label class="form-label">اختر المتطوع</label>
        <select name="user_id" class="form-select">
          <option value="">—</option>
          @foreach($volunteers as $v)
            <option value="{{ $v->id }}" @selected($userId==$v->id)>{{ $v->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100">عرض</button>
      </div>
    </div>
  </form>
  @if($selected)
  <div class="row g-3 mt-3">
    <div class="col-md-3"><div class="card p-3 text-center"><div class="small text-muted">المشاريع</div><div class="h4 mb-0">{{ $summary['projects'] }}</div><i class="bi bi-kanban text-primary"></i></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><div class="small text-muted">ساعات التطوع</div><div class="h4 mb-0">{{ number_format($summary['hours'],2) }}</div><i class="bi bi-clock-history text-success"></i></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><div class="small text-muted">مهام مكتملة</div><div class="h4 mb-0">{{ $summary['tasks_done'] }}</div><i class="bi bi-check2-circle text-info"></i></div></div>
  </div>
  <div class="mt-4">
    <h6>سجل التطوع</h6>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>المشروع</th>
            <th>الحملة</th>
            <th>البداية</th>
            <th>الدور في المشروع</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($assignments as $p)
          <tr>
            <td>{{ $p->name }}</td>
            <td>{{ optional($campaignMap->get($p->pivot->campaign_id))->name ?? '—' }}</td>
            <td>{{ $p->pivot->started_at ? \Illuminate\Support\Carbon::parse($p->pivot->started_at)->format('Y-m-d') : ($p->pivot->created_at?\Illuminate\Support\Carbon::parse($p->pivot->created_at)->format('Y-m-d'):'—') }}</td>
            <td>{{ $p->pivot->role ?? '—' }}</td>
            <td class="text-end"><a class="btn btn-outline-primary btn-sm" href="{{ route('projects.show',$p) }}">عرض المشروع</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</div>
@endsection

