<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SanitizesHtml;

class ImpactActivity extends Model
{
    use HasFactory, SanitizesHtml;

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

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = \Illuminate\Support\Str::slug($model->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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
