<div class="left-sidenav">
    <!-- LOGO -->
    <div class="brand">
        <a href="{{ route('home') }}" class="logo">
            <span>
                <img src="{{ URL::asset('public/itic/icon_itic.png') }}" alt="logo-small" class="logo-sm">
            </span>
            <span>
                <img src="{{ URL::asset('public/itic/text_itic.png') }}" alt="logo-large" class="logo-lg logo-light">
                <img src="{{ URL::asset('public/itic/text_itic_dark.png') }}" alt="logo-large" class="logo-lg logo-dark">
            </span>
        </a>
    </div>
    <div class="menu-content h-100" data-simplebar>
        <ul class="metismenu left-sidenav-menu">
            <li class="menu-label mt-0">Main</li>
            <li class="{{ Request::is('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}" class="{{ Request::is('home') ? 'active' : '' }}"><i class="mdi mdi-home"></i> Dashboard</a>
            </li>
            {{-- <li class="menu-label mt-0">Pengerjaan</li> --}}
            <li class="{{ Request::is('pengerjaan*') ? 'active' : '' }}">
                <a href="{{ route('pengerjaan') }}" class="{{ Request::is('pengerjaan*') ? 'active' : '' }}"><i class="mdi mdi-clipboard-text-play-outline"></i> Input Pengerjaan</a>
            </li>
            <li class="{{ Request::is('pengerjaan*') ? 'active' : '' }}">
                <a href="{{ route('hasil_kerja') }}" class="{{ Request::is('hasil_kerja*') ? 'active' : '' }}"><i class="mdi mdi-clipboard-text-play-outline"></i> Hasil Kerja</a>
            </li>
            {{-- <li class="{{ Request::is('payrol/operator_karyawan*') ? 'active' : '' }}">
                <a href="{{ route('operator_karyawan') }}" class="{{ Request::is('payrol/operator_karyawan*') ? 'active' : '' }}"><i class="mdi mdi-account-multiple-plus"></i> Data Karyawan Operator</a>
            </li> --}}
            <li>
                <a href="javascript: void(0);"> <i data-feather="file-text"
                        class="align-self-center menu-icon"></i><span>Data Karyawan Operator</span><span
                        class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li class="nav-item"><a class="nav-link" href="{{ route('operator_karyawan') }}" {{ Request::is('payrol/operator_karyawan*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Borongan</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('operator_karyawan_harian') }}" {{ Request::is('payrol/operator_karyawan_harian*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Harian</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('operator_karyawan_supir_rit') }}" {{ Request::is('payrol/operator_karyawan_supir_rit*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Supir RIT</a>
                    </li>
                    {{-- <li class="nav-item"><a class="nav-link" href="{{ route('payrol.harian') }}" {{ Request::is('payrol/harian*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Harian</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('payrol.supir_rit') }}" {{ Request::is('payrol/supir_rit*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Supir RIT</a>
                    </li> --}}
                </ul>
            </li>
            {{-- <li>
                <a href="javascript: void(0);"> <i data-feather="file-text"
                        class="align-self-center menu-icon"></i><span>Pengerjaan</span><span
                        class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li class="nav-item"><a class="nav-link" href="{{ route('pengerjaan') }}" {{ Request::is('pengerjaan/borongan_b*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Borongan B</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#" {{ Request::is('pengerjaan/borongan_20gr*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Borongan 20gr</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#" {{ Request::is('pengerjaan/harian*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Harian</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#" {{ Request::is('pengerjaan/supir_rit*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Supir RIT</a>
                    </li>
                </ul>
            </li> --}}
            <li class="menu-label mt-0">Payrol</li>
            <li>
                <a href="javascript: void(0);"> <i data-feather="file-text"
                        class="align-self-center menu-icon"></i><span>Laporan Payrol</span><span
                        class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li class="nav-item"><a class="nav-link" href="{{ route('payrol.borongan') }}" {{ Request::is('payrol/borongan*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Borongan</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('payrol.harian') }}" {{ Request::is('payrol/harian*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Harian</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('payrol.supir_rit') }}" {{ Request::is('payrol/supir_rit*') ? 'active' : '' }}>
                        <i class="ti-control-record"></i>Supir RIT</a>
                    </li>
                </ul>
            </li>
            {{-- <li class="{{ Request::is('laporan') ? 'active' : '' }}">
                <a href="{{ route('laporan') }}" class="{{ Request::is('laporan') ? 'active' : '' }}"><i class="mdi mdi-home"></i> Detail Laporan</a>
            </li> --}}
            {{-- <li class="menu-label mt-0">Laporan</li> --}}
            <li>
                <a href="javascript: void(0);"> <i data-feather="file-text"
                        class="align-self-center menu-icon"></i><span>Detail Laporan</span><span
                        class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.borongan') }}">
                        <i class="ti-control-record"></i>Borongan</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.harian') }}">
                        <i class="ti-control-record"></i>Harian</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.supir_rit') }}">
                        <i class="ti-control-record"></i>Supir RIT</a>
                    </li>
                </ul>
            </li>
            @if (auth()->user()->roles == 1)
            <li class="menu-label mt-0">Company</li>
            <li class="{{ Request::is('company') ? 'active' : '' }}">
                <a href="#" class="{{ Request::is('company') ? 'active' : '' }}"><i class="mdi mdi-office-building"></i> Company</a>
            </li>
            <li class="menu-label mt-0">Developer</li>
            <li class="{{ Request::is('periode/b') ? 'active' : '' }}">
                <a href="{{ route('b.periode') }}" class="{{ Request::is('periode/b') ? 'active' : '' }}"><i data-feather="file-text" class="align-self-center menu-icon"></i> Periode Payrol</a>
            </li>
            <li class="{{ Request::is('cut_off') ? 'active' : '' }}">
                <a href="{{ route('cut_off') }}" class="{{ Request::is('cut_off') ? 'active' : '' }}"><i data-feather="file-text" class="align-self-center menu-icon"></i> Cut Off</a>
            </li>
            <li class="{{ Request::is('jenis_operator') ? 'active' : '' }}">
                <a href="{{ route('jenis_operator') }}" class="{{ Request::is('jenis_operator') ? 'active' : '' }}"><i data-feather="file-text" class="align-self-center menu-icon"></i> Jenis Operator</a>
            </li>
            <li>
                <a href="javascript: void(0);"> <i data-feather="file-text"
                        class="align-self-center menu-icon"></i><span>UMK Borongan</span><span
                        class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li class="nav-item"><a class="nav-link" href="{{ route('jenis_umk_borongan.lokal') }}">
                        <i class="ti-control-record"></i>Lokal</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('jenis_umk_borongan.ekspor') }}">
                        <i class="ti-control-record"></i>Ekspor</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('jenis_umk_borongan.ambri') }}">
                        <i class="ti-control-record"></i>Ambri</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('umk_supir_rit.index') }}" class="{{ Request::is('umk_supir_rit') ? 'active' : '' }}"><i data-feather="file-text" class="align-self-center menu-icon"></i> UMK Supir RIT</a>
            </li>
            <li class="{{ Request::is('umk_periode') ? 'active' : '' }}">
                <a href="{{ route('umk_periode.lokal_umk_periode') }}" class="{{ Request::is('umk_periode') ? 'active' : '' }}"><i data-feather="file-text" class="align-self-center menu-icon"></i> UMK Periode</a>
            </li>
            <li>
                <a href="javascript: void(0);"> <i data-feather="file-text"
                    class="align-self-center menu-icon"></i><span>BPJS</span><span
                    class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li class="nav-item"><a class="nav-link" href="{{ route('bpjs.jht') }}">
                        <i class="ti-control-record"></i>JHT</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('bpjs.kesehatan') }}">
                        <i class="ti-control-record"></i>Kesehatan</a>
                    </li>
                </ul>
            </li>
            <li class="{{ Request::is('tunjangan_kerja') ? 'active' : '' }}">
                <a href="{{ route('tunjangan_kerja') }}" class="{{ Request::is('tunjangan_kerja') ? 'active' : '' }}"><i data-feather="file-text" class="align-self-center menu-icon"></i> Tunjangan Kerja</a>
            </li>
            <li class="menu-label mt-0">User Management</li>
            <li>
                <a href="javascript: void(0);"> <i data-feather="file-text"
                        class="align-self-center menu-icon"></i><span>User Management</span><span
                        class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li class="nav-item"><a class="nav-link" href="{{ route('pengguna.user') }}">
                        <i class="ti-control-record"></i>User</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('roles') }}">
                        <i class="ti-control-record"></i>Roles</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('user_management') }}">
                        <i class="ti-control-record"></i>Akses User</a>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
    </div>
</div>
