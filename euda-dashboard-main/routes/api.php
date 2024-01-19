<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\AwsIotController;
use App\Http\Controllers\API\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
$globalResponse = null;

//Mobile App API routes Public routes
Route::post('/login', [AuthController::class, 'login']);

// Route::get('/generate_qrcode', [AuthController::class, 'qrcode']);
// Route::post('/generate_qrcode', [AuthController::class, 'generateQrCode']);
//Route::get('/plants',[AwsIotController::class, 'plants']);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::get('/things',[AwsIotController::class, 'things']);
        Route::get('/plants', [AwsIotController::class, 'plants']);
        Route::get('/technicians',[AwsIotController::class, 'technicians']);

        Route::get('/plants/search', [AwsIotController::class, 'search']);
        Route::get('/get-plant-info/{plantId}', [AwsIotController::class, 'getPlantInfo']);
        Route::post('/create-iot-thing', [AwsIotController::class, 'createThing']);
        Route::post('/update-status', [AwsIotController::class, 'updateStatus']);
    
        

    });
});