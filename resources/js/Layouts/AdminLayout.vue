<script setup>
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

// Props dari Inertia
const props = defineProps({
    user: Object,
    devices: Array,
    sensorData: Object,
    stats: Array,
    filters: Object,
});

// Current active node index
const activeNodeIndex = ref(0);

// Keep track of pagination state for each node
const nodePaginationStates = ref({});

// Form filter dengan default dari props.filters
const form = ref({
    device_id: props.filters.device_id || "",
    sensor_type: props.filters.sensor_type || "",
    date_from: props.filters.date_from || "",
    date_to: props.filters.date_to || "",
    page: props.filters.page || 1,
    node_id: props.filters.node_id || "",
});

// Grouping data berdasarkan node_id
const nodeData = computed(() => {
    // Mengelompokkan data sensor berdasarkan node_id
    const groupedByNode = {};

    if (props.sensorData && props.sensorData.data) {
        // Dapatkan semua node IDs yang unik dan simpan sebagai objek dengan key informasi node
        props.sensorData.data.forEach((data) => {
            const nodeId = data.node_id || "unknown";
            const nodeKey = `${nodeId}-${data.device_name}`;

            if (!groupedByNode[nodeKey]) {
                groupedByNode[nodeKey] = {
                    nodeId: nodeId,
                    deviceName: data.device_name,
                    sensorTypes: new Set(),
                    data: [],
                };
            }

            // Tambahkan tipe sensor ke set
            groupedByNode[nodeKey].sensorTypes.add(data.sensor_name);

            // Tambahkan data
            groupedByNode[nodeKey].data.push(data);
        });
    }

    // Konversi objek ke array untuk iterasi di template
    return Object.values(groupedByNode).map((node) => {
        // Kelompokkan data berdasarkan timestamp
        const dataByTimestamp = {};

        node.data.forEach((item) => {
            const timestamp = item.timestamp;

            if (!dataByTimestamp[timestamp]) {
                dataByTimestamp[timestamp] = {
                    timestamp: timestamp,
                    sensors: {},
                };
            }

            // Simpan nilai sensor untuk timestamp ini
            dataByTimestamp[timestamp].sensors[item.sensor_name] = item.value;
        });

        // Konversi timestampData ke array
        const timeSeriesData = Object.values(dataByTimestamp).sort(
            (a, b) => new Date(b.timestamp) - new Date(a.timestamp)
        );

        // Initialize pagination state for this node if it doesn't exist
        if (!nodePaginationStates.value[node.nodeId]) {
            nodePaginationStates.value[node.nodeId] = {
                page: 1,
            };
        }

        return {
            ...node,
            sensorTypes: Array.from(node.sensorTypes),
            timeSeriesData: timeSeriesData,
        };
    });
});

// Active node data
const activeNode = computed(() => {
    return nodeData.value[activeNodeIndex.value] || null;
});

// Active node pagination info
const activeNodePagination = computed(() => {
    if (!activeNode.value) return { page: 1 };
    const nodeId = activeNode.value.nodeId;
    return nodePaginationStates.value[nodeId] || { page: 1 };
});

function nextNode() {
    // Save current node's page before switching
    saveCurrentNodeState();

    if (activeNodeIndex.value < nodeData.value.length - 1) {
        activeNodeIndex.value++;
    } else {
        activeNodeIndex.value = 0; // Loop back to first node
    }
}

function prevNode() {
    // Save current node's page before switching
    saveCurrentNodeState();

    if (activeNodeIndex.value > 0) {
        activeNodeIndex.value--;
    } else {
        activeNodeIndex.value = nodeData.value.length - 1; // Loop to last node
    }
}

function saveCurrentNodeState() {
    if (activeNode.value) {
        const nodeId = activeNode.value.nodeId;
        nodePaginationStates.value[nodeId] = {
            page: form.value.page,
        };
    }
}

function loadActiveNodeData() {
    if (activeNode.value) {
        const nodeId = activeNode.value.nodeId;
        form.value.node_id = nodeId;

        // Restore pagination state for this node
        if (nodePaginationStates.value[nodeId]) {
            form.value.page = nodePaginationStates.value[nodeId].page;
        } else {
            form.value.page = 1;
            nodePaginationStates.value[nodeId] = { page: 1 };
        }

        // Fetch data for this node with the appropriate pagination
        submitFilter();
    }
}

