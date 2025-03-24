<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsAppService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendBulkMessages(array $recipients, $message)
    {
        foreach ($recipients as $to) {
            $this->twilio->messages->create(
                'whatsapp:' . $to,
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'),
                    'body' => $message,
                ]
            );
        }
    }
}
