<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * หน้าแรก (Dashboard) — กระดานข่าวประชาสัมพันธ์
     */
    public function index()
    {
        $announcements = Announcement::published()
            ->with('images', 'author')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('dashboard', compact('announcements'));
    }

    /**
     * หน้ารายละเอียดประกาศ
     */
    public function show(Announcement $announcement)
    {
        // ประกาศที่ยังไม่เผยแพร่ → เห็นได้เฉพาะแอดมิน
        if (!$announcement->is_published && !(Auth::check() && Auth::user()->isAdmin())) {
            abort(404);
        }

        $announcement->load('images', 'author');

        return view('announcements.show', compact('announcement'));
    }
}
