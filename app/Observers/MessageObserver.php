<?php

namespace App\Observers;

use App\Jobs\SendSMS;
use App\Models\Message;

class MessageObserver
{

    public function created(Message $message): void
    {
        dispatch(new SendSMS($message->recipient, $message->text_message, $message));
        $partCount = 1;
        $characterCount = strlen($message->text_message);
        if ($characterCount > 160 && $characterCount < 320) {
            $partCount = 2;
        }

        $subscription = $message->merchant->subscriptions->first();
        $message->merchant->subscriptions->first->update([
            'account_balance' => $subscription->account_balance - $partCount
        ]);
        $message->update([
            'part_count' => $partCount,
            'character_count' => $characterCount
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
