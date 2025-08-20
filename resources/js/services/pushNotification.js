// Service untuk Push Notification di Frontend
class PushNotificationService {
    constructor() {
        this.isSupported =
            "serviceWorker" in navigator && "PushManager" in window;
        this.subscription = null;
        this.vapidPublicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY;

        if (!this.vapidPublicKey) {
            console.error(
                "VAPID_PUBLIC_KEY is not defined in environment variables"
            );
        }
    }

    // Check if push notification is supported
    isPushSupported() {
        return this.isSupported;
    }

    // Register service worker
    async registerServiceWorker() {
        try {
            const registration = await navigator.serviceWorker.register(
                "/sw.js", // Pastikan path ini benar
                { scope: "/" }
            );
            return registration;
        } catch (error) {
            console.error("Registration failed:", error);
            throw error;
        }
    }

    // Request notification permission
    async requestPermission() {
        if (!this.isSupported) {
            return "unsupported";
        }

        const permission = await Notification.requestPermission();
        return permission;
    }

    // Subscribe to push notifications
    async subscribeToPush() {
        if (!this.isSupported) {
            throw new Error("Push notifications not supported");
        }

        if (!this.vapidPublicKey) {
            throw new Error("VAPID public key is not configured");
        }

        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: this.urlBase64ToUint8Array(
                this.vapidPublicKey
            ),
        });

        this.subscription = subscription;
        return subscription;
    }

    // Unsubscribe from push notifications
    async unsubscribeFromPush() {
        if (!this.subscription) {
            const registration = await navigator.serviceWorker.ready;
            this.subscription =
                await registration.pushManager.getSubscription();
        }

        if (this.subscription) {
            await this.subscription.unsubscribe();
            this.subscription = null;
        }
    }

    async sendSubscriptionToServer(subscription) {
        try {
            // Menghapus 'Authorization' header dari request
            const response = await fetch("/api/push/subscribe", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content, // Tetap kirim CSRF jika ada
                },
                body: JSON.stringify(subscription.toJSON()),
            });

            // Check if response is JSON
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const text = await response.text();
                throw new Error(
                    `Expected JSON but got: ${text.substring(0, 100)}...`
                );
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message || "Failed to send subscription to server"
                );
            }

            return data;
        } catch (error) {
            console.error("Error in sendSubscriptionToServer:", error);
            throw error; // Re-throw to be caught by the calling function
        }
    }

    // Convert base64 to Uint8Array with better error handling
    urlBase64ToUint8Array(base64String) {
        if (!base64String) {
            throw new Error("Base64 string is required");
        }

        const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, "+")
            .replace(/_/g, "/");
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    // Check if user is subscribed
    async isSubscribed() {
        if (!this.isSupported) return false;

        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        return subscription !== null;
    }

    // Get current subscription
    async getCurrentSubscription() {
        if (!this.isSupported) return null;

        const registration = await navigator.serviceWorker.ready;
        return await registration.pushManager.getSubscription();
    }
}

// Export instance
const pushNotificationService = new PushNotificationService();
export default pushNotificationService;
