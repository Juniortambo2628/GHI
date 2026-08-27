<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use App\Models\Concerns\SanitizesHtml;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory, HasSlug, HasStatus, SanitizesHtml;

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
