<?php

namespace App\Models;

use App\Traits\UploadsImages;
use Illuminate\Database\Eloquent\Model;

final class GuestHousePatientProfile extends Model
{
    use UploadsImages;

    protected $fillable = [
        'beneficiary_id', 'guest_house_id', 'treatment_type', 'medical_center',
        'sessions_count', 'gender', 'patient_id_front_path', 'patient_id_back_path',
        'followup_card_path', 'referral_letter_path', 'medical_notes',
        'companion_name', 'companion_relation', 'companion_national_id',
        'companion_phone', 'companion_address', 'companion_id_front_path',
        'companion_id_back_path', 'companion_history', 'manager_notes',
    ];

    protected $casts = [
        'companion_history' => 'array',
    ];

    public function buildCompanionArchiveEntry(?string $reason = null): ?array
    {
        $oldName = trim((string) ($this->companion_name ?? ''));
        if ($oldName === '' || !GuestHouseMealServing::isRealCompanion($oldName)) {
            return null;
        }

        return [
            'name' => $oldName,
            'relation' => $this->companion_relation,
            'national_id' => $this->companion_national_id,
            'phone' => $this->companion_phone,
            'address' => $this->companion_address,
            'id_front_path' => $this->companion_id_front_path,
            'id_back_path' => $this->companion_id_back_path,
            'id_front_url' => $this->companion_id_front_url,
            'id_back_url' => $this->companion_id_back_url,
            'reason' => $reason,
            'archived_at' => now()->format('Y-m-d H:i'),
            'archived_by' => auth()->user()?->name ?? 'النظام',
        ];
    }

    public function getImageColumns(): array
    {
        return [
            'patient_id_front_path', 'patient_id_back_path',
            'followup_card_path', 'referral_letter_path',
            'companion_id_front_path', 'companion_id_back_path'
        ];
    }

    public function hasCompanion(): bool
    {
        return GuestHouseMealServing::isRealCompanion($this->companion_name);
    }

    public function getPatientIdFrontUrlAttribute(): ?string { return $this->getFileUrl('patient_id_front_path'); }
    public function getPatientIdBackUrlAttribute(): ?string { return $this->getFileUrl('patient_id_back_path'); }
    public function getCompanionIdFrontUrlAttribute(): ?string { return $this->getFileUrl('companion_id_front_path'); }
    public function getCompanionIdBackUrlAttribute(): ?string { return $this->getFileUrl('companion_id_back_path'); }
    public function getFollowupCardUrlAttribute(): ?string { return $this->getFileUrl('followup_card_path'); }
    public function getReferralLetterUrlAttribute(): ?string { return $this->getFileUrl('referral_letter_path'); }

    public static function isImageFile(?string $path): bool
    {
        if (!$path) return false;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'], true);
    }

    public function getUploadedDocumentsCountAttribute(): int
    {
        return collect([
            $this->patient_id_front_path,
            $this->patient_id_back_path,
            $this->companion_id_front_path,
            $this->companion_id_back_path,
            $this->followup_card_path,
            $this->referral_letter_path,
        ])->filter(fn($p) => !empty($p))->count();
    }

    public function getDocumentList(): array
    {
        return [
            'patient_id_front' => [
                'key' => 'patient_id_front',
                'column' => 'patient_id_front_path',
                'title' => 'بطاقة المريض (الوجه)',
                'subtitle' => 'صورة بطاقة الرقم القومي للمريض - الوجه الأمامي',
                'icon' => 'bi-person-badge',
                'badge' => 'بطاقة مريض',
                'badge_class' => 'bg-primary-subtle text-primary',
                'path' => $this->patient_id_front_path,
                'url' => $this->patient_id_front_url,
                'is_uploaded' => !empty($this->patient_id_front_path),
                'is_image' => self::isImageFile($this->patient_id_front_path),
            ],
            'patient_id_back' => [
                'key' => 'patient_id_back',
                'column' => 'patient_id_back_path',
                'title' => 'بطاقة المريض (الظهر)',
                'subtitle' => 'صورة بطاقة الرقم القومي للمريض - الوجه الخلفي',
                'icon' => 'bi-credit-card-2-front',
                'badge' => 'بطاقة مريض',
                'badge_class' => 'bg-primary-subtle text-primary',
                'path' => $this->patient_id_back_path,
                'url' => $this->patient_id_back_url,
                'is_uploaded' => !empty($this->patient_id_back_path),
                'is_image' => self::isImageFile($this->patient_id_back_path),
            ],
            'companion_id_front' => [
                'key' => 'companion_id_front',
                'column' => 'companion_id_front_path',
                'title' => 'بطاقة المرافق (الوجه)',
                'subtitle' => $this->companion_name ? "المرافق: {$this->companion_name}" . ($this->companion_relation ? " ({$this->companion_relation})" : '') : 'صورة بطاقة الرقم القومي للمرافق - الوجه الأمامي',
                'icon' => 'bi-person-vcard',
                'badge' => 'بطاقة مرافق',
                'badge_class' => 'bg-secondary-subtle text-secondary',
                'path' => $this->companion_id_front_path,
                'url' => $this->companion_id_front_url,
                'is_uploaded' => !empty($this->companion_id_front_path),
                'is_image' => self::isImageFile($this->companion_id_front_path),
            ],
            'companion_id_back' => [
                'key' => 'companion_id_back',
                'column' => 'companion_id_back_path',
                'title' => 'بطاقة المرافق (الظهر)',
                'subtitle' => 'صورة بطاقة الرقم القومي للمرافق - الوجه الخلفي',
                'icon' => 'bi-credit-card',
                'badge' => 'بطاقة مرافق',
                'badge_class' => 'bg-secondary-subtle text-secondary',
                'path' => $this->companion_id_back_path,
                'url' => $this->companion_id_back_url,
                'is_uploaded' => !empty($this->companion_id_back_path),
                'is_image' => self::isImageFile($this->companion_id_back_path),
            ],
            'followup_card' => [
                'key' => 'followup_card',
                'column' => 'followup_card_path',
                'title' => 'كارت المتابعة (جرعات الكيماوي)',
                'subtitle' => 'كارت متابعة جرعات وجلسات العلاج الكيماوي للحالة',
                'icon' => 'bi-file-earmark-medical',
                'badge' => 'كارت متابعة',
                'badge_class' => 'bg-info-subtle text-info',
                'path' => $this->followup_card_path,
                'url' => $this->followup_card_url,
                'is_uploaded' => !empty($this->followup_card_path),
                'is_image' => self::isImageFile($this->followup_card_path),
            ],
            'referral_letter' => [
                'key' => 'referral_letter',
                'column' => 'referral_letter_path',
                'title' => 'خطاب التحويل / التقرير الطبي',
                'subtitle' => 'خطاب التحويل الطبي بعدد الجلسات أو التقرير الطبي المعتمد',
                'icon' => 'bi-file-earmark-text',
                'badge' => 'تقرير / تحويل',
                'badge_class' => 'bg-warning-subtle text-warning',
                'path' => $this->referral_letter_path,
                'url' => $this->referral_letter_url,
                'is_uploaded' => !empty($this->referral_letter_path),
                'is_image' => self::isImageFile($this->referral_letter_path),
            ],
        ];
    }

    public function beneficiary() { return $this->belongsTo(Beneficiary::class); }
    public function guestHouse() { return $this->belongsTo(GuestHouse::class); }
}

