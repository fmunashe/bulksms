<?php

namespace App\Filament\Resources\PricePlanTypeResource\Pages;

use App\Filament\Resources\PricePlanTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListPricePlanTypes extends ListRecords
{
    protected static string $resource = PricePlanTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->askForFilename()
                        ->askForWriterType()
                        ->withColumns([
                            Column::make('created_at'),
                            Column::make('updated_at'),
                        ])
                ]),
        ];
    }
}
