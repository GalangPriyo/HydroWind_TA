<template>
    <div class="p-4 bg-white rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Test Push Notification</h3>

        <div class="space-y-4">
            <!-- Status -->
            <div class="p-3 bg-gray-100 rounded">
                <p class="text-sm">
                    <strong>Status:</strong>
                    <span :class="statusClass">{{ statusText }}</span>
                </p>
            </div>

            <!-- Permission Status -->
            <div class="p-3 bg-gray-100 rounded">
                <p class="text-sm">
                    <strong>Permission:</strong>
                    <span :class="permissionClass">{{ permissionText }}</span>
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                <button
                    @click="requestPermission"
                    :disabled="loading"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50"
                >
                    Request Permission
                </button>

                <button
                    @click="subscribe"
                    :disabled="loading || !isPermissionGranted || isSubscribed"
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 disabled:opacity-50"
                >
                    Subscribe
                </button>

                <button
                    @click="sendTest"
                    :disabled="loading || !isSubscribed"
                    class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 disabled:opacity-50"
                >
                    Send Test
                </button>

                <button
                    @click="unsubscribe"
                    :disabled="loading || !isSubscribed"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 disabled:opacity-50"
                >
                    Unsubscribe
                </button>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center">
                <div
                    class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-gray-900"
                ></div>
                Processing...
            </div>

            <!-- Results -->
            <div
                v-if="result"
                class="p-3 rounded"
                :class="result.success ? 'bg-green-100' : 'bg-red-100'"
            >
                <p class="text-sm font-medium">{{ result.message }}</p>
                <pre
                    v-if="result.data"
                    class="mt-2 text-xs bg-gray-800 text-white p-2 rounded overflow-auto"
                    >{{ JSON.stringify(result.data, null, 2) }}</pre
                >
            </div>
        </div>
    </div>
</template>

<script>
import pushNotificationService from "@/services/pushNotification.js";

export default {
    name: "TestPushNotification",
    data() {
        return {
            loading: false,
            isSupported: false,
            isPermissionGranted: false,
            isSubscribed: false,
            subscription: null,
            result: null,
        };
    },
    computed: {
        statusText() {
            if (!this.isSupported) return "Not Supported";
            if (this.isSubscribed) return "Subscribed";
            return "Not Subscribed";
        },
        statusClass() {
            if (!this.isSupported) return "text-red-600";
            if (this.isSubscribed) return "text-green-600";
            return "text-yellow-600";
        },
        permissionText() {
            if (!this.isSupported) return "Not Supported";
            return this.isPermissionGranted ? "Granted" : "Denied/Not Asked";
        },
        permissionClass() {
            if (!this.isSupported) return "text-red-600";
            return this.isPermissionGranted
                ? "text-green-600"
                : "text-yellow-600";
        },
    },
    async mounted() {
        await this.initialize();
    },
    methods: {
        async initialize() {
            this.isSupported = pushNotificationService.isPushSupported();

            if (!this.isSupported) {
                this.result = {
                    success: false,
                    message: "Push notifications not supported in this browser",
                };
                return;
            }

            try {
                await pushNotificationService.registerServiceWorker();

                this.isPermissionGranted =
                    Notification.permission === "granted";

                this.isSubscribed =
                    await pushNotificationService.isSubscribed();
                if (this.isSubscribed) {
                    this.subscription =
                        await pushNotificationService.getCurrentSubscription();
                }
            } catch (error) {
                console.error("Initialization error:", error);
                this.result = {
                    success: false,
                    message:
                        "Error initializing push notifications: " +
                        error.message,
                };
            }
        },

        async requestPermission() {
            this.loading = true;
            this.result = null;

            try {
                const permission =
                    await pushNotificationService.requestPermission();
                this.isPermissionGranted = permission === "granted";

                this.result = {
                    success: permission === "granted",
                    message:
                        permission === "granted"
                            ? "Permission granted"
                            : "Permission denied",
                };
            } catch (error) {
                this.result = {
                    success: false,
                    message: "Error requesting permission: " + error.message,
                };
            } finally {
                this.loading = false;
            }
        },

        async subscribe() {
            if (!this.isPermissionGranted) {
                this.result = {
                    success: false,
                    message: "Notification permission is not granted.",
                };
                return;
            }
            this.loading = true;
            this.result = null;

            try {
                const subscription =
                    await pushNotificationService.subscribeToPush();
                // Request ke /api/push/subscribe tetap sama karena publik
                await pushNotificationService.sendSubscriptionToServer(
                    subscription
                );

                this.isSubscribed = true;
                this.subscription = subscription;

                this.result = {
                    success: true,
                    message: "Successfully subscribed to push notifications",
                    data: subscription,
                };
            } catch (error) {
                this.result = {
                    success: false,
                    message: "Error subscribing: " + error.message,
                };
                console.error("Full subscription error:", error);
            } finally {
                this.loading = false;
            }
        },

        async unsubscribe() {
            this.loading = true;
            this.result = null;

            try {
                await pushNotificationService.unsubscribeFromPush();
                this.isSubscribed = false;
                this.subscription = null;

                this.result = {
                    success: true,
                    message:
                        "Successfully unsubscribed from push notifications",
                };
            } catch (error) {
                this.result = {
                    success: false,
                    message: "Error unsubscribing: " + error.message,
                };
            } finally {
                this.loading = false;
            }
        },

        async sendTest() {
            this.loading = true;
            this.result = null;

            try {
                // Ganti URL ke /push/test (tanpa /api) dan hapus header Authorization
                const response = await fetch("/push/test", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        // Baris ini sekarang akan berhasil menemukan token
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        )?.content,
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "An error occurred");
                }

                this.result = {
                    success: true,
                    message: "Test notification sent successfully",
                    data: data,
                };
            } catch (error) {
                this.result = {
                    success: false,
                    message: "Error sending test: " + error.message,
                };
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
