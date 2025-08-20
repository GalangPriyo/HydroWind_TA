<script setup>
import { Link, Head } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import StatusCard from "@/Components/StatusCard.vue";
import { ref, reactive, computed, onMounted, onUnmounted } from "vue";

defineOptions({ layout: GuestLayout });

const props = defineProps({
    devices: Array,
});

const state = reactive({
    nodes: {},
});

const activeNodeIndex = ref(0);

// --- FUNGSI KLASIFIKASI STATUS ---
const classifyStatus = (value, sensor) => {
    // [FIX] Memastikan perbandingan dilakukan antara number dengan number.
    // parseFloat() ditambahkan untuk menjaga konsistensi tipe data dari database.
    if (sensor.threshold) {
        if (value >= parseFloat(sensor.threshold.bahaya)) return "bahaya";
        if (value >= parseFloat(sensor.threshold.waspada)) return "waspada";
        return "aman";
    }
    // Fallback jika tidak ada threshold dari database
    const fallbackThresholds = {
        curah_hujan: { bahaya: 150, waspada: 100 },
        ketinggian_air: { bahaya: 150, waspada: 120 },
        kecepatan_angin: { bahaya: 50, waspada: 38 },
    };
    if (!fallbackThresholds[sensor.name] || typeof value !== "number") {
        return "No Data";
    }
    if (value >= fallbackThresholds[sensor.name].bahaya) return "bahaya";
    if (value >= fallbackThresholds[sensor.name].waspada) return "waspada";
    return "aman";
};

// --- INISIALISASI DATA AWAL DARI PROPS ---
const getInitialSensorData = (node) => {
    const sensors = {};
    const timestamps = [];

    node.sensors.forEach((sensor) => {
        const latest = sensor.latest_data;
        if (latest) {
            const value = parseFloat(latest.value);
            sensors[sensor.name] = {
                value,
                status: classifyStatus(value, sensor),
                threshold: sensor.threshold,
            };
            timestamps.push(new Date(latest.timestamp));
        } else {
            sensors[sensor.name] = {
                value: "-",
                status: "No Data",
                threshold: sensor.threshold,
            };
        }
    });

    const latestTimestamp = timestamps.length
        ? new Date(Math.max(...timestamps.map((t) => t.getTime())))
        : null;

    return {
        name: node.name,
        updatedAt: latestTimestamp
            ? (() => {
                  const d = latestTimestamp;
                  const day = String(d.getDate()).padStart(2, "0");
                  const month = String(d.getMonth() + 1).padStart(2, "0");
                  const year = d.getFullYear();
                  const time = d.toTimeString().split(" ")[0];
                  return `${day}-${month}-${year} | ${time} WIB`;
              })()
            : "Tidak tersedia",
        sensors,
    };
};

// --- MEMPROSES DATA AWAL UNTUK SEMUA NODE ---
const validSensorsPerNode = {};
const sensorThresholdsPerNode = {};

props.devices.forEach((node) => {
    validSensorsPerNode[node.node_id] = node.sensors.map((s) => s.name);
    sensorThresholdsPerNode[node.node_id] = {};
    node.sensors.forEach((sensor) => {
        sensorThresholdsPerNode[node.node_id][sensor.name] = sensor.threshold;
    });
    state.nodes[node.node_id] = getInitialSensorData(node);
});

