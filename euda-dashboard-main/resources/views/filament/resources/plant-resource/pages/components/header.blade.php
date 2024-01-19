<?php
use App\Models\Job;
use Illuminate\Support\Facades\Cache;

$latestUploads = Cache::get('latestUploads', []);

$versionInstalled = Cache::get('versionInstalled', []);

//dd($latestUploads);die();

$records = [];
$noversionFound = [];
$versions =[];
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
            $noversionFound[] = $latestUpload;
        }
}
if (Auth::check() && Auth::user()->is_admin) {
    $admin = true;
} else {
    $admin = false;

}
//dd($records, $noversionFound, $latestUpload['version'], $latestUpload['thing_type']);die();
?>

<header class="filament-header space-y-2 items-start justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:rtl:space-x-reverse sm:py-4">
    <div class="w-fit">
        <h1 class="filament-header-heading text-2xl font-bold tracking-tight w-full">
            {{ $plant->name }}
        </h1>
    </div>
    @if(!empty($records))
        @foreach($records as $record)
            @if(in_array($record['status'], ['Queued', 'IN_PROGRESS']))
                @include('filament.resources.plant-resource.pages.components.in_progress_notification',['status' => $record['status'], 'thing_type' => $record['thing_type']] )
            @endif
        @endforeach 
    @endif     
    
    @if(!empty($noversionFound))
        @foreach ($noversionFound as $upload)   
            @include('filament.resources.plant-resource.pages.components.version-notification',['version' => $upload['version'], 'thing_type' => $upload['thing_type'],'admin' => $admin] )
        @endforeach
    @else
        @include('filament.resources.plant-resource.pages.components.uptodate-notification')
    @endif
</header>

