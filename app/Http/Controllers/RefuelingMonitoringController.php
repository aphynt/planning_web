<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefuelingMonitoringController extends Controller
{
    //
    public function index()
    {
        return view('refuelingMonitoring.index');
    }

    public function api(Request $request)
    {
        $offset = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw   = $request->input('draw');

        $query = DB::connection('focus')
            ->table(DB::raw("
                (
                    SELECT
                        A.*,
                        IIF(COALESCE(EST_FUELLEVEL,0)=0,100,EST_FUELLEVEL) AS IDX
                    FROM RTV_PRD_FUEL_ESTLEVEL() A
                ) X
            "))
            ->leftJoin(DB::raw("
                (
                    SELECT
                        VHC_ID,
                        NET_IPADDRESS,
                        PSG_LOADERID,
                        dbo.STR_DURATIONFROMTIMERANGE(OPR_REPORTTIME, CURRENT_TIMESTAMP) AS LASTCONNECTED,
                        CAST(
                            CASE
                                WHEN DATEDIFF(SECOND, OPR_REPORTTIME, CURRENT_TIMESTAMP) <= 15 THEN 1
                                ELSE 0
                            END AS INTEGER
                        ) AS STATUS
                    FROM FLT_VEHICLE WITH (NOLOCK)
                    WHERE VHC_ACTIVE = 1
                ) Y
            "), 'X.VHC_ID', '=', 'Y.VHC_ID')
            ->select([
                'X.*',
                'Y.NET_IPADDRESS',
                'Y.LASTCONNECTED',
                'Y.PSG_LOADERID',
                'Y.STATUS'
            ])
            ->where('X.VHC_TYPEID', 2);

        // Search DataTables
        if ($request->filled('search.value')) {

            $search = '%' . $request->input('search.value') . '%';

            $query->where(function ($q) use ($search) {

                $q->where('X.VHC_ID', 'like', $search)
                ->orWhere('Y.NET_IPADDRESS', 'like', $search)
                ->orWhere('Y.PSG_LOADERID', 'like', $search);

            });
        }

        $recordsFiltered = (clone $query)->count();

        $data = $query
            ->orderBy('X.IDX')
            ->offset($offset)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
}
