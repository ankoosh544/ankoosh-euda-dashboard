
<div class="mb-3 2xl:px-4 px-0">
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        Last SOS : {{ $lastOutService }}
            
        </div>
        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
        (Smartphone Out of Service Information)
        </div>
    </div>
    <div class="mb-3 2xl:px-4 px-0">
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                            <span class="relative flex h-2 w-2 mr-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            
                            </span>
                            Total SOS : {{ $totalOutService }}
            
                </div>
                <div class="group/doorFault relative cursor-default flex items-center text-xs  text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">       
                    (Smartphone Out of Service Information)
                </div>
        </div>

    <div class="mb-3 2xl:px-4 px-0">
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        Last DFD : {{ $lastDoorFault }}
            
        </div>
        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                    
            (Door Fault Detection)
            </div>
    </div>  
    <div class="mb-3 2xl:px-4 px-0">  
        
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                  

                Last DFD Floor Number : {{ $lastDoorFaultFloor }}
            
        </div>
        <div class="group/doorFault relative cursor-default flex items-center text-xs  text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                    
            (Door Fault Detection)
            </div>
    </div>
    <div class="mb-3 2xl:px-4 px-0">   
       
        <div class="group/doorFault relative cursor-default flex items-center text-xs font-bold text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                <span class="relative flex h-2 w-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
                Total DFD : {{ $totalDoorFault }}
            </div>
            <div class="group/doorFault relative cursor-default flex items-center text-xs  text-red-800 w-fit bg-red-100 rounded-md px-2 py-0.5">
                    
            (Door Fault Detection)
            </div>
        </div>  