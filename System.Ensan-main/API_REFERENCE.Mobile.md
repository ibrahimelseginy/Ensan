# 📱 Ensan Mobile API — توثيق شامل لوحدة الموبايل

**الإصدار:** `v1`
**Base URL (Local):** `http://192.168.1.112:8000/api/v1/mobile`
**Routes File:** `routes/mobile_api.php`
**Controller:** `App\Http\Controllers\Api\MobileApiController`
**تنسيق الردود:** `application/json`
**المصادقة:** جميع الـ Endpoints عامة (لا تتطلب تسجيل دخول)

---

## 📋 فهرس الـ Endpoints

| # | الوظيفة | Method | Endpoint | الحالة |
|---|---------|--------|----------|--------|
| 0 | API Discovery (اكتشاف الـ API) | `GET` | `/` | ✅ عام |
| 1 | محتوى الصفحة الرئيسية | `GET` | `/home` | ✅ عام |
| 2 | المشاريع المفعّلة للموبايل | `GET` | `/projects` | ✅ عام |
| 3 | الحملات المفعّلة للموبايل | `GET` | `/campaigns` | ✅ عام |
| 4 | أخبار التطبيق — استعراض | `GET` | `/news` | ✅ عام |
| 4 | أخبار التطبيق — إضافة | `POST` | `/news` | ✅ عام |
| 5 | صندوق طلبات التطوع | `GET` | `/volunteer-requests` | ✅ عام |
| 5 | إرسال طلب تطوع جديد | `POST` | `/volunteer` | ✅ عام |
| 6 | التقديم لحالة مستحقة | `POST` | `/case-application` | ✅ عام |
| 7 | حجز دار الضيافة | `POST` | `/guest-house` | ✅ عام |
| 8 | إشعارات التطبيق | `GET` | `/notifications` | ✅ عام |
| 9 | بيانات التواصل | `GET` | `/contact-info` | ✅ عام |
| 10| معلومات عنا (About Us) | `GET` | `/about-us` | ✅ عام |

---

## 🔍 0. API Discovery — قائمة الروابط

> `GET /api/v1/mobile`

يُعيد قائمة بجميع الـ endpoints المتاحة في الـ API.

**Response `200`:**
```json
{
  "status": "ok",
  "name": "Ensan Mobile API",
  "version": "v1",
  "endpoints": {
    "home": "http://192.168.1.112:8000/api/v1/mobile/home",
    "projects": "http://192.168.1.112:8000/api/v1/mobile/projects",
    "campaigns": "http://192.168.1.112:8000/api/v1/mobile/campaigns",
    "news_list": "http://192.168.1.112:8000/api/v1/mobile/news",
    "news_create": { "method": "POST", "url": "http://192.168.1.112:8000/api/v1/mobile/news" },
    "volunteer_requests": "http://192.168.1.112:8000/api/v1/mobile/volunteer-requests",
    "volunteer_submit": { "method": "POST", "url": "http://192.168.1.112:8000/api/v1/mobile/volunteer" },
    "case_application": { "method": "POST", "url": "http://192.168.1.112:8000/api/v1/mobile/case-application" },
    "guest_house": { "method": "POST", "url": "http://192.168.1.112:8000/api/v1/mobile/guest-house" },
    "notifications": "http://192.168.1.112:8000/api/v1/mobile/notifications",
    "contact_info": "http://192.168.1.112:8000/api/v1/mobile/contact-info",
    "about_us": "http://192.168.1.112:8000/api/v1/mobile/about-us"
  }
}
```

---

## 🏠 1. محتوى الصفحة الرئيسية

> `GET /api/v1/mobile/home`

يسترجع جميع أقسام الصفحة الرئيسية للتطبيق دفعةً واحدة.

**جدول البيانات:** `mobile_home_items`

**الأقسام المُعادة:**

| المفتاح | النوع | الوصف |
|---------|-------|-------|
| `heroes` | `array` | الصور الترويجية الرئيسية في أعلى الصفحة |
| `gallery` | `array` | معرض الصور |
| `services` | `array` | قسم "خدماتنا" (الخدمات مع السعر) |
| `share_what_you_dont_need` | `array` | قسم "شارك بما لا تحتاجه" |
| `seasonal_campaigns` | `array` | الحملات الموسمية (رمضان، شتاء، إلخ) |
| `final_section` | `object\|null` | القسم الأخير في الصفحة |
| `about_us` | `object|null` | معلومات عن المؤسسة (تحتوي على image_url) |

