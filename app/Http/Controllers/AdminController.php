<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * แดชบอร์ดผู้ดูแลระบบ — สรุปสถิติผู้ใช้และรายงาน
     */
    public function dashboard()
    {
        $stats = [
            'users_total'       => User::count(),
            'users_staff'       => User::where('role', 'staff')->count(),
            'users_supervisor'  => User::where('role', 'supervisor')->count(),
            'users_admin'       => User::where('role', 'admin')->count(),

            'reports_total'     => Report::count(),
            'reports_draft'     => Report::where('status', 'draft')->count(),
            'reports_pending'   => Report::where('status', 'pending_signature')->count(),
            'reports_signed'    => Report::where('status', 'signed')->count(),
            'reports_approved'  => Report::where('status', 'approved')->count(),
        ];

        // รายงานล่าสุด 5 รายการ
        $recentReports = Report::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentReports'));
    }
}
