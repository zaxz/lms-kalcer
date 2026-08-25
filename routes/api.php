<?php

use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [ApiController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('lms.session')->group(function () {
        Route::post('/auth/logout', [ApiController::class, 'logout']);
        Route::get('/dashboard', [ApiController::class, 'dashboard']);
        Route::get('/profile', [ApiController::class, 'profile']);
        Route::get('/schedule', [ApiController::class, 'schedule']);
        Route::get('/attendance', [ApiController::class, 'attendance']);
        Route::get('/attendance/{courseCode}/{classCode}', [ApiController::class, 'attendanceDetail']);
        Route::get('/exams', [ApiController::class, 'exams']);
        Route::get('/grades/cumulative', [ApiController::class, 'gradesCumulative']);
        Route::get('/grades/khs', [ApiController::class, 'gradesKhs']);
        Route::get('/kmk', [ApiController::class, 'kmk']);
        Route::get('/pointbook', [ApiController::class, 'pointbook']);
        Route::get('/announcements', [ApiController::class, 'announcements']);
        Route::get('/announcements/{id}', [ApiController::class, 'announcementDetail'])->whereNumber('id');
    });
});
