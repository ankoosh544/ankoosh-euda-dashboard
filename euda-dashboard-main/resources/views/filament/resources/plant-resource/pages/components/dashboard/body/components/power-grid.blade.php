<div class="w-full rounded-xl bg-white p-5 shadow dark:border-gray-600 dark:bg-gray-700">
    <div class="w-full rounded-xl bg-white p-5 shadow dark:border-gray-600 dark:bg-gray-700" style="background-color: {{ $alarms['AC'] == 1 ? 'green' : 'orange' }}; color: white;">
        <div class="flex items-center rtl:space-x-reverse mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-1">
                <rect x="2" y="4" width="18" height="12" rx="2" ry="2" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></rect>
                <line x1="6" y1="8" x2="6" y2="16" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></line>
                <line x1="18" y1="8" x2="18" y2="16" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></line>
            </svg>
            <h1 class="text-sm font-medium text-white">AC</h1>
        </div>
        <h1 class="flex items-center rtl:space-x-reverse text-xs font-medium text-white-500 dark:text-gray-200 mb-2">
            (AC Status)
        </h1>
        <h2 class="text-2xl">{{ $alarms['AC'] == 1 ? 'ON' : 'OFF' }} </h2>
    </div>
</div>
