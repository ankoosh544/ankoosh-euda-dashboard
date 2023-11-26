<div class="flex xl:flex-row flex-col gap-2 justify-between">
    
    <div class="h-80 xl:basis-4/12 rounded-xl bg-white p-4 shadow dark:border-gray-600 dark:bg-gray-700 relative">
        @include('filament.resources.plant-resource.pages.components.dashboard.body.components.floor-calls', ['floorChart' => $floorChart]) 
    </div>
    <div class="xl:basis-1/2 flex flex-col gap-2 xl:gap-1 justify-between">
        <div class="flex xl:flex-row flex-col gap-2">
            <div class="flex flex-col gap-2">
                @include('filament.resources.plant-resource.pages.components.dashboard.body.components.out-of-service', ['data' => $data])
            </div>
            <div class="flex flex-col gap-2">
                @include('filament.resources.plant-resource.pages.components.dashboard.body.components.safety-test-panel',['data' => $data])
                @include('filament.resources.plant-resource.pages.components.dashboard.body.components.remote-reset', ['data' => $data])
            </div>
            
        </div>
        <div class="flex xl:flex-row flex-col gap-2">
             
            @include('filament.resources.plant-resource.pages.components.dashboard.body.components.remote-command', ['data' => $data])
            
        </div>
        <div class="flex xl:flex-row flex-col gap-2">

                @include('filament.resources.plant-resource.pages.components.dashboard.body.components.car-millage', ['data' => $data])
                @include('filament.resources.plant-resource.pages.components.dashboard.body.components.door-openings', ['data' => $data])
            
        </div>
    </div>
    
   
    <div class="xl:basis-3/12 flex flex-col gap-3 xl:gap-1 justify-between">
        <div class="flex flex-col gap-2">
        @include('filament.resources.plant-resource.pages.components.dashboard.body.components.battary-status', ['alarms' => $alarms])
        @include('filament.resources.plant-resource.pages.components.dashboard.body.components.power-grid', ['alarms' => $alarms])
        @include('filament.resources.plant-resource.pages.components.dashboard.body.components.light-warning', ['alarms' => $alarms])
        @include('filament.resources.plant-resource.pages.components.dashboard.body.components.rides', ['data' => $data])
        </div>
    </div>
    
</div>

