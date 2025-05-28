<script setup>
import { Link, router, Head } from "@inertiajs/vue3";
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
    <Head title="WhatsApp" />
    <div class="flex justify-center py-8">
        <div
            class="flex flex-col md:flex-row gap-6 w-full max-w-7xl items-stretch"
        >
            <!-- Card WhatsApp User -->
            <div class="card w-full md:w-2/5 bg-white shadow-xl">
                <div
                    class="card-body justify-center items-center text-center p-4"
                >
                    <img
                        src="/assets/media/index-wa.png"
                        alt="Nomor WhatsApp"
                        class="w-1/2 mb-2"
                    />
                    <h2 class="text-xl font-bold">Nomor WhatsApp Terdaftar</h2>

                    <div v-if="whatsapp" class="w-full">
                        <p class="text-lg text-gray-700 mb-3 py-1">
                            <i class="fa-brands fa-whatsapp"></i>
                            {{ whatsapp.phone_number }}
                        </p>
                        <div
                            class="flex flex-col sm:flex-row gap-3 justify-center mt-2"
                        >
                            <Link
                                :href="route('user.whatsapp.edit')"
                                class="px-3 py-2 bg-blue-600 text-white rounded-md w-full sm:w-32 text-center text-sm"
                                >Edit</Link
                            >
                            <button
                                @click="confirmDelete"
                                class="px-3 py-2 bg-red-600 text-white rounded-md w-full sm:w-32 text-center text-sm"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div v-else class="w-full">
                        <p class="text-red-500 mb-3 py-1 text-sm">
                            Anda belum mendaftarkan nomor WhatsApp.
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

            <!-- Kanan: Stack 2 card vertikal -->
            <div class="flex flex-col gap-6 w-full md:w-3/5">
                <!-- Komunitas Hydrowind -->
                <div
                    class="card bg-gradient-to-b from-sky-200 to-blue-300 shadow-xl flex-row items-center p-4"
                >
                    <div class="w-2/3 px-4">
                        <h1 class="text-xl font-bold">Komunitas Hydrowind</h1>
                        <p class="text-gray-700 py-3 text-sm">
                            Bergabung dalam komunitas pengguna Hydrowind untuk
                            diskusi & sharing pengalaman.
                        </p>
                        <a
                            href="https://chat.whatsapp.com/I2N74ilIQdbJtZCsnk1HeW"
                            target="_blank"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition block w-max"
                        >
                            Komunitas WhatsApp Hydrowind
                        </a>
                    </div>
                    <div class="w-1/3 pr-4">
                        <img
                            src="/assets/media/komunitas.png"
                            alt="Komunitas Hydrowind"
                            class="w-full object-contain"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
