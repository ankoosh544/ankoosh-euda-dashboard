<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\IotDataPlane\IotDataPlaneClient;
use Aws\Exception\AwsException;
use Filament\Notifications\Notification;
use App\Models\Command;
use App\Models\Thing;
use Aws\Iot\IotClient;
use Aws\Lambda\LambdaClient;
use Aws\CloudWatchLogs\CloudWatchLogsClient;

class CommandController extends Controller
{
    public function handleFormSubmission(Request $request)
    {

        $thing = Thing::where('plantId', $request->plantId)
        ->where('thing_type', 'icud')
        ->first();
        if ($thing) {
            $commandTopic = 'icud/' . $thing->thing_name . '/command';
        } else {
            return;
        }
        //dd($request->plantId);die();
        // Validate the form data
        $request->validate([
            'floorNumber' => 'required|integer',
        ]);

        // Get the submitted floor number
        $floorNumber = $request->input('floorNumber');

        // Create a JSON payload
        $payload = json_encode([
            'ERM' => $floorNumber,
        ]);

    

        // Publish the JSON payload to an MQTT topic
        try {
            $iotData = new IotDataPlaneClient([
                'region' => 'eu-central-1', 
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]); 
            $qos = 1; 

        $iotData->publish([
                'topic' => $commandTopic,
                'payload' => $payload,
                'qos' => $qos,
            ]);
        $command = Command::create([
                'plantId' => $request->plantId, 
                'ERM' => $request->floorNumber,
                'SET' => null, 
                'GET' => null,
                'ERR' => false,
            ]);
         
        Notification::make()
            ->title('Command published successfully.')
            ->send();
            
            // Redirect or return a response as needed
            return redirect()->back();

        } catch (AwsException $e) {
            Notification::make()
            ->title('Failed to publish the command')
            ->send();
            // Handle any AWS IoT exceptions here
            return redirect()->back();
        }
    }

