<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    device: Object,
    user: Object,
    thresholds: Object,
    defaultThresholds: Object,
});

const form = useForm({
    name: props.device.name,
    location: props.device.location,
    latitude: props.device.latitude,
    longitude: props.device.longitude,
    node_id: props.device.node_id,
    status: props.device.status,
    sensors: props.device.sensors.map((sensor) => ({
        id: sensor.id,
        name: sensor.name,
    })),
    thresholds: props.device.sensors.map((sensor) => ({
        sensor_id: sensor.id,
        waspada:
            props.thresholds[sensor.name]?.waspada ||
            props.defaultThresholds[sensor.name]?.waspada ||
            0,
        bahaya:
            props.thresholds[sensor.name]?.bahaya ||
            props.defaultThresholds[sensor.name]?.bahaya ||
            0,
    })),
});

const availableSensors = [
    {
        value: "curah_hujan",
        label: "Curah Hujan",
        unit: "mm",
        icon: "fa-cloud-showers-heavy",
        description: "Mengukur intensitas curah hujan",
    },
    {
        value: "ketinggian_air",
        label: "Ketinggian Air",
        unit: "m",
        icon: "fa-water",
        description: "Memantau level ketinggian air",
    },
    {
        value: "kecepatan_angin",
        label: "Kecepatan Angin",
        unit: "km/jam",
        icon: "fa-wind",
        description: "Mengukur kecepatan angin",
    },
    {
        value: "tekanan_udara",
        label: "Tekanan Udara",
        unit: "hPa",
        icon: "fa-temperature-three-quarters",
        description: "Memantau tekanan udara",
    },
];

const selectedSensors = ref(props.device.sensors.map((sensor) => sensor.name));
const sensorThresholds = ref({});
const errors = computed(() => form.errors);
const processing = computed(() => form.processing);

// Initialize thresholds
const initializeThresholds = () => {
    props.device.sensors.forEach((sensor) => {
        sensorThresholds.value[sensor.name] = {
            waspada:
                props.thresholds[sensor.name]?.waspada ||
                props.defaultThresholds[sensor.name]?.waspada ||
                0,
            bahaya:
                props.thresholds[sensor.name]?.bahaya ||
                props.defaultThresholds[sensor.name]?.bahaya ||
                0,
        };
    });
};

initializeThresholds();

// Update form.sensors and form.thresholds when selectedSensors changes
watch(
    [selectedSensors, sensorThresholds],
    ([newSelected, newThresholds]) => {
        form.sensors = newSelected.map((name) => {
            const existingSensor = props.device.sensors.find(
                (s) => s.name === name
            );
            return existingSensor ? { id: existingSensor.id, name } : { name };
        });

        form.thresholds = newSelected.map((name) => ({
            sensor_id:
                props.device.sensors.find((s) => s.name === name)?.id || null,
            waspada:
                newThresholds[name]?.waspada ||
                props.defaultThresholds[name]?.waspada ||
                0,
            bahaya:
                newThresholds[name]?.bahaya ||
                props.defaultThresholds[name]?.bahaya ||
                0,
        }));
    },
    { deep: true }
);

const updateDevice = () => {
    if (selectedSensors.value.length === 0) {
        alert("Pilih minimal satu sensor.");
        return;
    }

    form.put(route("admin.devices.update", props.device.id), {
        onSuccess: () => {
            // Optional: Add success notification
        },
        onError: (errors) => {
            console.log("Validasi gagal:", errors);
        },
    });
};

// Toggle threshold form visibility
const showThresholdForm = ref(false);
const toggleThresholdForm = () => {
    showThresholdForm.value = !showThresholdForm.value;
};

// Update form thresholds when sensor thresholds change
const updateFormThresholds = () => {
    form.thresholds = selectedSensors.value.map((name) => ({
        sensor_id:
            props.device.sensors.find((s) => s.name === name)?.id || null,
        waspada: sensorThresholds.value[name].waspada,
        bahaya: sensorThresholds.value[name].bahaya,
    }));
};
</script>

