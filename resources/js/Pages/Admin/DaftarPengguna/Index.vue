<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });
defineProps({
    user: Object,
    users: Array,
});

// Fungsi untuk menghapus pengguna
const confirmDelete = (event) => {
    if (!confirm("Apakah Anda yakin ingin menghapus pengguna ini?")) {
        event.preventDefault(); // Mencegah request jika user membatalkan konfirmasi
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-xl font-semibold mb-2">Daftar Pengguna</h2>
        <Link
            :href="route('admin.pengguna.create')"
            class="bg-blue-600 text-white px-3 py-1 rounded inline-block mr-2"
        >
            Tambah Pengguna
        </Link>
        <table class="w-full border-collapse border border-gray-300 my-2">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">No</th>
                    <th class="border border-gray-300 px-4 py-2">Nama</th>
                    <th class="border border-gray-300 px-4 py-2">Email</th>
                    <th class="border border-gray-300 px-4 py-2">Nomor WA</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(user, index) in users" :key="user.id">
                    <td class="border border-gray-300 px-4 py-2">
                        {{ index + 1 }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        {{ user.name }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        {{ user.email }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        {{
                            user.whatsapp
                                ? user.whatsapp.phone_number
                                : "Belum ada"
                        }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2 space-x-2">
                        <!-- <Link
                            :href="
                                route('admin.pengguna.show', { id: user.id })
                            "
                            class="bg-green-600 text-white px-3 py-1 rounded inline-block mr-2"
                        >
                            Show
                        </Link> -->

                        <Link
                            :href="
                                route('admin.pengguna.edit', { id: user.id })
                            "
                            class="bg-blue-600 text-white px-3 py-1 rounded inline-block mr-2"
                        >
                            Edit
                        </Link>

                        <Link
                            :href="
                                route('admin.pengguna.delete', { id: user.id })
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
