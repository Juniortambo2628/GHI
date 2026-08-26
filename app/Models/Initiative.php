<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SanitizesHtml;

class Initiative extends Model
{
    use HasFactory, SanitizesHtml;

    protected $table = 'initiatives';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'category',
        'status',
    ];

    protected $casts = [];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = \Illuminate\Support\Str::slug($model->title);
            }
        });
    }

    public function causes()
    {
        return $this->belongsToMany(Cause::class);
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
