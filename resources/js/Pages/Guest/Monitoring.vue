<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted, onUnmounted, computed, ref } from "vue";
import Echo from "laravel-echo";
import { Head } from "@inertiajs/vue3";
import SensorChart from "@/Components/SensorChart.vue";
import SensorStat from "@/Components/SensorStat.vue";

defineOptions({ layout: GuestLayout });

const props = defineProps({
    registeredNodeIds: Array,
    initialDevicesData: Array,
});

const validNodeIds = ref(props.registeredNodeIds || []);
const mqttData = ref(props.initialDevicesData || []);
const activeNode = ref(null);

const sensorLabel = (name) => {
    const labels = {
        kecepatan_angin: "Kecepatan Angin",
        ketinggian_air: "Ketinggian Air Sungai",
        curah_hujan: "Curah Hujan",
        tekanan_udara: "Tekanan Udara",
    };
    return labels[name] || "Sensor Tidak Dikenal";
};

const extractNumericValue = (value) => {
    if (typeof value === "number") return value;
    if (typeof value !== "string") return 0;
    const numericMatch = value.match(/[\d.]+/);
    return numericMatch ? parseFloat(numericMatch[0]) : 0;
};

function handleMQTTData(newData) {
    if (
        !newData ||
        typeof newData.sensor !== "object" ||
        newData.sensor === null
    ) {
        return;
    }

    if (!validNodeIds.value.includes(newData.node_id)) {
        console.warn("Data dari node_id tidak dikenal:", newData.node_id);
        return;
    }

    activeNode.value = newData.node_id;

    const sensorOrder = [
        "curah_hujan",
        "kecepatan_angin",
        "ketinggian_air",
        "tekanan_udara",
    ];

    const sensorUnits = {
        kecepatan_angin: "m/s",
        ketinggian_air: "cm",
        curah_hujan: "mm",
        tekanan_udara: "mb",
    };

    const formattedSensors = sensorOrder
        .filter((key) => newData.sensor.hasOwnProperty(key))
        .map((key) => ({
            type: key,
            value: extractNumericValue(newData.sensor[key]),
            unit: sensorUnits[key] || "N/A",
        }));

    const existingIndex = mqttData.value.findIndex(
        (d) => d.node_id === newData.node_id
    );

    if (existingIndex !== -1) {
        formattedSensors.forEach((sensor) => {
            const existingSensor = mqttData.value[existingIndex].sensors.find(
                (s) => s.type === sensor.type
            );
            if (existingSensor) {
                existingSensor.value = sensor.value;

                if (!existingSensor.data) {
                    existingSensor.data = Array(10).fill(sensor.value);
                } else {
                    existingSensor.data = [
                        ...existingSensor.data.slice(1),
                        sensor.value,
                    ];
                }
            }
        });
    } else {
        const newDevice = {
            node_id: newData.node_id,
            timestamp: newData.timestamp,
            sensors: formattedSensors.map((sensor) => ({
                ...sensor,
                data: Array(10).fill(sensor.value),
            })),
        };

        mqttData.value.push(newDevice);
    }
}

const monitoringStatus = computed(() => {
    if (validNodeIds.value.length === 0) {
        return "no_nodes_registered";
    } else if (mqttData.value.length > 0 && activeNode.value === null) {
        return "has_nodes_but_no_realtime";
    } else if (mqttData.value.length > 0 && activeNode.value !== null) {
        return "has_nodes_and_realtime";
    }
    return "no_data";
});

onMounted(() => {
    const channel = window.Echo.channel("mqtt-sensor");
    channel.listen(".sensor.updated", (e) => {
        console.log("✅ Data diterima dari WebSocket:", e);
        handleMQTTData(e.payload);
    });
});

onUnmounted(() => {
    mqttData.value = [];
    window.Echo.leave("mqtt-sensor");
});
</script>

