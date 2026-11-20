<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول عبر البريد الإلكتروني - مؤسسة إنسان</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction:rtl;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f7f6; padding:30px 15px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width:600px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 6px 24px rgba(0,0,0,0.06); border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%); padding:28px 30px; text-align:center; color:#ffffff;">
                            <h1 style="margin:0; font-size:22px; font-weight:700;">مؤسسة إنسان الخيرية</h1>
                            <p style="margin:6px 0 0; font-size:13px; opacity:0.9;">تسجيل الدخول السريع عبر البريد الإلكتروني</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 30px; color:#1e293b; font-size:15px; line-height:1.7;">
                            <p style="margin-top:0;">مرحباً <strong>{{ $user->name }}</strong>،</p>
                            <p>تم طلب تسجيل الدخول إلى حسابك في نظام <strong>مؤسسة إنسان</strong> عبر البريد الإلكتروني. يمكنك الدخول بإحدى الطريقتين التاليتين:</p>

                            <div style="background-color:#ecfdf5; border:2px dashed #10b981; border-radius:12px; padding:20px; text-align:center; margin:24px 0;">
                                <div style="font-size:13px; color:#065f46; font-weight:600; margin-bottom:8px;">كود التحقق الرقمي لتسجيل الدخول (صالح لمدة {{ $expiresMinutes }} دقائق)</div>
                                <div style="font-size:34px; font-weight:800; letter-spacing:8px; color:#047857; font-family:monospace;">{{ $otpCode }}</div>
                            </div>

                            <div style="text-align:center; margin:28px 0;">
                                <a href="{{ $magicUrl }}" style="display:inline-block; background-color:#10b981; color:#ffffff; text-decoration:none; padding:14px 32px; border-radius:10px; font-weight:700; font-size:15px; box-shadow:0 4px 12px rgba(16,185,129,0.25);">
                                    تسجيل الدخول المباشر بضغطة واحدة 🔑
                                </a>
                            </div>

                            <div style="background-color:#fffbeb; border-right:4px solid #f59e0b; padding:12px 16px; border-radius:6px; font-size:13px; color:#92400e; margin-top:24px;">
                                <strong>تنبيه أمني:</strong> لا تشارك هذا الكود أو الرابط مع أي شخص. إذا لم تطلب تسجيل الدخول، يمكنك تجاهل هذه الرسالة بأمان.
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#f8fafc; padding:18px 30px; text-align:center; font-size:12px; color:#64748b; border-top:1px solid #e2e8f0;">
                            هذه رسالة آلية من نظام إدارة مؤسسة إنسان الخيرية &copy; {{ date('Y') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

