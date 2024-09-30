<?php

namespace App\Observers;

use App\Jobs\SendSMS;
use App\Models\Message;

class MessageObserver
{

    public function created(Message $message): void
    {
        dispatch(new SendSMS($message->recipient, $message->text_message, $message));

        $subscription = $message->merchant->subscriptions->first();
        $message->merchant->subscriptions->first->update([
            'account_balance' => $subscription->account_balance - 1
        ]);
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     */
    public function restored(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     */
    public function forceDeleted(Message $message): void
    {
        //
    }
}
