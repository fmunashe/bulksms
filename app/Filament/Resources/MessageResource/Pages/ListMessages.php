<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use App\Models\MessageTemplate;
use App\traits\BulkSMSProcessor;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListMessages extends ListRecords
{
    use BulkSMSProcessor;

    protected static string $resource = MessageResource::class;

    protected ?string $subheading = 'Manage and track SMS messages sent to recipients. View delivery status, message content, and export message data.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('commaSeperated')
                ->label('Comma Seperated SMS')
                ->modalContent(new HtmlString('
                    <ol>
                        <li>Enter recipients starting with country code</li>
                        <li>Separate multiple numbers with commas</li>
                        <li>Example: 263778234258,263778234259,263778234260</li>
                        <li>Ensure sufficient credits before sending</li>
                        <li>Part count for a message with special characters such as /:;\.,$%^&*#@ is 70 characters per message</li>
                    </ol>
                '))
                ->action(function ($record, array $data): void {
                    if ($data['confirm']) {
                        $recipients = explode(',',$data['recipients'] );
                        $user = Auth::user();
                        $subscription = $user->merchant->subscriptions->first() ?? null;
                        if ($subscription) {
                            if ($subscription->account_balance < sizeof($recipients)) {
                                Notification::make()
                                    ->title('Insufficient Credits')
                                    ->body('You do not have enough credits to allow this action!!. Current credits are ' . $subscription->account_balance . ' you need at least ' . sizeof($recipients))
                                    ->danger()
                                    ->send()
                                    ->persistent();
                                $this->halt();
                            }
                        } else {
                            Notification::make()
                                ->title('No price plan found')
                                ->body('Your account has no price plan or subscription associated with it to allow this action')
                                ->danger()
                                ->send()
                                ->persistent();
                            $this->halt();
                        }
                        $response = $this->commaSeperatedSms($data['message'], $recipients);

                        if ($response->status() == 200) {
                            Notification::make()
                                ->title('Success')
                                ->body($response->getData())
                                ->success()
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Error')
                                ->body($response->getData())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    } else {
                        Notification::make()
                            ->title('Error')
                            ->body('Please confirm comma seperated sms sending before proceeding')
                            ->danger()
                            ->send();
                    }
                })
                ->form([
                    TextInput::make('recipients')
                        ->label('Recipients')
                        ->required(),
                    Textarea::make('message')
                        ->label('Message')
                        ->required(),
                    Toggle::make('confirm')
                        ->label("Confirm Sending Bulk SMS")
                        ->required()
                ]),
            Actions\Action::make('bulkSMS')
                ->label('Bulk SMS')
                ->tooltip('Upload an Excel file with recipients and send bulk SMS using templates')
                ->modalContent(new HtmlString('
                    <ol>
                        <li>Enter recipients starting with country code</li>
                        <li>Ensure sufficient credits before sending</li>
                        <li>Part count for a message with special characters such as /:;\.,$%^&*#@ is 70 characters per message</li>
                    </ol>
                '))
                ->action(function ($record, array $data): void {
                    if ($data['confirm']) {
                        $user = Auth::user();
                        $subscription = $user->merchant->subscriptions->first() ?? null;
                        if ($subscription) {
                            if ($subscription->account_balance < 1) {
                                Notification::make()
                                    ->title('Insufficient Credits')
                                    ->body('You do not have enough credits to allow this action!!. Current credits are ' . $subscription->account_balance . ' you need at least 1')
                                    ->danger()
                                    ->send()
                                    ->persistent();
                                $this->halt();
                            }
                        } else {
                            Notification::make()
                                ->title('No price plan found')
                                ->body('Your account has no price plan or subscription associated with it to allow this action')
                                ->danger()
                                ->send()
                                ->persistent();
                            $this->halt();
                        }


                        $template = MessageTemplate::query()->findOrFail($data['template']);
                        $filePath = $data['fileUpload'];
                        $response = $this->processUploadFile($filePath, $template, $user);

                        if ($response->status() == 200) {
                            Notification::make()
                                ->title('Success')
                                ->body($response->getData())
                                ->success()
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Error')
                                ->body($response->getData())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    } else {
                        Notification::make()
                            ->title('Error')
                            ->body('Please confirm bulk sms sending before proceeding')
                            ->danger()
                            ->send();
                    }
                })
                ->form([
                    FileUpload::make('fileUpload')
                        ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required()
                        ->label('Upload Recipients File'),
                    Select::make('template')
                        ->label('Template')
                        ->searchable()
                        ->required()
                        ->options(MessageTemplate::query()->pluck('name', 'id')->toArray()),
                    Toggle::make('confirm')
                        ->label("Confirm Sending Bulk SMS")
                        ->required()
                ]),
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

    private function getData($year, $month)
    {
        $cdr_url = Config('app.primary_cdr_url');
        try {
            $response = Http::asForm()->post($cdr_url, [
                'year' => $year,
                'month' => $month
            ])->body();


            $data = json_decode($response);

            if ($data->result != 'No table with information could be found for the specified period' || $data->result != 'No records were found for the specified period') {
                $this->CreateCDR($data);
                return 'CDR Data Successfully Synchronized';
            } else {
                return $data->result;
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
