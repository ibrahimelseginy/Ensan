<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class GuestHouseMealInventoryItem extends Model
{
    protected $fillable = [
        'guest_house_meal_id',
        'warehouse_id',
        'item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(GuestHouseMeal::class, 'guest_house_meal_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'guest_house_meal_inventory_item_id');
    }
}
