<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="/" class="text-nowrap logo-img mx-auto pt-2">
                <img src="{{ asset('images/logos/logo.png') }}" width="180" alt="" />
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="/" aria-expanded="false">
                        <i class="ti ti-layout-dashboard"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                @if (getAdmin()->tambah_pengunjung_masuk == 1 || getAdmin()->tambah_pengunjung_keluar == 1)
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Tambah</span>
                    </li>
                @endif
                @if (getAdmin()->tambah_pengunjung_masuk == 1)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/pengunjung-masuk" aria-expanded="false">
                            <i class="ti ti-browser-plus"></i>
                            <span class="hide-menu">Pengunjung Masuk</span>
                        </a>
                    </li>
                @endif
                @if (getAdmin()->tambah_pengunjung_keluar == 1)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/pengunjung-keluar" aria-expanded="false">
                            <i class="ti ti-transfer-out"></i>
                            <span class="hide-menu">Pengunjung Keluar</span>
                        </a>
                    </li>
                @endif
                @if (getAdmin()->riwayat_pengunjung_masuk == 1 || getAdmin()->riwayat_pengunjung_keluar == 1)
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Riwayat</span>
                    </li>
                @endif
                @if (getAdmin()->riwayat_pengunjung_masuk == 1)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/riwayat-pengunjung-masuk" aria-expanded="false">
                            <i class="ti ti-user-check"></i>
                            <span class="hide-menu">Pengunjung Masuk</span>
                        </a>
                    </li>
                @endif
                @if (getAdmin()->riwayat_pengunjung_keluar == 1)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/riwayat-pengunjung-keluar" aria-expanded="false">
                            <i class="ti ti-user-minus"></i>
                            <span class="hide-menu">Pengunjung Keluar</span>
                        </a>
                    </li>
                @endif
                @if (getAdmin()->laporan_keuangan == 1)
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Keuangan</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/laporan-keuangan" aria-expanded="false">
                            <i class="ti ti-file-invoice"></i>
                            <span class="hide-menu">Laporan Keuangan</span>
                        </a>
                    </li>
                @endif
                @if (getAdmin()->user_management == 1 || getAdmin()->ubah_tarif == 1)
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Pengaturan</span>
                    </li>
                @endif
                @if (getAdmin()->user_management == 1)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/user-management" aria-expanded="false">
                            <i class="ti ti-tournament"></i>
                            <span class="hide-menu">User Management</span>
                        </a>
                    </li>
                @endif
                @if (getAdmin()->ubah_tarif == 1)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/ubah-tarif" aria-expanded="false">
                            <i class="ti ti-report-money"></i>
                            <span class="hide-menu">Ubah Tarif</span>
                        </a>
                    </li>
                @endif
                <li class="pb-5 mb-5"></li>
            </ul>
        </nav>
    </div>
</aside>
