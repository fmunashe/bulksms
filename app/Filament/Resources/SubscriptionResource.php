<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Merchant;
use App\Models\Subscription;
use Carbon\Carbon;
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

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('merchant_id')
                    ->label('Merchant')
                    ->required()
                    ->searchable()
                    ->options(Merchant::query()->pluck('trade_name', 'id')->toArray())
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        if ($get('merchant_id')) {
                            $merchant = Merchant::query()->find($state);
                            if ($merchant) {
                                $set('total_messages', $merchant->pricePlan->total_sms);
                                $set('account_balance', $merchant->pricePlan->total_sms);
                                $set('sms_price', $merchant->pricePlan->price_per_sms);
                            };
                        }
                    }),
                Forms\Components\TextInput::make('total_messages')
                    ->required()
                    ->numeric()
                    ->live(),
                Forms\Components\TextInput::make('account_balance')
                    ->required()
                    ->numeric()
                    ->live(),
                Forms\Components\TextInput::make('sms_price')
                    ->required()
                    ->numeric()
                    ->live(),
                Forms\Components\Select::make('status')
                    ->searchable()
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                        'Expired' => 'Expired',
                    ])
                    ->default('Active'),
                Forms\Components\DateTimePicker::make('effective_date')
                    ->default(Carbon::now()),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant.trade_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_messages')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sms_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('effective_date')
                    ->dateTime()
                    ->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (Auth::user()->hasRole("Admin")) {
            return $query;
        }
        return $query->where('merchant_id', '=', Auth::user()->merchant_id);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Price Plans';
    }
}
