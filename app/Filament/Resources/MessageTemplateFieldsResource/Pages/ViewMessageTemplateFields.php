<?php

namespace App\Filament\Resources\MessageTemplateFieldsResource\Pages;

use App\Filament\Resources\MessageTemplateFieldsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMessageTemplateFields extends ViewRecord
{
    protected static string $resource = MessageTemplateFieldsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
