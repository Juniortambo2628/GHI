<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SanitizesHtml;

class Event extends Model
{
    use HasFactory, SanitizesHtml;

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

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = \Illuminate\Support\Str::slug($model->title);
            }
        });
    }

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
