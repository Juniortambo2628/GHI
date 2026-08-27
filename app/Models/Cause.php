<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\SanitizesHtml;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;

class Cause extends Model
{
    use HasFactory, SanitizesHtml, HasSlug, HasStatus;

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

    public function initiatives()
    {
        return $this->belongsToMany(Initiative::class);
    }
}
