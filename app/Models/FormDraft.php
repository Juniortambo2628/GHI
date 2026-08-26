<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormDraft extends Model
{
    use HasFactory;

    protected $table = 'form_drafts';

    protected $fillable = [
        'user_id',
        'form_key',
        'data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
