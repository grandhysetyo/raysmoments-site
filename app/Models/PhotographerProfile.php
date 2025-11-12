<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotographerProfile extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel secara eksplisit jika berbeda dari 'photographerprofiles'
     */
    protected $table = 'photographer_profiles';

    /**
     * Atribut yang boleh diisi.
     * Kita biarkan kosong agar aman, karena kita akan mengisinya
     * melalui relasi atau hanya 'photographer_id'.
     *
     * Atau, untuk membuatnya eksplisit:
     */
    protected $fillable = [
        'photographer_id',
        'default_rate',
        'experience_years',
        'speciality',
        'bio',
        'portfolio_url',
    ];

    /**
     * Mendefinisikan relasi kebalikannya (Profile ini 'milik' satu User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }
    /**
     * Profil ini memiliki banyak (hasMany) tarif.
     */
    public function rates(): HasMany
    {
        // Cek: Apakah nama kolom di tabel 'photographer_rates' yang menunjuk
        // kembali ke tabel 'photographer_profiles' sudah benar-benar 'photographer_id'?
        return $this->hasMany(PhotographerRate::class, 'photographer_id', 'id');
    }

}
