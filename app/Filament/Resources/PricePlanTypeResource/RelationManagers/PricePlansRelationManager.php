<?php

namespace App\Filament\Resources\PricePlanTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PricePlansRelationManager extends RelationManager
{
    protected static string $relationship = 'pricePlans';
    protected static ?string $inverseRelationship = 'pricePlanType';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('price_plan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price_per_sms')
                    ->required()
                    ->numeric()
                    ->default(0.0000)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        if ($get('price_per_sms')
                            && $get('total_sms')
                        ) {
                            $set('total_price', $get('price_per_sms') * $get('total_sms'));
                        } else {
                            $set('total_price', 0.0000);
                        }
                    }),
                Forms\Components\TextInput::make('total_sms')
                    ->required()
                    ->numeric()
                    ->default(0.0000)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        if ($get('price_per_sms')
                            && $get('total_sms')
                        ) {
                            $set('total_price', $get('price_per_sms') * $get('total_sms'));
                        } else {
                            $set('total_price', 0.0000);
                        }
                    }),
                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->default(0.0000)
                    ->live()
                    ->readOnly(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('price_plan')
            ->columns([
                Tables\Columns\TextColumn::make('pricePlanType.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_plan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_sms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable()
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make()
                    ->multiple()
                    ->preloadRecordSelect()
                    ->recordSelect(function ($select) {
                        return $select->placeholder("Select Price Plan");
                    })
                ->modalWidth('3xl'),
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->askForFilename()
                            ->askForWriterType()
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
