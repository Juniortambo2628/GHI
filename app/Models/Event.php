<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'event_date',
        'location',
        'initiative_id',
        'status',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'initiative_id' => 'integer',
    ];

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }

    public function impactActivities()
    {
        return $this->hasMany(ImpactActivity::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now())->where('status', 'published');
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now())->where('status', 'published');
    }
}
