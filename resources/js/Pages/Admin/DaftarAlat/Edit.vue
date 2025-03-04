<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    device: Object,
    user: Object,
});

const form = useForm({
    name: props.device.name,
    location: props.device.location,
    latitude: props.device.latitude,
    longitude: props.device.longitude,
    sensors: props.device.sensors.map((sensor) => ({
        id: sensor.id,
        name: sensor.name,
        unit: sensor.unit,
    })),
});

// Tambah sensor baru (max 3)
const addSensor = () => {
    if (form.sensors.length < 3) {
        form.sensors.push({ id: null, name: "", unit: "" });
    }
};

// Hapus sensor (min 1)
const removeSensor = (index) => {
    if (form.sensors.length > 1) {
        form.sensors.splice(index, 1);
    }
};

// Submit form
const submit = () => {
    form.put(route("admin.devices.update", props.device.id));
};
</script>

<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Edit Device</h1>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block">Nama Device</label>
                <input
                    v-model="form.name"
                    class="w-full border p-2 rounded"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block">Lokasi</label>
                <input
                    v-model="form.location"
                    class="w-full border p-2 rounded"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block">Latitude</label>
                <input
                    v-model="form.latitude"
                    type="number"
                    step="0.000001"
                    class="w-full border p-2 rounded"
                />
            </div>

            <div class="mb-4">
                <label class="block">Longitude</label>
                <input
                    v-model="form.longitude"
                    type="number"
                    step="0.000001"
                    class="w-full border p-2 rounded"
                />
            </div>

            <h2 class="text-lg font-semibold mt-4">Sensors</h2>
            <div
                v-for="(sensor, index) in form.sensors"
                :key="index"
                class="mb-4 border p-4 rounded bg-white"
            >
                <label class="block">Nama Sensor</label>
                <select v-model="sensor.name" class="w-full border p-2 rounded">
                    <option value="rain_intensity">Rain Intensity</option>
                    <option value="water_level">Water Level</option>
                    <option value="wind_speed">Wind Speed</option>
                    <option value="wind_direction">Wind Direction</option>
                </select>

                <label class="block mt-2">Satuan</label>
                <input
                    v-model="sensor.unit"
                    class="w-full border p-2 rounded"
                    required
                />

                <button
                    type="button"
                    @click="removeSensor(index)"
                    class="mt-2 bg-red-600 text-white px-2 py-1 rounded"
                    v-if="form.sensors.length > 1"
                >
                    Hapus Sensor
                </button>
            </div>

            <button
                type="button"
                @click="addSensor"
                class="mr-2 bg-blue-600 text-white px-4 py-2 rounded"
                v-if="form.sensors.length < 3"
            >
                Tambah Sensor
            </button>

            <button
                type="submit"
                class="bg-green-500 text-white px-4 py-2 rounded mt-4"
            >
                Simpan Perubahan
            </button>
        </form>
    </div>
</template>
