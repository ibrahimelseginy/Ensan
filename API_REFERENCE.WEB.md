# 📘 API Reference — موقع إنسان الإلكتروني

**Base URL (Local):** `http://127.0.0.1:8000/api/v1`  
**Base URL:** `http://127.0.0.1:8000/api/v1` 
**Auth Header (Admin):** `Authorization: Bearer {token}`

---

## 🌐 Public APIs (بدون تسجيل دخول)

### الصفحة الرئيسية
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/` | كل بيانات الصفحة الرئيسية (hero, stats, projects, campaigns, news, events, branches, ...) |
| GET | `/website/landing` | نفس الـ endpoint (alias) |
| GET | `/website/stats` | الإحصائيات فقط |
| GET | `/website/notification` | الإشعار العاجل (Breaking News) |
| GET | `/website/donation-page` | بيانات صفحة التبرعات (بانر، إحصائيات، مجالات) |

**Response `/website/` يحتوي:**
```json
{
  "hero": { "title_primary", "title_secondary", "description", "image" },
  "stats": { "donations", "projects", "branches", "beneficiaries", "volunteers" },
  "about": { "story_title", "story_content", "vision", "mission" },
  "features": [...],
  "sectors": [...],
  "partners": [...],
  "testimonials": [...],
  "branches": [...],
  "volunteer_wall": [...],
  "featured_projects": [...],
  "featured_campaigns": [...],
  "featured_news": [...],
  "featured_events": [...],
  "headquarters": { "title", "address", "phone", "working_hours", "features", "stats" },
  "guest_house_content": { "title", "content", "image", "link" },
  "bottom_cta": { "title", "text", "stat1_value", ... },
  "faqs": [...],
  "contact_channels": { "phone", "email", "whatsapp", "visit" },
  "ideal_partner": { "title", "items": [...] },
  "sponsorship_programs": { "section", "projects": { "hope", "zad" } },
  "notification": { "is_active", "label", "text", "link_text", "link_url" },
  "donation_page": {
    "banner": { "urgent_campaign", "hero_text", "description" },
    "stats": { "donors_count", "donors_label", "today_amount", "today_label" },
    "projects_section": { "title", "subtitle" },
    "fields_section": { "title", "fields": [] }
  },
  "raw_settings": { ... }
}
```

---

### المقر والفروع
| Method | Endpoint | الوصف |
|--------|----------|-------| قسم ضيافة إنسان (الصفحة الرئيسية)

| GET | `/website/branches` | بيانات المقر وقائمة الفروع |
| GET | `/website/coverage` | إحصائيات التغطية فقط |

**Response `/website/branches`:**
```json
{
  "headquarters": { "title", "address", "phone", "working_hours", "features", "labels" },
  "coverage_stats": [{ "value", "label" }],
  "branches": [{ "id", "name", "address", "phone", "status_text", ... }]
}
```

**Response `/website/coverage`:**
```json
[{ "value": "1", "label": "فرع رئيسي", "icon": "bi-geo-alt" }, { "value": "2", "label": "محافظات", "icon": "bi-map" }, ...]
```

---

### جدار الشرف
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/volunteer-wall` | قائمة المتطوعين في جدار الشرف |

**Response:**
```json
[{ "id", "name", "hours", "rank", "image", "description", "badge" }]
```

---

### مجلس الأمناء
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/board-members` | قائمة أعضاء مجلس الأمناء |

**Response:**
```json
[{ "id", "name", "role", "description", "image" }]
```

---

### محتوى المشاريع
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/projects` | قائمة المشاريع |
| GET | `/website/projects/{id}` | تفاصيل مشروع |

**Response:**
```json
[{ "id", "name", "description", "status", "image", "category", "location", "start_date", "end_date", "beneficiaries_count", "target_amount", "is_featured" }]
```

---

### محتوى الحملات
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/campaigns` | قائمة الحملات |
| GET | `/website/campaigns/{id}` | تفاصيل حملة |

**Response:**
```json
[{ "id", "name", "description", "status", "image", "goal_amount", "current_amount", "progress_percentage", "beneficiaries_count", "share_price", "goal_unit", "starts_in", "ui": { "collected_format", "benefited_format", "share_format", "goal_format" } }]
```

---

### دار الضيافة
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/guest-house` | بيانات صفحة دار الضيافة |
| POST | `/website/room-booking` | إرسال طلب حجز |
| POST | `/website/guest-house` | إرسال طلب حجز (alias) |

