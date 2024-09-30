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
        $url = env('EASY_SEND_BASE_URL');
        $apiKey = env('API_KEY');
        $recipient = $this->recipient;
        $text = $this->text;
        $data = [
            "from" => env('FROM'),
            "to" => "$recipient",
            "text" => "$text",
            "type" => env('MESSAGE_TYPE')
        ];

        $response = Http::withHeaders([
            'apikey' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, $data);

        Log::info("Data is ", $data);
        Log::info("response is ", [$response]);
        if ($response->successful()) {
            $responseData = json_decode($response->getBody(), true);
            Log::info("successful response ok", [$responseData['messageIds'][0]]);
            if (str_contains($responseData['messageIds'][0], "OK")) {
                $this->message->update([
                    'status' => Message::MESSAGE_STATUS_SELECT['Delivered']
                ]);
            }
        }
    }
}
