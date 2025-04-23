// resources/js/mqtt/mqttClient.js
import mqtt from "mqtt";

let mqttClient = null;
let isInitialized = false;
const subscribedTopics = new Set();
const messageHandlers = new Set();

// Konfigurasi broker
const options = {
    username: "mqtt_ta",
    password: "Semangat_45",
    protocol: "wss",
    reconnectPeriod: 5000,
    connectTimeout: 10000,
    clean: true,
};

// Koneksi hanya dibuat sekali
export const connectMQTT = (
    onMessageCallback,
    topics = ["sensor", "gps", "baterai"]
) => {
    if (!mqttClient) {
        mqttClient = mqtt.connect(
            "wss://bac8cead2b4841e8bd7432510d2c80de.s1.eu.hivemq.cloud:8884/mqtt",
            options
        );
    }

    if (onMessageCallback) {
        messageHandlers.add(onMessageCallback);
    }

    if (!isInitialized) {
        mqttClient.on("connect", () => {
            console.log("✅ MQTT Connected");

            // Subscribe ke semua topik dalam array
            topics.forEach((topic) => {
                if (!subscribedTopics.has(topic)) {
                    mqttClient.subscribe(topic, (err) => {
                        if (err) {
                            console.error(
                                "❌ Failed to subscribe:",
                                topic,
                                err
                            );
                        } else {
                            console.log("📡 Subscribed to topic:", topic);
                            subscribedTopics.add(topic);
                        }
                    });
                }
            });
        });

        mqttClient.on("message", (topic, message) => {
            try {
                const data = JSON.parse(message.toString());
                for (const handler of messageHandlers) {
                    handler(data, topic); // Kirim data + nama topik ke handler
                }
            } catch (err) {
                console.error("⚠️ Error parsing message:", err);
            }
        });

        mqttClient.on("error", (err) => {
            console.error("💥 MQTT Error:", err);
        });

        mqttClient.on("reconnect", () => {
            console.warn("🔄 Reconnecting to MQTT Broker...");
        });

        mqttClient.on("close", () => {
            console.warn("🔌 MQTT connection closed");
        });

        isInitialized = true;
    }

    return mqttClient;
};

// Fungsi opsional untuk menghapus handler
export const removeMQTTHandler = (handler) => {
    messageHandlers.delete(handler);
};

// Fungsi untuk manual disconnect
export const disconnectMQTT = () => {
    if (mqttClient) {
        mqttClient.end(true, () => {
            console.log("🔴 Disconnected from MQTT Broker");
            isInitialized = false;
            mqttClient = null;
            subscribedTopics.clear();
            messageHandlers.clear();
        });
    }
};
