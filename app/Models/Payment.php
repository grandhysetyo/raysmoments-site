<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'proof_of_payment_url',
        'status', // e.g., 'Pending', 'Verified', 'Rejected'
        'payment_type', // e.g., 'DP', 'Final Payment'
        'verified_by', // user_id yang memverifikasi (opsional)
        'verified_at', // timestamp verifikasi (opsional)
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}