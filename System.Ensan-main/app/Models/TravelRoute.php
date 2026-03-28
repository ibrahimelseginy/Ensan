<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TravelRoute extends Model
{
    protected $table = 'travel_routes';
    protected $fillable = ['name','description'];
    protected $casts = [
        'cities' => 'array'
    ];

    public function delegates(): HasMany { return $this->hasMany(Delegate::class, 'route_id'); }
    public function donations(): HasMany { return $this->hasMany(Donation::class, 'route_id'); }
}
