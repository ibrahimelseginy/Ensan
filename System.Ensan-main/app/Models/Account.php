<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Account extends Model
{
    protected $fillable = ['code','name','type','parent_id','description'];

    public function parent() { return $this->belongsTo(Account::class, 'parent_id'); }
    public function children() { return $this->hasMany(Account::class, 'parent_id'); }

    public function lines(): HasMany { return $this->hasMany(JournalEntryLine::class); }
}
