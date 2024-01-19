<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Iot\IotClient;
use App\Models\Thing;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use phpseclib\Net\SSH2;

class AwsIotController extends Controller
{
    public function createThing(Request $request)
    {

        $thingName = $request->input('thing_name');
        $thingType = $request->input('thing_type');
        $plantId = $request->input('plant_id'); 

        $attributes = [
            'plantId' => $plantId
        ];
    
        // Initialize AWS IoT Client
        $iotClient = new IotClient(config('aws'));
    
        // Create IoT Thing
        $result = $iotClient->createThing([
            'thingName' => $thingName,
            'thingTypeName' => $thingType,
            'attributePayload' => [
                'attributes' => $attributes,
            ],
        ]);
    
        // Save the IoT Thing record in the "things" table
        $thing = new Thing();
        $thing->thing_name = $thingName;
        $thing->thing_type = $thingType;
        $thing->user_id = 1; 
        $thing->plant_id = $plantId;
    
        $thing->save();
    
        // Generate certificates and attach them to the thing
        $certificates = $iotClient->createKeysAndCertificate([]);

        $iotClient->attachThingPrincipal([
            'thingName' => $thingName,
            'principal' => $certificates['certificateArn'],
        ]);
    
        // Return the response
        $response = response()->json([
            'message' => 'AWS IoT Thing created successfully',
            'thing' => $thing,
            'certificates' => $certificates,
        ]);
    
        return $response;
    }

    public function things(Request $request){
            //$user = auth()->user();
        $things = Thing::where('user_id', 2)->get();

        // Return the list of things
        return response()->json([
            'message' => 'List of IoT Things for the authenticated user',
            'things' => $things,
        ]);

    }
    // public function plants(Request $request){
    //         //$user = auth()->user();
    //         $data = Plant::where('assigned_to', 2)
    //         ->select('id', 'plant_id', 'name')
    //         ->get();
        
    //     return response()->json([
    //         'message' => 'success',
    //         'data' => $data,
    //     ]);

    // }
    public function plants(Request $request)
    {
        // Ensure the request is authorized and the user is authenticated
        $this->middleware('auth:api');
        $skip = $request->query('skip', 0);
        $limit = $request->query('limit', 2); 

        // Retrieve authenticated user
        $user = auth()->user();
        //dd($user);die();
        // Fetch plant data based on authorization logic
        $plants = Plant::where('assigned_to', $user->id)
        ->skip($skip)
        ->take($limit)
        ->select('id', 'plant_id', 'name','schedule_date', 'status', 'address', 'state', 'city', 'cap', 'country_code')
        ->get();

        // $data = Plant::where('assigned_to', $user->id)
        //     ->select('id', 'plant_id', 'name','schedule_date', 'status', 'address', 'state', 'city', 'cap', 'country_code')
        //     ->get();

        return response()->json([
            'message' => 'success',
            'data' => $plants,
        ]);
    }

    public function search(Request $request)
    {
        $this->middleware('auth:api');
        $query = $request->query('q');
        $skip = $request->query('skip', 0);
        $limit = $request->query('limit', 10); // Replace 10 with your PLANTS_PER_PAGE constant

        $plants = Plant::where('name', 'like', "%{$query}%")
            ->skip($skip)
            ->take($limit)
            ->get();

        return response()->json($plants);
    }

    public function updateStatus(Request $request){
        $this->middleware('auth:api');
    
        // Validate the request if necessary
    
        $plantId = $request->plantId;
    
        // Retrieve the Plant record
        $plant = Plant::where('plant_id', $plantId)->first();
    
        if (!$plant) {
            // Handle the case where the plant is not found
            return response()->json(['error' => 'Plant not found'], 404);
        }
        // Update the status
        $plant->status = $request->newStatus; 
        // Save the changes
        $plant->save();
        // Optionally, you can return a response indicating success
        return response()->json(['message' => 'Plant status updated successfully']);
    }
    public function getPlantInfo($plantId){
        $plant = Plant::where('plant_id', $plantId)->first();
        if (!$plant) {
            // Handle the case where the plant is not found
            return response()->json(['error' => 'Plant not found'], 404);
        }
        return response()->json(['message' => 'GGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGG']);

    }
    public function technicians(Request $request)
{
    $this->middleware('auth:api');

    // Use where clause to filter technicians based on isTechnician value
    $technicians = User::where('is_technician', $request->isTechnician)->get();

    // Use map to extract specific attributes from each technician
    $userAttributes = $technicians->map(function ($technician) {
        return [
            'id' => $technician->id,
            'name' => $technician->name,
            'isTechnician' => $technician->is_technician,
        ];
    });

    // Return the list of technicians
    return response()->json([
        'message' => 'List of Technicians',
        'technicians' => $userAttributes,
    ]);
}

    public function getDevices(Request $request)
    {
        //$wifiSsid = 'Vodafone-C01010249';
        //$wifiPassword = 'Pe3xfFyZAyhnKKAz';z
        $wifiSsid='ankoosh544';
        $wifiPassword='ankoosh544';

        // Connect to Wi-Fi network
        $connectionStatus = $this->connectToWifi($wifiSsid, $wifiPassword);

        if (!$connectionStatus) {
            return response()->json(['error' => 'Failed to connect to Wi-Fi'], 401);
        }

        // Execute the command to list connected devices using ARP
        $output = shell_exec('arp -a');

        // Parse the output to get connected devices
        $devices = $this->parseArpOutput($output);

        // Return the list of connected devices
        return response()->json(['devices' => $devices]);
    }
    private function connectToWifi($ssid, $password)
    {
        // Platform-specific commands
        $commands = [
            'linux' => "sudo nmcli device wifi connect '$ssid' password '$password'",
            'windows' => 'netsh wlan connect ssid="' . $ssid . '" key="' . $password . '"',
            // Add more platforms if needed
        ];
    
        $platform = strtolower(PHP_OS_FAMILY);
    
        if (isset($commands[$platform])) {
            $command = $commands[$platform];
            shell_exec($command);
        } else {
            // Handle unsupported platform
            error_log("Unsupported platform: $platform");
        }
    }

    private function parseOutput($output)
    {
        // Implement your logic to parse the output and extract connected devices
        // Example: You might use regular expressions or string manipulation to extract device information
        // Replace the following line with your parsing logic
        $devices = explode("\n", $output);

        return $devices;
    }

    
}

