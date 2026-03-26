@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">سجل الاستقبال</h1>
            <p class="text-muted small mb-0">إدارة الزوار والمكالمات الواردة للمؤسسة</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newLogModal">
            <i class="bi bi-plus-lg"></i> تسجيل جديد
        </button>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">إجمالي اليوم</div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $todayStats['total'] }}</div>
                    </div>
                </div>
                <div class="bg-primary py-1 opacity-25"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-4 me-3">
                        <i class="bi bi-person-walking fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">زيارات شخصية</div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $todayStats['personal'] }}</div>
                    </div>
                </div>
                <div class="bg-success py-1 opacity-25"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-3">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-4 me-3">
                        <i class="bi bi-telephone fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">مكالمات هاتفية</div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $todayStats['phone'] }}</div>
                    </div>
                </div>
                <div class="bg-info py-1 opacity-25"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4 me-3">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">تم الإنجاز</div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $todayStats['completed'] }}</div>
                    </div>
                </div>
                <div class="bg-warning py-1 opacity-25"></div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">سجل العمليات الأخير</h6>
        </div>
        <div class="card-body">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>الغرض</th>
                                <th>الحالة</th>
                                <th>محول إلى</th>
                                <th>التوقيت</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $log->visitor_name }}</div>
                                        <span class="text-muted small">{{ $log->phone }}</span>
                                    </td>
                                    <td>
                                        @if($log->visit_type == 'personal')
                                            <span class="badge bg-soft-primary text-primary"><i class="bi bi-person"></i> شخصي</span>
                                        @else
                                            <span class="badge bg-soft-info text-info"><i class="bi bi-telephone"></i> هاتف</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->purpose_label }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->status_color }}">{{ $log->status_label }}</span>
                                    </td>
                                    <td>{{ $log->directed_to ?? '-' }}</td>
                                    <td>{{ optional($log->created_at)->format('H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-light text-primary" title="تعديل"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-light text-success" title="إكمال"><i class="bi bi-check"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="No Data" style="max-width: 200px; opacity: 0.5">
                    <p class="mt-3 text-muted">لا توجد سجلات استقبال حتى الآن</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- New Log Modal -->
<div class="modal fade" id="newLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تسجيل عملية جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reception.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم الزائر / المتصل</label>
                        <input type="text" name="visitor_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">نوع العملية</label>
                            <select name="visit_type" class="form-select" required>
                                <option value="personal">زيارة شخصية</option>
                                <option value="phone">مكالمة هاتفية</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الغرض</label>
                            <select name="purpose" class="form-select" required>
                                <option value="help_request">طلب مساعدة</option>
                                <option value="complaint">شكوى</option>
                                <option value="donation">تبرع</option>
                                <option value="inquiry">استفسار عام</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تحويل إلى (اختياري)</label>
                        <input type="text" name="directed_to" class="form-control" placeholder="اسم الموظف أو القسم">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection