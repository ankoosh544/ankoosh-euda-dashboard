<?php

namespace App\Filament\Resources\ThingResource\Pages;

use App\Filament\Resources\ThingResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\DatePicker;

class ListThings extends ListRecords
{
    protected static string $resource = ThingResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // DatePicker::make('date')
            //         ->afterStateUpdated(function (Closure $set, $state) {
            //             $this->selectedDate = Carbon::parse($state)->format("Y-m-d");
            //             $this->goToDate();
            //         }),
        ];
    }



    public function goToDate() {
        $this->initVariable();
    }

    public function initVariable(){
        dd("coming to init");die();
    }
}
