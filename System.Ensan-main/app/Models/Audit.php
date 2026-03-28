<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Audit extends Model
{
    protected $fillable = ['user_id','method','path','status_code','ip','user_agent','entity_type','entity_id','payload'];
    protected $casts = ['payload' => 'array'];
}
