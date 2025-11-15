<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\PhotographerController;
use App\Http\Controllers\Admin\PhotographerRateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NewBookingController;
use App\Http\Controllers\Client\BookingChangeRequestController;
use App\Http\Controllers\Admin\UpcomingShootingController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminPaymentsController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Client\TrackingController;





Route::get('/', [FrontController::class, 'index'])->name('home');
/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy']) ->middleware('auth')->name('logout');

Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

/*
|--------------------------------------------------------------------------
| Booking Routes
|--------------------------------------------------------------------------
*/

// Booking
Route::get('bookings', [BookingController::class,'index'])->name('bookings.index');
Route::post('bookings', [BookingController::class,'store'])->name('bookings.store');
Route::get('bookings/{booking}', [BookingController::class,'show'])->name('bookings.show');

// Permintaan Perubahan
Route::get('/bookings/{order_code}/request-change', [BookingChangeRequestController::class, 'show'])
    ->name('request_change.show');
Route::post('/bookings/{order_code}/request-change', [BookingChangeRequestController::class, 'store'])
    ->name('request_change.store');

// Tracking status
Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::get('/track/{order_code}', [TrackingController::class, 'show'])->name('tracking.show');

// Upload pembayaran
Route::post('payments/upload/{order_code}', [PaymentController::class,'store'])->name('payments.store');


/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/
// Rute Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // Master
    Route::resource('packages', PackageController::class);
    Route::resource('addons', AddonController::class);
    Route::resource('photographers', PhotographerController::class);
    Route::post('/photographer-rates', [PhotographerRateController::class, 'store'])->name('photographer-rates.store');
    Route::delete('/photographer-rates/{rate}', [PhotographerRateController::class, 'destroy'])->name('photographer-rates.destroy');
    Route::resource('users', UserController::class);

    // "New Books" (Hanya untuk booking Awaiting DP)
    Route::get('/new-books', [NewBookingController::class, 'index'])->name('new-books.index');
    Route::get('/new-books/{booking}', [NewBookingController::class, 'show'])->name('new-books.show');
    // Master Booking
    Route::resource('bookings', AdminBookingController::class)->except(['index', 'show']);
    
    // Aksi Verifikasi DP
    Route::post('/payments/{booking}/update-dp-status', [AdminPaymentsController::class, 'verifyDP'])
             ->name('payments.verify_dp');
    Route::post('/payments/{booking}/update-fullpayments-status', [AdminPaymentsController::class, 'verifyFullPayment'])
    ->name('payments.verify_full');
    
     // Upcoming Shooting
    // (Nama route: admin.upcoming.*)
    Route::prefix('upcoming')->name('upcoming.')->group(function () {
        Route::get('/', [UpcomingShootingController::class, 'index'])
                ->name('index');
        
        // Halaman Show (Form Assignment)
        // (Nama route: admin.upcoming.show)
        Route::get('/{booking}', [UpcomingShootingController::class, 'show'])
                ->name('show');
        
        // Aksi untuk Assign Fotografer
        // (Nama route: admin.upcoming.assign)
        Route::post('/{booking}/assign', [UpcomingShootingController::class, 'assign'])
                ->name('assign');
        
        // Aksi untuk Refund
        // (Nama route: admin.upcoming.refund)
        Route::post('/{booking}/refund', [UpcomingShootingController::class, 'refund'])
                ->name('refund');

        // Helper Route untuk AJAX (Mengambil Rates)
        // (Nama route: admin.upcoming.get_rates)
        Route::get('/get-rates/{photographer}', [UpcomingShootingController::class, 'getRates'])
                ->name('get_rates');
    });

    // --- Rute List Project (Verifikasi Final Payment) ---
    Route::prefix('projects')->name('projects.')->group(function () {
        
        // Halaman Index (Daftar Proyek)
        // (Nama route: admin.projects.index)
        Route::get('/', [ProjectController::class, 'index'])
             ->name('index');
        
        // Halaman Show (Verifikasi Final Payment)
        // (Nama route: admin.projects.show)
        Route::get('/{booking}', [ProjectController::class, 'show'])
             ->name('show');
        
        // Aksi untuk Update Status
        Route::post('/{booking}/update-status', [App\Http\Controllers\Admin\ProjectController::class, 'updateStatus'])
            ->name('update_status');

        // Aksi untuk Verifikasi / Tolak Final Payment
        // (Nama route: admin.projects.verify_final)
        // Route::post('/{booking}/verify-final', [ProjectController::class, 'verifyFinalPayment'])
        //      ->name('verify_final');
    });
});

// Rute Fotografer
Route::middleware(['auth'])->prefix('photographer')->name('photographer.')->group(function () {
    Route::get('/dashboard', [PhotographerDashboardController::class, 'index'])->name('dashboard');
    // ... rute fotografer lainnya
    // Route::get('/schedule', ...)->name('schedule');
    // Route::get('/profile', ...)->name('profile');
    // Route::get('/rates', ...)->name('rates');
});