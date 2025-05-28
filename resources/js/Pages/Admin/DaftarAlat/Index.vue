<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

// Props untuk menerima data dari controller
const props = defineProps({
    devices: {
        type: Object,
        default: () => ({ data: [] }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    stats: {
        type: Object,
        default: () => ({
            totalDevices: 0,
            activeDevices: 0,
            inactiveDevices: 0,
            maintenanceDevices: 0,
        }),
    },
    user: Object,
});

// View mode state
const viewMode = ref("grid"); // 'grid' or 'table'

// Format status untuk tampilan
const getStatusConfig = (status) => {
    switch (status) {
        case "active":
            return {
                text: "Aktif",
                bgColor: "bg-emerald-100",
                textColor: "text-emerald-800",
                borderColor: "border-emerald-200",
                dotColor: "bg-emerald-500",
            };
        case "inactive":
            return {
                text: "Tidak Aktif",
                bgColor: "bg-red-100",
                textColor: "text-red-800",
                borderColor: "border-red-200",
                dotColor: "bg-red-500",
            };
        case "maintenance":
            return {
                text: "Maintenance",
                bgColor: "bg-amber-100",
                textColor: "text-amber-800",
                borderColor: "border-amber-200",
                dotColor: "bg-amber-500",
            };
        default:
            return {
                text: status,
                bgColor: "bg-blue-100",
                textColor: "text-blue-800",
                borderColor: "border-blue-200",
                dotColor: "bg-blue-500",
            };
    }
};

const sensorConfig = {
    curah_hujan: {
        label: "Curah Hujan",
    },
    ketinggian_air: {
        label: "Ketinggian Air",
    },
    kecepatan_angin: {
        label: "Kecepatan Angin",
    },
    tekanan_udara: {
        label: "Tekanan Udara",
    },
};
const searchQuery = ref(props.filters.search || "");

watch(searchQuery, (newVal) => {
    router.get(
        route("admin.devices"),
        { search: newVal },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
});

// Konfirmasi hapus dengan SweetAlert
const confirmDelete = (device) => {
    Swal.fire({
        title: "Hapus Perangkat",
        text: `Apakah Anda benar-benar ingin menghapus perangkat '${device.name}'?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
        customClass: {
            popup: "rounded-2xl",
            confirmButton: "rounded-xl",
            cancelButton: "rounded-xl",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.devices.destroy", device.id));
        }
    });
};

// Format tanggal
const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString("id-ID", options);
};

// Stats computation
const deviceStats = computed(() => {
    const total = props.stats.totalDevices || 0;
    const active = props.stats.activeDevices || 0;
    const inactive = props.stats.inactiveDevices || 0;
    const maintenance = props.stats.maintenanceDevices || 0;

    return { total, active, inactive, maintenance };
});

// Computed property untuk pagination links yang sudah difilter
const paginationLinks = computed(() => {
    return props.devices.links.filter((link) => link.url !== "...");
});
</script>

<template>
    <Head title="Daftar Perangkat" />

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
                            Daftar Perangkat
                        </h1>
                        <p class="text-gray-600">
                            Kelola dan pantau semua perangkat monitoring Anda
                        </p>
                    </div>

                    <!-- Search and Actions -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 lg:items-center"
                    >
                        <div class="relative">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-600"
                            ></i>
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Cari perangkat"
                                class="w-full sm:w-80 pl-8 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 bg-white"
                            />
                        </div>

                        <div class="flex gap-3">
                            <!-- View Mode Toggle -->
                            <div
                                class="flex bg-white rounded-xl border-2 border-gray-200 p-1"
                            >
                                <button
                                    @click="viewMode = 'grid'"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                        viewMode === 'grid'
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'text-gray-600 hover:text-gray-800',
                                    ]"
                                >
                                    <span
                                        ><i
                                            class="fa-solid fa-table-cells-large"
                                        ></i
                                    ></span>
                                </button>
                                <button
                                    @click="viewMode = 'table'"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                        viewMode === 'table'
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'text-gray-600 hover:text-gray-800',
                                    ]"
                                >
                                    <span
                                        ><i class="fa-solid fa-bars"></i
                                    ></span>
                                </button>
                            </div>

                            <Link
                                :href="route('admin.devices.create')"
                                class="btn border-2 border-gray-200 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium"
                            >
                                <span><i class="fa-solid fa-plus"></i></span>
                                Tambah Perangkat
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
            >
                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Total Perangkat
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ deviceStats.total }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl"
                                ><i
                                    class="fa-solid fa-satellite-dish text-blue-600"
                                ></i
                            ></span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Aktif
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ deviceStats.active }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl"
                                ><i
                                    class="fa-solid fa-check text-emerald-600"
                                ></i
                            ></span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Tidak Aktif
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ deviceStats.inactive }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl"
                                ><i class="fa-solid fa-xmark text-red-600"></i
                            ></span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Maintenance
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ deviceStats.maintenance }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl"
                                ><i
                                    class="fa-solid fa-screwdriver-wrench text-amber-600"
                                ></i
                            ></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div
                class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
            >
                <!-- Empty State -->
                <div
                    v-if="!devices.data || devices.data.length === 0"
                    class="text-center py-16"
                >
                    <div
                        class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <span class="text-4xl text-gray-400"
                            ><i class="fa-solid fa-satellite-dish"></i
                        ></span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Belum ada perangkat
                    </h3>
                    <p class="text-gray-600">
                        Mulai tambahkan perangkat monitoring
                    </p>
                </div>

                <!-- Grid View -->
                <div v-else-if="viewMode === 'grid'" class="p-6">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="device in devices.data"
                            :key="device.id"
                            class="bg-white border-2 border-gray-200 rounded-2xl p-6 hover:border-blue-300 hover:shadow-lg transition-all duration-200 group"
                        >
                            <!-- Device Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3"
                                        >
                                            <span class="text-blue-600"
                                                ><i
                                                    class="fa-solid fa-satellite-dish text-blue-600"
                                                ></i
                                            ></span>
                                        </div>
                                        <div>
                                            <h3
                                                class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors"
                                            >
                                                {{ device.name }}
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                {{ device.node_id }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="mb-4">
                                <div class="flex text-sm text-gray-600 mb-1">
                                    <span
                                        class="mr-3 tooltip text-base"
                                        data-tip="Lokasi"
                                        ><i class="fa-solid fa-location-dot"></i
                                    ></span>
                                    {{ device.location }}
                                </div>
                                <div
                                    v-if="device.latitude && device.longitude"
                                    class="text-xs text-gray-400 ml-6"
                                >
                                    {{ device.latitude }},
                                    {{ device.longitude }}
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <span
                                    class="mr-3 tooltip text-gray-600"
                                    data-tip="Status"
                                    ><i class="fa-solid fa-circle-info"></i
                                ></span>
                                <span
                                    :class="[
                                        'inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium',
                                        getStatusConfig(device.status).bgColor,
                                        getStatusConfig(device.status)
                                            .textColor,
                                        getStatusConfig(device.status)
                                            .borderColor,
                                    ]"
                                >
                                    {{ getStatusConfig(device.status).text }}
                                </span>
                            </div>

                            <!-- Sensors -->
                            <div class="mb-4">
                                <div class="flex text-sm text-gray-600 mb-1">
                                    <span
                                        class="mr-3 tooltip text-gray-600 text-base"
                                        data-tip="Sensor"
                                        ><i class="fa-solid fa-wrench"></i
                                    ></span>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="sensor in device.sensors?.slice(
                                                0,
                                                4
                                            )"
                                            :key="sensor.id"
                                            :class="[
                                                'px-2 py-1 text-xs rounded-lg font-medium',
                                                sensorConfig[sensor.name]
                                                    ?.color ||
                                                    'bg-gray-100 text-gray-800',
                                            ]"
                                        >
                                            {{
                                                sensorConfig[sensor.name]
                                                    ?.label || sensor.name
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="mb-4">
                                <span
                                    class="mr-3 tooltip text-gray-600"
                                    data-tip="Tanggal Dibuat"
                                    ><i class="fa-solid fa-calendar-days"></i
                                ></span>
                                <span class="text-sm text-gray-600">{{
                                    formatDate(device.created_at)
                                }}</span>
                            </div>

                            <!-- Actions -->
                            <div
                                class="flex gap-2 justify-center mt-4 pt-4 border-t border-gray-100"
                            >
                                <Link
                                    :href="
                                        route('admin.devices.show', device.id)
                                    "
                                    class="text-center border border-purple-600 px-3 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors text-sm font-medium tooltip"
                                    data-tip="Detail"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </Link>
                                <Link
                                    :href="
                                        route('admin.devices.edit', device.id)
                                    "
                                    class="text-center border border-amber-600 px-3 py-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors text-sm font-medium tooltip"
                                    data-tip="Edit"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </Link>
                                <button
                                    @click="confirmDelete(device)"
                                    class="text-center border border-red-600 px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium tooltip"
                                    data-tip="Hapus"
                                >
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table View -->
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="w-[170px] px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Perangkat
                                </th>
                                <th
                                    class="w-[270px] px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Lokasi
                                </th>
                                <th
                                    class="w-[120px] px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="w-[160px] px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Sensor
                                </th>
                                <th
                                    class="w-[130px] px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Tanggal
                                </th>
                                <th
                                    class="w-[90px] px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="device in devices.data"
                                :key="device.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                {{ device.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ device.node_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ device.location }}
                                    </div>
                                    <div
                                        v-if="
                                            device.latitude && device.longitude
                                        "
                                        class="text-xs text-gray-500"
                                    >
                                        {{ device.latitude }},
                                        {{ device.longitude }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium border',
                                            getStatusConfig(device.status)
                                                .bgColor,
                                            getStatusConfig(device.status)
                                                .textColor,
                                            getStatusConfig(device.status)
                                                .borderColor,
                                        ]"
                                    >
                                        {{
                                            getStatusConfig(device.status).text
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="sensor in device.sensors?.slice(
                                                0,
                                                2
                                            )"
                                            :key="sensor.id"
                                            :class="[
                                                'px-2 py-1 text-xs rounded-lg font-medium',
                                                sensorConfig[sensor.name]
                                                    ?.color ||
                                                    'bg-gray-100 text-gray-800',
                                            ]"
                                        >
                                            {{
                                                sensorConfig[sensor.name]
                                                    ?.label || sensor.name
                                            }}
                                        </span>
                                        <span
                                            v-if="device.sensors?.length > 2"
                                            class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg"
                                        >
                                            +{{ device.sensors.length - 2 }}
                                        </span>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-gray-500 text-center"
                                >
                                    {{ formatDate(device.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="
                                                route(
                                                    'admin.devices.show',
                                                    device.id
                                                )
                                            "
                                            class="text-center border border-purple-600 px-2 py-1 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors text-sm font-medium tooltip"
                                            data-tip="Detail"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'admin.devices.edit',
                                                    device.id
                                                )
                                            "
                                            class="text-center border border-amber-600 px-2 py-1 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors text-sm font-medium tooltip"
                                            data-tip="Edit"
                                        >
                                            <i
                                                class="fa-solid fa-pen-to-square"
                                            ></i>
                                        </Link>
                                        <button
                                            @click="confirmDelete(device)"
                                            class="text-center border border-red-600 px-2 py-1 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium tooltip"
                                            data-tip="Hapus"
                                        >
                                            <i
                                                class="fa-solid fa-trash-can"
                                            ></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div
                        class="flex flex-col sm:flex-row justify-between items-center gap-4"
                    >
                        <div class="text-sm text-gray-700">
                            <span
                                v-if="
                                    devices.from && devices.to && devices.total
                                "
                            >
                                Menampilkan {{ devices.from }} -
                                {{ devices.to }} dari {{ devices.total }} data
                            </span>
                            <span v-else>Tidak ada data untuk ditampilkan</span>
                        </div>
                        <div
                            v-if="devices.links && devices.links.length > 3"
                            class="flex gap-1"
                        >
                            <Link
                                v-for="(link, i) in paginationLinks"
                                :key="`link-${i}`"
                                :href="link.url || ''"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                                    link.active
                                        ? 'bg-blue-500 text-white shadow-md'
                                        : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
                                ]"
                            >
                                <template v-if="link.label.includes('laquo')">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </template>
                                <template
                                    v-else-if="link.label.includes('raquo')"
                                >
                                    <i class="fa-solid fa-chevron-right"></i>
                                </template>
                                <template v-else>
                                    {{ link.label }}
                                </template>
                            </Link>
                        </div>
                    </div>
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

/* Hover effects */
.group:hover .group-hover\:text-blue-600 {
    color: #2563eb;
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
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
