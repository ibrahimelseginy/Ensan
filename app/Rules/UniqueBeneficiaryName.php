<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Beneficiary;
use App\Models\ChangeRequest;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class UniqueBeneficiaryName implements ValidationRule
{
    public function __construct(
        private readonly ?int $ignoreBeneficiaryId = null,
        private readonly ?string $originalName = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalizedName = self::normalize((string) $value);

        if ($normalizedName === '') {
            return;
        }

        // Editing other fields on an old record must remain possible, even if
        // legacy data already contains another record with the same name.
        if ($this->originalName !== null
            && $normalizedName === self::normalize($this->originalName)) {
            return;
        }

        if (self::existingBeneficiaryId($normalizedName, $this->ignoreBeneficiaryId) !== null
            || $this->hasPendingRequestWithName($normalizedName)) {
            $fail('يوجد مستفيد مسجل أو طلب إضافة معلق بنفس الاسم أو باسم مطابق بعد إزالة اختلافات الكتابة.');
        }
    }

    public static function normalize(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');
        $name = preg_replace('/[\x{0610}-\x{061A}\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}\x{0640}]/u', '', $name) ?? $name;
        $name = strtr($name, [
            'أ' => 'ا',
            'إ' => 'ا',
            'آ' => 'ا',
            'ٱ' => 'ا',
            'ى' => 'ي',
            'ة' => 'ه',
        ]);

        return trim(preg_replace('/\s+/u', ' ', $name) ?? $name);
    }

    public static function existingBeneficiaryId(string $normalizedName, ?int $ignoreId = null): ?int
    {
        foreach (Beneficiary::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->select(['id', 'full_name'])
            ->cursor() as $beneficiary) {
            if (self::normalize((string) $beneficiary->full_name) === $normalizedName) {
                return (int) $beneficiary->id;
            }
        }

        return null;
    }

    private function hasPendingRequestWithName(string $normalizedName): bool
    {
        $requests = ChangeRequest::query()
            ->where('model_type', Beneficiary::class)
            ->where('status', 'pending')
            ->whereIn('action', ['create', 'update'])
            ->when(
                $this->ignoreBeneficiaryId,
                fn ($query) => $query->where(fn ($nested) => $nested
                    ->whereNull('model_id')
                    ->orWhere('model_id', '<>', $this->ignoreBeneficiaryId))
            )
            ->select(['payload'])
            ->cursor();

        foreach ($requests as $request) {
            $payload = $request->payload ?? [];
            if (($payload['__is_wrapped'] ?? false) === true) {
                $payload = $payload['data'] ?? [];
            }

            if (isset($payload['full_name'])
                && self::normalize((string) $payload['full_name']) === $normalizedName) {
                return true;
            }
        }

        return false;
    }
}
