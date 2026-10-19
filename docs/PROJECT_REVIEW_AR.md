# تقرير مراجعة مشروع Ensan

تاريخ الفحص: 2026-10-19. هذه مراجعة للكود والتهيئة المحلية وليست شهادة بخلو الأجزاء الأخرى من العيوب. المطلوب تحليل فقط؛ لم أغير منطق التطبيق أو بياناته.

## النطاق وطريقة التحقق

- Laravel مع Blade وواجهات Website/Mobile API، وخدمات ومستودعات ونظام طلبات اعتماد.
- 104 متحكمات، 108 نماذج، 216 ترحيلًا، 234 قالب PHP/Blade، و3 ملفات اختبارات.
- فحص صياغة 621 ملف PHP في app/routes/config/database/bootstrap: نجح دون أخطاء نحوية.
- تسجيل 841 مسارًا؛ فحص 833 ربطًا إلى متحكمات: جميع الأصناف والدوال المشار إليها موجودة. هذا لا يثبت صحة الصلاحيات أو التنفيذ.
- npm run build: نجح. خرج JavaScript فارغ لأن resources/js/app.js يحتوي تعليقات فقط؛ ليس خطأ بناء بحد ذاته. CSS الناتج نحو 458KB قبل الضغط.
- composer validate: صالح مع تحذير غياب license.
- php artisan test: غير متاح؛ لا أزعم نجاح الاختبارات.
- تجربة migrate على SQLite في الذاكرة توقفت عند ترحيل بصيغة MySQL؛ لم أشغل ترحيلات على قاعدة المشروع الفعلية.
- ظهرت تحذيرات إهمال PDO::MYSQL_ATTR_SSL_CA من Laravel عند PHP 8.5.6، دون منع route:list والبناء. يجب اختبار مصفوفة PHP المدعومة وعدم إخفاء التحذيرات بدل المعالجة.
- أعدت ملفات public/build إلى حالتها السابقة بعد التحقق. الإضافة الوحيدة المقصودة للمستودع هي هذا التقرير ومخرجات تدقيق التبعيات.

النتائج المؤكدة بالكود موضحة أدناه. شروط النشر والتزامن مذكورة حيث تؤثر على الاستغلال أو ظهور العطل. لم أختبر اختراقًا على خادم خارجي، ولم أرسل رسائل SMS، ولم أقرأ محتوى المفتاح الخاص.

## المشكلات المكتشفة

### 01. إمكانية رفع صلاحيات الحساب بإسناد دور إداري — حرج

