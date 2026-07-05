<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FFFFFF">
    <title>@yield('title', 'ระบบบริการบุคลากร') | มทร.อีสาน วิทยาเขตขอนแก่น</title>
    {{-- Favicon (ไอคอน tab เบราว์เซอร์) --}}
<link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rmuti.ico') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rmuti.ico') }}">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Google Font ภาษาไทย --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
    /* ================================================================
       Premium Design System — ธีมส้ม มทร. (เรียบหรู)
       ================================================================ */
    :root {
        /* Brand */
        --gov-orange-dark: #E65100;
        --gov-orange: #F57C00;
        --gov-orange-light: #FFB74D;
        --gov-orange-bg: #FFF1E4;
        --gov-gold: #FFC107;

        /* Neutrals (โทนอุ่น อ่านสบาย) */
        --ink: #2B2320;
        --ink-soft: #6B5F57;
        --ink-faint: #9C918A;
        --line: #EDE6DF;
        --bg: #F7F4F1;
        --surface: #FFFFFF;
        --gov-gray: var(--bg);
        --gov-text: var(--ink);

        /* Shape & Depth */
        --radius: 16px;
        --radius-sm: 10px;
        --shadow-xs: 0 1px 2px rgba(43, 35, 32, .05);
        --shadow-sm: 0 2px 12px rgba(43, 35, 32, .06);
        --shadow-md: 0 12px 32px rgba(43, 35, 32, .10);
        --shadow-brand: 0 6px 20px rgba(230, 81, 0, .22);
    }

    html { scroll-behavior: smooth; }

    body {
        font-family: 'IBM Plex Sans Thai', 'Sarabun', sans-serif;
        background: var(--bg);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        color: var(--ink);
        -webkit-font-smoothing: antialiased;
    }

    h1, h2, h3, h4, h5, h6 { font-weight: 600; letter-spacing: -0.01em; }

    /* ===== Navbar (ขาว แบบ glass, ติดบนเสมอ) ===== */
    .navbar-gov {
        background: rgba(255, 255, 255, .88);
        -webkit-backdrop-filter: blur(14px);
        backdrop-filter: blur(14px);
        border-bottom: 1px solid var(--line);
        box-shadow: var(--shadow-xs);
        padding-top: .6rem;
        padding-bottom: .6rem;
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    .navbar-gov .navbar-brand { color: var(--ink) !important; }
    .navbar-gov .nav-link { color: var(--ink-soft) !important; }
    .navbar-gov .nav-link:hover { color: var(--gov-orange-dark) !important; }

    .brand-logo {
        width: 44px;
        height: 44px;
        background: var(--gov-orange-bg);
        border-radius: 50%;
        padding: 5px;
        object-fit: contain;
    }

    .brand-title { font-size: 1rem; font-weight: 700; color: var(--ink); line-height: 1.2; }
    .brand-sub { font-size: .72rem; color: var(--ink-faint); letter-spacing: .04em; }

    /* ปุ่มโปรไฟล์ผู้ใช้ (pill) */
    .btn-user {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 999px;
        padding: .3rem .85rem .3rem .35rem;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        color: var(--ink);
        font-weight: 500;
        box-shadow: var(--shadow-xs);
        transition: all .2s ease;
    }

    .btn-user:hover, .btn-user:focus {
        border-color: var(--gov-orange);
        color: var(--gov-orange-dark);
        box-shadow: var(--shadow-sm);
    }

    /* ===== จุดแจ้งเตือนรายงานรอลงนาม ===== */
    .notif-dot {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 19px;
        height: 19px;
        padding: 0 5px;
        background: #DC3545;
        color: #ffffff;
        font-size: .68rem;
        font-weight: 700;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--surface);
        box-shadow: 0 2px 6px rgba(220, 53, 69, .4);
        animation: notif-pulse 2s ease-in-out infinite;
    }

    @keyframes notif-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.12); }
    }

    .menu-badge {
        background: #DC3545;
        color: #fff;
        font-size: .7rem;
        font-weight: 600;
        border-radius: 999px;
        padding: .1rem .5rem;
        margin-left: auto;
    }

    /* Toast แจ้งเตือน */
    .sign-toast {
        background: var(--surface);
        border-left: 4px solid #DC3545;
        border-radius: 12px;
        overflow: hidden;
    }

    /* ===== Auth (login / register) ===== */
    .auth-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem;
    }

    .auth-card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--line);
        overflow: hidden;
        width: 100%;
        max-width: 460px;
    }

    .auth-header {
        background: linear-gradient(135deg, var(--gov-orange) 0%, var(--gov-orange-dark) 100%);
        color: #ffffff;
        padding: 1.75rem 1.5rem;
        text-align: center;
    }

    .auth-body { padding: 2rem; }

    /* ===== Buttons ===== */
    .btn { border-radius: var(--radius-sm); font-weight: 500; }

    .btn-gov {
        background: linear-gradient(135deg, var(--gov-orange) 0%, var(--gov-orange-dark) 100%);
        color: #ffffff;
        font-weight: 600;
        padding: .6rem 1.3rem;
        border: none;
        box-shadow: var(--shadow-brand);
        transition: all .2s ease;
    }

    .btn-gov:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 10px 26px rgba(230, 81, 0, .30);
        filter: brightness(1.05);
    }

    .btn-gov:active { transform: translateY(0); }

    .btn-outline-primary {
        color: var(--gov-orange-dark) !important;
        border-color: #F3C89F !important;
        background: var(--surface);
        transition: all .2s ease;
    }

    .btn-outline-primary:hover {
        background: var(--gov-orange) !important;
        border-color: var(--gov-orange) !important;
        color: #ffffff !important;
    }

    .text-primary { color: var(--gov-orange) !important; }

    /* ===== Cards (ทั้งระบบ) ===== */
    .card {
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm) !important;
        transition: box-shadow .25s ease, transform .25s ease;
    }

    .card-header {
        border-top-left-radius: var(--radius) !important;
        border-top-right-radius: var(--radius) !important;
        border-bottom-color: var(--line) !important;
        padding-top: .9rem;
        padding-bottom: .9rem;
    }

    .card-header h5 { color: var(--gov-orange-dark) !important; }

    /* การ์ดข่าว/ลิงก์ ให้เด้งนุ่มๆ ตอน hover */
    .card:has(a.stretched-link):hover,
    .row.g-4 .card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md) !important;
    }

    /* ===== Forms ===== */
    .form-control, .form-select {
        border-radius: var(--radius-sm);
        border-color: var(--line);
        padding: .55rem .85rem;
        color: var(--ink);
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--gov-orange);
        box-shadow: 0 0 0 .22rem rgba(245, 124, 0, .12);
    }

    .form-label { font-weight: 500; color: var(--ink-soft); }

    .input-group-text {
        background: var(--gov-orange-bg);
        color: var(--gov-orange-dark);
        border-color: var(--line);
        border-right: none;
        border-radius: var(--radius-sm);
    }

    /* ===== Tables ===== */
    .table { --bs-table-color: var(--ink); margin-bottom: 0; }

    .table thead th {
        font-size: .8rem;
        font-weight: 600;
        color: var(--ink-soft);
        letter-spacing: .03em;
        border-bottom-width: 1px;
        padding-top: .8rem;
        padding-bottom: .8rem;
        white-space: nowrap;
    }

    .table-light { --bs-table-bg: #FBF8F4; }
    .table-hover > tbody > tr:hover > * { --bs-table-accent-bg: #FFF6EC; }
    .table td { vertical-align: middle; }

    /* ===== Dropdown ===== */
    .dropdown-menu {
        border: 1px solid var(--line);
        border-radius: 14px;
        box-shadow: var(--shadow-md);
        padding: .5rem;
        min-width: 230px;
    }

    .dropdown-item {
        border-radius: 9px;
        padding: .5rem .8rem;
        font-weight: 500;
        color: var(--ink-soft);
        transition: all .15s ease;
    }

    .dropdown-item:hover { background: var(--gov-orange-bg); color: var(--gov-orange-dark); }
    .dropdown-item i { width: 1.3rem; display: inline-block; }

    /* ===== Badge / Alert / Breadcrumb / Pagination ===== */
    .badge { border-radius: 999px; font-weight: 500; padding: .45em .85em; letter-spacing: .02em; }

    .alert { border-radius: var(--radius-sm); border: none; box-shadow: var(--shadow-xs); }

    .alert.alert-info {
        background-color: var(--gov-orange-bg);
        color: #BF360C;
    }

    .breadcrumb { font-size: .9rem; }
    .breadcrumb-item.active { color: var(--gov-orange-dark); font-weight: 500; }

    .pagination { --bs-pagination-color: var(--gov-orange-dark); gap: .25rem; }
    .page-link { border-radius: 9px !important; border-color: var(--line); color: var(--gov-orange-dark); }
    .page-item.active .page-link { background: var(--gov-orange); border-color: var(--gov-orange); }
    .page-link:focus { box-shadow: 0 0 0 .2rem rgba(245, 124, 0, .15); }

    /* ===== Links ===== */
    a.link-gov {
        color: var(--gov-orange-dark);
        text-decoration: none;
        font-weight: 600;
        transition: color .15s ease;
    }

    a.link-gov:hover { color: var(--gov-orange); }

    /* ===== Footer (เข้ม หรู) ===== */
    .footer-gov {
        background: #241B15;
        color: rgba(255, 255, 255, .78);
        padding: 1.75rem 0;
        font-size: .9rem;
        border-top: 3px solid var(--gov-orange);
        margin-top: 3rem;
    }

    .footer-gov .footer-title { color: #ffffff; font-weight: 600; }
    .footer-gov a { color: var(--gov-orange-light); text-decoration: none; }
    .footer-gov a:hover { color: var(--gov-gold); }

    /* ===== Scrollbar (เรียบ) ===== */
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #D8CFC7; border-radius: 99px; border: 2px solid var(--bg); }
    ::-webkit-scrollbar-thumb:hover { background: var(--gov-orange-light); }

    /* ================================================================
       Responsive — ใช้งานได้ทุกขนาดจอ
       ================================================================ */
    @media (max-width: 991.98px) {
        .auth-card { max-width: 540px; }
    }

    @media (max-width: 767.98px) {
        h2 { font-size: 1.45rem; }
        h3 { font-size: 1.3rem; }
        h4 { font-size: 1.15rem; }

        .auth-body { padding: 1.5rem 1.25rem; }
        .container { --bs-gutter-x: 1.5rem; }

        /* ตารางเลื่อนแนวนอนได้ ไม่ล้นจอ */
        .card .table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 575.98px) {
        .brand-title { font-size: .9rem; }
        .brand-sub { font-size: .65rem; }
        .brand-logo { width: 38px; height: 38px; }

        /* หัวข้อหน้า + ปุ่ม ให้ขึ้นบรรทัดใหม่แทนการบีบ */
        .d-flex.justify-content-between { flex-wrap: wrap; gap: .6rem; }

        .btn { padding-left: .9rem; padding-right: .9rem; }
        .footer-gov { text-align: center; }
    }
</style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-gov">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
    <img src="{{ asset('images/logo-rmuti.png') }}" alt="RMUTI Logo" class="brand-logo">
    <div>
        <div class="brand-title">ระบบบริการบุคลากร</div>
        <div class="brand-sub">Personnel Development Report</div>
    </div>
</a>

    @auth
    <div class="dropdown">
        <button class="btn-user dropdown-toggle position-relative"
                type="button" data-bs-toggle="dropdown" aria-expanded="false">
            @php $notifTotal = ($pendingSignCount ?? 0) + ($revisionCount ?? 0) + ($signedNewsCount ?? 0); @endphp
            @if($notifTotal > 0)
                <span class="notif-dot">{{ $notifTotal > 9 ? '9+' : $notifTotal }}</span>
            @endif
            @if(Auth::user()->avatar_url)
    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar"
         style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
    @else
    <div style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #FFC107, #F57C00); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px;">
        {{ Auth::user()->initials }}
    </div>
    @endif
            <span class="d-none d-sm-inline">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li class="px-3 py-2">
                <small class="text-muted d-block">เข้าสู่ระบบในชื่อ</small>
                <strong style="color: #EF6C00;">{{ Auth::user()->email }}</strong>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('profile.show') }}">
                    <i class="bi bi-person-circle text-primary"></i> โปรไฟล์ของฉัน
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="bi bi-pencil-square text-primary"></i> แก้ไขข้อมูล
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 text-primary"></i> หน้าหลัก
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('reports.index') }}">
                    <i class="bi bi-file-earmark-text-fill text-primary"></i> รายงานของฉัน
                    @if(($revisionCount ?? 0) > 0)
                        <span class="menu-badge" title="มีรายงานถูกส่งกลับให้แก้ไข">{{ $revisionCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('reports.index', ['filter' => 'signed']) }}">
                    <i class="bi bi-patch-check-fill text-success"></i> รายงานที่ลงนามแล้ว
                    @if(($signedNewsCount ?? 0) > 0)
                        <span class="menu-badge" style="background: #198754;" title="มีการลงนามใหม่ที่ยังไม่ได้ดู">{{ $signedNewsCount }}</span>
                    @endif
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
           
            @if(Auth::user()->isSupervisor())
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('supervisor.inbox') }}">
                        <i class="bi bi-inbox-fill" style="color: #F57C00;"></i>
                            กล่องรายงานที่รอลงนาม
                        @if(($pendingSignCount ?? 0) > 0)
                            <span class="menu-badge">{{ $pendingSignCount }}</span>
                        @endif
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            @endif

            @if(Auth::user()->isAdmin())
                <li>
                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 text-danger"></i>
                            แดชบอร์ดผู้ดูแลระบบ
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people-fill text-danger"></i>
                            จัดการผู้ใช้
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.reports.index') }}">
                        <i class="bi bi-file-earmark-text-fill text-danger"></i>
                            จัดการรายงานทั้งหมด
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.announcements.index') }}">
                        <i class="bi bi-megaphone-fill text-danger"></i>
                            จัดการประกาศข่าวสาร
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            @endif
            
            
            <li>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                    </button>
                </form>
            </li>
            
        </ul>
    </div>
