# Ensan — Project Module Structure

## 1. الملخص التنفيذي

المشروع حاليًا **Modular Monolith هجين** مبني على Laravel 11 وPHP 8.2+.

- الجزء الأكبر من النظام منظم بأسلوب Laravel التقليدي متعدد الطبقات:
  `Route -> Controller -> FormRequest -> Service -> Repository -> Model -> View/JSON`.
- يوجد اتجاه أحدث نحو Vertical Features داخل `app/Features`، لكنه مطبق حاليًا في ميزتين فقط:
  `WebsiteContent` و`WebsiteDonations`.
- الواجهات الرئيسية ثلاث:
  1. لوحة الإدارة Web/Blade.
  2. REST APIs للنظام والموقع وAnasen.
  3. Mobile API مع إدارة محتوى الموبايل من لوحة الإدارة.
- الحجم الحالي التقريبي: 831 route، و103 Controllers، و107 Models، و42 ملف Service/Feature، و29 Repository، و208 Blade views، و203 migrations.

هذا الملف يوثق **التقسيم المنطقي الكامل للموديولات** ثم يقترح **الشكل الفيزيائي المستهدف** إذا تقرر إعادة التنظيم لاحقًا.

---

## 2. الـ Runtime Entry Points

| الواجهة | نقطة الدخول | النطاق الأساسي | الحماية |
|---|---|---|---|
| لوحة الإدارة | `routes/web.php` | `/` | Session auth + permissions + audit |
| Core/Public API | `routes/api.php` | `/api/v1` | Public أو `TokenAuth` حسب المجموعة |
| Website/Anasen API | `routes/api.php` | `/api/v1/website` | Public، أو `AnasenApiAuth`، أو Admin only |
| Mobile API | `routes/mobile_api.php` | `/api/v1/mobile` | Public endpoints + `TokenAuth` للملف الشخصي والموظف |
| CLI | `routes/console.php` و`app/Console/Commands` | Artisan | تشغيل يدوي/تشغيلي |

### مسار الطلب المعتاد

```text
HTTP Request
  -> Route
  -> Middleware (Auth / Permission / Audit / Hashed Route Key)
  -> Controller
  -> FormRequest Validation
  -> Service / Feature Service
  -> Repository
  -> Eloquent Model + Database
  -> Blade View أو JSON Response
```

### طبقات المشروع الحالية

```text
app/
├── Http/
│   ├── Controllers/        Web + Core API + Website API + Mobile admin
│   ├── Requests/           Validation and input normalization
│   └── Middleware/         Authentication, authorization, audit, route security
├── Services/               Business workflows and cross-module orchestration
├── Repositories/           Database query and persistence layer
├── Models/                 Flat Eloquent model collection
├── Features/               New vertical feature slices (only 2 currently)
├── Support/                Shared presentation/support logic
├── Traits/                 Cross-cutting model/upload behavior
└── Providers/              Dependency bindings and application bootstrapping

resources/views/            Blade UI grouped mostly by business resource
routes/                     Web, API, mobile API, console routes
database/                   Migrations and seeders
public/                     Public assets and uploaded/served media
tests/                      Feature tests (currently limited coverage)
maintenance/                Legacy/admin repair and maintenance scripts
scripts/                    Operational/helper scripts
```

---

## 3. Module Catalog

## M01 — Identity & Access

**المسؤولية:** تسجيل الدخول، المستخدمون، الأدوار، الصلاحيات، جلسات الويب، وتوكنات الـ APIs.

