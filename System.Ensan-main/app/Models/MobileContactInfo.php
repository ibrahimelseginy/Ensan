<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MobileContactInfo extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function phones()
    {
        return $this->hasMany(MobileContactPhone::class, 'contact_info_id')->orderBy('sort_order');
    }
}
