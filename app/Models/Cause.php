<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cause extends Model
{
    use HasFactory;

    protected $table = 'causes';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'quote',
        'icon',
        'image',
        'display_order',
        'status',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function initiatives()
    {
        return $this->hasMany(Initiative::class);
    }
}
