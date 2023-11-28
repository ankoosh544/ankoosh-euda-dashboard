<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommandController;
use App\Filament\Resources\ThingResource;




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
Route::post('/command', [CommandController::class, 'handleFormSubmission']);
Route::get('/reset', [CommandController::class, 'reset']);
Route::get('/things/{record}/download', [ThingResource::class, 'downloadAction'])
    ->name('filament.resources.things.download');

Route::post('/rule', [CommandController::class, 'createIotRule']);    

Route::post('/trigger-action', [CommandController::class, 'triggerAction']);

Route::get('/sendPlantId', [CommandController::class, 'sendPlantId']);

Route::get('/create-iot-rule', function () {
        // Set up AWS IoT client
        $iotClient = app(IotClient::class);
    
        // Define rule parameters
        $ruleName = 'YourRuleName';
        $sqlQuery = "SELECT * FROM 'topic_1'";
        $actions = [
            [
                'ruleDisabled' => false,
                'type' => 'SNS',
                'snsTopicArn' => 'arn:aws:sns:your-region:your-account-id:your-sns-topic',
            ],
        ];
    
        // Create the rule
        $result = $iotClient->createTopicRule([
            'ruleName' => $ruleName,
            'topicRulePayload' => [
                'sql' => $sqlQuery,
                'actions' => $actions,
            ],
        ]);
    
        // Print result or handle errors
        dd($result);
    });

