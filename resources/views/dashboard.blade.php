@extends('layouts.app')

@section('title', 'ข่าวประชาสัมพันธ์และประกาศ')

@section('content')
<style>
    /* ปุ่มเลื่อนสไลด์ให้เห็นชัดบนรูปพื้นสว่าง */
    .ann-carousel .carousel-control-prev-icon,
    .ann-carousel .carousel-control-next-icon {
        background-color: rgba(239, 108, 0, 0.75);
        border-radius: 50%;
        padding: 10px;
        background-size: 55%;
    }
    .ann-carousel .carousel-indicators [data-bs-target] {
        background-color: #EF6C00;
    }
</style>
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1" style="color: #EF6C00;">
                <i class="bi bi-megaphone-fill"></i> ข่าวประชาสัมพันธ์และประกาศ
            </h3>
            <p class="text-muted mb-0">ติดตามข่าวสารและประกาศล่าสุดจากหน่วยงาน</p>
        </div>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-gov">
                    <i class="bi bi-gear-fill"></i> จัดการประกาศ
                </a>
            @endif
        @endauth
    </div>

    {{-- Feed การ์ดข่าว --}}
    @if($announcements->count() > 0)
        <div class="row g-4">
            @foreach($announcements as $a)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        @if($a->images->count() > 1)
                            {{-- หลายรูป → สไลด์ --}}
                            <div id="annCard{{ $a->id }}" class="carousel slide ann-carousel" data-bs-ride="carousel" data-bs-interval="4000">
                                <div class="carousel-indicators">
                                    @foreach($a->images as $i => $img)
                                        <button type="button" data-bs-target="#annCard{{ $a->id }}" data-bs-slide-to="{{ $i }}"
                                                class="{{ $i === 0 ? 'active' : '' }}" aria-label="รูปที่ {{ $i + 1 }}"></button>
                                    @endforeach
                                </div>
                                <div class="carousel-inner" style="border-top-left-radius: .375rem; border-top-right-radius: .375rem;">
                                    @foreach($a->images as $i => $img)
                                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                            <a href="{{ route('announcements.show', $a) }}">
                                                <img src="{{ $img->url }}" alt="{{ $a->title }}"
                                                     class="d-block w-100" style="height: 190px; object-fit: cover;">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#annCard{{ $a->id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#annCard{{ $a->id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        @elseif($a->cover_image_url)
                            <a href="{{ route('announcements.show', $a) }}" class="text-decoration-none">
                                <img src="{{ $a->cover_image_url }}" alt="{{ $a->title }}"
                                     style="height: 190px; object-fit: cover; border-top-left-radius: .375rem; border-top-right-radius: .375rem;"
                                     class="card-img-top">
                            </a>
                        @else
                            <a href="{{ route('announcements.show', $a) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center justify-content-center"
                                     style="height: 190px; background: linear-gradient(135deg, #FFB74D 0%, #EF6C00 100%); border-top-left-radius: .375rem; border-top-right-radius: .375rem;">
                                    <i class="bi bi-megaphone-fill text-white" style="font-size: 3rem; opacity: .8;"></i>
                                </div>
                            </a>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted mb-1">
                                <i class="bi bi-calendar3"></i>
                                {{ optional($a->published_at ?? $a->created_at)->format('d/m/Y') }}
                                @if($a->images_count ?? $a->images->count())
                                    <span class="ms-2"><i class="bi bi-images"></i> {{ $a->images->count() }} รูป</span>
                                @endif
                            </small>
                            <h5 class="card-title" style="color: #4E342E;">
                                <a href="{{ route('announcements.show', $a) }}" class="text-decoration-none" style="color: inherit;">
                                    {{ $a->title }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small flex-grow-1">{{ $a->excerpt }}</p>
                            <div class="d-flex align-items-center justify-content-between mt-2 flex-wrap gap-2">
                                <a href="{{ route('announcements.show', $a) }}" class="link-gov">
                                    อ่านต่อ <i class="bi bi-arrow-right"></i>
                                </a>
                                @if($a->link_url)
                                    <a href="{{ $a->link_url }}" target="_blank" rel="noopener noreferrer"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-box-arrow-up-right"></i> ไปยังเว็บไซต์
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $announcements->links() }}</div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">ยังไม่มีประกาศข่าวสารในขณะนี้</p>
            </div>
        </div>
    @endif

</div>
@endsection
