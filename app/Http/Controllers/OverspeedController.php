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

        $query = DB::connection('focus_reporting')
            ->table('dbo.HIS_OVERSPEED as A')
            ->select([
                DB::raw('
                    ROW_NUMBER() OVER (
                        ORDER BY A.OPR_REPORTTIME DESC
                    ) AS ID
                '),

                DB::raw('
                    LEAD(A.OPR_REPORTTIME) OVER (
                        PARTITION BY A.VHC_ID
                        ORDER BY A.OPR_REPORTTIME
                    ) AS NEXT_REPORTTIME
                '),

                'A.OPR_REPORTTIME',
                'A.VHC_ID',
                'A.LOC_NAME',
                'A.VHC_SPEED',
                'A.VHC_REFMAXSPEED',
                'A.OVERSPEEDSTATUS',
                'A.UPDATED_AT',
                'A.OPR_SHIFTNO',
            ])

            ->where('A.VHC_SPEED', '<=', 80)
            ->where('A.VHC_ID', 'like', 'FT%');

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

        if (!empty($shift) && $shift != 'Semua') {

            $query->where(
                'A.OPR_SHIFTNO',
                $shift
            );
        }

        $query->whereBetween('A.OPR_REPORTTIME', [
            $startDate,
            $endDate
        ]);

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
        $allData = (clone $query)
            ->orderBy('A.OPR_REPORTTIME', 'desc')
            ->get();

        $recordsFiltered = $allData->count();

        $data = $allData
            ->slice($offset, $length)
            ->values();

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
        | Durasi dihitung dari:
        |
        | OPR_REPORTTIME
        | sampai
        | NEXT_REPORTTIME
        |
        | Maksimal 5 menit per interval.
        |
        */

        $duration = [];
        foreach ($units as $unit) {
            $row = [
                'unit'  => $unit,
                'total' => 0
            ];
            foreach ($hours as $hour) {
                $minutes = $allData->where('VHC_ID', $unit)
                    ->filter(function ($item) use ($hour) {
                        return (int) Carbon::parse(
                            $item->OPR_REPORTTIME
                        )->format('H') === $hour;
                    })
                    ->sum(function ($item) {
                        if (empty($item->NEXT_REPORTTIME)) {
                            return 0;
                        }
                        $start = Carbon::parse(
                            $item->OPR_REPORTTIME
                        );
                        $end = Carbon::parse(
                            $item->NEXT_REPORTTIME
                        );
                        $seconds = $start->diffInSeconds(
                            $end
                        );
                        if ($seconds > 300 || $seconds < 0) {
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
