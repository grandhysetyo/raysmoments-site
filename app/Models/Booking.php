<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'client_id',
        'package_id',
        'event_date',
        'event_location',
        'event_city',
        'session_1_time',
        'session_2_time',
        'photographer_id',
        'photographer_rate',
        'photographer_other_costs', 
        'package_price',
        'addons_total',
        'grand_total',
        'status',
        'notes',
    ];
   
    protected $casts = [
        'event_date' => 'date',
        'session_1_time' => 'datetime:H:i',
        'session_2_time' => 'datetime:H:i',
        'package_price' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'photographer_rate' => 'decimal:2',
        'photographer_other_costs' => 'decimal:2', // <-- Tambahan dari migrasi baru
    ];

    public function user(): BelongsTo
    {        
        return $this->belongsTo(User::class, 'client_id'); 
    }

    public function photographer()
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function bookingAddons(): HasMany
    {
        return $this->hasMany(BookingAddon::class);
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'booking_addons')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function projectPhotos()
    {
        return $this->hasMany(ProjectPhoto::class);
    }
    public function changeRequests()
    {
        return $this->hasMany(BookingChangeRequest::class);
    }
}

