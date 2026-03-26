<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerHour extends Model
{
    protected $fillable = ['user_id','date','hours','task'];
    protected $casts = ['date' => 'date'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
