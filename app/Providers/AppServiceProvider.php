<?php

namespace App\Providers;

use App\Models\Report;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // อีเมลรีเซ็ตรหัสผ่านเป็นภาษาไทย
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]);

            return (new MailMessage)
                ->subject('ตั้งรหัสผ่านใหม่ — ระบบบริการบุคลากร มทร.อีสาน วิทยาเขตขอนแก่น')
                ->greeting('สวัสดี คุณ' . $notifiable->first_name)
                ->line('เราได้รับคำขอตั้งรหัสผ่านใหม่สำหรับบัญชีของคุณ')
                ->action('ตั้งรหัสผ่านใหม่', $url)
                ->line('ลิงก์นี้จะหมดอายุภายใน 60 นาที')
                ->line('หากคุณไม่ได้เป็นผู้ขอ กรุณาเพิกเฉยต่ออีเมลฉบับนี้ — รหัสผ่านของคุณจะไม่ถูกเปลี่ยน')
                ->salutation('ระบบบริการบุคลากร มทร.อีสาน วิทยาเขตขอนแก่น');
        });

        // แชร์จำนวน "รายงานที่รอฉันลงนาม" + "รายงานถูกส่งกลับแก้ไข" ให้ layout ทุกหน้า
        View::composer('layouts.app', function ($view) {
            $pendingSignCount = 0;
            $revisionCount = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $userId = $user->id;

                // 1) รอฉันเซ็นในฐานะผู้บังคับบัญชา (ถึงคิวตาม workflow แล้วเท่านั้น)
                if ($user->isSupervisor()) {
                    $candidates = Report::where(function ($q) use ($userId) {
                            $q->where('supervisor_1_id', $userId)
                              ->orWhere('supervisor_2_id', $userId)
                              ->orWhere('supervisor_3_id', $userId);
                        })
                        ->whereNotIn('status', ['draft', 'approved', 'revision'])
                        ->get();

                    $pendingSignCount += $candidates
                        ->filter(fn ($report) => $report->canSign($userId))
                        ->count();
                }

                // 2) รายงานของฉันที่ฉัน (ผู้รายงาน) ยังไม่ได้ลงนาม
                //    (ไม่นับที่ถูกส่งกลับแก้ไข — ไปโผล่ใน revisionCount แทน)
                $pendingSignCount += Report::where('user_id', $userId)
                    ->where('status', '!=', 'revision')
                    ->whereDoesntHave('signatures', fn ($q) => $q->where('role', 'reporter'))
                    ->count();

                // 3) รายงานของฉันที่ถูกส่งกลับให้แก้ไข
                $revisionCount = Report::where('user_id', $userId)
                    ->where('status', 'revision')
                    ->count();
            }

            $view->with('pendingSignCount', $pendingSignCount)
                 ->with('revisionCount', $revisionCount);
        });
    }
}
