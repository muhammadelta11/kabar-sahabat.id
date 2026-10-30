<?php

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

// Kirim artikel publik (tanpa login, dimoderasi admin)
Route::get('/kirim-artikel', [SubmissionController::class, 'create'])->name('submissions.create');
Route::post('/kirim-artikel', [SubmissionController::class, 'store'])
    ->middleware('throttle:3,60')
    ->name('submissions.store');
Route::post('/kirim-artikel/upload', [SubmissionController::class, 'upload'])
    ->middleware('throttle:60,60')
    ->name('submissions.upload');

// Settings management — protected by Filament auth
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/admin/settings/update', [SettingsController::class, 'update'])->name('admin.settings.update');
});
