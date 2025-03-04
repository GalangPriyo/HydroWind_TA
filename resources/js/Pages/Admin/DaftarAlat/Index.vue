<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });
defineProps({
    user: Object,
    devices: Array,
});

// Fungsi hapus device
const confirmDelete = (event) => {
    if (!confirm("Apakah Anda yakin ingin menghapus pengguna ini?")) {
        event.preventDefault(); // Mencegah request jika user membatalkan konfirmasi
    }
};
</script>

<template>
    <div class="container mx-auto p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-2xl font-bold mb-4">Daftar Devices</h1>
        <Link
            :href="route('admin.devices.create')"
            class="bg-blue-500 text-white px-4 py-2 rounded"
            >Tambah Device</Link
        >

        <table class="w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">#</th>
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Lokasi</th>
                    <th class="border p-2">Latitude</th>
                    <th class="border p-2">Longitude</th>
                    <th class="border p-2">Token</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(device, index) in devices" :key="device.id">
                    <td class="border p-2">{{ index + 1 }}</td>
                    <td class="border p-2">{{ device.name }}</td>
                    <td class="border p-2">{{ device.location }}</td>
                    <td class="border p-2">{{ device.latitude }}</td>
                    <td class="border p-2">{{ device.longitude }}</td>
                    <td class="border p-2 text-xs text-gray-600">
                        {{ device.token }}
                    </td>
                    <td class="border p-2">
                        <Link
                            :href="
                                route('admin.devices.show', { id: device.id })
                            "
                            class="bg-green-500 text-white px-3 py-1 rounded inline-block mr-2"
                        >
                            Show
                        </Link>
                        <Link
                            :href="
                                route('admin.devices.edit', { id: device.id })
                            "
                            class="bg-blue-600 text-white px-3 py-1 rounded inline-block mr-2"
                        >
                            Edit
                        </Link>

                        <Link
                            :href="
                                route('admin.devices.delete', {
                                    id: device.id,
                                })
                            "
                            method="delete"
                            as="button"
                            type="button"
                            class="bg-red-600 text-white px-3 py-1 rounded"
                            @click.prevent="confirmDelete"
                        >
                            Delete
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
