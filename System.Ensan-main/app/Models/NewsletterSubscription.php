<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'is_active',
    ];
}
