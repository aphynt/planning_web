<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusAvailabilityController extends Controller
{
    //
    public function index()
    {
        return view('statusAvailability.index');
    }

    public function grafik()
    {
        return view('statusAvailability.grafik');
    }

    public function api(Request $request)
    {
        $query = DB::connection('focus_reporting')
            ->table('dbo.VW_VSA_STATUSACTIVITYEX as A')
            ->leftJoin(
                'FOCUS.dbo.FLT_VSAGROUP as B',
                'A.VSA_GROUPID',
                '=',
                'B.VSA_GROUPID'
            )
            ->select([
                'A.VHC_ID',
                'A.OPR_REPORTTIME',
                'A.OPR_ENDTIME',
                'B.VSA_GROUPDESC',
                DB::raw(
                    'DATEDIFF_BIG(
                        SECOND,
                        A.OPR_REPORTTIME,
                        A.OPR_ENDTIME
                    ) / 60.0 AS DURATION'
                )
            ])
            ->where('A.VHC_TYPEID', 5);
        $tanggalInput = $request->input('tanggalStatus');
        $shift        = $request->input('shift');
        $aggregation = $request->input('aggregation', 'total');
        $vhc_id       = $request->input('vhc_id');

        if (empty($tanggalInput)) {
            $operationalDate = Carbon::now()->subHours(7)->format('Y-m-d');
            $startDate = $operationalDate;
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

        $dayCount = Carbon::parse($startDate)
            ->diffInDays(Carbon::parse($endDate)) + 1;

        if ($dayCount < 1) {
            $dayCount = 1;
        }

        if ($shift == '6') {
            $reportStart = Carbon::parse(
                $startDate . ' 07:00:00'
            );

            $reportEnd = Carbon::parse(
                $endDate . ' 19:00:00'
            );

        } elseif ($shift == '7') {
            $reportStart = Carbon::parse(
                $startDate . ' 19:00:00'
            );

            $reportEnd = Carbon::parse(
                $endDate . ' 07:00:00'
            )->addDay();

        } else {
            $reportStart = Carbon::parse(
                $startDate . ' 07:00:00'
            );

            $reportEnd = Carbon::parse(
                $endDate . ' 07:00:00'
            )->addDay();
        }

        $query->where(function ($q) use ($reportStart, $reportEnd) {
            $q->where(
                'A.OPR_ENDTIME',
                '>',
                $reportStart
            )
            ->where(
                'A.OPR_REPORTTIME',
                '<',
                $reportEnd
            );
        });
        if (!empty($vhc_id) && $vhc_id != 'Semua') {
            $query->where(
                'A.VHC_ID',
                $vhc_id
            );
        }

        $query->where(
            'A.OPR_REPORTTIME',
            '>',
            '1970-01-01'
        )
        ->where(
            'A.OPR_ENDTIME',
            '>',
            '1970-01-01'
        )
        ->whereRaw(
            'A.OPR_ENDTIME >= A.OPR_REPORTTIME'
        );

        $now = Carbon::now();
        if (
            $startDate === $now->format('Y-m-d') ||
            $endDate === $now->format('Y-m-d')
        ) {

            $query->where(
                'A.OPR_REPORTTIME',
                '<=',
                $now
            );
        }
        $vwData = $query
            ->orderBy('A.VHC_ID')
            ->orderBy('A.OPR_REPORTTIME')
            ->get();
        $unitQuery = DB::connection('focus')
            ->table('FLT_VEHICLE as A')
            ->leftJoin(
                'FLT_VSAGROUP as B',
                'A.VSA_GROUPID',
                '=',
                'B.VSA_GROUPID'
            )
            ->where('A.VHC_TYPEID', 5)
            ->where('A.VHC_ACTIVE', 1)
            ->select([
                'A.VHC_ID',
                'A.OPR_NAME',
                'A.LOC_NAME',
                DB::raw(
                    "COALESCE(B.VSA_GROUPDESC, 'Ready') AS STATUS"
                )
            ]);
        if (!empty($vhc_id) && $vhc_id != 'Semua') {
            $unitQuery->where(
                'A.VHC_ID',
                $vhc_id
            );
        }

        $units = $unitQuery
            ->orderBy('A.VHC_ID')
            ->get()
            ->map(function ($unit) {

                return [
                    'id'     => $unit->VHC_ID,
                    'status' => $unit->STATUS ?? 'Ready',
                    'opr_name' => $unit->OPR_NAME ?? null,
                    'loc_name' => $unit->LOC_NAME ?? null,
                ];

            })
            ->values();

        $allUnitIds = $units
            ->pluck('id')
            ->values();

        $vwUnitIds = $vwData
            ->pluck('VHC_ID')
            ->unique()
            ->values();

        $missingUnitIds = $allUnitIds
            ->diff($vwUnitIds)
            ->values();

        $trkData = collect();

        if ($missingUnitIds->isNotEmpty()) {

            $trkData = $this->getFallbackFromTrkLog(
                $missingUnitIds->toArray(),
                $reportStart,
                $reportEnd
            );
        }

        $data = $vwData
            ->concat($trkData)
            ->sortBy([
                ['VHC_ID', 'asc'],
                ['OPR_REPORTTIME', 'asc'],
            ])
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
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $nextHour
                );
            }

            for ($i = 0; $i <= 6; $i++) {
                $nextHour = $i + 1;
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $nextHour
                );
            }

        } elseif ($shift == '6') {
            for ($i = 7; $i <= 18; $i++) {
                $nextHour = $i + 1;
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $nextHour
                );
            }

        } else {
            for ($i = 7; $i <= 23; $i++) {
                $nextHour = ($i + 1) % 24;
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $nextHour
                );
            }

            for ($i = 0; $i <= 6; $i++) {
                $nextHour = $i + 1;
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $nextHour
                );
            }
        }
        if ($shift == '7') {
            $shiftStart = Carbon::parse(
                $startDate . ' 19:00:00'
            );

            $shiftEnd = Carbon::parse(
                $startDate . ' 07:00:00'
            )->addDay();

        } elseif ($shift == '6') {
            $shiftStart = Carbon::parse(
                $startDate . ' 07:00:00'
            );

            $shiftEnd = Carbon::parse(
                $startDate . ' 19:00:00'
            );

        } else {
            $shiftStart = Carbon::parse(
                $startDate . ' 07:00:00'
            );

            $shiftEnd = Carbon::parse(
                $startDate . ' 07:00:00'
            )->addDay();
        }
        if ($now->betweenIncluded($shiftStart, $shiftEnd)) {
            $elapsedMinutes = $shiftStart->diffInMinutes($now);
            $visibleSlots = (int) floor($elapsedMinutes / 60) + 1;
            $visibleSlots = max(1, min(count($hours), $visibleSlots));

            $hours = array_slice(
                $hours,
                0,
                $visibleSlots
            );
        }

        $pivot = [];

        $addToPivot = function (
            $slot,
            $status,
            $unit,
            $duration
        ) use (&$pivot) {

            if ($duration <= 0) {
                return;
            }

            if (!isset($pivot[$slot])) {
                $pivot[$slot] = [];
            }

            if (!isset($pivot[$slot][$status])) {
                $pivot[$slot][$status] = [];
            }

            if (!isset($pivot[$slot][$status][$unit])) {
                $pivot[$slot][$status][$unit] = 0;
            }

            $pivot[$slot][$status][$unit] += $duration;
        };


        $dataByUnit = $data->groupBy('VHC_ID');
        $loopDate = Carbon::parse($startDate);
        $lastDate = Carbon::parse($endDate);

        while ($loopDate->lte($lastDate)) {
            $currentDate = $loopDate->format('Y-m-d');
            if ($shift == '7') {
                $dailyShiftStart = Carbon::parse(
                    $currentDate . ' 19:00:00'
                );

                $dailyShiftEnd = Carbon::parse(
                    $currentDate . ' 07:00:00'
                )->addDay();

            } elseif ($shift == '6') {
                $dailyShiftStart = Carbon::parse(
                    $currentDate . ' 07:00:00'
                );

                $dailyShiftEnd = Carbon::parse(
                    $currentDate . ' 19:00:00'
                );

            } else {
                $dailyShiftStart = Carbon::parse(
                    $currentDate . ' 07:00:00'
                );

                $dailyShiftEnd = Carbon::parse(
                    $currentDate . ' 07:00:00'
                )->addDay();
            }

            if ($now->lt($dailyShiftStart)) {
                $loopDate->addDay();
                continue;
            }

            $effectiveEnd = $dailyShiftEnd->copy();
            if (
                $now->betweenIncluded(
                    $dailyShiftStart,
                    $dailyShiftEnd
                )
            ) {
                $effectiveEnd = $now->copy();
            }


            if ($effectiveEnd->lte($dailyShiftStart)) {
                $loopDate->addDay();
                continue;
            }

            $dayPivot = [];
            $addToDayPivot = function (
                $slot,
                $status,
                $unit,
                $duration
            ) use (&$dayPivot) {

                if ($duration <= 0) {
                    return;
                }

                if (!isset($dayPivot[$slot])) {
                    $dayPivot[$slot] = [];
                }

                if (!isset($dayPivot[$slot][$status])) {
                    $dayPivot[$slot][$status] = [];
                }

                if (!isset($dayPivot[$slot][$status][$unit])) {
                    $dayPivot[$slot][$status][$unit] = 0;
                }

                $dayPivot[$slot][$status][$unit] += $duration;
            };

            foreach ($units as $unit) {
                $unitId = $unit['id'];
                $unitData = $dataByUnit
                    ->get($unitId, collect())
                    ->filter(function ($row) use (
                        $dailyShiftStart,
                        $effectiveEnd
                    ) {

                        $start = Carbon::parse(
                            $row->OPR_REPORTTIME
                        );

                        $end = Carbon::parse(
                            $row->OPR_ENDTIME
                        );

                        return
                            $end->gt($dailyShiftStart)
                            &&
                            $start->lt($effectiveEnd)
                            &&
                            $end->gt($start);
                    })
                    ->sortBy('OPR_REPORTTIME')
                    ->values();

                if ($unitData->isEmpty()) {
                    $current = $dailyShiftStart->copy();
                    while ($current->lt($effectiveEnd)) {
                        $nextHour = $current
                            ->copy()
                            ->addHour();

                        if ($nextHour->gt($effectiveEnd)) {
                            $nextHour = $effectiveEnd->copy();
                        }

                        $duration =
                            $current->diffInSeconds($nextHour)
                            / 60;

                        $hour = (int) $current->format('H');

                        $slot = sprintf(
                            '%02d-%02d',
                            $hour,
                            ($hour + 1) % 24
                        );

                        if (in_array($slot, $hours)) {

                            $addToDayPivot(
                                $slot,
                                'Standby',
                                $unitId,
                                $duration
                            );
                        }

                        $current = $nextHour;
                    }

                    continue;
                }

                $lastEnd = $dailyShiftStart->copy();
                foreach ($unitData as $row) {
                    $start = Carbon::parse(
                        $row->OPR_REPORTTIME
                    );

                    $end = Carbon::parse(
                        $row->OPR_ENDTIME
                    );

                    if ($start->lt($dailyShiftStart)) {
                        $start = $dailyShiftStart->copy();
                    }

                    if ($end->gt($effectiveEnd)) {
                        $end = $effectiveEnd->copy();
                    }


                    if ($end->lte($start)) {
                        continue;
                    }
                    if ($start->gt($lastEnd)) {
                        $gapStart = $lastEnd->copy();
                        $gapEnd   = $start->copy();

                        $currentGap = $gapStart->copy()->startOfHour();

                        while ($currentGap->lt($gapEnd)) {
                            $nextHour = $currentGap->copy()->addHour();

                            $overlapStart =
                                $gapStart->greaterThan($currentGap)
                                    ? $gapStart->copy()
                                    : $currentGap->copy();

                            $overlapEnd =
                                $gapEnd->lessThan($nextHour)
                                    ? $gapEnd->copy()
                                    : $nextHour->copy();


                            if ($overlapEnd->gt($overlapStart)) {

                                $duration =
                                    $overlapStart
                                        ->diffInSeconds(
                                            $overlapEnd
                                        ) / 60;

                                $hour =
                                    (int) $currentGap->format('H');

                                $slot = sprintf(
                                    '%02d-%02d',
                                    $hour,
                                    ($hour + 1) % 24
                                );

                                if (in_array($slot, $hours)) {

                                    $addToDayPivot(
                                        $slot,
                                        'Standby',
                                        $unitId,
                                        $duration
                                    );
                                }
                            }

                            $currentGap = $nextHour;
                        }
                    }

                    $status = trim(
                        $row->VSA_GROUPDESC ?? ''
                    );

                    if (!in_array($status, $statuses)) {
                        $status = 'Standby';
                    }

                    $current = $start->copy()->startOfHour();
                    while ($current->lt($end)) {

                        $nextHour =
                            $current->copy()->addHour();

                        $overlapStart =
                            $start->greaterThan($current)
                                ? $start->copy()
                                : $current->copy();

                        $overlapEnd =
                            $end->lessThan($nextHour)
                                ? $end->copy()
                                : $nextHour->copy();


                        if ($overlapEnd->gt($overlapStart)) {

                            $duration =
                                $overlapStart
                                    ->diffInSeconds(
                                        $overlapEnd
                                    ) / 60;

                            $hour =
                                (int) $current->format('H');

                            $slot = sprintf(
                                '%02d-%02d',
                                $hour,
                                ($hour + 1) % 24
                            );


                            if (in_array($slot, $hours)) {

                                $addToDayPivot(
                                    $slot,
                                    $status,
                                    $unitId,
                                    $duration
                                );
                            }
                        }

                        $current = $nextHour;
                    }


                    if ($end->gt($lastEnd)) {
                        $lastEnd = $end->copy();
                    }
                }

                if ($lastEnd->lt($effectiveEnd)) {
                    $gapStart = $lastEnd->copy();
                    $gapEnd   = $effectiveEnd->copy();
                    $currentGap = $gapStart->copy()->startOfHour();

                    while ($currentGap->lt($gapEnd)) {
                        $nextHour = $currentGap->copy()->addHour();

                        $overlapStart =
                            $gapStart->greaterThan($currentGap)
                                ? $gapStart->copy()
                                : $currentGap->copy();

                        $overlapEnd =
                            $gapEnd->lessThan($nextHour)
                                ? $gapEnd->copy()
                                : $nextHour->copy();


                        if ($overlapEnd->gt($overlapStart)) {

                            $duration =
                                $overlapStart
                                    ->diffInSeconds(
                                        $overlapEnd
                                    ) / 60;

                            $hour =
                                (int) $currentGap->format('H');

                            $slot = sprintf(
                                '%02d-%02d',
                                $hour,
                                ($hour + 1) % 24
                            );


                            if (in_array($slot, $hours)) {

                                $addToDayPivot(
                                    $slot,
                                    'Standby',
                                    $unitId,
                                    $duration
                                );
                            }
                        }

                        $currentGap = $nextHour;
                    }
                }
            }

            $currentHour = $dailyShiftStart->copy();
            while ($currentHour->lt($effectiveEnd)) {
                $nextHour = $currentHour->copy()->addHour();

                if ($nextHour->gt($effectiveEnd)) {
                    $nextHour = $effectiveEnd->copy();
                }
                $hour = (int) $currentHour->format('H');

                $slot = sprintf(
                    '%02d-%02d',
                    $hour,
                    ($hour + 1) % 24
                );

                if (in_array($slot, $hours)) {

                    $expectedMinutes =
                        $currentHour->diffInSeconds(
                            $nextHour
                        ) / 60;

                    foreach ($units as $unit) {
                        $unitId = $unit['id'];
                        $usedMinutes = 0;
                        if (isset($dayPivot[$slot])) {
                            foreach ($statuses as $status) {
                                $usedMinutes +=
                                    $dayPivot[$slot][$status][$unitId]
                                    ?? 0;
                            }
                        }


                        if ($usedMinutes < $expectedMinutes) {
                            $remaining = $expectedMinutes - $usedMinutes;
                            $addToDayPivot(
                                $slot,
                                'Standby',
                                $unitId,
                                $remaining
                            );
                        }
                    }
                }

                $currentHour = $nextHour;
            }

            foreach ($dayPivot as $slot => $statusesData) {
                foreach ($statusesData as $status => $unitsData) {
                    foreach ($unitsData as $unitId => $duration) {
                        $addToPivot(
                            $slot,
                            $status,
                            $unitId,
                            $duration
                        );
                    }
                }
            }


            $loopDate->addDay();
        }

        $orderedPivot = [];
        foreach ($hours as $slot) {
            $orderedPivot[$slot] =
                $pivot[$slot] ?? [];
        }

        $pivot = $orderedPivot;
        $totals = [];

        foreach ($pivot as $hour => $statusesData) {
            foreach ($statusesData as $status => $unitsData) {
                foreach ($unitsData as $unit => $duration) {
                    if (!isset($totals[$status][$unit])) {
                        $totals[$status][$unit] = 0;
                    }

                    $totals[$status][$unit] += $duration;
                }
            }
        }

        foreach ($totals as $status => &$unitsData) {
            foreach ($unitsData as $unit => &$duration) {
                $duration = round($duration / 60, 2);
            }
        }

        unset($unitsData, $duration);
        $hoursPerDay = ($shift == 'Semua' || empty($shift))
            ? 24
            : 12;

        $baseHours = $dayCount * $hoursPerDay;
        $averages = [];
        foreach ($statuses as $status) {
            foreach ($units as $unit) {
                $unitId = $unit['id'];
                $total = $totals[$status][$unitId] ?? 0;
                $averages[$status][$unitId] =
                    $baseHours > 0
                        ? round($total / $baseHours, 2)
                        : 0;
            }
        }

        foreach ($pivot as $hour => &$statusesData) {
            foreach ($statusesData as $status => &$unitsData) {
                foreach ($unitsData as $unit => &$duration) {
                    $duration = round($duration / 60, 2);
                }
            }
        }

        unset($statusesData, $unitsData, $duration);

        $chartPivot = [];

        foreach ($hours as $hour) {

            foreach ($statuses as $status) {

                foreach ($units as $unit) {

                    $unitId = $unit['id'];

                    $totalMinutes =
                        $pivot[$hour][$status][$unitId] ?? 0;

                    $minutes = $totalMinutes * 60;

                    $normalizedMinutes =
                        $dayCount > 0
                            ? $minutes / $dayCount
                            : 0;

                    $chartPivot[$hour][$status][$unitId] =
                        round(
                            min(60, max(0, $normalizedMinutes)),
                            2
                        );
                }
            }
        }

        $chartAverage = [];
        foreach ($hours as $hour) {
            foreach ($statuses as $status) {
                $sum = 0;
                $unitCount = count($units);
                foreach ($units as $unit) {
                    $unitId = $unit['id'];
                    $sum +=
                        $chartPivot[$hour][$status][$unitId]
                        ?? 0;
                }

                $chartAverage[$hour][$status] =
                    $unitCount > 0
                        ? round($sum / $unitCount, 2)
                        : 0;
            }
        }

        return response()->json([
            'units'         => $units,
            'hours'         => $hours,
            'statuses'      => $statuses,
            'pivot'         => $pivot,
            'chartPivot'    => $chartPivot,
            'chartAverage'  => $chartAverage,
            'totals'        => $totals,
            'averages'      => $averages,
            'dayCount'      => $dayCount
        ]);
    }

    private function getFallbackFromTrkLog(
        array $unitIds,
        Carbon $reportStart,
        Carbon $reportEnd
    ) {
        if (empty($unitIds)) {
            return collect();
        }

        $lookbackStart = $reportStart
            ->copy()
            ->subMinutes(10);

        $placeholders = implode(
            ',',
            array_fill(0, count($unitIds), '?')
        );

        $sql = "
            WITH RawData AS
            (
                SELECT
                    T.VHC_ID,
                    T.OPR_REPORTTIME,
                    T.VSA_GROUPID,

                    CASE
                        WHEN
                            LAG(
                                ISNULL(T.VSA_GROUPID, -999)
                            ) OVER (
                                PARTITION BY T.VHC_ID
                                ORDER BY T.OPR_REPORTTIME
                            )
                            =
                            ISNULL(T.VSA_GROUPID, -999)

                        THEN 0
                        ELSE 1
                    END AS IS_NEW_GROUP

                FROM FOCUS_REPORTING.dbo.VW_TRK_LOG T

                WHERE T.VHC_TYPEID = 5

                AND T.VHC_ID IN ({$placeholders})

                AND T.OPR_REPORTTIME >= ?
                AND T.OPR_REPORTTIME < ?
            ),

            GroupedData AS
            (
                SELECT
                    VHC_ID,
                    OPR_REPORTTIME,
                    VSA_GROUPID,

                    SUM(IS_NEW_GROUP) OVER (
                        PARTITION BY VHC_ID
                        ORDER BY OPR_REPORTTIME
                        ROWS UNBOUNDED PRECEDING
                    ) AS GROUP_ID

                FROM RawData
            ),

            StatusSummary AS
            (
                SELECT
                    VHC_ID,
                    VSA_GROUPID,
                    GROUP_ID,

                    MIN(OPR_REPORTTIME) AS START_TIME,
                    MAX(OPR_REPORTTIME) AS LAST_TIME

                FROM GroupedData

                GROUP BY
                    VHC_ID,
                    VSA_GROUPID,
                    GROUP_ID
            ),

            StatusInterval AS
            (
                SELECT
                    VHC_ID,
                    VSA_GROUPID,
                    START_TIME,
                    LAST_TIME,

                    LEAD(START_TIME) OVER (
                        PARTITION BY VHC_ID
                        ORDER BY START_TIME
                    ) AS NEXT_START_TIME

                FROM StatusSummary
            ),

            FinalInterval AS
            (
                SELECT
                    VHC_ID,
                    VSA_GROUPID,
                    START_TIME AS OPR_REPORTTIME,

                    CASE
                        WHEN NEXT_START_TIME IS NOT NULL
                            THEN NEXT_START_TIME
                        ELSE DATEADD(
                            SECOND,
                            1,
                            LAST_TIME
                        )
                    END AS OPR_ENDTIME

                FROM StatusInterval
            )

            SELECT
                A.VHC_ID,

                A.OPR_REPORTTIME,
                A.OPR_ENDTIME,

                COALESCE(
                    B.VSA_GROUPDESC,
                    'Standby'
                ) AS VSA_GROUPDESC,

                DATEDIFF_BIG(
                    SECOND,
                    A.OPR_REPORTTIME,
                    A.OPR_ENDTIME
                ) / 60.0 AS DURATION

            FROM FinalInterval A

            LEFT JOIN dbo.FLT_VSAGROUP B
                ON A.VSA_GROUPID = B.VSA_GROUPID

            WHERE A.OPR_ENDTIME > ?
            AND A.OPR_REPORTTIME < ?

            ORDER BY
                A.VHC_ID,
                A.OPR_REPORTTIME
        ";

        $bindings = [];

        foreach ($unitIds as $unitId) {
            $bindings[] = $unitId;
        }

        $bindings[] = $lookbackStart->format('Y-m-d H:i:s');
        $bindings[] = $reportEnd->format('Y-m-d H:i:s');

        // Filter final
        $bindings[] = $reportStart->format('Y-m-d H:i:s');
        $bindings[] = $reportEnd->format('Y-m-d H:i:s');

        $results = DB::connection('focus')
            ->select(
                $sql,
                $bindings
            );

        return collect($results);
    }
}
