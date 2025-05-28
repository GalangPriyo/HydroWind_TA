<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $token;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN'); // simpan di .env
    }

    public function sendMessage(array $targets, string $message)
    {
        $targetString = implode(',', $targets); // format: 628xxxx,628yyyy

        $response = Http::asForm()->withHeaders([
            'Authorization' => $this->token
        ])->post('https://api.fonnte.com/send', [
            'target' => $targetString,
            'message' => $message,
            'delay' => 1,
            'countryCode' => '62',
        ]);

        return $response->json();
    }
}
