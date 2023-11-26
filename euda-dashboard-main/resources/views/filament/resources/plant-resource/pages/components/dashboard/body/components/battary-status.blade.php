
<div class="w-full rounded-xl bg-white p-5 shadow dark:border-gray-600 dark:bg-gray-700">

        <!-- First block -->
        <div class="flex items-center mb-2 rounded-xl p-2" style="background-color: {{ $alarms['BAT'] == 1 ? 'green' : 'orange' }}; color: white;">
            <h1 class="text-sm font-medium text-white">ICUD BAT</h1>
            <h1 class="flex items-center text-xs font-medium text-white-500 dark:text-gray-200 ml-2">
                (Battery Status)
            </h1>
            <h2 class="text-2xl">{{ $alarms['BAT'] == 1 ? 'ON' : 'OFF' }}</h2>
        </div>

        <!-- Second block -->
        <div class="flex items-center mb-2 rounded-xl p-2" style="background-color: {{ $alarms['BAT'] == 1 ? 'green' : 'orange' }}; color: white;">
            <h1 class="text-sm font-medium text-white">HUFD BAT</h1>
            <h1 class="flex items-center text-xs font-medium text-white-500 dark:text-gray-200 ml-2">
                (Battery Status)
            </h1>
            <h2 class="text-2xl">{{ $alarms['BAT'] == 1 ? 'ON' : 'OFF' }}</h2>
        </div>
    
</div>
