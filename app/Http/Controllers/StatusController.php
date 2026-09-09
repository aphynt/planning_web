<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function index()
    {
        $vehicle = DB::connection('focus')->table('FLT_VEHICLE')->where('VHC_TYPEID', 5)->select('VHC_ID')->get();
        return view('status.index', compact('vehicle'));
    }

    public function api(Request $request)
    {
        $tanggalInput = $request->input('tanggalStatus');
        $shift = $request->input('shift', '6');
        $vhcId = $request->input('vhc_id');

        if (empty($tanggalInput)) {
            $startDate = $endDate = Carbon::today()->format('Y-m-d');
        } elseif (str_contains($tanggalInput, 'to')) {
            [$startDate, $endDate] = array_map('trim', explode('to', $tanggalInput));
        } else {
            $startDate = $endDate = trim($tanggalInput);
        }

        $dayCount = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);

        if ($shift == '6') {
            $shiftStart = Carbon::parse($startDate . ' 07:00:00');
            $shiftEnd = Carbon::parse($endDate . ' 19:00:00');
        } elseif ($shift == '7') {
            $shiftStart = Carbon::parse($startDate . ' 19:00:00');
            $shiftEnd = Carbon::parse($endDate)->addDay()->setTime(7, 0, 0);
        } else {
            $shiftStart = Carbon::parse($startDate . ' 07:00:00');
            $shiftEnd = Carbon::parse($endDate)->addDay()->setTime(7, 0, 0);
        }

        $query = DB::connection('focus_reporting')
            ->table('dbo.VW_VSA_STATUSACTIVITYEX as A')
            ->leftJoin('FOCUS.dbo.FLT_VSAGROUP as B', 'A.VSA_GROUPID', '=', 'B.VSA_GROUPID')
            ->leftJoin('FOCUS.dbo.FLT_VSASTATUS as C', 'A.VSA_STATUSID', '=', 'C.VSA_STATUSID')
            ->select([
                'A.VHC_ID', 'A.OPR_REPORTTIME', 'A.OPR_ENDTIME', 'B.VSA_GROUPDESC', 'C.VSA_STATUSDESC',
                DB::raw('DATEDIFF_BIG(SECOND,A.OPR_REPORTTIME,A.OPR_ENDTIME)/60.0 AS DURATION')
            ])
            ->where('A.VHC_TYPEID', 5)
            ->where('A.OPR_ENDTIME', '>', $shiftStart)
            ->where('A.OPR_REPORTTIME', '<', $shiftEnd)
            ->where('A.OPR_REPORTTIME', '>', '1970-01-01')
            ->where('A.OPR_ENDTIME', '>', '1970-01-01')
            ->whereRaw('A.OPR_ENDTIME >= A.OPR_REPORTTIME');

        if (!empty($vhcId) && $vhcId != 'Semua') $query->where('A.VHC_ID', $vhcId);

        $data = $query->orderBy('A.VHC_ID')->orderBy('A.OPR_REPORTTIME')->get();

        $vehicleStatus = DB::connection('focus')->table('FLT_VEHICLE as A')
            ->leftJoin('FLT_VSAGROUP as B', 'A.VSA_GROUPID', '=', 'B.VSA_GROUPID')
            ->where('A.VHC_TYPEID', 5)->where('A.VHC_ACTIVE', 1)
            ->select(['A.VHC_ID', DB::raw("COALESCE(B.VSA_GROUPDESC,'Ready') AS STATUS")])
            ->get()->keyBy('VHC_ID');

        $units = $data->pluck('VHC_ID')->unique()->sort()->values()->map(function ($unit) use ($vehicleStatus) {
            return ['id' => $unit, 'status' => $vehicleStatus[$unit]->STATUS ?? 'Ready'];
        })->values();

        $statuses = ['Ready', 'Standby', 'Delay', 'Breakdown'];
        $activityMaster = DB::connection('focus')->table('FLT_VSASTATUSVHCTYPE as A')
            ->leftJoin('FLT_VSASTATUS as B', 'A.VSA_STATUSID', '=', 'B.VSA_STATUSID')
            ->leftJoin('FLT_VSAGROUP as C', 'B.VSA_GROUPID', '=', 'C.VSA_GROUPID')
            ->where('A.VHC_TYPEID', 5)->where('A.ISENABLED', 1)
            ->select(['B.VSA_STATUSDESC', 'C.VSA_GROUPDESC'])->orderBy('B.VSA_GROUPID')->get();

        $activitiesByStatus = array_fill_keys($statuses, []);
        foreach ($activityMaster as $item) {
            $status = trim($item->VSA_GROUPDESC ?? '');
            $activity = trim($item->VSA_STATUSDESC ?? '');
            if ($status !== '' && $activity !== '' && isset($activitiesByStatus[$status]) && !in_array($activity, $activitiesByStatus[$status])) {
                $activitiesByStatus[$status][] = $activity;
            }
        }

        $hours = [];
        if ($shift == '6') {
            for ($i = 7; $i <= 18; $i++) $hours[] = sprintf('%02d-%02d', $i, $i + 1);
        } elseif ($shift == '7') {
            for ($i = 19; $i <= 23; $i++) $hours[] = sprintf('%02d-%02d', $i, ($i + 1) % 24);
            for ($i = 0; $i <= 6; $i++) $hours[] = sprintf('%02d-%02d', $i, $i + 1);
        } else {
            for ($i = 7; $i <= 23; $i++) $hours[] = sprintf('%02d-%02d', $i, ($i + 1) % 24);
            for ($i = 0; $i <= 6; $i++) $hours[] = sprintf('%02d-%02d', $i, $i + 1);
        }

        $pivot = [];
        $pivotCount = [];
        foreach ($data as $row) {
            $start = Carbon::parse($row->OPR_REPORTTIME);
            $end = Carbon::parse($row->OPR_ENDTIME);
            if ($end->lte($start)) continue;
            if ($start->lt($shiftStart)) $start = $shiftStart->copy();
            if ($end->gt($shiftEnd)) $end = $shiftEnd->copy();
            if ($end->lte($start)) continue;

            $status = trim($row->VSA_GROUPDESC ?? '');
            $activity = trim($row->VSA_STATUSDESC ?? '');
            if (!in_array($status, $statuses) || $activity === '') continue;

            $current = $start->copy()->startOfHour();
            while ($current->lt($end)) {
                $next = $current->copy()->addHour();
                $overlapStart = $start->greaterThan($current) ? $start->copy() : $current->copy();
                $overlapEnd = $end->lessThan($next) ? $end->copy() : $next->copy();
                if ($overlapEnd->gt($overlapStart)) {
                    $hour = (int) $current->format('H');
                    $slot = sprintf('%02d-%02d', $hour, ($hour + 1) % 24);
                    if (in_array($slot, $hours)) {
                        $duration = $overlapStart->diffInSeconds($overlapEnd) / 60;
                        $unit = $row->VHC_ID;
                        $pivot[$slot][$status][$activity][$unit] = ($pivot[$slot][$status][$activity][$unit] ?? 0) + $duration;
                        $pivotCount[$slot][$status][$activity][$unit] = ($pivotCount[$slot][$status][$activity][$unit] ?? 0) + 1;
                    }
                }
                $current = $next;
            }
        }

        $orderedPivot = $orderedCount = [];
        foreach ($hours as $hour) {
            $orderedPivot[$hour] = $pivot[$hour] ?? [];
            $orderedCount[$hour] = $pivotCount[$hour] ?? [];
        }
        $pivot = $orderedPivot;
        $pivotCount = $orderedCount;

        $averages = $totals = $totalCounts = [];
        foreach ($pivot as $hour => $statusData) {
            foreach ($statusData as $status => $activityData) {
                foreach ($activityData as $activity => $unitData) {
                    foreach ($unitData as $unit => $duration) {
                        $count = $pivotCount[$hour][$status][$activity][$unit] ?? 0;
                        $averages[$hour][$status][$activity][$unit] = $count > 0 ? round($duration / $count, 2) : 0;
                        $totals[$status][$activity][$unit] = ($totals[$status][$activity][$unit] ?? 0) + $duration;
                        $totalCounts[$status][$activity][$unit] = ($totalCounts[$status][$activity][$unit] ?? 0) + $count;
                    }
                }
            }
        }

        $averageTotals = [];
        foreach ($totals as $status => $activityData) {
            foreach ($activityData as $activity => $unitData) {
                foreach ($unitData as $unit => $duration) {
                    $count = $totalCounts[$status][$activity][$unit] ?? 0;
                    $averageTotals[$status][$activity][$unit] = $count > 0 ? round($duration / $count, 2) : 0;
                }
            }
        }

        $averageStatusTotals = [];
        foreach ($statuses as $status) {
            foreach ($units as $unitData) {
                $unit = $unitData['id'];
                $duration = $count = 0;
                foreach ($totals[$status] ?? [] as $activity => $unitData2) {
                    $duration += $unitData2[$unit] ?? 0;
                    $count += $totalCounts[$status][$activity][$unit] ?? 0;
                }
                $averageStatusTotals[$status][$unit] = $count > 0 ? round($duration / $count, 2) : 0;
            }
        }

        $filteredActivities = array_fill_keys($statuses, []);
        foreach ($activitiesByStatus as $status => $activities) {
            foreach ($activities as $activity) {
                if (array_sum($totals[$status][$activity] ?? []) > 0) $filteredActivities[$status][] = $activity;
            }
        }

        return response()->json([
            'units' => $units,
            'hours' => $hours,
            'statuses' => $statuses,
            'activities' => $filteredActivities,
            'pivot' => $pivot,
            'averages' => $averages,
            'totals' => $totals,
            'averageTotals' => $averageTotals,
            'averageStatusTotals' => $averageStatusTotals,
            'counts' => $pivotCount,
            'dayCount' => $dayCount,
            'period' => ['start' => $shiftStart->format('Y-m-d H:i:s'), 'end' => $shiftEnd->format('Y-m-d H:i:s')],
        ]);
    }
}
