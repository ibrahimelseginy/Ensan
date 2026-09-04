<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Role extends Model
{
    use \App\Traits\HashedRouteKey;

    protected $fillable = ['name','key','description'];

    public function users(): BelongsToMany { return $this->belongsToMany(User::class); }
    public function permissions(): BelongsToMany { return $this->belongsToMany(Permission::class); }
}
