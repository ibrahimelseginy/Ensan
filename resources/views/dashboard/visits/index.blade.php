@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">الزيارات الميدانية</h1>
            <p class="text-muted small mb-0">جدولة ومتابعة زيارات البحث الاجتماعي</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newVisitModal">
            <i class="bi bi-calendar-plus"></i> جدولة زيارة
        </button>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-4">
                        <i class="bi bi-calendar-event fs-2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">زيارات قادمة</div>
                        <div class="h3 fw-bold mb-0 text-dark">{{ $stats['upcoming'] }}</div>
                    </div>
                </div>
                <div class="bg-primary py-1 opacity-25"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-4">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-4 me-4">
                        <i class="bi bi-check-all fs-2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">تمت هذا الشهر</div>
                        <div class="h3 fw-bold mb-0 text-dark">{{ $stats['completed_month'] }}</div>
                    </div>
                </div>
                <div class="bg-success py-1 opacity-25"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-4">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4 me-4">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">تحتاج مراجعة</div>
                        <div class="h3 fw-bold mb-0 text-dark">{{ $stats['needs_review'] }}</div>
                    </div>
                </div>
                <div class="bg-warning py-1 opacity-25"></div>
            </div>
        </div>
    </div>

    <!-- Visits List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جدول الزيارات</h6>
        </div>
        <div class="card-body">
            @if($visits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>المستفيد</th>
                                <th>الباحث</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>التوصية</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits as $visit)
                                <tr>
                                    <td>{{ $visit->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $visit->beneficiary->name }}</div>
                                    </td>
                                    <td>{{ $visit->researcher->name ?? 'غير محدد' }}</td>
                                    <td>
                                        <div>{{ $visit->visit_date->format('Y-m-d') }}</div>
                                        <small class="text-muted">{{ $visit->visit_date->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $visit->status_color }}">{{ $visit->status_label }}</span>
                                    </td>
                                    <td>{{ $visit->recommendation_label }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-light text-primary"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-light text-primary"><i class="bi bi-pencil"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $visits->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/location-search-2061730-1740050.png" alt="No Visits" style="max-width: 200px; opacity: 0.5">
                    <p class="mt-3 text-muted">لا يوجد زيارات مسجلة.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="newVisitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">جدولة زيارة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('visits.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">المستفيد</label>
                        <select name="beneficiary_id" class="form-select form-select-lg" required>
                            <option value="">-- اختر المستفيد --</option>
                            @foreach($beneficiaries as $ben)
                                <option value="{{ $ben->id }}">{{ $ben->name ?? $ben->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الباحث الميداني</label>
                        <select name="researcher_id" class="form-select form-select-lg" required>
                            <option value="">-- اختر الباحث --</option>
                            @foreach($researchers as $res)
                                <option value="{{ $res->id }}">{{ $res->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ الزيارة</label>
                        <input type="date" name="visit_date" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select" required>
                            <option value="scheduled">مجدولة</option>
                            <option value="completed">تمت الزيارة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات أولية</label>
                        <textarea name="report" class="form-control" rows="2"></textarea>
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

