@extends('layouts.app')

@section('title', 'إفطارات رمضان')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-cup-hot text-success me-2"></i>إفطارات رمضان
        </h3>
        <p class="text-muted small mb-0 mt-1">إدارة المستفيدين والوجبات وتوزيع الإفطارات</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('ramadan-iftars.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-lg me-1"></i>تسجيل حالة إفطار
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('ramadan-iftars.index') }}" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control" placeholder="بحث بالاسم، الهوية، الدليل، الهاتف..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">بحث</button>
            </div>
        </form>
    </div>
</div>

@if(isset($statistics) && count($statistics) > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center" style="border-color: #e2e8f0;">
                <thead style="background-color: #3b7625; color: white;">
                    <tr>
                        <th class="py-3 fw-bold border-0">المشروع</th>
                        <th class="py-3 fw-bold border-0">المنطقة</th>
                        <th class="py-3 fw-bold border-0">عدد الأسر</th>
                        <th class="py-3 fw-bold border-0">عدد الوجبات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statistics as $stat)
                        <tr>
                            <td class="fw-bold">{{ $stat['project'] }}</td>
                            <td class="fw-bold">{{ $stat['region'] }}</td>
                            <td class="fw-bold fs-5">{{ $stat['families_count'] }}</td>
                            <td class="fw-bold fs-5 text-success">{{ $stat['items_count'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-light fw-bold">
                        <td class="fs-5" colspan="2">الإجمالي المجُمع</td>
                        <td class="fs-5">{{ collect($statistics)->sum('families_count') }}</td>
                        <td class="fs-5 text-success">{{ collect($statistics)->sum('items_count') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">اسم المستفيد</th>
                        <th>عدد الوجبات</th>
                        <th>اسم الدليل</th>
                        <th>هاتف الدليل</th>
                        <th>الحملة</th>
                        <th class="text-end pe-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($iftars as $iftar)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $iftar->beneficiary_name }}</td>
                            <td><span class="badge bg-primary rounded-pill">{{ $iftar->meals_count }}</span></td>
                            <td>{{ $iftar->guide_name ?? '—' }}</td>
                            <td>{{ $iftar->guide_phone ?? '—' }}</td>
                            <td class="small">
                                @if($iftar->campaign)
                                    <span class="badge bg-success bg-opacity-10 text-success">{{ $iftar->campaign->name }}</span>
                                @endif
                                @if($iftar->project)
                                    <span class="badge bg-success bg-opacity-10 text-success">{{ $iftar->project->name }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('ramadan-iftars.edit', $iftar) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('ramadan-iftars.destroy', $iftar) }}" method="POST" class="d-inline-block" onsubmit="return confirm('تأكيد الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">لا توجد سجلات.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($iftars->hasPages())
        <div class="card-footer bg-white pt-3 pb-1 border-top-0">
            {{ $iftars->links() }}
        </div>
    @endif
</div>
@endsection


