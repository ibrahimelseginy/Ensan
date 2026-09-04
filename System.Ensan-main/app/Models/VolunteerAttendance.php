<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class VolunteerAttendance extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = ['user_id','date','check_in_at','check_out_at','notes','rating','evaluation_notes'];
    protected $casts = ['date' => 'date'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
