<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use App\Models\PasswordPolicy;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use Closure;

class StrictPasswordRule implements ValidationRule
{
    protected ?User $user;
    protected ?PasswordPolicy $policy;

    public function __construct(?User $user = null, ?PasswordPolicy $policy = null)
    {
        $this->user = $user;
        $this->policy = $policy ?? ($user ? $user->policy : PasswordPolicy::forTier('tier2_field'));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = (string) $value;
        $minLength = $this->policy ? $this->policy->min_length : 10;

        // 1. Minimum Length Check
        if (mb_strlen($password) < $minLength) {
            $fail("يجب ألا يقل طول كلمة المرور عن {$minLength} خانات.");
            return;
        }

        // 2. Strict Character Composition
        // Lowercase
        if (!preg_match('/[a-z]/', $password)) {
            $fail('يجب أن تحتوي كلمة المرور على حرف صغير واحد على الأقل [a-z].');
            return;
        }

        // Uppercase
        if (!preg_match('/[A-Z]/', $password)) {
            $fail('يجب أن تحتوي كلمة المرور على حرف كبير واحد على الأقل [A-Z].');
            return;
        }

        // Numbers
        if (!preg_match('/[0-9]/', $password)) {
            $fail('يجب أن تحتوي كلمة المرور على رقم واحد على الأقل [0-9].');
            return;
        }

        // Special Characters
        if (!preg_match('/[!@#$%^&*()_+=\-\[\]{}|:;\"\'<>,.?\/~`]/', $password)) {
            $fail('يجب أن تحتوي كلمة المرور على رمز خاص واحد على الأقل مثل: !@#$%^&*');
            return;
        }

        // 3. Blacklist & Predictable Pattern Checks
        $lowerPassword = mb_strtolower($password);

        // Common company/domain blacklist
        $forbiddenKeywords = ['ensan', 'insan', 'انسان', 'إنسان', 'password', '123456', 'qwerty', 'admin', 'root'];
        foreach ($forbiddenKeywords as $badWord) {
            if (str_contains($lowerPassword, $badWord)) {
                $fail("كلمة المرور تحتوي على كلمات أو أنماط متوقعة محظورة أمنياً ('{$badWord}').");
                return;
            }
        }

        // If user context is available, block personal info
        if ($this->user) {
            // Check against user code
            if (!empty($this->user->code) && str_contains($lowerPassword, mb_strtolower($this->user->code))) {
                $fail('لا يجوز أن تحتوي كلمة المرور على كود الموظف الخاص بك.');
                return;
            }

            // Check against phone
            if (!empty($this->user->phone)) {
                $cleanPhone = preg_replace('/\D/', '', $this->user->phone);
                if (strlen($cleanPhone) >= 6 && str_contains($password, $cleanPhone)) {
                    $fail('لا يجوز أن تحتوي كلمة المرور على رقم هاتفك.');
                    return;
                }
            }

            // Check against email username
            if (!empty($this->user->email)) {
                $emailUser = explode('@', $this->user->email)[0] ?? '';
                if (mb_strlen($emailUser) >= 3 && str_contains($lowerPassword, mb_strtolower($emailUser))) {
                    $fail('لا يجوز أن تحتوي كلمة المرور على اسم بريدك الإلكتروني.');
                    return;
                }
            }

            // Check against user name parts
            if (!empty($this->user->name)) {
                $nameParts = preg_split('/\s+/', $this->user->name);
                foreach ($nameParts as $part) {
                    if (mb_strlen($part) >= 3 && str_contains($lowerPassword, mb_strtolower($part))) {
                        $fail("لا يجوز أن تحتوي كلمة المرور على جزء من اسمك ('{$part}').");
                        return;
                    }
                }
            }

            // 4. Anti-Recycling: Check against recent password history
            $historyLimit = $this->policy ? $this->policy->history_limit : 6;
            $histories = $this->user->passwordHistories()->take($historyLimit)->get();

            // Also check current password
            if ($this->user->password && Hash::check($password, $this->user->password)) {
                $fail('لا يمكن استخدام كلمة المرور الحالية. يرجى اختيار كلمة مرور جديدة تماماً.');
                return;
            }

            foreach ($histories as $h) {
                if (Hash::check($password, $h->password_hash)) {
                    $fail("لا يمكنك إعادة استخدام أي كلمة مرور من آخر {$historyLimit} كلمات مرور سابقة لحسابك.");
                    return;
                }
            }
        }
    }
}
