@extends('layouts.app')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        /* Fix Select2 Multiple Tags Appearance */
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: 45px !important;
            height: auto !important;
            padding: 4px 8px !important;
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100%;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: var(--primary) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 2px 10px !important;
            margin: 0 !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            display: flex !important;
            align-items: center !important;
            line-height: 1.5 !important;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255, 255, 255, 0.8) !important;
            margin-right: 6px !important;
            order: 2 !important;
            border: none !important;
            background: none !important;
            padding: 0 !important;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #fff !important;
        }
        .select2-search__field {
            color: var(--dark) !important;
            background-color: var(--gray-100) !important;
            border-color: var(--gray-200) !important;
        }
        .select2-container--bootstrap-5 .select2-selection {
            background-color: var(--bg-card) !important;
            color: var(--dark) !important;
            border-color: var(--gray-200) !important;
            border-radius: 0.5rem !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: var(--dark) !important;
        }
        /* Fix Select2 Text Visibility & Dark Mode Support */
        .select2-results__option {
            color: #333 !important;
            font-weight: 500;
            background-color: #fff !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            background-color: #fff !important;
            border-color: var(--gray-200) !important;
            z-index: 1060 !important;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            margin-bottom: 2px !important;
        }
        .select2-container--bootstrap-5 .select2-search.select2-search--inline {
            flex: 1;
        }
        .select2-container--bootstrap-5 .select2-search.select2-search--inline .select2-search__field {
            margin: 2px 0 !important;
            min-width: 50px !important;
            height: 30px !important;
        }
    </style>
@endsection

