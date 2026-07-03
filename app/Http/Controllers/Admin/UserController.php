<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /** role ที่อนุญาตให้แอดมินกำหนดได้ */
    private const ROLES = ['staff', 'supervisor', 'admin'];

    /**
     * รายชื่อผู้ใช้ทั้งหมด — ค้นหา + กรองตาม role
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $role   = $request->input('role', '');

        $users = User::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
                });
            })
            ->when(in_array($role, self::ROLES, true), fn ($q) => $q->where('role', $role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    /**
     * ฟอร์มเพิ่มผู้ใช้ใหม่
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * บันทึกผู้ใช้ใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->rules(),
            $this->messages()
        );

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'เพิ่มผู้ใช้ใหม่เรียบร้อยแล้ว');
    }

    /**
     * ฟอร์มแก้ไขผู้ใช้
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * อัปเดตข้อมูลผู้ใช้ (รหัสผ่านเว้นว่างได้ = ไม่เปลี่ยน)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate(
            $this->rules($user->id, passwordRequired: false),
            $this->messages()
        );

        // กันแอดมินเปลี่ยน role ตัวเอง (กันล็อกตัวเองออกจากระบบ)
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->withInput()
                ->withErrors(['role' => 'ไม่สามารถเปลี่ยนสิทธิ์ของบัญชีตัวเองได้']);
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'อัปเดตข้อมูลผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * ลบผู้ใช้ (ห้ามลบตัวเอง)
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * กฎ validation (ใช้ร่วมกันทั้ง store/update)
     */
    private function rules(?int $ignoreId = null, bool $passwordRequired = true): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($ignoreId)],
            'position'   => 'required|string|max:150',
            'department' => 'required|string|max:200',
            'faculty'    => 'required|string|max:200',
            'role'       => ['required', Rule::in(self::ROLES)],
            'password'   => ($passwordRequired ? 'required' : 'nullable') . '|min:6|confirmed',
        ];
    }

    private function messages(): array
    {
        return [
            'first_name.required' => 'กรุณากรอกชื่อ',
            'last_name.required'  => 'กรุณากรอกนามสกุล',
            'email.required'      => 'กรุณากรอกอีเมล',
            'email.email'         => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique'        => 'อีเมลนี้ถูกใช้งานแล้ว',
            'position.required'   => 'กรุณากรอกตำแหน่งงาน',
            'department.required' => 'กรุณากรอกสังกัดสำนัก/สถาบัน/กอง',
            'faculty.required'    => 'กรุณากรอกสังกัดคณะ',
            'role.required'       => 'กรุณาเลือกประเภทผู้ใช้งาน',
            'role.in'             => 'ประเภทผู้ใช้งานไม่ถูกต้อง',
            'password.required'   => 'กรุณากรอกรหัสผ่าน',
            'password.min'        => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed'  => 'รหัสผ่านยืนยันไม่ตรงกัน',
        ];
    }
}
