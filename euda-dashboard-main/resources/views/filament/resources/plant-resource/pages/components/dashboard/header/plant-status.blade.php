<?php
use Carbon\Carbon;

$minutesDifference = 0;
//dd($lastcommunication);die();

if ($lastcommunication) {
    // Convert the provided $updated_at string to a Carbon instance
    $lastcommunication = Carbon::parse($lastcommunication)->setTimezone('Europe/Rome');

    // Get the current datetime in the 'Europe/Rome' timezone
    $currentDateTime = Carbon::now('Europe/Rome');

    // Check if the day has changed since the last update
    if ($currentDateTime->diffInDays($lastcommunication) > 0) {
        // If the day has changed, reset the $minutesDifference to 0
        $minutesDifference = 0;
    } else {
        // Calculate the difference in minutes within the same day
        $minutesDifference = $lastcommunication->diffInMinutes($currentDateTime);
    }
}
//dd($status);die();

//dd($alarms['OOS']);die();

?>
@if($minutesDifference > 450)
    <div class="relative flex items-center bg-red-100 px-4 py-1 rounded-md text-sm font-semibold text-red-800 w-fit">
                <span class="relative flex h-3 w-3 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
            Connection Lost! Last Communication happens at {{$lastcommunication}}
    </div>
    
@endif
@if($alarms['OOS'] == 0)
    <div class="relative flex items-center bg-green-100 px-4 py-1 rounded-md text-sm font-semibold text-green-800 w-fit">
        <span class="relative flex h-3 w-3 mr-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
        </span>
        CCMI 
        <br>         
        (Check Control Message Internet) 
   
    
    </div>
    
@else
    <div class="relative flex items-center bg-red-100 px-4 py-1 rounded-md text-sm font-semibold text-red-800 w-fit">
        <span class="relative flex h-3 w-3 mr-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        </span>
        Watchout! A problem has been detected in the system
    </div>
@endif



