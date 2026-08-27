<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use App\Models\Concerns\SanitizesHtml;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cause extends Model
{
    use HasFactory, HasSlug, HasStatus, SanitizesHtml;

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
