<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource\RelationManagers;
use App\Models\Merchant;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    public static function form(Form $form): Form
    {
        $adminOptions = Merchant::query()->pluck('trade_name', 'id')->toArray();
        $userOptions = [Auth::user()->merchant->id => Auth::user()->merchant->trade_name];
        return $form
            ->schema([
                Forms\Components\Select::make('merchant_id')
                    ->relationship('merchant', 'trade_name')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->options([
                        Auth::user()->hasRole("Admin") ? $adminOptions : $userOptions
                    ])
                    ->default(Auth::user()->merchant->id),
                Forms\Components\TextInput::make('recipient')
                    ->label('Recipient (Enter country code without 00 or +)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('text_message')
                    ->required()
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('recipient')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'Undelivered',
                        'warning' => 'Pending',
                        'success' => 'Delivered',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Sent')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('text_message')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('part_count')
                    ->searchable(),
                Tables\Columns\TextColumn::make('character_count')
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Delivery Status')
                    ->multiple()
                    ->options([
                        'Pending' => 'Pending',
                        'Delivered' => 'Delivered',
                        'Undelivered' => 'Undelivered'
                    ]),
                DateRangeFilter::make('created_at')
                    ->label('Date Sent')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'view' => Pages\ViewMessage::route('/{record}'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
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
        return 'SMS';
    }
}
