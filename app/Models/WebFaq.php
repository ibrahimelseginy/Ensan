<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebFaq extends Model
{
    protected $fillable = ['question', 'answer', 'category', 'sort_order'];
}
