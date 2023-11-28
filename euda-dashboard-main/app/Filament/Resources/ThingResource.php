<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ThingResource\Pages;
use App\Filament\Resources\ThingResource\RelationManagers;
use App\Models\Thing;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Input;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Aws\Iot\IotClient;
use Aws\S3\S3Client;
use App\Models\Plant;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\Components\DownloadCertificatesButton;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class ThingResource extends Resource
{
    protected static ?string $model = Thing::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {

        $plantOptions = [];
        $iotClient = new IotClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
        $plants = Plant::all();
        $plantOptions = $plants->pluck('name', 'plant_id')->toArray();
       
    
        try {
            $things = $iotClient->listThings([]);
            $thingTypes = [];
            foreach ($things['things'] as $thing) {
                $thingName = $thing['thingName'];
                $thingTypeName = $thing['thingTypeName'];
                $thingAttributes = $iotClient->describeThing(['thingName' => $thingName]);
                $attributes = $thingAttributes['attributes'];
                if (isset($thingTypeName)) {
                    $thingTypes[$thingTypeName] = $thingTypeName;
                }   
            }
      
            
          
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('AWS IoT Error: ' . $e->getMessage());
            // You can also throw the exception here for further handling
            // throw $e;
        }
        return $form
        ->schema([
            Section::make('Technical data')->schema([
                Group::make()->schema([
                        TextInput::make('thing_name')->label('Thing Name')->required()->reactive(),
                        Forms\Components\Select::make('plantId')->label('Plants')
                        ->options($plantOptions),

                        Select::make('thing_type')->label('Thing Type')->options($thingTypes)->required(),
                        Select::make('user_id')
                        ->label('Owner')
                        ->options(User::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->default(Auth::id())
                        ->disabled(),
                    
                       
                ])->columns(2),
                
            ]),
    
        ]);
    
    }

    public static function table(Table $table): Table
    {
        $action = Action::make('Download')
        ->url(fn (Thing $record): string => route('filament.resources.things.download', $record))
        ->icon('heroicon-o-download');
    
        $actions[] = $action->button();
        if(Auth::user()->is_admin || Auth::user()->is_technician)
            $actions[] = Tables\Actions\EditAction::make();
        return $table
            ->columns([
                TextColumn::make('thing_name')->label('Thing Name')->sortable()->searchable(),
                TextColumn::make('thing_type')->label('Thing Type')->sortable()->searchable(),
                TextColumn::make('plant.name')->label('Plant Name')->sortable()->searchable(), 
                TextColumn::make('created_at')->label('Created At')->sortable()->searchable(), 
                TextColumn::make('owner.name')->label('Created By')->sortable()->searchable(), 
   
            ])
            ->filters([
                //
            ])
            ->actions($actions)
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                ->before(function ($records) {
                  static::deleteCloudRecord($records);
                }),
            ]);
    }

    protected static function deleteCloudRecord($records)
    {

    
        $iotClient = new IotClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        foreach ($records as $record) {
            $thingType = strtolower($record['thing_type']);
            $plantId = $record['plantId'];
            //dd($thingType, $plantId, $record['job_id']);die();
            if($record['status' != "CANCELED"]){
                $iotClient->cancelJob([
                    'jobId' => $record['job_id'],
                ]);
            }
           
            $iotClient->deleteJob([
                'jobId' => $record['job_id'],
            ]);
        
        }
    }

    // public function downloadAction(Thing $record)
    // {

    //    // dd($record);die();
    //     dd($record);die();
    //     // if (Storage::disk('public')->exists($filePath)) {
    //     //     // Provide a proper response to initiate the file download
    //     //     return response()->download(Storage::disk('public')->path($filePath), $record->thing_name . '-certificates.zip');
    //     // } else {
    //     //     // Handle the case where the file does not exist
    //     //     return response()->json(['error' => 'File not found'], 404);
    //     // }
    //     // $filePath = str_replace('/', '\\', $record->certificates_file_path);
    //     //dd($filePath);die();
    //     //$filePath = realpath($filePath);

    //     //dd($filePath);die();

    //     //dd($filePath);die();
    //     // Check if the file exists
    //     // if (file_exists($filePath)) {
    //     //     // Provide a proper response to initiate the file download
    //     //     return response()->download($filePath, $record->thing_name);
    //     // } else {
    //     //     // Handle the case where the file does not exist
    //     //     return response()->json(['error' => 'File not found'], 404);
    //     // }
    // }
   

    public function downloadAction(Thing $record)
    {
        // Use the stored relative file path from the database
        $relativePath = $record->file_name;
    
        // Use the Storage facade to generate the correct file path
        $filePath = Storage::disk('public')->path($relativePath);
    
        // Check if the file exists using the Storage facade
        if (file_exists($filePath)) {
            // Return the file as a response for download
            return response()->download($filePath);
        } else {
            // If the file is not found, return a JSON response with a 404 status
            return response()->json(['error' => 'File not found'], 404);
        }
    }
    
    
    


    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThings::route('/'),
            'create' => Pages\CreateThing::route('/create'),
            'edit' => Pages\EditThing::route('/{record}/edit'),
           
           
            
        ];
    }  
    
    public static function canViewAny(): bool
    {
        return Auth::user()->is_admin;
    }
}
