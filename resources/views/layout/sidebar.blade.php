<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box text-center">
        <a href="#" class="logo-light">
            <img src="{{ asset('app') }}/assets/images/sims5.png"
                class="img-fluid"
                width="200px"
                alt="logo">
        </a>
    </div>

    <!-- Menu Toggle Button (sm-hover) -->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <i class="ri-menu-2-line fs-24 button-sm-hover-icon"></i>
    </button>

    <div class="scrollbar" data-simplebar>

        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">Home</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard.index') }}">
                    <span class="nav-icon">
                        <i class="ri-dashboard-2-line"></i>
                    </span>
                    <span class="nav-text">Beranda</span>
                </a>
            </li>
            <li class="menu-title">Dokumen & SOP</li>
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#daftarLaporanSidebar" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="daftarLaporanSidebar">
                    <span class="nav-icon">
                        <i class="ri-news-line"></i>
                    </span>
                    <span class="nav-text"> Daftar Laporan </span>
                </a>
                <div class="collapse" id="daftarLaporanSidebar">
                    <ul class="nav sub-navbar-nav">

                        <li class="sub-nav-item">
                            <a class="sub-nav-link  menu-arrow" href="#laporanKLKH" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="laporanKLKH">
                                <span> Laporan KLKH </span>
                            </a>
                            <div class="collapse" id="laporanKLKH">
                                <ul class="nav sub-navbar-nav">
                                    <li class="sub-nav-item">
                                        <a class="sub-nav-link" href="{{ route('klkh.fuelStation.report') }}">Fuel Station</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#formSAPSidebar" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="formSAPSidebar">
                    <span class="nav-icon">
                        <i class="ri-layout-line"></i>
                    </span>
                    <span class="nav-text"> Form SAP </span>
                </a>
                <div class="collapse" id="formSAPSidebar">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('klkh.fuelStation.index') }}">KLKH Fuel Station</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('kkh.index') }}">
                    <span class="nav-icon">
                        <i class="ri-task-line"></i>
                    </span>
                    <span class="nav-text">KKH</span>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link menu-arrow" href="#fuelman" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="fuelman">
                    <span class="nav-icon">
                        <i class="ri-gas-station-line"></i>
                    </span>
                    <span class="nav-text"> FuelMan </span>
                </a>
                <div class="collapse" id="fuelman">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('fuelman.dashboard') }}">Dashboard</a>
                        </li>
                    </ul>
                </div>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sop" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sop">
                    <span class="nav-icon">
                        <i class="ri-layout-line"></i>
                    </span>
                    <span class="nav-text">SOP</span>
                </a>
                <div class="collapse" id="sop">
                    <ul class="nav sub-navbar-nav">

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.ringkasanSOP') }}">
                                Ringkasan SOP
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.prosesPlanning') }}">
                                01. Proses Planning
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.surveyKepuasanPelangganEksternal') }}">
                                02. Survey Kepuasan Pelanggan Eksternal
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.keluhanPelanggan') }}">
                                03. Keluhan Pelanggan
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.laporanOwningOperationCost') }}">
                                04. Laporan Owning & Operation Cost
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.pengelolaanSuratMasukDanKeluar') }}">
                                05. Pengelolaan Surat Masuk dan Keluar
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.pencatatanSystemFuelManagement') }}">
                                06. Pencatatan System Fuel Management
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.laporanProduktivity') }}">
                                08. Laporan Produktivity
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.laporanPencatatanHoursMeter') }}">
                                09. Laporan Pencatatan Hours Meter
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.penetapanBaselineEnergi') }}">
                                11. Penetapan Baseline Energi
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.pengelolaanFuel') }}">
                                12. Pengelolaan Fuel
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.pengoperasianUnitFuelTruck') }}">
                                13. Pengoperasian Unit Fuel Truck
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.pembuatanLaporanManagementProfitLo') }}">
                                14. Pembuatan Laporan Management Profit & Loss
                            </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sop.pembuatanLaporanBusinessPlan') }}">
                                15. Pembuatan Laporan Business Plan
                            </a>
                        </li>

                    </ul>
                </div>

            </li>
            <li class="menu-title">Fuel Management</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('mineMonitoring.index') }}">
                    <span class="nav-icon">
                        <i class="ri-road-map-line"></i>
                    </span>
                    <span class="nav-text">Mine Monitoring</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('refuelingMonitoring.index') }}">
                    <span class="nav-icon">
                        <i class="ri-gas-station-line"></i>
                    </span>
                    <span class="nav-text">Refueling Monitoring</span>
                </a>
            </li>

            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('summary.index') }}">
                    <span class="nav-icon">
                        <i class="ri-dashboard-3-line"></i>
                    </span>
                    <span class="nav-text">Summary</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('journal.index') }}">
                    <span class="nav-icon">
                        <i class="ri-book-open-line"></i>
                    </span>
                    <span class="nav-text">Journal</span>
                </a>
            </li> --}}

            <li class="nav-item">
                <a class="nav-link" href="{{ route('statusAvailability.index') }}">
                    <span class="nav-icon">
                        <i class="ri-pie-chart-2-line"></i>
                    </span>
                    <span class="nav-text">Status & Availability</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('status.index') }}">
                    <span class="nav-icon">
                        <i class="ri-information-line"></i>
                    </span>
                    <span class="nav-text">Detail Status</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('statusActivity.index') }}">
                    <span class="nav-icon">
                        <i class="ri-line-chart-line"></i>
                    </span>
                    <span class="nav-text">Status & Activity</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{ route('distribusiFrekuensiFuelTruck.index') }}">
                    <span class="nav-icon">
                        <i class="ri-truck-line"></i>
                    </span>
                    <span class="nav-text">Distribusi Frek. Fuel Truck</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('distribusiFrekuensiFuelTruck.durasi') }}">
                    <span class="nav-icon">
                        <i class="ri-timer-line"></i>
                    </span>
                    <span class="nav-text">Durasi Refueling Fuel Truck</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('distribusiFrekuensiFuelStation.index') }}">
                    <span class="nav-icon">
                        <i class="ri-gas-station-line"></i>
                    </span>
                    <span class="nav-text">Distribusi Frek. Fuel Station</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('distribusiFrekuensiFuelStation.durasi') }}">
                    <span class="nav-icon">
                        <i class="ri-time-line"></i>
                    </span>
                    <span class="nav-text">Durasi Refueling Fuel Station</span>
                </a>
            </li>

           <li class="nav-item">
                <a class="nav-link" href="{{ route('overspeed.index') }}">
                    <span class="nav-icon">
                        <i class="ri-speed-line"></i>
                    </span>
                    <span class="nav-text">Unit Overspeed</span>
                </a>
            </li>
            @if (Auth::user()->role == 'ADMIN')
                <li class="menu-title">Configuration Mobile</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <span class="nav-icon">
                            <i class="ri-user-line"></i>
                        </span>
                        <span class="nav-text">User</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('mappingVerifier.index') }}">
                        <span class="nav-icon">
                            <i class="ri-shield-check-line"></i>
                        </span>
                        <span class="nav-text">Mapping Verifier</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('mappingSOP.index') }}">
                        <span class="nav-icon">
                            <i class="ri-file-list-3-line"></i>
                        </span>
                        <span class="nav-text">Mapping SOP</span>
                    </a>
                </li>
            @endif


        </ul>
    </div>
</div>

