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
        'tracking_code','source_type','source_id','against_user_id',
        'status','subject','message','attachment_path','resolution','resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (Complaint $complaint) {
            if (empty($complaint->tracking_code)) {
                do {
                    $code = 'ENS-' . strtoupper(Str::random(6));
                } while (static::where('tracking_code', $code)->exists());
                $complaint->tracking_code = $code;
            }
        });
    }

    public function against(): BelongsTo { return $this->belongsTo(User::class, 'against_user_id'); }
}
