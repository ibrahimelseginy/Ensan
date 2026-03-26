<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use \App\Traits\UploadsImages;
    protected $appends = ['image_url'];

    protected $fillable = [
        'sku',
        'name',
        'unit',
        'estimated_value',
        'original_price',
        'discount_percentage',
        'category',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'barcode',
        'image_path',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'estimated_value' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'min_stock_level' => 'integer',
        'max_stock_level' => 'integer',
        'reorder_point' => 'integer'
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // Get current stock across all warehouses
    public function getCurrentStock($warehouseId = null)
    {
        $query = $this->transactions()->where('status', 'approved');
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $inStock = (clone $query)->where('type', 'in')->sum('quantity');
        $outStock = (clone $query)->where('type', 'out')->sum('quantity');

        return $inStock - $outStock;
    }

    // Check if stock is low
    public function isLowStock($warehouseId = null)
    {
        $currentStock = $this->getCurrentStock($warehouseId);
        return $currentStock <= $this->min_stock_level;
    }

    // Check if out of stock
    public function isOutOfStock($warehouseId = null)
    {
        return $this->getCurrentStock($warehouseId) <= 0;
    }

    // Check if reorder needed
    public function needsReorder($warehouseId = null)
    {
        if (!$this->reorder_point) {
            return false;
        }
        return $this->getCurrentStock($warehouseId) <= $this->reorder_point;
    }

    // Get stock value
    public function getStockValue($warehouseId = null)
    {
        $currentStock = $this->getCurrentStock($warehouseId);
        return $currentStock * ($this->estimated_value ?? 0);
    }
}
