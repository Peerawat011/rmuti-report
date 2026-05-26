<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ========== แสดงหน้า Login ==========
    public function showLogin()
    {
        return view('auth.login');
    }

    // ========== ประมวลผลการ Login ==========
    public function login(Request $request)
    {
        // 1) ตรวจสอบข้อมูลที่กรอกมา
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
        ]);

        // 2) พยายามล็อกอินด้วย email + password
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->with('success', 'เข้าสู่ระบบสำเร็จ');
        }

        // 3) ถ้าไม่ตรง → กลับไปหน้า Login พร้อมข้อความ error
        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('email');
    }

    // ========== แสดงหน้า Register ==========
    public function showRegister()
    {
        return view('auth.register');
    }

    // ========== ประมวลผลการ Register ==========
    public function register(Request $request)
{
    // 1) ตรวจสอบข้อมูล
    $validated = $request->validate([
        'first_name' => 'required|string|max:100',
        'last_name'  => 'required|string|max:100',
        'email'      => 'required|email|unique:users,email',
        'position'   => 'required|string|max:150',
        'department' => 'required|string|max:200',
        'faculty'    => 'required|string|max:200',
        'role'       => 'required|in:staff,supervisor',
        'password'   => 'required|min:6|confirmed',
    ], [
        'first_name.required' => 'กรุณากรอกชื่อ',
        'last_name.required'  => 'กรุณากรอกนามสกุล',
        'email.required'      => 'กรุณากรอกอีเมล',
        'email.email'         => 'รูปแบบอีเมลไม่ถูกต้อง',
        'email.unique'        => 'อีเมลนี้ถูกใช้งานแล้ว',
        'position.required'   => 'กรุณากรอกตำแหน่งงาน',
        'department.required' => 'กรุณาเลือกสังกัดสำนัก/สถาบัน/กอง',
        'faculty.required'    => 'กรุณาเลือกสังกัดคณะ',
        'role.required'       => 'กรุณาเลือกประเภทผู้ใช้งาน',
        'password.required'   => 'กรุณากรอกรหัสผ่าน',
        'password.min'        => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
        'password.confirmed'  => 'รหัสผ่านยืนยันไม่ตรงกัน',
    ]);

    // 2) สร้าง User ใหม่
    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name'  => $validated['last_name'],
        'email'      => $validated['email'],
        'position'   => $validated['position'],
        'department' => $validated['department'],
        'faculty'    => $validated['faculty'],
        'role'       => $validated['role'],
        'password'   => $validated['password'],
    ]);

    // 3) ล็อกอินอัตโนมัติ + ไปหน้า dashboard
    Auth::login($user);
    return redirect()->route('dashboard')
        ->with('success', 'ลงทะเบียนสำเร็จ ยินดีต้อนรับเข้าสู่ระบบ!');
}

    // ========== Logout ==========
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->with('success', 'ออกจากระบบเรียบร้อย');
    }
}