@endauth
        </div>
    </nav>

    {{-- Main Content --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="footer-gov mt-auto">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo-rmuti.png') }}" alt="RMUTI"
                         style="width: 36px; height: 36px; object-fit: contain; background: rgba(255,255,255,.1); border-radius: 50%; padding: 4px;">
                    <div>
                        <div class="footer-title">ระบบบริการบุคลากร</div>
                        <div style="font-size: .78rem; opacity: .7;">มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น</div>
                    </div>
                </div>
                <div class="text-sm-end">
                    <div>© {{ date('Y') }} สงวนลิขสิทธิ์</div>
                    <a href="https://www.kkc.rmuti.ac.th/" target="_blank" rel="noopener" style="font-size: .8rem;">www.kkc.rmuti.ac.th</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Signature Pad library (สำหรับวาดลายเซ็น) --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    {{-- Toast แจ้งเตือน: รายงานรอลงนาม / ถูกส่งกลับแก้ไข / มีการลงนามใหม่ --}}
    @auth
    @if((($pendingSignCount ?? 0) + ($revisionCount ?? 0) + ($signedNewsCount ?? 0)) > 0)
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="pendingSignToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="sign-toast d-flex align-items-start">
                <div class="toast-body">
                    @if(($revisionCount ?? 0) > 0)
                        <div class="fw-bold mb-1 text-danger">
                            <i class="bi bi-arrow-return-left"></i>
                            มีรายงานถูกส่งกลับให้แก้ไข {{ $revisionCount }} ฉบับ
                        </div>
                        <div class="small text-muted mb-2">กรุณาเปิดดูเหตุผล แก้ไข แล้วลงนามส่งใหม่</div>
                    @endif
                    @if(($pendingSignCount ?? 0) > 0)
                        <div class="fw-bold mb-1">
                            <i class="bi bi-vector-pen text-danger"></i>
                            มีรายงานรอลงนาม {{ $pendingSignCount }} ฉบับ
                        </div>
                        <div class="small text-muted mb-2">กรุณาตรวจสอบและลงนามรายงานที่ค้างอยู่</div>
                    @endif
                    @if(($signedNewsCount ?? 0) > 0)
                        <div class="fw-bold mb-1 text-success">
                            <i class="bi bi-patch-check-fill"></i>
                            รายงานของคุณได้รับการลงนามใหม่ {{ $signedNewsCount }} ฉบับ
                        </div>
                        <div class="small text-muted mb-2">
                            <a href="{{ route('reports.index', ['filter' => 'signed']) }}" class="link-gov">เปิดดูรายงานที่ลงนามแล้ว →</a>
                        </div>
                    @endif
                    <a href="{{ (Auth::user()->isSupervisor() && ($revisionCount ?? 0) == 0) ? route('supervisor.inbox') : route('reports.index') }}"
                       class="btn btn-gov btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> เปิดดูรายงาน
                    </a>
                </div>
                <button type="button" class="btn-close me-2 mt-2 ms-auto" data-bs-dismiss="toast" aria-label="ปิด"></button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // เด้งเตือนครั้งเดียวต่อ session (เด้งซ้ำเมื่อยอดเปลี่ยน)
            var key = '{{ ($pendingSignCount ?? 0) }}-{{ ($revisionCount ?? 0) }}-{{ ($signedNewsCount ?? 0) }}';
            var shownFor = sessionStorage.getItem('pendingSignToastShown');
            if (shownFor !== key) {
                var el = document.getElementById('pendingSignToast');
                new bootstrap.Toast(el, { delay: 10000 }).show();
                sessionStorage.setItem('pendingSignToastShown', key);
            }
        });
    </script>
    @endif
    @endauth
</body>
</html>