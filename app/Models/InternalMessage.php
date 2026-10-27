<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalMessage extends Model
{
    use HasFactory;

    protected $table = 'internal_messages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'is_bot',
        'target_type',
        'visible_to_users',
        'hidden_from_users',
        'message',
        'attachment_path',
        'attachment_type',
        'attachment_name',
        'attachment_size',
        'read_at',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
        'read_at' => 'datetime',
        'visible_to_users' => 'array',
        'hidden_from_users' => 'array',
    ];

    protected $appends = [
        'attachment_url',
    ];

    public function getAttachmentUrlAttribute(): ?string
    {
        if (empty($this->attachment_path)) {
            return null;
        }
        if (str_starts_with($this->attachment_path, 'http://') || str_starts_with($this->attachment_path, 'https://')) {
            return $this->attachment_path;
        }
        return asset('storage/' . $this->attachment_path);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function isPublic(): bool
    {
        return is_null($this->receiver_id);
    }

    public function scopePublicRoom($query)
    {
        return $query->whereNull('receiver_id');
    }

    public function scopePublicRoomForUser($query, int $userId)
    {
        return $query->whereNull('receiver_id')
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere(function ($sub) use ($userId) {
                      $sub->where(function ($onlyQ) use ($userId) {
                          $onlyQ->where('target_type', 'only')
                                ->whereJsonContains('visible_to_users', $userId);
                      })
                      ->orWhere(function ($exclQ) use ($userId) {
                          $exclQ->where('target_type', 'exclude')
                                ->where(function ($hQ) use ($userId) {
                                    $hQ->whereNull('hidden_from_users')
                                       ->orWhereJsonDoesntContain('hidden_from_users', $userId);
                                });
                      })
                      ->orWhere(function ($allQ) use ($userId) {
                          $allQ->where(function ($tQ) {
                              $tQ->where('target_type', 'all')->orWhereNull('target_type');
                          })->where(function ($hQ) use ($userId) {
                              $hQ->whereNull('hidden_from_users')
                                 ->orWhereJsonDoesntContain('hidden_from_users', $userId);
                          });
                      });
                  });
            });
    }

    public function scopeBetweenUsers($query, int $userA, int $userB)
    {
        return $query->where(function ($q) use ($userA, $userB) {
            $q->where(function ($sub) use ($userA, $userB) {
                $sub->where('sender_id', $userA)->where('receiver_id', $userB);
            })->orWhere(function ($sub) use ($userA, $userB) {
                $sub->where('sender_id', $userB)->where('receiver_id', $userA);
            });
        });
    }

    public function isVisibleTo(int $userId): bool
    {
        if ($this->sender_id === $userId) {
            return true;
        }
        if (!empty($this->receiver_id)) {
            return $this->receiver_id === $userId;
        }
        if ($this->target_type === 'only') {
            $visible = is_array($this->visible_to_users) ? $this->visible_to_users : [];
            return in_array($userId, $visible);
        }
        $hidden = is_array($this->hidden_from_users) ? $this->hidden_from_users : [];
        if (in_array($userId, $hidden)) {
            return false;
        }
        return true;
    }
}
