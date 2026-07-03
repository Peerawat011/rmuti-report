@extends('layouts.app')

@section('title', 'จัดการประกาศข่าวสาร')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h3 class="mb-1" style="color: #EF6C00;"><i class="bi bi-megaphone-fill"></i> จัดการประกาศข่าวสาร</h3>
            <p class="text-muted mb-0">ทั้งหมด {{ $announcements->total() }} ประกาศ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary"><i class="bi bi-speedometer2"></i> แดชบอร์ด</a>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-gov"><i class="bi bi-plus-circle-fill"></i> เพิ่มประกาศ</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 90px;">รูป</th>
                        <th>หัวข้อ</th>
                        <th>สถานะ</th>
                        <th>วันที่</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $a)
                        <tr>
                            <td>
                                @if($a->cover_image_url)
                                    <img src="{{ $a->cover_image_url }}" alt=""
                                         style="width: 70px; height: 50px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <div style="width:70px;height:50px;border-radius:6px;background:#FFF3E0;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-megaphone text-warning"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($a->title, 60) }}</div>
                                <small class="text-muted"><i class="bi bi-images"></i> {{ $a->images_count }} รูป</small>
                            </td>
                            <td>
                                @if($a->is_published)
                                    <span class="badge bg-success">เผยแพร่</span>
                                @else
                                    <span class="badge bg-secondary">ซ่อน</span>
                                @endif
                            </td>
                            <td><small>{{ optional($a->published_at ?? $a->created_at)->format('d/m/Y') }}</small></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('announcements.show', $a) }}" class="btn btn-sm btn-outline-secondary" title="ดู"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.announcements.edit', $a) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข"><i class="bi bi-pencil-square"></i></a>
                                <form action="{{ route('admin.announcements.destroy', $a) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('ยืนยันลบประกาศนี้? รูปภาพทั้งหมดจะถูกลบด้วย');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">ยังไม่มีประกาศ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $announcements->links() }}</div>

</div>
@endsection
