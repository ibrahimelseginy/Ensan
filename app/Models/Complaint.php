<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class Complaint extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'tracking_code','source_type','source_id','complainant_name','complainant_phone',
        'against_user_id','status','subject','message','attachment_path','resolution','resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (Complaint $complaint) {
            if (empty($complaint->tracking_code)) {
                $complaint->tracking_code = static::generateUniqueTrackingCode();
            }
        });
    }

    public static function generateUniqueTrackingCode(): string
    {
        // Use unambiguous characters (excluding easily confused 0, O, 1, I)
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $charsLen = strlen($chars);
        do {
            $random = '';
            for ($i = 0; $i < 6; $i++) {
                $random .= $chars[random_int(0, $charsLen - 1)];
            }
            $code = 'ENS-' . $random;
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

    public function getTrackingUrlAttribute(): string
    {
        return route('complaint.track', ['code' => $this->tracking_code]);
    }

    public function against(): BelongsTo { return $this->belongsTo(User::class, 'against_user_id'); }

    public function getSourceNameAttribute(): string
    {
        if (!empty($this->complainant_name)) {
            return $this->complainant_name;
        }
        if ($this->source_type === 'donor') {
            return \App\Models\Donor::find($this->source_id)?->name ?? ('متبرع #' . $this->source_id);
        }
        if ($this->source_type === 'beneficiary') {
            return \App\Models\Beneficiary::find($this->source_id)?->full_name ?? ('مستفيد #' . $this->source_id);
        }
        if ($this->source_type === 'employee') {
            return \App\Models\User::find($this->source_id)?->name ?? ('موظف #' . $this->source_id);
        }
        return 'غير محدد';
    }

    public function getSourceModelAttribute(): mixed
    {
        return match ($this->source_type) {
            'donor' => \App\Models\Donor::find($this->source_id),
            'beneficiary' => \App\Models\Beneficiary::find($this->source_id),
            'employee' => \App\Models\User::find($this->source_id),
            default => null,
        };
    }

    public function getSourceTypeNameAttribute(): string
    {
        return match ($this->source_type) {
            'donor' => 'متبرع',
            'beneficiary' => 'مستفيد',
            'employee' => 'موظف',
            default => $this->source_type,
        };
    }
}
