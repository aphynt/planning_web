<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MineMonitoringController extends Controller
{
    //
    public function index()
    {
        return view('mineMonitoring.index');
    }

    public function api(Request $request)
    {
        $query = DB::connection('focus')
            ->table('FLT_VEHICLE as A')
            ->leftJoin('FLT_VSAGROUP as B', 'A.VSA_GROUPID', '=', 'B.VSA_GROUPID')
            ->leftJoin('FLT_VSASTATUS as C', 'A.VSA_STATUSID', '=', 'C.VSA_STATUSID')
            ->leftJoin('FLT_VSAACTIVITY as D', function ($join) {
                $join->on('A.VHC_TYPEID', '=', 'D.VHC_TYPEID')
                ->on('A.VSA_ACTIVITYID', '=', 'D.VSA_ACTIVITYID');
                })
            ->select([
                'A.VHC_ID',
                'A.VHC_TYPEID',
                'A.VHC_ACTIVE',
                'A.VSA_GROUPID',
                'A.VSA_STATUSID',
                'A.VSA_ACTIVITYID',
                'A.OPR_REPORTTIME',
                'A.OPR_NAME',
                'A.GPS_TIMESTAMP',
                'A.GPS_LON',
                'A.GPS_LAT',
                'A.GPS_ALT',
                'A.GPS_SPEED',
                'A.GPS_DIR',
                'A.LOC_NAME',
                'A.LOC_REGIONID',
                'A.ASG_LOADERID',
                'A.ASG_TO_LOC_NAME',
                'A.ASG_MODE',
                'A.NET_STATUSCON',
                'A.NET_STATUSCLOCK',
                'A.NET_TIMECHECK',
                'A.NET_IPADDRESS',
                'A.TRK_TIMESTAMP',
                'A.TRK_UPDATED',
                'A.KAL_UPDATED',
                'A.VHM_ENGSPEED',
                'A.VHM_LIVEWEIGHTTON',
                'A.MTR_HOURMETER',
                'A.MTR_FUELLEVEL',
                'A.ALR_ISMISROUTE',
                'A.ALR_ISOVERLOAD',
                'A.ALR_ISOVERSPEED',
                'B.VSA_GROUPDESC',
                DB::raw("
                    CAST(
                        IIF(
                            A.VSA_ACTIVITYID = 0,
                            B.VSA_GROUPDESC + ' - [' + A.VSA_STATUSID + '] ' + C.VSA_STATUSDESC,
                            B.VSA_GROUPDESC + ' - [' + A.VSA_STATUSID + '] ' + C.VSA_STATUSDESC + ' - ' + CAST(A.VSA_ACTIVITYID AS CHAR(1)) + ' - ' + D.VSA_ACTIVITYDESC
                        )
                    AS VARCHAR(100)) AS STATUSACTIVITYDESC
                "),

                DB::raw("
                    CAST(
                        CASE
                            WHEN DATEDIFF(SECOND, A.TRK_TIMESTAMP, CURRENT_TIMESTAMP) <= 15
                                THEN 4

                            WHEN DATEDIFF(SECOND, A.TRK_UPDATED, CURRENT_TIMESTAMP) <= 15
                                THEN 3

                            WHEN DATEDIFF(SECOND, A.KAL_UPDATED, CURRENT_TIMESTAMP) <= 180
                                THEN 2

                            WHEN DATEDIFF(SECOND, A.NET_TIMECHECK, CURRENT_TIMESTAMP) <= 500
                                AND A.NET_STATUSCON = 1
                                AND DATEDIFF(SECOND, A.TRK_UPDATED, CURRENT_TIMESTAMP) > 120
                                THEN 1

                            WHEN DATEDIFF(SECOND, A.TRK_UPDATED, CURRENT_TIMESTAMP) > 86400
                                THEN -1

                            ELSE 0
                        END
                    AS INT) AS ON_CODE
                "),

                DB::raw("
                    CAST(
                        CASE
                            WHEN DATEDIFF(SECOND, A.TRK_TIMESTAMP, CURRENT_TIMESTAMP) <= 15
                                THEN 'OK'

                            WHEN DATEDIFF(SECOND, A.TRK_UPDATED, CURRENT_TIMESTAMP) <= 15
                                THEN 'TIME'

                            WHEN DATEDIFF(SECOND, A.KAL_UPDATED, CURRENT_TIMESTAMP) <= 180
                                THEN 'GPS'

                            WHEN DATEDIFF(SECOND, A.NET_TIMECHECK, CURRENT_TIMESTAMP) <= 500
                                AND A.NET_STATUSCON = 1
                                AND DATEDIFF(SECOND, A.TRK_UPDATED, CURRENT_TIMESTAMP) > 120
                                THEN 'SOFT'

                            WHEN DATEDIFF(SECOND, A.TRK_UPDATED, CURRENT_TIMESTAMP) > 86400
                                THEN 'OFF'

                            ELSE 'NET'
                        END
                    AS VARCHAR(30)) AS ERR
                ")
            ])
            ->where('A.VHC_ACTIVE', 1)
            ->where('A.VHC_TYPEID', 5);

        $mapObjects = DB::connection('focus')
            ->table('MAP_OBJECT')
            ->select([
                'MAPOBJECTID',
                'LAYERNAME',
                'MAPOBJECTTYPE',
                'MAPOBJECTNAME',
                'GEOPOINTS',
                'VISIBLE'
            ])
            ->where('VISIBLE', 1)
            ->whereNotNull('GEOPOINTS')
            ->whereRaw("DATALENGTH(CAST(GEOPOINTS AS VARCHAR(MAX))) > 0")
            ->get();

        $type = $request->input('type');

        if (!empty($type) && $type !== 'all') {
            $query->where('A.VHC_TYPEID', $type);
        }

        $data = $query
            ->orderBy('A.VHC_ID')
            ->get();

        $units = [];

        foreach ($data as $row) {

            $status = trim($row->VSA_GROUPDESC ?? '');

            if ($status === '') {
                $status = 'Ready';
            }

            $statusLower = strtolower($status);

            if (
                str_contains($statusLower, 'production')
            ) {
                $category = 'production';

            } elseif (
                str_contains($statusLower, 'waiting') ||
                str_contains($statusLower, 'standby')
            ) {
                $category = 'waiting';

            } elseif (
                str_contains($statusLower, 'maintenance') ||
                str_contains($statusLower, 'breakdown') ||
                str_contains($statusLower, 'delay')
            ) {
                $category = 'maintenance';

            } else {
                $category = 'other';
            }

            $onCode = (int) ($row->ON_CODE ?? 0);
            $err = strtoupper(trim((string) ($row->ERR ?? 'NET')));

            if ($onCode === -1 || $err === 'OFF') {
                $category = 'offline';
            }

            $units[] = [
                'id' => $row->VHC_ID,
                'type' => $row->VHC_TYPEID,
                'status' => $status,
                'statusdesc' => $row->STATUSACTIVITYDESC,
                'category' => $category,

                'online' => $onCode !== -1,
                'on_code' => $onCode,
                'err' => $err,
                'ipaddress' => $row->NET_IPADDRESS,

                'report_time' => $row->OPR_REPORTTIME,
                'gps_time' => $row->GPS_TIMESTAMP,

                'lat' => $row->GPS_LAT,
                'lon' => $row->GPS_LON,
                'alt' => $row->GPS_ALT,
                'speed' => $row->GPS_SPEED,
                'direction' => $row->GPS_DIR,

                'location' => $row->LOC_NAME,
                'operator' => $row->OPR_NAME,
                'assignment' => $row->ASG_LOADERID,
                'destination' => $row->ASG_TO_LOC_NAME,
                'mode' => $row->ASG_MODE,

                'engine' => $row->VHM_ENGSPEED,
                'payload' => $row->VHM_LIVEWEIGHTTON,
                'hourmeter' => $row->MTR_HOURMETER,
                'fuel' => $row->MTR_FUELLEVEL,

                'misroute' => $row->ALR_ISMISROUTE,
                'overload' => $row->ALR_ISOVERLOAD,
                'overspeed' => $row->ALR_ISOVERSPEED
            ];
        }

        $total = count($units);

        $production = collect($units)
            ->where('category', 'production')
            ->count();

        $waiting = collect($units)
            ->where('category', 'waiting')
            ->count();

        $maintenance = collect($units)
            ->where('category', 'maintenance')
            ->count();

        $offline = collect($units)
            ->where('category', 'offline')
            ->count();

        $online = $total - $offline;

        $activities = collect($units)
            // ->sortByDesc(function ($unit) {
            //     return $unit['report_time'];
            // })
            ->take(10)
            ->values();

        return response()->json([
            'success' => true,
            'updated_at' => now()->format('Y-m-d H:i:s'),

            'summary' => [
                'total' => $total,
                'production' => $production,
                'waiting' => $waiting,
                'maintenance' => $maintenance,
                'offline' => $offline,
                'online' => $online
            ],

            'units' => $units,

            'activities' => $activities,

            'map_objects' => $mapObjects
        ]);
    }
}
