<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Livewire\Actions\Logout; // ✅ Tambahkan ini agar Logout bisa digunakan

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman welcome (utama)
Route::view('/', 'welcome');

// Dashboard
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Group auth untuk halaman yang dilindungi
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ✅ Tambahkan route logout POST agar tombol logout bekerja
    Route::post('/logout', function (Logout $logout) {
        $logout(); // Jalankan aksi logout dari class Logout
        return redirect('/login'); // Arahkan kembali ke halaman login
    })->name('logout');
});

// Route untuk menampilkan file bukti (gambar transaksi)
Route::get('/bukti/{filename}', function ($filename) {
    // Hindari path traversal
    $filename = str_replace(['..', "\\"], '', $filename);

    // Gunakan path relatif dari storage/app/public/
    $path = storage_path('app/public/' . ltrim($filename, '/'));

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('bukti.show');

// Auth routes (login, register, dll)
require __DIR__ . '/auth.php';
