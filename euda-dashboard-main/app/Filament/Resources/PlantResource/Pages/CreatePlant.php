<?php

namespace App\Filament\Resources\PlantResource\Pages;

use App\Filament\Resources\PlantResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePlant extends CreateRecord
{
    protected static string $resource = PlantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $countryTelephoneCodes = [
            'US' => '01',
            'CA' => '01',
            'UK' => '044',
            'IT' => '039',
        ];
        $plantCode = rand(100, 999);
        $productCode = rand(1000, 9999);
        $data['plant_id'] = $countryTelephoneCodes[$data['country_code']] . '-'. $data['cap'] . '-' . $plantCode . '-'. $productCode;
        return $data;
    }

   
    public static function canViewAny(): bool
    {
        return Auth::user()->is_admin;
    }
}
