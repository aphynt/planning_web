<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $endDate   = Carbon::now()->endOfMonth();
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $now = Carbon::now();

        $dataPerBulan = DB::table('KLKH_FUEL_STATION as fs')
            ->selectRaw('YEAR(fs.DATE) as tahun, MONTH(fs.DATE) as bulan, COUNT(*) as jumlah')
            ->whereBetween('fs.DATE', [$startDate, $endDate])
            ->where('fs.STATUSENABLED', true)
            ->groupByRaw('YEAR(fs.[DATE]), MONTH(fs.[DATE])')
            ->orderByRaw('YEAR(fs.[DATE]), MONTH(fs.[DATE])')
            ->get();


        $dataPerBulan = $dataPerBulan->keyBy(function ($row) {
            return $row->tahun . '-' . str_pad($row->bulan, 2, '0', STR_PAD_LEFT);
        });



        $labels    = [];
        $chartData = [];

        $cursor = $startDate->copy();
        for ($i = 0; $i < 12; $i++) {
            $key = $cursor->format('Y-m');

            $labels[] = $cursor->format('M Y');

            $chartData[] = $dataPerBulan->has($key)
                ? $dataPerBulan->get($key)->jumlah
                : 0;

            $cursor->addMonth();
        }

        $latestIds = DB::connection('kkh')
        ->table('db_payroll.dbo.web_kkh')
        ->where('tgl', $now)
        ->groupBy('tgl', 'nik')
        ->selectRaw('tgl, nik, MAX(id) as id');

        $kkh = DB::connection('kkh')
            ->table('db_payroll.dbo.web_kkh as kkh')
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
            ->where('dp.Departemen', 'Planning')
            ->where('kkh.tgl', $now);


        $dataKKH = $kkh->get();


        $kkhBelumDiverifikasi = $dataKKH->where('ferivikasi_pengawas', '!=', 1);


        $kkhUnfit = $dataKKH->where('KELUHAN', '!=', 'FIT');


        $kkhdibawah6Jam = $dataKKH->filter(function($item) {
            return floatval($item->TOTAL_TIDUR) < 6;
        });


        return view('dashboard.index', [
            'chartLabels' => $labels,
            'chartData'   => $chartData,
            'kkhBelumDiverifikasi'   => $kkhBelumDiverifikasi,
            'kkhUnfit'   => $kkhUnfit,
            'kkhdibawah6Jam'   => $kkhdibawah6Jam,
        ]);

    }
}
