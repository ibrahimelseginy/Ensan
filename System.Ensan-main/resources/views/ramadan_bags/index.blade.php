@extends('layouts.app')

@section('title', 'شنط رمضان')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-bag-heart text-primary me-2"></i>شنط رمضان
        </h3>
        <p class="text-muted small mb-0 mt-1">إدارة المستفيدين وتوزيع الشنط الرمضانية</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('ramadan-bags.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i>تسجيل حالة شنطة
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('ramadan-bags.index') }}" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control" placeholder="بحث بالاسم، الرقم القومي، الهاتف..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات (الشنط)</option>
                    <option value="جديد" {{ request('status') == 'جديد' ? 'selected' : '' }}>جديد</option>
                    <option value="مقبول" {{ request('status') == 'مقبول' ? 'selected' : '' }}>مقبول</option>
                    <option value="مرفوض" {{ request('status') == 'مرفوض' ? 'selected' : '' }}>مرفوض</option>
                    <option value="تم التسليم" {{ request('status') == 'تم التسليم' ? 'selected' : '' }}>تم التسليم</option>
                </select>
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
                        <th class="py-3 fw-bold border-0">عدد الشنط</th>
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
                        <th class="ps-3">الاسم</th>
                        <th>الرقم القومي</th>
                        <th>الهاتف</th>
                        <th>المنطقة</th>
                        <th>إجمالي الشنط</th>
                        <th>الحملة/المشروع</th>
                        <th>الحالة</th>
                        <th class="text-end pe-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bags as $bag)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $bag->beneficiary_name }}</td>
                            <td>{{ $bag->national_id ?? '—' }}</td>
                            <td>{{ $bag->phone ?? '—' }}</td>
                            <td>{{ $bag->region ?? '—' }}</td>
                            <td>{{ $bag->bags_count ?? 1 }}</td>
                            <td class="small">
                                @if($bag->campaign)
                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $bag->campaign->name }}</span>
                                @endif
                                @if($bag->project)
                                    <span class="badge bg-success bg-opacity-10 text-success">{{ $bag->project->name }}</span>
                                @endif
                            </td>
                            <td>
                                @if($bag->status == 'تم التسليم')
                                    <span class="badge bg-success">تم التسليم</span>
                                @elseif($bag->status == 'مقبول')
                                    <span class="badge bg-info text-dark">مقبول</span>
                                @elseif($bag->status == 'مرفوض')
                                    <span class="badge bg-danger">مرفوض</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $bag->status }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('ramadan-bags.edit', $bag) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('ramadan-bags.destroy', $bag) }}" method="POST" class="d-inline-block" onsubmit="return confirm('تأكيد الحذف؟')">
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
    @if($bags->hasPages())
        <div class="card-footer bg-white pt-3 pb-1 border-top-0">
            {{ $bags->links() }}
        </div>
    @endif
</div>
@endsection


