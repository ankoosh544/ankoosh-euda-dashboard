<div class="mb-3 2xl:px-4 px-0">
        <!-- <h3 class="text-gray-500 dark:text-gray-200 text-sm">Total:</h3>
        <h2 class="text-xl leading-none font-semibold">{{ $totalOutService }}</h2> -->
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                        </span>
                        Last SOS : {{ $lastOutService }}
            
        </div>
        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
        (Smartphone Out of Service Information)
        </div>
    </div>
    <div class="mb-3 2xl:px-4 px-0">
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                            <span class="relative flex h-2 w-2 mr-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                            
                            </span>
                            Total SOS : {{ $totalOutService }}
            
                </div>
                <div class="group/doorFault relative cursor-default flex items-center text-xs  text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">       
                    (Smartphone Out of Service Information)
                </div>
        </div>

    <div class="mb-3 2xl:px-4 px-0">
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                        </span>
                        Last DFD : {{ $lastDoorFault }}
            
        </div>
        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                    
            (Door Fault Detection)
            </div>
    </div>  
    <div class="mb-3 2xl:px-4 px-0">  
        
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                  

                Last DFD Floor Number : {{ $lastDoorFaultFloor }}
            
        </div>
        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                    
            (Door Fault Detection)
            </div>
    </div>
    <div class="mb-3 2xl:px-4 px-0">   
       
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                <span class="relative flex h-2 w-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                </span>
                Total DFD : {{ $totalDoorFault }}
            </div>
            <div class="group/doorFault relative cursor-default flex items-center text-xs  text-yellow-800 w-fit bg-yellow-100 rounded-md px-2 py-0.5">
                    
            (Door Fault Detection)
            </div>
        </div>  