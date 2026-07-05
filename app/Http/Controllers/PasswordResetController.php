<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // ========== ฟอร์มกรอกอีเมล (ลืมรหัสผ่าน) ==========
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // ========== ส่งลิงก์รีเซ็ตไปที่อีเมล ==========
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email'    => 'รูปแบบอีเมลไม่ถูกต้อง',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        // แจ้งข้อความกลางๆ เสมอ — ไม่เปิดเผยว่าอีเมลนี้มีในระบบหรือไม่ (กันการเดาบัญชี)
        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors([
                'email' => 'ส่งลิงก์ไปแล้วเมื่อสักครู่ กรุณารอประมาณ 1 นาทีก่อนขอใหม่',
            ])->onlyInput('email');
        }

        return back()->with('success',
            'หากอีเมลนี้มีอยู่ในระบบ เราได้ส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ไปให้แล้ว กรุณาตรวจสอบกล่องจดหมาย');
    }

    // ========== ฟอร์มตั้งรหัสผ่านใหม่ (มาจากลิงก์ในอีเมล) ==========
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // ========== บันทึกรหัสผ่านใหม่ ==========
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required'     => 'กรุณากรอกอีเมล',
            'email.email'        => 'รูปแบบอีเมลไม่ถูกต้อง',
            'password.required'  => 'กรุณากรอกรหัสผ่านใหม่',
            'password.min'       => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านยืนยันไม่ตรงกัน',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'ตั้งรหัสผ่านใหม่เรียบร้อยแล้ว กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่');
        }

        return back()->withErrors([
            'email' => 'ลิงก์รีเซ็ตไม่ถูกต้องหรือหมดอายุแล้ว กรุณาขอลิงก์ใหม่อีกครั้ง',
        ])->onlyInput('email');
    }
}
