<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $token;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN'); // pastikan token disimpan di .env
    }

    public function sendMessage(array $targets, string $message)
    {
        $results = [];

        foreach ($targets as $target) {
            $response = Http::asForm()->withHeaders([
                'Authorization' => $this->token
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'delay' => 1,
                'countryCode' => '62',
            ]);

            $results[$target] = $response->json();
        }

        return $results;
    }
}