- Models: `User`, `Role`, `Permission`, `Token`, `PersonalAccessToken`.
- Web Controllers: `LoginWebController`, `UserWebController`, `RoleWebController`.
- API Controllers: `AuthController`, `UserController`, `RoleController`, `Api/AuthController`, و`Api/Anasen/AuthController`.
- Services/Repositories: `UserService`, `RoleService`, `UserRepository`, `RoleRepository`.
- Requests: `Store/UpdateUserRequest`, `Store/UpdateRoleRequest`.
- Views: `auth`, `users`, `roles`.
- Middleware: `WebAuth`, `TokenAuth`, `AnasenApiAuth`, `AnasenAdminOnly`, `SecurePermissionMiddleware`, `OwnUserOrPermission`, `RoleAccess`.
- يعتمد عليه: كل الموديولات الإدارية.

## M02 — Governance, Audit & Approval Workflow

**المسؤولية:** تسجيل العمليات، مراجعة واعتماد التغييرات، وإبقاء طلبات المراجعة داخل سياق الشاشة.

- Models: `Audit`, `ChangeRequest`, `NotificationLog`.
- Controllers: `AuditWebController`, `ChangeRequestWebController`, `NotificationWebController`.
- Services: `ChangeRequestService`, `NotificationService`.
- Support: `ChangeRequestPresentation`.
- Middleware: `AuditLogger`, `KeepReviewRequestsInContext`, `EnsureHashedRouteKeys`.
- Views: `audits`, `change_requests`, `notifications`.
- يعتمد على: Identity & Access.
- مستخدم بواسطة: Donations، Beneficiaries، Finance، Projects، Campaigns، وغيرهم.

## M03 — Dashboard, Reporting & Analytics

**المسؤولية:** مؤشرات لوحة التحكم والتقارير المجمعة عبر الموديولات.

- Controllers: `DashboardWebController`, `ReportsWebController`, `ReportsController`.
- Views: `dashboard`, `reports`, وأجزاء dashboard داخل بعض الموديولات.
- مصادر البيانات: Donations، Beneficiaries، Projects، Campaigns، Finance، HR، Inventory، Logistics، Guest Houses.
- ملاحظة: هذا Read Model عابر للموديولات وليس مصدرًا مستقلًا للحقيقة.

## M04 — Donors & Fundraising

**المسؤولية:** إدارة المتبرعين، بيانات التواصل، التصنيف، الكفالات والتخصيصات الدورية.

- Models: `Donor`، وبشكل خاص بقناة الموقع `WebDonor`.
- Controllers: `DonorWebController`, `DonorController`.
- Services/Repositories: `DonorService`, `DonorRepository`.
- Requests: `StoreDonorRequest`, `UpdateDonorRequest`.
- Views: `donors`.
- يعتمد على: Beneficiaries للتوجيه والكفالة، وProjects/Campaigns/Guest Houses للتخصيص.

## M05 — Donations & Donation Catalog

**المسؤولية:** التبرعات النقدية والعينية، الإثباتات، قنوات الاستلام، تصنيفات التبرع، وتوجيه التبرع.

- Models: `Donation`, `DonationProof`, `DonationCategory`, `DonationItem`, `PaymentTransaction`, `WebDonation`, `MobileDonation`, `MobileInKindDonation`.
- Web Controllers: `DonationWebController`, `DonationCategoryWebController`, `DonationItemWebController`, `AdminWebsiteDonationWebController`.
- API Controllers: `DonationController`, `Api/DonationController`, `Api/AdminDonationController`, `Api/PublicWebsiteDonationController`, `Api/AdminWebsiteDonationController`, وAnasen donation controllers.
- Services: `DonationService`, `DonationProcessingService`.
- Feature slice: `app/Features/WebsiteDonations`.
- Repository: `DonationRepository`.
- Requests: `StoreDonationRequest`, `UpdateDonationRequest`.
- Views: `donations`, `donation-settings`, `website/donations`.
- يعتمد على: Donors، Treasury/Accounting، Inventory، Projects، Campaigns، Beneficiaries، Guest Houses.

## M06 — Beneficiary & Case Management

**المسؤولية:** ملفات المستفيدين، الأسرة، التخصيص، الزيارات الميدانية والمرفقات.

