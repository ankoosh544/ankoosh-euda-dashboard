<?php 
    use Aws\Iot\IotClient;
    use Aws\Lambda\LambdaClient;
    $iotClient = new IotClient([
        'version' => 'latest',
        'region' => env('AWS_DEFAULT_REGION'),
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
    ]);

    $lambdaClient = new LambdaClient([
        'version' => 'latest',
        'region' => env('AWS_DEFAULT_REGION'),
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
    ]);

    // List IoT rules
    $iotRules = $iotClient->listTopicRules();

    //dd($iotRules['rules']);die();
   
    $lambdaFunctions = $lambdaClient->listFunctions();
   


?>
<div class="w-full rounded-xl bg-white p-4 shadow dark:border-gray-600 dark:bg-gray-700">
    <h1 class="flex items-center rtl:space-x-reverse text-sm font-medium text-gray-500 dark:text-gray-200 mb-3">
       
       AwsIOT Mqtt Routing Rules
    </h1>

    <div class="flex gap-4 pd-5">
        <!-- Column 1: Form to create rules -->
        <div class="w-1/2">
            <div class="w-full rounded-xl p-4 shadow dark:border-gray-600 dark:bg-gray-700">
                <h1 class="flex items-center rtl:space-x-reverse text-sm font-medium text-gray-500 dark:text-gray-200 mb-2">
                    Create Rule
                </h1>
                <form action="{{ url('/rule') }}" method="POST">
                @csrf
                
                    <label for="ruleName">Rule Name:</label>
                    <input type="text" id="ruleName" name="ruleName" class="w-full p-2 mb-2" />

                    <label for="topic">Select Topic:</label>
                    <select id="topic" name="topic" class="w-full p-2 mb-2">
                        <option value="state">State</option>
                        <option value="event">Event</option>
                        <!-- Add more topics as needed -->
                    </select>
                    <input type="hidden" name="plantId" value="{{ $data->plantId }}">
                    <button type="submit" class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">Create Rule</button>
                </form>
            </div>
        </div>

        <!-- Column 2: Display existing rules -->
        <div class="w-1/2">
            <div class="w-full rounded-xl p-4 shadow dark:border-gray-600 dark:bg-gray-700">
                <h1 class="flex items-center rtl:space-x-reverse text-sm font-medium text-gray-500 dark:text-gray-200 mb-2">
                    Create Lambda Function and Trigger
                </h1>

                <form action="{{ url('/trigger-action') }}" method="post">
                    @csrf

                    <div class="mb-4">
                        <label for="iotRule" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select IoT Rule</label>
                        <select name="iotRule" id="iotRule" class="mt-1 block w-full p-2 border rounded-md bg-gray-100">
                        <option> Select Rule</option>
                            @foreach($iotRules['rules'] as $rule)
                                <option value="{{ $rule['ruleName'] }}">{{ $rule['ruleName'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="lambdaFunction" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Lambda Function</label>
                        <select name="lambdaFunction" id="lambdaFunction" class="mt-1 block w-full p-2 border rounded-md bg-gray-100">
                        <option> Select Lambda Function</option>
                            @foreach($lambdaFunctions['Functions'] as $function)
                                <option value="{{ $function['FunctionName'] }}">{{ $function['FunctionName'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="plantId" value="{{ $data->plantId }}">
                    <button type="submit" class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">
                        Trigger
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
