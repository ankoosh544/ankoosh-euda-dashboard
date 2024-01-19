<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlantResource\Pages;
use App\Filament\Resources\PlantResource\RelationManagers;
use App\Models\Plant;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action;
use Filament\Support\Facades\FilamentAsset;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\PlantResource\Pages\CreateBoard;




class PlantResource extends Resource
{
    protected static ?string $model = Plant::class;
    protected static ?string $navigationIcon = 'heroicon-o-office-building';
    protected static ?string $modelLabel = 'Plant';
    protected static ?string $pluralModelLabel = 'Plants list';
    protected static ?string $navigationLabel = 'Plants list';
    protected static ?string $recordTitleAttribute = 'Plants list';


    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'topic', 'datastore', 'city', 'state', 'address', 'cap'];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->name,
            'Address' => $record->getCompleteAddress(),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->is_admin) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('assigned_to', $user->id);
    }
    

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Technical data')->schema([
                Group::make()->schema([
                        TextInput::make('name')->label('Name')->required()->reactive(),
                        Select::make('owner_id')
                            ->label('Administator')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->default(Auth::id())
                            ->disabled(),
                        Select::make('assigned_to')
                            ->label('Assigned To')
                            ->options(User::where('is_technician', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Datepicker::make('schedule_date')
                            ->label('Schedule Date')
                            ->required(),   
                ])->columns(2),
            ]),
            Section::make('Address info')->schema([
                TextInput::make('address')->label('Address')->required(),
                TextInput::make('state')->label('State')->required(),
                TextInput::make('city')->label('City')->required(),
                TextInput::make('cap')->label('CAP')->required(),
                TextInput::make('country_code')->label('CountryCode')->required(),
            ])->columns(2)
           
        ]);
    
    }
    
    public static function table(Table $table): Table
    {
        $actions[] = Action::make('Open')
            ->url(fn (Plant $record): string => route('filament.resources.plants.view', $record))
            ->icon('heroicon-o-external-link');  
    
        if(Auth::user()->is_admin)
            $actions[] = Tables\Actions\EditAction::make();
    
        // Filter records based on the authenticated user's owner_id
        $ownerId = Auth::user()->id;
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->sortable()->searchable(),
                TextColumn::make('owner.name')->label('Administator')->sortable()->searchable(),
                TextColumn::make('customer.name')->label('Customer')->sortable()->searchable(),
                TextColumn::make('state')->label('State')->sortable()->searchable(),
                TextColumn::make('city')->label('City')->sortable()->searchable(),
                TextColumn::make('address')->label('Address')->sortable()->searchable(),
                TextColumn::make('cap')->label('CAP')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions($actions)
            ->bulkActions(
                Auth::user()->is_admin ?
            [Tables\Actions\DeleteBulkAction::make()] : []);
    }
    



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()->is_admin;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->is_admin;
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->is_admin;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlants::route('/'),
            'create' => Pages\CreatePlant::route('/create'),
            'edit' => Pages\EditPlant::route('/{record}/edit'),
            'view' => Pages\ViewPlant::route('/{record}/view'),
            'board' => Pages\CreateBoard::route('/{record}/create-board'),
        ];
    }

    public static function canViewAny(): bool
    {
    
        return true;
    }
    
    
}