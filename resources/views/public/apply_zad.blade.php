@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

<div class="container py-4 min-vh-100">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-md-11">

      <!-- Header Card -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); color: #ffffff;">
        <div class="p-4 p-md-5 text-center position-relative">
          <div class="badge bg-white text-dark rounded-pill px-3.5 py-1.5 fw-bold mb-3 shadow-xs">
            مؤسسة إنسان الخيرية | مشهرة برقم 63 لسنة 2017
          </div>
          <h1 class="fw-black mb-2" style="font-size: 2rem;">إستمارة تقديم زاد (الأيتام)</h1>
          <p class="mb-0 opacity-90 mx-auto" style="max-width: 650px; font-size: 1.05rem; line-height: 1.7;">
            <strong>رؤيتنا :</strong> تقديم عمل خيري تطوعي بإحترافية مع الحفاظ علي كرامة الإنسان
          </p>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 text-center">
          <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
          <h4 class="fw-bold mb-2">تم استلام الاستمارة بنجاح</h4>
          <p class="mb-0 text-muted fs-6">{{ session('success') }}</p>
          <div class="mt-3">
            <a href="{{ route('cases.apply-zad') }}" class="btn btn-outline-success rounded-pill px-4 fw-bold">تقديم استمارة جديدة</a>
          </div>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4">
          <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> يرجى استكمال الحقول الإلزامية المطلوبة:</h6>
          <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Main Form -->
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 bg-stats-card-main">
        <form method="POST" action="{{ route('cases.apply-zad.store') }}">
          @csrf

          <!-- Section 1: بيانات المصدر والتسجيل -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-bookmark-star-fill me-2"></i> 1. بيانات المصدر والتسجيل
            </h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold small">اسم المصدر</label>
                <input type="text" name="source_name" class="form-control" value="{{ old('source_name') }}" placeholder="اسم المصدر أو الجمعية أو فاعل الخير الدال على الحالة">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">رقم المسجل / رقم الدليل</label>
                <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="رقم القيد أو الدليل إن وجد">
              </div>
            </div>
          </div>

          <!-- Section 2: بيانات الأم ومقدم الطلب -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-person-heart me-2"></i> 2. بيانات الأم والزوج ومقدم الطلب
            </h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold small">اسم الأم <span class="text-danger">*</span></label>
                <input type="text" name="mother_name" class="form-control" required value="{{ old('mother_name') }}" placeholder="اسم الأم رباعياً كما في البطاقة">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">اسم الزوج المتوفي <span class="text-danger">*</span></label>
                <input type="text" name="deceased_husband_name" class="form-control" required value="{{ old('deceased_husband_name') }}" placeholder="اسم الزوج المتوفي رباعياً">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold small">مين مقدم للأم (صفته) <span class="text-danger">*</span></label>
                <input type="text" name="submitter_relation_to_mother" class="form-control" required value="{{ old('submitter_relation_to_mother') }}" placeholder="مثال: الأم نفسها / ابنها / أخوها / جارة / فاعل خير">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">رقم تليفون 1 <span class="text-danger">*</span></label>
                <input type="tel" name="phone_1" class="form-control" required value="{{ old('phone_1') }}" placeholder="01xxxxxxxxx">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">رقم تليفون 2</label>
                <input type="tel" name="phone_2" class="form-control" value="{{ old('phone_2') }}" placeholder="رقم إضافي للتواصل">
              </div>
            </div>
          </div>

          <!-- Section 3: العنوان وتفاصيل الوصول -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-geo-alt-fill me-2"></i> 3. العنوان وتفاصيل الوصول
            </h5>
            <div class="mb-3">
              <label class="form-label fw-bold small">العنوان ( البلد / موقف إيه / بيتركب ليها إيه ) <span class="text-danger">*</span></label>
              <textarea name="transit_details" class="form-control" rows="3" required placeholder="المحافظة، القرية أو المدينة، الموقف الموصل للمكان، تفاصيل المواصلات والشارع...">{{ old('transit_details') }}</textarea>
            </div>
          </div>

          <!-- Section 4: تفاصيل الوفاة والأولاد -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-people-fill me-2"></i> 4. تفاصيل الوفاة والأولاد
            </h5>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-bold small">متوفى من امتى / كان شغال إيه / سبب الوفاة <span class="text-danger">*</span></label>
                <textarea name="death_details" class="form-control" rows="2" required placeholder="تاريخ أو مدة الوفاة، مهنة الزوج قبل وفاته، سبب الوفاة...">{{ old('death_details') }}</textarea>
              </div>

              <div class="col-12">
                <label class="form-label fw-bold small">عدد الأولاد <span class="badge bg-danger ms-1">ممنوع التقديم لطفل واحد</span> <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-3 mt-1">
                  @foreach(['2', '3', '4', '5', 'غير ذلك'] as $cnt)
                    <div class="form-check form-check-inline p-2 border rounded-3 px-3 bg-light">
                      <input class="form-check-input" type="radio" name="children_count" id="count_{{ $loop->index }}" value="{{ $cnt }}" {{ old('children_count') === $cnt ? 'checked' : '' }} required>
                      <label class="form-check-label fw-bold cursor-pointer" for="count_{{ $loop->index }}">
                        {{ $cnt }} {{ $cnt !== 'غير ذلك' ? 'أولاد' : '' }}
                      </label>
                    </div>
                  @endforeach
                </div>
              </div>

              <div class="col-12">
                <label class="form-label fw-bold small">أسماء الأطفال وسنهم ومصاريف دروسهم ولو حد فيهم بيتعالج من تعب مزمن <span class="text-danger">*</span></label>
                <textarea name="children_details" class="form-control" rows="3" required placeholder="اذكر اسم كل طفل وعمره ومصروف مدرسته أو درسه وأي أمراض مزمنة أو علاج مستمر...">{{ old('children_details') }}</textarea>
              </div>
            </div>
          </div>

          <!-- Section 5: الدخل والمعاشات والمساعدات -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-cash-coin me-2"></i> 5. الدخل والمعاشات والمساعدات
            </h5>
            <div class="mb-3">
              <label class="form-label fw-bold small">بتقبض معاش تكافل / تأمين / مساعدات جمعيات / أهلها / أهل زوجها / الجيران / مساجد <span class="text-danger">*</span></label>
              <textarea name="income_aid_sources" class="form-control" rows="3" required placeholder="اذكر بالتفصيل أي مبالغ أو معاشات أو كفالات تستلمها الأسرة من أي جهة وقيمة كل منها...">{{ old('income_aid_sources') }}</textarea>
            </div>
          </div>

          <!-- Section 6: ملاحظات المقدم والالتزامات -->
          <div class="mb-4">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-file-earmark-text-fill me-2"></i> 6. ملاحظات المقدم والحالة الاجتماعية والمصاريف
            </h5>
            <div class="mb-3">
              <label class="form-label fw-bold small">
                ملاحظات المقدم ( الحالة الاجتماعية للأم ) — مصاريفها (كهرباء - غاز - مياه) لو البيت إيجار كام ؟ — الديون / العجز في إيه بالضبط؟ <span class="text-danger">*</span>
              </label>
              <textarea name="notes_expenses_debts" class="form-control" rows="4" required placeholder="اكتب وصفاً شاملاً لحالة الأم الاجتماعية، فواتير المرافق، قيمة الإيجار الشهري إن وجد، أي ديون متراكمة وأوجه العجز الأساسية...">{{ old('notes_expenses_debts') }}</textarea>
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm" style="font-size: 1.15rem;">
              <i class="bi bi-send-check-fill me-2"></i> إرسال استمارة تقديم زاد (الأيتام)
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
@endsection
