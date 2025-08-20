// Service Worker untuk Web Push Notification
const CACHE_NAME = "hydrowind-push-v1";
const urlsToCache = ["/", "/favicon.png"];

// Install Service Worker
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // Use Promise.allSettled to handle partial failures gracefully
            return Promise.allSettled(
                urlsToCache.map((url) =>
                    cache.add(url).catch((err) => {
                        console.warn(`Failed to cache ${url}:`, err);
                        return null;
                    })
                )
            );
        })
    );
    self.skipWaiting();
});

// Activate Service Worker
self.addEventListener("activate", (event) => {
    event.waitUntil(self.clients.claim());
});

// Handle Push Notification
self.addEventListener("push", (event) => {
    if (!(self.Notification && self.Notification.permission === "granted")) {
        return;
    }

    let data = {};
    if (event.data) {
        data = event.data.json();
    }

    const title = data.title || "HydroWind Alert";
    const options = {
        body: data.body || "Ada peringatan baru dari sistem monitoring",
        icon: data.icon || "/favicon.png",
        badge: "/favicon.png",
        vibrate: [200, 100, 200],
        tag: "hydrowind-alert",
        data: {
            url: data.url || "/",
            timestamp: Date.now(),
        },
        actions: [
            {
                action: "view",
                title: "Lihat Detail",
                icon: "/favicon.png",
            },
            {
                action: "dismiss",
                title: "Tutup",
                icon: "/favicon.png",
            },
        ],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// Handle Notification Click
self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    if (event.action === "view") {
        event.waitUntil(clients.openWindow(event.notification.data.url));
    }
});

// Handle Background Sync
self.addEventListener("sync", (event) => {
    if (event.tag === "background-sync") {
        event.waitUntil(backgroundSync());
    }
});

async function backgroundSync() {
    // Implementasi background sync jika diperlukan
}
