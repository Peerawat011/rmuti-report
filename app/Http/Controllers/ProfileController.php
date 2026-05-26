<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // ========== แสดงหน้าโปรไฟล์ ==========
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    // ========== แสดงหน้าแก้ไขโปรไฟล์ ==========
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // ========== บันทึกการแก้ไขข้อมูลส่วนตัว ==========
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1) ตรวจสอบข้อมูล
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'position'   => 'required|string|max:150',
            'department' => 'required|string|max:200',
            'faculty'    => 'required|string|max:200',
        ], [
            'first_name.required' => 'กรุณากรอกชื่อ',
            'last_name.required'  => 'กรุณากรอกนามสกุล',
            'email.required'      => 'กรุณากรอกอีเมล',
            'email.email'         => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique'        => 'อีเมลนี้ถูกใช้งานแล้ว',
            'position.required'   => 'กรุณากรอกตำแหน่งงาน',
            'department.required' => 'กรุณาเลือกสังกัดสำนัก/สถาบัน/กอง',
            'faculty.required'    => 'กรุณาเลือกสังกัดคณะ',
        ]);

        // 2) อัปเดตข้อมูล
        $user->update($validated);

        // 3) Redirect กลับพร้อมข้อความสำเร็จ
        return redirect()->route('profile.show')
            ->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }

    // ========== เปลี่ยนรหัสผ่าน ==========
    public function updatePassword(Request $request)
    {
        // 1) ตรวจสอบข้อมูล
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed|different:current_password',
        ], [
            'current_password.required' => 'กรุณากรอกรหัสผ่านปัจจุบัน',
            'password.required'         => 'กรุณากรอกรหัสผ่านใหม่',
            'password.min'              => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed'        => 'รหัสผ่านยืนยันไม่ตรงกัน',
            'password.different'        => 'รหัสผ่านใหม่ต้องไม่เหมือนรหัสผ่านปัจจุบัน',
        ]);

        $user = Auth::user();

        // 2) ตรวจสอบรหัสผ่านปัจจุบัน
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง'
            ]);
        }

        // 3) อัปเดตรหัสผ่านใหม่ (จะถูก hash อัตโนมัติ)
        $user->update([
            'password' => $request->password,
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
    }

    // ========== อัปโหลดรูปโปรไฟล์ ==========
    public function updateAvatar(Request $request)
    {
        // 1) ตรวจสอบไฟล์
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'avatar.required' => 'กรุณาเลือกรูปภาพ',
            'avatar.image'    => 'ไฟล์ต้องเป็นรูปภาพ',
            'avatar.mimes'    => 'รองรับเฉพาะไฟล์ JPG, JPEG, PNG',
            'avatar.max'      => 'ขนาดไฟล์ต้องไม่เกิน 2 MB',
        ]);

        $user = Auth::user();

        // 2) ลบรูปเก่า (ถ้ามี)
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // 3) บันทึกรูปใหม่
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        // 4) อัปเดต database
        $user->update(['avatar' => $path]);

        return redirect()->route('profile.show')
            ->with('success', 'อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว');
    }

    // ========== ลบรูปโปรไฟล์ ==========
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            // ลบไฟล์จาก storage
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // ลบข้อมูลใน database
            $user->update(['avatar' => null]);
        }

        return redirect()->route('profile.show')
            ->with('success', 'ลบรูปโปรไฟล์เรียบร้อยแล้ว');
    }
}