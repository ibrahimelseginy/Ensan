@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-cart-plus text-primary"></i>
      إضافة مصروف جديد
    </h4>
    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>



  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('expenses.store') }}">
        @csrf

        {{-- Basic Info Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-info-circle"></i>
            <span>المعلومات الأساسية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label form-label-required">النوع</label>
              <select name="type" class="form-select" required>
                <option value="operational">تشغيلي</option>
                <option value="aid">مساعدات</option>
                <option value="logistics">لوجستي</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">البند الفرعي (التصنيف)</label>
              <input list="categoryOptions" name="category" class="form-control" placeholder="اختر أو اكتب...">
              <datalist id="categoryOptions">
                <option value="المرتبات">
                <option value="الايجارات">
                <option value="فواتير الموبايل والإنترنت">
                <option value="فواتير الكهرباء والمياه">
                <option value="أدوات مكتبية وورق">
                <option value="نثريات">
                <option value="صيانة">
                <option value="وقود ومواصلات">
              </datalist>
            </div>
          </div>
        </div>

        {{-- Financial Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-cash-stack"></i>
            <span>البيانات المالية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label form-label-required">المبلغ</label>
              <div class="input-group">
                <input name="amount" type="number" step="0.01" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label form-label-required">
                <i class="bi bi-safe text-primary me-1"></i>
                الخزينة (مصدر الصرف)
              </label>
              <select name="treasury_id" class="form-select" required>
                <option value="">-- اختر الخزينة --</option>
                @if(isset($treasuries) && $treasuries->count() > 0)
                  @foreach($treasuries as $treasury)
                    <option value="{{ $treasury->id }}" {{ old('treasury_id') == $treasury->id ? 'selected' : '' }}>
                      {{ $treasury->name }}
                      @if($treasury->type_name)
                        ({{ $treasury->type_name }})
                      @endif
                       - الرصيد: {{ number_format($treasury->current_balance, 2) }} {{ $treasury->currency }}
                    </option>
                  @endforeach
                @else
                  <option value="" disabled>لا توجد خزائن متاحة</option>
                @endif
              </select>
              <small class="text-muted">سيتم خصم المبلغ من رصيد الخزينة المختارة</small>
            </div>
          </div>
        </div>

        {{-- Allocation Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-diagram-3"></i>
            <span>التخصيص</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">المستفيد</label>
              <select name="beneficiary_id" class="form-select">
                <option value="">— اختر المستفيد —</option>
                @foreach($beneficiaries as $b)
                  <option value="{{ $b->id }}">{{ $b->full_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">المشروع</label>
              <select name="project_id" class="form-select">
                <option value="">— اختر المشروع —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">الحملة</label>
              <select name="campaign_id" class="form-select">
                <option value="">— اختر الحملة —</option>
                @foreach($campaigns as $c)
                  <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">دار الضيافة</label>
              <select name="guest_house_id" class="form-select">
                <option value="">— اختر دار الضيافة —</option>
                @foreach($guestHouses as $g)
                  <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">مساحة العمل (Workspace)</label>
              <select name="workspace_id" class="form-select">
                <option value="">— اختر مساحة العمل —</option>
                @foreach($workspaces as $w)
                  <option value="{{ $w->id }}">{{ $w->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Notes Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-pencil-square"></i>
            <span>ملاحظات</span>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">وصف المصروف</label>
              <textarea name="description" class="form-control" rows="3"
                placeholder="أضف تفاصيل المصروف هنا..."></textarea>
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end">
          <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> حفظ المصروف
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        /* Fix Select2 Text Visibility & Dark Mode Support */
        .select2-results__option {
            color: var(--dark) !important;
            font-weight: 500;
            background-color: var(--bg-card) !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            background-color: var(--bg-card) !important;
            border-color: var(--gray-200) !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--highlighted {
            color: #fff !important;
            background-color: var(--primary) !important;
        }
        .select2-search__field {
            color: var(--dark) !important;
            background-color: var(--gray-100) !important;
            border-color: var(--gray-200) !important;
        }
        /* Enhance Select2 containers */
        .select2-container--bootstrap-5 .select2-selection {
            background-color: var(--bg-card) !important;
            color: var(--dark) !important;
            border-color: var(--gray-200) !important;
            padding-top: 0.35rem; 
            padding-bottom: 0.35rem;
            min-height: 45px;
            display: flex;
            align-items: center;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: var(--dark) !important;
        }
          /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 on all form-select elements
        $('select.form-select').select2({
            theme: 'bootstrap-5',
            dir: 'rtl',
            width: '100%',
            placeholder: 'اختر...',
            allowClear: true,
            language: {
                noResults: function () { return "لا توجد نتائج"; },
                searching: function () { return "جاري البحث..."; }
            }
        });
    });
</script>
@endsection