**POST Booking Fields:**
```
name*, phone*, email, national_id, companion_name, companion_phone,
arrival_date, expected_duration, medical_center, notes, guest_house_id,
patient_id(file), companion_id(file), medical_transfer(file),
followup_card(file), medical_report(file)
```

---

### الأخبار
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/news` | قائمة الأخبار |

**Response:**
```json
[{ "id", "title", "content", "image", "category", "contact_name", "contact_number", "views_count", "shares_count", "date" }]
```

---

### الفعاليات
| Method | Endpoint | Query Params | الوصف |
|--------|----------|-------------|-------|
| GET | `/website/events` | `?category=X&featured=1` | قائمة الفعاليات |
| GET | `/website/events/{id}` | - | تفاصيل فعالية + زيادة views |

**Response:**
```json
[{ "id", "title", "content", "image", "location", "category", "event_date", "event_end_date", "is_featured", "views_count", "shares_count", "published_at", "created_at" }]
```

---

### تواصل معنا
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/contact` | بيانات صفحة التواصل |
| POST | `/website/contact` | إرسال رسالة تواصل |

**POST Fields:** `name*, phone*, email*, subject, message*, image(file)`

**GET Response:**
```json
{
  "slider": [...],
  "section": { "title", "subtitle" },
  "social_links": { "facebook", "twitter", "instagram", "youtube", "whatsapp" },
  "channels": { "phone", "email", "whatsapp", "visit" },
  "info": { "hq_address", "hq_details", "hotline", "support_email", "working_days", "working_hours" }
}
```

---

### تطوع معنا
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/volunteer` | بيانات صفحة التطوع |
| POST | `/website/volunteer` | إرسال طلب تطوع |

**POST Fields:**
```
name*, phone*, email*, area_of_interest, message, cv(file),
address, birth_date, national_id, gender, education_level,
faculty, university, current_job, skills, goal, volunteer_hours
```

**GET Response:**
```json
{
  "hero": { "title", "subtitle", "description", "image" },
  "slider": [...],
  "stats": { "volunteers", "hours", "branches", "projects" },
  "success_partners_stats": { "donors", "volunteers", "institutions", "completed_campaigns" },
  "wall": [...]
}
```

---

### أخرى
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/website/features` | لماذا تختارنا |
| GET | `/website/sectors` | قطاعات العمل |
| GET | `/website/partners` | الشركاء |
| GET | `/website/testimonials` | الآراء |
| POST | `/website/testimonials` | إضافة رأي |
| GET | `/website/faqs` | الأسئلة الشائعة |
| GET | `/website/pages` | الصفحات الديناميكية |
| GET | `/website/pages/{slug}` | صفحة بالـ slug |

### النشرة الإخبارية
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST/GET | `/website/subscribe` | الاشتراك في النشرة الإخبارية (في الـ GET نمرر `?email=...`) |

**Example (POST):** `{"email": "user@example.com"}`

---

## 🔐 Authentication (الهاتف و OTP)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/auth/login-phone` | طلب إرسال رمز OTP (أرسل `phone`) |
| POST | `/auth/verify-otp` | التحقق من الرمز والحصول على Token (أرسل `phone`, `otp`) |
| GET | `/auth/profile` | بيانات الملف الشخصي للمستخدم |

---

## 💳 Donations & Payments (التبرعات)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/donations` | إرسال تبرع جديد (Polymorphic) |
| POST | `/donations/upload-proof` | رفع إيصال الدفع (أرسل `donation_id`, `proof`) |
| GET | `/my-donations` | عرض قائمة تبرعاتي الخاصة |

**Donation Fields:**
`type*` (campaign/project), `target_id*`, `amount*`, `payment_method*` (instapay/vodafone_cash/card)

---

## 🔐 Admin APIs (يحتاج: `Authorization: Bearer {token}`)

### إدارة النشرة الإخبارية (Admin)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/subscriptions` | عرض كل المشتركين |
| DELETE | `/admin/website/subscriptions/{id}` | حذف مشترك |

