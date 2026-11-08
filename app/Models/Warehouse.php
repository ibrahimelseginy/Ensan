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
        'department',
        'guest_house_id',
        'project_id',
        'manager_id',
        'assigned_user_ids',
        'phone',
        'address',
        'capacity',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'assigned_user_ids' => 'array',
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

    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class, 'guest_house_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function scopeAccessibleBy($query, ?User $user = null)
    {
        $user = $user ?: (request()->user() ?? auth()->user());
        if (!$user) {
            return $query;
        }

        if ($user->hasRole(['admin', 'super_admin', 'manager', 'مدير المؤسسة بالكامل', 'إدارة'])) {
            return $query;
        }

        $hasCols = \Illuminate\Support\Facades\Schema::hasColumn('warehouses', 'guest_house_id');
        if (!$hasCols) {
            return $query;
        }

        // Check if any warehouse has restrictions configured
        $anyLinked = static::query()
            ->where(function ($q) {
                $q->whereNotNull('guest_house_id')
                  ->orWhereNotNull('project_id')
                  ->orWhereNotNull('manager_id')
                  ->orWhereNotNull('assigned_user_ids')
                  ->orWhereNotNull('department');
            })->exists();

        if (!$anyLinked) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            // Unassigned general warehouses remain visible unless user is strictly tied to a guest house
            $q->where('manager_id', $user->id)
              ->orWhereJsonContains('assigned_user_ids', (int) $user->id)
              ->orWhereJsonContains('assigned_user_ids', (string) $user->id);

            if ($user->guest_house_id) {
                $q->orWhere('guest_house_id', $user->guest_house_id);
            }
            if ($user->project_id) {
                $q->orWhere('project_id', $user->project_id);
            }
            if (!empty($user->department)) {
                $q->orWhere('department', $user->department);
            }

            // Also include warehouses where this user is a guest house manager
            $managedHouseIds = GuestHouse::where('manager_user_id', $user->id)->pluck('id')->all();
            if (!empty($managedHouseIds)) {
                $q->orWhereIn('guest_house_id', $managedHouseIds);
            }

            // If user is not bound to a specific guest house, also show general unlinked warehouses
            if (!$user->guest_house_id && empty($managedHouseIds)) {
                $q->orWhere(function ($sub) {
                    $sub->whereNull('guest_house_id')
                        ->whereNull('project_id')
                        ->whereNull('manager_id')
                        ->whereNull('assigned_user_ids');
                });
            }
        });
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