- Models: `Beneficiary`, `FieldVisit`, `Attachment`.
- Controllers: `BeneficiaryWebController`, `BeneficiaryController`, `VisitWebController`, `AttachmentWebController`, `AttachmentController`.
- Services/Repositories: `BeneficiaryService`, `BeneficiaryRepository`.
- Requests: `StoreBeneficiaryRequest`, `UpdateBeneficiaryRequest`.
- Views: `beneficiaries`, `dashboard/visits`.
- يعتمد على: Projects، Campaigns، Guest Houses، Donors، Governance.
- تعتمد عليه: Donations، البرامج المتخصصة، والإقامات.

## M07 — Projects & Campaigns

**المسؤولية:** المشروعات والحملات، الإدارة والنواب، الأنشطة، المتطوعون، ملفات المستفيدين، الإيرادات والمصروفات المرتبطة.

- Models: `Project`, `ProjectActivity`, `ProjectMonthlyVolunteer`, `Campaign`, `CampaignMonthlyVolunteer`, `CampaignDailyMenu`.
- Controllers: `ProjectWebController`, `ProjectController`, `CampaignWebController`, `CampaignController`.
- Services/Repositories: `ProjectService`, `CampaignService`, `ProjectRepository`, `CampaignRepository`.
- Requests: Store/Update project and campaign requests، `StoreProjectActivityRequest`, `SetCampaignManagerRequest`, `AttachVolunteerRequest`.
- Views: `projects`, `campaigns`.
- يعتمد على: Beneficiaries، Volunteers، Donations، Expenses، Users/RBAC.
- ملاحظة: Project وCampaign هما Aggregate roots أساسيان في النظام.

## M08 — Programs & Integrated Services

**المسؤولية:** الخدمات والمبادرات التشغيلية المتخصصة التابعة للمؤسسة.

- Core presentation models: `EnsanPillar`, `EnsanPillarCard`, `IntegratedService`.
- Program models: `ZadFamily`, `RamadanBag`, `RamadanIftar`, `SchoolCollaboration`, `TantaWorker`, `KafrElSheikhDelivery`, `KafrElSheikhService`, `KafrElSheikhBroker`, `OncologyMedicineRep`, `Membership`.
- Controllers: `Api/EnsanPillarController`, `RamadanBagWebController`, `RamadanIftarWebController`, `SchoolCollaborationWebController`, `TantaWorkerWebController`, `KafrElSheikhDeliveryWebController`, `KafrElSheikhServiceWebController`, `KafrElSheikhBrokerWebController`, `OncologyMedicineRepWebController`, `MembershipWebController`.
- Services/Repositories المتاحة: `RamadanBagService/Repository`, `RamadanIftarService/Repository`؛ باقي البرامج تعتمد بدرجة أكبر على Controllers + Models مباشرة.
- Requests: طلبات رمضان وZad، بينما عدة برامج أخرى تستخدم validation داخل Controllers.
- Views: `ramadan_bags`, `ramadan_iftars`, `school_collaborations`, `tanta_workers`, `kafr_el_sheikh_*`, `oncology_medicine_reps`, `memberships`.
- يعتمد على: Projects، Beneficiaries، Donations/Finance حسب البرنامج.

## M09 — Finance, Accounting & Treasury

**المسؤولية:** دليل الحسابات، القيود، الخزن، الحركات المالية، المصروفات، الإيرادات والإقفالات.

- Models: `Account`, `JournalEntry`, `JournalEntryLine`, `Treasury`, `TreasuryTransaction`, `Expense`, `FinancialClosure`.
- Controllers: `AccountWebController/AccountController`, `JournalEntryWebController/JournalEntryController`, `TreasuryController`, `ExpenseWebController/ExpenseController`, `RevenueWebController`, `FinancialClosureWebController/FinancialClosureController`.
- Services: `AccountService`, `JournalEntryService`, `TreasuryService`, `TreasuryIntegrationService`, `ExpenseService`, `FinancialClosureService`.
- Repositories: matching repositories for all main aggregates.
- Views: `accounts`, `journal_entries`, `treasuries`, `expenses`, `revenues`, `closures`.
- يعتمد على: Identity/RBAC وGovernance.
- يستقبل حركات من: Donations، Payroll، Logistics، Inventory/Procurement، Projects/Campaigns.