// Watch for active node changes to load correct data
watch(activeNodeIndex, () => {
    loadActiveNodeData();
});

function submitFilter() {
    router.get(route("admin.riwayat"), form.value, {
        preserveState: true,
        preserveScroll: true,
        only: ["sensorData", "stats", "filters"],
    });
}

function resetFilter() {
    form.value = {
        device_id: "",
        sensor_type: "",
        date_from: "",
        date_to: "",
        per_page: 1000,
        page: 1,
        node_id: "",
    };

    // Reset pagination states
    nodePaginationStates.value = {};
    activeNodeIndex.value = 0;
    submitFilter();
}

function handlePageChange(page) {
    if (activeNode.value) {
        // Update form with the new page
        form.value.page = page;

        // Update pagination state for current node
        const nodeId = activeNode.value.nodeId;
        nodePaginationStates.value[nodeId] = {
            page: page,
        };

        // Fetch data with new page
        submitFilter();
    }
}

function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleString("id-ID", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
}

function formatSensorName(name) {
    const nameMap = {
        curah_hujan: "Curah Hujan",
        ketinggian_air: "Ketinggian Air",
        kecepatan_angin: "Kecepatan Angin",
        tekanan_udara: "Tekanan Udara",
    };
    return nameMap[name] || name;
}

function getSensorUnit(name) {
    const unitMap = {
        curah_hujan: "mm",
        ketinggian_air: "cm",
        kecepatan_angin: "km/jam",
        tekanan_udara: "hPa",
    };
    return unitMap[name] || "";
}

function getSensorIcon(name) {
    const iconMap = {
        curah_hujan: "🌧️",
        ketinggian_air: "🌊",
        kecepatan_angin: "💨",
        tekanan_udara: "🌡️",
    };
    return iconMap[name] || "📊";
}

function getSensorColor(name) {
    const colorMap = {
        curah_hujan: "bg-blue-100 text-blue-800 border-blue-200",
        ketinggian_air: "bg-cyan-100 text-cyan-800 border-cyan-200",
        kecepatan_angin: "bg-gray-100 text-gray-800 border-gray-200",
        tekanan_udara: "bg-purple-100 text-purple-800 border-purple-200",
    };
    return colorMap[name] || "bg-gray-100 text-gray-800 border-gray-200";
}

const downloadUrl = computed(() => {
    const params = new URLSearchParams();

    if (form.value.device_id) params.append("device_id", form.value.device_id);
    if (form.value.sensor_type)
        params.append("sensor_type", form.value.sensor_type);
    if (form.value.date_from) params.append("date_from", form.value.date_from);
    if (form.value.date_to) params.append("date_to", form.value.date_to);
    if (form.value.node_id) params.append("node_id", form.value.node_id);

    return `${route("admin.riwayat.download")}?${params.toString()}`;
});

// Initialize with the first node's data when mounted
onMounted(() => {
    if (nodeData.value.length > 0) {
        const firstNode = nodeData.value[0];
        form.value.node_id = firstNode.nodeId;

        // Check if we already have a saved page for this node
        const savedState = nodePaginationStates.value[firstNode.nodeId];
        if (savedState) {
            form.value.page = savedState.page;
        } else {
            form.value.page = 1;
            nodePaginationStates.value[firstNode.nodeId] = { page: 1 };
        }
    }
});
</script>