الموقع: [app/Http/Middleware/RoleAccess.php:89](../app/Http/Middleware/RoleAccess.php#L89).

مسارا users.attachRole وusers.detachRole موجودان في API، لكن attachRole وdetachRole غير موجودين في خريطة الإجراءات. عند غياب suffix يسمح الوسيط بالمرور، وUserController ينفذ إسناد الدور مباشرة دون تفويض آخر. حساب عادي نشط يحمل Token يستطيع من حيث منطق الكود إسناد دور متاح، بما فيه admin، إذا عرف معرّف المستخدم والدور. تشفير معرفات بعض الروابط ليس تفويضًا.

الإصلاح المقترح: إضافة صلاحية صريحة لإدارة إسناد الأدوار، ومنع الإجراءات غير المعروفة افتراضيًا، واختبار محاولة التصعيد بحساب متبرع.

### 02. واجهات إدارة الشكاوى والمتبرعين بلا مصادقة — حرج

الموقع: [routes/api.php:113](../routes/api.php#L113).

مجموعة admin/website الأولى خارج مجموعة TokenAuth. تشمل عرض الشكاوى وتعديلها وبيانات المتبرعين وتعديلها وعرض التبرعات. لا توجد حماية تعويضية في adminListComplaints وadminUpdateComplaint وupdateDonor. اعتماد/رفض التبرع في هذه المجموعة يعاني كذلك خطأ النوع المذكور لاحقًا؛ لذلك لا أعتبر نجاح الاعتماد المجهول مثبتًا.

الإصلاح المقترح: نقل المجموعة إلى مصادقة وصلاحيات صريحة لكل عملية، مع اختبارات 401 و403.

### 03. أي حساب API يستطيع الوصول إلى بعض وظائف الموظفين — حرج

الموقع: [routes/mobile_api.php:87](../routes/mobile_api.php#L87).

المجموعة تستخدم TokenAuth فقط. storeNews لا يتحقق من دور الموظف أو صلاحية النشر، وgetVolunteerRequests يعيد جميع طلبات التطوع دون تفويض. النموذج يتضمن الرقم القومي والعنوان ومسارات الوثائق والملاحظات الإدارية؛ أي متبرع مسجل يمكنه الوصول وفق الكود.

الإصلاح المقترح: فرض صلاحيات مستقلة للنشر والاطلاع، وإرجاع حقول محددة مع pagination.

### 04. مفتاح SSH خاص متتبّع داخل Git — حرج مشروط بصلاحية المفتاح

الموقع: [id_rsa2:1](../id_rsa2#L1).

أكد git ls-files أن id_rsa2 وملفه العام متتبّعان، رغم وجود id_rsa* في gitignore. إضافة النمط لا تلغي تتبع ملف سابق. لم أقرأ قيمة المفتاح أو أختبر الخادم الذي يفتح له؛ لا يمكن الجزم بأنه ما زال صالحًا.

الإصلاح المقترح: إلغاء المفتاح واستبداله إذا استُخدم، وإزالته من التتبع والتاريخ بعد تنسيق ذلك مع الفريق.

### 05. حذف صور الآخرين اعتمادًا على المسار فقط — عالٍ

الموقع: [app/Http/Controllers/ImageUploadController.php:122](../app/Http/Controllers/ImageUploadController.php#L122).

واجهات رفع/حذف الصور في API تحتاج Token فقط. destroy يقبل path دون ملكية أو صلاحية للسجل، وupload يقبل old_path الذي تحذفه الخدمة. امتلاك حساب عادي ومعرفة مسار صورة يكفي لاستهدافها.

الإصلاح المقترح: ربط الملفات بسجلات ومالكين والتحقق من صلاحية تعديل السجل قبل الحذف أو الاستبدال.

### 06. تخزين وثائق هوية وعقود في مساحة عامة — عالٍ

الموقع: [app/Services/UserService.php:57](../app/Services/UserService.php#L57).

ملفات العقد والسجل الجنائي والهوية تمر عبر uploadImage إلى user-docs، وخدمة الصور تستخدم افتراضيًا قرص uploads الواقع داخل public. كذلك تحفظ AttachmentController المرفقات وإثباتات التبرع في public. الرابط لا يفرض صلاحية الوصول إلى صاحب الوثيقة.

الإصلاح المقترح: تخزين الوثائق الحساسة في قرص خاص وإتاحتها عبر تنزيل مصرح به أو روابط مؤقتة.

### 07. رفع SVG خام إلى أصل الموقع دون تنقية — عالٍ

الموقع: [app/Services/ImageUploadService.php:82](../app/Services/ImageUploadService.php#L82).

القاعدة any_image تقبل image/svg+xml وتستخدمها storeNews، والخدمة تحفظ الملف كما هو بامتداده داخل public/uploads. يمكن أن يحتوي SVG على سكربت ينفذ عند فتحه مباشرة، حسب سياسات المتصفح والخادم. لا يوجد تحويل أو sanitizer في هذا المسار.

الإصلاح المقترح: رفض SVG غير الموثوق أو تنقيته وتحويله إلى صورة raster، أو تقديم الملفات من أصل معزول.

### 08. سياسة صلاحيات تسمح افتراضيًا عند غياب الاسم أو الإجراء — عالٍ

الموقع: [app/Http/Middleware/RoleAccess.php:22](../app/Http/Middleware/RoleAccess.php#L22).

أحصيت 90 مسارًا تحت RoleAccess بلا اسم. الوسيط يتجاوز الفحص عند غياب الاسم أو suffix أو المورد المعروف. هذه ليست بالضرورة 90 ثغرة مستقلة؛ بعضها قد يملك فحصًا داخليًا، لكن السياسة المركزية لا تحميها. إسناد الأدوار مثال مثبت على أثرها.

الإصلاح المقترح: استخدام صلاحيات صريحة على المسارات، ورفض الحالات غير المعرّفة بدل السماح بها.

### 09. رموز الدخول القديمة مخزنة كنص واضح وبلا انتهاء افتراضي — عالٍ

الموقع: [app/Http/Controllers/AuthController.php:105](../app/Http/Controllers/AuthController.php#L105).

login وverifyOtp يحفظان الرمز نفسه في tokens دون expires_at. TokenAuth يقارن النص ولا يرفض غياب تاريخ الانتهاء. توجد آلية أخرى hashed في PersonalAccessToken، لكنها لا تحمي الرموز المستخدمة هنا.

الإصلاح المقترح: توحيد آلية الرموز، وتخزين hash فقط وتحديد انتهاء وتجديد وإلغاء واضح.

### 10. تغيير كلمة مرور الهاتف بلا تحقق من قوة القيمة الجديدة أو إبطال الرموز — عالٍ

الموقع: [app/Http/Controllers/Api/MobileApiController.php:703](../app/Http/Controllers/Api/MobileApiController.php#L703).

الدالة تعمل trim مباشرة ثم تحفظ Hash للقيمة الجديدة دون required أو min أو confirmed. بعد كلمة المرور الحالية الصحيحة يمكن حفظ كلمة مرور فارغة/ضعيفة، كما تبقى رموز tokens الحالية فعالة لأن TokenAuth لا يربطها بتغيير كلمة المرور.

الإصلاح المقترح: التحقق من المدخلات بقواعد واضحة وإبطال الجلسات/الرموز الأخرى عند التغيير.

### 11. تجاوز مرحلة إثبات ملكية الهاتف عند تسجيل الدخول بكلمة المرور — متوسط

الموقع: [app/Http/Controllers/AuthController.php:160](../app/Http/Controllers/AuthController.php#L160).

التسجيل يطلب تفعيل الهاتف برسالة للمستخدم لكنه ينشئ active=true، ثم login يصدر رمزًا دون تحقق من إكمال OTP. publicLogin في WebsiteApiController يتجاهل كذلك otp_verified رغم حفظه عند التسجيل.

الإصلاح المقترح: تحديد السياسة المطلوبة ثم تطبيق شرط التفعيل في جميع مداخل الدخول إذا كان إثبات الهاتف مطلوبًا.

### 12. تسجيل OTP وإعلان نجاح الإرسال رغم فشله — عالٍ

الموقع: [app/Http/Controllers/AuthController.php:68](../app/Http/Controllers/AuthController.php#L68).

عند غياب إعداد المزود أو فشله يولد الكود رمزًا محليًا ويعيد OTP sent successfully دون إرسال فعلي، ويسجل الرمز في السجلات. كذلك تُسجل استجابة المزود كاملة عند النجاح.

الإصلاح المقترح: عدم إعلان نجاح الإرسال عند الفشل، وإزالة الرموز واستجاباتها الحساسة من السجلات.

### 13. الحماية من تخمين OTP لا تربط عدد المحاولات بالرمز نفسه — متوسط

الموقع: [app/Providers/AppServiceProvider.php:105](../app/Providers/AppServiceProvider.php#L105).

الحد مربوط بالمعرف وعنوان IP. لا يوجد عداد محاولات فاشلة لكل رمز في verifyOtp، والرمز البديل أربعة أرقام وصلاحيته 15 دقيقة. تعدد عناوين IP يتجاوز الحد الخاص بالحساب.

الإصلاح المقترح: تحديد عدد محاولات لكل حساب/رمز مع إبطاله عند التجاوز، إلى جانب حد IP.

### 14. سجل التدقيق لا يحجب new_password وOTP — متوسط

الموقع: [app/Http/Middleware/AuditLogger.php:14](../app/Http/Middleware/AuditLogger.php#L14).

قائمة الحجب لا تشمل new_password أو otp أو otp_code. هذه فجوة في التصفية إذا مرت تلك الحقول عبر مسار يسجله الوسيط؛ مسار تغيير كلمة مرور الموبايل الحالي ليس عليه AuditLogger، فلا أنسب له تسريبًا مثبتًا عبر هذا الوسيط.

الإصلاح المقترح: توسيع الحجب وتقليل البيانات المسجلة إلى قائمة مسموحة لكل حدث حساس.

### 15. عدم توافق Donation مع WebDonation يعطل اعتماد التبرعات عبر API — عالٍ

الموقع: [app/Http/Controllers/Api/AdminWebsiteDonationController.php:94](../app/Http/Controllers/Api/AdminWebsiteDonationController.php#L94).

verifyDonation وrejectDonation يستقبلان Donation ويمررانه إلى واجهة وخدمة تتطلبان WebDonation. ينتج TypeError عند التنفيذ على سجل موجود. كذلك index وallDonations يقرآن donations بينما submitPublicDonation ينشئ web_donations، فتغيب التبرعات الجديدة عن هذه القوائم.

الإصلاح المقترح: توحيد النماذج والاستعلامات وربط المسارات على WebDonation وتحديث الاختبارات والوثائق.

### 16. اعتماد التبرع وتحديث حصيلة الهدف ليسا عملية ذرية — عالٍ

الموقع: [app/Features/WebsiteDonations/Services/WebsiteDonationService.php:92](../app/Features/WebsiteDonations/Services/WebsiteDonationService.php#L92).

تُحفظ verified ثم تُقرأ current_amount وتُزاد وتُحفظ دون transaction أو قفل. تعطل التحديث يترك حالة الاعتماد منفصلة عن الحصيلة، والطلبات المتزامنة قد تفقد زيادات أو تعيد احتساب العملية بحسب التداخل.

الإصلاح المقترح: معاملة وقفل/تحديث شرطي يضمن انتقال pending مرة واحدة، وزيادة ذرية للمبلغ.

### 17. رفض تبرع سبق اعتماده لا يعكس أثره على الحصيلة — عالٍ

الموقع: [app/Features/WebsiteDonations/Services/WebsiteDonationService.php:116](../app/Features/WebsiteDonations/Services/WebsiteDonationService.php#L116).

rejectDonation يقبل أي حالة ويحولها إلى rejected دون التحقق من pending ودون عكس زيادة current_amount السابقة.

الإصلاح المقترح: حصر انتقالات الحالة، وإضافة عملية عكس مالية صريحة عند الحاجة لإلغاء اعتماد سابق.

### 18. اعتماد طلب قيد محاسبي يفقد تفاصيل الأسطر — عالٍ

الموقع: [app/Http/Controllers/ChangeRequestWebController.php:76](../app/Http/Controllers/ChangeRequestWebController.php#L76).

الخدمة تحفظ lines داخل طلب المراجعة وتحتاج createLine عند التنفيذ المباشر. الاعتماد يستدعي modelClass::create أو update مباشرة دون معالجة خاصة لـJournalEntry. lines ليست fillable ولا توجد مراقبة نموذج تنشئها؛ القيد الجديد قد يعتمد بلا أسطر والتعديل لا يحدّث أسطره.

الإصلاح المقترح: إعادة استخدام خدمة تنفيذ القيد من مسار الاعتماد بدل CRUD عام على النموذج.

### 19. نوع الاستجابة لا يقبل طلب مراجعة عند تعديل القيد — عالٍ

الموقع: [app/Http/Controllers/JournalEntryController.php:36](../app/Http/Controllers/JournalEntryController.php#L36).

نوع الإرجاع JournalEntry|JsonResponse، بينما updateEntry يعيد ChangeRequest للمستخدم غير الإداري. يؤدي ذلك إلى TypeError بعد تسجيل طلب المراجعة، وcatch Exception لا يلتقط TypeError.

الإصلاح المقترح: تحويل ChangeRequest إلى JSON واضح بحالة 202 كما في InventoryTransactionController.

### 20. حماية القيود المقفلة لا يعاد فحصها عند الاعتماد — عالٍ

الموقع: [app/Http/Controllers/ChangeRequestWebController.php:127](../app/Http/Controllers/ChangeRequestWebController.php#L127).

JournalEntryService يفحص locked عند تقديم الطلب، لكن الاعتماد اللاحق يعدل/يحذف النموذج مباشرة. قد يقفل القيد بين الطلب والاعتماد ثم يُعدّل رغم ذلك.

الإصلاح المقترح: إعادة فحص الشروط داخل المعاملة وقت التنفيذ وقفل السجل المستهدف.

### 21. الإغلاق المالي لا يمنع إنشاء قيود جديدة بتاريخ مغلق — عالٍ

الموقع: [app/Services/JournalEntryService.php:30](../app/Services/JournalEntryService.php#L30).

الإغلاق يقفل السجلات الموجودة حتى تاريخ معين، بينما إنشاء قيد جديد لا يستعلم عن FinancialClosure ولا يفرض locked وفق الفترة. يمكن إدراج قيد بأثر رجعي بعد الإغلاق من هذا المسار.

الإصلاح المقترح: قاعدة موحدة تمنع الإنشاء والتعديل في الفترات المغلقة، داخل خدمة المجال.

### 22. سلوك إلغاء الإغلاق غير متسق — متوسط

الموقع: [app/Services/FinancialClosureService.php:53](../app/Services/FinancialClosureService.php#L53).

approveClosure يقلب approved لكنه يقفل فقط عند التحول إلى true ولا يفك القفل عند false. حذف الإغلاق عبر ChangeRequest يفك القيود لتاريخ مساوي فقط، بينما الإغلاق قفل جميع التواريخ السابقة؛ كما لا يراعي إغلاقًا آخر يغطي الفترة.

الإصلاح المقترح: تعريف سياسة إعادة فتح الفترة وإعادة حساب القفل بناء على جميع الإغلاقات المتبقية.

### 23. فحص المخزون منفصل عن التنفيذ ولا يعاد عند الاعتماد — عالٍ

الموقع: [app/Services/InventoryTransactionService.php:40](../app/Services/InventoryTransactionService.php#L40).

فحص الرصيد يسبق الإنشاء والقفل، وقد يُحفظ الطلب للمراجعة. عند اعتماده لاحقًا ينشئ ChangeRequestWebController الحركة مباشرة دون إعادة فحص المخزون. والتحويل يفحص الرصيد خارج المعاملة. يمكن صرف كمية لم تعد متاحة.

الإصلاح المقترح: قفل رصيد الصنف/المخزن وإعادة التحقق في المعاملة التي تنفذ الحركة، بما فيها الاعتماد.

### 24. تعديل حركة المخزون قد يجعل الرصيد سالبًا — عالٍ

الموقع: [app/Services/InventoryTransactionService.php:124](../app/Services/InventoryTransactionService.php#L124).

التعديل يمرر النوع والكمية مباشرة إلى repository دون حساب أثر فرق الكمية أو تغيير in إلى out. قواعد الطلب تتحقق من إيجابية الكمية فقط.

الإصلاح المقترح: التحقق من الرصيد الناتج ومن أثر تعديل طرفي التحويل قبل الحفظ والاعتماد.

### 25. طلبات سابقة توسم معتمدة رغم أن تنفيذها لم يحدث — متوسط

الموقع: [app/Services/ChangeRequestService.php:46](../app/Services/ChangeRequestService.php#L46).

عند تعديل الإدارة نموذجًا مباشرة، تحدث الخدمة كل طلبات pending لنفس النموذج إلى approved، بصرف النظر عن محتوياتها. سجل الاعتماد لا يثبت أن payload كل طلب نُفذ.

الإصلاح المقترح: استخدام حالة superseded/cancelled مع سبب واضح، أو اعتماد الطلب المنفذ فقط.

### 26. ابتلاع أخطاء العمليات المحاسبية التابعة عند الاعتماد — متوسط

الموقع: [app/Http/Controllers/ChangeRequestWebController.php:106](../app/Http/Controllers/ChangeRequestWebController.php#L106).

توجد catch Exception فارغة حول إنشاء قيود الرواتب وحركات الخزينة وغيرها. قد تنجح الموافقة بينما يفشل أثر مالي تابع دون تنبيه.

الإصلاح المقترح: إرجاع فشل العملية كاملة عندما يكون الأثر المالي مطلوبًا، أو تسجيل حالة فشل قابلة لإعادة المعالجة.

### 27. تعارض ثلاثة تعريفات لمسار storage — عالٍ

الموقع: [routes/web.php:85](../routes/web.php#L85).

المسار معرف مرتين في web.php، ويضيف Laravel تعريفًا ثالثًا بسبب filesystems.disks.local.serve=true. الفحص الفعلي لجدول المسارات أظهر أن التعريف الفعال من FilesystemServiceProvider، ويشير إلى local/private؛ ولذلك بديل الصور العامة لا يعمل كما توحي التعليقات عند وصول الطلب إلى Laravel بدل ملف static.

الإصلاح المقترح: فصل عنوان الملفات الخاصة الموقعة عن العامة وحذف التعريفات المتعارضة.

### 28. بديل storage اليدوي الأخير لا يحصر المسار داخل المجلد — خطر كامن عالٍ

الموقع: [routes/web.php:684](../routes/web.php#L684).

يصل path مباشرة إلى storage_path ثم File::get دون realpath وفحص احتواء. هذا مسار traversal خطر إذا صار هذا التعريف فعالًا. في الإعداد المفحوص Laravel يستبدله، لذلك لم أثبت قابلية الاستغلال عبر HTTP الحالية، ولم أقرأ ملفات حساسة لتجربته.

الإصلاح المقترح: حذف البديل غير الآمن، واستخدام قرص تخزين مضبوط النطاق وتحقق من احتواء المسار.

### 29. حذف الصورة السابقة قبل ضمان حفظ البديل — متوسط

الموقع: [app/Services/ImageUploadService.php:63](../app/Services/ImageUploadService.php#L63).

الخدمة تحذف oldPath أولًا ثم تحاول الحفظ. UploadsImages يفعل الشيء نفسه قبل استدعائها. فشل كتابة الملف الجديد يؤدي إلى فقد الصورة القديمة.

الإصلاح المقترح: حفظ البديل والتحقق من نجاحه ثم تحديث المرجع وحذف السابق.

### 30. رفع الصور قد يعلن النجاح رغم فشل الكتابة — متوسط

الموقع: [app/Services/ImageUploadService.php:97](../app/Services/ImageUploadService.php#L97).

نتيجة putFileAs مهملة، ثم ترجع savedPath المبنية مسبقًا. إعداد الأقراص throw=false، لذلك يمكن أن تعود false بلا استثناء وتصدر API نجاحًا لمسار غير موجود.

الإصلاح المقترح: فحص نتيجة التخزين والتأكد من الملف قبل إرجاع النجاح.

### 31. الضغط والتحويل المعلن للصور غير مطبق — متوسط

الموقع: [app/Services/ImageUploadService.php:54](../app/Services/ImageUploadService.php#L54).

الخدمة تحفظ الملف الأصلي؛ maxWidth وmaxHeight وquality لا تستخدم، ولا يوجد تحويل WebP رغم الوصف. صور كبيرة حتى 10MB قد تقدم مباشرة للمستخدمين.

الإصلاح المقترح: تنفيذ معالجة الصور فعليًا وحدود الأبعاد، أو تصحيح الواجهة والتوثيق إذا كان الحفظ الأصلي مقصودًا.

### 32. استخدام env داخل خدمات التشغيل يغير السلوك مع config cache — متوسط

الموقع: [app/Services/ImageUploadService.php:71](../app/Services/ImageUploadService.php#L71).

اختيار قرص الصور يقرأ env خارج config في أربعة مواضع، وMobileNews يفعل ذلك لـPRODUCTION_ASSET_URL. إذا كانت القيم في .env فقط، قد تختلف القيم بعد config:cache ويستخدم التطبيق القرص الافتراضي.

الإصلاح المقترح: نقل القيم إلى ملفات config وقراءتها عبر config().

### 33. N+1 في عرض حركات المخزون — متوسط

الموقع: [app/Services/InventoryTransactionService.php:25](../app/Services/InventoryTransactionService.php#L25).

يُنفذ استعلام ChangeRequest منفصل لكل حركة داخل الصفحة، إضافة إلى الاستعلامات الأصلية. صفحة 20 حركة تضيف 20 استعلامًا لهذا الغرض.

الإصلاح المقترح: جلب الطلبات المعلقة دفعة واحدة وربطها بمعرفات الحركات.

### 34. قائمة طلبات التطوع بلا حدود للنتائج — متوسط

الموقع: [app/Http/Controllers/Api/MobileApiController.php:245](../app/Http/Controllers/Api/MobileApiController.php#L245).

استدعاء get لكل السجلات يزيد زمن الاستجابة والذاكرة وحجم البيانات مع النمو؛ ويضاعف أثر مشكلة الصلاحيات المذكورة سابقًا.

الإصلاح المقترح: إضافة pagination وحد أقصى وحصر الحقول والفلاتر.

### 35. صلاحية بطاقة طلبات المراجعة تختلف عن صلاحية الصفحة — متوسط

الموقع: [resources/views/dashboard/partials/app_launcher.blade.php:1956](../resources/views/dashboard/partials/app_launcher.blade.php#L1956).

البطاقة تشترط change_requests.view بينما routes/web.php يشترط manage_change_requests، وهو المفتاح الموجود في SidebarPermissionsSeeder. مستخدم مخول قد لا يرى البطاقة، أو يراها ثم يتلقى 403 بحسب الأدوار.

الإصلاح المقترح: توحيد المفتاح في الواجهة والمسار والبذور.

### 36. بنية الاختبارات غير قابلة للتشغيل كما هي — عالٍ من ناحية ضمان الجودة

الموقع: [composer.json:5](../composer.json#L5).

توجد ثلاثة ملفات اختبارات فقط، دون tests/TestCase.php أو phpunit.xml أو autoload-dev أو تبعية PHPUnit/Pest. php artisan test أعاد أن الأمر غير معرّف. الاختبارات الحالية ترث Tests\TestCase غير الموجود.

الإصلاح المقترح: استكمال بيئة الاختبار وقاعدة مستقلة ثم اختبارات أمن الصلاحيات وسلامة الحركات والاعتماد.

### 37. اختبار التبرعات لا يطابق عقد الخدمة الحالي — متوسط

الموقع: [tests/Feature/PublicWebsiteDonationTest.php:28](../tests/Feature/PublicWebsiteDonationTest.php#L28).

الاختبار يرسل type ولا يرسل category_id، بينما WebsiteDonationService يقرأ category_id ويستخدم slug من السجل دون fallback. سيحتاج الاختبار إلى تحديث حتى بعد إصلاح مشغل الاختبارات.

الإصلاح المقترح: إنشاء DonationCategory في بيانات الاختبار وإرسال category_id الصحيح.

### 38. اعتماد MySQL فقط — مغلقة بقرار نطاق المشروع

بناء على طلب صاحب المشروع، MySQL هو محرك قاعدة البيانات المعتمد للتطبيق والاختبارات. عدم توافق الترحيلات مع SQLite ليس عيبًا مطلوب الإصلاح، ولا يوجد التزام بدعمه. أزيل اتصال SQLite وبقية اتصالات SQL غير المستخدمة من إعداد التطبيق، وأصبح MySQL الخيار الافتراضي للتطبيق والطوابير، وأزيل فرع SQLite من AppServiceProvider. أوامر MySQL في الترحيلات بقيت كما هي.

تجربة SQLite الواردة في وصف الفحص تاريخية فقط، ولم تمس قاعدة بيانات المشروع. اختبارات التكامل اللاحقة يجب أن تستخدم قاعدة MySQL اختبارية منفصلة.

### 39. ترتيب rollback لتقييمات الموقع غير صالح — متوسط

الموقع: [database/migrations/2026_02_14_113000_add_status_to_web_testimonials_table.php:27](../database/migrations/2026_02_14_113000_add_status_to_web_testimonials_table.php#L27).

ترحيل إضافة status أقدم من إنشاء web_testimonials بتاريخ 16 فبراير. up يتجاوز الجدول الغائب، لكن down لا يتحقق من وجوده؛ عند rollback لكليهما يُحذف الجدول أولًا ثم يحاول الترحيل الأقدم إسقاط عمود من جدول غير موجود.

الإصلاح المقترح: تصحيح ترتيب المسؤولية بين الترحيلين وجعل down يعكس ما يمكن أن ينفذه up بأمان.

### 40. إعداد التطوير الحالي غير مناسب إذا نُقل للإنتاج كما هو — متوسط

الموقع: [config/app.php:42](../config/app.php#L42).

قراءة مفاتيح غير سرية فقط أظهرت APP_ENV=local وAPP_DEBUG=true في .env المحلي. هذا طبيعي محليًا وليس دليلًا على إعداد الخادم الحقيقي. نشره كما هو قد يكشف تفاصيل الأخطاء ويغير سلوك HTTPS والكوكيز المرتبط ببيئة production.

الإصلاح المقترح: توثيق إعداد الإنتاج والتحقق من APP_DEBUG=false وبيئة production على الخادم دون نسخ ملف التطوير.

### 41. تضخم المتحكمات وتكرار مسارات منطق المجال — متوسط

الموقع: [app/Http/Controllers/Api/WebsiteApiController.php:29](../app/Http/Controllers/Api/WebsiteApiController.php#L29).

WebsiteApiController نحو 3270 سطرًا، MobileApiController نحو 1541، وChangeRequestWebController نحو 674. يوجد تداخل بين Web وAPI واعتماد التغييرات مع تكرار منطق النماذج. هذا عامل صيانة ثبت أثره في اختلاف نماذج التبرع وتجاوز خدمات القيود، وليس عطلًا مستقلًا لكل ملف كبير.

الإصلاح المقترح: تقسيم المتحكمات حسب الوظيفة وتوحيد العمليات الحساسة في خدمات قابلة للاختبار.

## تدقيق التبعيات

نفذت composer audit وnpm audit على التبعيات الحالية. هذه نتائج مطابقة إصدارات، وليست إثباتًا بأن كل تنبيه قابل للاستغلال من هذا التطبيق. قد تتكرر المشكلة نفسها عبر مصادر/معرّفات مختلفة.

- Composer أعاد 46 سجل تنبيه موزعة على 13 حزمة؛ القائمة الكاملة أدناه.
- npm أعاد 3 حزم مصنفة high: nanoid وpostcss وsource-map-js. هذه ضمن سلسلة أدوات البناء؛ لا يصح وصفها تلقائيًا كثغرات إنتاج قابلة للاستغلال عبر الموقع.
- يحتفظ التقرير بالمخرجات الأصلية في composer-audit.json وnpm-audit.json داخل هذا المجلد، مع مصادر كل تنبيه وشروط الإصدارات.

### guzzlehttp/guzzle — 7.10.0

- [Guzzle: Noncanonical host can bypass host-based checks](https://github.com/advisories/GHSA-v5mv-p594-2x33) — high — PKSA-gcrk-3vtt-1r14
- [Guzzle: Noncanonical cookie domain keeps subdomain scope](https://github.com/advisories/GHSA-f7vp-7xgx-4w4r) — medium — PKSA-cnw1-2ytm-cgr8
- [Guzzle: URI fragments disclosed in redirect Referer headers](https://github.com/advisories/GHSA-h95v-h523-3mw8) — medium — PKSA-fy2t-3c5f-827y
- [Guzzle: Host-only cookie scope is not preserved](https://github.com/advisories/GHSA-wm3w-8rrp-j577) — medium — PKSA-qxvb-2bpp-dnk6
- [Guzzle: Unbounded response cookies risk denial of service](https://github.com/advisories/GHSA-f283-ghqc-fg79) — medium — PKSA-bbs6-q5q9-f3t4
- [Guzzle: Cookie Disclosure and Injection via IP-Address Domains](https://github.com/advisories/GHSA-g446-98w2-8p5w) — medium — PKSA-bcdd-5xc7-gwfb
- [Guzzle: Proxy-Authorization headers can be sent to origin servers](https://github.com/advisories/GHSA-94pj-82f3-465w) — medium — PKSA-pwsk-hy21-4gby
- [Dot-only cookie domains match all hosts](https://github.com/guzzle/guzzle/security/advisories/GHSA-cwxw-98qj-8qjx) — medium — PKSA-93qv-9n9h-6k6p
- [Silent HTTPS proxy downgrade to cleartext](https://github.com/guzzle/guzzle/security/advisories/GHSA-wpwq-4j6v-78m3) — medium — PKSA-k22t-f949-t9g6

### guzzlehttp/psr7 — 2.8.0

- [guzzlehttp/psr7: Host Confusion via Weak URI Host Validation](https://github.com/advisories/GHSA-c2w2-prh8-qm98) — medium — PKSA-vznr-tgp9-fd7d
- [CRLF injection in HTTP start-line serialization](https://github.com/guzzle/psr7/security/advisories/GHSA-vm85-hxw5-5432) — medium — PKSA-7qs6-zvnz-h66r
- [CRLF injection via URI host component](https://github.com/guzzle/psr7/security/advisories/GHSA-hq7v-mx3g-29hw) — medium — PKSA-gm5x-j3mz-71n9
- [Host confusion via authority reinterpretation](https://github.com/guzzle/psr7/security/advisories/GHSA-34xg-wgjx-8xph) — medium — PKSA-jj5t-2zs1-dcfm

### laravel/framework — v11.46.2

- [Laravel: XSS in Debug Page Information](https://github.com/advisories/GHSA-jh5r-qr3c-85q8) — low — PKSA-d5tc-s1qs-h781
- [Laravel Framework: Temporary Signed URL Path Confusion](https://github.com/advisories/GHSA-crmm-hgp2-wgrp) — medium — PKSA-m5cs-t1y6-qpcs
- [Laravel Framework: CRLF injection in default email rule ](https://github.com/advisories/GHSA-5vg9-5847-vvmq) — high — PKSA-3r5d-mb8f-1qw9
- [Laravel CRLF injection in default email rule](https://github.com/laravel/framework/security/advisories/GHSA-5vg9-5847-vvmq) — غير محددة — PKSA-mdq4-51ck-6kdq

### league/commonmark — 2.7.1

- [league/commonmark: DisallowedRawHtml bypassed when a disallowed tag name ends the raw-HTML literal](https://github.com/advisories/GHSA-97jj-33gv-5xf9) — medium — PKSA-m2dq-1fhr-29b1
- [league/commonmark: Quadratic-time denial of service in the GitHub Flavored Markdown Table extension block-start scan](https://github.com/advisories/GHSA-3q6v-r5mr-hxv8) — high — PKSA-m4t9-vsgq-8khn
- [league/commonmark: Denial of service via distinctly-named attributes in the Attributes extension](https://github.com/advisories/GHSA-8rr7-cvq3-gmfh) — high — PKSA-zyf5-hrxv-hrd7
- [league/commonmark: Denial of service in the SmartPunct and Attributes extensions](https://github.com/advisories/GHSA-jjv6-8j6v-6j52) — high — PKSA-nv44-1b4d-6gjg
- [league/commonmark XSS: `on*` event-handler filter in `AttributesExtension` bypassed with a U+000C form feed](https://github.com/advisories/GHSA-f8fg-pg57-v4j8) — high — PKSA-kr3s-894t-g5w2
- [league/commonmark: Denial of service via crafted code fences, reference links, and emphasis delimiters](https://github.com/advisories/GHSA-j8pm-gj4c-rq4x) — high — PKSA-9q1p-3s19-bp1q
- [league/commonmark: Denial of service via deeply nested XML output](https://github.com/advisories/GHSA-mj63-m3rc-8ppr) — medium — PKSA-5mzr-szzf-z6cn
- [league/commonmark: Denial of service via colliding heading slugs](https://github.com/advisories/GHSA-mh25-x5hq-wrqp) — high — PKSA-cqd6-fg4n-nxpf
- [league/commonmark:  Denial of service via duplicate footnote definitions](https://github.com/advisories/GHSA-jfm3-95jq-q3rf) — high — PKSA-1q6p-sqkj-8mmj
- [league/commonmark: Denial of service via adjacent inline attribute blocks](https://github.com/advisories/GHSA-g2gp-3wwq-f4ph) — high — PKSA-mc58-w91n-f5gv
- [league/commonmark: Quadratic-time denial of service when parsing crafted Markdown](https://github.com/advisories/GHSA-2q4p-g7hv-5rgv) — high — PKSA-t21r-vtr5-3mdz
- [league/commonmark: AttributesExtension href/src unsafe-link filter bypass via embedded control bytes](https://github.com/advisories/GHSA-29pj-957v-52mc) — medium — PKSA-scnn-p8mm-jbft
- [league/commonmark has an embed extension allowed_domains bypass](https://github.com/advisories/GHSA-hh8v-hgvp-g3f5) — medium — PKSA-21fb-n1x5-5nf7
- [CommonMark has DisallowedRawHtml extension bypass via whitespace in HTML tag names](https://github.com/advisories/GHSA-4v6x-c7xx-hw9f) — medium — PKSA-2cx9-ynrq-qdk3

### league/flysystem — 3.30.2

- [Flysystem: WhitespacePathNormalizer's control-character (CorruptedPathDetected) check is bypassed by malformed UTF-8 in the path, affecting every adapter](https://github.com/advisories/GHSA-cxf4-7mrp-vvpr) — low — PKSA-w9tt-7782-78jx

### psy/psysh — v0.12.14

- [PsySH has Local Privilege Escalation via CWD .psysh.php auto-load](https://github.com/advisories/GHSA-4486-gxhx-5mg7) — medium — PKSA-4s4z-t146-6123

### symfony/http-foundation — v7.3.7

- [CVE-2026-108082: ReDoS in HeaderUtils::split() Reachable Through the Cache-Control Request Header](https://symfony.com/cve-2026-108082) — غير محددة — PKSA-xmwy-rmx5-m7h4
- [CVE-2026-48736: IpUtils::PRIVATE_SUBNETS Omits IPv6 Transition Forms (6to4, NAT64, Teredo, IPv4-compatible): SSRF Bypass in NoPrivateNetworkHttpClient](https://symfony.com/cve-2026-48736) — medium — PKSA-y6py-qpv1-h52p

### symfony/mailer — v7.3.5

- [CVE-2026-45068: Argument Injection in SendmailTransport via Dash-Prefixed Recipient Address](https://symfony.com/cve-2026-45068) — medium — PKSA-28rh-rzzn-djk4

### symfony/mime — v7.3.4

- [CVE-2026-45070: Email Header Injection via Non-Token Characters in Mime Parameter Names](https://symfony.com/cve-2026-45070) — medium — PKSA-wtxr-p26d-nn42
- [CVE-2026-45067: Email Header / SMTP Command Injection via CRLF in Symfony\Component\Mime\Address](https://symfony.com/cve-2026-45067) — high — PKSA-2n2k-66v2-bwg3

### symfony/polyfill-intl-idn — v1.33.0

- [CVE-2026-46644: symfony/polyfill-intl-idn accepts xn-- labels whose Punycode payload decodes to ASCII-only: insecure equivalence](https://symfony.com/cve-2026-46644) — low — PKSA-dwsq-ppd2-mb1x

### symfony/process — v7.3.4

- [Symfony's incorrect argument escaping under MSYS2/Git Bash can lead to destructive file operations on Windows](https://github.com/advisories/GHSA-r39x-jcww-82v6) — medium — PKSA-rkkf-636k-qjb3

### symfony/routing — v7.3.6

- [CVE-2026-48784: UrlGenerator Dot-Segment Encoding Skips Every Other Chained `../` or `./` → Generated URL Collapses Off-Route Under RFC 3986 Normalization](https://symfony.com/cve-2026-48784) — medium — PKSA-bf7t-jnpz-492k
- [CVE-2026-45065: UrlGenerator Route-Requirement Bypass via Unanchored Regex Alternation → Off-Site //host URL Injection](https://symfony.com/cve-2026-45065) — medium — PKSA-yc7t-91v9-99xs

### symfony/yaml — v7.4.1

- [CVE-2026-109413: Unbounded Recursion in the YAML Inline Lexer Lets a Single Document Crash the Process](https://symfony.com/cve-2026-109413) — غير محددة — PKSA-xz3r-9gp4-4m1z
- [CVE-2026-45304: YAML Parser Exponential Memory Allocation via Recursive Collection-Alias Expansion ("Billion Laughs")](https://symfony.com/cve-2026-45304) — low — PKSA-v5yj-8nmz-sk2q
- [CVE-2026-45305: YAML Parser ReDoS via Catastrophic Backtracking in Parser::cleanup() Regex](https://symfony.com/cve-2026-45305) — low — PKSA-ft77-7h5f-p3r6
- [CVE-2026-45133: YAML Parser Stack Exhaustion via Unbounded Recursion in Nested Blocks, Sequences, and Mappings](https://symfony.com/cve-2026-45133) — low — PKSA-b14r-zh1d-vdrc

## ترتيب المعالجة

1. إغلاق مسارات الإدارة العامة وتصعيد الأدوار ووظائف الموظفين، ومعالجة المفتاح الخاص إذا كان مستخدمًا.
2. عزل الوثائق الحساسة وتأمين حذف ورفع الملفات والرموز وتغيير كلمات المرور.
3. إصلاح اعتماد القيود والتبرعات والمخزون وتوحيد تنفيذ طلبات المراجعة مع الخدمات.
4. إصلاح تعارض التخزين وبيئة الاختبارات والترحيلات؛ ثم تحديث التبعيات بعد اختبارات توافق.
5. تحسين الاستعلامات وحجم الصور وتوحيد الصلاحيات وتقسيم المتحكمات الكبيرة.

## حدود النتيجة وما لم يُحسم

لم أفحص إعداد Nginx/Apache أو TLS أو النسخ الاحتياطي أو صلاحيات قاعدة الإنتاج أو بياناتها أو سلوك واجهات كل دور داخل متصفح. لم أختبر كل رحلة عمل أو أجْرِ اختبار ضغط/تزامن. لذلك هذه القائمة الكاملة للمشكلات التي أثبتتها أو ميزت شروطها خلال هذه المراجعة، وليست ادعاءً بإحصاء كل عيب ممكن في المشروع. يلزم تشغيل اختبارات تكامل على MySQL ببيانات تجريبية لتأكيد المخرجات النهائية والإصلاحات. وجود ملفات وظائف كثيرة أو نجاح البناء لا يعوض اختبار الصلاحيات وسلامة البيانات.
