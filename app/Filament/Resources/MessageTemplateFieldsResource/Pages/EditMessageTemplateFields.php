<?php

namespace App\Filament\Resources\MessageTemplateFieldsResource\Pages;

use App\Filament\Resources\MessageTemplateFieldsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMessageTemplateFields extends EditRecord
{
    protected static string $resource = MessageTemplateFieldsResource::class;

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
