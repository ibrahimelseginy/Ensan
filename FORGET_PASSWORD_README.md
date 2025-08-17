# صفحة نسيان كلمة المرور - Forget Password Page

## الوصف
صفحة متكاملة لنسيان كلمة المرور مع دعم اللغتين العربية والإنجليزية، مرتبطة بـ Cubit لإدارة الحالة.

## المميزات
- ✅ دعم اللغتين العربية والإنجليزية
- ✅ تحقق من صحة البريد الإلكتروني
- ✅ رسائل خطأ ونجاح واضحة
- ✅ حالة التحميل أثناء الإرسال
- ✅ تصميم متجاوب وجميل
- ✅ مرتبط بـ Cubit لإدارة الحالة

## الملفات المطلوبة

### 1. ForgetPasswordCubit
```dart
lib/auth/cubit/forget_password_cubit.dart
```
- يدير حالة الصفحة
- يتعامل مع التحقق من صحة البريد الإلكتروني
- يحاكي استدعاء API لإرسال رابط إعادة تعيين كلمة المرور

### 2. صفحة نسيان كلمة المرور
```dart
lib/auth/forget_password.dart
```
- واجهة المستخدم الرئيسية
- مرتبطة بـ Cubit
- تدعم تغيير اللغة

## كيفية الاستخدام

### 1. إضافة الصفحة إلى Router
```dart
// في app_router.dart
case ForgetPassword.routeName:
  return MaterialPageRoute(
    builder: (context) => const ForgetPassword(),
  );
```

### 2. التنقل إلى الصفحة
```dart
Navigator.of(context).pushNamed(ForgetPassword.routeName);
```

### 3. ربط API حقيقي
في `ForgetPasswordCubit.onResetPasswordSubmitted()`:
```dart
// استبدل المحاكاة بـ API حقيقي
final response = await authService.sendPasswordResetEmail(email);
if (response.success) {
  // نجح الإرسال
} else {
  // فشل الإرسال
}
```

## المكونات المستخدمة
- `CustomText`: للنصوص مع دعم اللغتين
- `CustomTextField`: لحقل إدخال البريد الإلكتروني
- `CustomButton`: لزر إرسال رابط إعادة التعيين

## الحالات المدعومة
1. **الحالة الأولية**: عرض النموذج
2. **التحميل**: أثناء إرسال الطلب
3. **النجاح**: بعد إرسال رابط إعادة التعيين
4. **الخطأ**: في حالة فشل الإرسال

## التخصيص
يمكن تخصيص:
- الألوان من خلال `AppColors`
- النصوص والرسائل
- تصميم الواجهة
- منطق التحقق من صحة البيانات

## ملاحظات
- الصفحة تستخدم `ChangeNotifierProvider` مع `Consumer`
- تدعم التنقل العكسي
- تتحقق من صحة البريد الإلكتروني في الوقت الفعلي
