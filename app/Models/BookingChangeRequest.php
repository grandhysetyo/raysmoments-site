<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingChangeRequest extends Model {
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'old_event_date',
        'old_event_location',
        'old_event_city',
        'new_event_date',
        'new_event_location',
        'new_event_city',
        'reason',
        'status',
        'admin_notes',
    ];

    /**
     * Pengajuan ini milik satu Booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

}