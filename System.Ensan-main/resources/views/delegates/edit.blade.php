@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-person-badge text-primary"></i>
            تعديل المندوب
        </h4>
        <a href="{{ route('delegates.show', $delegate) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('delegates.update', $delegate) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الموظف (اختياري)</label>
                            <select name="user_id" class="form-select" id="employeeSelect">
                                <option value="">-- اختر موظف --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-phone="{{ $emp->phone }}" data-name="{{ $emp->name }}"
                                        @selected($delegate->user_id == $emp->id)>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">عند اختيار موظف، سيتم تعبئة البيانات تلقائيًا</div>
                        </div>
                        <div class="col-md-6"></div> <!-- Spacer -->

                        <div class="col-md-6"><label class="form-label">الاسم</label><input name="name" id="nameInput"
                                class="form-control" value="{{ $delegate->name }}"></div>
                        <div class="col-md-6"><label class="form-label">الهاتف</label><input name="phone" id="phoneInput"
                                class="form-control" value="{{ $delegate->phone }}"></div>
                        <script>
                            document.getElementById('employeeSelect').addEventListener('change', function () {
                                var opt = this.options[this.selectedIndex];
                                var name = opt.getAttribute('data-name');
                                var phone = opt.getAttribute('data-phone');
                                if (name) document.getElementById('nameInput').value = name;
                                if (phone) document.getElementById('phoneInput').value = phone;
                            });
                        </script>
                        <div class="col-md-6"><label class="form-label">خط السير</label><select name="route_id"
                                class="form-select">
                                <option value="">—</option>@foreach($routes as $r)<option value="{{ $r->id }}"
                                @selected($delegate->route_id == $r->id)>{{ $r->name }}</option>@endforeach
                            </select></div>
                        <div class="col-md-6">
                            <label class="form-label">الصورة الشخصية</label>
                            <input type="file" name="profile_photo" class="form-control" accept="image/*">
                            @if($delegate->profile_photo_path)
                                <div class="mt-2">
                                    <img src="{{ $delegate->image_url }}" alt="Current Photo"
                                        class="img-thumbnail" style="height: 100px;">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('delegates.show', $delegate) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> إلغاء
                        </a>
                        <button class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> حفظ التغييرات
                        </button>
                    </div>
            </form>
        </div>
    </div>
    </div>
@endsection


