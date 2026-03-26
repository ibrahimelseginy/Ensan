@extends('layouts.app')
@section('content')
<div class="card p-4">
    <h5 class="mb-3">تعديل قيد يومية #{{ $journalEntry->id }}</h5>
    <form method="POST" action="{{ route('journal-entries.update', $journalEntry) }}">
        @csrf
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">التاريخ</label>
                <input name="date" type="date" class="form-control" value="{{ optional($journalEntry->date)->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">نوع القيد</label>
                <select name="entry_type" class="form-select">
                    <option value="daily" @selected($journalEntry->entry_type == 'daily')>يومية</option>
                    <option value="opening" @selected($journalEntry->entry_type == 'opening')>افتتاحي</option>
                    <option value="closing" @selected($journalEntry->entry_type == 'closing')>إقفال</option>
                    <option value="adjustment" @selected($journalEntry->entry_type == 'adjustment')>تسوية</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الوصف</label>
                <input name="description" class="form-control" value="{{ $journalEntry->description }}">
            </div>
        </div>

        <h5>سطور القيد</h5>
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <table class="table table-bordered" id="linesTable">
            <thead>
                <tr>
                    <th width="40%">الحساب</th>
                    <th width="25%">مدين</th>
                    <th width="25%">دائن</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($journalEntry->lines as $index => $line)
                <tr>
                    <td>
                        <select name="lines[{{$index}}][account_id]" class="form-select" required>
                            <option value="">اختر حساب...</option>
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" @selected($line->account_id == $acc->id)>{{ $acc->code }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input name="lines[{{$index}}][debit]" type="number" step="0.01" class="form-control debit-input" value="{{ $line->debit }}"></td>
                    <td><input name="lines[{{$index}}][credit]" type="number" step="0.01" class="form-control credit-input" value="{{ $line->credit }}"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-line">X</button></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>الإجمالي</th>
                    <th id="totalDebit">0.00</th>
                    <th id="totalCredit">0.00</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <button type="button" class="btn btn-secondary btn-sm" id="addLine">إضافة سطر</button>

        <div class="mt-4">
            <button class="btn btn-primary">تحديث القيد</button>
            <a href="{{ route('journal-entries.show', $journalEntry) }}" class="btn btn-light">رجوع</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let lineIndex = {{ $journalEntry->lines->count() }};
    const table = document.querySelector('#linesTable tbody');
    const accounts = @json($accounts);

    function updateTotals() {
        let debits = 0;
        let credits = 0;
        document.querySelectorAll('.debit-input').forEach(i => debits += parseFloat(i.value) || 0);
        document.querySelectorAll('.credit-input').forEach(i => credits += parseFloat(i.value) || 0);
        document.getElementById('totalDebit').innerText = debits.toFixed(2);
        document.getElementById('totalCredit').innerText = credits.toFixed(2);
    }

    document.getElementById('addLine').addEventListener('click', function() {
        let options = '<option value="">اختر حساب...</option>';
        accounts.forEach(acc => {
            options += `<option value="${acc.id}">${acc.code} - ${acc.name}</option>`;
        });
        
        const row = `
            <tr>
                <td><select name="lines[${lineIndex}][account_id]" class="form-select" required>${options}</select></td>
                <td><input name="lines[${lineIndex}][debit]" type="number" step="0.01" class="form-control debit-input" value="0"></td>
                <td><input name="lines[${lineIndex}][credit]" type="number" step="0.01" class="form-control credit-input" value="0"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-line">X</button></td>
            </tr>
        `;
        table.insertAdjacentHTML('beforeend', row);
        lineIndex++;
    });

    table.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-line')) {
            if (table.querySelectorAll('tr').length > 2) {
                e.target.closest('tr').remove();
                updateTotals();
            } else {
                alert('يجب أن يحتوي القيد على سطرين على الأقل');
            }
        }
    });

    table.addEventListener('input', function(e) {
        if (e.target.classList.contains('debit-input') || e.target.classList.contains('credit-input')) {
            updateTotals();
        }
    });
    
    updateTotals();
});
</script>
@endsection
