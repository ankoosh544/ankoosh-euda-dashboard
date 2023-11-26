<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Filament\Resources\JobResource\RelationManagers;
use App\Models\Job;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Aws\Iot\IotClient;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Card;
use App\Models\Plant;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Auth\User;




class JobResource extends Resource
{
   
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

   
    

    public static function form(Form $form): Form
    {
        $plantOptions = [];
        $thingOptions = [];
        $fileOptions = [];
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $iotClient = new IotClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

    try {
    
        $things = $iotClient->listThings([]);

        
        $allPlantIds = [];

        foreach ($things['things'] as $thing) {
            $thingName = $thing['thingName'];

            // Fetch all attributes for the current thing
            $thingAttributes = $iotClient->describeThing(['thingName' => $thingName]);

            // Assuming 'attributes' is an array containing the attributes of the thing
            $attributes = $thingAttributes['attributes'];

            // Check if 'plantId' attribute exists for the current thing
            if (isset($attributes['plantId'])) {
                // Add the 'plantId' value to the result array only if it's not already present
                $allPlantIds[$thingName] = $attributes['plantId'];
            }
        }

        // Use array_unique to filter out duplicate 'plantId' values
        $uniquePlantIds = array_unique($allPlantIds);
        $plants = Plant::select('plant_id', 'name')->get();
        $plantOptions = $plants->pluck('name', 'plant_id')->toArray();
        $bucketName = 'otabucket001'; 
        $objects = $s3Client->listObjects([
            'Bucket' => $bucketName,
        ]);
    
        foreach ($objects['Contents'] as $object) {
            $objectKey = $object['Key'];
            $s3Uri = "s3://$bucketName/$objectKey"; 
            $fileOptions[$s3Uri] = $s3Uri; 
        }
    } catch (\Exception $e) {
        // Log the error for debugging
        \Log::error('AWS IoT Error: ' . $e->getMessage());
        // You can also throw the exception here for further handling
        // throw $e;
    }

   return $form
        ->schema([
            Forms\Components\TextInput::make('job_id')->required(),
            Forms\Components\Select::make('plantId')->label('Plants')
                ->options($plantOptions),
            Card::make()->columns(2)
            ->schema([ 
                Forms\Components\Checkbox::make('board_type_HUFD')
                    ->label('HUFD'),
                Forms\Components\Select::make('HUFD_s3_file')
                    ->options($fileOptions),
            ]),
            Card::make()->columns(2)
            ->schema([ 
                Forms\Components\Checkbox::make('board_type_ICUD')
                    ->label('ICUD'),
                Forms\Components\Select::make('ICUD_s3_file')
                    ->options($fileOptions), 
            ]),   

            Card::make()->columns(2)
            ->schema([ 
                Forms\Components\Checkbox::make('board_type_UICD')
                    ->label('UICD'),
                Forms\Components\Select::make('UICD_s3_file')
                    ->options($fileOptions),
            ]),
            Select::make('owner_id')
                        ->label('Owner')
                        ->options(User::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->default(Auth::id())
                        ->disabled(),
        
        ]);

}



    public static function table(Table $table): Table
    {
   
        return $table
            ->columns([
                TextColumn::make('job_id')->label('JobId')->sortable()->searchable(),
                TextColumn::make('thing_type')->label('ThingType')->sortable()->searchable(),
                TextColumn::make('plant.name')->label('Plant Name')->sortable()->searchable(),
                TextColumn::make('status')->label('Status')->sortable()->searchable(),
                TextColumn::make('version_number')->label('Version')->sortable()->searchable(),
                TextColumn::make('owner.name')->label('Owner')->sortable()->searchable(), 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                ->before(function ($records) {
                  static::deleteCloudRecord($records);
                }),
            ]);
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
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
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


    protected static function listJobsById($iotClient, $thingType, $plantId)
    {
        // Use the listJobs method to retrieve jobs based on thingType and plantId
        $result = $iotClient->listJobs([
            'thingTypeName' => $thingType,
            'targetSelection' => 'CONTINUOUS',
        ]);
    
        return $result['jobs'];
    }
}