---

### إعدادات الموقع
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/settings` | جلب كل الإعدادات |
| POST | `/admin/website/settings` | تحديث الإعدادات + الصور |
| POST | `/admin/website/donation-page` | تحديث نصوص وإعدادات صفحة التبرعات |

**Donation Page Settings Fields (multipart/form-data):**
- `donation_page_campaign_title` (string, optional)
- `donation_page_campaign_link` (string, optional) - Direct link for the banner (بدلاً من صورة البنر).
- `hero_title`: العنوان الرئيسي للهيرو.
- `hero_description`: الوصف الرئيسي.
- `stats_donors_count`: عدد المتبرعين (نص).
- `stats_today_amount`: تبرعات اليوم (نص).
- `projects_title`: عنوان قسم المشاريع.
- `projects_subtitle`: عنوان فرعي للمشاريع.
- `categories`: مصفوفة من الفئات (مثال: `["الكل", "المشاريع"]`).

---

### إدارة التبرعات (Admin)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/donations` | عرض كل التبرعات (مع الفلترة) |
| POST | `/admin/donations/verify` | تأكيد وصول التبرع (أرسل `donation_id`) |
| POST | `/admin/donations/reject` | رفض التبرع/الإيصال (أرسل `donation_id`, `reason`) |

### حسابات متبرعي الويب (Donation Accounts)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/donation-accounts` | قائمة المتبرعين (اسم، هاتف، عدد التبرعات، إجمالي الموثق) |
| GET | `/admin/website/donation-accounts/{id}` | سجل تبرعات متبرع محدد بالتفصيل |
| PUT | `/admin/website/donation-accounts/{id}` | تحديث بيانات متبرع (اسم، هاتف) |
| POST | `/admin/website/donation-accounts/donations/{id}/verify` | توثيق تبرع محدد قادم من الويب |
| POST | `/admin/website/donation-accounts/donations/{id}/reject` | رفض تبرع محدد قادم من الويب |

**Image fields (multipart/form-data):**
`hero_image`, `featured_campaign_image`, `featured_campaign_icon1_image`,
`featured_campaign_icon2_image`, `gh_home_image`,
`gallery_image_1..10`, `news_slider_1..10`, `contact_slider_1..10`,
`volunteer_slider_1..10`, `gh_slider_1..10`

---

### مجلس الأمناء (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/board-members` |
| POST | `/admin/website/board-members` |
| POST | `/admin/website/board-members/{id}` |
| DELETE | `/admin/website/board-members/{id}` |

---

### الفروع (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/branches` |
| POST | `/admin/website/branches` |
| POST | `/admin/website/branches/{id}` |
| DELETE | `/admin/website/branches/{id}` |

---

### جدار الشرف (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/volunteer-wall` |
| POST | `/admin/website/volunteer-wall` |
| POST | `/admin/website/volunteer-wall/{id}` |
| DELETE | `/admin/website/volunteer-wall/{id}` |

---

### الأخبار (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/news` |
| POST | `/admin/website/news` |
| POST | `/admin/website/news/{id}` |
| DELETE | `/admin/website/news/{id}` |

---

### الفعاليات (Admin)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/events` | قائمة كل الفعاليات |
| POST | `/admin/website/events` | إضافة فعالية |
| POST | `/admin/website/events/{id}` | تعديل فعالية |
| DELETE | `/admin/website/events/{id}` | حذف فعالية |

**Fields:** `title*, content, location, category, event_date, event_end_date, is_featured, published_at, image(file), delete_image`

---

### المشاريع (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/projects` |
| POST | `/admin/website/projects/{id}` |

---

### الحملات (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/campaigns` |
| POST | `/admin/website/campaigns/{id}` |

---

### الرسائل والطلبات (Admin - Read Only)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/contact-messages` | رسائل التواصل |
| PATCH | `/admin/website/contact-messages/{id}/read` | تعليم كمقروء |
| DELETE | `/admin/website/contact-messages/{id}` | حذف |
| GET | `/admin/website/volunteer-requests` | طلبات التطوع |
| DELETE | `/admin/website/volunteer-requests/{id}` | حذف |
| GET | `/admin/website/room-bookings` | حجوزات دار الضيافة |
| PATCH | `/admin/website/room-bookings/{id}/status` | تحديث الحالة |
| DELETE | `/admin/website/room-bookings/{id}` | حذف |

