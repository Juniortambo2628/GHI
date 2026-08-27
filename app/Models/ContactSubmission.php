<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $table = 'contact_submissions';

    public $timestamps = false;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return trim(($this->firstname ?? '').' '.($this->lastname ?? ''));
    }
}
