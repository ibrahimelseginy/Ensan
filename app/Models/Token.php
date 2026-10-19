<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Token extends Model
{
    protected $fillable = ['user_id','token','expires_at','hashed'];

    protected $casts = ['expires_at' => 'datetime', 'hashed' => 'boolean'];

    public static function issue(User $user): string
    {
        $plain = bin2hex(random_bytes(32));
        static::create(['user_id' => $user->id, 'token' => hash('sha256', $plain), 'hashed' => true, 'expires_at' => now()->addDays(30)]);
        return $plain;
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