    public function reset(Request $request){
       
        //dd($request->plantId);die();

        $thing = Thing::where('plantId', $request->plantId)
            ->where('thing_type', 'icud')
            ->first();

        //dd($thing);die();

        $payload = json_encode([
            'ERR' => true,
        ]);
        
        // Publish the JSON payload to an MQTT topic
        try {
            $iotData = new IotDataPlaneClient([
                'region' => 'eu-central-1', 
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);
            if ($thing) {
                $commandTopic = 'icud/' . $thing->thing_name . '/command';
            } else {
                return;
            }
            $qos = 1; 

        $iotData->publish([
                'topic' => $commandTopic,
                'payload' => $payload,
                'qos' => $qos,
            ]);
         
        Command::create([
                'plantId' => $request->plantId, 
                'ERM' => null, 
                'SET' => null, 
                'GET' => null,
                'ERR' => true,
            ]);    
            


        Notification::make()
            ->title('Command published successfully.')
            ->send();
            
            // Redirect or return a response as needed
            return redirect()->back();

        } catch (AwsException $e) {
            Notification::make()
            ->title('Failed to publish the command')
            ->send();
            // Handle any AWS IoT exceptions here
            return redirect()->back();
        }

    }


    public function createIotRule(Request $request)
    {
        $ruleName = $request->ruleName;
        $topic = $request->topic;
        $plantId = $request->plantId;
        $thing = Thing::where('plantId', $plantId)
                      ->where('thing_type', 'icud')
                      ->first();
    
        $topic_name = $thing->thing_type.'/'.$thing->thing_name.'/'.$topic;
           
        try {
            $iotClient = new IotClient([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);
    
            $roleArn = 'arn:aws:iam::880819439345:role/service-role/Admin';
    
            if ($topic == 'state') {
                $lambda_function = 'global_state';
                $lambdaFunctionArn = 'arn:aws:lambda:eu-central-1:880819439345:function:'.$lambda_function;
            } elseif ($topic == 'event') {
                $lambda_function = 'global_event';
                $lambdaFunctionArn = 'arn:aws:lambda:eu-central-1:880819439345:function:'.$lambda_function;
            }
            
            $rulePayload = [
                'ruleName' => $ruleName,
                'topicRulePayload' => [
                    'sql' => "SELECT * FROM '$topic_name'",
                    'actions' => [
                        [
                            'lambda' => [
                                'functionArn' => $lambdaFunctionArn,
                            ],
                        ],
                    ],
                    'description' => 'Your rule description',
                    'ruleDisabled' => false,
                ],
            ];
        
            $iotClient->createTopicRule($rulePayload);
            
            Notification::make()
                ->title('Rule created successfully.')
                ->send();
                
            // Redirect or return a response as needed
            return redirect()->back();
        } catch (AwsException $e) {
            Notification::make()
                ->title('Failed to Create Rule')
                ->send();
            return redirect()->back();
        }
    }



    public function triggerAction(Request $request)
{
    // Retrieve parameters from the request
    $iotRuleName = $request->input('iotRule');
    $lambdaFunctionName = $request->input('lambdaFunction');

    try {
        // Initialize IoT client
        $iotClient = new IotClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // Get IoT rule details
        $iotRule = $iotClient->getTopicRule(['ruleName' => $iotRuleName]);

        // Check if the rule has actions
        if (isset($iotRule['rule']['actions']) && !empty($iotRule['rule']['actions'])) {
            // Find the Lambda action in the rule
            $lambdaActions = collect($iotRule['rule']['actions'])->filter(function ($action) {
                return isset($action['lambda']);
            });

            // If Lambda action is found, get the function ARN
            if (!$lambdaActions->isEmpty()) {
                // Assuming you want to trigger the first Lambda action found
                $lambdaAction = $lambdaActions->first();
                $lambdaFunctionArn = $lambdaAction['lambda']['functionArn'];

                // Generate a unique statement ID based on some criteria
                $statementId = 'iot-trigger-' . time();

                // Initialize Lambda client
                $lambdaClient = new LambdaClient([
                    'version' => 'latest',
                    'region' => env('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);

                $sourceAccount = '880819439345';
                $result = $lambdaClient->addPermission([
                    'FunctionName' => $lambdaFunctionName,
                    'StatementId' => $statementId,
                    'Action' => 'lambda:InvokeFunction',
                    'Principal' => 'iot.amazonaws.com',
                    'SourceArn' => $iotRule['ruleArn'],
                    'SourceAccount' => $sourceAccount,
                ]);

                Notification::make()
                    ->title('Rule Triggered successfully.')
                    ->send();

                // Redirect or return a response as needed
                return redirect()->back();
            }
        }

        // Send failure notification
        Notification::make()
            ->title('Failed to trigger the Rule.1111')
            ->send();

        // Redirect or return a response as needed
        return redirect()->back();

    } catch (AwsException $e) {
        dd($e);
        Notification::make()
            ->title('Failed to trigger the Rule.'.$e->getMessage())
            ->send();

        // Log the exception and return an error response
        logger('Failed to trigger rule: ' . $e->getMessage());

        return redirect()->back()->with('error', 'Failed to trigger rule: ' . $e->getMessage());
    }
}


    public function sendPlantId(Request $request){
        //dd($request->plantId);die();
       
        $thing = Thing::where('plantId', $request->plantId)
        ->where('thing_type', 'icud')
        ->first();

        $payload = json_encode([
            "SET" => [
                "plantId" => $request->plantId
            ]
        ]);
        
        
        // Publish the JSON payload to an MQTT topic
        try {
            $iotData = new IotDataPlaneClient([
                'region' => 'eu-central-1', 
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);
            if ($thing) {
                $commandTopic = 'icud/' . $thing->thing_name . '/command';
            } else {
                return;
            }
            $qos = 1; 

            $iotData->publish([
                    'topic' => $commandTopic,
                    'payload' => $payload,
                    'qos' => $qos,
                ]);
         

        Notification::make()
            ->title('Command published successfully.')
            ->send();
            
            // Redirect or return a response as needed
            return redirect()->back();

        } catch (AwsException $e) {
            Notification::make()
            ->title('Failed to publish the command')
            ->send();
            // Handle any AWS IoT exceptions here
            return redirect()->back();
        }

    }
}
