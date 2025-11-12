<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Impor relasi

class Package extends Model
{
    use HasFactory;

    /**
     * Atribut yang diizinkan untuk diisi secara massal (mass assignable).
     *
     * Ini penting untuk keamanan dan agar method Package::create() berfungsi.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_hours',
        'max_photos',
        'is_active',
    ];

    /**
     * Tipe data kustom untuk atribut (casting).
     *
     * Ini akan secara otomatis mengubah tipe data saat mengambil/menyimpan ke database.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',      // Menangani harga sebagai desimal (baik untuk uang)
        'duration_hours' => 'integer', // Pastikan ini selalu angka bulat
        'max_photos' => 'integer',     // Pastikan ini selalu angka bulat
        'is_active' => 'boolean',    // Mengubah 0/1 dari DB menjadi true/false
    ];

    /**
     * Mendefinisikan relasi "satu ke banyak" dengan model Booking.
     *
     * Satu paket (Package) bisa memiliki banyak (hasMany) pesanan (Booking).
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}