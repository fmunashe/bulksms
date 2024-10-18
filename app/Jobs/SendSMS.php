<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSMS implements ShouldQueue
{
    public $recipient;
    public $text;
    public Message $message;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct($recipient, $message, $messageObject)
    {
        $this->recipient = $recipient;
        $this->text = $message;
        $this->message = $messageObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $infoBipUrl = env('INFO_BIP_BASE_URL') . '/sms/2/text/advanced';
        $infoBipApiKey = env('INFO_BIP_API_KEY');
        $recipient = $this->recipient;
        $text = $this->text;

        $infoBipData = [
            'messages' => [
                [
                    'from' => env('FROM'),
                    'destinations' => [
                        [
                            'to' => "$recipient",
                        ],
                    ],
                    'text' => $text,
                ],
            ],
        ];

        Log::info("message is ", $infoBipData);

        $response = Http::withHeaders([
            'Authorization' => "App $infoBipApiKey",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($infoBipUrl, $infoBipData);

        Log::info("response is ", [$response]);
        if ($response->successful()) {
            $this->message->update([
                'status' => Message::MESSAGE_STATUS_SELECT['Delivered']
            ]);
        }
    }
}
