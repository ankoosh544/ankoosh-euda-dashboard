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
            'US' => '0001',
            'CA' => '0001',
            'UK' => '0044',
            'IT' => '0039',
            'ES' => '0034',
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
