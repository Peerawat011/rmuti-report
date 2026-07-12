<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;



    // หน้าแรก → redirect ไป login
    Route::get('/', function () {
    return redirect()->route('login');
    });

    // ===== เสิร์ฟรูปสาธารณะ (avatar/ประกาศ) จาก database =====
    // ไฟล์บนดิสก์หายเมื่อ Render restart — URL /storage/... เดิมจะตกมาที่ route นี้แล้วอ่านจาก TiDB แทน
    Route::get('/storage/{path}', [\App\Http\Controllers\FileController::class, 'show'])
        ->where('path', '.*')
        ->name('files.show');

    // ===== Routes สำหรับคนที่ยังไม่ล็อกอิน =====
    Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // ===== ลืมรหัสผ่าน (รีเซ็ตผ่านอีเมลที่สมัคร) =====
    Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'reset'])
        ->name('password.update');
    });

        // ===== Routes สำหรับคนที่ล็อกอินแล้ว =====
        Route::middleware('auth')->group(function () {
        // หน้าแรก = กระดานข่าวประชาสัมพันธ์
        Route::get('/dashboard', [AnnouncementController::class, 'index'])->name('dashboard');

        // หน้ารายละเอียดประกาศ
        Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])
            ->name('announcements.show');

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // ===== Profile Routes  =====
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // ===== Avatar Routes  =====
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // ===== ลายเซ็นประจำตัว + เสิร์ฟรูปลายเซ็นแบบตรวจสิทธิ์ =====
        Route::post('/profile/signature', [\App\Http\Controllers\UserSignatureController::class, 'store'])
            ->name('profile.signature.store');
        Route::get('/profile/signature/preview', [\App\Http\Controllers\UserSignatureController::class, 'myPreview'])
            ->name('profile.signature.preview');
        Route::get('/signatures/{signature}/image', [\App\Http\Controllers\UserSignatureController::class, 'reportImage'])
            ->name('signatures.image');


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

        // ===== Admin Routes =====
        Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
            // แดชบอร์ดผู้ดูแลระบบ
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

            // จัดการผู้ใช้ (CRUD)
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
                ->except(['show']);

            // จัดการรายงานทั้งหมด (CRUD) — ดูรายละเอียด/PDF ใช้ route reports.show / reports.pdf เดิม
            Route::resource('reports', \App\Http\Controllers\Admin\ReportController::class)
                ->except(['show']);

            // จัดการประกาศข่าวสาร (CRUD)
            Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)
                ->except(['show']);
        });

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

        // ===== ตีกลับให้แก้ไข =====
        Route::post('/supervisor/reports/{report}/reject',
        [\App\Http\Controllers\SupervisorController::class, 'reject'])
        ->name('supervisor.reject');

        

});
});