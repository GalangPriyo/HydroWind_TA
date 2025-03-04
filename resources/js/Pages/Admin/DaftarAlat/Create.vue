<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    location: "",
    latitude: "",
    longitude: "",
    sensors: [{ name: "", unit: "" }],
});

// Menambah sensor baru
const addSensor = () => {
    if (form.sensors.length < 3) {
        form.sensors.push({ name: "", unit: "" });
    }
};

// Menghapus sensor
const removeSensor = (index) => {
    form.sensors.splice(index, 1);
};

// Submit form
const submit = () => {
    form.post(route("admin.devices.store"));
};
</script>

<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Tambah Device</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <input
                v-model="form.name"
                type="text"
                placeholder="Nama Device"
                class="w-full p-2 border rounded"
                required
            />
            <input
                v-model="form.location"
                type="text"
                placeholder="Lokasi Device"
                class="w-full p-2 border rounded"
                required
            />
            <input
                v-model="form.latitude"
                type="number"
                step="any"
                placeholder="Latitude"
                class="w-full p-2 border rounded"
            />
            <input
                v-model="form.longitude"
                type="number"
                step="any"
                placeholder="Longitude"
                class="w-full p-2 border rounded"
            />

            <h2 class="text-lg font-semibold">Sensors</h2>
            <div
                v-for="(sensor, index) in form.sensors"
                :key="index"
                class="flex gap-2"
            >
                <select
                    v-model="sensor.name"
                    class="p-2 border rounded"
                    required
                >
                    <option value="" disabled>Pilih Sensor</option>
                    <option value="rain_intensity">Rain Intensity</option>
                    <option value="water_level">Water Level</option>
                    <option value="wind_speed">Wind Speed</option>
                    <option value="wind_direction">Wind Direction</option>
                </select>
                <input
                    v-model="sensor.unit"
                    type="text"
                    placeholder="Satuan (mm, m, m/s)"
                    class="p-2 border rounded"
                    required
                />
                <button
                    type="button"
                    @click="removeSensor(index)"
                    class="bg-red-500 text-white p-2 rounded"
                >
                    Hapus
                </button>
            </div>

            <button
                type="button"
                @click="addSensor"
                v-if="form.sensors.length < 3"
                class="bg-green-500 text-white px-4 py-2 rounded"
            >
                Tambah Sensor
            </button>

            <button
                type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded"
            >
                Simpan
            </button>
        </form>
    </div>
</template>
