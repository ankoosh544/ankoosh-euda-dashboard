@php
    use Carbon\Carbon;
 
    $data = json_decode($awsData);
    //dd($data->IUPS->f);die();
    //dd($IUPS);die();
    //dd($DFD);die();
    //dd($versionInstalled);die();
    $floorChart = new \Asantibanez\LivewireCharts\Models\RadarChartModel();

    $actualFloor = 0;
    foreach ($FCN as $floor) {
        $floorChart->addSeries('Stops', 'Floor ' . $actualFloor, $floor);
        $actualFloor++;
    }

    if (Auth::check() && Auth::user()->is_admin) {
        $admin = true;
    } else {
        $admin = false;

    }

@endphp
<x-filament::page :widget-data="['record' => $record]">
    
    <div class="plant-view rounded-xl bg-gray-100 shadow dark:bg-gray-800 dark:text-gray-100">
        <div class="w-full p-2 rounded-t-xl bg-white flex xl:flex-row flex-col gap-3 xl:justify-between border-b border-b-slate-200 dark:bg-gray-700 dark:border-gray-600">
            
            @include('filament.resources.plant-resource.pages.components.dashboard.header.plant-info', ['record' => $record, 'versionInstalled' => $versionInstalled])
            
            <div class="flex flex-col justify-between gap-3 items-end">

                @include('filament.resources.plant-resource.pages.components.dashboard.header.plant-status', ['status' => $status, 'updated_at' => $data->updated_at])

                @include('filament.resources.plant-resource.pages.components.dashboard.header.plant-date')

            </div>
        </div>
        <div class="w-full p-2">

            @include('filament.resources.plant-resource.pages.components.dashboard.body.body', 
            [
                'floorChart' => $floorChart,
                'data' => $data,
                'alarms' => $alarms,
                'CAM' => $CAM,
                'DON' =>$DON,
            ])
            
        </div>
        <div class="w-full">
            @include('filament.resources.plant-resource.pages.components.dashboard.footer.floor-status', ['data' => $data, 'iups' => $DFD[1],  'dfd' => !empty($DFD[0]) ? $DFD[0] : ($data->DFD ?? $data->event->DFD), ])
        </div>
        @if($admin)        
        <div class="w-full">
            @include('filament.resources.plant-resource.pages.components.dashboard.body.components.plant-rule', ['data' => $record, ])
        </div>
        @endif
       
        @push('scripts')
            <script>
                setInterval(() => {
                    @this.call('getData');
                }, 10000);
            </script>
        @endpush
    </div>
    @livewireChartsScripts
   
</x-filament::page>
