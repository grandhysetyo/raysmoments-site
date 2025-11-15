<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::updateOrCreate(
            [
                // Laravel akan mencari User berdasarkan email ini
                'email' => 'raysmoments.grads@gmail.com'
            ],
            [
                // Jika tidak ada, buat baru dengan data ini:
                // Jika sudah ada, update dengan data ini:
                'name' => 'Rays Moments Admin',
                'password' => '2012grandhys', // Ini akan di-hash otomatis
                'role' => 'admin',                
            ]
        );

        // 2. Akun Fotografer Default
        User::updateOrCreate(
            [
                // Cari berdasarkan email
                'email' => 'raysmoments.fg@gmail.com'
            ],
            [
                // Buat/Update dengan data ini
                'name' => 'Rays Moments FG',
                'password' => '2012grandhys', // Password sama, akan di-hash
                'role' => 'photographer',                
            ]
        );
    }
}
