<!DOCTYPE html>
<html lang="en">
@php
    use Carbon\Carbon;
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLKH Fuel Station</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
        }
        td { width: 13.5pt; }
        th { background-color: #f2f2f2; }
        tr { height: 5mm; }
        tr td:nth-child(2), tr th { text-align: left; }
        .left { text-align: left; }
        .right { text-align: right; }
        .noborder { border: none; }
        .nobg { background-color: white; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <table class="point-table">
        <thead>
            <tr>
                <th class="noborder nobg" style="padding:0;"><img src="{!! public_path('app/assets/images/sims1.png') !!}" width="240px"></th>
                <th class="noborder nobg right" colspan="5"><p style="margin:0;">FM-PLA-28/01/03/06/25</p></th>
            </tr>
            <tr>
                <th style="background:#DDEBF7" class="noborder center" colspan="6"><strong style="font-size:17pt;">Pemeriksaan Kelayakan Lingkungan Kerja Harian (KLKH)</strong></th>
            </tr>
            <tr>
                <th style="background:#DDEBF7" class="noborder center" colspan="6"><strong style="font-size:17pt;">Area Fuel Station</strong></th>
            </tr>
        </thead>
    </table>

    <table>
        <tr style="border-top:1px solid #000; border-left:1px solid #000; border-right:1px solid #000">
            <td class="left noborder" colspan="2">Departement</td>
            <td class="left noborder" colspan="2">: PLANNING</td>
            <td class="left noborder" colspan="2">Shift</td>
            <td class="left noborder" colspan="4">: {!! $fuelStation->SHIFT !!}</td>
        </tr>
        <tr style="border-top:1px solid #000; border-left:1px solid #000; border-right:1px solid #000">
            <td class="left noborder" colspan="2">Hari / Tanggal</td>
            <td class="left noborder" colspan="2">: {!! Carbon::parse($fuelStation->DATE)->locale('id')->isoFormat('dddd, D MMMM YYYY') !!}</td>
            <td class="left noborder" colspan="2">Jam</td>
            <td class="left noborder" colspan="4">: {!! Carbon::parse($fuelStation->TIME)->locale('id')->isoFormat('HH:mm') !!}</td>
        </tr>
        <tr style="border-top:1px solid #000; border-left:1px solid #000; border-right:1px solid #000">
            <td class="left noborder" colspan="2">PIT</td>
            <td class="left noborder" colspan="2">: {!! $fuelStation->PIT !!}</td>
            <td class="noborder" colspan="6"></td>
        </tr>

        <!-- Header tabel utama yang telah diperbaiki -->
        <tr>
            <th rowspan="2">No</th>
            <th class="center" colspan="3" rowspan="2">Item yang diperiksa</th>
            <th class="center" colspan="3">Cek</th>
            <th class="center" colspan="3" rowspan="2">Keterangan</th>
        </tr>
        <tr>
            <th>Ya</th>
            <th>Tidak</th>
            <th>N/A</th>
        </tr>

        <!-- Divider & sub-header -->
        <tr><td class="left" style="background:#2F75B5" colspan="10"></td></tr>
        <tr><th class="left" style="background:#d4d4d4" colspan="10">Lokasi Kerja</th></tr>

        <!-- Semua baris asli tetap utuh di sini -->
        <tr>
            <td>1</td>
            <td colspan="3">Permukaan tanah rata dan tidak berlubang</td>
            <td class="center">{!! $fuelStation->PERMUKAAN_TANAH_RATA_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PERMUKAAN_TANAH_RATA_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PERMUKAAN_TANAH_RATA_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->PERMUKAAN_TANAH_RATA_NOTE !!}</td>
        </tr>
        <tr>
            <td>2</td>
            <td colspan="3">Permukaan tanah tidak licin</td>
            <td class="center">{!! $fuelStation->PERMUKAAN_TANAH_LICIN_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PERMUKAAN_TANAH_LICIN_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PERMUKAAN_TANAH_LICIN_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->PERMUKAAN_TANAH_LICIN_NOTE !!}</td>
        </tr>
        <tr>
            <td>3</td>
            <td colspan="3">Lokasi kerja jauh dengan lintasan aktif angkutan</td>
            <td class="center">{!! $fuelStation->LOKASI_JAUH_LINTASAN_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->LOKASI_JAUH_LINTASAN_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->LOKASI_JAUH_LINTASAN_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->LOKASI_JAUH_LINTASAN_NOTE !!}</td>
        </tr>
        <tr>
            <td>4</td>
            <td colspan="3">Tidak ada ceceran B3</td>
            <td class="center">{!! $fuelStation->TIDAK_CECERAN_B3_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->TIDAK_CECERAN_B3_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->TIDAK_CECERAN_B3_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->TIDAK_CECERAN_B3_NOTE !!}</td>
        </tr>
        <tr>
            <td>5</td>
            <td colspan="3">Area parkir khusus Fuel Truck untuk penyetokan tersedia</td>
            <td class="center">{!! $fuelStation->PARKIR_FUELTRUCK_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PARKIR_FUELTRUCK_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PARKIR_FUELTRUCK_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->PARKIR_FUELTRUCK_NOTE !!}</td>
        </tr>
        <tr>
            <td>6</td>
            <td colspan="3">Area parkir khusus untuk LV tersedia</td>
            <td class="center">{!! $fuelStation->PARKIR_LV_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PARKIR_LV_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PARKIR_LV_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->PARKIR_LV_NOTE !!}</td>
        </tr>
        <tr>
            <td>7</td>
            <td colspan="3">Semua lampu kerja menyala dengan normal dan memadai untuk kerja malam hari</td>
            <td class="center">{!! $fuelStation->LAMPU_KERJA_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->LAMPU_KERJA_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->LAMPU_KERJA_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->LAMPU_KERJA_NOTE !!}</td>
        </tr>
        <tr>
            <td>8</td>
            <td colspan="3">Sisa fuel genset >10% kapasitas tangki</td>
            <td class="center">{!! $fuelStation->FUEL_GENSET_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->FUEL_GENSET_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->FUEL_GENSET_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->FUEL_GENSET_NOTE !!}</td>
        </tr>
        <tr>
            <td>9</td>
            <td colspan="3">Sisa air dalam tandon air bersih >30% kapasitas tandon</td>
            <td class="center">{!! $fuelStation->AIR_BERSIH_TANDON_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->AIR_BERSIH_TANDON_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->AIR_BERSIH_TANDON_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->AIR_BERSIH_TANDON_NOTE !!}</td>
        </tr>
        <tr>
            <th style="text-align: left;background:#d4d4d4" colspan="10">Perlengkapan Kerja</th>
        </tr>
        <tr>
            <td>1</td>
            <td colspan="3">Tersedia SOP/ JSA untuk pekerjaan yang akan dilakukan</td>
            <td class="center">{!! $fuelStation->SOP_JSA_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->SOP_JSA_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->SOP_JSA_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->SOP_JSA_NOTE !!}</td>
        </tr>
        <tr>
            <td>2</td>
            <td colspan="3">Terpasang safety post sebagai batas berhenti unit untuk refueling</td>
            <td class="center">{!! $fuelStation->SAFETY_POST_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->SAFETY_POST_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->SAFETY_POST_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->SAFETY_POST_NOTE !!}</td>
        </tr>
        <tr>
            <td>3</td>
            <td colspan="3">Terpasang rambu peringatan dan rambu APD</td>
            <td class="center">{!! $fuelStation->RAMBU_APD_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->RAMBU_APD_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->RAMBU_APD_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->RAMBU_APD_NOTE !!}</td>
        </tr>
        <tr>
            <td>4</td>
            <td colspan="3">Perlengkapan kerja ditata dengan rapi & tidak berserakan</td>
            <td class="center">{!! $fuelStation->PERLENGKAPAN_KERJA_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PERLENGKAPAN_KERJA_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PERLENGKAPAN_KERJA_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->PERLENGKAPAN_KERJA_NOTE !!}</td>
        </tr>
        <tr>
            <td>5</td>
            <td colspan="3">Tersedia APAB dan APAR</td>
            <td class="center">{!! $fuelStation->APAB_APAR_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->APAB_APAR_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->APAB_APAR_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->APAB_APAR_NOTE !!}</td>
        </tr>
        <tr>
            <td>6</td>
            <td colspan="3">Tersedia kotak P3K dan Eyewash</td>
            <td class="center">{!! $fuelStation->P3K_EYEWASH_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->P3K_EYEWASH_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->P3K_EYEWASH_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->P3K_EYEWASH_NOTE !!}</td>
        </tr>
        <tr>
            <td>7</td>
            <td colspan="3">Terdapat tag inspeksi APAR dan eyewash yang sudah diinspeksi</td>
            <td class="center">{!! $fuelStation->INSPEKSI_APAR_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->INSPEKSI_APAR_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->INSPEKSI_APAR_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->INSPEKSI_APAR_NOTE !!}</td>
        </tr>
        <tr>
            <td>8</td>
            <td colspan="3">Tersedia form checklist peralatan Refueling</td>
            <td class="center">{!! $fuelStation->FORM_CHECKLIST_REFUELING_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->FORM_CHECKLIST_REFUELING_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->FORM_CHECKLIST_REFUELING_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->FORM_CHECKLIST_REFUELING_NOTE !!}</td>
        </tr>
        <tr>
            <td>9</td>
            <td colspan="3">Tersedia tiga wadah / tempat penampung sampah</td>
            <td class="center">{!! $fuelStation->TEMPAT_SAMPAH_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->TEMPAT_SAMPAH_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->TEMPAT_SAMPAH_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->TEMPAT_SAMPAH_NOTE !!}</td>
        </tr>
        <tr>
            <th style="text-align: left;background:#d4d4d4" colspan="10">Kegiatan Refueling Unit A2B</th>
        </tr>
        <tr>
            <td>1</td>
            <td colspan="3">Fuelman memiliki dan membawa minepermit sebagai izin kerja</td>
            <td class="center">{!! $fuelStation->MINEPERMIT_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->MINEPERMIT_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->MINEPERMIT_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->MINEPERMIT_NOTE !!}</td>
        </tr>
        <tr>
            <td>2</td>
            <td colspan="3">Operator Fuel Truck memiliki dan membawa SIMPER sesuai peralatan yang digunakan</td>
            <td class="center">{!! $fuelStation->SIMPER_OPERATOR_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->SIMPER_OPERATOR_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->SIMPER_OPERATOR_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->SIMPER_OPERATOR_NOTE !!}</td>
        </tr>
        <tr>
            <td>3</td>
            <td colspan="3">Tersedia Padlock untuk kegiatan refueling</td>
            <td class="center">{!! $fuelStation->PADLOCK_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PADLOCK_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->PADLOCK_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->PADLOCK_NOTE !!}</td>
        </tr>
        <tr>
            <td>4</td>
            <td colspan="3">Tersedia wadah penampung untuk kegiatan Refueling</td>
            <td class="center">{!! $fuelStation->WADAH_PENAMPUNG_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->WADAH_PENAMPUNG_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->WADAH_PENAMPUNG_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->WADAH_PENAMPUNG_NOTE !!}</td>
        </tr>
        <tr>
            <td>5</td>
            <td colspan="3">Tersedia ganjal / Wheel Chock</td>
            <td class="center">{!! $fuelStation->WHEEL_CHOCK_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->WHEEL_CHOCK_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->WHEEL_CHOCK_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->WHEEL_CHOCK_NOTE !!}</td>
        </tr>
        <tr>
            <td>6</td>
            <td colspan="3">Tersedia Radio Komunikasi</td>
            <td class="center">{!! $fuelStation->RADIO_KOMUNIKASI_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->RADIO_KOMUNIKASI_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->RADIO_KOMUNIKASI_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->RADIO_KOMUNIKASI_NOTE !!}</td>
        </tr>
        <tr>
            <td>7</td>
            <td colspan="3">Pekerja memakai APD standar dan APD tambahan jika diperlukan</td>
            <td class="center">{!! $fuelStation->APD_STANDAR_CHECK == 'true' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->APD_STANDAR_CHECK == 'false' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td class="center">{!! $fuelStation->APD_STANDAR_CHECK == 'n/a' ? '<img src="' . public_path('check.png') . '">' : '' !!}</td>
            <td colspan="3" class="left">{!! $fuelStation->APD_STANDAR_NOTE !!}</td>
        </tr>

    </table>
    <table>
        <tr><th class="noborder nobg" colspan="10"></th></tr>
        <tr><th class="noborder nobg" colspan="10">Catatan:</th></tr>
        <tr><td class="noborder nobg left" colspan="10">@if($fuelStation->ADDITIONAL_NOTES != null) {{ $fuelStation->ADDITIONAL_NOTES }} @endif</td></tr>
    </table>
    <!-- Bagian tanda tangan -->
    <table>
        <tr>
            <td class="noborder"></td>
            <td class="noborder nobg"></td>
        </tr>
        <tr>
            <td class="noborder">Pengawas</td>
            <td class="noborder nobg" style="text-align: center">Diketahui</td>
        </tr>
        <tr>
            <td class="noborder">@if ($fuelStation->VERIFIED_PENGAWAS != null)<img src="{!! $fuelStation->VERIFIED_PENGAWAS !!}" style="max-width: 70px;">@endif</td>
            <td class="noborder nobg" style="text-align: center">@if ($fuelStation->VERIFIED_DIKETAHUI != null)<img src="{!! $fuelStation->VERIFIED_DIKETAHUI !!}" style="max-width: 70px;">@endif</td>
        </tr>
        <tr>
            <td class="noborder">{!! Carbon::parse($fuelStation->VERIFIED_DATETIME_PENGAWAS)->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') !!}</td>
            <td class="noborder nobg" style="text-align: center">{!! Carbon::parse($fuelStation->VERIFIED_DATETIME_DIKETAHUI)->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') !!}</td>
        </tr>
        <tr>
            <td class="noborder">{!! $fuelStation->NAMA_PENGAWAS !!}</td>
            <td class="noborder nobg" style="text-align: center">{!! $fuelStation->NAMA_DIKETAHUI !!}</td>
        </tr>
        <tr style="font-size:8pt;">
            <td class="noborder nobg">
                {!! $fuelStation->CATATAN_VERIFIED_PENGAWAS != null
                    ? '<img src="' . public_path("app/assets/images/note.png") . '" alt="" style="width:16px;">: ' . $fuelStation->CATATAN_VERIFIED_PENGAWAS
                    : '' !!}
            </td>
            <td class="noborder nobg" style="text-align: center">
                {!! $fuelStation->CATATAN_VERIFIED_DIKETAHUI != null
                    ? '<img src="' . public_path("app/assets/images/note.png") . '" alt="" style="width:16px;">: ' . $fuelStation->CATATAN_VERIFIED_DIKETAHUI
                    : '' !!}
            </td>
        </tr>
    </table>
</body>
</html>