**Response `200`:**
```json
{
  "status": "success",
  "data": {
    "heroes": [
      {
        "id": 1,
        "type": "hero",
        "title": "عنوان البانر الرئيسي",
        "description": null,
        "sort_order": 0,
        "image_url": "http://127.0.0.1:8000/storage/mobile/home/hero.jpg"
      }
    ],
    "gallery": [
      {
        "id": 2,
        "type": "gallery",
        "image_url": "http://127.0.0.1:8000/storage/mobile/home/gallery1.jpg",
        "sort_order": 1
      }
    ],
    "services": [
      {
        "id": 3,
        "type": "service",
        "title": "مشروع زاد",
        "description": "كفالة الأسر المحتاجة",
        "icon": "bi-heart",
        "share_price": 500.00,
        "image_url": "http://127.0.0.1:8000/storage/mobile/home/service.jpg"
      }
    ],
    "share_what_you_dont_need": [
      {
        "id": 4,
        "type": "share",
        "title": "أثاث",
        "description": "تبرع بالأثاث الذي لا تحتاجه",
        "sort_order": 1,
        "image_url": "http://127.0.0.1:8000/storage/mobile/home/share.jpg"
      }
    ],
    "seasonal_campaigns": [
      {
        "id": 5,
        "type": "campaign",
        "title": "حملة رمضان 2026",
        "details": "وصف الحملة الموسمية",
        "image_url": "http://127.0.0.1:8000/storage/mobile/home/ramadan.jpg"
      }
    ],
    "final_section": {
      "id": 6,
      "type": "final",
      "title": "انضم إلى عائلة إنسان",
      "description": "كن جزءاً من رحلة العطاء"
    },
    "about_us": {
      "image_url": "http://127.0.0.1:8000/storage/mobile/home/about.jpg"
    }
  }
}
```

> **ملاحظة:** القسم الغير موجود في قاعدة البيانات يُعاد كـ `null` أو مصفوفة فارغة `[]`.

---

## 📂 2. المشاريع المفعّلة للموبايل

> `GET /api/v1/mobile/projects`

يسترجع قائمة المشاريع التي تم تفعيل `show_on_mobile = true` لها من لوحة التحكم.

**جدول البيانات:** `projects` (مفلتر بـ `show_on_mobile = 1`)

**Response `200`:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "مشروع زاد الأيتام",
      "mobile_content": "محتوى مخصص للموبايل يشرح المشروع بإيجاز",
      "image_path": "website/projects/image.webp",
      "goal_amount": "50000.00",
      "current_amount": "12500.00",
      "image_url": "http://127.0.0.1:8000/storage/website/projects/image.webp"
    },
    {
      "id": 2,
      "name": "مشروع بعثاء الأمل",
      "mobile_content": null,
      "image_path": null,
      "goal_amount": "0.00",
      "current_amount": "0.00",
      "image_url": null
    }
  ]
}
```

**الأعمدة المُعادة فقط:**
`id`, `name`, `mobile_content`, `image_path`, `goal_amount`, `current_amount`, `image_url` (مُحسوبة)

> **كيفية التفعيل:** من لوحة التحكم ← تطبيق الموبايل ← المحتوى (مشاريع/حملات) ← تفعيل "عرض على الموبايل"

---

## 📣 3. الحملات المفعّلة للموبايل

> `GET /api/v1/mobile/campaigns`

يسترجع قائمة الحملات التي تم تفعيل `show_on_mobile = true` لها من لوحة التحكم.

**جدول البيانات:** `campaigns` (مفلتر بـ `show_on_mobile = 1`)

**Response `200`:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 10,
      "name": "حملة رمضان 2026",
      "mobile_content": "خلال شهر رمضان نوفر وجبات إفطار يومية",
      "image_path": "campaigns/ramadan.jpg",
      "goal_amount": "100000.00",
      "current_amount": "45000.00",
      "end_date": "2026-04-15",
      "image_url": "http://127.0.0.1:8000/storage/campaigns/ramadan.jpg"
    }
  ]
}
```

**الأعمدة المُعادة فقط:**
`id`, `name`, `mobile_content`, `image_path`, `goal_amount`, `current_amount`, `end_date`, `image_url` (مُحسوبة)

---

## 📰 4. أخبار التطبيق

