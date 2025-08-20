<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    protected $webPushService;

    public function __construct(WebPushService $webPushService)
    {
        $this->webPushService = $webPushService;
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        // Tetap mencoba mendapatkan user ID jika ada yang login, jika tidak, akan menjadi null.
        // Ini memungkinkan pengguna yang login tetap terasosiasi dengan subscription mereka.
        $userId = Auth::id();

        $this->webPushService->storeSubscription(
            $request->endpoint,
            $request->input('keys.p256dh'),
            $request->input('keys.auth'),
            $userId // Akan menjadi null jika tidak ada user yang login
        );

        return response()->json(['message' => 'Subscription saved successfully']);
    }

    public function sendTestNotification()
    {
        $payload = [
            'title' => 'Test Notification',
            'body' => 'This is a test push notification from the server!',
            'icon' => '/favicon.png',
            'url' => url('/'),
        ];

        $results = $this->webPushService->sendToAll($payload);

        return response()->json($results);
    }
}
