<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImpactActivity extends Model
{
    use HasFactory;

    protected $table = 'impact_activities';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'event_id',
        'people_affected',
        'outcome_summary',
        'image',
        'display_order',
        'metric_type',
        'metric_value',
        'activity_date',
        'location',
        'featured',
        'status',
    ];

    protected $casts = [
        'event_id' => 'integer',
        'people_affected' => 'integer',
        'display_order' => 'integer',
        'metric_value' => 'decimal:2',
        'activity_date' => 'date',
        'featured' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
