<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SanitizesHtml;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;

class Story extends Model
{
    use HasFactory, SanitizesHtml, HasSlug, HasStatus;

    protected $table = 'stories';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'author',
        'featured_image',
        'image',
        'likes',
        'comments',
        'category',
        'status',
    ];
}
