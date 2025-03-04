<script setup>
import { Link } from "@inertiajs/vue3";
import { defineProps } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    device: Object,
    user: Object,
});
</script>

<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Detail Device</h1>

        <div class="border p-4 bg-white shadow-lg rounded-lg">
            <h2 class="text-xl font-semibold">{{ device.name }}</h2>
            <p><strong>Lokasi:</strong> {{ device.location }}</p>
            <p><strong>Latitude:</strong> {{ device.latitude || "N/A" }}</p>
            <p><strong>Longitude:</strong> {{ device.longitude || "N/A" }}</p>

            <h2 class="text-lg font-semibold mt-4">Sensors</h2>
            <table class="w-full border mt-2">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2">Nama Sensor</th>
                        <th class="border p-2">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="sensor in device.sensors" :key="sensor.id">
                        <td class="border p-2">{{ sensor.name }}</td>
                        <td class="border p-2">{{ sensor.unit }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Link
            :href="route('admin.devices')"
            class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded"
        >
            Kembali
        </Link>
    </div>
</template>
