<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingAddon extends Model
{
    use HasFactory;
    
    protected $table = 'booking_addons';

    protected $fillable = [
        'booking_id',
        'addon_id',
        'quantity',
        'price',
        'grand_total',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'quantity' => 'integer',
    ];
    
    public function addon(): BelongsTo
    {
        // Secara default, Laravel mencari addon_id.
        return $this->belongsTo(Addon::class); 
    }
}