<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });
defineProps({
    user: Object,
    whatsapp: Object,
});

// Fungsi Hapus
const confirmDelete = (event) => {
    if (!confirm("Apakah Anda yakin ingin menghapus nomor WhatsApp ini?")) {
        event.preventDefault();
    }
};
</script>

<template>
    <div class="card bg-base-100 w-96 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Nomor WhatsApp Anda</h2>
            <div v-if="whatsapp">
                <p class="text-lg mb-5">Nomor: {{ whatsapp.phone_number }}</p>
                <Link
                    :href="route('user.whatsapp.edit')"
                    class="bg-blue-600 text-white px-4 py-2 rounded inline-block mr-2"
                >
                    Edit
                </Link>
                <Link
                    :href="route('user.whatsapp.delete')"
                    method="delete"
                    as="button"
                    class="bg-red-600 text-white px-4 py-2 rounded inline-block"
                    @click.prevent="confirmDelete"
                >
                    Hapus
                </Link>
            </div>
            <div v-else>
                <p class="text-red-500 mb-5">
                    Anda belum menambahkan nomor WhatsApp.
                </p>
                <Link
                    :href="route('user.whatsapp.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Tambah
                </Link>
            </div>
        </div>
    </div>
</template>