## M10 — Inventory & Procurement

**المسؤولية:** المخازن، الأصناف، حركات الإدخال والإخراج والنقل والتسوية، الموردون والمشتريات.

- Models: `Warehouse`, `Item`, `InventoryTransaction`, `Supplier`, `Purchase`.
- Controllers: `WarehouseWebController/WarehouseController`, `ItemWebController/ItemController`, `InventoryTransactionWebController/InventoryTransactionController`, `SupplierWebController`, `PurchaseWebController`.
- Services: `WarehouseService`, `WarehouseIntegrationService`, `ItemService`, `InventoryTransactionService`, `SupplierService`, `PurchaseService`.
- Repositories: corresponding Warehouse/Item/Inventory/Supplier/Purchase repositories.
- Requests: Store/Update requests، بالإضافة إلى transfer وreconcile requests.
- Views: `warehouses`, `items`, `inventory`, `suppliers`.
- يعتمد على: Finance للمحاسبة، Donations للتبرعات العينية، Guest Houses للاستهلاك/التخصيص.

## M11 — HR & Employee Operations

**المسؤولية:** الحضور والانصراف، الإجازات، التقييمات، مهام الموظفين والرواتب.

- Models: `EmployeeAttendance`, `Leave`, `Payroll`, `Task`، مع استخدام `User` كملف الموظف.
- Controllers: `HrDashboardWebController`, `HrEvaluationWebController`, `EmployeeAttendanceWebController`, `LeaveWebController`, `EmployeeTaskWebController`, `TaskWebController/TaskController`, `PayrollWebController/PayrollController`.
- Services: `EmployeeAttendanceService`, `LeaveService`, `HrEvaluationService`, `TaskService`, `PayrollService`, `PayrollAccountingService`.
- Repositories: EmployeeAttendance، Leave، Task، Payroll.
- Views: `hr`, `employee_attendance`, `leaves`, `employee_tasks`, `tasks`, `payrolls`.
- يعتمد على: Identity، Finance للرواتب والقيود، Attachments.

## M12 — Volunteer Management

**المسؤولية:** ملفات المتطوعين، ساعات التطوع، الحضور، المهام، والربط بالمشروعات والحملات ودور الضيافة.

- Models: `VolunteerHour`, `VolunteerAttendance`، profile fields داخل `User`، وجداول الربط الشهرية داخل Project/Campaign/GuestHouse.
- Controllers: `VolunteerWebController`, `VolunteerHourWebController/VolunteerHourController`, `VolunteerAttendanceWebController`, `VolunteerTaskWebController`.
- Services: `VolunteerService`, `VolunteerAttendanceService`.
- Repositories: `VolunteerRepository`, `VolunteerAttendanceRepository`.
- Views: `volunteers`, `volunteer_hours`, `volunteer_attendance`, `volunteer_tasks`.
- يعتمد على: Identity، Projects، Campaigns، Guest Houses، Attachments.

## M13 — Logistics & Delegates

**المسؤولية:** المندوبون، خطوط السير، الرحلات، الأداء، التقييمات وصيانة المركبات.

- Models: `Delegate`, `DelegateRating`, `DelegateTrip`, `TravelRoute`, `ScheduledTrip`, `VehicleMaintenance`.
- Controllers: `DelegateWebController/DelegateController`, `TravelRouteWebController/TravelRouteController`, `TripWebController`, `LogisticsDashboardController`.
- Services: `DelegateService`, `TravelRouteService`, `TripService`, `LogisticsAccountingService`.
- Repositories: Delegate، TravelRoute، Trip.
- Views: `delegates`, `routes`, `trips`.
- يعتمد على: Users، Donations، Treasury/Accounting، Beneficiaries/Projects حسب الرحلة.

