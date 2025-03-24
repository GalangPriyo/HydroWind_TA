<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;

class NotificationController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function sendBulkAlert()
    {
        // Daftar nomor tujuan (format internasional)
        $recipients = [
            '+6287731398753',
            '+6285712690978',
            '+6282131179365',
        ];

        $message = '🚨 Peringatan: Status Bencana Darurat! Harap waspada.';

        $this->whatsappService->sendBulkMessages($recipients, $message);

        return response()->json(['message' => 'Notifikasi terkirim ke semua nomor']);
    }
}
