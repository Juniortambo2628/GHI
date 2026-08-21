<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{
    use HasFactory;

    protected $table = 'stories';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'author',
        'featured_image',
        'image',
        'category',
        'status',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
