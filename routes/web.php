<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');

Route::middleware('lms.session')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/jadwal', [PageController::class, 'jadwal'])->name('jadwal');
    Route::get('/ujian', [PageController::class, 'ujian'])->name('ujian');
    Route::get('/nilai', [PageController::class, 'nilai'])->name('nilai');
    Route::get('/khs', [PageController::class, 'khs'])->name('khs');
    Route::get('/kmk', [PageController::class, 'kmk'])->name('kmk');
    Route::get('/kehadiran', [PageController::class, 'kehadiran'])->name('kehadiran');
    Route::get('/profil', [PageController::class, 'profil'])->name('profil');
    Route::get('/point-book', [PageController::class, 'pointBook'])->name('point-book');
    Route::get('/pengumuman', [PageController::class, 'pengumuman'])->name('pengumuman');
    Route::get('/pengumuman/{id}', [PageController::class, 'pengumumanDetail'])->whereNumber('id')->name('pengumuman.detail');
    Route::get('/photo', PhotoController::class)->name('photo');
});
