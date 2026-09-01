<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistribusiFrekuensiFuelStationController extends Controller
{
    //
    public function index()
    {
        return view('distribusiFrekuensi.fuelStation.index');
    }

    public function api(Request $request)
    {
        $tanggalInput = $request->input('tanggalStatus');
        $shift = $request->input('shift');

        if (empty($tanggalInput)) {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = $startDate;
        } else {
            if (str_contains($tanggalInput, 'to')) {
                [$startDate, $endDate] = array_map(
                    'trim',
                    explode('to', $tanggalInput)
                );
            } else {
                $startDate = trim($tanggalInput);
                $endDate = $startDate;
            }
        }

        $query = DB::connection('focus_reporting')
            ->table('VW_VSA_STATUSACTIVITYEX as A')
            ->leftJoin(
                'focus.dbo.LOC_REGION as B',
                'A.LOC_REGIONID',
                '=',
                'B.LOC_REGIONID'
            )
            ->select([
                'A.VHC_ID',
                'A.VHC_TYPEID',
                'A.VSA_STATUSID',
                'A.OPR_REPORTTIME',
                'A.OPR_SHIFTDATE',
                'A.OPR_SHIFTNO',
                'A.LOC_REGIONID',
                'A.LOC_NAME',
                'A.VSA_NOTES',
                DB::raw("
                    CASE
                        WHEN B.LOC_REGIONNAME = 'B1' THEN 'SM-B1'
                        WHEN B.LOC_REGIONNAME = 'B2' THEN 'SM-B2'
                    END AS LOC_REGIONNAME
                "),
            ])
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereIn('A.VHC_TYPEID', [2, 3])
                        ->where('A.VSA_STATUSID', 'S30');
                })
                ->orWhere(function ($q2) {
                    $q2->where('A.VHC_TYPEID', 5)
                        ->whereNotNull('A.VSA_NOTES')
                        ->where('A.VSA_NOTES', '<>', '');
                });
            })
            ->where('A.OPR_REPORTTIME', '>', '1970-01-01')
            ->whereIn('B.LOC_REGIONNAME', ['B1', 'B2']);

        $query->whereBetween('A.OPR_SHIFTDATE', [
            $startDate,
            $endDate
        ]);

        if (!empty($shift) && $shift !== 'Semua') {
            $query->where('A.OPR_SHIFTNO', $shift);
        }

        $data = $query
            ->orderBy('A.OPR_REPORTTIME')
            ->get();

        $units = [
            'Hauler',
            'Grader',
            'Dozer',
        ];

        $fuelStations = collect([
            'SM-B1',
            'SM-B2',
        ]);

        $hours = [];

        if ($shift === '7') {
            for ($i = 19; $i <= 23; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    ($i + 1) % 24
                );
            }

            for ($i = 0; $i <= 6; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $i + 1
                );
            }
        } elseif ($shift === 'Semua') {
            for ($i = 0; $i <= 23; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    ($i + 1) % 24
                );
            }
        } else {
            for ($i = 7; $i <= 18; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $i + 1
                );
            }
        }

        $pivot = [];

        foreach ($hours as $hour) {
            foreach ($units as $unit) {
                foreach ($fuelStations as $fuelStation) {
                    $pivot[$hour][$unit][$fuelStation] = 0;
                }
            }
        }

        foreach ($data as $row) {
            $region = trim($row->LOC_REGIONNAME ?? '');

            if ($region === '' || !$fuelStations->contains($region)) {
                continue;
            }

            $unitName = null;

            if ((int) $row->VHC_TYPEID === 2) {
                $unitName = 'Hauler';
            } elseif ((int) $row->VHC_TYPEID === 3) {
                $unitName = 'Grader';
            } elseif ((int) $row->VHC_TYPEID === 5) {
                if (empty($row->VSA_NOTES)) {
                    continue;
                }

                $notes = str_replace(
                    ["\r\n", "\r"],
                    "\n",
                    $row->VSA_NOTES
                );

                foreach (explode("\n", $notes) as $noteLine) {
                    $noteLine = trim($noteLine);

                    if ($noteLine === '') {
                        continue;
                    }

                    $parts = array_map(
                        'trim',
                        explode('|', $noteLine)
                    );

                    if (count($parts) < 3) {
                        continue;
                    }

                    $tempUnit = $parts[1];

                    if (in_array($tempUnit, $units, true)) {
                        $unitName = $tempUnit;
                        break;
                    }
                }
            }

            if (
                $unitName === null ||
                !in_array($unitName, $units, true)
            ) {
                continue;
            }

            try {
                $reportTime = Carbon::parse($row->OPR_REPORTTIME);
            } catch (\Throwable $e) {
                continue;
            }

            $hour = (int) $reportTime->format('H');

            $slot = sprintf(
                '%02d-%02d',
                $hour,
                ($hour + 1) % 24
            );

            if (!in_array($slot, $hours, true)) {
                continue;
            }

            $pivot[$slot][$unitName][$region]++;
        }

        $totalsByUnit = [];

        foreach ($units as $unit) {
            foreach ($fuelStations as $fuelStation) {
                $total = 0;

                foreach ($hours as $hour) {
                    $total += $pivot[$hour][$unit][$fuelStation] ?? 0;
                }

                $totalsByUnit[$unit][$fuelStation] = $total;
            }
        }

        $totalsByHour = [];

        foreach ($hours as $hour) {
            foreach ($fuelStations as $fuelStation) {
                $total = 0;

                foreach ($units as $unit) {
                    $total += $pivot[$hour][$unit][$fuelStation] ?? 0;
                }

                $totalsByHour[$hour][$fuelStation] = $total;
            }
        }

        $unitGrandTotal = [];

        foreach ($units as $unit) {
            $unitGrandTotal[$unit] = 0;

            foreach ($fuelStations as $fuelStation) {
                $unitGrandTotal[$unit] +=
                    $totalsByUnit[$unit][$fuelStation] ?? 0;
            }
        }

        $unitAverage = [];

        foreach ($units as $unit) {
            foreach ($fuelStations as $fuelStation) {
                $total = $totalsByUnit[$unit][$fuelStation] ?? 0;

                $unitAverage[$unit][$fuelStation] = count($hours) > 0
                    ? round($total / count($hours), 2)
                    : 0;
            }
        }

        $fuelStationGrandTotal = [];

        foreach ($fuelStations as $fuelStation) {
            $fuelStationGrandTotal[$fuelStation] = 0;

            foreach ($units as $unit) {
                $fuelStationGrandTotal[$fuelStation] +=
                    $totalsByUnit[$unit][$fuelStation] ?? 0;
            }
        }

        $grandTotal = array_sum($fuelStationGrandTotal);

        return response()->json([
            'hours' => $hours,
            'units' => $units,
            'fuelStations' => $fuelStations->values(),
            'pivot' => $pivot,
            'totalsByUnit' => $totalsByUnit,
            'totalsByHour' => $totalsByHour,
            'unitGrandTotal' => $unitGrandTotal,
            'unitAverage' => $unitAverage,
            'fuelStationGrandTotal' => $fuelStationGrandTotal,
            'grandTotal' => $grandTotal,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shift' => $shift,
        ]);
    }

    public function durasi()
    {
        return view('distribusiFrekuensi.fuelStation.durasi');
    }

    public function durasi_api(Request $request)
    {
        $tanggalInput = $request->input('tanggalStatus');
        $shift = $request->input('shift');

        if (empty($tanggalInput)) {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = $startDate;
        } else {
            if (str_contains($tanggalInput, 'to')) {
                [$startDate, $endDate] = array_map(
                    'trim',
                    explode('to', $tanggalInput)
                );
            } else {
                $startDate = trim($tanggalInput);
                $endDate = $startDate;
            }
        }

        $query = DB::connection('focus_reporting')
    ->table('VW_VSA_STATUSACTIVITYEX as A')
    ->leftJoin(
        'focus.dbo.LOC_REGION as B',
        'A.LOC_REGIONID',
        '=',
        'B.LOC_REGIONID'
    )
    ->select([
        'A.VHC_ID',
        'A.VHC_TYPEID',
        'A.VSA_STATUSID',
        'A.OPR_REPORTTIME',
        'A.OPR_ENDTIME',
        'A.OPR_SHIFTDATE',
        'A.OPR_SHIFTNO',
        'A.LOC_REGIONID',
        'A.LOC_NAME',
        'A.VSA_NOTES',
        DB::raw("
            CASE
                WHEN B.LOC_REGIONNAME = 'B1' THEN 'SM-B1'
                WHEN B.LOC_REGIONNAME = 'B2' THEN 'SM-B2'
            END AS LOC_REGIONNAME
        "),
    ])
    ->where(function ($q) {
        $q->where(function ($q2) {
            $q2->whereIn('A.VHC_TYPEID', [2, 3])
                ->where('A.VSA_STATUSID', 'S30');
        })
        ->orWhere(function ($q2) {
            $q2->where('A.VHC_TYPEID', 5)
                ->whereNotNull('A.VSA_NOTES')
                ->where('A.VSA_NOTES', '<>', '');
        });
    })
    ->where('A.OPR_REPORTTIME', '>', '1970-01-01')
    ->where('A.OPR_ENDTIME', '>', '1970-01-01')
    ->whereRaw('A.OPR_ENDTIME >= A.OPR_REPORTTIME')
    ->whereIn('B.LOC_REGIONNAME', ['B1', 'B2']);

        $query->whereBetween('A.OPR_SHIFTDATE', [
            $startDate,
            $endDate
        ]);

        if (!empty($shift) && $shift !== 'Semua') {
            $query->where('A.OPR_SHIFTNO', $shift);
        }

        $data = $query
            ->orderBy('A.OPR_REPORTTIME')
            ->get();

        $units = [
            'Hauler',
            'Grader',
            'Dozer',
        ];

        $fuelStations = [
            'SM-B1',
            'SM-B2',
        ];

        $hours = [];

        if ($shift === '7') {
            for ($i = 19; $i <= 23; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    ($i + 1) % 24
                );
            }

            for ($i = 0; $i <= 6; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $i + 1
                );
            }
        } elseif ($shift === 'Semua') {
            for ($i = 0; $i <= 23; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    ($i + 1) % 24
                );
            }
        } else {
            for ($i = 7; $i <= 18; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $i + 1
                );
            }
        }

        $durationPivot = [];
        $frequencyPivot = [];

        foreach ($fuelStations as $fuelStation) {
            foreach ($hours as $hour) {
                foreach ($units as $unit) {
                    $durationPivot[$fuelStation][$hour][$unit] = 0;
                    $frequencyPivot[$fuelStation][$hour][$unit] = 0;
                }
            }
        }

        foreach ($data as $row) {
            $fuelStation = trim(
                (string) ($row->LOC_REGIONNAME ?? '')
            );

            if (!in_array($fuelStation, $fuelStations, true)) {
                continue;
            }

            $unitName = null;

            if ((int) $row->VHC_TYPEID === 2) {
                $unitName = 'Hauler';
            } elseif ((int) $row->VHC_TYPEID === 3) {
                $unitName = 'Grader';
            } elseif ((int) $row->VHC_TYPEID === 5) {
                if (empty($row->VSA_NOTES)) {
                    continue;
                }

                $notes = str_replace(
                    ["\r\n", "\r"],
                    "\n",
                    $row->VSA_NOTES
                );

                foreach (explode("\n", $notes) as $noteLine) {
                    $noteLine = trim($noteLine);

                    if ($noteLine === '') {
                        continue;
                    }

                    $parts = array_map(
                        'trim',
                        explode('|', $noteLine)
                    );

                    if (count($parts) < 3) {
                        continue;
                    }

                    if (in_array($parts[1], $units, true)) {
                        $unitName = $parts[1];
                        break;
                    }
                }
            }

            if ($unitName === null) {
                continue;
            }

            try {
                $reportTime = Carbon::parse($row->OPR_REPORTTIME);
            } catch (\Throwable $e) {
                continue;
            }

            $hour = (int) $reportTime->format('H');

            $slot = sprintf(
                '%02d-%02d',
                $hour,
                ($hour + 1) % 24
            );

            if (!in_array($slot, $hours, true)) {
                continue;
            }

            $frequencyPivot[$fuelStation][$slot][$unitName]++;

            if (
                empty($row->OPR_ENDTIME) ||
                $row->OPR_ENDTIME <= '1970-01-01'
            ) {
                continue;
            }

            try {
                $endTime = Carbon::parse($row->OPR_ENDTIME);
            } catch (\Throwable $e) {
                continue;
            }

            if ($endTime->lte($reportTime)) {
                continue;
            }

            $durationMinutes =
                $reportTime->diffInSeconds($endTime) / 60;

            $durationPivot[$fuelStation][$slot][$unitName]
                += $durationMinutes;
        }

        $allDurationPivot = [];
        $allFrequencyPivot = [];

        foreach ($hours as $hour) {
            foreach ($units as $unit) {
                $allDurationPivot[$hour][$unit] = 0;
                $allFrequencyPivot[$hour][$unit] = 0;

                foreach ($fuelStations as $fuelStation) {
                    $allDurationPivot[$hour][$unit] +=
                        $durationPivot[$fuelStation][$hour][$unit] ?? 0;

                    $allFrequencyPivot[$hour][$unit] +=
                        $frequencyPivot[$fuelStation][$hour][$unit] ?? 0;
                }

                $allDurationPivot[$hour][$unit] = round(
                    $allDurationPivot[$hour][$unit],
                    2
                );
            }
        }

        $durationByUnit = [];

        foreach ($fuelStations as $fuelStation) {
            foreach ($units as $unit) {
                $total = 0;

                foreach ($hours as $hour) {
                    $total +=
                        $durationPivot[$fuelStation][$hour][$unit] ?? 0;
                }

                $durationByUnit[$fuelStation][$unit] = round(
                    $total,
                    2
                );
            }
        }

        $frequencyByUnit = [];

        foreach ($fuelStations as $fuelStation) {
            foreach ($units as $unit) {
                $total = 0;

                foreach ($hours as $hour) {
                    $total +=
                        $frequencyPivot[$fuelStation][$hour][$unit] ?? 0;
                }

                $frequencyByUnit[$fuelStation][$unit] = $total;
            }
        }

        $averageDurationTotal = [];

        foreach ($fuelStations as $fuelStation) {
            foreach ($units as $unit) {
                $totalDuration =
                    $durationByUnit[$fuelStation][$unit] ?? 0;

                $totalFrequency =
                    $frequencyByUnit[$fuelStation][$unit] ?? 0;

                $averageDurationTotal[$fuelStation][$unit] =
                    $totalFrequency > 0
                        ? round(
                            $totalDuration / $totalFrequency,
                            2
                        )
                        : 0;
            }
        }

        $averageDuration = [];

        foreach ($fuelStations as $fuelStation) {
            foreach ($hours as $hour) {
                foreach ($units as $unit) {
                    $duration =
                        $durationPivot[$fuelStation][$hour][$unit] ?? 0;

                    $frequency =
                        $frequencyPivot[$fuelStation][$hour][$unit] ?? 0;

                    $averageDuration[$fuelStation][$hour][$unit] =
                        $frequency > 0
                            ? round($duration / $frequency, 2)
                            : 0;
                }
            }
        }

        $allAverageDuration = [];

        foreach ($hours as $hour) {
            foreach ($units as $unit) {
                $duration =
                    $allDurationPivot[$hour][$unit] ?? 0;

                $frequency =
                    $allFrequencyPivot[$hour][$unit] ?? 0;

                $allAverageDuration[$hour][$unit] =
                    $frequency > 0
                        ? round($duration / $frequency, 2)
                        : 0;
            }
        }

        $allAverageDurationTotal = [];

        foreach ($units as $unit) {
            $totalDuration = 0;
            $totalFrequency = 0;

            foreach ($hours as $hour) {
                $totalDuration +=
                    $allDurationPivot[$hour][$unit] ?? 0;

                $totalFrequency +=
                    $allFrequencyPivot[$hour][$unit] ?? 0;
            }

            $allAverageDurationTotal[$unit] =
                $totalFrequency > 0
                    ? round(
                        $totalDuration / $totalFrequency,
                        2
                    )
                    : 0;
        }

        $fuelStationGrandTotal = [];
        $fuelStationGrandFrequency = [];

        foreach ($fuelStations as $fuelStation) {
            $totalDuration = 0;
            $totalFrequency = 0;

            foreach ($units as $unit) {
                $totalDuration +=
                    $durationByUnit[$fuelStation][$unit] ?? 0;

                $totalFrequency +=
                    $frequencyByUnit[$fuelStation][$unit] ?? 0;
            }

            $fuelStationGrandTotal[$fuelStation] =
                round($totalDuration, 2);

            $fuelStationGrandFrequency[$fuelStation] =
                $totalFrequency;
        }

        $grandTotal = round(
            array_sum($fuelStationGrandTotal),
            2
        );

        $grandFrequency = array_sum(
            $fuelStationGrandFrequency
        );

        $grandAverage = $grandFrequency > 0
            ? round($grandTotal / $grandFrequency, 2)
            : 0;

        return response()->json([
            'hours' => $hours,
            'units' => $units,
            'fuelStations' => $fuelStations,
            'durationPivot' => $durationPivot,
            'frequencyPivot' => $frequencyPivot,
            'allDurationPivot' => $allDurationPivot,
            'allFrequencyPivot' => $allFrequencyPivot,
            'allAverageDuration' => $allAverageDuration,
            'allAverageDurationTotal' => $allAverageDurationTotal,
            'durationByUnit' => $durationByUnit,
            'frequencyByUnit' => $frequencyByUnit,
            'averageDuration' => $averageDuration,
            'averageDurationTotal' => $averageDurationTotal,
            'fuelStationGrandTotal' => $fuelStationGrandTotal,
            'fuelStationGrandFrequency' => $fuelStationGrandFrequency,
            'grandTotal' => $grandTotal,
            'grandFrequency' => $grandFrequency,
            'grandAverage' => $grandAverage,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shift' => $shift,
        ]);
    }

}
