<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OverspeedController extends Controller
{
    //
    public function index(){
        return view('overspeed.index');
    }

    public function api(Request $request)
{
    $offset = $request->input('start', 0);
    $length = $request->input('length', 10);
    $draw   = $request->input('draw');

    /*
    |--------------------------------------------------------------------------
    | DATA DARI HIS_OVERSPEED_TEMP
    |--------------------------------------------------------------------------
    |
    | START_TIME = waktu mulai overspeed
    | STOP_TIME  = waktu akhir / END TIME overspeed
    |
    | Hanya mengambil data yang sudah memiliki STOP_TIME.
    |
    */

    $query = DB::connection('focus_reporting')
    ->table('dbo.HIS_OVERSPEED_TEMP as A')
    ->select([
        DB::raw('
            ROW_NUMBER() OVER (
                ORDER BY A.START_TIME DESC
            ) AS ID
        '),

        'A.START_TIME as OPR_REPORTTIME',

        /*
        |--------------------------------------------------------------------------
        | END TIME
        |--------------------------------------------------------------------------
        |
        | Jika STOP_TIME NULL:
        | END_TIME = START_TIME
        |
        | Jika STOP_TIME ada:
        | END_TIME = STOP_TIME
        |
        */

        DB::raw('
            CASE
                WHEN A.STOP_TIME IS NULL
                    THEN A.START_TIME
                ELSE A.STOP_TIME
            END AS END_TIME
        '),

        'A.STOP_TIME',

        'A.VHC_ID',
        'A.LOC_NAME',
        'A.VHC_SPEED',
        'A.VHC_REFMAXSPEED',
        'A.OVERSPEEDSTATUS',
        'A.UPDATED_AT',
        'A.COUNT_CHECK',

        DB::raw("
            CASE
                WHEN DATEPART(HOUR, A.START_TIME) >= 19
                     OR DATEPART(HOUR, A.START_TIME) < 7
                THEN 7
                ELSE 6
            END AS OPR_SHIFTNO
        "),
    ])

    /*
    |--------------------------------------------------------------------------
    | JANGAN gunakan whereNotNull(STOP_TIME)
    |--------------------------------------------------------------------------
    */
    ->where('A.VHC_ID', 'like', 'FT%');

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    $tanggalInput = $request->input('tanggalStatus');
    $shift        = $request->input('shift');

    if (empty($tanggalInput)) {

        $startDate = Carbon::today()->startOfDay();
        $endDate   = Carbon::today()->endOfDay();

    } else {

        if (str_contains($tanggalInput, 'to')) {

            [$startDate, $endDate] = array_map(
                'trim',
                explode('to', $tanggalInput)
            );

        } else {

            $startDate = trim($tanggalInput);
            $endDate   = $startDate;
        }

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate   = Carbon::parse($endDate)->endOfDay();
    }

    $query->whereBetween('A.START_TIME', [
        $startDate,
        $endDate
    ]);

    /*
    |--------------------------------------------------------------------------
    | FILTER SHIFT
    |--------------------------------------------------------------------------
    */

    if (!empty($shift) && $shift != 'Semua') {

        if ($shift == '7') {

            $query->where(function ($q) {

                $q->whereRaw(
                    'DATEPART(HOUR, A.START_TIME) >= 19'
                )
                ->orWhereRaw(
                    'DATEPART(HOUR, A.START_TIME) < 7'
                );

            });

        } else {

            $query->whereRaw(
                'DATEPART(HOUR, A.START_TIME) >= 7'
            )
            ->whereRaw(
                'DATEPART(HOUR, A.START_TIME) < 19'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH DATATABLE
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search.value')) {

        $search = '%' . $request->input('search.value') . '%';

        $query->where(function ($q) use ($search) {

            $q->where('A.VHC_ID', 'like', $search)
                ->orWhere('A.LOC_NAME', 'like', $search)
                ->orWhere('A.VHC_SPEED', 'like', $search)
                ->orWhere('A.VHC_REFMAXSPEED', 'like', $search)
                ->orWhere('A.OVERSPEEDSTATUS', 'like', $search);

        });
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL DATA
    |--------------------------------------------------------------------------
    */

    $allData = (clone $query)
        ->orderBy('A.START_TIME', 'desc')
        ->get();

    $recordsFiltered = $allData->count();

    /*
    |--------------------------------------------------------------------------
    | DATA DATATABLE
    |--------------------------------------------------------------------------
    */

    $data = $allData
        ->slice($offset, $length)
        ->values();

    /*
    |--------------------------------------------------------------------------
    | JAM SHIFT
    |--------------------------------------------------------------------------
    */

    if ($shift == '7') {

        $hours = [
            19, 20, 21, 22, 23,
            0, 1, 2, 3, 4, 5, 6
        ];

    } else {

        $hours = [
            7, 8, 9, 10, 11, 12,
            13, 14, 15, 16, 17, 18
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FREQUENCY
    |--------------------------------------------------------------------------
    |
    | Frekuensi dihitung berdasarkan START_TIME.
    |
    */

    $frequency = [];

    $units = $allData
        ->pluck('VHC_ID')
        ->unique()
        ->sort()
        ->values();

    foreach ($units as $unit) {

        $row = [
            'unit'  => $unit,
            'total' => 0
        ];

        foreach ($hours as $hour) {

            $jumlah = $allData
                ->where('VHC_ID', $unit)
                ->filter(function ($item) use ($hour) {

                    return (int) Carbon::parse(
                        $item->OPR_REPORTTIME
                    )->format('H') === $hour;

                })
                ->count();

            $row['hour_' . $hour] = $jumlah;
            $row['total'] += $jumlah;
        }

        $frequency[] = $row;
    }

    /*
    |--------------------------------------------------------------------------
    | FREQUENCY TOTAL
    |--------------------------------------------------------------------------
    */

    $frequencyTotal = [
        'unit'  => 'Total',
        'total' => 0
    ];

    foreach ($hours as $hour) {

        $total = collect($frequency)->sum(
            'hour_' . $hour
        );

        $frequencyTotal['hour_' . $hour] = $total;
        $frequencyTotal['total'] += $total;
    }

    /*
    |--------------------------------------------------------------------------
    | DURASI OVER SPEED
    |--------------------------------------------------------------------------
    |
    | Sekarang durasi TIDAK lagi menggunakan LEAD().
    |
    | START_TIME
    |     ↓
    | STOP_TIME
    |
    | Jadi langsung mengambil END TIME dari HIS_OVERSPEED_TEMP.
    |
    */

    $duration = [];

    foreach ($units as $unit) {

        $row = [
            'unit'  => $unit,
            'total' => 0
        ];

        foreach ($hours as $hour) {

            $minutes = $allData
                ->where('VHC_ID', $unit)
                ->filter(function ($item) use ($hour) {

                    return (int) Carbon::parse(
                        $item->OPR_REPORTTIME
                    )->format('H') === $hour;

                })
                ->sum(function ($item) {

                    if (
                        empty($item->OPR_REPORTTIME) ||
                        empty($item->END_TIME)
                    ) {
                        return 0;
                    }

                    $start = Carbon::parse(
                        $item->OPR_REPORTTIME
                    );

                    $end = Carbon::parse(
                        $item->END_TIME
                    );

                    $seconds = $start->diffInSeconds(
                        $end,
                        false
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI DURASI
                    |--------------------------------------------------------------------------
                    |
                    | Hindari durasi negatif atau lebih dari 5 menit.
                    |
                    */

                    if (
                        $seconds < 0 ||
                        $seconds > 300
                    ) {
                        return 0;
                    }

                    return $seconds / 60;
                });

            $minutes = round($minutes, 2);

            $row['hour_' . $hour] = $minutes;
            $row['total'] += $minutes;
        }

        $row['total'] = round(
            $row['total'],
            2
        );

        $duration[] = $row;
    }

    /*
    |--------------------------------------------------------------------------
    | DURASI TOTAL
    |--------------------------------------------------------------------------
    */

    $durationTotal = [
        'unit'  => 'Total',
        'total' => 0
    ];

    foreach ($hours as $hour) {

        $total = collect($duration)->sum(
            'hour_' . $hour
        );

        $total = round(
            $total,
            2
        );

        $durationTotal['hour_' . $hour] = $total;
        $durationTotal['total'] += $total;
    }

    $durationTotal['total'] = round(
        $durationTotal['total'],
        2
    );

    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'draw' => $draw,

        'recordsTotal' => $recordsFiltered,

        'recordsFiltered' => $recordsFiltered,

        'data' => $data,

        'frequency' => [
            'hours' => $hours,
            'rows' => $frequency,
            'total' => $frequencyTotal
        ],

        'duration' => [
            'hours' => $hours,
            'rows' => $duration,
            'total' => $durationTotal
        ]

    ]);
}
}
