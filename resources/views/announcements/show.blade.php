@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<style>
    .ann-carousel .carousel-control-prev-icon,
    .ann-carousel .carousel-control-next-icon {
        background-color: rgba(239, 108, 0, 0.85);
        border-radius: 50%;
        padding: 12px;
        background-size: 55%;
    }
    .ann-carousel .carousel-indicators [data-bs-target] {
        background-color: #EF6C00;
    }
</style>
<div class="container py-4" style="max-width: 820px;">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="link-gov">ข่าวประชาสัมพันธ์</a></li>
            <li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($announcement->title, 40) }}</li>
        </ol>
    </nav>

    <article class="card border-0 shadow-sm">
        <div class="card-body p-4">

            @unless($announcement->is_published)
                <div class="alert alert-warning py-2 small">
                    <i class="bi bi-eye-slash-fill"></i> ประกาศนี้ยังไม่เผยแพร่ (เห็นได้เฉพาะผู้ดูแลระบบ)
                </div>
            @endunless

            <h2 style="color: #EF6C00;">{{ $announcement->title }}</h2>

            <div class="text-muted small mb-4 border-bottom pb-3">
                <i class="bi bi-calendar3"></i>
                {{ optional($announcement->published_at ?? $announcement->created_at)->format('d/m/Y H:i') }}
                @if($announcement->author)
                    <span class="ms-3"><i class="bi bi-person-fill"></i> {{ $announcement->author->full_name }}</span>
                @endif
            </div>

            {{-- เนื้อหา (คงรูปแบบบรรทัด) --}}
            <div style="white-space: pre-wrap; line-height: 1.9; color: #4E342E;">{{ $announcement->content }}</div>

            {{-- ลิงก์เว็บไซต์ภายนอก --}}
            @if($announcement->link_url)
                <div class="mt-4">
                    <a href="{{ $announcement->link_url }}" target="_blank" rel="noopener noreferrer"
                       class="btn btn-gov">
                        <i class="bi bi-box-arrow-up-right"></i> ไปยังเว็บไซต์ที่เกี่ยวข้อง
                    </a>
                    <div class="small text-muted mt-1"><i class="bi bi-link-45deg"></i> {{ $announcement->link_url }}</div>
                </div>
            @endif

            {{-- รูปภาพประกอบ --}}
            @if($announcement->images->count() > 1)
                {{-- หลายรูป → สไลด์โชว์ --}}
                <hr class="my-4">
                <div id="annDetail{{ $announcement->id }}" class="carousel slide ann-carousel" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-indicators">
                        @foreach($announcement->images as $i => $img)
                            <button type="button" data-bs-target="#annDetail{{ $announcement->id }}" data-bs-slide-to="{{ $i }}"
                                    class="{{ $i === 0 ? 'active' : '' }}" aria-label="รูปที่ {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner rounded shadow-sm" style="background: #00000010;">
                        @foreach($announcement->images as $i => $img)
                            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                <a href="{{ $img->url }}" target="_blank" rel="noopener">
                                    <img src="{{ $img->url }}" alt="รูปประกอบ"
                                         class="d-block w-100" style="height: 460px; object-fit: contain;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#annDetail{{ $announcement->id }}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#annDetail{{ $announcement->id }}" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                <p class="text-center text-muted small mt-2"><i class="bi bi-images"></i> {{ $announcement->images->count() }} รูป — คลิกที่รูปเพื่อดูขนาดเต็ม</p>
            @elseif($announcement->images->count() === 1)
                {{-- รูปเดียว --}}
                <hr class="my-4">
                <a href="{{ $announcement->images->first()->url }}" target="_blank" rel="noopener">
                    <img src="{{ $announcement->images->first()->url }}" alt="รูปประกอบ"
                         class="img-fluid rounded shadow-sm d-block mx-auto" style="max-height: 460px;">
                </a>
            @endif

        </div>
    </article>

    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> กลับหน้าข่าว
        </a>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-square"></i> แก้ไขประกาศนี้
                </a>
            @endif
        @endauth
    </div>

</div>
@endsection
