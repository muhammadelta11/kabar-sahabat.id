<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContributorAccountController;
use App\Http\Controllers\ContributorSubmissionController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');

// Halaman informasi redaksi
Route::view('/redaksi', 'pages.redaksi')->name('pages.redaksi');
Route::view('/tentang-kami', 'pages.tentang-kami')->name('pages.tentang-kami');
Route::view('/kontak', 'pages.kontak')->name('pages.kontak');
Route::view('/pedoman-media-siber', 'pages.pedoman-media-siber')->name('pages.pedoman-media-siber');
Route::permanentRedirect('/profil-perusahaan', '/tentang-kami');

// Sitemap & RSS
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-posts.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/sitemap-static.xml', [SitemapController::class, 'static'])->name('sitemap.static');
Route::get('/feed.xml', [SitemapController::class, 'feed'])->name('sitemap.feed');

// Auth publik — /masuk boleh saat sudah login agar bisa ganti akun
Route::get('/masuk', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('auth.login.submit');

Route::middleware('guest')->group(function () {
    Route::get('/bergabung', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/bergabung', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('auth.register.submit');
});

Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');

Route::get('/menunggu-persetujuan', [AuthController::class, 'showPending'])
    ->middleware('auth')
    ->name('auth.pending');

// Akun kontributor: profil + logout (juga untuk status pending)
Route::middleware('auth')->group(function () {
    Route::get('/akun', [ContributorAccountController::class, 'edit'])->name('account.edit');
    Route::put('/akun', [ContributorAccountController::class, 'update'])
        ->middleware('throttle:10,1')
        ->name('account.update');
});

// Kirim artikel: wajib login sebagai kontributor aktif
Route::middleware(['auth', 'can.submit'])->group(function () {
    Route::get('/kirim-artikel', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/kirim-artikel', [SubmissionController::class, 'store'])
        ->middleware('throttle:3,60')
        ->name('submissions.store');
    Route::post('/kirim-artikel/upload', [SubmissionController::class, 'upload'])
        ->middleware('throttle:60,60')
        ->name('submissions.upload');
    Route::get('/pengajuan-saya', [ContributorSubmissionController::class, 'index'])->name('submissions.index');
});

// Settings management — protected by Filament auth
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/admin/settings/update', [SettingsController::class, 'update'])->name('admin.settings.update');
});
