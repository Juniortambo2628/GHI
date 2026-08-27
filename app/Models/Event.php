<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use App\Models\Concerns\SanitizesHtml;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasSlug, HasStatus, SanitizesHtml;

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
