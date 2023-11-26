<?php

namespace App\Filament\Resources\PlantResource\Pages;

use App\Filament\Resources\PlantResource;
use Filament\Resources\Pages\Page;

class CreateBoard extends Page
{
    protected static string $resource = PlantResource::class;

    protected static string $view = 'filament.resources.plant-resource.pages.components.create-board';

    public $customVariable;

    public function mount($record): void
    {
        parent::mount($record);

        // Perform additional logic if needed
        $this->customVariable = 'Custom Value';
    }
  
}