## M14 — Guest Houses & Patient Stay Operations

**المسؤولية:** دور الضيافة، الأجنحة والأسرة، ملفات المرضى، الإقامات، الوجبات، العهدة والمتطوعون والحجوزات.

- Models: `GuestHouse`, `GuestHouseWing`, `GuestHouseBed`, `GuestHousePatientProfile`, `GuestHouseStay`, `GuestHouseMeal`, `GuestHouseMealServing`, `GuestHouseCustody`, `GuestHouseMonthlyVolunteer`, `WebRoomBooking`, `MobileRoomBooking`.
- Controllers: `GuestHouseWebController`, `Website/GuestHouseController`، وأجزاء من Website/Mobile controllers للحجوزات.
- Services/Repositories: `GuestHouseService`, `GuestHouseRepository`.
- Requests: `Store/UpdateGuestHouseRequest`, `SetGuestHouseManagerRequest`, `AttachGuestHouseVolunteerRequest`, `StoreDailyMenuRequest`.
- Views: `guest_houses` و`guest_houses/partials`.
- يعتمد على: Beneficiaries، Volunteers، Inventory، Donations، Website/Mobile booking channels.

## M15 — Complaints, Reception & Engagement

**المسؤولية:** الشكاوى وتتبعها، الاستقبال، رسائل التواصل والاشتراكات.

- Models: `Complaint`, `ReceptionLog`, `WebContactMessage`, `MobileContactMessage`, `NewsletterSubscription`.
- Controllers: `ComplaintWebController/ComplaintController`, `ComplaintTrackingController`, `ReceptionWebController`، وأجزاء من Website/Mobile controllers.
- Views: `complaints`, `dashboard/reception`، وصفحات إدارة الرسائل والاشتراكات داخل `website` و`mobile`.
- يعتمد على: Identity في الإدارة؛ complaint tracking وبعض قنوات التواصل Public.

## M16 — Website CMS & Public Website API

**المسؤولية:** محتوى موقع المؤسسة، الصفحات، الإعدادات، الأخبار، الشركاء، مجلس الإدارة، الفروع، القطاعات، البطاقات والآراء.

- Models: `WebSetting`, `WebPage`, `WebNews`, `WebPartner`, `WebBoardMember`, `WebBranch`, `WebSector`, `WebFeature`, `WebDynamicCard`, `WebFaq`, `WebEvent`, `WebTestimonial`, `WebOpinion`, `WebVolunteerLeader`, `WebVolunteerWall`, `WebVolunteerRequest`.
- Controllers: `WebsiteWebController`, `Api/WebsiteApiController`, `Api/WebsiteContentController`, `Api/PublicContentController`.
- Feature slice: `app/Features/WebsiteContent`.
- Views: `resources/views/website`.
- يعتمد على: Projects، Campaigns، Guest Houses، Donations، Volunteers، Engagement.
- ملاحظة: Website donations لها ownership مشترك حاليًا بين هذا الموديول وM05.

## M17 — Mobile App & Mobile Content Management

**المسؤولية:** API تطبيق الموبايل، المحتوى، الإشعارات، الطلبات، الحجوزات، الملف الشخصي وواجهة الموظف.

- Models: `MobileBanner`, `MobileHeroCard`, `MobileHomeItem`, `MobileNews`, `MobileNotification`, `MobileCaseApplication`, `MobileVolunteerRequest`, `MobileContactInfo`, `MobileContactPhone`، إضافة إلى donation/booking/contact models المذكورة في موديولاتها الأساسية.
- Controllers: `Api/MobileApiController`, `MobileContentController`.
- Routes: `routes/mobile_api.php` تحت `/api/v1/mobile`، ومسارات إدارة المحتوى تحت `/admin/mobile`.
- Views: `mobile`.
- يعتمد على: Identity tokens، Donations، Beneficiaries، HR، Guest Houses، Volunteers، Website-style content.

