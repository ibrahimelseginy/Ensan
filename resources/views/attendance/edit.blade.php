@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                        <h4 class="fw-bold text-body mb-0">تعديل سجل حضور</h4>
                        <p class="text-muted small mt-1">تحديث بيانات الحضور والانصراف للمتطوع.</p>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('volunteer-attendance.update', $rec) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-medium text-secondary">المتطوع</label>
                                    <input type="text" class="form-control form-control-lg fs-6 bg-body-secondary"
                                        value="{{ $rec->user?->name }}" disabled>
                                </div>

                                <div class="col-12">
                                    <hr class="text-muted opacity-25">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-secondary">التاريخ <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control form-control-lg fs-6"
                                        value="{{ $rec->date->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-secondary">وقت الدخول</label>
                                    <input type="text" name="check_in_at"
                                        class="form-control form-control-lg fs-6 time-picker"
                                        value="{{ $rec->check_in_at }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-secondary">وقت الخروج</label>
                                    <input type="text" name="check_out_at"
                                        class="form-control form-control-lg fs-6 time-picker"
                                        value="{{ $rec->check_out_at }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-medium text-secondary">ملاحظات</label>
                                    <textarea name="notes" class="form-control form-control-lg fs-6"
                                        rows="2">{{ $rec->notes }}</textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-body border-bottom pb-2 mb-3">تقييم الأداء</h5>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium text-secondary d-block">التقييم (من
                                                5)</label>
                                            <div class="btn-group" role="group" aria-label="Rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" class="btn-check" name="rating" id="rating{{$i}}"
                                                        value="{{$i}}" {{ $rec->rating == $i ? 'checked' : '' }}
                                                        autocomplete="off">
                                                    <label class="btn btn-outline-warning" for="rating{{$i}}">{{$i}} <i
                                                            class="bi bi-star-fill"></i></label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-medium text-secondary">ملاحظات التقييم</label>
                                            <textarea name="evaluation_notes" class="form-control" rows="2"
                                                placeholder="أضف ملاحظات حول أداء المتطوع في هذا اليوم...">{{ $rec->evaluation_notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end align-items-center mt-5">
                                <a href="{{ route('volunteer-attendance.index') }}"
                                    class="btn btn-secondary-subtle btn-lg px-4 me-2 rounded-pill text-secondary">إلغاء</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">حفظ
                                    التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

