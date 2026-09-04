<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OverspeedController extends Controller
{

    public function index()
    {
        return view('overspeed.index');
    }


    public function api(Request $request)
    {
        $offset       = (int) $request->input('start', 0);
        $length       = (int) $request->input('length', 10);
        $draw         = $request->input('draw');
        $tanggalInput = $request->input('tanggalStatus');
        $shift        = $request->input('shift');
        $searchValue  = $request->input('search.value');

        if (empty($tanggalInput)) {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate   = $startDate;
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
        }

        $startDay = Carbon::parse($startDate)->startOfDay();
        $endDay   = Carbon::parse($endDate)->startOfDay();

        if (!empty($shift) && $shift != 'Semua') {
            if ($shift == '6') {
                $filterStart = Carbon::parse($startDate . ' 07:00:00');
                $filterEnd   = Carbon::parse($endDate . ' 19:00:00');
            } elseif ($shift == '7') {
                $filterStart = Carbon::parse($startDate . ' 19:00:00');
                $filterEnd   = Carbon::parse($endDate)->addDay()->setTime(7, 0, 0);
            } else {
                $filterStart = $startDay;
                $filterEnd   = $endDay->copy()->addDay();
            }
        } else {
            $filterStart = $startDay;
            $filterEnd   = $endDay->copy()->addDay();
        }

        $bindings = [
            'filterStart' => $filterStart->format('Y-m-d H:i:s'),
            'filterEnd'   => $filterEnd->format('Y-m-d H:i:s'),
        ];

        $searchCondition = '';
        if (!empty($searchValue)) {
            $searchCondition = "
                AND (
                    VHC_ID LIKE :search
                    OR LOC_NAME LIKE :search
                    OR OPR_NAME LIKE :search
                )
            ";
            $bindings['search'] = '%' . $searchValue . '%';
        }

        $sql = "
            WITH FilteredData AS (
                SELECT
                    VHC_ID,
                    OPR_NRP,
                    OPR_NAME,
                    GPS_TIMESTAMP,
                    (GPS_SPEED - 5) AS GPS_SPEED,
                    LOC_NAME,
                    GPS_LON,
                    GPS_LAT,
                    GPS_ALT,
                    DATEDIFF(SECOND,
                        LAG(GPS_TIMESTAMP, 1) OVER (PARTITION BY VHC_ID ORDER BY GPS_TIMESTAMP),
                        GPS_TIMESTAMP
                    ) AS GAP_SEC
                FROM [FOCUS_REPORTING].[dbo].[TRK_LOG_OVERSPEED_TEMP]
                WHERE (GPS_SPEED - 5) > 41
                AND (GPS_SPEED - 5) <= 70
                AND VHC_ID LIKE 'FT%'
                AND LOC_NAME IS NOT NULL
                AND LTRIM(RTRIM(LOC_NAME)) <> ''
                AND GPS_TIMESTAMP >= :filterStart
                AND GPS_TIMESTAMP < :filterEnd
                {$searchCondition}
            ),
            GroupedEvents AS (
                SELECT
                    VHC_ID,
                    OPR_NRP,
                    OPR_NAME,
                    GPS_TIMESTAMP,
                    GPS_SPEED,
                    LOC_NAME,
                    GPS_LON,
                    GPS_LAT,
                    GPS_ALT,
                    SUM(CASE WHEN GAP_SEC IS NULL OR GAP_SEC > 2 THEN 1 ELSE 0 END)
                        OVER (PARTITION BY VHC_ID ORDER BY GPS_TIMESTAMP) AS EVENT_GROUP_ID
                FROM FilteredData
            ),
            RankedEvents AS (
                SELECT
                    VHC_ID,
                    OPR_NRP,
                    OPR_NAME,
                    GPS_TIMESTAMP,
                    GPS_SPEED,
                    LOC_NAME,
                    GPS_LON,
                    GPS_LAT,
                    GPS_ALT,
                    EVENT_GROUP_ID,
                    ROW_NUMBER() OVER (
                        PARTITION BY VHC_ID, EVENT_GROUP_ID
                        ORDER BY GPS_SPEED DESC, GPS_TIMESTAMP ASC
                    ) AS RN_PEAK_SPEED
                FROM GroupedEvents
            ),
            EventSummary AS (
                SELECT
                    VHC_ID,
                    OPR_NRP,
                    OPR_NAME,
                    EVENT_GROUP_ID,
                    MIN(GPS_TIMESTAMP) AS START_TIME,
                    MAX(GPS_TIMESTAMP) AS END_TIME,
                    DATEDIFF(SECOND, MIN(GPS_TIMESTAMP), MAX(GPS_TIMESTAMP)) + 1 AS DURASI_DETIK,
                    ROUND(AVG(GPS_SPEED), 1) AS RATA_RATA_SPEED,
                    MAX(GPS_SPEED) AS MAX_SPEED,
                    MAX(CASE WHEN RN_PEAK_SPEED = 1 THEN LOC_NAME END) AS LOC_NAME,
                    MAX(CASE WHEN RN_PEAK_SPEED = 1 THEN GPS_LON END) AS GPS_LON,
                    MAX(CASE WHEN RN_PEAK_SPEED = 1 THEN GPS_LAT END) AS GPS_LAT,
                    MAX(CASE WHEN RN_PEAK_SPEED = 1 THEN GPS_ALT END) AS GPS_ALT
                FROM RankedEvents
                GROUP BY VHC_ID, OPR_NRP, OPR_NAME, EVENT_GROUP_ID
            )
            SELECT
                ROW_NUMBER() OVER (ORDER BY START_TIME DESC) AS ID,
                VHC_ID,
                OPR_NRP,
                OPR_NAME,
                LOC_NAME,
                GPS_LON,
                GPS_LAT,
                GPS_ALT,
                START_TIME AS OPR_REPORTTIME,
                END_TIME AS NEXT_REPORTTIME,
                MAX_SPEED AS VHC_SPEED,          -- Nilai ini sudah otomatis terpotong 5 km/jam
                RATA_RATA_SPEED,                 -- Rata-rata juga sudah terpotong 5 km/jam
                41 AS VHC_REFMAXSPEED,           -- Batas regulasi tetap 41 km/jam
                DURASI_DETIK,
                'VALID OVERSPEED AKTUAL' AS OVERSPEEDSTATUS,
                CASE
                    WHEN CAST(START_TIME AS TIME) >= '07:00:00'
                    AND CAST(START_TIME AS TIME) < '19:00:00'
                    THEN 6
                    ELSE 7
                END AS OPR_SHIFTNO
            FROM EventSummary
            WHERE DURASI_DETIK >= 3
            ORDER BY START_TIME DESC
        ";

        $rawResults = DB::connection('focus_reporting')->select($sql, $bindings);
        $allData    = collect($rawResults);

        $recordsFiltered = $allData->count();
        $data = ($length > 0)
            ? $allData->slice($offset, $length)->values()
            : $allData->values();

        if ($shift == '7') {
            $hours = [19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5, 6];
        } elseif ($shift == 'Semua' || empty($shift)) {
            $hours = range(0, 23);
        } else {
            $hours = range(7, 18);
        }

        $units = $allData->pluck('VHC_ID')->unique()->sort()->values();
        $frequency = [];
        foreach ($units as $unit) {
            $row = [
                'unit'  => $unit,
                'total' => 0
            ];

            foreach ($hours as $hour) {
                $jumlah = $allData
                    ->where('VHC_ID', $unit)
                    ->filter(function ($item) use ($hour) {
                        return (int) Carbon::parse($item->OPR_REPORTTIME)->format('H') === $hour;
                    })
                    ->count();

                $row['hour_' . $hour] = $jumlah;
                $row['total'] += $jumlah;
            }

            $frequency[] = $row;
        }

        $frequencyTotal = [
            'unit'  => 'Total',
            'total' => 0
        ];

        foreach ($hours as $hour) {
            $total = collect($frequency)->sum('hour_' . $hour);
            $frequencyTotal['hour_' . $hour] = $total;
            $frequencyTotal['total'] += $total;
        }
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
                        return (int) Carbon::parse($item->OPR_REPORTTIME)->format('H') === $hour;
                    })
                    ->sum(function ($item) {
                        return ((float) $item->DURASI_DETIK) / 60;
                    });

                $minutes = round($minutes, 2);
                $row['hour_' . $hour] = $minutes;
                $row['total'] += $minutes;
            }

            $row['total'] = round($row['total'], 2);
            $duration[] = $row;
        }

        $durationTotal = [
            'unit'  => 'Total',
            'total' => 0
        ];

        foreach ($hours as $hour) {
            $total = collect($duration)->sum('hour_' . $hour);
            $total = round($total, 2);

            $durationTotal['hour_' . $hour] = $total;
            $durationTotal['total'] += $total;
        }

        $durationTotal['total'] = round($durationTotal['total'], 2);

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,

            'frequency' => [
                'hours' => $hours,
                'rows'  => $frequency,
                'total' => $frequencyTotal
            ],

            'duration' => [
                'hours' => $hours,
                'rows'  => $duration,
                'total' => $durationTotal
            ]
        ]);
    }
}
