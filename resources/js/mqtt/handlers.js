import { getHandlers } from "./subscriptions";
import { cekDanKirimNotifikasi } from "./notifikasiWhatsapp";

export const registerMQTTHandlers = (client) => {
    client.on("connect", () => {
        console.log("✅ MQTT Connected");
    });

    client.on("message", (topic, message) => {
        try {
            const payload = message.toString().replace(/[\x00-\x1F\x7F]/g, "");
            const data = JSON.parse(payload);

            for (const handler of getHandlers()) {
                handler(data, topic);
            }

            // Aktifkan jika dibutuhkan
            cekDanKirimNotifikasi(data);
        } catch (err) {
            console.error("⚠️ Error parsing message:", err);
        }
    });

    client.on("error", (err) => {
        console.error("💥 MQTT Error:", err);
    });

    client.on("reconnect", () => {
        console.warn("🔄 Reconnecting to MQTT Broker...");
    });

    client.on("close", () => {
        console.warn("🔌 MQTT connection closed");
    });
};
