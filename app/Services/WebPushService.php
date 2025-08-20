<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    protected $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT', 'mailto:avatar.hydrowind@gmail.com'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];
        $this->webPush = new WebPush($auth);
    }

    public function storeSubscription($endpoint, $publicKey, $authToken, $userId = null)
    {
        return PushSubscription::updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'user_id' => $userId,
            ]
        );
    }

    /**
     * Mengirim notifikasi ke semua subscriber secara efisien.
     *
     * @param array $payload
     * @return array
     */
    public function sendToAll(array $payload)
    {
        $subscriptions = PushSubscription::all();
        $payloadJson = json_encode($payload);
        $results = ['success_count' => 0, 'failure_count' => 0, 'expired_count' => 0];

        foreach ($subscriptions as $subscription) {
            $this->webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                ]),
                $payloadJson
            );
        }

        /** @var \Minishlink\WebPush\MessageSentReport $report */
        foreach ($this->webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $results['success_count']++;
            } else {
                $results['failure_count']++;
                Log::error("Push notification failed for endpoint: {$report->getEndpoint()}. Reason: {$report->getReason()}");

                // **PENTING**: Hapus langganan yang sudah tidak valid (expired).
                if ($report->isSubscriptionExpired()) {
                    $results['expired_count']++;
                    PushSubscription::where('endpoint', $report->getEndpoint())->delete();
                }
            }
        }

        return $results;
    }
}
