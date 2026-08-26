<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventImage extends Model
{
    protected $table = 'event_images';

    protected $fillable = [
        'event_id',
        'path',
        'type',
        'sort_order',
    ];

    protected $casts = [
        'event_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