<template>
    <Head title="Riwayat Sensor" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mb-4"
            >
                <span class="text-2xl">📊</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Riwayat Data Sensor
            </h1>
            <p class="text-gray-600">
                Pantau dan analisis data historis dari semua sensor monitoring
            </p>
        </div>

        <!-- Main Content Card -->
        <div
            class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
        >
            <!-- Filter Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <span class="mr-3">🔍</span>
                    Filter Data
                </h2>
            </div>

            <div class="p-8">
                <form @submit.prevent="submitFilter" class="mb-8">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6"
                    >
                        <!-- Device -->
                        <div class="space-y-2">
                            <label
                                class="flex items-center text-sm font-semibold text-gray-700"
                            >
                                <span class="mr-2">📡</span>
                                Perangkat
                            </label>
                            <div class="relative">
                                <select
                                    v-model="form.device_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 bg-white appearance-none"
                                >
                                    <option value="">Semua Perangkat</option>
                                    <option
                                        v-for="device in devices"
                                        :key="device.id"
                                        :value="device.id"
                                    >
                                        {{ device.name }} ({{
                                            device.node_id || "Tanpa ID"
                                        }})
                                    </option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                                >
                                    <span class="text-gray-400">▼</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sensor Type -->
                        <div class="space-y-2">
                            <label
                                class="flex items-center text-sm font-semibold text-gray-700"
                            >
                                <span class="mr-2">🔬</span>
                                Jenis Sensor
                            </label>
                            <div class="relative">
                                <select
                                    v-model="form.sensor_type"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 bg-white appearance-none"
                                >
                                    <option value="">Semua Sensor</option>
                                    <option value="curah_hujan">
                                        🌧️ Curah Hujan
                                    </option>
                                    <option value="ketinggian_air">
                                        🌊 Ketinggian Air
                                    </option>
                                    <option value="kecepatan_angin">
                                        💨 Kecepatan Angin
                                    </option>
                                    <option value="tekanan_udara">
                                        🌡️ Tekanan Udara
                                    </option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                                >
                                    <span class="text-gray-400">▼</span>
                                </div>
                            </div>
                        </div>

                        <!-- Date From -->
                        <div class="space-y-2">
                            <label
                                class="flex items-center text-sm font-semibold text-gray-700"
                            >
                                <span class="mr-2">📅</span>
                                Tanggal Mulai
                            </label>
                            <input
                                type="date"
                                v-model="form.date_from"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                            />
                        </div>

                        <!-- Date To -->
                        <div class="space-y-2">
                            <label
                                class="flex items-center text-sm font-semibold text-gray-700"
                            >
                                <span class="mr-2">📅</span>
                                Tanggal Akhir
                            </label>
                            <input
                                type="date"
                                v-model="form.date_to"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                            />
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col justify-end space-y-3">
                            <button
                                type="submit"
                                class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl flex items-center justify-center"
                            >
                                <span class="mr-2">🔍</span>
                                Filter Data
                            </button>
                            <button
                                type="button"
                                @click="resetFilter"
                                class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium flex items-center justify-center"
                            >
                                <span class="mr-2">🔄</span>
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Download Button -->
                <div class="flex justify-end mb-8">
                    <a
                        :href="downloadUrl"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
                    >
                        <span class="mr-2">📥</span>
                        Download Data
                    </a>
                </div>

                <!-- Node Data Section -->
                <div v-if="nodeData.length > 0">
                    <!-- Node Navigation -->
                    <div
                        class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-200"
                    >
                        <div class="flex items-center justify-between">
                            <button
                                @click="prevNode"
                                class="w-12 h-12 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center text-gray-600 hover:text-blue-600 border border-gray-200"
                                :disabled="nodeData.length <= 1"
                            >
                                <span class="text-xl">←</span>
                            </button>

                            <div class="text-center flex-1 mx-6">
                                <div
                                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-200"
                                >
                                    <h3
                                        class="text-xl font-bold text-gray-900 mb-1 flex items-center justify-center"
                                    >
                                        <span class="mr-2">📡</span>
                                        {{
                                            activeNode?.deviceName ||
                                            "Tanpa nama"
                                        }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Node ID:
                                        {{ activeNode?.nodeId || "Tanpa ID" }}
                                    </p>
                                    <div
                                        class="flex items-center justify-center space-x-4"
                                    >
                                        <span
                                            class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full"
                                        >
                                            {{ activeNodeIndex + 1 }} dari
                                            {{ nodeData.length }} node
                                        </span>
                                        <span
                                            class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full"
                                        >
                                            {{
                                                activeNode?.sensorTypes
                                                    ?.length || 0
                                            }}
                                            sensor aktif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button
                                @click="nextNode"
                                class="w-12 h-12 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center text-gray-600 hover:text-blue-600 border border-gray-200"
                                :disabled="nodeData.length <= 1"
                            >
                                <span class="text-xl">→</span>
                            </button>
                        </div>
                    </div>

                    <!-- Sensor Types Overview -->
                    <div v-if="activeNode" class="mb-8">
                        <h4
                            class="text-lg font-semibold text-gray-800 mb-4 flex items-center"
                        >
                            <span class="mr-2">🔬</span>
                            Sensor Terpasang
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div
                                v-for="sensorType in activeNode.sensorTypes"
                                :key="sensorType"
                                :class="[
                                    'p-4 rounded-xl border-2 text-center',
                                    getSensorColor(sensorType),
                                ]"
                            >
                                <div class="text-2xl mb-2">
                                    {{ getSensorIcon(sensorType) }}
                                </div>
                                <div class="font-semibold text-sm">
                                    {{ formatSensorName(sensorType) }}
                                </div>
                                <div class="text-xs opacity-75">
                                    {{ getSensorUnit(sensorType) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div
                        v-if="
                            activeNode && activeNode.timeSeriesData.length > 0
                        "
                        class="bg-white rounded-2xl border border-gray-200 overflow-hidden"
                    >
                        <div
                            class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                        >
                            <h4
                                class="text-lg font-semibold text-gray-800 flex items-center"
                            >
                                <span class="mr-2">📈</span>
                                Data Historis
                                <span
                                    class="ml-2 text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full"
                                >
                                    {{ activeNode.timeSeriesData.length }}
                                    record
                                </span>
                            </h4>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 border-r border-gray-200"
                                        >
                                            <div class="flex items-center">
                                                <span class="mr-2">⏰</span>
                                                Waktu
                                            </div>
                                        </th>
                                        <th
                                            v-for="sensorType in activeNode.sensorTypes"
                                            :key="sensorType"
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            <div class="flex items-center">
                                                <span class="mr-2">{{
                                                    getSensorIcon(sensorType)
                                                }}</span>
                                                {{
                                                    formatSensorName(sensorType)
                                                }}
                                                <span class="ml-1 text-gray-400"
                                                    >({{
                                                        getSensorUnit(
                                                            sensorType
                                                        )
                                                    }})</span
                                                >
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="(
                                            dataPoint, index
                                        ) in activeNode.timeSeriesData"
                                        :key="index"
                                        class="hover:bg-gray-50 transition-colors"
                                    >
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 sticky left-0 bg-white z-10 border-r border-gray-200"
                                        >
                                            {{
                                                formatDate(dataPoint.timestamp)
                                            }}
                                        </td>
                                        <td
                                            v-for="sensorType in activeNode.sensorTypes"
                                            :key="sensorType"
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                        >
                                            <span
                                                v-if="
                                                    dataPoint.sensors[
                                                        sensorType
                                                    ]
                                                "
                                                class="font-medium"
                                            >
                                                {{
                                                    dataPoint.sensors[
                                                        sensorType
                                                    ]
                                                }}
                                            </span>
                                            <span v-else class="text-gray-400"
                                                >-</span
                                            >
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- No Data for Active Node -->
                    <div v-else-if="activeNode" class="text-center py-16">
                        <div
                            class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6"
                        >
                            <span class="text-4xl text-gray-400">📊</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Tidak ada data
                        </h3>
                        <p class="text-gray-600">
                            Tidak ada data sensor yang ditemukan untuk node ini
                        </p>
                    </div>
                </div>

                <!-- No Nodes -->
                <div v-else class="text-center py-16">
                    <div
                        class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <span class="text-4xl text-gray-400">📡</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Belum ada data
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Belum ada data sensor yang tersedia. Coba sesuaikan
                        filter atau periksa koneksi perangkat.
                    </p>
                    <button
                        @click="resetFilter"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-medium"
                    >
                        <span class="mr-2">🔄</span>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

/* Smooth transitions */
* {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Sticky column styling */
.sticky {
    position: sticky;
    z-index: 10;
}

/* Disabled button styling */
button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

button:disabled:hover {
    transform: none;
    box-shadow: none;
}
</style>
