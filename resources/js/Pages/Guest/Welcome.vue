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

const classifyStatus = (value, type) => {
    const thresholds = {
        curah_hujan: { bahaya: 1100, waspada: 500 },
        ketinggian_air: { bahaya: 120, waspada: 60 },
        kecepatan_angin: { bahaya: 13, waspada: 7 },
        tekanan_udara: { bahaya: 1000, waspada: 1010 },
    };

    if (value >= thresholds[type].bahaya) return "bahaya";
    if (value >= thresholds[type].waspada) return "waspada";
    return "aman";
};

const getInitialSensorData = (node) => {
    const sensors = {};

    node.sensors.forEach((sensor) => {
        const latest = sensor.latest_data;
        if (latest) {
            const value = parseFloat(latest.value);
            sensors[sensor.name] = {
                value,
                status: classifyStatus(value, sensor.name),
            };
        } else {
            // Beri nilai default jika belum ada data
            sensors[sensor.name] = {
                value: "-",
                status: "No Data",
            };
        }
    });

    return {
        name: node.name,
        updatedAt: node.updated_at
            ? new Date(node.updated_at).toLocaleTimeString("en-GB", {
                  hour: "2-digit",
                  minute: "2-digit",
                  second: "2-digit",
                  timeZone: "Asia/Jakarta",
              })
            : "Tidak tersedia",
        sensors,
    };
};

// ⬇️ Harus diletakkan setelah fungsi di atas
const validSensorsPerNode = {};

props.devices.forEach((node) => {
    validSensorsPerNode[node.node_id] = node.sensors.map((s) => s.name);
    state.nodes[node.node_id] = getInitialSensorData(node);
});

const handleMQTTData = (payload) => {
    const node = props.devices.find((d) => d.node_id === payload.node_id);
    if (!node) return;

    const validSensorNames = validSensorsPerNode[payload.node_id];
    if (!validSensorNames) return;

    const updatedAt = new Date().toLocaleTimeString("en-GB", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        timeZone: "Asia/Jakarta",
    });

    const sensors = {};

    for (const [key, val] of Object.entries(payload.sensor)) {
        if (!validSensorNames.includes(key)) continue;

        const numericValue = parseFloat(
            val?.toString().replace(/[^0-9.]/g, "") || "0"
        );
        sensors[key] = {
            value: numericValue,
            status: classifyStatus(numericValue, key),
        };
    }

    if (Object.keys(sensors).length > 0) {
        const previous = state.nodes[payload.node_id]?.sensors || {};

        state.nodes[payload.node_id] = {
            name: node.name || "Titik Pantau",
            updatedAt,
            sensors: {
                ...previous, // simpan sensor lama
                ...sensors, // timpa dengan sensor yang baru dikirim
            },
        };
    }

    // if (Object.keys(sensors).length > 0) {
    //     state.nodes[payload.node_id] = {
    //         name: node.name || "Titik Pantau",
    //         updatedAt,
    //         sensors,
    //     };
    // }
};

const nodeData = computed(() => Object.entries(state.nodes));
const activeNode = computed(() =>
    nodeData.value.length > 0 ? nodeData.value[activeNodeIndex.value] : null
);

const nextNode = () => {
    activeNodeIndex.value = (activeNodeIndex.value + 1) % nodeData.value.length;
};

const prevNode = () => {
    activeNodeIndex.value =
        (activeNodeIndex.value - 1 + nodeData.value.length) %
        nodeData.value.length;
};

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
    <div class="min-h-screen bg-blue-50">
        <div class="max-w-8xl mx-auto pt-20 px-4">
            <div class="text-center">
                <h1
                    class="text-lg font-extrabold text-primary sm:text-xl md:text-2xl"
                >
                    <span
                        class="block text-blue-600 text-3xl mb-2 md:text-5xl sm:text-4xl"
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

            <!-- 1. Belum ada node terdaftar -->
            <div v-if="nodeData.length === 0" class="my-16 text-center">
                <div
                    class="w-56 h-56 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6"
                >
                    <span class="text-9xl text-blue-300"
                        ><i class="fa-solid fa-satellite-dish"></i
                    ></span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    Belum Ada Perangkat Terdaftar
                </h3>
            </div>

            <!-- 2. Sudah ada node & data sensor di database, tapi belum ada data realtime (activeNode == null) -->
            <div v-else-if="!activeNode && nodeData.length > 0">
                <div
                    v-for="[nodeId, node] in nodeData"
                    :key="nodeId"
                    class="mb-8 border-b pb-4"
                >
                    <div class="text-center mb-3">
                        <h2
                            class="font-semibold text-gray-700 text-base sm:text-lg"
                        >
                            {{ node.name }} | Node ID: {{ nodeId }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            Menampilkan data dari database
                        </p>
                    </div>
                    <div class="flex flex-wrap justify-center gap-6">
                        <StatusCard
                            v-for="(sensor, type) in node.sensors"
                            :key="type"
                            :type="type"
                            :data="{
                                value: sensor.value,
                                status: sensor.status,
                                location: node.name,
                            }"
                        />
                    </div>
                </div>
            </div>

            <!-- 3. Sudah ada node dan data dari MQTT (realtime) tersedia -->
            <div v-else class="my-2">
                <div class="my-3 flex justify-center items-center gap-4">
                    <button
                        class="bg-transparent text-gray-700 px-4 py-2 rounded-full hover:bg-blue-200"
                        @click="prevNode"
                        :disabled="nodeData.length === 0"
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
                            {{ activeNode[1].updatedAt }} WIB
                        </p>
                    </div>
                    <button
                        class="bg-transparent text-gray-700 px-4 py-2 rounded-full hover:bg-blue-200"
                        @click="nextNode"
                        :disabled="nodeData.length === 0"
                    >
                        <i class="fa-solid fa-chevron-right text-xl"></i>
                    </button>
                </div>
                <div class="flex flex-wrap justify-center gap-6">
                    <StatusCard
                        v-for="(sensorData, sensorType) in activeNode[1]
                            .sensors"
                        :key="sensorType"
                        :type="sensorType"
                        :data="{
                            value: sensorData.value,
                            status: sensorData.status,
                            location: activeNode[1].name,
                        }"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
