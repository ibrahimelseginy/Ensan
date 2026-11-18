<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تعيين كلمة المرور - مؤسسة إنسان</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 25px; direction: rtl; text-align: right;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
    
    {{-- Header --}}
    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 25px; text-align: center; color: #ffffff;">
      <h2 style="margin: 0; font-size: 22px; font-weight: bold;">مؤسسة إنسان الخيرية والتنموية</h2>
      <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">نظام إدارة الهوية والأمان السيبراني</p>
    </div>

    {{-- Body --}}
    <div style="padding: 30px 25px;">
      <p style="font-size: 16px; color: #1e293b; margin-top: 0;">
        مرحباً <strong>{{ $user->name }}</strong>،
      </p>
      <p style="font-size: 14px; color: #475569; line-height: 1.7;">
        تلقينا طلباً لاسترداد أو تعيين كلمة المرور الخاصة بحسابك في نظام مؤسسة إنسان. لتسهيل الأمر عليك، يمكنك استخدام أحد الخيارين التاليين:
      </p>

      {{-- الخيار الثاني أولاً: كود التحقق السريع --}}
      <div style="background: #f0fdf4; border: 1px dashed #34d399; border-radius: 10px; padding: 20px; text-align: center; margin: 25px 0;">
        <span style="font-size: 13px; color: #047857; font-weight: bold; display: block; margin-bottom: 8px;">
          كود التحقق الرقمي السريع (OTP):
        </span>
        <div style="font-family: monospace; font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #059669; padding: 5px 0;">
          {{ $otpCode }}
        </div>
        <span style="font-size: 12px; color: #64748b; display: block; margin-top: 6px;">
          صالح للاستخدام لمدة <strong>10 دقائق</strong> فقط (حد أقصى 3 محاولات)
        </span>
      </div>

      <div style="text-align: center; margin: 20px 0; color: #94a3b8; font-size: 13px;">
        — أو بالضغط المباشر على الرابط التالي —
      </div>

      {{-- الخيار الأول: الرابط المباشر --}}
      <div style="text-align: center; margin: 25px 0;">
        <a href="{{ $resetUrl }}" style="background-color: #10b981; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 15px; display: inline-block; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
          تعيين كلمة المرور فوراً بضغطة واحدة
        </a>
        <p style="font-size: 12px; color: #64748b; margin-top: 10px;">
          صلاحية الرابط: <strong>15 دقيقة</strong> للاستخدام لمرة واحدة فقط
        </p>
      </div>

      {{-- متطلبات كلمة المرور --}}
      <div style="background: #f8fafc; border-radius: 8px; padding: 15px; margin-top: 25px; border-right: 4px solid #10b981;">
        <span style="font-size: 13px; font-weight: bold; color: #334155; display: block; margin-bottom: 5px;">
          شروط كلمة المرور الجديدة المطلوبة:
        </span>
        <ul style="font-size: 12px; color: #475569; margin: 0; padding-right: 20px; line-height: 1.6;">
          <li>طول لا يقل عن 10 خانات.</li>
          <li>تحتوي على حروف كبيرة [A-Z] وحروف صغيرة [a-z].</li>
          <li>تحتوي على أرقام [0-9] ورموز خاصة مثل (!@#$%^&*).</li>
          <li>عدم استخدام كلمات متوقعة أو اسمك أو اسم المؤسسة.</li>
        </ul>
      </div>

      <p style="font-size: 12px; color: #94a3b8; margin-top: 30px; text-align: center;">
        إذا لم تكن أنت من طلب تعيين كلمة المرور، فيرجى تجاهل هذا البريد؛ حسابك بأمان تام ولن يتم اتخاذ أي إجراء دونه.
      </p>
    </div>

    {{-- Footer --}}
    <div style="background: #f1f5f9; padding: 15px 25px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
      © {{ date('Y') }} مؤسسة إنسان الخيرية والتنموية — جميع الحقوق محفوظة
    </div>
  </div>
</body>
</html>
