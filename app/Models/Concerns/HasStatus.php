<?php

namespace App\Models\Concerns;

trait HasStatus
{
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
