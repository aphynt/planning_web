<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class KKHController extends Controller
{
    //
    public function index()
    {
        $user = DB::connection('kkh')->table('db_payroll.dbo.tbl_data_hr as hr')
        ->leftJoin('db_payroll.dbo.tm_departemen as dp', 'hr.Id_Departemen', '=', 'dp.ID_Departemen')
        ->where('dp.Departemen', 'Planning')
        ->select(
            'hr.Nik as nik',
            'hr.Nama as name',
        )

        ->get();
        return view('kkh.index', compact('user'));
    }

    public function all_api(Request $request)
    {

        $offset = $request->input('start', 0);   // Offset
        $length = $request->input('length', 10); // Default 10 items
        $draw = $request->input('draw');
        $tanggalInput = $request->input('tanggalKKH');

        $startDate = Carbon::today()->toDateString();
        $endDate   = Carbon::today()->toDateString();

        if (!empty($tanggalInput)) {

            if (str_contains($tanggalInput, 'to')) {

                [$startDate, $endDate] = array_map('trim', explode('to', $tanggalInput));

            } else {

                $startDate = trim($tanggalInput);
                $endDate   = $startDate;

            }

        }

        $latestIds = DB::connection('kkh')
        ->table('db_payroll.dbo.web_kkh')
        ->whereBetween('tgl', [$startDate, $endDate])
        ->groupBy('tgl', 'nik')
        ->selectRaw('tgl, nik, MAX(id) as id');

        $kkh = DB::connection('kkh')->table('db_payroll.dbo.web_kkh as kkh')
            ->joinSub($latestIds, 'latest', function ($join) {
                $join->on('kkh.id', '=', 'latest.id');
            })
            ->leftJoin('db_payroll.dbo.tbl_data_hr as hr', 'kkh.nik', '=', 'hr.nik')
            ->leftJoin('db_payroll.dbo.tbl_data_hr as hr2', 'kkh.nik_pengawas', '=', 'hr2.nik')
            ->leftJoin('db_payroll.dbo.tm_departemen as dp', 'hr.Id_Departemen', '=', 'dp.ID_Departemen')
            ->leftJoin('db_payroll.dbo.tm_perusahaan as pr', 'hr.ID_Perusahaan', '=', 'pr.ID_Perusahaan')
            ->leftJoin('db_payroll.dbo.tm_jabatan as jb', 'hr.ID_Jabatan', '=', 'jb.ID_Jabatan')
            ->select(
                'kkh.id',
                'kkh.tgl',
                DB::raw("FORMAT(kkh.tgl_input, 'yyyy-MM-dd HH:mm') as TANGGAL_DIBUAT"),
                'hr.Nik as NIK_PENGISI',
                'hr.Nama as NAMA_PENGISI',
                'kkh.shift_kkh as SHIFT',
                'jb.Jabatan as JABATAN',
                DB::raw("
                    CASE
                        WHEN kkh.jam_pulang IS NULL OR LTRIM(RTRIM(kkh.jam_pulang)) = '' THEN '-'
                        ELSE
                            RIGHT('0' + LEFT(kkh.jam_pulang, CHARINDEX(':', kkh.jam_pulang) - 1), 2)
                            + ':' +
                            RIGHT('0' + RIGHT(kkh.jam_pulang, LEN(kkh.jam_pulang) - CHARINDEX(':', kkh.jam_pulang)), 2)
                    END AS JAM_PULANG
                "),
                DB::raw("
                    CASE
                        WHEN kkh.jam_tidur IS NULL OR LTRIM(RTRIM(kkh.jam_tidur)) = '' THEN '-'
                        ELSE
                            RIGHT('0' + LEFT(kkh.jam_tidur, CHARINDEX(':', kkh.jam_tidur) - 1), 2)
                            + ':' +
                            RIGHT('0' + RIGHT(kkh.jam_tidur, LEN(kkh.jam_tidur) - CHARINDEX(':', kkh.jam_tidur)), 2)
                    END AS JAM_TIDUR
                "),
                DB::raw("
                    CASE
                        WHEN kkh.jam_bangun IS NULL OR LTRIM(RTRIM(kkh.jam_bangun)) = '' THEN '-'
                        ELSE
                            RIGHT('0' + LEFT(kkh.jam_bangun, CHARINDEX(':', kkh.jam_bangun) - 1), 2)
                            + ':' +
                            RIGHT('0' + RIGHT(kkh.jam_bangun, LEN(kkh.jam_bangun) - CHARINDEX(':', kkh.jam_bangun)), 2)
                    END AS JAM_BANGUN
                "),
                DB::raw("
                    STR(
                        ROUND(
                            CASE
                                WHEN DATEDIFF(MINUTE, kkh.jam_tidur, kkh.jam_bangun) < 0 THEN
                                    DATEDIFF(MINUTE, kkh.jam_tidur, DATEADD(DAY, 1, kkh.jam_bangun)) / 60.0
                                ELSE
                                    DATEDIFF(MINUTE, kkh.jam_tidur, kkh.jam_bangun) / 60.0
                            END, 1
                        ), 10, 1
                    ) AS TOTAL_TIDUR
                "),
                 DB::raw("
                    CASE
                        WHEN kkh.jam_berangkat IS NULL OR LTRIM(RTRIM(kkh.jam_berangkat)) = '' THEN '-'
                        ELSE
                            RIGHT('0' + LEFT(kkh.jam_berangkat, CHARINDEX(':', kkh.jam_berangkat) - 1), 2)
                            + ':' +
                            RIGHT('0' + RIGHT(kkh.jam_berangkat, LEN(kkh.jam_berangkat) - CHARINDEX(':', kkh.jam_berangkat)), 2)
                    END AS JAM_BERANGKAT
                "),
                'kkh.fit_or as FIT_BEKERJA',
                DB::raw('UPPER(kkh.keluhan) as KELUHAN'),
                'kkh.masalah_pribadi as MASALAH_PRIBADI',
                'kkh.ferivikasi_pengawas',
                'kkh.nik_pengawas as NIK_PENGAWAS',
                'hr2.Nama as NAMA_PENGAWAS'
            )
            ->where('dp.Departemen', 'Planning');

        if ($request->search['value']) {
            $searchValue = '%' . $request->search['value'] . '%';
            $columnsToSearch = ['hr.Nik', 'hr.Nama', 'kkh.shift_kkh', 'kkh.jam_pulang', 'kkh.jam_tidur', 'kkh.jam_bangun', 'kkh.jam_berangkat', 'kkh.fit_or', 'hr2.Nama'];

            $kkh->where(function($query) use ($columnsToSearch, $searchValue) {
                foreach ($columnsToSearch as $column) {
                    $query->orWhere($column, 'like', $searchValue);
                }
            });
        }





        // if ($tanggalInput) {
        //     if (str_contains($tanggalInput, 'to')) {
        //         [$startDate, $endDate] = array_map('trim', explode('to', $tanggalInput));
        //     } else {
        //         $startDate = trim($tanggalInput);
        //         $endDate = $startDate;
        //     }

        // }


        $shift = $request->shift;
        if ($shift == 'Pagi') {
            $kkh->where('kkh.shift_kkh', $shift);
        }elseif($shift == 'Malam'){
            $kkh->where('kkh.shift_kkh', $shift);
        }

        $name = $request->name;
        if ($name != 'Semua') {
            $kkh->where('kkh.Nik', $name);
        }


        // $kkh->whereBetween('kkh.tgl', [$startDate, $endDate]);



        $cluster = $request->cluster;


        $filteredRecords = $kkh->count();


        $kkh = $kkh
        ->orderBy('kkh.tgl')
        ->offset($offset)
        ->limit($length)
        ->get();

        // Return JSON response
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $filteredRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $kkh,
        ]);

    }

    public function verifikasi(Request $request)
    {

        // return $request->all();
        $rowID = $request->rowID;

        DB::connection('kkh')->table('web_kkh')
            ->where('id', $rowID)
            ->update([
                'ferivikasi_pengawas' => true,
                'fit_or' => true,
                'nik_pengawas' => Auth::user()->nik,
            ]);

        return response()->json(['status' => 'ok']);
    }

    public function downloadPDF(Request $request)
    {
        $namaKKH = $request->name;
        if (empty($request->rangeStart) || empty($request->rangeEnd)){
            $time = new DateTime();
            $startDate = $time->format('Y-m-d');
            $endDate = $time->format('Y-m-d');

            $start = new DateTime("$startDate");
            $end = new DateTime("$endDate");

        }else{
            $start = new DateTime("$request->rangeStart");
            $end = new DateTime("$request->rangeEnd");
        }
        $startTimeFormatted = $start->format('Y-m-d');
        $endTimeFormatted = $end->format('Y-m-d');

        $kkh = DB::connection('kkh')->table('db_payroll.dbo.web_kkh as kkh')
            ->leftJoin('db_payroll.dbo.tbl_data_hr as hr', 'kkh.nik', '=', 'hr.nik')
            ->leftJoin('db_payroll.dbo.tbl_data_hr as hr2', 'kkh.nik_pengawas', '=', 'hr2.nik')
            ->leftJoin('db_payroll.dbo.tm_departemen as dp', 'hr.Id_Departemen', '=', 'dp.ID_Departemen')
            ->leftJoin('db_payroll.dbo.tm_perusahaan as pr', 'hr.ID_Perusahaan', '=', 'pr.ID_Perusahaan')
            ->leftJoin('db_payroll.dbo.tm_jabatan as jb', 'hr.ID_Jabatan', '=', 'jb.ID_Jabatan')
            ->select(
                'kkh.id',
                'kkh.tgl',
                DB::raw("FORMAT(kkh.tgl_input, 'yyyy-MM-dd HH:mm') as TANGGAL_DIBUAT"),
                'pr.Perusahaan as PERUSAHAAN',
                'dp.Departemen as DEPARTEMEN',
                'jb.Jabatan as JABATAN',
                'hr.Nik as NIK_PENGISI',
                'hr.Nama as NAMA_PENGISI',
                'hr.Shift as SIKLUS_KERJA',
                DB::raw("
                    CASE
                        WHEN kkh.jam_pulang IS NULL OR LTRIM(RTRIM(kkh.jam_pulang)) = '' THEN '-'
                        ELSE
                            RIGHT('0' + LEFT(kkh.jam_pulang, CHARINDEX(':', kkh.jam_pulang) - 1), 2)
                            + ':' +
                            RIGHT('0' + RIGHT(kkh.jam_pulang, LEN(kkh.jam_pulang) - CHARINDEX(':', kkh.jam_pulang)), 2)
                    END AS JAM_PULANG
                "),
                DB::raw("
                    CASE
                        WHEN kkh.jam_tidur IS NULL OR LTRIM(RTRIM(kkh.jam_tidur)) = '' THEN '-'
                        ELSE
                            RIGHT('0' + LEFT(kkh.jam_tidur, CHARINDEX(':', kkh.jam_tidur) - 1), 2)
                            + ':' +
                            RIGHT('0' + RIGHT(kkh.jam_tidur, LEN(kkh.jam_tidur) - CHARINDEX(':', kkh.jam_tidur)), 2)
                    END AS JAM_TIDUR
                "),
                DB::raw("
                    CASE
                        WHEN kkh.jam_bangun IS NULL OR LTRIM(RTRIM(kkh.jam_bangun)) = '' THEN '-'
                        ELSE
                            RIGHT('0' + LEFT(kkh.jam_bangun, CHARINDEX(':', kkh.jam_bangun) - 1), 2)
                            + ':' +
                            RIGHT('0' + RIGHT(kkh.jam_bangun, LEN(kkh.jam_bangun) - CHARINDEX(':', kkh.jam_bangun)), 2)
                    END AS JAM_BANGUN
                "),
                DB::raw("
                    STR(
                        ROUND(
                            CASE
                                WHEN DATEDIFF(MINUTE, kkh.jam_tidur, kkh.jam_bangun) < 0 THEN
                                    DATEDIFF(MINUTE, kkh.jam_tidur, DATEADD(DAY, 1, kkh.jam_bangun)) / 60.0
                                ELSE
                                    DATEDIFF(MINUTE, kkh.jam_tidur, kkh.jam_bangun) / 60.0
                            END, 1
                        ), 10, 1
                    ) AS TOTAL_TIDUR
                "),
                 DB::raw("
                    CASE
                        WHEN kkh.jam_berangkat IS NULL OR LTRIM(RTRIM(kkh.jam_berangkat)) = '' THEN '-'
                        ELSE
                            RIGHT('0' + LEFT(kkh.jam_berangkat, CHARINDEX(':', kkh.jam_berangkat) - 1), 2)
                            + ':' +
                            RIGHT('0' + RIGHT(kkh.jam_berangkat, LEN(kkh.jam_berangkat) - CHARINDEX(':', kkh.jam_berangkat)), 2)
                    END AS JAM_BERANGKAT
                "),
                // DB::raw("
                //     CASE
                //         WHEN kkh.fit_or IS NULL THEN '-'
                //         WHEN kkh.fit_or = 0 THEN 'TIDAK'
                //         ELSE 'YA'
                //     END as FIT_BEKERJA
                // "),
                DB::raw("CASE WHEN kkh.fit_or IS NULL OR kkh.fit_or = 0 THEN 'Perlu Verifikasi' ELSE 'Ya' END as FIT_BEKERJA"),
                DB::raw('UPPER(kkh.keluhan) as KELUHAN'),
                'kkh.masalah_pribadi as MASALAH_PRIBADI',
                'kkh.verifikasi as VERIFIKASI',
                'kkh.nama_verifikasi as NAMA_VERIFIKASI',
                'kkh.ferivikasi_pengawas',
                'kkh.nik_pengawas as NIK_PENGAWAS',
                'hr2.Nama as NAMA_PENGAWAS'
            )
            ->where('dp.Departemen', 'Planning');


        $tanggalInput = $request->input('tanggalKKH');

        $startDate = Carbon::now();
        $endDate = $startDate;


        if ($tanggalInput) {
            if (str_contains($tanggalInput, 'to')) {
                [$startDate, $endDate] = array_map('trim', explode('to', $tanggalInput));
            } else {
                $startDate = trim($tanggalInput);
                $endDate = $startDate;
            }

        }
        $kkh->whereBetween('kkh.tgl', [$startDate, $endDate]);

        $shift = $request->shift;
        if ($shift == 'Pagi') {
            $kkh->where('kkh.shift_kkh', $shift);
        }elseif($shift == 'Malam'){
            $kkh->where('kkh.shift_kkh', $shift);
        }

        $name = $request->name;
        if ($name != 'Semua') {
            $kkh->where('kkh.Nik', $name);
        }

        $kkh = $kkh
            ->orderBy('kkh.tgl')
            ->get();

        if ($kkh->isEmpty()) {
            return redirect()->back()->with('info', 'Maaf, data tidak ditemukan');
        } else {
            foreach ($kkh as $item) {
            // buat folder qr-temp jika belum ada
                $qrTempFolder = storage_path('app/qr-temp');
                if (!File::exists($qrTempFolder)) {
                    File::makeDirectory($qrTempFolder, 0755, true);
                }

                if ($item->NIK_PENGAWAS != null) {
                    $fileName = 'qr_pengawas_' . $item->id . '.png';
                    $filePath = $qrTempFolder . DIRECTORY_SEPARATOR . $fileName;

                    QrCode::size(150)->format('png')->generate(route('verified.index', ['encodedNik' => base64_encode($item->NIK_PENGAWAS)]), $filePath);

                    $item->QR_CODE_PENGAWAS = $filePath;
                } else {
                    $item->QR_CODE_PENGAWAS = null;
                }
            }
        }

        $pdf = PDF::loadView('kkh.download', compact('kkh'));

        return $pdf->download('Kesiapan Kerja Harian.pdf');
    }
}
