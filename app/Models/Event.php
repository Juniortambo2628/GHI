<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SanitizesHtml;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;

class Event extends Model
{
    use HasFactory, SanitizesHtml, HasSlug, HasStatus;

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

    public function images()
    {
        return $this->hasMany(EventImage::class)->orderBy('sort_order');
    }

    public function impactActivities()
    {
        return $this->hasMany(ImpactActivity::class);
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
