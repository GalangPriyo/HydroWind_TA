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

const activeTab = ref(0);
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

const handleMQTTData = (newData) => {
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
        kecepatan_angin: "km/jam",
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
        // Update status and timestamp
        const formattedTimestamp = newData.timestamp
            ? (() => {
                  const d = new Date(newData.timestamp);
                  if (isNaN(d)) return "Tidak tersedia"; // Cek jika invalid date
                  const day = String(d.getDate()).padStart(2, "0");
                  const month = String(d.getMonth() + 1).padStart(2, "0");
                  const year = d.getFullYear();
                  const time = d.toTimeString().split(" ")[0];
                  return `${day}-${month}-${year} | ${time} WIB`;
              })()
            : "Tidak tersedia";

        mqttData.value[existingIndex].status = "Online";
        mqttData.value[existingIndex].last_updated = formattedTimestamp;

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
        const formattedTimestamp = newData.timestamp
            ? (() => {
                  const d = new Date(newData.timestamp);
                  if (isNaN(d)) return "Tidak tersedia"; // Cek jika invalid date
                  const day = String(d.getDate()).padStart(2, "0");
                  const month = String(d.getMonth() + 1).padStart(2, "0");
                  const year = d.getFullYear();
                  const time = d.toTimeString().split(" ")[0];
                  return `${day}-${month}-${year} | ${time} WIB`;
              })()
            : "Tidak tersedia";

        const newDevice = {
            node_id: newData.node_id,
            name: "Node " + newData.node_id.split("-")[1], // Default name if not found
            status: "Online",
            last_updated: formattedTimestamp,
            sensors: formattedSensors.map((sensor) => ({
                ...sensor,
                data: Array(10).fill(sensor.value),
            })),
        };

        mqttData.value.push(newDevice);
    }
};

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

                <p class="text-base sm:text-lg text-gray-600 max-w-4xl mx-auto">
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
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto">
                        <button
                            v-for="(device, index) in mqttData"
                            :key="device.node_id"
                            @click="activeTab = index"
                            :class="[
                                activeTab === index
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                            ]"
                        >
                            {{ device.name }}
                        </button>
                    </nav>
                </div>

                <div
                    v-for="(device, index) in mqttData"
                    :key="device.node_id"
                    v-show="activeTab === index"
                    class="py-4"
                >
                    <div class="flex justify-end items-center">
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-500">
                                Terakhir diperbarui: {{ device.last_updated }}
                            </span>
                            <span
                                v-if="device.status === 'Offline'"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600 border border-red-600"
                            >
                                Offline
                            </span>
                            <span
                                v-else-if="device.status === 'Online'"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-600 border border-green-600"
                            >
                                Online
                            </span>
                        </div>
                    </div>
                    <div
                        v-for="(sensor, sensorIndex) in device.sensors"
                        :key="sensor.type"
                        class="py-4"
                    >
                        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                            <h1
                                class="font-bold text-lg sm:text-xl pb-4 text-center"
                            >
                                {{ sensorLabel(sensor.type) }}
                            </h1>
                            <div class="flex flex-col lg:flex-row gap-4">
                                <div
                                    class="w-full lg:w-auto lg:flex-1 lg:max-w-xs"
                                >
                                    <SensorStat
                                        :sensor-type="sensor.type"
                                        :sensor-value="sensor.value"
                                    />
                                </div>
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
                    </div>
                </div>
            </div>

            <div v-else-if="monitoringStatus === 'has_nodes_and_realtime'">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto">
                        <button
                            v-for="(device, index) in mqttData"
                            :key="device.node_id"
                            @click="activeTab = index"
                            :class="[
                                activeTab === index
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                            ]"
                        >
                            {{ device.name }}
                        </button>
                    </nav>
                </div>

                <div
                    v-for="(device, index) in mqttData"
                    :key="device.node_id"
                    v-show="activeTab === index"
                    class="py-4"
                >
                    <div class="flex justify-end items-center">
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-500">
                                Terakhir diperbarui: {{ device.last_updated }}
                            </span>
                            <span
                                v-if="device.status === 'Offline'"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600 border border-red-600"
                            >
                                Offline
                            </span>
                            <span
                                v-else-if="device.status === 'Online'"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-600 border border-green-600"
                            >
                                Online
                            </span>
                        </div>
                    </div>
                    <div
                        v-for="(sensor, sensorIndex) in device.sensors"
                        :key="sensor.type"
                        class="py-4"
                    >
                        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                            <h1
                                class="font-bold text-lg sm:text-xl pb-4 text-center"
                            >
                                {{ sensorLabel(sensor.type) }}
                            </h1>
                            <div class="flex flex-col lg:flex-row gap-4">
                                <div
                                    class="w-full lg:w-auto lg:flex-1 lg:max-w-xs"
                                >
                                    <SensorStat
                                        :sensor-type="sensor.type"
                                        :sensor-value="sensor.value"
                                        :unit="sensor.unit"
                                    />
                                </div>
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
                    </div>
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