// --- MENANGANI DATA MQTT REALTIME ---
const handleMQTTData = (payload) => {
    const node = props.devices.find((d) => d.node_id === payload.node_id);
    if (!node) return;

    const validSensorNames = validSensorsPerNode[payload.node_id];
    if (!validSensorNames) return;

    const displaySensorNames = [
        "curah_hujan",
        "ketinggian_air",
        "kecepatan_angin",
    ];

    const d = new Date();
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();
    const time = d.toTimeString().split(" ")[0];
    const updatedAt = `${day}-${month}-${year} | ${time} WIB`;

    const newSensors = {};

    const initialNodeSensors = state.nodes[payload.node_id]?.sensors || {};
    for (const sensorName in initialNodeSensors) {
        if (displaySensorNames.includes(sensorName)) {
            newSensors[sensorName] = { ...initialNodeSensors[sensorName] };
        }
    }

    for (const [key, val] of Object.entries(payload.sensor)) {
        if (
            !validSensorNames.includes(key) ||
            !displaySensorNames.includes(key)
        )
            continue;

        const numericValue = parseFloat(
            val?.toString().replace(/[^0-9.]/g, "") || "0"
        );

        const threshold = sensorThresholdsPerNode[payload.node_id]?.[key];
        const sensorForClassification = { name: key, threshold: threshold };

        newSensors[key] = {
            value: numericValue,
            status: classifyStatus(numericValue, sensorForClassification),
            threshold: threshold,
        };
    }

    if (Object.keys(newSensors).length > 0) {
        state.nodes[payload.node_id] = {
            name: node.name || "Titik Pantau",
            updatedAt,
            sensors: newSensors,
        };
    }
};

// --- COMPUTED PROPERTIES ---
const nodeData = computed(() => Object.entries(state.nodes));
const activeNode = computed(() =>
    nodeData.value.length > 0 ? nodeData.value[activeNodeIndex.value] : null
);

const overallStatus = computed(() => {
    if (nodeData.value.length === 0) {
        return null;
    }

    const counts = { bahaya: 0, waspada: 0, aman: 0, "No Data": 0 };
    const statusPriority = ["bahaya", "waspada", "aman", "No Data"];
    let overallSystemStatus = "aman";
    const alertNodesList = [];

    const nodeStatuses = Object.values(state.nodes).map((node) => {
        let highestNodeStatus = "No Data";
        if (node.sensors && Object.keys(node.sensors).length > 0) {
            highestNodeStatus = "aman";
            for (const sensor of Object.values(node.sensors)) {
                if (
                    statusPriority.indexOf(sensor.status) <
                    statusPriority.indexOf(highestNodeStatus)
                ) {
                    highestNodeStatus = sensor.status;
                }
            }
        }
        counts[highestNodeStatus]++;
        return { name: node.name, status: highestNodeStatus };
    });

    for (const status of statusPriority) {
        if (counts[status] > 0) {
            overallSystemStatus = status;
            break;
        }
    }

    if (overallSystemStatus === "waspada" || overallSystemStatus === "bahaya") {
        nodeStatuses.forEach((node) => {
            if (node.status === "waspada" || node.status === "bahaya") {
                alertNodesList.push(node.name);
            }
        });
    }

    return {
        overall: overallSystemStatus,
        counts: counts,
        alertNodes: alertNodesList,
    };
});

// --- FUNGSI NAVIGASI NODE ---
const nextNode = () => {
    activeNodeIndex.value = (activeNodeIndex.value + 1) % nodeData.value.length;
};

const prevNode = () => {
    activeNodeIndex.value =
        (activeNodeIndex.value - 1 + nodeData.value.length) %
        nodeData.value.length;
};

// --- LIFECYCLE HOOKS (MQTT) ---
onMounted(() => {
    const channel = window.Echo.channel("mqtt-sensor");
    channel.listen(".sensor.updated", (e) => {
        handleMQTTData(e.payload);
    });
    channel.subscribed(() => {
        console.log("✅ Subscribed to mqtt-sensor");
    });
});

onUnmounted(() => {
    window.Echo.leave("mqtt-sensor");
});
</script>