<template>
    <Head title="Edit Perangkat" />
    <div class="max-w-6xl mx-auto">
        <button
            @click="$inertia.visit(route('admin.devices'))"
            class="flex items-center gap-2 text-blue-900 hover:text-blue-600 font-semibold"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>

        <div class="flex items-center mt-4 mb-4 justify-center">
            <div>
                <h1 class="text-2xl text-center font-bold text-gray-900 mb-1">
                    Ubah Perangkat
                </h1>
                <p class="text-gray-600 text-center">
                    Perbarui detail dan informasi perangkat monitoring yang
                    sudah terdaftar
                </p>
            </div>
        </div>
        <div
            class="card bg-white border border-gray-100 w-full shadow-xl self-center my-2"
        >
            <div class="card-body px-4 py-6">
                <form @submit.prevent="updateDevice" class="p-4 space-y-8">
                    <!-- Device Information Section -->
                    <div
                        class="grid grid-cols-1 lg:grid-cols-2 gap-8 border-t border-gray-200 pt-8"
                    >
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Node ID -->
                            <div class="group">
                                <label
                                    for="node_id"
                                    class="flex items-center text-sm font-semibold text-gray-700 mb-3"
                                >
                                    Node ID
                                </label>
                                <div class="relative">
                                    <input
                                        v-model="form.node_id"
                                        type="text"
                                        id="node_id"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none"
                                        placeholder="Contoh: NODE-001"
                                        disabled
                                    />
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                                    ></div>
                                </div>
                                <div
                                    class="mt-2 flex items-center text-xs text-gray-500"
                                >
                                    Node ID tidak dapat diubah.
                                </div>
                                <div
                                    v-if="errors.node_id"
                                    class="mt-2 flex items-center text-sm text-red-600"
                                >
                                    {{ errors.node_id }}
                                </div>
                            </div>

                            <!-- Device Name -->
                            <div class="group">
                                <label
                                    for="name"
                                    class="flex items-center text-sm font-semibold text-gray-700 mb-3"
                                >
                                    Nama Alat
                                </label>
                                <div class="relative">
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        id="name"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-gray-400"
                                        required
                                        placeholder="Contoh: Titik Pantau 1"
                                    />
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                                    ></div>
                                </div>
                                <div
                                    v-if="errors.name"
                                    class="mt-2 flex items-center text-sm text-red-600"
                                >
                                    {{ errors.name }}
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="group">
                                <label
                                    for="location"
                                    class="flex items-center text-sm font-semibold text-gray-700 mb-3"
                                >
                                    Lokasi
                                </label>
                                <div class="relative">
                                    <input
                                        v-model="form.location"
                                        type="text"
                                        id="location"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-gray-400"
                                        required
                                        placeholder="Contoh: Jl. Raya Malang No. 123"
                                    />
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                                    ></div>
                                </div>
                                <div
                                    v-if="errors.location"
                                    class="mt-2 flex items-center text-sm text-red-600"
                                >
                                    {{ errors.location }}
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Coordinates -->
                            <div
                                class="bg-gray-50 rounded-xl p-6 border border-gray-200"
                            >
                                <h3
                                    class="flex items-center text-sm font-semibold text-gray-700 mb-4"
                                >
                                    Koordinat GPS
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            for="latitude"
                                            class="block text-sm font-medium text-gray-600 mb-2"
                                        >
                                            Latitude
                                        </label>
                                        <input
                                            v-model="form.latitude"
                                            type="text"
                                            id="latitude"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200"
                                            placeholder="-7.2574719"
                                        />
                                        <div
                                            v-if="errors.latitude"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ errors.latitude }}
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            for="longitude"
                                            class="block text-sm font-medium text-gray-600 mb-2"
                                        >
                                            Longitude
                                        </label>
                                        <input
                                            v-model="form.longitude"
                                            type="text"
                                            id="longitude"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200"
                                            placeholder="112.752088"
                                        />
                                        <div
                                            v-if="errors.longitude"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ errors.longitude }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="group">
                                <label
                                    for="status"
                                    class="flex items-center text-sm font-semibold text-gray-700 mb-3"
                                >
                                    Status Perangkat
                                </label>
                                <div class="relative">
                                    <select
                                        v-model="form.status"
                                        id="status"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 appearance-none bg-white"
                                    >
                                        <option value="active">Aktif</option>
                                        <option value="inactive">
                                            Tidak Aktif
                                        </option>
                                        <option value="maintenance">
                                            Maintenance
                                        </option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                                    ></div>
                                </div>
                                <div
                                    v-if="errors.status"
                                    class="mt-2 flex items-center text-sm text-red-600"
                                >
                                    {{ errors.status }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sensors Section -->
                    <div class="border-t border-gray-200 pt-8">
                        <div class="mb-6">
                            <h3
                                class="flex items-center text-lg font-semibold text-gray-800 mb-2"
                            >
                                Konfigurasi Sensor
                            </h3>
                            <p class="text-gray-600 flex items-center">
                                Pilih sensor yang terpasang pada alat (minimal
                                1, maksimal 4)
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="sensor in availableSensors"
                                :key="sensor.value"
                                class="relative"
                            >
                                <input
                                    :id="`sensor-${sensor.value}`"
                                    type="checkbox"
                                    :value="sensor.value"
                                    v-model="selectedSensors"
                                    class="sr-only peer"
                                />
                                <label
                                    :for="`sensor-${sensor.value}`"
                                    class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-blue-300 hover:bg-blue-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-4 peer-checked:ring-blue-100"
                                >
                                    <div
                                        class="flex items-center justify-center w-10 h-10 bg-white rounded-lg border border-blue-500 mr-4 peer-checked:bg-blue-500 peer-checked:border-blue-500 transition-all duration-200"
                                    >
                                        <span
                                            class="text-lg peer-checked:hidden"
                                            ><i
                                                :class="[
                                                    'fa-solid text-blue-500',
                                                    sensor.icon,
                                                ]"
                                            ></i>
                                        </span>
                                        <span
                                            class="text-white hidden peer-checked:block"
                                            ><i
                                                class="fa-solid fa-circle-check text-lg"
                                            ></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-semibold text-gray-800 mb-1"
                                        >
                                            {{ sensor.label }}
                                        </div>
                                        <div class="text-sm text-gray-600 mb-1">
                                            {{ sensor.description }}
                                        </div>
                                        <div
                                            class="text-xs text-blue-600 font-medium"
                                        >
                                            Unit: {{ sensor.unit }}
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Threshold Configuration Button -->
                        <div class="mt-6">
                            <button
                                type="button"
                                @click="toggleThresholdForm"
                                class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-2"
                            >
                                <i class="fa-solid fa-sliders"></i>
                                <span>Atur Threshold Sensor</span>
                            </button>
                        </div>

                        <!-- Threshold Form -->
                        <div
                            v-if="
                                showThresholdForm && selectedSensors.length > 0
                            "
                            class="mt-6 bg-blue-50 p-6 rounded-xl"
                        >
                            <h4
                                class="text-lg font-semibold text-gray-800 mb-4"
                            >
                                Atur Threshold
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div
                                    v-for="sensorType in selectedSensors"
                                    :key="sensorType"
                                >
                                    <div
                                        class="bg-white p-4 rounded-lg border border-blue-500"
                                    >
                                        <h5
                                            class="font-medium text-gray-700 mb-3"
                                        >
                                            {{
                                                availableSensors.find(
                                                    (s) =>
                                                        s.value === sensorType
                                                )?.label
                                            }}
                                        </h5>

                                        <div class="space-y-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-600 mb-1"
                                                >
                                                    Nilai Waspada
                                                </label>
                                                <input
                                                    type="number"
                                                    v-model="
                                                        sensorThresholds[
                                                            sensorType
                                                        ].waspada
                                                    "
                                                    @change="
                                                        updateFormThresholds
                                                    "
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                    min="0"
                                                    step="0.01"
                                                />
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-600 mb-1"
                                                >
                                                    Nilai Bahaya
                                                </label>
                                                <input
                                                    type="number"
                                                    v-model="
                                                        sensorThresholds[
                                                            sensorType
                                                        ].bahaya
                                                    "
                                                    @change="
                                                        updateFormThresholds
                                                    "
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                    :min="
                                                        sensorThresholds[
                                                            sensorType
                                                        ].waspada + 0.01
                                                    "
                                                    step="0.01"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="errors.sensors"
                            class="mt-4 flex items-center text-sm text-red-600 bg-red-50 p-3 rounded-lg"
                        >
                            {{ errors.sensors }}
                        </div>
                        <div
                            v-if="selectedSensors.length === 0"
                            class="mt-4 flex items-center text-sm text-red-600 bg-red-50 p-3 rounded-lg"
                        >
                            Minimal pilih 1 sensor untuk melanjutkan
                        </div>
                        <div
                            v-if="selectedSensors.length > 0"
                            class="mt-4 flex items-center text-sm text-green-600 bg-green-50 p-3 rounded-lg"
                        >
                            {{ selectedSensors.length }} sensor dipilih
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="border-t border-gray-200 pt-8">
                        <div
                            class="flex flex-col sm:flex-row gap-4 justify-end"
                        >
                            <button
                                type="button"
                                @click="$inertia.visit(route('admin.devices'))"
                                class="btn text-gray-700 bg-gray-100 hover:bg-gray-200 transition-all duration-200 font-medium flex items-center justify-center"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="btn text-white bg-blue-600 hover:bg-blue-700 transition-all duration-200 font-medium flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-xl"
                                :disabled="
                                    processing || selectedSensors.length === 0
                                "
                            >
                                <span v-if="processing">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom checkbox styling */
input[type="checkbox"]:checked + label .peer-checked\:bg-blue-500 {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

input[type="checkbox"]:checked + label .peer-checked\:block {
    display: block;
}

input[type="checkbox"]:checked + label .peer-checked\:hidden {
    display: none;
}

/* Smooth transitions */
* {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Focus states */
input:focus,
select:focus {
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

/* Hover effects */
.group:hover label {
    color: #374151;
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
