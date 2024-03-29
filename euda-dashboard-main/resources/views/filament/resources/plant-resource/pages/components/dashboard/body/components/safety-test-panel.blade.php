
<div class="w-full rounded-xl bg-white p-5 shadow dark:border-gray-600 dark:bg-gray-700">
    <h1 class="flex items-center rtl:space-x-reverse text-sm font-medium text-gray-500 dark:text-gray-200 mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-5 h-5 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
        </svg>
        Elevator Remote Test Panel
    </h1>
    
    <div class="mb-3 2xl:px-4 px-0">
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
            <span class="relative flex h-2 w-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            
            </span>
            Total ERM : {{$data->total_erm}}
            </br>
            Last ERM : {{ $data->lastErm_date }}                
        </div>

        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
            (Elevator Remote Move)
        </div>
    </div>

    <div class="mb-3 2xl:px-4 px-0">
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
            <span class="relative flex h-2 w-2 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            
                </span>
                Total ERR : {{ $data->total_err }}
                    </br>
                Last ERR : {{$data->lastErr_date}}
                            
        </div>

        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
        (Euda Remote Reset)
        </div>
    </div>
</div>
