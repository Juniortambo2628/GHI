<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GetInvolvedSubmission extends Model
{
    protected $table = 'get_involved_submissions';

    protected $fillable = [
        'full_name',
        'email',
        'initiative_id',
        'message',
        'status',
    ];

    protected $casts = [
        'initiative_id' => 'integer',
    ];

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(Initiative::class);
    }
}
