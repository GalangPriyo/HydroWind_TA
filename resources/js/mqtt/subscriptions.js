const subscribedTopics = new Set();
const messageHandlers = new Set();

export const addHandler = (handler) => messageHandlers.add(handler);
export const removeHandler = (handler) => messageHandlers.delete(handler);
export const getHandlers = () => messageHandlers;

export const subscribeToTopics = (client, topics = []) => {
    topics.forEach((topic) => {
        if (!subscribedTopics.has(topic)) {
            client.subscribe(topic, (err) => {
                if (err) {
                    console.error("❌ Failed to subscribe:", topic, err);
                } else {
                    console.log("📡 Subscribed to topic:", topic);
                    subscribedTopics.add(topic);
                }
            });
        }
    });
};

export const clearSubscriptions = () => {
    subscribedTopics.clear();
    messageHandlers.clear();
};
