<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'website', 'address', 'merchant_name', 'source_name', 'project_id', 'notes'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
