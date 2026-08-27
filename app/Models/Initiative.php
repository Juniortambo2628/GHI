<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SanitizesHtml;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;

class Initiative extends Model
{
    use HasFactory, SanitizesHtml, HasSlug, HasStatus;

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

    public function causes()
    {
        return $this->belongsToMany(Cause::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
