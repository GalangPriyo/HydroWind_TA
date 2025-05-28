<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, computed, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
defineOptions({ layout: AuthenticatedLayout });

// Props untuk menerima data dari controller
const props = defineProps({
    user: Object,
    statsUser: {
        type: Object,
        default: () => ({
            totalUsers: 0,
            usersWithWhatsapp: 0,
        }),
    },
    statsDevice: {
        type: Object,
        default: () => ({
            totalDevices: 0,
            activeDevices: 0,
            inactiveDevices: 0,
            maintenanceDevices: 0,
        }),
    },
    batteryStatuses: {
        type: Array,
        default: () => [],
    },
});

// State untuk menyimpan data battery yang dipolling
const batteryData = ref([]);

// Fungsi polling data baterai dari backend
const fetchBatteryData = async () => {
    try {
        const response = await axios.get("/api/admin/battery-data");
        batteryData.value = response.data;
        console.log("🔋 Battery data fetched:", batteryData.value);
    } catch (error) {
        console.error("❌ Gagal mengambil data baterai:", error);
    }
};

// Inisialisasi polling saat komponen dimount
onMounted(() => {
    fetchBatteryData();
    setInterval(fetchBatteryData, 10000); // polling 10 detik
    console.log("🔋 Initial battery data:", batteryData.value);
});

// Stats computation
const deviceStats = computed(() => {
    const total = props.statsDevice.totalDevices || 0;
    const active = props.statsDevice.activeDevices || 0;
    const inactive = props.statsDevice.inactiveDevices || 0;
    const maintenance = props.statsDevice.maintenanceDevices || 0;

    return { total, active, inactive, maintenance };
});

const userStats = computed(() => {
    const total = props.statsUser.totalUsers || 0;
    const withWhatsapp = props.statsUser.usersWithWhatsapp || 0;
    const withoutWhatsapp = total - withWhatsapp;

    return { total, withWhatsapp, withoutWhatsapp };
});

// Battery level color helper
const getBatteryColor = (level) => {
    if (level === null) return "text-gray-400";
    if (level >= 80) return "text-green-500";
    if (level >= 50) return "text-yellow-500";
    if (level >= 20) return "text-orange-500";
    return "text-red-500";
};

const getBatteryBgColor = (level) => {
    if (level === null) return "bg-gray-100";
    if (level >= 80) return "bg-green-50 border-green-200";
    if (level >= 50) return "bg-yellow-50 border-yellow-200";
    if (level >= 20) return "bg-orange-50 border-orange-200";
    return "bg-red-50 border-red-200";
};

