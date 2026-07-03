<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบสารสนเทศ')</title>
    {{-- Favicon (ไอคอน tab เบราว์เซอร์) --}}
<link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rmuti.ico') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rmuti.ico') }}">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Google Font ภาษาไทย --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
    :root {
        /* ===== ธีมส้ม (แบบ B) ===== */
        --gov-orange-dark: #EF6C00;     /* ส้มเข้ม - hover/active */
        --gov-orange: #F57C00;          /* ส้มหลัก - navbar, ปุ่ม */
        --gov-orange-light: #FFB74D;    /* ส้มอ่อน - accent */
        --gov-orange-bg: #FFF3E0;       /* ส้มซีด - พื้นหลังเด่น */
        --gov-gold: #FFC107;            /* ทอง - icon/avatar */
        --gov-gray: #FFF8F0;            /* พื้นหลังหลัก (ครีมส้มอ่อน) */
        --gov-text: #4E342E;            /* น้ำตาลเข้ม - อ่านง่าย */
    }

    body {
        font-family: 'Sarabun', sans-serif;
        background: var(--gov-gray);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        color: var(--gov-text);
    }

    .navbar-gov {
        background: var(--gov-orange);
        box-shadow: 0 2px 8px rgba(245, 124, 0, 0.2);
    }

    .navbar-gov .navbar-brand,
    .navbar-gov .nav-link {
        color: #ffffff !important;
    }

    .navbar-gov .nav-link:hover {
        color: var(--gov-gold) !important;
    }

    .brand-icon {
        width: 40px;
        height: 40px;
        background: var(--gov-gold);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--gov-orange-dark);
        font-size: 20px;
        margin-right: 10px;
    }

    .auth-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .auth-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(245, 124, 0, 0.12);
        overflow: hidden;
        width: 100%;
        max-width: 450px;
    }

    .auth-header {
        background: var(--gov-orange);
        color: #ffffff;
        padding: 1.5rem;
        text-align: center;
    }

    .auth-body {
        padding: 2rem;
    }

    .btn-gov {
        background: var(--gov-orange);
        color: #ffffff;
        font-weight: 500;
        padding: 0.6rem;
        border: none;
        transition: all 0.2s;
    }

    .btn-gov:hover {
        background: var(--gov-orange-dark);
        color: #ffffff;
    }

    .btn-outline-primary {
        color: var(--gov-orange) !important;
        border-color: var(--gov-orange) !important;
    }

    .btn-outline-primary:hover {
        background: var(--gov-orange) !important;
        color: #ffffff !important;
    }

    .text-primary {
        color: var(--gov-orange) !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--gov-orange);
        box-shadow: 0 0 0 0.2rem rgba(245, 124, 0, 0.15);
    }

    .input-group-text {
        background: var(--gov-orange-bg);
        color: var(--gov-orange-dark);
        border-right: none;
    }

    .footer-gov {
        background: var(--gov-orange);
        color: #ffffff;
        padding: 1rem 0;
        text-align: center;
        font-size: 0.9rem;
    }

    a.link-gov {
        color: var(--gov-orange);
        text-decoration: none;
        font-weight: 500;
    }

    a.link-gov:hover {
        color: var(--gov-orange-dark);
    }

    /* ปรับการ์ดที่ใช้สีน้ำเงินเดิม */
    .card-header h5 {
        color: var(--gov-orange-dark) !important;
    }

    /* Alert info ใช้ส้มอ่อนแทนน้ำเงิน */
    .alert.alert-info {
    background-color: #FFF3E0;
    border-color: #FFB74D;
    color: #BF360C;
}

    /* Breadcrumb */
    .breadcrumb-item.active {
        color: var(--gov-orange-dark);
    }
</style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-gov">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
    <img src="{{ asset('https://ess-register.rmuti.ac.th/AppKK/assets/media/logos/demo7.svg') }}"
         alt="RMUTI Logo"
         style="width: 42px; height: 42px; margin-right: 12px; background: #ffffff; padding: 4px; border-radius: 50%;">
    <div>
        <div style="font-size: 1rem; font-weight: 600;">ระบบบริการบุคลากร</div>
        <div style="font-size: 0.75rem; opacity: 0.85;">Personnel Development Report</div>
    </div>
</a>

    @auth
    <div class="dropdown">
        <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2"
                type="button" data-bs-toggle="dropdown" aria-expanded="false">
            @if(Auth::user()->avatar_url)
    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar"
         style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
    @else
    <div style="width: 28px; height: 28px; border-radius: 50%; background: #FFC107; color: #BF360C; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px;">
        {{ Auth::user()->initials }}
    </div>
    @endif
            <span>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
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
                <a class="dropdown-item" href="{{ route('reports.index') }}">
                    <i class="bi bi-file-earmark-text-fill text-primary"></i> รายงานของฉัน
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
           
            @if(Auth::user()->isSupervisor())
                <li>
                    <a class="dropdown-item" href="{{ route('supervisor.inbox') }}">
                        <i class="bi bi-inbox-fill" style="color: #F57C00;"></i>
                            กล่องรายงานที่รอลงนาม
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
            <div>© {{ date('Y') }} มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น — สงวนลิขสิทธิ์</div>
            <div style="font-size: 0.8rem; opacity: 0.8;">https://www.kkc.rmuti.ac.th/</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Signature Pad library (สำหรับวาดลายเซ็น) --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
</body>
</html>