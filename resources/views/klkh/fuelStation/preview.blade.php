@include('layout.head', ['title' => 'Preview KLKH Fuel Station'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')
@php
    use Carbon\Carbon;
@endphp
<style>
  @media (max-width: 767.98px) {
    .dt-buttons {
      display: none !important;
    }
     table td:nth-child(2),
        table th:nth-child(2) {
            min-width: 400px;
            white-space: normal;
        }
  }
</style>
<div class="page-content">

    <!-- Start Container Fluid -->
    <div class="container-fluid">

        <!-- ========== Page Title Start ========== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="row align-items-center g-3">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="{{ asset('app') }}/assets/images/sims2.png" class="img-fluid" alt="images" width="70px">
                                            <img src="{{ asset('app') }}/assets/images/sims1.png" class="img-fluid" alt="images" width="200px">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-sm-end">
                                        <h6>FM-PLA-28/01/03/06/25</h6>
                                    </div>
                                </div>
                            </div>
                            <h4 style="text-align: center;">Pemeriksaan Kelayakan Lingkungan Kerja Harian (KLKH) Area Fuel Station</h4>
                            <div class="col-sm-3">
                                <div class="border rounded p-3">
                                    <h6 class="mb-0">Pit:</h6>
                                    <h5>{{ $fuelStation->PIT }}</h5>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="border rounded p-3">
                                    <h6 class="mb-0">Shift:</h6>
                                    <h5>{{ $fuelStation->SHIFT }}</h5>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="border rounded p-3">
                                    <h6 class="mb-0">Hari/ Tanggal:</h6>
                                    <h5>{{ Carbon::parse($fuelStation->DATE)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</h5>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="border rounded p-3">
                                    <h6 class="mb-0">Jam:</h6>
                                    <h5>{{ Carbon::parse($fuelStation->TIME)->locale('id')->isoFormat('HH:mm') }}</h5>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="2">No</th>
                                                <th rowspan="2">Item Yang Diperiksa</th>
                                                <th colspan="3">Cek</th>
                                                <th rowspan="2">Keterangan</th>
                                            </tr>
                                            <tr>
                                                <th>Ya</th>
                                                <th>Tidak</th>
                                                <th>N/A</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <th colspan="6" style="background-color:rgb(228, 228, 228);">Lokasi Kerja</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Permukaan tanah rata dan tidak berlubang</td>
                                                <td style="text-align: center">{{ $fuelStation->PERMUKAAN_TANAH_RATA_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PERMUKAAN_TANAH_RATA_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PERMUKAAN_TANAH_RATA_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->PERMUKAAN_TANAH_RATA_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Permukaan tanah tidak licin</td>
                                                <td style="text-align: center">{{ $fuelStation->PERMUKAAN_TANAH_LICIN_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PERMUKAAN_TANAH_LICIN_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PERMUKAAN_TANAH_LICIN_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->PERMUKAAN_TANAH_LICIN_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Lokasi kerja jauh dengan lintasan aktif angkutan</td>
                                                <td style="text-align: center">{{ $fuelStation->LOKASI_JAUH_LINTASAN_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->LOKASI_JAUH_LINTASAN_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->LOKASI_JAUH_LINTASAN_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->LOKASI_JAUH_LINTASAN_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Tidak ada ceceran B3</td>
                                                <td style="text-align: center">{{ $fuelStation->TIDAK_CECERAN_B3_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->TIDAK_CECERAN_B3_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->TIDAK_CECERAN_B3_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->TIDAK_CECERAN_B3_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Area parkir khusus Fuel Truck untuk penyetokan tersedia</td>
                                                <td style="text-align: center">{{ $fuelStation->PARKIR_FUELTRUCK_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PARKIR_FUELTRUCK_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PARKIR_FUELTRUCK_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->PARKIR_FUELTRUCK_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>Area parkir khusus untuk LV tersedia</td>
                                                <td style="text-align: center">{{ $fuelStation->PARKIR_LV_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PARKIR_LV_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PARKIR_LV_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->PARKIR_LV_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>Semua lampu kerja menyala dengan normal dan memadai untuk kerja malam hari</td>
                                                <td style="text-align: center">{{ $fuelStation->LAMPU_KERJA_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->LAMPU_KERJA_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->LAMPU_KERJA_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->LAMPU_KERJA_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>Sisa fuel genset >10% kapasitas tangki</td>
                                                <td style="text-align: center">{{ $fuelStation->FUEL_GENSET_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->FUEL_GENSET_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->FUEL_GENSET_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->FUEL_GENSET_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>Sisa air dalam tandon air bersih >30% kapasitas tandon</td>
                                                <td style="text-align: center">{{ $fuelStation->AIR_BERSIH_TANDON_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->AIR_BERSIH_TANDON_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->AIR_BERSIH_TANDON_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->AIR_BERSIH_TANDON_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <th colspan="6" style="background-color:rgb(228, 228, 228);">Perlengkapan Kerja</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Tersedia SOP/ JSA untuk pekerjaan yang akan dilakukan</td>
                                                <td style="text-align: center">{{ $fuelStation->SOP_JSA_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->SOP_JSA_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->SOP_JSA_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->SOP_JSA_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Terpasang safety post sebagai batas berhenti unit untuk refueling</td>
                                                <td style="text-align: center">{{ $fuelStation->SAFETY_POST_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->SAFETY_POST_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->SAFETY_POST_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->SAFETY_POST_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Terpasang rambu peringatan dan rambu APD</td>
                                                <td style="text-align: center">{{ $fuelStation->RAMBU_APD_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->RAMBU_APD_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->RAMBU_APD_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->RAMBU_APD_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Perlengkapan kerja ditata dengan rapi & tidak berserakan</td>
                                                <td style="text-align: center">{{ $fuelStation->PERLENGKAPAN_KERJA_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PERLENGKAPAN_KERJA_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PERLENGKAPAN_KERJA_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->PERLENGKAPAN_KERJA_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Tersedia APAB dan APAR</td>
                                                <td style="text-align: center">{{ $fuelStation->APAB_APAR_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->APAB_APAR_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->APAB_APAR_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->APAB_APAR_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>Tersedia kotak P3K dan Eyewash</td>
                                                <td style="text-align: center">{{ $fuelStation->P3K_EYEWASH_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->P3K_EYEWASH_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->P3K_EYEWASH_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->P3K_EYEWASH_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>Terdapat tag inspeksi APAR dan eyewash yang sudah diinspeksi</td>
                                                <td style="text-align: center">{{ $fuelStation->INSPEKSI_APAR_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->INSPEKSI_APAR_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->INSPEKSI_APAR_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->INSPEKSI_APAR_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>Tersedia form checklist peralatan Refueling</td>
                                                <td style="text-align: center">{{ $fuelStation->FORM_CHECKLIST_REFUELING_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->FORM_CHECKLIST_REFUELING_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->FORM_CHECKLIST_REFUELING_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->FORM_CHECKLIST_REFUELING_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>Tersedia tiga wadah / tempat penampung sampah</td>
                                                <td style="text-align: center">{{ $fuelStation->TEMPAT_SAMPAH_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->TEMPAT_SAMPAH_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->TEMPAT_SAMPAH_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->TEMPAT_SAMPAH_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <th colspan="6" style="background-color:rgb(228, 228, 228);">Kegiatan Refueling Unit A2B</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Fuelman memiliki dan membawa minepermit sebagai izin kerja</td>
                                                <td style="text-align: center">{{ $fuelStation->MINEPERMIT_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->MINEPERMIT_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->MINEPERMIT_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->MINEPERMIT_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Operator Fuel Truck memiliki dan membawa SIMPER sesuai peralatan yang digunakan</td>
                                                <td style="text-align: center">{{ $fuelStation->SIMPER_OPERATOR_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->SIMPER_OPERATOR_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->SIMPER_OPERATOR_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->SIMPER_OPERATOR_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Tersedia Padlock untuk kegiatan refueling</td>
                                                <td style="text-align: center">{{ $fuelStation->PADLOCK_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PADLOCK_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->PADLOCK_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->PADLOCK_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Tersedia wadah penampung untuk kegiatan Refueling</td>
                                                <td style="text-align: center">{{ $fuelStation->WADAH_PENAMPUNG_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->WADAH_PENAMPUNG_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->WADAH_PENAMPUNG_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->WADAH_PENAMPUNG_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Tersedia ganjal / Wheel Chock</td>
                                                <td style="text-align: center">{{ $fuelStation->WHEEL_CHOCK_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->WHEEL_CHOCK_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->WHEEL_CHOCK_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->WHEEL_CHOCK_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>Tersedia Radio Komunikasi</td>
                                                <td style="text-align: center">{{ $fuelStation->RADIO_KOMUNIKASI_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->RADIO_KOMUNIKASI_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->RADIO_KOMUNIKASI_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->RADIO_KOMUNIKASI_NOTE }}</td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>Pekerja memakai APD standar dan APD tambahan jika diperlukan</td>
                                                <td style="text-align: center">{{ $fuelStation->APD_STANDAR_CHECK == 'true' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->APD_STANDAR_CHECK == 'false' ? "✔️" : "" }}</td>
                                                <td style="text-align: center">{{ $fuelStation->APD_STANDAR_CHECK == 'n/a' ? "✔️" : "" }}</td>
                                                <td>{{ $fuelStation->APD_STANDAR_NOTE }}</td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>
                                <div class="text-start">
                                    <hr class="mb-2 mt-1 border-secondary border-opacity-50">
                                </div>
                            </div>
                            <div class="col-12"><label class="form-label">Catatan:</label>
                                <p class="mb-0">{{ $fuelStation->ADDITIONAL_NOTES }}</p>
                            </div>
                            <div class="col-sm-4">
                                <div class="border rounded p-3">
                                    <h6>Pengawas</h6>

                                    @if ($fuelStation->VERIFIED_PENGAWAS)
                                        <h5>
                                            <img src="{{ $fuelStation->VERIFIED_PENGAWAS }}" style="max-width: 70px;">
                                        </h5>
                                    @endif

                                    <h5>{{ $fuelStation->NAMA_PENGAWAS ?? '.......................' }}</h5>

                                    @if ($fuelStation->CATATAN_VERIFIED_PENGAWAS)
                                        <p>
                                            <img src="{{ asset('app/assets/images/note.png') }}" alt="">
                                            : {{ $fuelStation->CATATAN_VERIFIED_PENGAWAS }}
                                        </p>
                                    @endif

                                    <p>{{ $fuelStation->VERIFIED_DATETIME_PENGAWAS != null ? date('d-m-Y H:i', strtotime($fuelStation->VERIFIED_DATETIME_PENGAWAS)) : '' }}</p>
                                </div>
                            </div>
                            <div class="col-sm-4"></div>
                            <div class="col-sm-4">
                                <div class="border rounded p-3">
                                    <h6>Diketahui</h6>

                                    @if ($fuelStation->VERIFIED_DIKETAHUI)
                                        <h5>
                                            <img src="{{ $fuelStation->VERIFIED_DIKETAHUI }}" style="max-width: 70px;">
                                        </h5>
                                    @endif

                                    <h5>{{ $fuelStation->NAMA_DIKETAHUI ?? '.......................' }}</h5>

                                    @if ($fuelStation->CATATAN_VERIFIED_DIKETAHUI)
                                        <p>
                                            <img src="{{ asset('app/assets/images/note.png') }}" alt="">
                                            : {{ $fuelStation->CATATAN_VERIFIED_DIKETAHUI }}
                                        </p>
                                    @endif
                                    <p>{{ $fuelStation->VERIFIED_DATETIME_DIKETAHUI != null ? date('d-m-Y H:i', strtotime($fuelStation->VERIFIED_DATETIME_DIKETAHUI)) : '' }}</p>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if (Auth::user()->role == 'Admin')
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#verifiedAll{{ $fuelStation->UUID }}">
                                            <span class="badge bg-success" style="font-size:14px">Verifikasi Semua</span>
                                        </a>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#verifiedPengawas{{ $fuelStation->UUID }}">
                                            <span class="badge bg-success" style="font-size:14px">Verifikasi Pengawas</span>
                                        </a>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#verifiedDiketahui{{ $fuelStation->UUID }}">
                                            <span class="badge bg-success" style="font-size:14px">Verifikasi Diketahui</span>
                                        </a>
                                    @endif

                                    @if (Auth::user()->nik == $fuelStation->PENGAWAS && $fuelStation->VERIFIED_PENGAWAS == null)
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#verifiedPengawas{{ $fuelStation->UUID }}">
                                            <span class="badge bg-success" style="font-size:14px">Verifikasi Pengawas</span>
                                        </a>
                                    @endif

                                    @if (Auth::user()->nik == $fuelStation->DIKETAHUI && $fuelStation->VERIFIED_DIKETAHUI == null)
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#verifiedDiketahui{{ $fuelStation->UUID }}">
                                            <span class="badge bg-success" style="font-size:14px">Verifikasi Diketahui</span>
                                        </a>
                                    @endif
                                </div>

                                @include('klkh.fuelStation.modal.verifiedAll')
                                @include('klkh.fuelStation.modal.verifiedPengawas')
                                @include('klkh.fuelStation.modal.verifiedDiketahui')

                                <div class="d-flex justify-content-end flex-wrap gap-2">
                                    <a href="#" onclick="window.history.back()" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bx bx-arrow-back" style="font-size: 20px;"></i>
                                    </a>
                                    <a href="{{ route('klkh.fuelStation.download', $fuelStation->UUID) }}" target="_blank" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bx bx-download" style="font-size: 20px;"></i>
                                    </a>
                                    <a href="{{ route('klkh.fuelStation.cetak', $fuelStation->UUID) }}" target="_blank" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bx bx-printer" style="font-size: 20px;"></i>
                                    </a>
                                </div>

                            </div>


                            {{-- <div class="col-12 text-end d-print-none">
                                <button class="btn btn-outline-secondary btn-print-invoice">Download</button>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@include('layout.footer')
<script>
    // [ HTML5 Export Buttons ]
    $('#basic-btn').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'print']
    });

    // [ Column Selectors ]
    $('#cbtn-selectors').DataTable({
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, ':visible']
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'portrait',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                customize: function (doc) {
                    doc.content[1].margin = [10, 10, 10, 10];
                }
            },
            'colvis'
        ]
    });

    // [ Excel - Cell Background ]
    $('#excel-bg').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                $('row c[r^="F"]', sheet).each(function () {
                    if ($('is t', this).text().replace(/[^\d]/g, '') * 1 >= 500000) {
                        $(this).attr('s', '20');
                    }
                });
            }
        }]
    });

    // [ Custom File (JSON) ]
    $('#pdf-json').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            text: 'JSON',
            action: function (e, dt, button, config) {
                var data = dt.buttons.exportData();
                $.fn.dataTable.fileSave(new Blob([JSON.stringify(data)]), 'Export.json');
            }
        }]
    });

</script>
