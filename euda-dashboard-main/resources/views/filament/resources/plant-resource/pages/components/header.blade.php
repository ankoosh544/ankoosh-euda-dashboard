<?php
use App\Models\Job;
use Illuminate\Support\Facades\Cache;

$latestUploads = Cache::get('latestUploads', []);

$versionInstalled = Cache::get('versionInstalled', []);
//dd($versionInstalled);die();

$records = [];
$noversionFound = [];
foreach ($latestUploads as $key => $latestUpload) {

    $record = Job::where('version_number', $latestUpload['version'])
    ->where('thing_type', $latestUpload['thing_type'])
    ->whereIn('status', ['IN_PROGRESS', 'Queued','COMPLETED' ])
    ->where('plantId', $plant->plant_id)
    ->where('status', '!=', 'CANCELED')
    ->first();
    if ($record) {
        $records[] = $record;
    }else {
       // dd($latestUpload);die();
        $noversionFound[] = $latestUpload;
    }
   // dd($noversionFound);die();
}

?>

<header class="filament-header space-y-2 items-start justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:rtl:space-x-reverse sm:py-4">
    <div class="w-fit">
        <h1 class="filament-header-heading text-2xl font-bold tracking-tight w-full">
            {{ $plant->name }}
        </h1>
    </div>
    @if(!empty($noversionFound))
        @foreach ($noversionFound as $upload)
                @if ($upload['thing_type'] == 'icud' )
                    
                    @include('filament.resources.plant-resource.pages.components.version-notification',['version' => $upload['version'], 'thing_type' => $upload['thing_type']] )
                @endif
                @if ($upload['thing_type'] == 'hufd')
                    @include('filament.resources.plant-resource.pages.components.version-notification',['version' => $upload['version'], 'thing_type' => $upload['thing_type']] )
                   
                @endif
                @if ($upload['thing_type'] == 'uicd')
                    @include('filament.resources.plant-resource.pages.components.version-notification',['version' => $upload['version'], 'thing_type' => $upload['thing_type']] )
                @endif
        @endforeach
    @else
        
        @include('filament.resources.plant-resource.pages.components.uptodate-notification')

    @endif

    @if(!empty($records))
        @foreach($records as $record)
            @if(in_array($record['status'], ['Queued', 'IN_PROGRESS']))
                @include('filament.resources.plant-resource.pages.components.in_progress_notification',['status' => $record['status'], 'thing_type' => $record['thing_type']] )
            @endif
        @endforeach  
    @endif

   
</header>
