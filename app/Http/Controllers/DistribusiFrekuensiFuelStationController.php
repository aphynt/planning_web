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
            ->select([
                'A.VHC_ID',
                'A.OPR_REPORTTIME',
                'A.OPR_SHIFTDATE',
                'A.OPR_SHIFTNO',
                'A.LOC_NAME',
                'A.VSA_NOTES',
            ])
            ->where('A.VHC_TYPEID', 5)
            ->where('A.OPR_REPORTTIME', '>', '1970-01-01')
            ->whereNotNull('A.VSA_NOTES')
            ->where('A.LOC_NAME', 'LIKE', '%FUE%')
            ->where('A.VSA_NOTES', '<>', '')
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
            ->pluck('LOC_NAME')
            ->filter()
            ->map(function ($location) {
                return trim($location);
            })
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
            $fuelStation = trim(
                $row->LOC_NAME
            );
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

                $pivot[$slot][$unitName][$fuelStation]++;
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
        $shift = $request->input('shift');

        if (empty($tanggalInput)) {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = $startDate;
        } elseif (str_contains($tanggalInput, 'to')) {
            [$startDate, $endDate] = array_map('trim', explode('to', $tanggalInput));
        } else {
            $startDate = trim($tanggalInput);
            $endDate = $startDate;
        }

        $query = DB::connection('focus')
            ->table('VSA_STATUSACTIVITYEX as A')
            ->select([
                'A.VHC_ID',
                'A.OPR_REPORTTIME',
                'A.OPR_ENDTIME',
                'A.OPR_SHIFTDATE',
                'A.OPR_SHIFTNO',
                'A.LOC_NAME',
                'A.VSA_NOTES',
            ])
            ->where('A.VHC_TYPEID', 5)
            ->where('A.OPR_REPORTTIME', '>', '1970-01-01')
            ->where('A.OPR_ENDTIME', '>', '1970-01-01')
            ->whereRaw('A.OPR_ENDTIME >= A.OPR_REPORTTIME')
            ->whereNotNull('A.LOC_NAME')
            ->where('A.LOC_NAME', '<>', '')
            ->whereNotNull('A.VSA_NOTES')
            ->where('A.LOC_NAME', 'LIKE', '%FUE%')
            ->where('A.VSA_NOTES', '<>', '')
            ->where(function ($q) {
                $q->where('A.VSA_NOTES', 'LIKE', '%Hauler%')
                ->orWhere('A.VSA_NOTES', 'LIKE', '%Dozer%')
                ->orWhere('A.VSA_NOTES', 'LIKE', '%Grader%');
            })
            ->whereBetween('A.OPR_SHIFTDATE', [$startDate, $endDate]);

        if (!empty($shift) && $shift !== 'Semua') {
            $query->where('A.OPR_SHIFTNO', $shift);
        }

        $data = $query->orderBy('A.OPR_REPORTTIME')->get();

        $units = ['Hauler', 'Grader', 'Dozer'];

        $fuelStations = $data->pluck('LOC_NAME')
            ->filter()
            ->map(fn ($location) => trim($location))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $hours = [];

        if ($shift === '7') {
            for ($i = 19; $i <= 23; $i++) {
                $hours[] = sprintf('%02d-%02d', $i, ($i + 1) % 24);
            }
            for ($i = 0; $i <= 6; $i++) {
                $hours[] = sprintf('%02d-%02d', $i, $i + 1);
            }
        } elseif ($shift === 'Semua') {
            for ($i = 0; $i <= 23; $i++) {
                $hours[] = sprintf('%02d-%02d', $i, ($i + 1) % 24);
            }
        } else {
            for ($i = 7; $i <= 18; $i++) {
                $hours[] = sprintf('%02d-%02d', $i, $i + 1);
            }
        }

        $durationPivot = [];
        $frequencyPivot = [];

        foreach ($hours as $hour) {
            foreach ($units as $unit) {
                foreach ($fuelStations as $station) {
                    $durationPivot[$hour][$unit][$station] = 0;
                    $frequencyPivot[$hour][$unit][$station] = 0;
                }
            }
        }

        foreach ($data as $row) {
            if (empty($row->VSA_NOTES)) continue;

            $station = trim($row->LOC_NAME);
            if ($station === '') continue;

            try {
                $reportTime = Carbon::parse($row->OPR_REPORTTIME);
                $endTime = Carbon::parse($row->OPR_ENDTIME);
            } catch (\Throwable $e) {
                continue;
            }

            if ($endTime->lte($reportTime)) continue;

            $durationMinutes = $reportTime->diffInSeconds($endTime) / 60;
            $hour = (int) $reportTime->format('H');
            $slot = sprintf('%02d-%02d', $hour, ($hour + 1) % 24);

            if (!in_array($slot, $hours, true)) continue;

            $notes = str_replace(["\r\n", "\r"], "\n", $row->VSA_NOTES);
            $noteLines = explode("\n", $notes);

            foreach ($noteLines as $noteLine) {
                $noteLine = trim($noteLine);
                if ($noteLine === '') continue;

                $parts = array_map('trim', explode('|', $noteLine));
                if (count($parts) < 3) continue;

                $unitName = $parts[1];
                if (!in_array($unitName, $units, true)) continue;

                $durationPivot[$slot][$unitName][$station] += $durationMinutes;
                $frequencyPivot[$slot][$unitName][$station]++;
            }
        }

        $averageDuration = [];
        foreach ($units as $unit) {
            foreach ($hours as $hour) {
                foreach ($fuelStations as $station) {
                    $duration = $durationPivot[$hour][$unit][$station] ?? 0;
                    $frequency = $frequencyPivot[$hour][$unit][$station] ?? 0;
                    $averageDuration[$unit][$hour][$station] =
                        $frequency > 0 ? round($duration / $frequency, 1) : 0;
                }
            }
        }

        $averageDurationTotal = [];
        foreach ($units as $unit) {
            $totalDuration = 0;
            $totalFrequency = 0;
            foreach ($hours as $hour) {
                foreach ($fuelStations as $station) {
                    $totalDuration += $durationPivot[$hour][$unit][$station] ?? 0;
                    $totalFrequency += $frequencyPivot[$hour][$unit][$station] ?? 0;
                }
            }
            $averageDurationTotal[$unit] =
                $totalFrequency > 0 ? round($totalDuration / $totalFrequency, 1) : 0;
        }

        return response()->json([
            'hours' => $hours,
            'units' => $units,
            'fuelStations' => $fuelStations,
            'durationPivot' => $durationPivot,
            'frequencyPivot' => $frequencyPivot,
            'averageDuration' => $averageDuration,
            'averageDurationTotal' => $averageDurationTotal,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shift' => $shift,
        ]);
    }
}
