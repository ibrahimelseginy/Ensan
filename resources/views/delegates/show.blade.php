@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row">
        <!-- Sidebar / Delegate Info -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body p-4">
                    <div class="mb-4">
                        @if($delegate->profile_photo_path)
                            <img src="{{ $delegate->image_url }}" class="rounded-circle img-thumbnail shadow-sm" style="width: 150px; height: 150px; object-fit: cover;" alt="{{ $delegate->name }}">
                        @else
                            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold display-4" style="width: 120px; height: 120px;">
                                {{ strtoupper(substr($delegate->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1">{{ $delegate->name }}</h3>
                    <p class="text-muted mb-3">{{ $delegate->phone ?? '—' }}</p>
                    
                    <div class="mb-4">
                        <span class="badge bg-secondary-subtle text-body border fs-6 px-3 py-2 rounded-pill">
                            <i class="bi bi-geo-alt me-1"></i> خط السير: {{ $delegate->route?->name ?? 'غير محدد' }}
                        </span>
                    </div>

                    <div class="d-grid gap-2">
                        @if(auth()->check())
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                                <a href="{{ route('delegates.edit', $delegate) }}" class="btn btn-primary"><i class="bi bi-pencil-square me-2"></i> تعديل البيانات</a>
                                
                                <form action="{{ route('delegates.destroy', $delegate) }}" method="POST"
                                      onsubmit="return confirm('حذف المندوب؟');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash me-2"></i> حذف المندوب
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('delegates.edit', $delegate) }}" class="btn btn-warning"><i class="bi bi-pencil-square me-2"></i> طلب تعديل بيانات</a>
                                
                                <form action="{{ route('delegates.destroy', $delegate) }}" method="POST"
                                      onsubmit="return confirm('هل أنت متأكد من طلب إلغاء المندوب؟');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-warning w-100">
                                        <i class="bi bi-x-circle me-2"></i> طلب إلغاء
                                    </button>
                                </form>
                            @endif
                        @endif
                        <a href="{{ route('delegates.index') }}" class="btn btn-outline-secondary">رجوع للقائمة</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Stats Cards (Modern) -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-3">
                            <div class="text-muted small mb-1">عدد المشاوير</div>
                            <div class="h3 fw-bold mb-0 text-primary">{{ $stats['count'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-3">
                            <div class="text-muted small mb-1">إجمالي التكلفة</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($stats['total_cost'], 0) }}</div>
                            <small class="text-muted">ج.م</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-3">
                            <div class="text-muted small mb-1">مستحق للدفع</div>
                            <div class="h3 fw-bold mb-0 text-warning">{{ number_format($stats['pending_cost'], 0) }}</div>
                            <small class="text-muted">ج.م</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-3">
                            <div class="text-muted small mb-1">مدفوع</div>
                            <div class="h3 fw-bold mb-0 text-success">{{ number_format($stats['paid_cost'], 0) }}</div>
                            <small class="text-muted">ج.م</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trips Management -->
            <div class="row">
                <!-- Add Trip Form -->
                <div class="col-md-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#addTripCollapse" role="button" aria-expanded="false">
                            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-plus-circle me-2"></i> إضافة مشوار جديد</h5>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="collapse" id="addTripCollapse">
                            <div class="card-body p-4">
                                <form action="{{ route('delegates.storeTrip', $delegate) }}" method="POST" class="row g-3">
                                    @csrf
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">التاريخ</label>
                                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">التكلفة</label>
                                        <div class="input-group">
                                            <input type="number" name="cost" step="0.01" class="form-control" required>
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">الحالة</label>
                                        <select name="status" class="form-select">
                                            <option value="pending">مستحق (Pending)</option>
                                            <option value="paid">مدفوع (Paid)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">الوصف</label>
                                        <input type="text" name="description" class="form-control" placeholder="مثال: توصيل طلبية...">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary px-4">إضافة المشوار</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trips Log -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent py-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i> سجل المشاوير</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="border-0 px-4">التاريخ</th>
                                        <th class="border-0">الوصف</th>
                                        <th class="border-0">التكلفة</th>
                                        <th class="border-0">الحالة</th>
                                        <th class="border-0 text-end px-4">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trips as $trip)
                                    <tr>
                                        <td class="px-4">{{ $trip->date->format('Y-m-d') }}</td>
                                        <td>{{ $trip->description ?? '—' }}</td>
                                        <td class="fw-bold">{{ number_format($trip->cost, 2) }} <span class="small text-muted">ج.م</span></td>
                                        <td>
                                            @if($trip->status === 'paid')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">مدفوع</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">مستحق</span>
                                            @endif
                                        </td>
                                        <td class="text-end px-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="{{ route('delegates.updateTripStatus', [$delegate, $trip]) }}" method="POST"
                                                    onsubmit="return confirm('{{ auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')) ? 'هل أنت متأكد من تغيير الحالة؟' : 'هل أنت متأكد من طلب تغيير حالة المشوار؟' }}')">
                                                    @csrf @method('PATCH')
                                                    @if($trip->status === 'pending')
                                                        <input type="hidden" name="status" value="paid">
                                                        <button type="submit" class="btn btn-sm btn-light text-success" 
                                                            data-bs-toggle="tooltip" title="{{ auth()->check() && auth()->user()->hasRole('admin') ? 'تحديد كمدفوع' : 'طلب تحديد كمدفوع' }}">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    @else
                                                        <input type="hidden" name="status" value="pending">
                                                        <button type="submit" class="btn btn-sm btn-light text-warning" 
                                                            data-bs-toggle="tooltip" title="{{ auth()->check() && auth()->user()->hasRole('admin') ? 'تحديد كمستحق' : 'طلب تحديد كمستحق' }}">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                                <form action="{{ route('delegates.destroyTrip', [$delegate, $trip]) }}" method="POST" 
                                                      onsubmit="return confirm('{{ auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')) ? 'هل أنت متأكد من حذف المشوار؟' : 'هل أنت متأكد من طلب حذف المشوار؟' }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light {{ (auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))) ? 'text-danger' : 'text-warning' }}" 
                                                        data-bs-toggle="tooltip" title="{{ (auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))) ? 'حذف' : 'طلب حذف' }}">
                                                        <i class="{{ (auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))) ? 'bi bi-trash' : 'bi bi-x-circle' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-5 text-muted">لا توجد مشاوير مسجلة لهذا المندوب</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($trips->hasPages())
                        <div class="card-footer bg-transparent border-top-0 py-3">
                            {{ $trips->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



