<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusActivityController extends Controller
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
        return view('statusActivity.index', compact('vehicle'));
    }

    public function api(Request $request)
    {
        $offset = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw = $request->input('draw');
        $query = DB::connection('focus_reporting')
            ->table('dbo.VW_VSA_STATUSACTIVITYEX as A')
            ->leftJoin('FOCUS.dbo.FLT_VSAGROUP as B', 'A.VSA_GROUPID', '=', 'B.VSA_GROUPID')
            ->leftJoin('FOCUS.dbo.FLT_VSASTATUS as C', 'A.VSA_STATUSID', '=', 'C.VSA_STATUSID')
            ->leftJoin('FOCUS.dbo.FLT_VSAACTIVITY as D', function ($join) {
                $join->on('A.VHC_TYPEID', '=', 'D.VHC_TYPEID')
                ->on('A.VSA_ACTIVITYID', '=', 'D.VSA_ACTIVITYID');
                })
            ->leftJoin('FOCUS.dbo.FLT_SHIFT as E', 'A.OPR_SHIFTNO', '=', 'E.SHIFTNO')
            ->select([
                'A.ID',
                'A.OPR_REPORTTIME',
                'A.OPR_ENDTIME',
                'A.OPR_SHIFTDATE',
                'A.OPR_SHIFTNO',
                'E.SHIFTDESC',

                DB::raw('CAST(COALESCE(A.ENG_TRAVEL,0)/60.0 AS FLOAT) AS ENG_TRAVEL'),
                DB::raw('CAST(COALESCE(A.ENG_STOPPED,0)/60.0 AS FLOAT) AS ENG_STOPPED'),
                DB::raw('CAST(COALESCE(A.ENG_OFF,0)/60.0 AS FLOAT) AS ENG_OFF'),

                DB::raw('DATEDIFF_BIG(SECOND,A.OPR_REPORTTIME,A.OPR_ENDTIME)/60.0 AS DURATION'),

                'A.VHC_ID',
                'A.VSA_GROUPID',
                'A.VSA_STATUSID',
                'A.VSA_ACTIVITYID',

                DB::raw("
                    CAST(
                        IIF(
                            A.VSA_ACTIVITYID = 0,
                            B.VSA_GROUPDESC + ' - [' + A.VSA_STATUSID + '] ' + C.VSA_STATUSDESC,
                            B.VSA_GROUPDESC + ' - [' + A.VSA_STATUSID + '] ' + C.VSA_STATUSDESC + ' - ' + CAST(A.VSA_ACTIVITYID AS CHAR(1)) + ' - ' + D.VSA_ACTIVITYDESC
                        )
                    AS VARCHAR(100)) AS STATUSACTIVITYDESC
                ")
            ])->where('A.VHC_TYPEID', 5);

            $tanggal = $request->input('tanggalStatus');
            $shift   = $request->input('shift');
            $vhc_id  = $request->input('vhc_id');

            if (empty($tanggal)) {
                $tanggal = Carbon::today()->format('Y-m-d');
            }

            $query->whereDate('A.OPR_SHIFTDATE', $tanggal);

            if (!empty($shift) && $shift != 'Semua') {
                $query->where('A.OPR_SHIFTNO', $shift);
            }

            if (!empty($vhc_id) && $vhc_id != 'Semua') {
                $query->where('A.VHC_ID', $vhc_id);
            }

        $query->where('A.OPR_REPORTTIME', '>', '1970-01-01')
            ->where('A.OPR_ENDTIME', '>', '1970-01-01')
            ->whereRaw('A.OPR_ENDTIME >= A.OPR_REPORTTIME');


        if ($request->filled('search.value')) {

            $search = '%' . $request->input('search.value') . '%';

            $query->where(function ($q) use ($search) {

                $q->orWhere('A.VHC_ID', 'like', $search)
                ->orWhere('B.VSA_GROUPDESC', 'like', $search)
                ->orWhere('C.VSA_STATUSDESC', 'like', $search)
                ->orWhere('D.VSA_ACTIVITYDESC', 'like', $search);

            });

        }

        $filtered = (clone $query)->count();

        $data = $query
            ->orderBy('A.OPR_REPORTTIME')
            ->offset($offset)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $filtered,
            'recordsFiltered' => $filtered,
            'data' => $data
        ]);
    }
}
