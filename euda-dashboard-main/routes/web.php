<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommandController;
use App\Filament\Resources\ThingResource;
use App\Http\Controllers\AwsIotController;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//Route::get('/generate-pdf', [CommandController::class, 'generatePDF']);
Route::get('/generate_qrcode', [CommandController::class, 'qrcodeForm']);
Route::post('/generate_qrcode', [CommandController::class, 'generateQrCode']);


Route::post('/command', [CommandController::class, 'handleFormSubmission']);
Route::get('/reset', [CommandController::class, 'reset']);
Route::get('/things/{record}/download', [ThingResource::class, 'downloadAction'])
    ->name('filament.resources.things.download');

Route::post('/rule', [CommandController::class, 'createIotRule']);    

Route::post('/trigger-action', [CommandController::class, 'triggerAction']);

Route::get('/sendPlantId', [CommandController::class, 'sendPlantId']);
Route::get('/get-devices', [AwsIotController::class, 'getDevices']);