## M18 — Workspaces & Rentals

**المسؤولية:** مساحات العمل والإيجارات وربط المصروفات بها.

- Models: `Workspace`, `WorkspaceRental`.
- Controller: `WorkspaceWebController`.
- Views: `workspaces`.
- يعتمد على: Finance/Expenses وIdentity.

## M19 — Media & File Operations

**المسؤولية:** رفع الصور والمرفقات، تحسين الصور وتقديم ملفات الوسائط.

- Models/Traits: `Attachment`, `UploadsImages`.
- Controllers: `ImageUploadController`, `MediaController`, `AttachmentWebController`, `AttachmentController`.
- Service: `ImageUploadService`.
- Requests: `UploadImageRequest`.
- Commands: `FixStoragePaths`, `OptimizeExistingImages`.
- يعتمد عليه: Website CMS، Mobile CMS، Beneficiaries، HR، Volunteers، Complaints.

---

## 4. خريطة الاعتماديات الرئيسية

```text
Identity & Access
  ├── Governance / Audit / Approval
  ├── HR & Volunteers
  └── كل واجهات الإدارة

Projects & Campaigns
  ├── Beneficiaries
  ├── Volunteers
  ├── Donations
  ├── Expenses / Finance
  └── Programs

Donors -> Donations
Donations
  ├── Treasury & Accounting
  ├── Inventory (in-kind)
  ├── Beneficiary allocation
  ├── Projects / Campaigns
  └── Guest Houses

Guest Houses
  ├── Beneficiaries
  ├── Volunteers
  ├── Inventory
  └── Website/Mobile bookings

Website CMS + Mobile CMS
  ├── Public content
  ├── Projects / Campaigns
  ├── Donations
  ├── Guest Houses
  └── Engagement requests

Dashboard & Reports -> Read-only aggregation from almost all modules
```

قاعدة الاعتمادية المقترحة: موديولات الـ channels مثل Website وMobile يجب أن تستدعي Application contracts للموديولات الأساسية، ولا تكتب مباشرة في جداولها.

---

## 5. الـ Target Module Structure المقترح

الشكل التالي هو الشكل المستهدف لكل Module عند إعادة التنظيم. هو **اقتراح refactor** وليس الشكل الفيزيائي الحالي.

```text
app/Modules/{ModuleName}/
├── Domain/
│   ├── Models/
│   ├── Enums/
│   ├── Events/
│   ├── Exceptions/
│   └── Policies/
├── Application/
│   ├── Actions/
│   ├── DTOs/
│   ├── Queries/
│   ├── Services/
│   └── Contracts/
├── Infrastructure/
│   ├── Persistence/
│   │   └── Repositories/
│   ├── Integrations/
│   └── Providers/
├── Presentation/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Web/
│   │   │   └── Api/
│   │   ├── Requests/
│   │   └── Resources/
│   └── routes/
│       ├── web.php
│       └── api.php
└── Tests/
    ├── Unit/
    └── Feature/

resources/views/modules/{module-name}/
database/migrations/{module-name}/
```

### الشكل المستهدف على مستوى المشروع

```text
app/Modules/
├── Shared/
├── IdentityAccess/
├── Governance/
├── Analytics/
├── Fundraising/
│   ├── Donors/
│   └── Donations/
├── BeneficiaryCare/
├── Programs/
│   ├── Projects/
│   ├── Campaigns/
│   └── SpecializedPrograms/
├── Finance/
├── InventoryProcurement/
├── People/
│   ├── HR/
│   └── Volunteers/
├── Logistics/
├── GuestHouses/
├── Engagement/
├── WebsiteChannel/
├── MobileChannel/
├── Workspaces/
└── Media/
```

