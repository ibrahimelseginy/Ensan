<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = ['date','branch','gate','entry_type','locked','description'];

    protected $casts = ['date' => 'date','locked' => 'boolean'];

    public function lines(): HasMany { return $this->hasMany(JournalEntryLine::class); }
}
