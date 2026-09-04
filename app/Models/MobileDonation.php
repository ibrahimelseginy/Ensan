<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\UploadsImages;

final class MobileDonation extends Model
{
    use HasFactory, UploadsImages;

    /**
     * Override the image column for this model.
     */
    public function getImageColumn(): string
    {
        return 'receipt_path';
    }

    protected $fillable = [
        'donor_name',
        'donor_phone',
        'donor_address',
        'donation_amount',
        'donation_for',
        'payment_method',
        'notes',
        'status',
        'transaction_id',
        'account_number',
        'account_name',
        'receipt_path',
    ];
}
