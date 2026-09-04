<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MobileContactPhone extends Model
{
    protected $fillable = ['contact_info_id', 'phone', 'sort_order'];

    public function contactInfo()
    {
        return $this->belongsTo(MobileContactInfo::class, 'contact_info_id');
    }
}
