<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileDonation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_name',
        'donor_phone',
        'donor_address',
        'donation_amount',
        'donation_for',
        'payment_method',
        'notes',
        'status',
        'transaction_id'
    ];
}