---

### الأسئلة الشائعة (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/faqs` |
| POST | `/admin/website/faqs` |
| POST | `/admin/website/faqs/{id}` |
| DELETE | `/admin/website/faqs/{id}` |

---

### الشركاء والآراء والمميزات (Admin)
| Section | Endpoints |
|---------|-----------|
| Partners | GET/POST `/admin/website/partners`, POST/DELETE `/{id}` |
| Testimonials | GET/POST `/admin/website/testimonials`, POST/DELETE `/{id}` |
| Features | GET/POST `/admin/website/features`, POST/DELETE `/{id}` |
| Sectors | GET/POST `/admin/website/sectors`, POST/DELETE `/{id}` |

---

| GET | `/website/features` | لماذا تختارنا |
| GET | `/website/sectors` | قطاعات العمل |
| GET | `/website/partners` | الشركاء |
| GET | `/website/testimonials` | الآراء |
| POST | `/website/testimonials` | إضافة رأي |
| GET | `/website/faqs` | الأسئلة الشائعة |
| GET | `/website/pages` | الصفحات الديناميكية |
| GET | `/website/pages/{slug}` | صفحة بالـ slug |

### النشرة الإخبارية
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST/GET | `/website/subscribe` | الاشتراك في النشرة الإخبارية (في الـ GET نمرر `?email=...`) |

**Example (POST):** `{"email": "user@example.com"}`

---

## 🔐 Authentication (الهاتف و OTP)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/auth/login-phone` | طلب إرسال رمز OTP (أرسل `phone`) |
| POST | `/auth/verify-otp` | التحقق من الرمز والحصول على Token (أرسل `phone`, `otp`) |
| GET | `/auth/profile` | بيانات الملف الشخصي للمستخدم |

---

## 💳 Donations & Payments (التبرعات)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/donations` | إرسال تبرع جديد (Polymorphic) |
| POST | `/donations/upload-proof` | رفع إيصال الدفع (أرسل `donation_id`, `proof`) |
| GET | `/my-donations` | عرض قائمة تبرعاتي الخاصة |

**Donation Fields:**
`type*` (campaign/project), `target_id*`, `amount*`, `payment_method*` (instapay/vodafone_cash/card)

---

## 🔐 Admin APIs (يحتاج: `Authorization: Bearer {token}`)

### إدارة النشرة الإخبارية (Admin)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/subscriptions` | عرض كل المشتركين |
| DELETE | `/admin/website/subscriptions/{id}` | حذف مشترك |

---

### إعدادات الموقع
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/settings` | جلب كل الإعدادات |
| POST | `/admin/website/settings` | تحديث الإعدادات + الصور |
| POST | `/admin/website/donation-page` | تحديث نصوص وإعدادات صفحة التبرعات |

**Donation Page Settings Fields (multipart/form-data):**
- `donation_page_campaign_title` (string, optional)
- `donation_page_campaign_link` (string, optional) - Direct link for the banner (بدلاً من صورة البنر).
- `hero_title`: العنوان الرئيسي للهيرو.
- `hero_description`: الوصف الرئيسي.
- `stats_donors_count`: عدد المتبرعين (نص).
- `stats_today_amount`: تبرعات اليوم (نص).
- `projects_title`: عنوان قسم المشاريع.
- `projects_subtitle`: عنوان فرعي للمشاريع.
- `categories`: مصفوفة من الفئات (مثال: `["الكل", "المشاريع"]`).

---

### إدارة التبرعات (Admin)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/donations` | عرض كل التبرعات (مع الفلترة) |
| POST | `/admin/donations/verify` | تأكيد وصول التبرع (أرسل `donation_id`) |
| POST | `/admin/donations/reject` | رفض التبرع/الإيصال (أرسل `donation_id`, `reason`) |

