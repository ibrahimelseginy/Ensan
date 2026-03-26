<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * تسجيل حركة إرسال الرسالة في قاعدة البيانات
     */
    public static function log($type, $recipient, $message, $provider, $status = 'sent', $response = null, $userId = null)
    {
        return NotificationLog::create([
            'type' => $type,
            'phone' => $type === 'sms' ? $recipient : null,
            'email' => $type === 'email' ? $recipient : null,
            'message' => $message,
            'provider' => $provider,
            'status' => $status,
            'provider_response' => $response,
            'user_id' => $userId,
        ]);
    }

    /**
     * إرسال رسالة نصية SMS وتوثيقها
     */
    public static function sendSms($phone, $message, $userId = null)
    {
        $provider = 'twilio'; // يمكنك تغييرها بناءً على مزود الخدمة الخاص بك مثل VictoryLink أو وغيرها
        $status = 'sent';
        $response = null;

        try {
            // ضع كود الـ API الخاص بـ SMS الخاص بك هنا
            // مثال:
            // $client = new \GuzzleHttp\Client();
            // $res = $client->post('https://api.smsprovider.com/send', ['json' => ['to' => $phone, 'text' => $message]]);
            // $response = json_decode($res->getBody(), true);
            
            // تمت محاكاة نجاح الإرسال للوقت الحالي
            $status = 'delivered'; 

        } catch (\Exception $e) {
            $status = 'failed';
            $response = ['error' => $e->getMessage()];
            Log::error("SMS Sending failed to {$phone}: " . $e->getMessage());
        }

        // تسجيل العملية في جدول الـ notification_logs
        self::log('sms', $phone, $message, $provider, $status, $response, $userId);

        return $status === 'delivered' || $status === 'sent';
    }

    /**
     * توثيق رسالة بريد إلكتروني Email
     * يمكن استخدام هذا إذا كنت لا تعتمد على نظام أحداث الـ Mail في لارافل
     */
    public static function logEmail($email, $subject, $userId = null)
    {
        return self::log('email', $email, $subject, config('mail.default'), 'sent', null, $userId);
    }
}
