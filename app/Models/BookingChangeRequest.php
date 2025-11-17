<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingChangeRequest extends Model {
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'new_package_id',   
        'new_addons',
        'additional_cost',
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

    protected $casts = [
        'new_addons' => 'array', // Laravel akan otomatis json_encode saat simpan, dan json_decode saat ambil
    ];

    /**
     * Pengajuan ini milik satu Booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function newPackage()
    {
        return $this->belongsTo(Package::class, 'new_package_id');
    }

}