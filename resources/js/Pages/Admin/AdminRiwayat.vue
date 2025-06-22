<script setup>
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import { throttle } from "lodash";

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

// Reactive state for loading status
const isLoading = ref(false);

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

const throttledSubmitFilter = throttle(() => {
    isLoading.value = true;
    router.get(route("admin.riwayat"), form.value, {
        preserveState: true,
        onFinish: () => (isLoading.value = false),
    });
}, 1000);

function submitFilter() {
    throttledSubmitFilter();
}

function resetFilter() {
    form.value = {
        device_id: "",
        sensor_type: "",
        date_from: "",
        date_to: "",
        per_page: 200,
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
    return date.toLocaleString("en-GB", {
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

const confirmTruncate = () => {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Seluruh data sensor akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#aaa",
        confirmButtonText: "Ya, hapus semua!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.riwayat.truncate"), {
                onSuccess: () => {
                    Swal.fire(
                        "Terhapus!",
                        "Data sensor berhasil dihapus.",
                        "success"
                    );
                },
                onError: () => {
                    Swal.fire(
                        "Gagal",
                        "Terjadi kesalahan saat menghapus data.",
                        "error"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Riwayat Sensor" />
    <div class="py-2">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <!-- Header Section -->
            <div class="mb-8">
                <div
                    class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 mb-2 flex items-center"
                        >
                            Data Riwayat Sensor
                        </h1>
                        <p class="text-gray-600">
                            Melihat data riwayat sensor dari semua perangkat
                            yang terdaftar
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <a
                            :href="downloadUrl"
                            class="btn border-2 border-gray-200 bg-green-600 text-white text-sm rounded-xl hover:bg-green-700 transition-all duration-200 font-medium"
                        >
                            <i class="fa-solid fa-download"></i> Download Data
                        </a>
                        <button
                            @click="confirmTruncate"
                            class="btn border-2 border-gray-200 bg-red-600 text-white text-sm rounded-xl hover:bg-red-700 transition-all duration-200 font-medium"
                        >
                            <i class="fa-solid fa-trash-can"></i> Hapus Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6"
            >
                <form @submit.prevent="submitFilter" class="px-6 pb-6 pt-3">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Device -->
                        <div class="form-control">
                            <label class="label" for="device">
                                <span class="label-text text-sm"
                                    >Perangkat</span
                                >
                            </label>
                            <select
                                id="device"
                                v-model="form.device_id"
                                class="select select-sm text-sm py-0 px-3 select-bordered w-full rounded-lg"
                            >
                                <option value="">Semua Perangkat</option>
                                <option
                                    v-for="device in devices"
                                    :key="device.id"
                                    :value="device.id"
                                >
                                    {{ device.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Sensor Type -->
                        <div class="form-control">
                            <label class="label" for="sensor_type">
                                <span class="label-text text-sm"
                                    >Jenis Sensor</span
                                >
                            </label>
                            <select
                                id="sensor_type"
                                v-model="form.sensor_type"
                                class="select select-sm text-sm py-0 px-3 select-bordered w-full rounded-lg"
                            >
                                <option value="">Semua Sensor</option>
                                <option value="curah_hujan">Curah Hujan</option>
                                <option value="ketinggian_air">
                                    Ketinggian Air
                                </option>
                                <option value="kecepatan_angin">
                                    Kecepatan Angin
                                </option>
                                <option value="tekanan_udara">
                                    Tekanan Udara
                                </option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div class="form-control">
                            <label class="label" for="date_from">
                                <span class="label-text text-sm"
                                    >Tanggal Mulai</span
                                >
                            </label>
                            <input
                                id="date_from"
                                type="date"
                                v-model="form.date_from"
                                class="input input-sm input-bordered w-full rounded-lg"
                            />
                        </div>

                        <!-- Date To -->
                        <div class="form-control">
                            <label class="label" for="date_to">
                                <span class="label-text text-sm"
                                    >Tanggal Akhir</span
                                >
                            </label>
                            <input
                                id="date_to"
                                type="date"
                                v-model="form.date_to"
                                class="input input-sm input-bordered w-full rounded-lg"
                            />
                        </div>

                        <!-- Buttons -->
                        <div class="form-control flex flex-col justify-end">
                            <div class="flex gap-1">
                                <button
                                    type="button"
                                    @click="resetFilter"
                                    class="btn btn-sm bg-white hover:bg-red-500 border border-red-500 rounded-lg w-1/2 text-red-500 hover:text-white transition-colors"
                                >
                                    <i
                                        class="fa-solid fa-arrow-rotate-left"
                                    ></i>
                                    Reset
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white w-1/2 rounded-lg"
                                >
                                    <i class="fa-solid fa-filter"></i>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Node Data Section -->
            <div v-if="nodeData.length > 0">
                <!-- Node Navigation -->
                <div
                    class="flex justify-between items-center mb-6 bg-white rounded-2xl shadow-lg border border-gray-100 p-4"
                >
                    <button
                        @click="prevNode"
                        class="bg-gray-100 p-2 rounded-xl hover:bg-gray-200 transition-colors"
                    >
                        <!-- Prev Icon -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </button>

                    <div class="text-center">
                        <h1 class="text-xl font-medium">
                            {{ activeNode?.deviceName || "Tanpa nama" }}
                        </h1>
                        <span class="text-sm font-normal text-gray-500">
                            (Node ID:
                            {{ activeNode?.nodeId || "Tanpa ID" }})
                        </span>
                    </div>

                    <button
                        @click="nextNode"
                        class="bg-gray-100 p-2 rounded-xl hover:bg-gray-200 transition-colors"
                    >
                        <!-- Next Icon -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Tabel Data -->
                <div
                    class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
                    v-if="activeNode"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider sticky left-0 bg-gray-50"
                                    >
                                        Waktu
                                    </th>
                                    <th
                                        v-for="sensorType in activeNode.sensorTypes"
                                        :key="sensorType"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                    >
                                        {{ formatSensorName(sensorType) }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="(
                                        dataPoint, index
                                    ) in activeNode.timeSeriesData"
                                    :key="index"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 sticky left-0 bg-white"
                                    >
                                        {{ formatDate(dataPoint.timestamp) }}
                                    </td>
                                    <td
                                        v-for="sensorType in activeNode.sensorTypes"
                                        :key="sensorType"
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            dataPoint.sensors[sensorType] || "-"
                                        }}
                                        {{ getSensorUnit(sensorType) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div
                            class="flex justify-between items-center bg-gray-50 p-3 border-t border-gray-200"
                        >
                            <div class="text-sm text-gray-600">
                                Menampilkan halaman
                                {{ sensorData.current_page }} dari
                                {{ sensorData.last_page }}
                            </div>
                            <div class="join">
                                <button
                                    @click="
                                        handlePageChange(
                                            sensorData.current_page - 1
                                        )
                                    "
                                    :disabled="sensorData.current_page === 1"
                                    class="join-item btn btn-sm"
                                >
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <button class="join-item btn btn-sm">
                                    {{ sensorData.current_page }}
                                </button>
                                <button
                                    @click="
                                        handlePageChange(
                                            sensorData.current_page + 1
                                        )
                                    "
                                    :disabled="
                                        sensorData.current_page ===
                                        sensorData.last_page
                                    "
                                    class="join-item btn btn-sm"
                                >
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="text-center py-16 bg-white rounded-2xl shadow-xl border border-gray-100"
                >
                    <p class="text-gray-500">Tidak ada data yang ditemukan</p>
                </div>
            </div>

            <!-- No Data -->
            <div
                v-else
                class="text-center py-16 bg-white rounded-2xl shadow-xl border border-gray-100"
            >
                <i
                    class="fas fa-exclamation-triangle text-6xl text-gray-500 mb-2"
                ></i>
                <p class="text-gray-500">Tidak ada data yang ditemukan</p>
            </div>
        </div>
        <!-- Tambahkan di template (sebelum </div> penutup) -->
        <div
            v-if="isLoading"
            class="fixed inset-0 bg-black bg-opacity-30 z-50 flex items-center justify-center"
        >
            <div
                class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center"
            >
                <progress
                    class="progress progress-primary w-56 mb-2"
                ></progress>
                <span>Memuat data...</span>
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
</style>
