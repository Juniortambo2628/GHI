<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Initiative extends Model
{
    use HasFactory;

    protected $table = 'initiatives';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'category',
        'cause_id',
        'status',
    ];

    protected $casts = [
        'cause_id' => 'integer',
    ];

    public function cause()
    {
        return $this->belongsTo(Cause::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
