<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;



    // หน้าแรก → redirect ไป login
    Route::get('/', function () {
    return redirect()->route('login');
    });

    // ===== Routes สำหรับคนที่ยังไม่ล็อกอิน =====
    Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    });

        // ===== Routes สำหรับคนที่ล็อกอินแล้ว =====
        Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
        return view('dashboard');
        })->name('dashboard');

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // ===== Profile Routes  =====
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // ===== Avatar Routes  =====
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');


        // ===== Signature Routes  =====
        Route::get('/reports/{report}/sign', [\App\Http\Controllers\SignatureController::class, 'create'])
        ->name('reports.sign');
        Route::post('/reports/{report}/sign', [\App\Http\Controllers\SignatureController::class, 'store'])
        ->name('reports.sign.store');
        Route::delete('/reports/{report}/sign/{signature}', [\App\Http\Controllers\SignatureController::class, 'destroy'])
        ->name('reports.sign.delete');

        // ===== Export PDF Route =====
        Route::get('/reports/{report}/pdf', [ReportController::class, 'downloadPDF'])
        ->name('reports.pdf');

        // ===== Report Routes  =====
        Route::resource('reports', ReportController::class);

        // ===== Supervisor Routes (Phase 3B) =====
        Route::middleware(['auth', 'role:supervisor'])->group(function () {
        Route::get('/supervisor/inbox', [\App\Http\Controllers\SupervisorController::class, 'inbox'])
        ->name('supervisor.inbox');

        // ===== ลงนาม (ใหม่!) =====
        Route::get('/supervisor/reports/{report}/sign',
        [\App\Http\Controllers\SupervisorController::class, 'signForm'])
        ->name('supervisor.sign');
        Route::post('/supervisor/reports/{report}/sign',
        [\App\Http\Controllers\SupervisorController::class, 'sign'])
        ->name('supervisor.sign.store');

        

});
});