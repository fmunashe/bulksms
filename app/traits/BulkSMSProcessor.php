<?php

namespace app\traits;

use App\Imports\DataImport;
use App\Models\Message;
use App\Models\MessageTemplate;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait BulkSMSProcessor
{
    public function processUploadFile($uploadedFile, MessageTemplate $template, Authenticatable $user): JsonResponse
    {
        try {
            $data = Excel::toArray(new DataImport, storage_path('app/public/' . $uploadedFile));

            $vars = $template->messageTemplateFields->pluck('field_name')->toArray();
            $subscription = $user->merchant->subscriptions->first();
            $total = sizeof($data[0])-1;
            if ($subscription->account_balance < $total) {
                throw new \Exception("You do not have enough credits to allow this action!!. Current credits are $subscription->account_balance you need at least $total");
//                Notification::make()
//                    ->title('Insufficient Credits')
//                    ->body("You do not have enough credits to allow this action!!. Current credits are $subscription->account_balance you need at least $total")
//                    ->danger()
//                    ->send();
            }
            foreach ($data[0] as $index => $row) {

                if ($index == 0) {
                    continue;
                }
                $replacements = [];

                foreach ($vars as $key => $var) {
                    $replacements[] = $row[$key];
                }
                array_shift($replacements);
                $filledTemplate = str_replace(array_map(fn($var) => "\$$var", $vars), $replacements, $template->message);
                Message::query()->create([
                    'merchant_id' => Auth::user()->merchant_id,
                    'recipient' => $row[0],
                    'text_message' => $filledTemplate
                ]);
            }
            return response()->json('File Successfully Uploaded And Processed', 200);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 400);
        }
    }
}