### حسابات متبرعي الويب (Donation Accounts)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/donation-accounts` | قائمة المتبرعين (اسم، هاتف، عدد التبرعات، إجمالي الموثق) |
| GET | `/admin/website/donation-accounts/{id}` | سجل تبرعات متبرع محدد بالتفصيل |
| PUT | `/admin/website/donation-accounts/{id}` | تحديث بيانات متبرع (اسم، هاتف) |
| POST | `/admin/website/donation-accounts/donations/{id}/verify` | توثيق تبرع محدد قادم من الويب |
| POST | `/admin/website/donation-accounts/donations/{id}/reject` | رفض تبرع محدد قادم من الويب |

**Image fields (multipart/form-data):**
`hero_image`, `featured_campaign_image`, `featured_campaign_icon1_image`,
`featured_campaign_icon2_image`, `gh_home_image`,
`gallery_image_1..10`, `news_slider_1..10`, `contact_slider_1..10`,
`volunteer_slider_1..10`, `gh_slider_1..10`

---

### مجلس الأمناء (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/board-members` |
| POST | `/admin/website/board-members` |
| POST | `/admin/website/board-members/{id}` |
| DELETE | `/admin/website/board-members/{id}` |

---

### الفروع (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/branches` |
| POST | `/admin/website/branches` |
| POST | `/admin/website/branches/{id}` |
| DELETE | `/admin/website/branches/{id}` |

---

### جدار الشرف (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/volunteer-wall` |
| POST | `/admin/website/volunteer-wall` |
| POST | `/admin/website/volunteer-wall/{id}` |
| DELETE | `/admin/website/volunteer-wall/{id}` |

---

### الأخبار (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/news` |
| POST | `/admin/website/news` |
| POST | `/admin/website/news/{id}` |
| DELETE | `/admin/website/news/{id}` |

---

### الفعاليات (Admin)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/events` | قائمة كل الفعاليات |
| POST | `/admin/website/events` | إضافة فعالية |
| POST | `/admin/website/events/{id}` | تعديل فعالية |
| DELETE | `/admin/website/events/{id}` | حذف فعالية |

**Fields:** `title*, content, location, category, event_date, event_end_date, is_featured, published_at, image(file), delete_image`

---

### المشاريع (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/projects` |
| POST | `/admin/website/projects/{id}` |

---

### الحملات (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/campaigns` |
| POST | `/admin/website/campaigns/{id}` |

---

### الرسائل والطلبات (Admin - Read Only)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/website/contact-messages` | رسائل التواصل |
| PATCH | `/admin/website/contact-messages/{id}/read` | تعليم كمقروء |
| DELETE | `/admin/website/contact-messages/{id}` | حذف |
| GET | `/admin/website/volunteer-requests` | طلبات التطوع |
| DELETE | `/admin/website/volunteer-requests/{id}` | حذف |
| GET | `/admin/website/room-bookings` | حجوزات دار الضيافة |
| PATCH | `/admin/website/room-bookings/{id}/status` | تحديث الحالة |
| DELETE | `/admin/website/room-bookings/{id}` | حذف |

---

### الأسئلة الشائعة (Admin)
| Method | Endpoint |
|--------|----------|
| GET | `/admin/website/faqs` |
| POST | `/admin/website/faqs` |
| POST | `/admin/website/faqs/{id}` |
| DELETE | `/admin/website/faqs/{id}` |

---

### الشركاء والآراء والمميزات (Admin)
| Section | Endpoints |
|---------|-----------|
| Partners | GET/POST `/admin/website/partners`, POST/DELETE `/{id}` |
| Testimonials | GET/POST `/admin/website/testimonials`, POST/DELETE `/{id}` |
| Features | GET/POST `/admin/website/features`, POST/DELETE `/{id}` |
| Sectors | GET/POST `/admin/website/sectors`, POST/DELETE `/{id}` |

---

## 📝 ملاحظات

1. **صور الرفع:** كل الـ requests التي تحتوي صور يجب أن تكون `multipart/form-data`
2. **حذف صورة:** أرسل `delete_image=1` مع الـ POST request
3. **الـ Cache:** الصفحة الرئيسية تُخزَّن مؤقتاً لمدة ساعة — تُمسح تلقائياً عند تحديث الإعدادات
4. **تحديث الإعدادات** للصور المفردة: أرسل الحقل بـ `null` لحذف الصورة
5. **الـ Auth:** استخدم `POST /api/v1/auth/login` للحصول على الـ token
