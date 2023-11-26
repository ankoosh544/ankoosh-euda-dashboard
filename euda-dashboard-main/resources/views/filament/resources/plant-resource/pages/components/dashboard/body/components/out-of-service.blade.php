<?php //dd($alarms['OOS']);die();?>
<?php //dd($totalOutService);die(); ?>

<div class="w-full rounded-xl bg-white p-5 shadow dark:border-gray-600 dark:bg-gray-700">
    <h1 class="flex items-center rtl:space-x-reverse text-sm font-medium text-gray-500 dark:text-gray-200 mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-5 h-5 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        Out of Service Panel
    </h1>
    @if($alarms['OOS'] == 1)
        @include('filament.resources.plant-resource.pages.components.dashboard.body.components.oos-panel-oos-status')

    @elseif($totalOutService == 0 && $totalDoorFault == 0 && $alarms['OOS'] == 0)

        @include('filament.resources.plant-resource.pages.components.dashboard.body.components.oos-panel-active-status')

    @elseif(!empty($totalOutService) || !empty($totalDoorFault) && $alarms['OOS'] == 0)

        @include('filament.resources.plant-resource.pages.components.dashboard.body.components.oos-panel-warning-status')
  
    @endif
</div>
