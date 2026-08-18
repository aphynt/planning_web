@include('layout.head', ['title' => 'Dashboard'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')

<style>
    @media (max-width: 767.98px) {
        .dt-buttons { display: none !important; }
    }
    .dashboard-page { background: #f7f8fa; min-height: calc(100vh - 70px); }
    .page-title-box { padding-top: 10px; padding-bottom: 18px; }
    .dashboard-title { font-size: 21px; font-weight: 650; color: #1f2937; margin-bottom: 3px; }
    .dashboard-subtitle { color: #8a94a6; font-size: 13px; }
    .app-card { background: #fff; border: 1px solid #edf0f4; border-radius: 12px; transition: .2s; height: 100%; }
    .app-card:hover { transform: translateY(-2px); border-color: #dfe4ea; box-shadow: 0 8px 22px rgba(20,30,50,.06); }
    .app-card-body { padding: 18px; display: flex; align-items: center; gap: 14px; }
    .app-icon { width: 44px; height: 44px; min-width: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #f3f5f8; color: #344054; font-size: 21px; }
    .app-name { font-size: 13px; font-weight: 600; color: #344054; margin: 0; }
    .app-label { font-size: 11px; color: #000000; margin-top: 2px; }
    .dashboard-section { margin: 8px 0 14px; }
    .section-title { font-size: 15px; font-weight: 650; color: #344054; margin-bottom: 3px; }
    .section-subtitle { color: #2d2e30; font-size: 12px; }
    .kpi-card { background: #fff; border: 1px solid #edf0f4; border-radius: 12px; padding: 18px; height: 100%; position: relative; overflow: hidden; }
    .kpi-card::after { content: ""; position: absolute; width: 70px; height: 70px; right: -28px; top: -28px; border-radius: 50%; background: #f7f8fa; }
    .kpi-icon { width: 38px; height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 19px; margin-bottom: 13px; }
    .kpi-icon.warning { background: #fff7e6; color: #d88a00; }
    .kpi-icon.danger { background: #fff0f0; color: #d64545; }
    .kpi-icon.info { background: #eef6ff; color: #3578c9; }
    .kpi-label { font-size: 12px; color: #2d2e30; margin-bottom: 3px; }
    .kpi-value { font-size: 26px; line-height: 1.1; font-weight: 700; color: #1d2939; }
    .kpi-description { font-size: 11px; color: #2d2e30; margin-top: 5px; }
    .dashboard-card { background: #fff; border: 1px solid #edf0f4; border-radius: 12px; box-shadow: 0 2px 8px rgba(20,30,50,.025); }
    .dashboard-card-header { padding: 18px 20px 10px; display: flex; justify-content: space-between; align-items: center; }
    .dashboard-card-title { font-size: 14px; font-weight: 650; color: #344054; margin: 0; }
    .dashboard-card-subtitle { font-size: 11px; color: #2d2e30; margin-top: 3px; }
    .dashboard-card-body { padding: 10px 20px 20px; }
    .status-card { background: #fff; border: 1px solid #edf0f4; border-radius: 12px; height: 100%; overflow: hidden; }
    .status-header { padding: 16px 18px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; }
    .status-header-left { display: flex; align-items: center; gap: 10px; }
    .status-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 17px; }
    .status-icon.warning { background: #fff7e6; color: #d88a00; }
    .status-icon.danger { background: #fff0f0; color: #d64545; }
    .status-icon.info { background: #eef6ff; color: #3578c9; }
    .status-title { font-size: 13px; font-weight: 650; color: #344054; margin: 0; }
    .status-count { font-size: 18px; font-weight: 700; color: #1d2939; }
    .dashboard-table { margin-bottom: 0 !important; }
    .dashboard-table thead th { background: #fafbfc; color: #667085; font-size: 11px; font-weight: 600; border-bottom: 1px solid #edf0f4; padding: 10px 14px; white-space: nowrap; }
    .dashboard-table tbody td { color: #475467; font-size: 12px; padding: 10px 14px; border-bottom: 1px solid #f2f4f7; vertical-align: middle; }
    .dashboard-table tbody tr:last-child td { border-bottom: 0; }
    .dashboard-table tbody tr:hover { background: #fafbfc; }
    .empty-state { padding: 25px 10px !important; text-align: center; color: #2d2e30 !important; }
    .status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; }
    .status-badge.info { background: #eef6ff; color: #3578c9; }
    #datalabels-column { min-height: 330px; }
    @media (max-width: 767.98px) {
        .dashboard-title { font-size: 18px; }
        .dashboard-card-header { padding: 15px; }
        .dashboard-card-body { padding: 8px 15px 15px; }
        .kpi-value { font-size: 23px; }
        .app-card-body { padding: 14px; }
    }
</style>

<div class="page-content dashboard-page">
    <div class="container-fluid">
        <div class="page-title-box">
            <div class="dashboard-title">Dashboard</div>
            <div class="dashboard-subtitle">Monitoring aktivitas dan kesiapan kerja hari ini</div>
        </div>
        <div class="row g-3 mb-2">
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="{{ route('klkh.fuelStation.index') }}" class="text-decoration-none">
                    <div class="app-card">
                        <div class="app-card-body">
                            <div class="app-icon"><i class="bx bx-gas-pump"></i></div>
                            <div>
                                <p class="app-name">KLKH Fuel Station</p>
                                <div class="app-label">Kelayakan lingkungan kerja</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <a href="{{ route('kkh.index') }}" class="text-decoration-none">
                    <div class="app-card">
                        <div class="app-card-body">
                            <div class="app-icon"><i class="bx bx-user-check"></i></div>
                            <div>
                                <p class="app-name">KKH</p>
                                <div class="app-label">Kesiapan kerja harian</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <a href="#" class="text-decoration-none" onclick="Swal.fire('Fitur masih belum difungsikan!'); return false;">
                    <div class="app-card">
                        <div class="app-card-body">
                            <div class="app-icon"><i class="bx bx-clipboard"></i></div>
                            <div>
                                <p class="app-name">P2H</p>
                                <div class="app-label">Pemeriksaan unit</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <a href="#" class="text-decoration-none" onclick="Swal.fire('Fitur masih belum difungsikan!'); return false;">
                    <div class="app-card">
                        <div class="app-card-body">
                            <div class="app-icon"><i class="bx bx-category"></i></div>
                            <div>
                                <p class="app-name">FuelMan</p>
                                <div class="app-label">Fuel management</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-3 mb-2">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="kpi-card">
                    <div class="kpi-icon warning"><i class="bx bx-time-five"></i></div>
                    <div class="kpi-label">Belum Diverifikasi</div>
                    <div class="kpi-value">{{ $kkhBelumDiverifikasi->count() }}</div>
                    <div class="kpi-description">Data KKH yang menunggu verifikasi pengawas</div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="kpi-card">
                    <div class="kpi-icon danger"><i class="bx bx-error-circle"></i></div>
                    <div class="kpi-label">Status Unfit</div>
                    <div class="kpi-value">{{ $kkhUnfit->count() }}</div>
                    <div class="kpi-description">Karyawan dengan status kesiapan tidak fit</div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="kpi-card">
                    <div class="kpi-icon info"><i class="bx bx-moon"></i></div>
                    <div class="kpi-label">Tidur di Bawah 6 Jam</div>
                    <div class="kpi-value">{{ $kkhdibawah6Jam->count() }}</div>
                    <div class="kpi-description">Karyawan dengan waktu tidur kurang dari 6 jam</div>
                </div>
            </div>
        </div>

        <div class="dashboard-card mb-2">
            <div class="dashboard-card-header">
                <div>
                    <div class="dashboard-card-title">Pengisian KLKH</div>
                    <div class="dashboard-card-subtitle">Jumlah pengisian dalam 12 bulan terakhir</div>
                </div>
                <div class="status-badge info"><i class="bx bx-bar-chart-alt-2"></i> 12 Bulan</div>
            </div>
            <div class="dashboard-card-body">
                <div id="datalabels-column"></div>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-title">Kesiapan Kerja Harian</div>
            <div class="section-subtitle">Monitoring status kesiapan kerja berdasarkan data hari ini</div>
        </div>

        <div class="row g-3 mb-2">
            <div class="col-12 col-lg-4">
                <div class="status-card">
                    <div class="status-header">
                        <div class="status-header-left">
                            <div class="status-icon warning"><i class="bx bx-time-five"></i></div>
                            <div class="status-title">Belum Diverifikasi</div>
                        </div>
                        <div class="status-count">{{ $kkhBelumDiverifikasi->count() }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead><tr><th>NIK</th><th>Nama</th></tr></thead>
                            <tbody>
                                @forelse ($kkhBelumDiverifikasi as $row)
                                    <tr><td>{{ $row->NIK_PENGISI }}</td><td>{{ $row->NAMA_PENGISI }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="empty-state">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="status-card">
                    <div class="status-header">
                        <div class="status-header-left">
                            <div class="status-icon danger"><i class="bx bx-error-circle"></i></div>
                            <div class="status-title">Unfit</div>
                        </div>
                        <div class="status-count">{{ $kkhUnfit->count() }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead><tr><th>NIK</th><th>Nama</th></tr></thead>
                            <tbody>
                                @forelse ($kkhUnfit as $row)
                                    <tr><td>{{ $row->NIK_PENGISI }}</td><td>{{ $row->NAMA_PENGISI }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="empty-state">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="status-card">
                    <div class="status-header">
                        <div class="status-header-left">
                            <div class="status-icon info"><i class="bx bx-moon"></i></div>
                            <div class="status-title">Tidur di Bawah 6 Jam</div>
                        </div>
                        <div class="status-count">{{ $kkhdibawah6Jam->count() }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead><tr><th>NIK</th><th>Nama</th></tr></thead>
                            <tbody>
                                @forelse ($kkhdibawah6Jam as $row)
                                    <tr><td>{{ $row->NIK_PENGISI }}</td><td>{{ $row->NAMA_PENGISI }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="empty-state">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layout.footer')

<script>
    var dashboardChartLabels = @json($chartLabels);
    var dashboardChartData = @json($chartData);

    var dashboardChartOptions = {
        chart: {
            height: 330,
            type: 'bar',
            toolbar: { show: false },
            fontFamily: 'inherit',
            zoom: { enabled: false }
        },
        series: [{
            name: 'Pengisian',
            data: dashboardChartData
        }],
        plotOptions: {
            bar: {
                borderRadius: 5,
                columnWidth: '42%',
                dataLabels: { position: 'top' }
            }
        },
        dataLabels: {
            enabled: true,
            offsetY: -20,
            style: {
                fontSize: '11px',
                fontWeight: 600,
                colors: ['#667085']
            },
            formatter: function(value) {
                return value;
            }
        },
        colors: ['#6b8afd'],
        grid: {
            borderColor: '#eef0f3',
            strokeDashArray: 4,
            padding: { top: 15, left: 5, right: 5 }
        },
        xaxis: {
            categories: dashboardChartLabels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: '#2d2e30',
                    fontSize: '11px'
                }
            }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            labels: {
                style: {
                    colors: '#2d2e30',
                    fontSize: '11px'
                }
            }
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function(value) {
                    return value + ' pengisian';
                }
            }
        },
        legend: { show: false },
        fill: { opacity: 1 }
    };

    new ApexCharts(
        document.querySelector('#datalabels-column'),
        dashboardChartOptions
    ).render();
</script>
