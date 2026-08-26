<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';

    protected $fillable = [
        'donor_name',
        'donor_email',
        'amount',
        'currency',
        'donation_type',
        'transaction_id',
        'stripe_payment_intent_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
