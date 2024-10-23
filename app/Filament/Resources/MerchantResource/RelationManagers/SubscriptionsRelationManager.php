<?php

namespace App\Filament\Resources\MerchantResource\RelationManagers;

use App\Models\Merchant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('merchant_id')
                    ->label('Merchant')
                    ->required()
                    ->searchable()
                    ->options(Merchant::query()->pluck('trade_name', 'id')->toArray())
                    ->live()
                    ->unique('subscriptions','merchant_id',ignoreRecord: true)
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
                    ->numeric()
                    ->live(),
                Forms\Components\TextInput::make('account_balance')
                    ->numeric()
                    ->live(),
                Forms\Components\TextInput::make('sms_price')
                    ->numeric()
                    ->live()
                    ->visible(Auth::user()->hasRole("Admin")),
                Forms\Components\Select::make('status')
                    ->searchable()
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                        'Expired' => 'Expired',
                    ])
                    ->default('Active'),
                Forms\Components\DateTimePicker::make('effective_date')
                    ->default(Carbon::now())
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('total_messages')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sms_price')
                    ->numeric()
                    ->sortable()
                    ->visible(Auth::user()->hasRole("Admin"))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('effective_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->before(function ( $data){
//                    Log::info("record is ",[$this->ownerRecord]);
//                    Log::info("data is ",[$data]);
////                    $currentSubscription = $s
                }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
