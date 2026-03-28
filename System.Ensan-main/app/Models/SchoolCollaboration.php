<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class SchoolCollaboration extends Model
{
    protected $fillable = [
        'shop_name', 'address', 'phone', 'discount', 'transactions', 'campaign', 'notes'
    ];
}
