<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\IotDataPlane\IotDataPlaneClient;
use Aws\Exception\AwsException;
use Filament\Notifications\Notification;
use App\Models\Command;
use App\Models\Thing;

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
}
