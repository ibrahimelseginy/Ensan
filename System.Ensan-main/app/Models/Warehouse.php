<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Warehouse extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = [
        'name',
        'location',
        'manager_id',
        'phone',
        'address',
        'capacity',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer'
    ];

    public function transactions(): HasMany 
    { 
        return $this->hasMany(InventoryTransaction::class); 
    }

    public function donations(): HasMany 
    { 
        return $this->hasMany(Donation::class); 
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Get total stock value in warehouse
    public function getTotalStockValue()
    {
        $items = Item::all();
        $totalValue = 0;

        foreach ($items as $item) {
            $totalValue += $item->getStockValue($this->id);
        }

        return $totalValue;
    }

    // Get utilization percentage
    public function getUtilizationPercentage()
    {
        if (!$this->capacity) {
            return 0;
        }

        $currentStock = $this->transactions()
            ->where('status', 'approved')
            ->where('type', 'in')
            ->sum('quantity');

        $outStock = $this->transactions()
            ->where('status', 'approved')
            ->where('type', 'out')
            ->sum('quantity');

        $netStock = $currentStock - $outStock;

        return ($netStock / $this->capacity) * 100;
    }
}
