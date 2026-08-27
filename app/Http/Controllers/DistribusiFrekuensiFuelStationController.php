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
        $shift        = $request->input('shift');
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

        $query = DB::connection('focus')
            ->table('VSA_STATUSACTIVITYEX as A')
            ->leftJoin(
                'LOC_REGION as B',
                'A.LOC_REGIONID',
                '=',
                'B.LOC_REGIONID'
            )
            ->select([
                'A.VHC_ID',
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
            ->where('A.VHC_TYPEID', 5)
            ->where('A.OPR_REPORTTIME', '>', '1970-01-01')
            ->whereNotNull('A.VSA_NOTES')
            ->where('A.VSA_NOTES', '<>', '')
            ->whereIn('B.LOC_REGIONNAME', [
                'B1',
                'B2',
            ])
            ->where(function ($q) {
                $q->where('A.VSA_NOTES', 'LIKE', '%Hauler%')
                ->orWhere('A.VSA_NOTES', 'LIKE', '%Dozer%')
                ->orWhere('A.VSA_NOTES', 'LIKE', '%Grader%');
            });

        $query->whereBetween('A.OPR_SHIFTDATE', [
            $startDate,
            $endDate
        ]);

        if (!empty($shift) && $shift !== 'Semua') {
            $query->where(
                'A.OPR_SHIFTNO',
                $shift
            );
        }
        $data = $query
            ->orderBy('A.OPR_REPORTTIME')
            ->get();
        $units = [
            'Hauler',
            'Grader',
            'Dozer',
        ];
        $fuelStations = $data
        ->pluck('LOC_REGIONNAME')
        ->filter()
        ->unique()
        ->sort()
        ->values();

        $hours = [];
        if ($shift === '7') {
            for ($i = 19; $i <= 23; $i++) {
                $nextHour = ($i + 1) % 24;
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $nextHour
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
                $nextHour = ($i + 1) % 24;
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $nextHour
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
            if (empty($row->VSA_NOTES)) {
                continue;
            }
            $region = trim($row->LOC_REGIONNAME);

            if ($region === '') {
                continue;
            }
            if ($fuelStation === '') {
                continue;
            }

            try {
                $reportTime = Carbon::parse(
                    $row->OPR_REPORTTIME
                );
            } catch (\Throwable $e) {
                continue;
            }

            $hour = (int) $reportTime->format('H');
            $nextHour = ($hour + 1) % 24;
            $slot = sprintf(
                '%02d-%02d',
                $hour,
                $nextHour
            );

            if (!in_array(
                $slot,
                $hours,
                true
            )) {
                continue;
            }

            $notes = str_replace(
                ["\r\n", "\r"],
                "\n",
                $row->VSA_NOTES
            );

            $noteLines = explode(
                "\n",
                $notes
            );

            foreach ($noteLines as $noteLine) {
                $noteLine = trim(
                    $noteLine
                );

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

                $unitName = $parts[1];
                if (!in_array(
                    $unitName,
                    $units,
                    true
                )) {
                    continue;
                }

                $pivot[$slot][$unitName][$region]++;
            }
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
            $total = 0;
            foreach ($fuelStations as $fuelStation) {
                $total += $totalsByUnit[$unit][$fuelStation] ?? 0;
            }

            $unitGrandTotal[$unit] = $total;
        }

        $fuelStationGrandTotal = [];
        foreach ($fuelStations as $fuelStation) {
            $total = 0;
            foreach ($units as $unit) {
                $total += $totalsByUnit[$unit][$fuelStation] ?? 0;
            }
            $fuelStationGrandTotal[$fuelStation] = $total;
        }
        $grandTotal = array_sum($fuelStationGrandTotal);

        return response()->json([
            'hours'                 => $hours,
            'units'                 => $units,
            'fuelStations'          => $fuelStations,
            'pivot'                 => $pivot,
            'totalsByUnit'          => $totalsByUnit,
            'totalsByHour'          => $totalsByHour,
            'unitGrandTotal'        => $unitGrandTotal,
            'fuelStationGrandTotal' => $fuelStationGrandTotal,
            'grandTotal'            => $grandTotal,
            'startDate'             => $startDate,
            'endDate'               => $endDate,
            'shift'                 => $shift,
        ]);
    }

    public function durasi()
    {
        return view('distribusiFrekuensi.fuelStation.durasi');
    }

    public function durasi_api(Request $request)
    {
        $tanggalInput = $request->input('tanggalStatus');
        $shift        = $request->input('shift');

        $summaryMode = $request->input('summaryMode', 'total');

        if (!in_array($summaryMode, ['total', 'average'], true)) {
            $summaryMode = 'total';
        }

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
        $query = DB::connection('focus')
            ->table('VSA_STATUSACTIVITYEX as A')
            ->select([
                'A.VHC_ID',
                'A.OPR_REPORTTIME',
                'A.OPR_ENDTIME',
                'A.OPR_SHIFTDATE',
                'A.OPR_SHIFTNO',
                'A.VSA_NOTES',
            ])
            ->where('A.VHC_TYPEID', 5)
            ->where('A.OPR_REPORTTIME', '>', '1970-01-01')
            ->where('A.OPR_ENDTIME', '>', '1970-01-01')
            ->whereRaw('A.OPR_ENDTIME >= A.OPR_REPORTTIME');

        $query->whereBetween('A.OPR_SHIFTDATE', [
            $startDate,
            $endDate
        ]);

        if (!empty($shift) && $shift !== 'Semua') {

            $query->where(
                'A.OPR_SHIFTNO',
                $shift
            );
        }

        $data = $query
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
            DB::raw(
                "COALESCE(B.VSA_GROUPDESC, 'Ready') AS STATUS"
            )
        ]);

        $units = $unitQuery
            ->orderBy('A.VHC_ID')
            ->get()
            ->map(function ($unit) {
                return [
                    'id'     => $unit->VHC_ID,
                    'status' => trim($unit->STATUS ?? 'Ready'),
                ];
            })
            ->values();

        $fuelTrucks = $units
            ->pluck('id')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $fuelTruckStatus = $units
            ->pluck('status', 'id')
            ->toArray();
        $units = [
            'Hauler',
            'Grader',
            'Dozer',
        ];
        $hours = [];

        if ($shift === '7') {
            for ($i = 19; $i <= 23; $i++) {
                $nextHour = ($i + 1) % 24;
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $nextHour
                );
            }


            for ($i = 0; $i <= 6; $i++) {
                $hours[] = sprintf(
                    '%02d-%02d',
                    $i,
                    $i + 1
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
        foreach ($hours as $hour) {
            foreach ($units as $unit) {
                foreach ($fuelTrucks as $fuelTruck) {
                    $durationPivot[$hour][$unit][$fuelTruck] = 0;
                }
            }
        }
        $frequencyPivot = [];
        foreach ($hours as $hour) {
            foreach ($units as $unit) {
                foreach ($fuelTrucks as $fuelTruck) {
                    $frequencyPivot[$hour][$unit][$fuelTruck] = 0;
                }
            }
        }

        foreach ($data as $row) {
            if (empty($row->VSA_NOTES)) {
                continue;
            }
            $fuelTruck = trim(
                $row->VHC_ID
            );

            if ($fuelTruck === '') {
                continue;
            }
            try {

                $reportTime = Carbon::parse(
                    $row->OPR_REPORTTIME
                );

                $endTime = Carbon::parse(
                    $row->OPR_ENDTIME
                );

            } catch (\Throwable $e) {

                continue;
            }
            if ($endTime->lte($reportTime)) {
                continue;
            }

            $durationMinutes = $reportTime->diffInSeconds($endTime) / 60;
            $hour = (int) $reportTime->format('H');
            $nextHour = ($hour + 1) % 24;

            $slot = sprintf(
                '%02d-%02d',
                $hour,
                $nextHour
            );

            if (!in_array(
                $slot,
                $hours,
                true
            )) {
                continue;
            }

            $notes = str_replace(
                ["\r\n", "\r"],
                "\n",
                $row->VSA_NOTES
            );

            $noteLines = explode(
                "\n",
                $notes
            );

            foreach ($noteLines as $noteLine) {
                $noteLine = trim(
                    $noteLine
                );


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

                $unitName = $parts[1];
                if (!in_array(
                    $unitName,
                    $units,
                    true
                )) {
                    continue;
                }
                $durationPivot[$slot][$unitName][$fuelTruck] += $durationMinutes;
                $frequencyPivot[$slot][$unitName][$fuelTruck]++;
            }
        }

        $durationByUnit = [];
        foreach ($units as $unit) {
            foreach ($fuelTrucks as $fuelTruck) {
                $total = 0;
                foreach ($hours as $hour) {
                    $total += $durationPivot[$hour][$unit][$fuelTruck] ?? 0;
                }

                $durationByUnit[$unit][$fuelTruck] = round($total, 2);
            }
        }
        $durationByHour = [];
        foreach ($hours as $hour) {
            foreach ($fuelTrucks as $fuelTruck) {
                $total = 0;
                foreach ($units as $unit) {
                    $total += $durationPivot[$hour][$unit][$fuelTruck] ?? 0;
                }

                $durationByHour[$hour][$fuelTruck] = round($total, 2);
            }
        }

        $averageDuration = [];
        foreach ($units as $unit) {
            foreach ($hours as $hour) {
                $totalDuration = 0;
                $totalFrequency = 0;

                foreach ($fuelTrucks as $fuelTruck) {
                    $totalDuration += $durationPivot[$hour][$unit][$fuelTruck] ?? 0;
                    $totalFrequency += $frequencyPivot[$hour][$unit][$fuelTruck] ?? 0;
                }

                if ($totalFrequency > 0) {
                    $average = $totalDuration / $totalFrequency;
                } else {
                    $average = 0;
                }

                $averageDuration[$unit][$hour] = round($average, 2);
            }
        }

        $averageDurationTotal = [];
        foreach ($units as $unit) {
            $totalDuration = 0;
            $totalFrequency = 0;
            foreach ($hours as $hour) {
                foreach ($fuelTrucks as $fuelTruck) {
                    $totalDuration += $durationPivot[$hour][$unit][$fuelTruck] ?? 0;
                    $totalFrequency += $frequencyPivot[$hour][$unit][$fuelTruck] ?? 0;
                }
            }
            $averageDurationTotal[$unit] =
                $totalFrequency > 0
                    ? round(
                        $totalDuration / $totalFrequency,
                        2
                    )
                    : 0;
        }

        $fuelTruckGrandTotal = [];
        foreach ($fuelTrucks as $fuelTruck) {
            $total = 0;
            foreach ($units as $unit) {
                $total += $durationByUnit[$unit][$fuelTruck] ?? 0;
            }

            $fuelTruckGrandTotal[$fuelTruck] = round($total, 2);
        }

        $grandTotal =
            round(
                array_sum($fuelTruckGrandTotal),
                2
            );

        $grandDuration = 0;
        $grandFrequency = 0;
        foreach ($hours as $hour) {
            foreach ($units as $unit) {
                foreach ($fuelTrucks as $fuelTruck) {
                    $grandDuration += $durationPivot[$hour][$unit][$fuelTruck] ?? 0;
                    $grandFrequency += $frequencyPivot[$hour][$unit][$fuelTruck] ?? 0;
                }
            }
        }

        $grandAverage = $grandFrequency > 0
            ? round($grandDuration / $grandFrequency, 2)
            : 0;

        $summaryLabel = $summaryMode === 'average'
            ? 'Rata-rata'
            : 'Total';

        $summaryGrandTotal = $summaryMode === 'average'
            ? $grandAverage
            : $grandTotal;
        return response()->json([
            'hours' => $hours,
            'units' => $units,
            'fuelTrucks' => $fuelTrucks,
            'fuelTruckStatus'        => $fuelTruckStatus,
            'durationPivot' => $durationPivot,
            'durationByUnit' => $durationByUnit,
            'durationByHour' => $durationByHour,
            'averageDuration' => $averageDuration,
            'averageDurationTotal' => $averageDurationTotal,
            'frequencyPivot' => $frequencyPivot,
            'fuelTruckGrandTotal' => $fuelTruckGrandTotal,
            'grandTotal' => $grandTotal,
            'summaryMode' => $summaryMode,
            'summaryLabel' => $summaryLabel,
            'summaryGrandTotal' => $summaryGrandTotal,
            'grandAverage' => $grandAverage,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shift' => $shift,
        ]);
    }
}
