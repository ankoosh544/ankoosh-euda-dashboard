
<div class="mb-3 2xl:px-4 px-0">
    <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
        <span class="relative flex h-2 w-2 mr-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>              
        </span>    
        Last OOS : {{ $lastOutService }}
    </div>

        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
        (Out of Service)
        </div>
    </div>
    <div class="mb-3 2xl:px-4 px-0">
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
                            <span class="relative flex h-2 w-2 mr-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            
                            </span>
                            Total OSN : {{ $totalOutService }}
            
                </div>
                <div class="group/doorFault relative cursor-default flex items-center text-xs  text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">       
                    (Out of Service)
                </div>
        </div>
    <div class="mb-3 2xl:px-4 px-0">
        <!-- <h3 class="text-gray-500 dark:text-gray-200 text-sm">Last door fault:</h3> -->
            <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
                        <span class="relative flex h-2 w-2 mr-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        
                        </span>
                        Last DFD : {{ $lastDoorFault }}
                     
            </div>
            <div class="group/doorFault relative cursor-default flex items-center text-xs text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
                       
            (Door Fault Detection)
                     
            </div>
            
    </div>        
    <div class="mb-3 2xl:px-4 px-0">
            <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
                        <span class="relative flex h-2 w-2 mr-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        
                        </span>
                        Total DFD : {{ $totalDoorFault }}
                        
            </div>
            <div class="group/doorFault relative cursor-default flex items-center text-xs  text-green-800 w-fit bg-green-100 rounded-md px-2 py-0.5">
                    
            (Door Fault Detection)
            </div>
            <!-- <h2 class="text-xl leading-none font-semibold">{{ $lastDoorFault }}</h2> -->
    </div>