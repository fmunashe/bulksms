<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateMessage extends CreateRecord
{
    protected static string $resource = MessageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
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
        return static::getModel()::create($data);
    }
}
