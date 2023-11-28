<div class="relative flex items-center bg-red-100 px-4 py-1 rounded-md text-sm font-semibold text-red-800 w-fit">
        <span class="relative flex h-3 w-3 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        </span>
        Software Update is Available {{$thing_type}} with version {{ $version }}
            @if($admin)
            <a href="{{ url('/admin/jobs/create') }}" class="bg-green-100 px-4 py-1 rounded-md text-sm font-semibold text-green-800 w-fit">
                Update
            </a>
            @endif
           
 </div>