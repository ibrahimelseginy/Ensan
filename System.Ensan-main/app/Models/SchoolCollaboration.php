<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolCollaboration extends Model
{
    protected $fillable = [
        'shop_name', 'address', 'phone', 'discount', 'transactions', 'campaign', 'notes'
    ];
}
