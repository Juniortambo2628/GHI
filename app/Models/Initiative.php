<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use App\Models\Concerns\SanitizesHtml;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Initiative extends Model
{
    use HasFactory, HasSlug, HasStatus, SanitizesHtml;

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
