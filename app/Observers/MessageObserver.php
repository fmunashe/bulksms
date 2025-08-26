<?php

namespace App\Observers;

use App\Jobs\SendSMS;
use App\Models\Message;

class MessageObserver
{

    public function created(Message $message): void
    {
        $partCount = 1;
        $characterCount = strlen($message->text_message);
        if ($characterCount > 160 && $characterCount < 320) {
            $partCount = 2;
        }

        $specialChars = ['/', ':', ';', '\\', '.', ',', '$', '%', '^', '&', '*', '#', '@'];
        $hasSpecialChars = false;

        foreach ($specialChars as $char) {
            if (str_contains($message->text_message, $char)) {
                $hasSpecialChars = true;
                break;
            }
        }

        if ($hasSpecialChars) {
            $partCount = ceil($characterCount / 70);
        }

        $subscription = $message->merchant->subscriptions->first();
        $message->merchant->subscriptions->first->update([
            'account_balance' => $subscription->account_balance - $partCount
        ]);

        if ($subscription->account_balance >= 0) {
            dispatch(new SendSMS($message->recipient, $message->text_message, $message));
            $message->update([
                'part_count' => $partCount,
                'character_count' => $characterCount
            ]);
        } else {
            $message->update([
                'part_count' => $partCount,
                'character_count' => $characterCount,
                'status' => Message::MESSAGE_STATUS_SELECT['InsufficientCredit']
            ]);
        }
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
