@extends('layouts.app')
@section('content')
<div class="card p-4">
    <h5 class="mb-3">تعديل حساب</h5>
    <form method="POST" action="{{ route('accounts.update', $account) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">كود الحساب</label>
                <input name="code" class="form-control" value="{{ $account->code }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">اسم الحساب</label>
                <input name="name" class="form-control" value="{{ $account->name }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select" required>
                    <option value="asset" @selected($account->type=='asset')>أصول</option>
                    <option value="liability" @selected($account->type=='liability')>خصوم</option>
                    <option value="equity" @selected($account->type=='equity')>حقوق ملكية</option>
                    <option value="revenue" @selected($account->type=='revenue')>إيرادات</option>
                    <option value="expense" @selected($account->type=='expense')>مصروفات</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الحساب الرئيسي</label>
                <select name="parent_id" class="form-select">
                    <option value="">-- لا يوجد --</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" @selected($account->parent_id == $p->id)>{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control" rows="3">{{ $account->description }}</textarea>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary">حفظ</button>
            <a href="{{ route('accounts.index') }}" class="btn btn-light">رجوع</a>
        </div>
    </form>
</div>
@endsection
