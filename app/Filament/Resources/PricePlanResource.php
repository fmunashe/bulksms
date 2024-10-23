<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricePlanResource\Pages;
use App\Filament\Resources\PricePlanResource\RelationManagers;
use App\Models\PricePlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PricePlanResource extends Resource
{
    protected static ?string $model = PricePlan::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('price_plan_type_id')
                    ->relationship('pricePlanType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
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

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pricePlanType.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_plan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_sms')
                    ->numeric()
                    ->sortable()
                    ->visible(Auth::user()->hasRole("Admin")),
                Tables\Columns\TextColumn::make('total_sms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable()
                    ->visible(Auth::user()->hasRole("Admin")),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->askForFilename()
                                ->askForWriterType()
                        ])
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MerchantsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricePlans::route('/'),
            'create' => Pages\CreatePricePlan::route('/create'),
            'view' => Pages\ViewPricePlan::route('/{record}'),
            'edit' => Pages\EditPricePlan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Price Plans';
    }
}