<template>
    <Head title="Home" />
    <div
        class="min-h-screen bg-gradient-to-b from-blue-50 via-cyan-200 to-blue-400"
    >
        <div class="max-w-7xl mx-auto pt-20 px-4">
            <div class="text-center">
                <h1
                    class="text-lg font-extrabold text-primary sm:text-xl md:text-2xl"
                >
                    <span
                        class="block text-blue-700 text-3xl mb-2 md:text-5xl sm:text-4xl xl:text-6xl"
                    >
                        HydroWind
                    </span>
                    <span class="block">
                        Sistem Pemantauan Bencana Banjir dan Angin Kencang
                    </span>
                </h1>
                <p
                    class="max-w-md mx-auto text-base text-gray-600 sm:text-lg mt-2 md:text-lg md:max-w-5xl"
                >
                    Sistem pemantauan bencana berbasis IoT yang menampilkan data
                    real-time tentang curah hujan, kecepatan angin, dan
                    ketinggian air sungai untuk mendeteksi potensi bencana
                    banjir dan angin kencang di Desa Gebangan.
                </p>
            </div>

            <div v-if="overallStatus" class="my-8">
                <div
                    class="p-4 rounded-lg shadow-lg text-center"
                    :class="{
                        'bg-red-100 border border-red-400 text-red-800':
                            overallStatus.overall === 'bahaya',
                        'bg-yellow-100 border border-yellow-400 text-yellow-800':
                            overallStatus.overall === 'waspada',
                        'bg-green-100 border border-green-400 text-green-800':
                            overallStatus.overall === 'aman',
                        'bg-gray-100 border border-gray-400 text-gray-800':
                            overallStatus.overall === 'No Data',
                    }"
                >
                    <h2 class="text-xl font-bold uppercase tracking-wider">
                        Status Keseluruhan: {{ overallStatus.overall }}
                    </h2>
                    <div
                        class="mt-2 flex justify-center items-center gap-x-6 text-sm"
                    >
                        <span
                            ><i class="fa-solid fa-triangle-exclamation"></i>
                            Bahaya:
                            <strong>{{
                                overallStatus.counts.bahaya
                            }}</strong></span
                        >
                        <span
                            ><i class="fa-solid fa-circle-exclamation"></i>
                            Waspada:
                            <strong>{{
                                overallStatus.counts.waspada
                            }}</strong></span
                        >
                        <span
                            ><i class="fa-solid fa-circle-check"></i> Aman:
                            <strong>{{
                                overallStatus.counts.aman
                            }}</strong></span
                        >
                    </div>
                    <div
                        v-if="overallStatus.alertNodes.length > 0"
                        class="mt-2 text-xs font-semibold"
                    >
                        LOKASI TERDAMPAK:
                        {{ overallStatus.alertNodes.join(", ") }}
                    </div>
                </div>
            </div>

            <div v-if="nodeData.length === 0" class="my-16 text-center">
                <div
                    class="w-56 h-56 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6"
                >
                    <span class="text-9xl text-blue-600"
                        ><i class="fa-solid fa-satellite-dish"></i
                    ></span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    Belum Ada Perangkat Terdaftar
                </h3>
            </div>

            <div v-else-if="activeNode" class="pt-2 pb-4">
                <div class="my-3 flex justify-center items-center gap-4">
                    <button
                        class="bg-transparent text-gray-700 px-4 py-2 rounded-full hover:bg-blue-200 disabled:opacity-50"
                        @click="prevNode"
                        :disabled="nodeData.length <= 1"
                    >
                        <i class="fa-solid fa-chevron-left text-xl"></i>
                    </button>
                    <div class="text-center">
                        <h2
                            class="font-semibold text-gray-700 text-base sm:text-lg"
                        >
                            {{ activeNode[1].name }} | Node ID:
                            {{ activeNode[0] }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            Terakhir diperbarui:
                            {{ activeNode[1].updatedAt }}
                        </p>
                    </div>
                    <button
                        class="bg-transparent text-gray-700 px-4 py-2 rounded-full hover:bg-blue-200 disabled:opacity-50"
                        @click="nextNode"
                        :disabled="nodeData.length <= 1"
                    >
                        <i class="fa-solid fa-chevron-right text-xl"></i>
                    </button>
                </div>
                <div class="flex flex-wrap justify-center gap-6">
                    <template
                        v-for="(sensorData, sensorType) in activeNode[1]
                            .sensors"
                    >
                        <StatusCard
                            v-if="
                                [
                                    'curah_hujan',
                                    'ketinggian_air',
                                    'kecepatan_angin',
                                ].includes(sensorType)
                            "
                            :key="sensorType"
                            :type="sensorType"
                            :data="{
                                value: sensorData.value,
                                status: sensorData.status,
                                location: activeNode[1].name,
                            }"
                        />
                    </template>
                </div>
            </div>

            <div v-else class="my-16 text-center">
                <p class="text-gray-600">Menunggu data realtime...</p>
            </div>
        </div>
    </div>
</template>
