<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Commons\BackupController;
use App\Http\Controllers\Api\Reports\CashDeskClosingController;
use App\Http\Controllers\Api\Reports\ReportController;
use App\Http\Controllers\Api\Services\CedulaCNEController;
use App\Http\Controllers\Api\Services\DeliveryController;
use App\Http\Controllers\Api\Services\ServiceController;
use App\Http\Controllers\Api\Services\TicketController;
use App\Http\Controllers\Api\Techs\RepairedController;
use App\Http\Controllers\Api\Techs\RepairingController;
use App\Http\Controllers\Api\Toners\ReportTonerController;
use App\Http\Controllers\Api\Toners\TonerController;
use App\Http\Controllers\Api\Users\CustomerController;
use App\Http\Controllers\Api\Users\TechController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::post('/users/search', [UserController::class, 'search']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

Route::post('consultarCedula', [CedulaCNEController::class, 'consultarCedula']);

Route::get('/reports/pdf', [CashDeskClosingController::class, 'pdf'])->name('reports.pdf');
Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
Route::get('/services/{id}/pdf', [ServiceController::class, 'pdf'])->name('services.pdf');
Route::get('/toners/pdf', [ReportTonerController::class, 'pdf'])->name('reorts.toners.pdf');
Route::get('/toners/{id}/pdf', [TonerController::class, 'pdf'])->name('toners.pdf');
Route::get('/toners/{id}/factura', [ReportTonerController::class, 'factura'])->name('toners.factura');
Route::get('/services/{id}/factura', [ServiceController::class, 'factura'])->name('services.factura');
Route::post('/tickectInfo', [TicketController::class, 'ticketInfo'])->name('ticketInfo');
Route::get('/report/{id}/factura', [ReportController::class, 'factura'])->name('reports.factura');

Route::group(['middleware' => ['auth:sanctum']], function () {
    // Auth User
    Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    // Customers
    Route::post('/customers/check', [CustomerController::class, 'check'])->name('customers.check');
    Route::post('/customers/autocomplete', [CustomerController::class, 'autocomplete'])->name('customers.autocomplete');
    // Repairing
    Route::get('/tech/services', [RepairingController::class, 'index'])->name('services.asign.index');
    Route::post('/tech/services/asign', [RepairingController::class, 'asign'])->name('services.asign.tech');
    Route::get('/tech/services/reports/autocomplete', [RepairingController::class, 'reportsAutocomplete'])->name('services.reports-autocomplete.tech');
    Route::get('/tech/services/pieces/autocomplete', [RepairingController::class, 'piecesAutocomplete'])->name('services.pieces-autocomplete.tech');
    Route::post('/tech/services/report', [RepairingController::class, 'store'])->name('tech.services.report');
    Route::put('/tech/services/report', [RepairingController::class, 'update'])->name('tech.services.update');
    Route::get('/tech/repairing/count', [RepairingController::class, 'count'])->name('repairing.count');
    Route::post('/services/repairing/reject', [RepairingController::class, 'reject'])->name('services.repairing.reject');
    //Services
    Route::get('/services-autocomplete', [ServiceController::class, 'autocomplete'])->name('services.autocomplete');
    Route::get('/services/count', [ServiceController::class, 'count'])->name('services.count');
    Route::get('/services/repaired', [RepairedController::class, 'index'])->name('services.repaired.index');
    Route::post('/services/repaired/reject', [RepairedController::class, 'reject'])->name('services.repaired.reject');
    Route::post('/services/delivery', [RepairedController::class, 'delivery'])->name('services.repaired.delivery');
    Route::get('/services/delivery', [DeliveryController::class, 'index'])->name('services.delivery.index');
    Route::post('/services/delivery/reject', [DeliveryController::class, 'reject'])->name('services.delivery.reject');
    //Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/cash-desk-closing', [CashDeskClosingController::class, 'index'])->name('reports.cash-closing.index');
    // Commons
    Route::post('/backup', [BackupController::class, 'backup'])->name('commons.backup');
    // Toners
    Route::get('/toners/pieces/autocomplete', [TonerController::class, 'piecesAutocomplete'])->name('toners.pieces-autocomplete');
    Route::get('/toners/services/autocomplete', [TonerController::class, 'servicesAutocomplete'])->name('toners.services-autocomplete');
    Route::post('toners/reports/cash-desk-closing', [ReportTonerController::class, 'index'])->name('toners.reports.cash-closing.index');
    Route::put('/toners/{toner}/delivery', [TonerController::class, 'delivery'])->name('toners.delivery');
    // Resources
    Route::apiResource('techs', TechController::class)->parameters(['techs' => 'user']);
    Route::apiResources([
        'customers' => CustomerController::class,
        'services'  => ServiceController::class,
        'toners'    => TonerController::class,
    ]);
});
Route::post('/sync/bidirectional', [SyncController::class, 'bidirectionalSync']);
Route::post('/sync/table', [SyncController::class, 'syncSpecificTable']);
