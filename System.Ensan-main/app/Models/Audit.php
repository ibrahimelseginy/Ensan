<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = ['user_id','method','path','status_code','ip','user_agent','entity_type','entity_id','payload'];
    protected $casts = ['payload' => 'array'];
}
