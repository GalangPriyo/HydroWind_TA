import {
    connectClient,
    getClient,
    setClientInitialized,
    isClientInitialized,
    disconnectClient,
} from "./client";
import {
    subscribeToTopics,
    addHandler,
    removeHandler,
    clearSubscriptions,
} from "./subscriptions";
import { registerMQTTHandlers } from "./handlers";

export const connectMQTT = (onMessageCallback, topics = ["sensor"]) => {
    const client = connectClient();

    if (onMessageCallback) {
        addHandler(onMessageCallback);
    }

    if (!isClientInitialized()) {
        registerMQTTHandlers(client);
        subscribeToTopics(client, topics);
        setClientInitialized(true);
    }

    return client;
};

export {
    removeHandler as removeMQTTHandler,
    disconnectClient as disconnectMQTT,
};