<template>
    <Head title="Monitoring" />
    <div class="bg-blue-50">
        <div class="pt-20 w-[90%] mx-auto pb-10 min-h-screen">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                    Panel Real-Time Monitoring
                </h1>

                <p class="text-lg text-gray-600 max-w-4xl mx-auto">
                    Menampilkan data sensor bencana dari setiap alat pendeteksi
                    secara real-time.
                </p>
            </div>

            <div
                v-if="monitoringStatus === 'no_nodes_registered'"
                class="text-gray-900 text-center flex flex-col items-center justify-center py-20"
            >
                <img
                    src="/assets/media/no_device1.png"
                    alt="Tidak ada perangkat terdaftar"
                    class="w-1/3 max-w-xs sm:max-w-sm md:max-w-md"
                />
                <p class="text-xl font-bold pt-4">
                    Tidak ada perangkat terdaftar dalam sistem.
                </p>
                <p class="text-gray-600">
                    Silakan hubungi administrator untuk mendaftarkan perangkat.
                </p>
            </div>

            <div v-else-if="monitoringStatus === 'has_nodes_but_no_realtime'">
                <div role="tablist" class="tabs tabs-lifted">
                    <template
                        v-for="(device, index) in mqttData"
                        :key="device.node_id"
                    >
                        <input
                            type="radio"
                            :id="'tab-' + index"
                            name="device_tabs"
                            role="tab"
                            class="tab"
                            :aria-label="device.name"
                            :checked="index === 0"
                        />
                        <div
                            role="tabpanel"
                            class="tab-content bg-base-100 border-base-300 rounded-tr-xl rounded-br-xl rounded-bl-xl px-4 sm:px-10 py-4"
                        >
                            <div
                                v-for="(sensor, sensorIndex) in device.sensors"
                                :key="sensor.type"
                                class="flex flex-col lg:flex-row gap-4 sm:gap-10 items-stretch py-4"
                            >
                                <!-- SensorStat - diubah width dan grow settings -->
                                <div
                                    class="w-full lg:w-auto lg:flex-1 lg:max-w-xs"
                                >
                                    <SensorStat
                                        :sensor-type="sensor.type"
                                        :sensor-value="sensor.value"
                                        :unit="sensor.unit"
                                    />
                                </div>

                                <!-- SensorChart - diubah flex-grow settings -->
                                <div class="w-full lg:flex-[2]">
                                    <SensorChart
                                        :sensor-type="sensor.type"
                                        :sensor-data="sensor.data"
                                        :sensor-value="sensor.value"
                                        :index="index"
                                        :sensor-index="sensorIndex"
                                    />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div v-else-if="monitoringStatus === 'has_nodes_and_realtime'">
                <div role="tablist" class="tabs tabs-lifted">
                    <template
                        v-for="(device, index) in mqttData"
                        :key="device.node_id"
                    >
                        <input
                            type="radio"
                            :id="'tab-' + index"
                            name="device_tabs"
                            role="tab"
                            class="tab"
                            :aria-label="device.name"
                            :checked="index === 0"
                        />
                        <div
                            role="tabpanel"
                            class="tab-content bg-base-100 border-base-300 rounded-tr-xl rounded-br-xl rounded-bl-xl px-4 sm:px-10 py-4"
                        >
                            <div
                                v-for="(sensor, sensorIndex) in device.sensors"
                                :key="sensor.type"
                                class="flex flex-col lg:flex-row gap-4 sm:gap-10 items-stretch py-4"
                            >
                                <!-- SensorStat - diubah width dan grow settings -->
                                <div
                                    class="w-full lg:w-auto lg:flex-1 lg:max-w-xs"
                                >
                                    <SensorStat
                                        :sensor-type="sensor.type"
                                        :sensor-value="sensor.value"
                                        :unit="sensor.unit"
                                    />
                                </div>

                                <!-- SensorChart - diubah flex-grow settings -->
                                <div class="w-full lg:flex-[2]">
                                    <SensorChart
                                        :sensor-type="sensor.type"
                                        :sensor-data="sensor.data"
                                        :sensor-value="sensor.value"
                                        :index="index"
                                        :sensor-index="sensorIndex"
                                    />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div
                v-else
                class="text-gray-900 text-center flex flex-col items-center justify-center py-20"
            >
                <p class="text-xl font-bold pt-4">
                    Status monitoring tidak dikenali.
                </p>
            </div>
        </div>
    </div>
</template>
