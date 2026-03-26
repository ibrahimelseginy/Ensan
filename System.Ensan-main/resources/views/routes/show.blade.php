@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header mb-3">
    <h4 class="mb-0">
      <i class="bi bi-signpost-2 text-primary"></i>
      {{ $route->name }}
    </h4>
    <div class="btn-group">
      @if(auth()->check())
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
          <a class="btn btn-outline-primary" href="{{ route('travel-routes.edit', $route) }}">
            <i class="bi bi-pencil me-1"></i> تعديل
          </a>
          <form method="POST" action="{{ route('travel-routes.destroy', $route) }}" onsubmit="return confirm('إلغاء هذا الطريق نهائياً؟');" style="display:inline;">
             @csrf @method('DELETE')
             <button class="btn btn-outline-danger ms-1">
               <i class="bi bi-trash me-1"></i> حذف
             </button>
          </form>
        @else
          <a class="btn btn-outline-warning" href="{{ route('travel-routes.edit', $route) }}">
            <i class="bi bi-pencil-square me-1"></i> طلب تعديل
          </a>
          <form method="POST" action="{{ route('travel-routes.destroy', $route) }}" onsubmit="return confirm('طلب إلغاء هذا الطريق؟');" style="display:inline;">
             @csrf @method('DELETE')
             <button class="btn btn-outline-warning ms-1">
               <i class="bi bi-x-circle me-1"></i> طلب إلغاء
             </button>
          </form>
        @endif
      @endif
      <a href="{{ route('travel-routes.index') }}" class="btn btn-outline-secondary ms-2">
        <i class="bi bi-arrow-right me-1"></i> رجوع
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      @php
        $citiesList = is_array($route->cities ?? null) ? $route->cities : ((isset($cities) && is_array($cities)) ? $cities : []);
      @endphp
      {{-- الوصف غير مطلوب في العرض --}}
      <div class="mt-3">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="mb-0">مدن داخل المحافظة</h6>
          @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
            <button class="btn btn-primary btn-sm" type="button"
              data-bs-toggle="collapse" data-bs-target="#addCityForm">إضافة مدينة وتسعيرة</button>
          @endif
        </div>
        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
        <div class="collapse mt-3" id="addCityForm">
          <form method="POST" action="{{ route('travel-routes.addCity', $route) }}" class="card card-body p-3">
            @csrf
            <div class="row g-2">
              <div class="col-md-6"><label class="form-label">المدينة</label><input name="name" class="form-control"
                  required></div>
              <div class="col-md-6"><label class="form-label">التسعيرة</label>
                <div class="input-group">
                  <input name="fare" class="form-control" type="number" step="0.01" required>
                  <select name="fare_currency" class="form-select" style="max-width:140px" required>
                    <option value="EGP">EGP</option>
                    <option value="USD">USD</option>
                    <option value="SAR">SAR</option>
                    <option value="EUR">EUR</option>
                    <option value="AED">AED</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="mt-2"><button class="btn btn-success btn-sm">إضافة</button></div>
          </form>
        </div>
        @endif
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>المدينة</th>
                <th>التسعيرة</th>
                <th>العملة</th>
              </tr>
            </thead>
            <tbody>
              @forelse($citiesList as $c)
                <tr>
                  <td>{{ $c['name'] ?? '—' }}</td>
                  <td>{{ $c['fare'] ?? '—' }}</td>
                  <td>{{ $c['currency'] ?? 'EGP' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-muted">لا توجد مدن مُسجلة</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection