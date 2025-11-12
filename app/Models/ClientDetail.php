<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDetail extends Model
{
    use HasFactory;

    protected $table = 'client_details';

    protected $fillable = [
        'client_id',
        'full_name',
        'university',
        'faculty_or_major',
        'whatsapp_number',
        'instagram',
    ];

    /**
     * Relasi: Detail klien ini dimiliki oleh satu User.
     */
    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'client_id'); 
    }
}