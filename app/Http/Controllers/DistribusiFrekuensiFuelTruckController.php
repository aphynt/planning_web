<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistribusiFrekuensiFuelTruckController extends Controller
{
    //
    public function index()
    {
        return view('distribusiFrekuensi.fuelTruck.index');
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
            ->select([
                'A.VHC_ID',
                'A.OPR_REPORTTIME',
                'A.OPR_SHIFTDATE',
                'A.OPR_SHIFTNO',
                'A.VSA_NOTES',
            ])
            ->where('A.VHC_TYPEID', 5)
            ->where('A.OPR_REPORTTIME', '>', '1970-01-01');


        $query->whereBetween('A.OPR_SHIFTDATE', [
            $startDate,
            $endDate
        ]);

        if (!empty($shift) && $shift != 'Semua') {
            $query->where('A.OPR_SHIFTNO', $shift);
        }
        $data = $query
            ->orderBy('A.OPR_REPORTTIME')
            ->get();

        $vehicleStatus = DB::connection('focus')
            ->table('FLT_VEHICLE as A')
            ->leftJoin(
                'FLT_VSAGROUP as B',
                'A.VSA_GROUPID',
                '=',
                'B.VSA_GROUPID'
            )
            ->where('A.VHC_TYPEID', 5)
            ->select([
                'A.VHC_ID',
                DB::raw("COALESCE(B.VSA_GROUPDESC, 'Ready') AS STATUS")
            ])
            ->get()
            ->keyBy('VHC_ID');

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
            'Fuel Station',
            'Loader',
            'Hauler',
            'Grader',
            'Dozer',
            'Fuel Truck',
            'Water Truck',
            'Excavator Support',
            'Pump',
            'Genset',
            'Unit Support',
            'Drilling',
            'Service Truck',
            'Lighting Tower',
            'Air Compressor',
            'Welding Machine',
            'Etc',
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

        $pivot = [];
        foreach ($hours as $hour) {
            foreach ($units as $unit) {
                foreach ($fuelTrucks as $fuelTruck) {
                    $pivot[$hour][$unit][$fuelTruck] = 0;
                }
            }
        }

        foreach ($data as $row) {
            if (empty($row->VSA_NOTES)) {
                continue;
            }

            $notes = str_replace(
                ["\r\n", "\r"],
                "\n",
                $row->VSA_NOTES
            );
            $noteLines = explode("\n", $notes);

            $fuelTruck = trim($row->VHC_ID);

            if ($fuelTruck === '') {
                continue;
            }

            $reportTime = Carbon::parse($row->OPR_REPORTTIME);
            $hour = (int) $reportTime->format('H');
            $nextHour = ($hour + 1) % 24;
            $slot = sprintf(
                '%02d-%02d',
                $hour,
                $nextHour
            );

            if (!in_array($slot, $hours, true)) {
                continue;
            }

            foreach ($noteLines as $noteLine) {
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
                $unitName = $parts[1];
                if (!in_array($unitName, $units, true)) {
                    continue;
                }
                $pivot[$slot][$unitName][$fuelTruck]++;
            }
        }

        $totalsByUnit = [];
        foreach ($units as $unit) {
            foreach ($fuelTrucks as $fuelTruck) {
                $total = 0;
                foreach ($hours as $hour) {
                    $total += $pivot[$hour][$unit][$fuelTruck] ?? 0;
                }
                $totalsByUnit[$unit][$fuelTruck] = $total;
            }
        }

        $totalsByHour = [];
        foreach ($hours as $hour) {
            foreach ($fuelTrucks as $fuelTruck) {
                $total = 0;
                foreach ($units as $unit) {
                    $total += $pivot[$hour][$unit][$fuelTruck] ?? 0;
                }
                $totalsByHour[$hour][$fuelTruck] = $total;
            }
        }

        $unitGrandTotal = [];

        foreach ($units as $unit) {
            $total = 0;
            foreach ($fuelTrucks as $fuelTruck) {
                $total += $totalsByUnit[$unit][$fuelTruck] ?? 0;
            }
            $unitGrandTotal[$unit] = $total;
        }

        $fuelTruckGrandTotal = [];
        foreach ($fuelTrucks as $fuelTruck) {
            $total = 0;
            foreach ($units as $unit) {
                $total += $totalsByUnit[$unit][$fuelTruck] ?? 0;
            }
            $fuelTruckGrandTotal[$fuelTruck] = $total;
        }

        $grandTotal = array_sum($fuelTruckGrandTotal);

        $totalHours = count($hours);
        $totalUnits = count($units);
        $totalFuelTrucks = count($fuelTrucks);

        $averagePerHour = $totalHours > 0 ? round($grandTotal / $totalHours, 2) : 0;
        $averagePerFuelTruck = $totalFuelTrucks > 0 ? round($grandTotal / $totalFuelTrucks, 2) : 0;
        $averagePerUnit = $totalUnits > 0 ? round($grandTotal / $totalUnits, 2) : 0;

        return response()->json([
            'hours'                  => $hours,
            'units'                  => $units,
            'fuelTrucks'             => $fuelTrucks,
            'fuelTruckStatus'        => $fuelTruckStatus,
            'pivot'                  => $pivot,
            'totalsByUnit'           => $totalsByUnit,
            'totalsByHour'           => $totalsByHour,
            'unitGrandTotal'         => $unitGrandTotal,
            'fuelTruckGrandTotal'    => $fuelTruckGrandTotal,
            'grandTotal'             => $grandTotal,
            'averagePerHour'         => $averagePerHour,
            'averagePerFuelTruck'    => $averagePerFuelTruck,
            'averagePerUnit'         => $averagePerUnit,
            'startDate'              => $startDate,
            'endDate'                => $endDate,
            'shift'                  => $shift,
        ]);
    }

    //Durasi
    public function durasi()
    {
        return view('distribusiFrekuensi.fuelTruck.durasi');
    }

    public function durasi_api(Request $request)
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
            ->whereRaw('A.OPR_ENDTIME >= A.OPR_REPORTTIME')
            ->whereBetween('A.OPR_SHIFTDATE', [
                $startDate,
                $endDate
            ]);

        if (!empty($shift) && $shift !== 'Semua') {
            $query->where('A.OPR_SHIFTNO', $shift);
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

        $unitTypes = [
            'Fuel Station',
            'Loader',
            'Hauler',
            'Grader',
            'Dozer',
            'Fuel Truck',
            'Water Truck',
            'Excavator Support',
            'Pump',
            'Genset',
            'Unit Support',
            'Drilling',
            'Service Truck',
            'Lighting Tower',
            'Air Compressor',
            'Welding Machine',
            'Etc',
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
            foreach ($unitTypes as $unitType) {
                foreach ($fuelTrucks as $fuelTruck) {
                    $durationPivot[$hour][$unitType][$fuelTruck] = 0;
                }
            }
        }

        $frequencyPivot = [];
        foreach ($hours as $hour) {
            foreach ($unitTypes as $unitType) {
                foreach ($fuelTrucks as $fuelTruck) {
                    $frequencyPivot[$hour][$unitType][$fuelTruck] = 0;
                }
            }
        }

        foreach ($data as $row) {
            if (empty($row->VSA_NOTES)) {
                continue;
            }
            $fuelTruck = trim($row->VHC_ID);
            if ($fuelTruck === '') {
                continue;
            }

            if (!$fuelTrucks->contains($fuelTruck)) {
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

            if (!in_array($slot, $hours, true)) {
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

                $unitName = $parts[1];
                if (!in_array(
                    $unitName,
                    $unitTypes,
                    true
                )) {
                    continue;
                }

                $durationPivot[$slot][$unitName][$fuelTruck] += $durationMinutes;
                $frequencyPivot[$slot][$unitName][$fuelTruck]++;
            }
        }

        $durationByUnit = [];
        foreach ($unitTypes as $unitType) {
            foreach ($fuelTrucks as $fuelTruck) {
                $total = 0;
                foreach ($hours as $hour) {
                    $total +=
                        $durationPivot[$hour][$unitType][$fuelTruck]
                        ?? 0;
                }

                $durationByUnit[$unitType][$fuelTruck] =
                    round($total, 2);
            }
        }

        $durationByHour = [];
        foreach ($hours as $hour) {
            foreach ($fuelTrucks as $fuelTruck) {
                $total = 0;
                foreach ($unitTypes as $unitType) {
                    $total += $durationPivot[$hour][$unitType][$fuelTruck] ?? 0;
                }

                $durationByHour[$hour][$fuelTruck] =
                    round($total, 2);
            }
        }

        $averageDuration = [];
        foreach ($unitTypes as $unitType) {
            foreach ($hours as $hour) {
                $totalDuration = 0;
                $totalFrequency = 0;
                foreach ($fuelTrucks as $fuelTruck) {
                    $totalDuration += $durationPivot[$hour][$unitType][$fuelTruck] ?? 0;
                    $totalFrequency += $frequencyPivot[$hour][$unitType][$fuelTruck] ?? 0;
                }
                $average =
                    $totalFrequency > 0
                        ? $totalDuration / $totalFrequency
                        : 0;

                $averageDuration[$unitType][$hour] =
                    round($average, 2);
            }
        }

        $averageDurationTotal = [];
        foreach ($unitTypes as $unitType) {
            $totalDuration = 0;
            $totalFrequency = 0;
            foreach ($hours as $hour) {
                foreach ($fuelTrucks as $fuelTruck) {
                    $totalDuration += $durationPivot[$hour][$unitType][$fuelTruck] ?? 0;
                    $totalFrequency += $frequencyPivot[$hour][$unitType][$fuelTruck] ?? 0;
                }
            }

            $averageDurationTotal[$unitType] =
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
            foreach ($unitTypes as $unitType) {
                $total += $durationByUnit[$unitType][$fuelTruck] ?? 0;
            }

            $fuelTruckGrandTotal[$fuelTruck] =
                round($total, 2);
        }

        $grandTotal =
            round(
                array_sum($fuelTruckGrandTotal),
                2
            );

        return response()->json([
            'hours'                => $hours,
            'units'                => $unitTypes,
            'fuelTrucks'           => $fuelTrucks,
            'fuelTruckStatus'      => $fuelTruckStatus,
            'durationPivot'        => $durationPivot,
            'durationByUnit'       => $durationByUnit,
            'durationByHour'       => $durationByHour,
            'averageDuration'      => $averageDuration,
            'averageDurationTotal' => $averageDurationTotal,
            'frequencyPivot'       => $frequencyPivot,
            'fuelTruckGrandTotal'  => $fuelTruckGrandTotal,
            'grandTotal'           => $grandTotal,
            'startDate'            => $startDate,
            'endDate'              => $endDate,
            'shift'                => $shift,
        ]);
    }
}
