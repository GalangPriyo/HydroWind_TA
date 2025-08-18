<script setup>
import { Link, Head } from "@inertiajs/vue3";
import { defineProps, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    device: Object,
    user: Object,
});

// Computed properties for better data handling
const statusConfig = computed(() => {
    const configs = {
        active: {
            label: "Aktif",
            color: "bg-emerald-100 text-emerald-800 border-emerald-200",
            icon: "fa-check text-emerald-600",
        },
        inactive: {
            label: "Tidak Aktif",
            color: "bg-red-100 text-red-800 border-red-200",
            icon: "fa-xmark text-red-600",
        },
        maintenance: {
            label: "Maintenance",
            color: "bg-amber-100 text-amber-800 border-amber-200",
            icon: "fa-screwdriver-wrench text-amber-600",
        },
    };
    return configs[props.device.status] || configs.active;
});

const sensorConfig = {
    curah_hujan: {
        label: "Curah Hujan",
        unit: "mm",
        icon: "fa-cloud-showers-heavy",
    },
    ketinggian_air: {
        label: "Ketinggian Air",
        unit: "m",
        icon: "fa-water",
    },
    kecepatan_angin: {
        label: "Kecepatan Angin",
        unit: "km/jam",
        icon: "fa-wind",
    },
    tekanan_udara: {
        label: "Tekanan Udara",
        unit: "hPa",
        icon: "fa-temperature-three-quarters",
    },
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const hasCoordinates = computed(() => {
    return props.device.latitude && props.device.longitude;
});
</script>

<template>
    <Head title="Detail Perangkat" />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <button
            @click="$inertia.visit(route('admin.devices'))"
            class="flex items-center gap-2 text-blue-900 hover:text-blue-600 font-semibold py-2"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span class="hidden sm:inline">Kembali</span>
        </button>

        <div class="flex items-center mt-2 sm:mt-4 mb-2 sm:mb-4 justify-center">
            <div>
                <h1
                    class="text-xl sm:text-2xl text-center font-bold text-gray-900 mb-1"
                >
                    Detail Perangkat
                </h1>
                <p class="text-gray-600 text-center text-sm sm:text-base">
                    Informasi lengkap perangkat monitoring
                </p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div
            class="card bg-white border border-gray-100 shadow-lg overflow-hidden"
        >
            <div class="card-body p-4 sm:p-6">
                <!-- Card Header -->
                <div
                    class="card-title border-b border-gray-200 pb-3 sm:pb-4 mb-2"
                >
                    <h2 class="text-xl sm:text-2xl font-semibold">
                        {{ device.name }}
                    </h2>
                    <p class="text-gray-600 text-xs sm:text-sm">
                        ( {{ device.node_id || "Auto-generated ID" }} )
                    </p>
                </div>

                <!-- Card Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                    <!-- Left Column (Location & Status) -->
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Location Information -->
                        <div class="space-y-3 sm:space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Informasi Lokasi
                            </h3>

                            <div class="space-y-3 sm:space-y-4">
                                <div class="flex items-start">
                                    <div>
                                        <div
                                            class="text-xs sm:text-sm font-medium text-gray-500"
                                        >
                                            Alamat
                                        </div>
                                        <div
                                            class="text-gray-900 text-sm sm:text-base"
                                        >
                                            {{ device.location }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="hasCoordinates"
                                    class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200"
                                >
                                    <div
                                        class="text-xs sm:text-sm font-medium text-gray-500 mb-2"
                                    >
                                        Koordinat GPS
                                    </div>
                                    <div
                                        class="grid grid-cols-2 gap-3 sm:gap-4"
                                    >
                                        <div>
                                            <div
                                                class="text-xs text-gray-500 mb-1"
                                            >
                                                Latitude
                                            </div>
                                            <div
                                                class="font-mono text-xs sm:text-sm text-gray-800 bg-white px-2 sm:px-3 py-1 sm:py-2 rounded border"
                                            >
                                                {{ device.latitude }}
                                            </div>
                                        </div>
                                        <div>
                                            <div
                                                class="text-xs text-gray-500 mb-1"
                                            >
                                                Longitude
                                            </div>
                                            <div
                                                class="font-mono text-xs sm:text-sm text-gray-800 bg-white px-2 sm:px-3 py-1 sm:py-2 rounded border"
                                            >
                                                {{ device.longitude }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-else
                                    class="bg-amber-50 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-amber-200"
                                >
                                    <div
                                        class="flex items-center text-amber-800 text-xs sm:text-sm"
                                    >
                                        Koordinat GPS belum diatur
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-b border-gray-200"></div>

                        <!-- Status & Metadata -->
                        <div class="space-y-3 sm:space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Status & Metadata
                            </h3>

                            <div class="space-y-3 sm:space-y-4">
                                <div
                                    class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-lg sm:rounded-xl border border-gray-200"
                                >
                                    <div class="flex items-center">
                                        <div>
                                            <div
                                                class="text-xs sm:text-sm font-medium text-gray-500"
                                            >
                                                Status
                                            </div>
                                            <div
                                                class="text-gray-900 font-medium text-sm sm:text-base"
                                            >
                                                {{ statusConfig.label }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        :class="[
                                            'px-2 py-1 rounded text-xs sm:text-sm font-medium border',
                                            statusConfig.color,
                                        ]"
                                    >
                                        <i
                                            :class="[
                                                'fa-solid',
                                                statusConfig.icon,
                                            ]"
                                        ></i>
                                    </div>
                                </div>

                                <div class="space-y-3 sm:space-y-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div
                                                class="text-xs sm:text-sm font-medium text-gray-500"
                                            >
                                                Dibuat
                                            </div>
                                            <div
                                                class="text-gray-900 text-sm sm:text-base"
                                            >
                                                {{
                                                    formatDate(
                                                        device.created_at
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <div>
                                            <div
                                                class="text-xs sm:text-sm font-medium text-gray-500"
                                            >
                                                Terakhir Diperbarui
                                            </div>
                                            <div
                                                class="text-gray-900 text-sm sm:text-base"
                                            >
                                                {{
                                                    formatDate(
                                                        device.updated_at
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-200"></div>
                    </div>

                    <!-- Right Column (Sensors) -->
                    <div
                        class="space-y-4 sm:space-y-6 lg:border-l lg:border-gray-200 lg:pl-6"
                    >
                        <h3 class="text-lg font-semibold text-gray-800">
                            Perangkat Terpasang
                        </h3>

                        <div
                            v-if="device.sensors && device.sensors.length > 0"
                            class="space-y-3"
                        >
                            <div
                                v-for="sensor in device.sensors"
                                :key="sensor.id"
                                :class="[
                                    'p-2 sm:p-3 rounded-lg sm:rounded-xl border transition-all duration-200',
                                    sensorConfig[sensor.name]?.color ||
                                        'bg-gray-50 border-gray-200',
                                ]"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-full flex items-center justify-center shadow mr-2 sm:mr-3"
                                        >
                                            <i
                                                :class="[
                                                    'fa-solid',
                                                    sensorConfig[sensor.name]
                                                        ?.icon || 'fa-question',
                                                ]"
                                                class="text-blue-500 text-lg sm:text-xl"
                                            ></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <div
                                                class="font-semibold text-gray-800 text-sm sm:text-base"
                                            >
                                                {{
                                                    sensorConfig[sensor.name]
                                                        ?.label || sensor.name
                                                }}
                                            </div>
                                            <div
                                                class="text-gray-600 text-xs sm:text-sm"
                                            >
                                                Unit:
                                                {{
                                                    sensorConfig[sensor.name]
                                                        ?.unit || "N/A"
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Threshold display -->
                                <div
                                    v-if="sensor.threshold"
                                    class="mt-2 sm:mt-3 pl-10 sm:pl-14 pr-1 sm:pr-2"
                                >
                                    <div class="grid grid-cols-2 gap-2">
                                        <div
                                            class="bg-amber-50 p-2 rounded border border-amber-200"
                                        >
                                            <div
                                                class="text-xs text-amber-700 font-medium"
                                            >
                                                Waspada
                                            </div>
                                            <div
                                                class="font-semibold text-amber-900 text-sm"
                                            >
                                                {{ sensor.threshold.waspada }}
                                                {{
                                                    sensorConfig[sensor.name]
                                                        ?.unit || ""
                                                }}
                                            </div>
                                        </div>
                                        <div
                                            class="bg-red-50 p-2 rounded border border-red-200"
                                        >
                                            <div
                                                class="text-xs text-red-700 font-medium"
                                            >
                                                Bahaya
                                            </div>
                                            <div
                                                class="font-semibold text-red-900 text-sm"
                                            >
                                                {{ sensor.threshold.bahaya }}
                                                {{
                                                    sensorConfig[sensor.name]
                                                        ?.unit || ""
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="mt-2 sm:mt-3 pl-10 sm:pl-14 pr-1 sm:pr-2 text-xs sm:text-sm text-gray-500"
                                >
                                    Belum ada threshold yang ditetapkan
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-6 sm:py-8">
                            <div
                                class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4"
                            >
                                <i
                                    class="fa-solid fa-triangle-exclamation text-xl sm:text-2xl text-gray-400"
                                ></i>
                            </div>
                            <p
                                class="text-gray-500 font-medium text-sm sm:text-base"
                            >
                                Tidak ada sensor terpasang
                            </p>
                            <p class="text-gray-400 text-xs sm:text-sm mt-1">
                                Tambahkan sensor untuk mulai monitoring
                            </p>
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
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Status indicator pulse */
@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Gradient text */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

@media (max-width: 1023px) {
    .lg\:border-l {
        border-left: none !important;
    }
    .lg\:pl-6 {
        padding-left: 0 !important;
    }
}
</style>
