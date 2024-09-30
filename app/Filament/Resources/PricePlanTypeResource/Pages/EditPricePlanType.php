<?php

namespace App\Filament\Resources\PricePlanTypeResource\Pages;

use App\Filament\Resources\PricePlanTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPricePlanType extends EditRecord
{
    protected static string $resource = PricePlanTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