const getBatteryIcon = (level) => {
    if (level === null) return "fa-battery-empty";
    if (level >= 80) return "fa-battery-full";
    if (level >= 50) return "fa-battery-three-quarters";
    if (level >= 20) return "fa-battery-half";
    return "fa-battery-quarter";
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <!-- Welcome Card -->
        <div class="bg-primary rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="p-6 md:p-8 text-white">
                <div
                    class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6"
                >
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">
                            Selamat Datang,
                            <span class="text-yellow-300"
                                >{{ user.name }}!</span
                            >
                        </h1>
                        <p class="mt-2 text-blue-100">
                            Anda login sebagai Administrator Sistem
                        </p>
                    </div>
                    <div class="flex items-center">
                        <div
                            class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm"
                        >
                            <i
                                class="fas fa-user-shield text-white text-3xl"
                            ></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Perangkat & Pengguna Berdampingan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Card Statistik Perangkat -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6"
            >
                <h2
                    class="text-xl font-bold text-gray-900 mb-4 flex items-center"
                >
                    Statistik Perangkat
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        class="bg-gray-50 rounded-xl p-4 flex justify-between items-center hover:shadow-md transition"
                    >
                        <div>
                            <p class="text-sm text-gray-600">Total Perangkat</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ deviceStats.total }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                        >
                            <i
                                class="fa-solid fa-satellite-dish text-blue-600 text-xl"
                            ></i>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 rounded-xl p-4 flex justify-between items-center hover:shadow-md transition"
                    >
                        <div>
                            <p class="text-sm text-gray-600">Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ deviceStats.active }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center"
                        >
                            <i
                                class="fa-solid fa-check text-emerald-600 text-xl"
                            ></i>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 rounded-xl p-4 flex justify-between items-center hover:shadow-md transition"
                    >
                        <div>
                            <p class="text-sm text-gray-600">Tidak Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ deviceStats.inactive }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center"
                        >
                            <i
                                class="fa-solid fa-xmark text-red-600 text-xl"
                            ></i>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 rounded-xl p-4 flex justify-between items-center hover:shadow-md transition"
                    >
                        <div>
                            <p class="text-sm text-gray-600">Maintenance</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ deviceStats.maintenance }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center"
                        >
                            <i
                                class="fa-solid fa-screwdriver-wrench text-amber-600 text-xl"
                            ></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Statistik Pengguna -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6"
            >
                <h2
                    class="text-xl font-bold text-gray-900 mb-4 flex items-center"
                >
                    Statistik Pengguna
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        class="bg-gray-50 rounded-xl p-4 flex justify-between items-center hover:shadow-md transition sm:col-span-2"
                    >
                        <div>
                            <p class="text-sm text-gray-600">Total Pengguna</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ userStats.total }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                        >
                            <i
                                class="fa-solid fa-users text-blue-600 text-xl"
                            ></i>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 rounded-xl p-4 flex justify-between items-center hover:shadow-md transition"
                    >
                        <div>
                            <p class="text-sm text-gray-600">
                                Terhubung WhatsApp
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ userStats.withWhatsapp }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center"
                        >
                            <i
                                class="fa-brands fa-whatsapp text-emerald-600 text-xl"
                            ></i>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 rounded-xl p-4 flex justify-between items-center hover:shadow-md transition"
                    >
                        <div>
                            <p class="text-sm text-gray-600">Belum Terhubung</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ userStats.withoutWhatsapp }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center"
                        >
                            <i
                                class="fa-solid fa-phone-slash text-amber-600 text-xl"
                            ></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Battery Monitoring Section -->
        <div class="mb-8">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
            >
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    Monitoring Baterai Perangkat
                </h2>
                <div class="text-sm text-gray-500 flex items-center">
                    <i class="fa-solid fa-sync-alt mr-1"></i>
                    <span
                        >Update terakhir:
                        {{ new Date().toLocaleTimeString("id-ID") }}</span
                    >
                </div>
            </div>

            <!-- Battery Cards Grid -->
            <div
                v-if="batteryData && batteryData.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6"
            >
                <div
                    v-for="device in batteryData"
                    :key="device.id"
                    :class="[
                        'rounded-2xl shadow-lg border-2 p-6 transition-all duration-300',
                        device.latest_battery
                            ? getBatteryBgColor(device.latest_battery.level)
                            : 'bg-gray-100 border-gray-200',
                    ]"
                >
                    <!-- Device Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <div
                                :class="[
                                    'w-10 h-10 rounded-lg flex items-center justify-center',
                                    device.latest_battery?.level >= 20
                                        ? 'bg-white'
                                        : 'bg-red-100',
                                ]"
                            >
                                <i
                                    :class="[
                                        'fa-solid text-lg',
                                        device.latest_battery
                                            ? getBatteryIcon(
                                                  device.latest_battery.level
                                              )
                                            : 'fa-battery-empty',
                                        device.latest_battery
                                            ? getBatteryColor(
                                                  device.latest_battery.level
                                              )
                                            : 'text-gray-400',
                                    ]"
                                ></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">
                                    {{ device.name }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    ID: {{ device.node_id }}
                                </p>
                            </div>
                        </div>
                        <div
                            v-if="device.latest_battery?.charging"
                            class="flex items-center"
                        >
                            <i
                                class="fa-solid fa-bolt text-yellow-500 text-sm animate-pulse"
                            ></i>
                        </div>
                    </div>

                    <!-- Battery Info -->
                    <template v-if="device.latest_battery">
                        <!-- Battery Level -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700"
                                    >Level Baterai</span
                                >
                                <span
                                    :class="[
                                        'text-lg font-bold',
                                        getBatteryColor(
                                            device.latest_battery.level
                                        ),
                                    ]"
                                >
                                    {{
                                        device.latest_battery.level !== null
                                            ? device.latest_battery.level + "%"
                                            : "N/A"
                                    }}
                                </span>
                            </div>
                            <div
                                v-if="device.latest_battery.level !== null"
                                class="w-full bg-gray-200 rounded-full h-2"
                            >
                                <div
                                    :class="[
                                        'h-2 rounded-full transition-all duration-500',
                                        device.latest_battery.level >= 80
                                            ? 'bg-green-500'
                                            : device.latest_battery.level >= 50
                                            ? 'bg-yellow-500'
                                            : device.latest_battery.level >= 20
                                            ? 'bg-orange-500'
                                            : 'bg-red-500',
                                    ]"
                                    :style="{
                                        width:
                                            device.latest_battery.level + '%',
                                    }"
                                ></div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i
                                        class="fa-solid fa-thermometer-half text-blue-500 text-sm"
                                    ></i>
                                    <span class="text-sm text-gray-600"
                                        >Suhu</span
                                    >
                                </div>
                                <span
                                    class="text-sm font-semibold text-gray-900"
                                >
                                    {{
                                        device.latest_battery.temperature !==
                                        null
                                            ? device.latest_battery
                                                  .temperature + "°C"
                                            : "N/A"
                                    }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i
                                        class="fa-solid fa-plug text-purple-500 text-sm"
                                    ></i>
                                    <span class="text-sm text-gray-600"
                                        >Status</span
                                    >
                                </div>
                                <span
                                    :class="[
                                        'text-sm font-semibold px-2 py-1 rounded-full text-xs',
                                        device.latest_battery.charging
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-600',
                                    ]"
                                >
                                    {{
                                        device.latest_battery.charging
                                            ? "Mengisi"
                                            : "Tidak Mengisi"
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Low Battery Warning -->
                        <div
                            v-if="
                                device.latest_battery.level !== null &&
                                device.latest_battery.level < 20
                            "
                            class="mt-4 p-3 bg-red-100 border border-red-200 rounded-lg"
                        >
                            <div class="flex items-center space-x-2">
                                <i
                                    class="fa-solid fa-exclamation-triangle text-red-600 text-sm"
                                ></i>
                                <span class="text-xs text-red-800 font-medium"
                                    >Baterai Lemah!</span
                                >
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <div class="text-center py-4 text-gray-500 italic">
                            Belum ada data baterai
                        </div>
                    </template>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="bg-white rounded-2xl shadow-lg border border-gray-100 p-12 text-center"
            >
                <div
                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                    <i
                        class="fa-solid fa-battery-empty text-gray-400 text-2xl"
                    ></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Tidak Ada Data Baterai
                </h3>
                <p class="text-gray-500">
                    Belum ada perangkat yang terdaftar atau data baterai belum
                    tersedia.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom animations */
@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Responsive text sizing */
@media (max-width: 640px) {
    .text-2xl {
        font-size: 1.5rem;
    }
    .text-3xl {
        font-size: 1.875rem;
    }
}
</style>
