<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    protected $fillable = [
        'path',
        'original_name',
        'alt_text',
        'caption',
        'group',
        'width',
        'height',
        'file_size',
        'mime_type',
    ];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('path', 'like', "%{$search}%")
                ->orWhere('original_name', 'like', "%{$search}%")
                ->orWhere('alt_text', 'like', "%{$search}%")
                ->orWhere('caption', 'like', "%{$search}%");
        });
    }

    public function scopeOfGroup(Builder $query, ?string $group): Builder
    {
        if (! $group) {
            return $query;
        }

        return $query->where('group', $group);
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('mime_type', 'like', 'image/%');
    }
}
