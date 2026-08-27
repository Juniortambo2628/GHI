<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use App\Models\Concerns\SanitizesHtml;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactActivity extends Model
{
    use HasFactory, HasSlug, HasStatus, SanitizesHtml;

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

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
