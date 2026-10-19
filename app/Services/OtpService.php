<?php

namespace App\Services;

use App\Models\User;
use App\Models\WebDonor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class OtpService
{
    public function send(string $phone): string
    {
        $token = config('services.beon.token');
        if (!$token) {
            throw new HttpException(503, 'خدمة رسائل التحقق غير متاحة حالياً.');
        }
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '01') && strlen($phone) === 11) $phone = '2'.$phone;
        try {
            $response = Http::withHeaders(['beon-token' => $token])->timeout(15)
                ->post('https://v3.api.beon.chat/api/v3/messages/otp', [
                    'recipient' => $phone, 'phoneNumber' => $phone, 'phone' => $phone,
                    'name' => 'Ensan', 'channel' => 'sms', 'type' => 'sms',
                ]);
            $otp = $response->json('data.otp');
            if ($response->successful() && is_scalar($otp) && preg_match('/^\d{4,6}$/', (string) $otp)) {
                return (string) $otp;
            }
        } catch (\Exception $e) {
            // Do not log provider payloads, credentials, or verification codes.
        }
        throw new HttpException(503, 'تعذر إرسال رمز التحقق. يرجى المحاولة لاحقاً.');
    }

    public function consume(string $modelClass, string $phone, string $otp): User|WebDonor|null
    {
        if (!in_array($modelClass, [User::class, WebDonor::class], true)) {
            throw new \InvalidArgumentException('Unsupported OTP account type.');
        }
        $key = 'otp-verify:'.hash('sha256', $modelClass.'|'.$phone);
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw new HttpException(429, 'تم تجاوز محاولات التحقق. يرجى الانتظار قبل المحاولة مجدداً.');
        }
        RateLimiter::hit($key, 900);
        $user = DB::transaction(function () use ($modelClass, $phone, $otp) {
            $user = $modelClass::where('phone', $phone)->lockForUpdate()->first();
            if (!$user || !$user->active || !$user->otp_code || !$user->otp_expires_at ||
                $user->otp_expires_at->isPast() || !hash_equals((string) $user->otp_code, $otp)) {
                return null;
            }
            $user->otp_code = null;
            $user->otp_expires_at = null;
            if ($user instanceof WebDonor) $user->otp_verified = true;
            else $user->phone_verified_at = now();
            $user->save();
            return $user;
        });
        if ($user) RateLimiter::clear($key);
        return $user;
    }
}
