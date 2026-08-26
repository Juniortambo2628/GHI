<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = ['path', 'route_name', 'referrer', 'visitor_hash', 'occurred_at'];

    protected $casts = ['occurred_at' => 'datetime'];
}
