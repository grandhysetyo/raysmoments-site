<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role', 'status',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Tipe data kustom untuk atribut (casting).
     */
    protected $casts = [
        'password' => 'hashed', // Otomatis hash jika di-set, tapi kita akan hash manual
    ];

    
    public function clientDetails(): HasOne
    {        
        return $this->hasOne(ClientDetail::class, 'client_id');
    }
    public function photographerProfile()
    {
        return $this->hasOne(PhotographerProfile::class, 'photographer_id');
    }
    public function bookings(): HasMany
    {
        // Pastikan Anda menggunakan 'client_id' sebagai foreign key
        return $this->hasMany(Booking::class, 'client_id');
    }

    // --- TAMBAHKAN FUNGSI INI ---
    /**
     * Event yang dijalankan saat model ini di-boot.
     */
    protected static function booted()
    {
        // Saat seorang user dihapus (deleting)
        static::deleting(function ($user) {
            // Jika user tersebut memiliki profile (adalah fotografer)
            if ($user->profile) {
                // Hapus juga profilenya.
                $user->profile()->delete();
            }
        });
    }
}
