<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebOpinion extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'opinion', 'is_published'];
    
    protected $casts = [
        'is_published' => 'boolean',
    ];
}
