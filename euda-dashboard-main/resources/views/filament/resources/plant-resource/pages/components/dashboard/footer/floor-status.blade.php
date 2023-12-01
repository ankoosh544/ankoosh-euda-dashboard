<div class="w-full rounded-xl  p-4 shadow dark:border-gray-600 dark:bg-gray-700">
<div class="w-full rounded-xl bg-white p-4 shadow dark:border-gray-600 dark:bg-gray-700">
    <h1 class="flex items-center rtl:space-x-reverse text-sm font-medium text-gray-500 dark:text-gray-200 mb-2">
        Floors status</h1>
      
        
    <div class="flex flex-wrap gap-1">
    
        @foreach ($data->DFD as $k=> $floor)
       
            @php    
                
                
                //dd($currentFloor);die();
                //$floor = (array) $floor; 
                //dd($floor);die();
                //dd($data->IUPS->f);die();
                //dd($floor[$k]);die();
            @endphp
           
            @if ($floor >= 2)
                <div
                    class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                    <span class="relative flex h-2 w-2 mr-2">
                        @if ($k == $currentFloor)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-800 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-800"></span>
                        @else
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        @endif
                    </span>
                    {{ $loop->index }}
                    <div
                        class="hidden group-hover/doorFault:block absolute top-6 left-0 px-4 py-2 bg-white 
                            shadow border rounded-lg border-slate-200 dark:border-gray-600 dark:bg-gray-700
                            text-gray-700 dark:text-gray-200 font-normal w-max">
                            @if ( $loop->index == $currentFloor)
                                <div class="thick-circle">This is Current Car Position and Problem with the door locking</div>

                                @else
                                    There may be a problem with the door locking
                                @endif  
                    </div>
                </div>
            @elseif($floor == 1)
                <div
                    class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                    <span class="relative flex h-2 w-2 mr-2">
                    @if ($loop->index == $currentFloor)
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-800 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-600"></span>
                    @else
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                    @endif    
                    </span>
                    {{ $loop->index }}
                    <div
                        class="hidden group-hover/doorFault:block absolute top-6 left-0 px-4 py-2 bg-white 
                                shadow border rounded-lg border-slate-200 dark:border-gray-600 dark:bg-gray-700
                                text-gray-700 dark:text-gray-200 font-normal w-max">
                                @if ( $loop->index == $currentFloor)
                                <div class="thick-circle">This is Current Car Position and Problem with the door locking</div>

                                @else
                                    There may be a problem with the door locking
                                @endif    
                    </div>
                </div>
            @else
                <div
                    class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
                    <span class="relative flex h-2 w-2 mr-2">
                    @if ($loop->index == $currentFloor)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-800 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-800"></span>
                    @else
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    @endif
                    </span>
                    {{ $loop->index }}
                    <div
                        class="hidden group-hover/doorFault:block absolute top-6 left-0 px-4 py-2 bg-white 
                                shadow border rounded-lg border-slate-200 dark:border-gray-600 dark:bg-gray-700
                                text-gray-700 dark:text-gray-200 font-normal w-max">
                                @if ( $loop->index == $currentFloor)
                                    <div class="thick-circle">This is Current Car Position and No problems detected</div>
                                @else
                                    No problems detected
                                @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
</div>