@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-heart text-primary"></i>
      إضافة تبرع جديد
    </h4>
    <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('donations.store') }}" id="donationForm">
        @csrf
        <div class="row g-3">

          {{-- Donor Selection --}}
          <div class="col-md-12 mb-3">
              <label class="form-label d-flex justify-content-between align-items-center mb-3">
                  <span><i class="bi bi-person-circle text-primary me-1"></i> بيانات المتبرع</span>
              </label>
              
              <div class="p-3 bg-light rounded-3 border mb-3">
                  <div class="row align-items-center">
                      <div class="col-md-4">
                          <h6 class="mb-md-0"><i class="bi bi-person-circle me-1"></i> مصدر التبرع:</h6>
                      </div>
                      <div class="col-md-8">
                          <div class="btn-group w-100" role="group">
                              <input type="radio" class="btn-check" name="donor_type_toggle" id="donorTypeRegistered" value="registered" checked>
                              <label class="btn btn-outline-primary" for="donorTypeRegistered"><i class="bi bi-search me-1"></i> متبرع مسجل</label>

                              <input type="radio" class="btn-check" name="donor_type_toggle" id="donorTypeNew" value="new">
                              <label class="btn btn-outline-success" for="donorTypeNew"><i class="bi bi-person-plus me-1"></i> متبرع جديد</label>
                          </div>
                      </div>
                  </div>
              </div>

              {{-- Registered Donor Section --}}
              <div id="registeredDonorSection">
                  <select id="donorSelect" class="form-select @error('donor_id') is-invalid @enderror" name="donor_id">
                      <option value="" disabled {{ !old('donor_id', request('donor_id')) ? 'selected' : '' }}>-- ابحث عن متبرع مسجل --</option>
                      @foreach($donors as $donor)
                        <option value="{{ $donor->id }}" {{ old('donor_id', request('donor_id')) == $donor->id ? 'selected' : '' }}>
                          {{ $donor->name }} ({{ $donor->phone }})
                        </option>
                      @endforeach
                  </select>
                  @error('donor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              {{-- New Donor Section --}}
              <div id="newDonorSection" class="d-none">
                  <div class="row g-3 p-3 bg-light rounded-3 border">
                      <div class="col-md-6">
                          <label class="form-label small fw-bold">اسم المتبرع الجديد <span class="text-danger">*</span></label>
                          <input type="text" name="new_donor_name" id="newDonorName" class="form-control @error('new_donor_name') is-invalid @enderror" value="{{ old('new_donor_name') }}" placeholder="الاسم ثلاثي...">
                          @error('new_donor_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                      </div>
                      <div class="col-md-6">
                          <label class="form-label small fw-bold">رقم الهاتف <span class="text-danger">*</span></label>
                          <input type="text" name="new_donor_phone" id="newDonorPhone" class="form-control @error('new_donor_phone') is-invalid @enderror" value="{{ old('new_donor_phone') }}" placeholder="01xxxxxxxxx">
                          @error('new_donor_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                      </div>
                      <div class="col-md-6">
                          <label class="form-label small fw-bold">التصنيف</label>
                          <select name="new_donor_classification" class="form-select">
                              <option value="one_time">مرة واحدة</option>
                              <option value="recurring">متكرر</option>
                          </select>
                      </div>
                      <div class="col-md-6">
                          <label class="form-label small fw-bold">دورة التكرار (للمتكرر فقط)</label>
                          <select name="new_donor_cycle" class="form-select">
                              <option value="">— اختر —</option>
                              <option value="monthly">شهري</option>
                              <option value="yearly">سنوي</option>
                          </select>
                      </div>
                  </div>
              </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">النوع</label>
            <select name="type" class="form-select" required id="donationType">
              <option value="cash">نقدي</option>
              <option value="in_kind">عيني</option>
            </select>
          </div>
          <div class="col-md-4 cash-only">
            <label class="form-label">المبلغ</label>
            <div class="input-group">
              <input name="amount" type="number" step="any" min="0" class="form-control" placeholder="0.00">
              <select name="currency" class="form-select" style="max-width:140px">
                <option value="EGP" selected>EGP</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="SAR">SAR</option>
                <option value="GBP">GBP</option>
              </select>
            </div>
          </div>
          <div class="col-md-6 cash-only">
            <label class="form-label">طريقة الدفع</label>
            <select name="cash_channel" id="cashChannelSel" class="form-select">
              <option value="cash">نقدي</option>
              <option value="instapay">انستا باي</option>
              <option value="vodafone_cash">فودافون كاش</option>
              <option value="delegate">مندوب</option>
            </select>
          </div>
          <div class="col-md-6 cash-only">
            <label class="form-label">رقم الإيصال <span class="text-danger">*</span></label>
            <input name="receipt_number" class="form-control @error('receipt_number') is-invalid @enderror"
              value="{{ old('receipt_number') }}" placeholder="مثال: RC-2025-000123" required>
            @error('receipt_number')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Treasury Selection for Cash Donations --}}
          <div class="col-md-6 cash-only">
            <label class="form-label">
              <i class="bi bi-safe text-primary me-1"></i>
              الخزينة
              <span class="badge bg-success ms-1">جديد</span>
            </label>
            <select name="treasury_id" id="treasurySelect" class="form-select">
              <option value="">-- اختر الخزينة --</option>
              @if(isset($treasuries) && $treasuries->count() > 0)
                @foreach($treasuries as $treasury)
                  <option value="{{ $treasury->id }}" {{ old('treasury_id') == $treasury->id ? 'selected' : '' }}>
                    {{ $treasury->name }} 
                    @if($treasury->type_name ?? false)
                      ({{ $treasury->type_name }})
                    @endif
                    - {{ number_format($treasury->current_balance, 2) }} {{ $treasury->currency }}
                  </option>
                @endforeach
              @else
                <option value="" disabled>لا توجد خزائن - يرجى إنشاء خزينة أولاً</option>
              @endif
            </select>
            <div class="form-text text-muted">سيتم إضافة مبلغ التبرع إلى رصيد هذه الخزينة تلقائياً.</div>
          </div>

          <div class="col-md-6 in-kind-only" style="display:none">
            <label class="form-label">القيمة التقديرية</label>
            <div class="input-group">
              <input name="estimated_value" type="number" step="0.01" min="0" class="form-control">
              <span class="input-group-text">EGP</span>
            </div>
          </div>

          <div class="col-md-6 in-kind-only" style="display:none">
            <label class="form-label">الكمية</label>
            <input name="quantity" type="number" step="0.01" min="0" class="form-control">
          </div>
          <div class="col-md-6 in-kind-only" style="display:none">
            <label class="form-label">اسم الصنف</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-box"></i></span>
                <input type="text" name="item_name" class="form-control" placeholder="اسم الصنف المتبرع به">
                {{-- If you have a Select2 for items, you can use:
                <select name="item_id" class="form-select">...</select> 
                --}}
            </div>
          </div>
          
          <div class="col-md-6 in-kind-only" style="display:none">
              <label class="form-label">المخزن</label>
              <select name="warehouse_id" class="form-select">
                  <option value="">-- اختر المخزن --</option>
                  @foreach(\App\Models\Warehouse::all() as $w)
                      <option value="{{ $w->id }}">{{ $w->name }}</option>
                  @endforeach
              </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">الحملة</label>
            <select name="campaign_id" id="campaignSelect" class="form-select select2-js" data-placeholder="اختر الحملة...">
              <option value=""></option>
              @foreach($campaigns as $campaign)
                <option value="{{ $campaign->id }}" data-project="{{ $campaign->project_id }}">
                    {{ $campaign->name }} ({{ $campaign->season_year }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6" id="campaignSubCategoryWrapper" style="display:none;">
            <label class="form-label" id="subCategoryLabel">تخصيص داخل الحملة</label>
            <select name="campaign_sub_category" id="campaignSubCategory" class="form-select">
                <option value="">عام للحملة</option>
                {{-- Options populated via JS --}}
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">المشروع</label>
            <select name="project_id" id="projectSelect" class="form-select select2-js" data-placeholder="اختر مشروع...">
              <option value=""></option>
              @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6" id="projectSubCategoryWrapper" style="display:none;">
            <label class="form-label" id="projectSubCategoryLabel">ملف التخصيص للمشروع</label>
            <select name="project_sub_category" id="projectSubCategory" class="form-select">
                <option value="">عام للمشروع</option>
                {{-- Options populated via JS --}}
            </select>
          </div>
          
          {{-- Sponsorship Fields --}}
          <div class="col-md-12 mt-3 pt-3 border-top">
              <h6 class="text-primary mb-3"><i class="bi bi-stars"></i> تخصيص التبرع (كفالة / صدقة جارية)</h6>
              <div class="row g-3">
                  <div class="col-md-6">
                      <label class="form-label">نوع التخصيص</label>
                      <select name="sponsorship_kind" id="sponsorshipKind" class="form-select">
                          <option value="">-- بدون تخصيص --</option>
                          <option value="kafalat_yateem">كفالة يتيم</option>
                          <option value="kafalat_osra">كفالة أسرة</option>
                          <option value="sadaqa_jariya">صدقة جارية</option>
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label">نوع التوجيه</label>
                      <select name="allocation_target_type" id="allocationTargetType" class="form-select">
                          <option value="single">مستفيد واحد</option>
                          <option value="multiple">أكثر من مستفيد / أسرة كاملة</option>
                      </select>
                  </div>
                  <div class="col-md-8" id="beneficiaryWrapper" style="display:none;">
                      <label class="form-label" id="beneficiaryLabel">المستفيد</label>
                      
                      {{-- Single Select container --}}
                      <div id="singleBeneficiaryContainer">
                          <select name="beneficiary_id" id="sponsorshipBeneficiary" class="form-select select2-js">
                              <option value="">-- اختر المستفيد --</option>
                              @foreach($beneficiaries as $ben)
                                <option value="{{ $ben->id }}">{{ $ben->full_name }}</option>
                              @endforeach
                          </select>
                      </div>

                      {{-- Multiple Select container --}}
                      <div id="multipleBeneficiaryContainer" style="display:none;">
                          <select name="beneficiary_ids[]" id="sponsorshipBeneficiaries" class="form-select select2-js" multiple="multiple">
                              @foreach($beneficiaries as $ben)
                                <option value="{{ $ben->id }}">{{ $ben->full_name }}</option>
                              @endforeach
                          </select>
                          <div class="form-text small">يمكنك اختيار أكثر من مستفيد أو أسرة.</div>
                      </div>
                  </div>
              </div>
          </div>

          <div class="col-12 mt-3">
              <label class="form-label">ملاحظات التخصيص</label>
              <textarea name="allocation_note" id="allocationNoteText" class="form-control" rows="2" placeholder="أي ملاحظات إضافية حول تخصيص التبرع..."></textarea>
          </div>

        </div>

        <div class="mt-4 pt-3 border-top text-end">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i> حفظ التبرع
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
{{-- Load jQuery and Select2 --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // 1. Select2 Initialization
        const initSelect2 = () => {
            if ($('#donorSelect').length) {
                $('#donorSelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'ابحث عن اسم المتبرع أو رقم الهاتف...',
                    allowClear: true,
                    width: '100%',
                    dir: 'rtl'
                });
            }
            if ($('.select2-js').length) {
                $('.select2-js').each(function() {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        placeholder: $(this).data('placeholder') || 'اختر المستفيد...',
                        allowClear: true,
                        width: '100%',
                        dir: 'rtl'
                    });
                });
            }
        };

        initSelect2();

        // 2. Donor Toggle Logic
        const donorTypeRegistered = $('#donorTypeRegistered');
        const donorTypeNew = $('#donorTypeNew');
        const registeredSection = $('#registeredDonorSection');
        const newSection = $('#newDonorSection');
        const donorSelect = $('#donorSelect');

        function toggleDonorInputs(isClick = false) {
            const isRegistered = $('#donorTypeRegistered').is(':checked');
            
            if (isRegistered) {
                // Show Registered, Hide New
                registeredSection.removeClass('d-none');
                newSection.addClass('d-none');
                
                // Form Validation
                donorSelect.prop('required', true);
                $('#newDonorName').prop('required', false);
                $('#newDonorPhone').prop('required', false);
                
                // Fix Select2 width if it was hidden
                initSelect2();
                
                // Open search if user clicked
                if (isClick) {
                    $('#donorSelect').select2('open');
                }
            } else {
                // Show New, Hide Registered
                registeredSection.addClass('d-none');
                newSection.removeClass('d-none');
                
                // Form Validation
                donorSelect.prop('required', false);
                $('#newDonorName').prop('required', true);
                $('#newDonorPhone').prop('required', true);
                
                // Clear selected donor to avoid confusion
                if (isClick) {
                   $('#donorSelect').val(null).trigger('change');
                }
            }
        }

        // Listen for changes
        $('input[name="donor_type_toggle"]').on('change', function() {
            toggleDonorInputs(true);
        });
        
        // Run once on load to ensure correct state
        toggleDonorInputs();
        
        // If we have old values for new donor or validation failed for new donor, switch to new donor tab
        @if(old('new_donor_name') || $errors->has('new_donor_name') || $errors->has('new_donor_phone'))
            $('#donorTypeNew').prop('checked', true).trigger('change');
        @elseif(request('donor_id') || old('donor_id'))
            $('#donorTypeRegistered').prop('checked', true).trigger('change');
        @endif

        // 3. Donation Type Logic (Cash vs In-Kind)
        const typeSelect = $('#donationType');
        const cashElements = $('.cash-only');
        const inKindElements = $('.in-kind-only');
        
        function toggleType() {
            const isCash = (typeSelect.val() === 'cash');
            if (isCash) {
                cashElements.show();
                inKindElements.hide();
            } else {
                cashElements.hide();
                inKindElements.show();
            }
        }
        
        typeSelect.on('change', toggleType);
        toggleType(); // init

        // 4. Sponsorship Logic
        const skInfo = $('#sponsorshipKind');
        const benWrapper = $('#beneficiaryWrapper');
        const allocNote = $('#allocationNoteText');
        const benSelect = $('#sponsorshipBeneficiary');
        
        skInfo.on('change', updateSponsorship);
        
        $('#allocationTargetType').on('change', function() {
            const type = $(this).val();
            if (type === 'single') {
                $('#singleBeneficiaryContainer').show();
                $('#multipleBeneficiaryContainer').hide();
                $('#beneficiaryLabel').text('المستفيد');
            } else {
                $('#singleBeneficiaryContainer').hide();
                $('#multipleBeneficiaryContainer').show();
                $('#beneficiaryLabel').text('المستفيدين / الأسر');
                initSelect2(); // Re-init to handle multiple width correctly
            }
        });
        
        function updateSponsorship() {
            const val = skInfo.val();
            if (val && (val.startsWith('kafalat_') || val === 'sadaqa_jariya')) {
                benWrapper.show();
            } else {
                benWrapper.hide();
            }
        }
        
        // 5. Campaign & Project Relationship Logic
        const campaignSelect = $('#campaignSelect');
        const projectSelect = $('#projectSelect');
        const campSubCatWrapper = $('#campaignSubCategoryWrapper');
        const campSubCatSelect = $('#campaignSubCategory');
        const projSubCatWrapper = $('#projectSubCategoryWrapper');
        const projSubCatSelect = $('#projectSubCategory');

        campaignSelect.on('change', function() {
            const selectedOpt = $(this).find('option:selected');
            const projectId = selectedOpt.data('project');
            const campaignName = selectedOpt.text();
            const campaignId = $(this).val();

            // Auto-select related project if exists
            if (projectId) {
                projectSelect.val(projectId).trigger('change');
            }

            // Handle Sub-categories (Files)
            campSubCatSelect.empty().append('<option value="">عام للحملة</option>');
            
            if (campaignName.includes('رمضان')) {
                campSubCatWrapper.fadeIn();
                campSubCatSelect.append('<option value="شنط رمضان">شنط رمضان</option>');
                campSubCatSelect.append('<option value="إفطارات رمضان">إفطارات رمضان</option>');
            } else if (campaignId) {
                campSubCatWrapper.fadeIn();
                // Add default files for any campaign
                campSubCatSelect.append('<option value="سهم صدقة">سهم صدقة</option>');
                campSubCatSelect.append('<option value="عام">عام</option>');
            } else {
                campSubCatWrapper.fadeOut();
            }
        });

        projectSelect.on('change', function() {
            const projectName = $(this).find('option:selected').text();
            const projectId = $(this).val();
            const labelEl = $('#projectSubCategoryLabel');

            projSubCatSelect.empty().append('<option value="">عام للمشروع</option>');

            if (projectId) {
                projSubCatWrapper.fadeIn();
                
                if (projectName.includes('زاد')) {
                    labelEl.text('ملف التخصيص للمشروع');
                    projSubCatSelect.append('<option value="ملف أهالي زاد">ملف أهالي زاد</option>');
                } else if (projectName.includes('سكن') || projectName.includes('كرفان')) {
                    labelEl.text('ملف التخصيص للمشروع');
                    projSubCatSelect.append('<option value="ملف الإسكان والتعمير">ملف الإسكان والتعمير</option>');
                } else if (projectName.includes('سواء') || projectName.includes('عمليات') || projectName.includes('صحي')) {
                    labelEl.text('ملف التخصيص للمشروع');
                    projSubCatSelect.append('<option value="الملف الطبي">الملف الطبي</option>');
                } else if (projectName.includes('مدارس') || projectName.includes('تعليم') || projectName.includes('علم')) {
                    labelEl.text('ملف التخصيص للمشروع');
                    projSubCatSelect.append('<option value="ملف التعليم">ملف التعليم</option>');
                } else {
                    labelEl.text('ملف التخصيص للمشروع');
                    projSubCatSelect.append('<option value="ملف عام">ملف عام</option>');
                }
            } else {
                projSubCatWrapper.fadeOut();
            }
        });
    });
</script>
@endsection