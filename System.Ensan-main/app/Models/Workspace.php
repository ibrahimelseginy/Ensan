<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amenities',
        'location',
        'capacity',
        'price_per_hour',
        'price_per_day',
        'manager_id',
        'status',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function rentals()
    {
        return $this->hasMany(WorkspaceRental::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
