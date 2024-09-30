<?php

namespace App\Filament\Resources\PricePlanResource\Pages;

use App\Filament\Resources\PricePlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPricePlan extends ViewRecord
{
    protected static string $resource = PricePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
