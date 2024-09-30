<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageTemplateResource\Pages;
use App\Filament\Resources\MessageTemplateResource\RelationManagers;
use App\Models\Merchant;
use App\Models\MessageTemplate;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    public static function form(Form $form): Form
    {
        $adminOptions = Merchant::query()->pluck('trade_name', 'id')->toArray();
        $merchantOptions = [Auth::user()->merchant->id => Auth::user()->merchant->trade_name];
        return $form
            ->schema([
                Forms\Components\Select::make('merchant_id')
                    ->relationship('merchant', 'trade_name')
                    ->label('Merchant')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->options(Auth::user()->hasRole('Admin') ? $adminOptions : $merchantOptions),
                Forms\Components\TextInput::make('name')
                    ->label('Template Name')
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->label('Message (Place holders should start with $ e.g Dear $name, your subscription of $subscription expires on $expiryDate)')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('Dear $name, your subscription of $subscription expires on $expiryDate'),
                Repeater::make('field_name')
                    ->relationship('messageTemplateFields')
                    ->label('Template Placeholder Fields')
                    ->schema([
                        TextInput::make('field_name')->required()
                            ->label('Placeholder Field Name'),
                    ])
                    ->columnSpanFull()
                    ->visible(fn($livewire) => $livewire instanceof EditRecord)
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant.trade_name'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('message'),
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
            RelationManagers\MessageTemplateFieldsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageTemplates::route('/'),
            'create' => Pages\CreateMessageTemplate::route('/create'),
            'view' => Pages\ViewMessageTemplate::route('/{record}'),
            'edit' => Pages\EditMessageTemplate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (Auth::user()->hasRole('Admin')) {
            return $query;
        }

        return $query->where('merchant_id', '=', Auth::user()->merchant_id);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Message Templates';
    }
}
