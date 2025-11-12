<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotographerRate extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel secara eksplisit.
     */
    protected $table = 'photographer_rates';

    /**
     * Atribut yang diizinkan untuk diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'photographer_id',
        'city',
        'base_rate',
        'transport_fee',
        'effective_start',
        'effective_end',
        'notes',
    ];

    /**
     * Tipe data kustom untuk atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_rate' => 'decimal:2',
        'transport_fee' => 'decimal:2',
        'effective_start' => 'date',
        'effective_end' => 'date',
    ];

    /**
     * Relasi: Rate ini dimiliki oleh satu PhotographerProfile.
     * Catatan: Relasi ini menunjuk ke model PhotographerProfile,
     * yang mana memiliki foreign key photographer_id yang sama.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(PhotographerProfile::class, 'photographer_id');
    }
}