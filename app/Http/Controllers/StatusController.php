<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    //
    public function index()
    {
        $vehicle = DB::connection('focus')->table('FLT_VEHICLE')
        ->where('VHC_TYPEID', 5)
        ->select(
            'VHC_ID'
        )

        ->get();
        return view('status.index', compact('vehicle'));
    }

    public function api(Request $request)
    {
        $query = DB::connection('focus_reporting')
            ->table('dbo.VW_VSA_STATUSACTIVITYEX as A')
            ->leftJoin('FOCUS.dbo.FLT_VSAGROUP as B', 'A.VSA_GROUPID', '=', 'B.VSA_GROUPID')
            ->leftJoin('FOCUS.dbo.FLT_VSASTATUS as C', 'A.VSA_STATUSID', '=', 'C.VSA_STATUSID')
            ->select([
                'A.VHC_ID',
                'A.OPR_REPORTTIME',
                'A.OPR_ENDTIME',
                'B.VSA_GROUPDESC',
                'C.VSA_STATUSDESC',
                DB::raw('DATEDIFF_BIG(SECOND,A.OPR_REPORTTIME,A.OPR_ENDTIME)/60.0 AS DURATION')
            ])
            ->where('A.VHC_TYPEID', 5);

        $tanggalInput = $request->input('tanggalStatus');
        $shift = $request->input('shift');
        $vhc_id = $request->input('vhc_id');

        if (empty($tanggalInput)) {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = $startDate;
        } else {
            if (str_contains($tanggalInput, 'to')) {
                [$startDate, $endDate] = array_map('trim', explode('to', $tanggalInput));
            } else {
                $startDate = trim($tanggalInput);
                $endDate = $startDate;
            }
        }

        if (!empty($shift) && $shift != 'Semua') {
            if ($shift == '6') {
                $shiftStart = Carbon::parse($startDate . ' 07:00:00');
                $shiftEnd = Carbon::parse($endDate . ' 19:00:00');
            } elseif ($shift == '7') {
                $shiftStart = Carbon::parse($startDate . ' 19:00:00');
                $shiftEnd = Carbon::parse($endDate . ' 07:00:00')->addDay();
            } else {
                $shiftStart = Carbon::parse($startDate . ' 00:00:00');
                $shiftEnd = Carbon::parse($endDate . ' 23:59:59');
            }

            $query->where(function ($q) use ($shiftStart, $shiftEnd) {
                $q->where('A.OPR_ENDTIME', '>', $shiftStart)
                ->where('A.OPR_REPORTTIME', '<', $shiftEnd);
            });
        } else {
            $allStart = Carbon::parse($startDate . ' 00:00:00');
            $allEnd = Carbon::parse($endDate . ' 23:59:59');

            $query->whereBetween('A.OPR_REPORTTIME', [$allStart, $allEnd]);
        }

        if (!empty($vhc_id) && $vhc_id != 'Semua') {
            $query->where('A.VHC_ID', $vhc_id);
        }

        $query->where('A.OPR_REPORTTIME', '>', '1970-01-01')
            ->where('A.OPR_ENDTIME', '>', '1970-01-01')
            ->whereRaw('A.OPR_ENDTIME >= A.OPR_REPORTTIME');

        $data = $query
            ->orderBy('A.VHC_ID')
            ->orderBy('A.OPR_REPORTTIME')
            ->get();

        $vehicleStatus = DB::connection('focus')
            ->table('FLT_VEHICLE as A')
            ->leftJoin('FLT_VSAGROUP as B', 'A.VSA_GROUPID', '=', 'B.VSA_GROUPID')
            ->where('A.VHC_TYPEID', 5)
            ->where('A.VHC_ACTIVE', 1)
            ->select([
                'A.VHC_ID',
                DB::raw("COALESCE(B.VSA_GROUPDESC,'Ready') AS STATUS")
            ])
            ->get()
            ->keyBy('VHC_ID');

        $units = $data
            ->pluck('VHC_ID')
            ->unique()
            ->sort()
            ->values()
            ->map(function ($unit) use ($vehicleStatus) {
                $status = 'Ready';
                if (isset($vehicleStatus[$unit])) {
                    $status = $vehicleStatus[$unit]->STATUS;
                }
                return [
                    'id' => $unit,
                    'status' => $status
                ];
            })
            ->values();

        $statuses = [
            'Ready',
            'Standby',
            'Delay',
            'Breakdown'
        ];

        $hours = [];

        if ($shift == '7') {
            for ($i = 19; $i <= 23; $i++) {
                $nextHour = ($i + 1) % 24;
                $hours[] = sprintf('%02d-%02d', $i, $nextHour);
            }

            for ($i = 0; $i <= 6; $i++) {
                $nextHour = $i + 1;
                $hours[] = sprintf('%02d-%02d', $i, $nextHour);
            }
        } else {
            for ($i = 7; $i <= 18; $i++) {
                $nextHour = $i + 1;
                $hours[] = sprintf('%02d-%02d', $i, $nextHour);
            }
        }

        $pivot = [];

        foreach ($data as $row) {
            $start = Carbon::parse($row->OPR_REPORTTIME);
            $end = Carbon::parse($row->OPR_ENDTIME);

            if ($end->lte($start)) {
                continue;
            }

            if (isset($shiftStart) && $start->lt($shiftStart)) {
                $start = $shiftStart->copy();
            }

            if (isset($shiftEnd) && $end->gt($shiftEnd)) {
                $end = $shiftEnd->copy();
            }

            if ($end->lte($start)) {
                continue;
            }

            $status = trim($row->VSA_GROUPDESC ?? '');

            if (!in_array($status, $statuses)) {
                continue;
            }

            $activity = trim($row->VSA_STATUSDESC ?? '');

            if ($activity === '') {
                continue;
            }

            $current = $start->copy()->startOfHour();

            while ($current->lt($end)) {
                $nextHour = $current->copy()->addHour();

                $overlapStart = $start->greaterThan($current)
                    ? $start->copy()
                    : $current->copy();

                $overlapEnd = $end->lessThan($nextHour)
                    ? $end->copy()
                    : $nextHour->copy();

                if ($overlapEnd->gt($overlapStart)) {
                    $duration = $overlapStart->diffInSeconds($overlapEnd) / 60;
                    $hour = (int) $current->format('H');
                    $nextHourNumber = ($hour + 1) % 24;
                    $slot = sprintf('%02d-%02d', $hour, $nextHourNumber);

                    if (in_array($slot, $hours)) {
                        if (!isset($pivot[$slot])) {
                            $pivot[$slot] = [];
                        }

                        if (!isset($pivot[$slot][$status])) {
                            $pivot[$slot][$status] = [];
                        }

                        if (!isset($pivot[$slot][$status][$activity])) {
                            $pivot[$slot][$status][$activity] = [];
                        }

                        if (!isset($pivot[$slot][$status][$activity][$row->VHC_ID])) {
                            $pivot[$slot][$status][$activity][$row->VHC_ID] = 0;
                        }

                        $pivot[$slot][$status][$activity][$row->VHC_ID] += $duration;
                    }
                }

                $current = $nextHour;
            }
        }

        $totals = [];

        foreach ($pivot as $hour => $statusesData) {
            foreach ($statusesData as $status => $activitiesData) {
                foreach ($activitiesData as $activity => $unitsData) {
                    foreach ($unitsData as $unit => $duration) {
                        if (!isset($totals[$status][$activity][$unit])) {
                            $totals[$status][$activity][$unit] = 0;
                        }

                        $totals[$status][$activity][$unit] += $duration;
                    }
                }
            }
        }

        return response()->json([
            'units' => $units,
            'hours' => $hours,
            'statuses' => $statuses,
            'pivot' => $pivot,
            'totals' => $totals,
        ]);
    }
}