### 4a. استعراض الأخبار
> `GET /api/v1/mobile/news`

يسترجع قائمة الأخبار مرتبةً من الأحدث إلى الأقدم.

**جدول البيانات:** `web_news`

**Response `200`:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "توزيع مساعدات على 200 أسرة",
      "content": "قامت مؤسسة إنسان بتوزيع...",
      "category": "أخبار",
      "image_url": "http://127.0.0.1:8000/storage/news/abc.jpg",
      "created_at": "2026-03-04T18:00:00.000000Z"
    }
  ]
}
```

---

### 4b. إضافة خبر جديد
> `POST /api/v1/mobile/news`
> **Content-Type:** `multipart/form-data`

| الحقل | النوع | مطلوب | القيود | الوصف |
|-------|-------|:------:|--------|-------|
| `title` | `string` | ✅ | max: 255 | عنوان الخبر |
| `content` | `string` | ✅ | — | محتوى الخبر (HTML مسموح) |
| `image` | `file` | ❌ | image, max: 5MB | صورة الخبر |
| `category` | `string` | ❌ | max: 255 | تصنيف الخبر |

**Response `201`:**
```json
{
  "status": "success",
  "message": "News created successfully",
  "data": {
    "id": 15,
    "title": "خبر جديد",
    "content": "...",
    "image_path": "news/randomname.jpg",
    "created_at": "2026-03-05T20:00:00.000000Z"
  }
}
```

---

## 🤝 5. طلبات التطوع

### 5a. استعراض طلبات التطوع (Mobile Unit)
> `GET /api/v1/mobile/volunteer-requests`

يسترجع قائمة جميع طلبات التطوع المُقدَّمة عبر تطبيق الموبايل حصرياً، مرتبةً من الأحدث.

**جدول البيانات:** `mobile_volunteer_requests`

**Response `200`:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "محمد أحمد علي",
      "phone": "01012345678",
      "email": "m@example.com",
      "interests": "برامج الأيتام، التوزيع الميداني",
      "message": "أريد المساهمة في أعمال الخير",
      "national_id": "2990101...",
      "id_card_path": "mobile/volunteers/ids/xyz.jpg",
      "status": "new",
      "created_at": "2026-03-11T10:00:00.000000Z"
    }
  ]
}
```

---

### 5b. إرسال طلب تطوع جديد
> `POST /api/v1/mobile/volunteer`
> **Content-Type:** `application/json` أو `multipart/form-data`

| الحقل | النوع | مطلوب | القيود | الوصف |
|-------|-------|:------:|--------|-------|
| `name` | `string` | ✅ | max: 255 | الاسم الكامل |
| `phone` | `string` | ✅ | max: 20 | رقم الهاتف |
| `email` | `email` | ❌ | — | البريد الإلكتروني |
| `national_id` | `string` | ❌ | — | الرقم القومي |
| `gender` | `string` | ❌ | — | النوع (ذكر/أنثى) |
| `education_level` | `string` | ❌ | — | المرحلة التعليمية |
| `faculty` | `string` | ❌ | — | الكلية |
| `university` | `string` | ❌ | — | الجامعة |
| `current_job` | `string` | ❌ | — | الوظيفة الحالية |
| `address` | `string` | ❌ | — | محل الميلاد |
| `current_address` | `string` | ❌ | — | العنوان الحالي |
| `volunteer_hours` | `string` | ❌ | — | عدد الساعات المتاحة للتطوع |
| `interests` | `string` | ❌ | — | مجالات الاهتمام (تُحفظ كـ `area_of_interest`) |
| `skills` | `string` | ❌ | — | المهارات واللغات |
| `previous_experience`| `string` | ❌ | (yes/no) | هل لديك خبرة سابقة؟ |
| `goal` | `string` | ❌ | — | الهدف من التطوع |
| `expectations` | `string` | ❌ | — | التوقعات من التطوع |
| `message` | `string` | ❌ | — | رسالة إضافية |
| `cv` | `file` | ❌ | — | ملف السيرة الذاتية (PDF/Image) |
| `id_card` | `file` | ❌ | — | صورة بطاقة الهوية (Image) |

**Response `201`:**
```json
{
  "status": "success",
  "message": "Volunteer request submitted successfully",
  "data": {
    "id": 42,
    "name": "فاطمة حسن",
    "phone": "01198765432",
    "created_at": "2026-03-05T20:00:00.000000Z"
  }
}
```

