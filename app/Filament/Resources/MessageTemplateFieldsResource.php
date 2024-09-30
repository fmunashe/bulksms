<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageTemplateFieldsResource\Pages;
use App\Filament\Resources\MessageTemplateFieldsResource\RelationManagers;
use App\Models\Merchant;
use App\Models\MessageTemplate;
use App\Models\MessageTemplateFields;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class MessageTemplateFieldsResource extends Resource
{
    protected static ?string $model = MessageTemplateFields::class;

    public static function form(Form $form): Form
    {
        $adminOptions = Merchant::query()->pluck('trade_name', 'id')->toArray();
        $merchantOptions = [Auth::user()->merchant->id => Auth::user()->merchant->trade_name];

        $adminTemplateOptions = MessageTemplate::query()->pluck('name', 'id')->toArray();
        $merchantTemplateOptions = MessageTemplate::query()->where('merchant_id', '=', Auth::user()->merchant_id)->pluck('name', 'id')->toArray();
        return $form
            ->schema([
                Forms\Components\Select::make('message_template_id')
                    ->searchable()
                    ->relationship('messageTemplate', 'name')
                    ->required()
                    ->preload()
                    ->options(Auth::user()->hasRole('Admin') ? $adminTemplateOptions : $merchantTemplateOptions)
                    ->createOptionForm([
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
                        Forms\Components\TextInput::make('message')
                            ->label('Message')
                            ->required()
                    ]),
                TextInput::make('field_name')
                    ->required()
                    ->label('Field Name')

            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('messageTemplate.merchant.trade_name'),
                Tables\Columns\TextColumn::make('messageTemplate.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('field_name')
                    ->searchable(),
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
            'index' => Pages\ListMessageTemplateFields::route('/'),
            'create' => Pages\CreateMessageTemplateFields::route('/create'),
            'view' => Pages\ViewMessageTemplateFields::route('/{record}'),
            'edit' => Pages\EditMessageTemplateFields::route('/{record}/edit'),
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
        $templates = MessageTemplate::query()->where('merchant_id', '=', Auth::user()->merchant_id)->pluck('id')->toArray();

        return $query->whereIn('message_template_id', $templates);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Message Templates';
    }
}
