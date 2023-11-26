<x-filament::page :widget-data="['record' => $record]">
    <div class="plant-view rounded-xl bg-gray-100 shadow dark:bg-gray-800 dark:text-gray-100">
        <div
            class="w-full p-4 rounded-t-xl bg-white flex xl:flex-row flex-col gap-3 xl:justify-between border-b border-b-slate-200 dark:bg-gray-700 dark:border-gray-600">
            
            @include('filament.resources.plant-resource.pages.components.dashboard.header.plant-info', ['record' => $record])

            <div class="flex flex-col justify-end gap-3 items-end">
                
                @include('filament.resources.plant-resource.pages.components.dashboard.header.plant-date')

            </div>
        </div>
        <div class="w-full p-4">
            <div class="w-full h-80 flex justify-center items-center">
                <h1 class="text-3xl text-gray-400 dark:text-gray-200">No data found for the selected date</h1>
            </div>
        </div>
    </div>
    @livewireChartsScripts
</x-filament::page>