> **ملاحظة:** محاولة فتح `/volunteer` عبر GET تُعيد رسالة توضيحية بالحقول المطلوبة (Status 405).

---

## 🏥 6. التقديم لحالة مستحقة

> `POST /api/v1/mobile/case-application`
> **Content-Type:** `multipart/form-data`

يدعم التقديم لـ: **مشروع زاد الأيتام**، **مشروع بعثاء الأمل**، وحالات أخرى.

**جدول البيانات:** `mobile_case_applications`

| الحقل | النوع | مطلوب | القيود | الوصف |
|-------|-------|:------:|--------|-------|
| `applicant_name` | `string` | ✅ | max: 255 | اسم مقدم الطلب |
| `applicant_phone` | `string` | ✅ | max: 20 | رقم الهاتف |
| `case_type` | `string` | ✅ | انظر القائمة | نوع الحالة |
| `description` | `string` | ✅ | — | وصف تفصيلي للحالة |
| `governorate` | `string` | ❌ | max: 255 | المحافظة |
| `city` | `string` | ❌ | max: 255 | المدينة / المركز |
| `address` | `string` | ❌ | — | العنوان بالتفصيل |
| `id_image` | `file\|image` | ❌ | max: 5MB | صورة بطاقة الهوية |
| `medical_report` | `file` | ❌ | max: 10MB | التقرير الطبي / المستندات |

**القيم المقبولة لـ `case_type`:**

| القيمة | القسم في النظام |
|--------|----------------|
| `zad` | مشروع زاد الأيتام |
| `hope` | مشروع بعثاء الأمل |
| `medical` | حالة طبية |
| `financial` | حالة مالية |
| `education` | حالة تعليمية |

**Response `201`:**
```json
{
  "status": "success",
  "message": "Case application submitted successfully",
  "data": {
    "id": 8,
    "applicant_name": "أحمد محمود سعيد",
    "applicant_phone": "01098765432",
    "case_type": "zad",
    "status": "pending",
    "id_image_path": "mobile/cases/ids/img.jpg",
    "medical_report_path": null,
    "created_at": "2026-03-05T20:00:00.000000Z"
  }
}
```

---

## 🏨 7. دار الضيافة — حجز غرفة

> `POST /api/v1/mobile/guest-house`
> **Content-Type:** `multipart/form-data`

**جدول البيانات:** `web_room_bookings`

| الحقل | النوع | مطلوب | القيود | الوصف |
|-------|-------|:------:|--------|-------|
| `name` | `string` | ✅ | max: 255 | اسم المريض أو الطالب |
| `phone` | `string` | ✅ | max: 20 | رقم الهاتف |
| `national_id` | `string` | ✅ | — | رقم البطاقة الوطنية |
| `arrival_date` | `date` | ✅ | تنسيق: `YYYY-MM-DD` | تاريخ الوصول المتوقع |
| `expected_duration` | `string` | ✅ | انظر القائمة | مدة الإقامة المتوقعة |
| `medical_center` | `string` | ❌ | max: 255 | اسم المستشفى / المركز الطبي |
| `notes` | `string` | ❌ | — | ملاحظات إضافية |
| `patient_id_file` | `file` | ❌ | max: 5MB | ملف بطاقة المريض |

**القيم المقبولة لـ `expected_duration`:**

| القيمة | المعنى |
|--------|--------|
| `less_than_week` | أقل من أسبوع |
| `one_week` | أسبوع واحد |
| `two_weeks` | أسبوعان |
| `three_weeks` | ثلاثة أسابيع |
| `month` | شهر واحد |
| `more_than_month` | أكثر من شهر |

**Response `201`:**
```json
{
  "status": "success",
  "message": "Guest house booking submitted successfully",
  "data": {
    "id": 15,
    "name": "فاطمة حسن محمد",
    "phone": "01155667788",
    "national_id": "29901010123456",
    "arrival_date": "2026-04-01",
    "expected_duration": "one_week",
    "medical_center": "مستشفى سرطان الأطفال",
    "status": "pending",
    "created_at": "2026-03-05T20:00:00.000000Z"
  }
}
```

---

## 🔔 8. إشعارات التطبيق (Push Notifications)

> `GET /api/v1/mobile/notifications`

يسترجع قائمة الإشعارات المُرسلة فعلاً (`is_sent = true`)، مرتبةً من الأحدث.

