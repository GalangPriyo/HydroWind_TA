<script setup>
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });
defineProps({
    user: Object,
    whatsapp: Object,
});

// Fungsi Hapus dengan SweetAlert
const confirmDelete = () => {
    Swal.fire({
        title: "Hapus Nomor WhatsApp?",
        text: "Tindakan ini akan menghapus nomor WhatsApp yang Anda daftarkan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("user.whatsapp.delete"));
        }
    });
};
</script>

<template>
    <div class="bg-base200 flex flex-col items-center">
        <div
            class="card w-1/2 max-w-5xl bg-gradient-to-b from-blue-200 to-cyan-200 shadow-xl"
        >
            <div class="card-body items-center text-center">
                <img
                    src="/assets/media/index-wa.png"
                    alt="Nomor WhatsApp"
                    class="w-1/2"
                />
                <h1 class="text-xl font-bold">Nomor WhatsApp Terdaftar</h1>
                <div v-if="whatsapp" class="w-full">
                    <p class="text-xl text-gray-700 mb-5 py-1">
                        <i class="fa-brands fa-whatsapp"></i>
                        {{ whatsapp.phone_number }}
                    </p>
                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-center mt-2"
                    >
                        <Link
                            :href="route('user.whatsapp.edit')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md w-full sm:w-36 text-center text-sm"
                        >
                            Edit
                        </Link>
                        <button
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-md w-full sm:w-36 text-center text-sm"
                        >
                            Hapus
                        </button>
                    </div>
                </div>

                <div v-else class="w-full">
                    <p class="text-red-500 mb-5 py-2">
                        Anda belum menambahkan nomor WhatsApp.
                    </p>
                    <Link
                        :href="route('user.whatsapp.create')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md w-full sm:w-64 text-center text-sm"
                    >
                        Tambah Nomor WhatsApp
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
