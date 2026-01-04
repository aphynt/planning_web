<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelmanController;
use App\Http\Controllers\KKHController;
use App\Http\Controllers\KLKHFuelStationController;
use App\Http\Controllers\MappingSOPController;
use App\Http\Controllers\MappingVerifierController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VerifiedController;
use App\Http\Controllers\SOPPlanningController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/post', [AuthController::class, 'loginPost'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/verified/{encodedNik}', [VerifiedController::class, 'index'])->name('verified.index');

Route::any('/fuel/{path?}', function (Request $request, $path = '') {
    $targetUrl = 'http://10.10.2.5:9071/' . $path;

    $response = Http::withHeaders(
        collect($request->headers->all())
            ->map(fn ($v) => $v[0])
            ->toArray()
    )->send(
        $request->method(),
        $targetUrl,
        [
            'query' => $request->query(),
            'body'  => $request->getContent(),
        ]
    );

    return response(
        $response->body(),
        $response->status()
    )->withHeaders($response->headers());
})->where('path', '.*');

//Middleware
Route::group(['middleware' => ['auth']], function(){

    Route::get('/dashboards', [DashboardController::class, 'index'])->name('dashboard.index');

    //KLKH Fuel Station
    Route::get('/klkh/fuelStation', [KLKHFuelStationController::class, 'index'])->name('klkh.fuelStation.index');
    Route::get('/klkh/fuelStation/insert', [KLKHFuelStationController::class, 'insert'])->name('klkh.fuelStation.insert');
    Route::post('/klkh/fuelStation/post', [KLKHFuelStationController::class, 'post'])->name('klkh.fuelStation.post');
    Route::post('/klkh/fuelStation/verified/all/{UUID}', [KLKHFuelStationController::class, 'verifiedAll'])->name('klkh.fuelStation.verifiedAll');
    Route::post('/klkh/fuelStation/verified/pengawas/{UUID}', [KLKHFuelStationController::class, 'verifiedPengawas'])->name('klkh.fuelStation.verifiedPengawas');
    Route::post('/klkh/fuelStation/verified/diketahui/{UUID}', [KLKHFuelStationController::class, 'verifiedDiketahui'])->name('klkh.fuelStation.verifiedDiketahui');
    Route::get('/klkh/fuelStation/edit/{UUID}', [KLKHFuelStationController::class, 'edit'])->name('klkh.fuelStation.edit');
    Route::post('/klkh/fuelStation/update/{UUID}', [KLKHFuelStationController::class, 'update'])->name('klkh.fuelStation.update');
    Route::get('/klkh/fuelStation/preview/{UUID}', [KLKHFuelStationController::class, 'preview'])->name('klkh.fuelStation.preview');
    Route::get('/klkh/fuelStation/cetak/{UUID}', [KLKHFuelStationController::class, 'cetak'])->name('klkh.fuelStation.cetak');
    Route::get('/klkh/fuelStation/download/{UUID}', [KLKHFuelStationController::class, 'download'])->name('klkh.fuelStation.download');
    Route::get('/klkh/fuelStation/delete/{UUID}', [KLKHFuelStationController::class, 'delete'])->name('klkh.fuelStation.delete');
    Route::get('/klkh/fuelStation/report', [KLKHFuelStationController::class, 'report'])->name('klkh.fuelStation.report');


    Route::get('/kkh', [KKHController::class, 'index'])->name('kkh.index');
    Route::get('/kkh/api', [KKHController::class, 'all_api'])->name('kkh.all_api');
    Route::get('/kkh/downloadPDF', [KKHController::class, 'downloadPDF'])->name('kkh.downloadPDF');
    Route::post('/kkh/verifikasi', [KKHController::class, 'verifikasi'])->name('kkh.verifikasi');

    Route::get('/fuelman/dashboard', [FuelmanController::class, 'dashboard'])->name('fuelman.dashboard');

    //SOP
    Route::get('/files/sop/{name}', function ($name) {
        $path = public_path('sop/' . $name);
        abort_unless(File::exists($path), 404);
        return Response::file($path, ['Content-Type' => 'application/pdf']);
    })->where('name', '.*')->name('sop.show');

    Route::prefix('/sop/planning')->name('sop.')->group(function () {
        Route::get('/ringkasanSOP', [SOPPlanningController::class, 'ringkasanSOP'])->name('ringkasanSOP');
        Route::get('/prosesPlanning', [SOPPlanningController::class, 'prosesPlanning'])->name('prosesPlanning');
        Route::get('/surveyKepuasanPelangganEksternal', [SOPPlanningController::class, 'surveyKepuasanPelangganEksternal'])->name('surveyKepuasanPelangganEksternal');
        Route::get('/keluhanPelanggan', [SOPPlanningController::class, 'keluhanPelanggan'])->name('keluhanPelanggan');
        Route::get('/laporanOwningOperationCost', [SOPPlanningController::class, 'laporanOwningOperationCost'])->name('laporanOwningOperationCost');
        Route::get('/pengelolaanSuratMasukDanKeluar', [SOPPlanningController::class, 'pengelolaanSuratMasukDanKeluar'])->name('pengelolaanSuratMasukDanKeluar');
        Route::get('/pencatatanSystemFuelManagement', [SOPPlanningController::class, 'pencatatanSystemFuelManagement'])->name('pencatatanSystemFuelManagement');
        Route::get('/laporanProduktivity', [SOPPlanningController::class, 'laporanProduktivity'])->name('laporanProduktivity');
        Route::get('/laporanPencatatanHoursMeter', [SOPPlanningController::class, 'laporanPencatatanHoursMeter'])->name('laporanPencatatanHoursMeter');
        Route::get('/penetapanBaselineEnergi', [SOPPlanningController::class, 'penetapanBaselineEnergi'])->name('penetapanBaselineEnergi');
        Route::get('/pengelolaanFuel', [SOPPlanningController::class, 'pengelolaanFuel'])->name('pengelolaanFuel');
        Route::get('/pengoperasianUnitFuelTruck', [SOPPlanningController::class, 'pengoperasianUnitFuelTruck'])->name('pengoperasianUnitFuelTruck');
        Route::get('/pembuatanLaporanManagementProfitLo', [SOPPlanningController::class, 'pembuatanLaporanManagementProfitLo'])->name('pembuatanLaporanManagementProfitLo');
        Route::get('/pembuatanLaporanBusinessPlan', [SOPPlanningController::class, 'pembuatanLaporanBusinessPlan'])->name('pembuatanLaporanBusinessPlan');
    });

    //Users
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::post('/users/changeRole/{id}', [UsersController::class, 'changeRole'])->name('users.changeRole');
    Route::get('/users/statusEnabled/{id}', [UsersController::class, 'statusEnabled'])->name('users.statusEnabled');

    Route::get('/mappingVerifier', [MappingVerifierController::class, 'index'])->name('mappingVerifier.index');
    Route::post('/mappingVerifier/insert', [MappingVerifierController::class, 'insert'])->name('mappingVerifier.insert');
    Route::post('/mappingVerifier/update/{id}', [MappingVerifierController::class, 'update'])->name('mappingVerifier.update');
    Route::get('/mappingVerifier/statusEnabled/{id}', [MappingVerifierController::class, 'statusEnabled'])->name('mappingVerifier.statusEnabled');

    Route::get('/mappingSOP', [MappingSOPController::class, 'index'])->name('mappingSOP.index');
    Route::post('/mappingSOP/insert', [MappingSOPController::class, 'insert'])->name('mappingSOP.insert');
    Route::get('/mappingSOP/statusEnabled/{id}', [MappingSOPController::class, 'statusEnabled'])->name('mappingSOP.statusEnabled');

});
