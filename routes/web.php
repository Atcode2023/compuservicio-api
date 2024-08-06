<?php

use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\Services\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // phpinfo();
    return view('welcome');
});

Route::get('import/{name}', [ImportController::class, 'import']);

Route::get('/optimize', function () {
    \Artisan::call('optimize:clear');
    return 'success';
});

Route::get('/storage', function () {
    \Artisan::call('storage:link');
    return 'success';
});

Route::get('/backup-system', function () {
    try {
        \Artisan::call('backup:run --only-db --disable-notifications');
        return response()->json(['message' => 'success'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'error', 'errors' => $e->getMessage()], 400);
    }
});

Route::get('/depurar', function () {
    $services = \App\Models\Service::where('tech_id', 0)->orWhereIn('date_delivery', ['0000-00-01', '0000-01-01'])->update([
        'tech_id'       => null,
        'date_delivery' => null
    ]);
    dd($services);
});

Route::get('/qrGenerate', [TicketController::class, 'qrGenerate'])->name('qrGenerate');

// Route::get('test/printer', [\App\Http\Controllers\Api\Toners\TonerController::class, 'printReceipt']);
