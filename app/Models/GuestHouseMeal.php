<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UploadsImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class GuestHouseMeal extends Model
{
    use UploadsImages;

    protected $fillable = [
        'guest_house_id',
        'meal_date',
        'meal_type',
        'served_at',
        'image_path',
        'notes',
        'created_by',
        'total_meals_served',
        'inventory_item_id',
        'warehouse_id',
        'treasury_id',
        'expense_amount',
        'quantity_deducted',
        'expense_id',
        'expense_recorded',
    ];

    protected $casts = [
        'meal_date'         => 'date',
        'expense_amount'    => 'decimal:2',
        'quantity_deducted' => 'decimal:2',
        'expense_recorded'  => 'boolean',
    ];

    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class, 'guest_house_id');
    }

    public function servings(): HasMany
    {
        return $this->hasMany(GuestHouseMealServing::class, 'guest_house_meal_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'inventory_item_id');
    }

    /** أصناف المخزون المصروفة لتحضير هذه الوجبة. */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany(GuestHouseMealInventoryItem::class, 'guest_house_meal_id');
    }

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }
}

