import mqtt from "mqtt";

let mqttClient = null;
let isInitialized = false;

const options = {
    username: "mqtt_ta",
    password: "Semangat_45",
    protocol: "wss",
    reconnectPeriod: 5000,
    connectTimeout: 10000,
    clean: true,
};

export const getClient = () => mqttClient;

export const connectClient = () => {
    if (!mqttClient) {
        mqttClient = mqtt.connect(
            "wss://bac8cead2b4841e8bd7432510d2c80de.s1.eu.hivemq.cloud:8884/mqtt",
            options
        );
    }
    return mqttClient;
};

export const isClientInitialized = () => isInitialized;
export const setClientInitialized = (value) => {
    isInitialized = value;
};

export const disconnectClient = () => {
    if (mqttClient) {
        mqttClient.end(true, () => {
            mqttClient = null;
            isInitialized = false;
        });
    }
};
