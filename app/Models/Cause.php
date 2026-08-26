<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SanitizesHtml;

class Cause extends Model
{
    use HasFactory, SanitizesHtml;

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

    public function initiatives()
    {
        return $this->belongsToMany(Initiative::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
