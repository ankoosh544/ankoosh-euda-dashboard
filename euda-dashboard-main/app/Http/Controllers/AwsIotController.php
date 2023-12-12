<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Iot\IotClient;
use App\Models\Thing;
use App\Models\Plant;
use Illuminate\Support\Facades\Auth;

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

        // Retrieve authenticated user
        $user = auth()->user();
        //dd($user);die();
        // Fetch plant data based on authorization logic
        $data = Plant::where('assigned_to', $user->id)
            ->select('id', 'plant_id', 'name','schedule_date', 'status', 'address', 'state', 'city', 'cap', 'country_code')
            ->get();

        return response()->json([
            'message' => 'success',
            'data' => $data,
        ]);
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
    
}