### مثال: Donations بعد التنظيم

```text
app/Modules/Fundraising/Donations/
├── Domain/
│   ├── Models/Donation.php
│   ├── Models/DonationProof.php
│   ├── Enums/DonationStatus.php
│   └── Events/DonationReceived.php
├── Application/
│   ├── Actions/CreateDonation.php
│   ├── Actions/VerifyDonation.php
│   ├── DTOs/CreateDonationData.php
│   └── Contracts/DonationRepository.php
├── Infrastructure/
│   ├── Persistence/EloquentDonationRepository.php
│   └── Integrations/TreasuryDonationPoster.php
└── Presentation/
    ├── Http/Controllers/Web/DonationController.php
    ├── Http/Controllers/Api/DonationController.php
    ├── Http/Requests/StoreDonationRequest.php
    └── routes/{web,api}.php
```

---

## 6. Ownership Rules المقترحة

1. كل Model له Module owner واحد فقط.
2. Website وMobile هما Channels وليسا مصدر الحقيقة للعمليات الأساسية.
3. لا يستدعي Controller Model من Module آخر مباشرة؛ يستدعي Contract أو Application Action.
4. Finance وحده يكتب القيود والحركات المالية؛ الموديولات الأخرى تطلب عملية ترحيل منه.
5. Inventory وحده يكتب حركات المخزون.
6. Dashboard/Reports يقرأ من الموديولات ولا يملك عملياتها.
7. Change Requests وAudit يعالجان العمليات من خلال events/contracts بدل شروط حسب أسماء الـ Models.
8. Blade/API resources تخص Presentation فقط ولا تحتوي business rules.

---

## 7. ملاحظات معمارية مهمة من الوضع الحالي

- `routes/web.php` و`routes/api.php` كبيران؛ الأفضل تقسيمهما إلى route files حسب Module.
- `WebsiteWebController`, `Api/WebsiteApiController`, `MobileContentController`, و`DashboardWebController` تجمع مسؤوليات كثيرة ويجب تقسيمها تدريجيًا.
- أسماء مثل `DonationController` موجودة في أكثر من namespace وقناة؛ التنظيم المقترح يجعل القناة واضحة.
- Models حاليًا داخل مجلد مسطح واحد، لذلك ownership بين الموديولات غير ظاهر من المسار.
- يوجد تكرار بين Web/API flows في عدد من الموارد رغم وجود Service/Repository layer.
- `app/Features` يمثل الاتجاه الأنسب، لكنه غير مطبق إلا في Website content/donations.
- تغطية الاختبارات منخفضة مقارنة بحجم النظام: توجد ثلاثة Feature test files فقط حاليًا.
- يوجد داخل `routes/web.php` مسار صيانة عام يعيد تعيين كلمة مرور إدارية؛ يجب حذفه فورًا وتحويل أي عملية مماثلة إلى Console Command محمي.

---

## 8. ترتيب Refactor آمن مقترح

1. إزالة مسارات الصيانة الحساسة وتأمين التشغيل.
2. تقسيم route files بدون تغيير السلوك.
3. تحديد ownership لكل Model وربطه بموديول واحد.
4. استخراج Website CMS وMobile CMS أولًا لأن حدودهما واضحة.
5. توحيد Donations عبر Application actions واحدة لكل القنوات.
6. فصل Finance وInventory خلف contracts ثابتة.
7. نقل Projects/Campaigns/Beneficiaries مع الحفاظ على العلاقات الحالية.
8. فصل HR/Volunteers/Logistics/Guest Houses.
9. تحويل Dashboard/Reports إلى query/read layer.
10. إضافة tests لكل Module قبل نقل business logic الخاص به.

لا يوصى بنقل كل الملفات دفعة واحدة؛ الأفضل Strangler Refactor بحيث ينقل كل use case مع route واختباراته ثم تزال النسخة القديمة.
