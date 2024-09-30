<?php

namespace App\Filament\Resources\PricePlanTypeResource\Pages;

use App\Filament\Resources\PricePlanTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPricePlanType extends ViewRecord
{
    protected static string $resource = PricePlanTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
