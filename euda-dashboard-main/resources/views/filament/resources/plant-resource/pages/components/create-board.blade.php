<x-filament::page>
    <div class="my-custom-component">
        <!-- Access $record directly in your view -->
        <p>Plant ID: {{ $record->id }}</p>
        <p>Plant Name: {{ $record->name }}</p>
        <!-- ... other content using $record -->
    </div>
</x-filament::page>