**جدول البيانات:** `mobile_notifications`

**Response `200`:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "تبرعك وصل!",
      "body": "شكراً لتبرعك، تم توصيل مساعدتك لأسرة محتاجة في المنيا.",
      "target_audience": "donors",
      "is_sent": true,
      "sent_at": "2026-03-04T15:30:00.000000Z",
      "image_url": "http://127.0.0.1:8000/storage/mobile/notifications/thank.jpg",
      "created_at": "2026-03-04T15:00:00.000000Z"
    }
  ]
}
```

---

## 💰 9. طلب تبرع (Mobile Donation)

> `POST /api/v1/mobile/donation`
> **Content-Type:** `application/json`

يستخدم لإرسال تفاصيل التبرع مباشرة من التطبيق ليتم مراجعتها من قبل الإدارة.

**جدول البيانات:** `mobile_donations`

| الحقل | النوع | مطلوب | القيود | الوصف |
|-------|-------|:------:|--------|-------|
| `donor_name` | `string` | ✅ | max: 255 | اسم المتبرع |
| `donor_phone` | `string` | ✅ | max: 20 | رقم الهاتف |
| `donor_address` | `string` | ❌ | — | عنوان المتبرع |
| `donation_amount` | `numeric` | ✅ | min: 1 | مبلغ التبرع |
| `donation_for` | `string` | ✅ | — | التبرع موجه لـ (عام، مشروع معين، إلخ) |
| `payment_method` | `string` | ✅ | — | طريقة الدفع (Cash, Fawry, Card...) |
| `notes` | `string` | ❌ | — | ملاحظات إضافية |

**Response `201`:**
```json
{
  "status": "success",
  "message": "Donation submitted successfully",
  "data": {
    "id": 5,
    "donor_name": "إبراهيم الفيل",
    "donor_phone": "01012345678",
    "donation_amount": 1000,
    "donation_for": "مشروع زاد الأيتام",
    "status": "pending",
    "created_at": "2026-03-11T14:30:00.000000Z"
  }
}
```

**القيم المحتملة لـ `target_audience`:**

| القيمة | الوصف |
|--------|-------|
| `all` | جميع مستخدمي التطبيق |
| `donors` | المتبرعون فقط |
| `beneficiaries` | المستفيدون فقط |

> **ملاحظة:** الإشعارات تُضاف وتُدار حصرياً من لوحة التحكم ← تطبيق الموبايل ← الإشعارات.

---

## ❌ أخطاء الـ Validation (422)

عند إرسال بيانات ناقصة أو غير صحيحة:

```json
{
  "status": "error",
  "errors": {
    "applicant_name": ["حقل اسم مقدم الطلب مطلوب."],
    "case_type": ["الحقل المحدد لـ case_type غير صالح."],
    "phone": ["حقل رقم الهاتف مطلوب."]
  }
}
```

## ⚠️ خطأ Method Not Allowed (405)

عند فتح Endpoint يقبل POST فقط عن طريق GET مباشرةً من المتصفح:

```json
{
  "status": "error",
  "message": "This endpoint only accepts POST requests for submitting case applications (Zad, Hope, etc.).",
  "required_fields": ["applicant_name", "applicant_phone", "case_type", "description"],
  "allowed_case_types": ["zad", "hope", "medical", "financial", "education"]
}
```

---

## 🗄️ جداول قاعدة البيانات

| Model | الجدول | الوصف |
|-------|--------|-------|
| `MobileHomeItem` | `mobile_home_items` | عناصر الصفحة الرئيسية (hero, gallery, service, share, campaign, final, about_us) |
| `Project` | `projects` | المشاريع — مفلترة بـ `show_on_mobile = 1` |
| `Campaign` | `campaigns` | الحملات — مفلترة بـ `show_on_mobile = 1` |
| `WebNews` | `web_news` | أخبار التطبيق |
| `MobileVolunteerRequest` | `mobile_volunteer_requests` | طلبات التطوع (من الموبايل) |
| `WebVolunteerRequest` | `web_volunteer_requests` | طلبات التطوع (من الموقع) |
| `MobileCaseApplication` | `mobile_case_applications` | طلبات الحالات المستحقة |
| `WebRoomBooking` | `web_room_bookings` | حجوزات دار الضيافة |
| `MobileNotification` | `mobile_notifications` | إشعارات التطبيق |
| `MobileInKindDonation` | `mobile_in_kind_donations` | التبرعات العينية |
| `MobileBanner` | `mobile_banners` | البنرات الإعلانية |
| `MobileDonation` | `mobile_donations` | طلبات التبرع (Mobile Unit) |

---

## 📁 مسارات تخزين الملفات (Storage)

جميع الصور والملفات تُخزن في `storage/app/public/` وتُعرض من خلال `public/storage/`.

| النوع | المسار النسبي |
|-------|--------------|
| صور الصفحة الرئيسية للموبايل | `mobile/home/` |
| مستندات طلبات الحالات (هوية) | `mobile/cases/ids/` |
| مستندات طلبات الحالات (تقارير) | `mobile/cases/reports/` |
| ملفات حجوزات دار الضيافة | `mobile/bookings/` |
| صور الإشعارات | `mobile/notifications/` |
| صور الأخبار | `news/` |
| صور المشاريع | `website/projects/` |
| صور الحملات | `campaigns/` |

---

## ⚙️ إعدادات تقنية مهمة

### Cache
- في بيئة التطوير (`APP_ENV=local`): الـ Cache **معطّل** — البيانات تُقرأ مباشرة من DB في كل طلب
- في الإنتاج (`APP_ENV=production`): الـ Cache يعمل لمدة **ساعة (3600 ثانية)**

### Storage Link
```
public/storage  →  storage/app/public  (Junction/Symlink)
```

### متغيرات البيئة المهمة
```env
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_DATABASE=ensan_db
FILESYSTEM_DISK=public
SESSION_DRIVER=file
CACHE_STORE=file
```

---

## 🛠️ لوحة التحكم — إدارة محتوى الموبايل

| الصفحة | المسار | الوظيفة |
|--------|--------|---------|
| محتوى الصفحة الرئيسية | `/mobile/home-content` | إدارة Heroes, Gallery, Services, إلخ |
| المشاريع والحملات | `/mobile/dashboard` | التحكم في `show_on_mobile` |
| أخبار التطبيق | `/website/news` | إضافة وتعديل الأخبار |
| طلبات التطوع (ويب) | `/website/volunteer-requests` | طلبات الموقع الإلكتروني |
| طلبات التطوع (موبايل) | `/mobile/volunteer-requests` | طلبات تطبيق الموبايل (Unit منفصلة) |
| الإشعارات | `/mobile/notifications` | إرسال Push Notifications |
| تقديم حالة — زاد | `/mobile/cases?type=zad` | طلبات مشروع زاد |
| تقديم حالة — بعثاء | `/mobile/cases?type=hope` | طلبات مشروع بعثاء الأمل |
| سجلات التبرعات | `/mobile/donations` | إدارة تبرعات الموبايل |
| دار الضيافة | `/website/bookings` | حجوزات دار الضيافة |

---

---

---

## ℹ️ 1.5 معلومات عنا (About Us)

> `GET /api/v1/mobile/about-us`

يسترجع رابط الصورة الخاصة بقسم "معلومات عنا" حصرياً.

**Response `200`:**
```json
{
  "status": "success",
  "data": {
    "about_us": {
      "image_url": "http://127.0.0.1:8000/storage/mobile/home/about.jpg"
    }
  }
}
```

---

## 📞 10. بيانات التواصل (Contact Info)

> `GET /api/v1/mobile/contact-info`

يسترجع قائمة بجهات الاتصال المخصصة للموبايل (أسماء وأرقام هواتف).

**جدول البيانات:** `mobile_contact_infos`, `mobile_contact_phones`

**Response `200`:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "كفر الشيخ الرئيسي",
      "sort_order": 1,
      "phones": [
        {
          "id": 1,
          "contact_info_id": 1,
          "phone": "01006090616",
          "sort_order": 0
        }
      ]
    },
    {
      "id": 2,
      "name": "دار إنسان للأورام - كفر الشيخ",
      "sort_order": 2,
      "phones": [
        {
          "id": 2,
          "contact_info_id": 2,
          "phone": "01016649593",
          "sort_order": 0
        },
        {
          "id": 3,
          "contact_info_id": 2,
          "phone": "0473131335",
          "sort_order": 1
        }
      ]
    }
  ]
}
```

---

*آخر تحديث: 2026-03-12 | إنسان للخير — وحدة تطبيق الموبايل (إضافة بيانات التواصل)*
