<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementImage;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * รายการประกาศทั้งหมด (รวมที่ซ่อน)
     */
    public function index()
    {
        $announcements = Announcement::with('images')
            ->withCount('images')
            ->latest()
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create', ['announcement' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $announcement = Announcement::create([
            'user_id'      => $request->user()->id,
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'link_url'     => $validated['link_url'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]);

        $this->storeImages($request, $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'เพิ่มประกาศเรียบร้อยแล้ว');
    }

    public function edit(Announcement $announcement)
    {
        $announcement->load('images');
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $this->validateData($request);

        $wasPublished = $announcement->is_published;
        $isPublished  = $request->boolean('is_published');

        $announcement->update([
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'link_url'     => $validated['link_url'] ?? null,
            'is_published' => $isPublished,
            // ตั้ง published_at ครั้งแรกที่เผยแพร่ ถ้ายังไม่เคยมี
            'published_at' => $isPublished
                ? ($announcement->published_at ?? now())
                : $announcement->published_at,
        ]);

        // ลบรูปที่ถูกติ๊กเลือก
        if ($request->filled('delete_images')) {
            AnnouncementImage::where('announcement_id', $announcement->id)
                ->whereIn('id', $request->input('delete_images'))
                ->get()
                ->each->delete(); // ผ่าน model event เพื่อลบไฟล์จริง
        }

        // เพิ่มรูปใหม่
        $this->storeImages($request, $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'แก้ไขประกาศเรียบร้อยแล้ว');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete(); // cascade ลบ images + ไฟล์จริง (ผ่าน model event)

        return redirect()->route('admin.announcements.index')
            ->with('success', 'ลบประกาศเรียบร้อยแล้ว');
    }

    /**
     * อัปโหลดรูปหลายไฟล์ → บันทึกลง storage + ตาราง
     */
    private function storeImages(Request $request, Announcement $announcement): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $maxSort = (int) $announcement->images()->max('sort_order');

        foreach ($request->file('images') as $file) {
            $filename = 'ann_' . $announcement->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('announcements', $filename, 'public');

            $announcement->images()->create([
                'path'       => $path,
                'sort_order' => ++$maxSort,
            ]);
        }
    }

    private function validateData(Request $request): array
    {
        // เติม https:// ให้อัตโนมัติ ถ้าผู้ใช้พิมพ์ URL มาโดยไม่มี scheme
        $link = trim((string) $request->input('link_url'));
        if ($link !== '' && !preg_match('#^https?://#i', $link)) {
            $link = 'https://' . $link;
        }
        $request->merge(['link_url' => $link ?: null]);

        return $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'link_url' => 'nullable|url|max:2000',
            'images'   => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
        ], [
            'title.required'   => 'กรุณากรอกหัวข้อประกาศ',
            'content.required' => 'กรุณากรอกเนื้อหาประกาศ',
            'link_url.url'     => 'รูปแบบ URL ไม่ถูกต้อง (เช่น https://www.example.com)',
            'images.*.image'   => 'ไฟล์ต้องเป็นรูปภาพ',
            'images.*.mimes'   => 'รองรับเฉพาะ JPG, PNG, WEBP',
            'images.*.max'     => 'ขนาดรูปต้องไม่เกิน 4 MB ต่อไฟล์',
        ]);
    }
}
