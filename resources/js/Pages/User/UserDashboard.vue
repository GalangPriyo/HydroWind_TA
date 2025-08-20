<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import TestPushNotification from "@/Components/TestPushNotification.vue";

defineOptions({ layout: AuthenticatedLayout });
defineProps({
    user: Object, // User yang sedang login
    whatsapp: Object,
});

const confirmDelete = () => {
    Swal.fire({
        title: "Hapus Nomor WhatsApp?",
        text: "Tindakan ini akan menghapus nomor WhatsApp yang Anda daftarkan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
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
    <Head title="User Dashboard" />

    <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <!-- Welcome Hero Section -->
        <div class="bg-primary rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="p-6 md:p-8 text-white">
                <div
                    class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6"
                >
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">
                            Selamat Datang,
                            <span class="text-yellow-300"
                                >{{ user.name }}!</span
                            >
                        </h1>
                        <p class="text-blue-100 mt-2">
                            Kelola akun Anda dengan mudah dan bergabung dengan
                            komunitas Hydrowind untuk menerima informasi
                            terbaru.
                        </p>
                    </div>
                    <div class="flex items-center">
                        <div
                            class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm"
                        >
                            <i
                                class="fas fa-user-check text-white text-3xl"
                            ></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8">
            <!-- WhatsApp Management Card -->
            <div class="xl:col-span-2">
                <div
                    class="bg-white px-5 py-3 border-b border-gray-200 rounded-t-xl shadow-lg transition-all duration-300"
                >
                    <div class="flex items-center space-x-3">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        <div>
                            <p class="font-medium">
                                Kelola nomor WhatsApp Anda
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="px-8 pb-8 pt-4 space-y-6 bg-white rounded-b-xl shadow-lg"
                >
                    <div v-if="whatsapp" class="space-y-4">
                        <!-- Current WhatsApp Number -->
                        <div>
                            <h3
                                class="text-sm text-green-600 mb-2 flex items-center"
                            >
                                <i class="fas fa-check-circle mr-2"></i>
                                Anda telah mendaftarkan nomor WhatsApp
                            </h3>

                            <!-- Card -->
                            <div
                                class="bg-blue-50 border border-blue-200 rounded-2xl px-4 py-4 shadow-sm"
                            >
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:space-x-4"
                                >
                                    <!-- Icon -->
                                    <div
                                        class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center shadow-inner mx-auto sm:mx-0"
                                    >
                                        <i
                                            class="fas fa-phone text-blue-600 text-2xl"
                                        ></i>
                                    </div>
                                    <!-- Text -->
                                    <div
                                        class="text-center sm:text-left mt-3 sm:mt-0"
                                    >
                                        <p
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Nomor Terdaftar
                                        </p>
                                        <p
                                            class="text-xl sm:text-2xl font-bold text-gray-900 tracking-wide"
                                        >
                                            {{ whatsapp.phone_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <p
                                class="text-gray-600 my-3 max-w-md text-center sm:text-left"
                            >
                                Untuk mengubah atau menghapus nomor WhatsApp
                                terdaftar, gunakan tombol di bawah.
                            </p>

                            <!-- Tombol Aksi -->
                            <div
                                class="flex flex-col sm:flex-row justify-center sm:justify-end gap-3"
                            >
                                <Link
                                    :href="route('user.whatsapp.edit')"
                                    class="text-center px-4 py-2 bg-amber-400 text-white text-sm rounded-lg hover:bg-amber-500 transition-colors font-medium tooltip"
                                    data-tip="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </Link>
                                <button
                                    @click="confirmDelete"
                                    class="text-center px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors font-medium tooltip"
                                    data-tip="Hapus"
                                >
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="space-y-6 text-center">
                        <!-- Empty State -->
                        <div class="py-6">
                            <div
                                class="w-24 h-24 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-6"
                            >
                                <i
                                    class="fa-solid fa-phone-slash text-gray-400 text-4xl"
                                ></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                Belum Ada Nomor WhatsApp
                            </h3>
                            <p class="text-gray-600 mb-6 max-w-md">
                                Daftarkan nomor WhatsApp Anda untuk mendapatkan
                                notifikasi peringatan bencana dari Hydrowind.
                            </p>
                            <Link
                                :href="route('user.whatsapp.create')"
                                class="inline-flex items-center text-sm gap-3 bg-green-600 hover:bg-green-700 font-semibold text-white rounded-lg px-4 py-2 transition-all duration-300 transform hover:shadow-lg"
                            >
                                <i class="fas fa-plus"></i>
                                <span>Tambah Nomor WhatsApp</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Community Card -->
            <div class="xl:col-span-3">
                <div
                    class="bg-white px-5 py-3 border-b border-gray-200 rounded-t-xl shadow-lg transition-all duration-300"
                >
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-users-viewfinder text-2xl"></i>

                        <div>
                            <p class="font-medium">
                                Grup WhatsApp Komunitas Hydrowind
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="px-8 pb-6 pt-4 space-y-6 bg-white rounded-b-xl shadow-lg"
                >
                    <div class="space-y-4">
                        <!-- Current WhatsApp Number -->
                        <div>
                            <div
                                class="flex flex-col md:flex-row items-center gap-6"
                            >
                                <!-- Teks dan Tombol -->
                                <div class="md:flex-[3] w-full">
                                    <div
                                        class="flex justify-center mb-4 md:mb-0"
                                    >
                                        <img
                                            src="/assets/media/komunitas.png"
                                            alt="Komunitas Hydrowind"
                                            class="block md:hidden w-1/2 object-contain"
                                        />
                                    </div>
                                    <p
                                        class="text-gray-700 mb-4 md:mb-8 text-center md:text-left"
                                    >
                                        Bergabung dalam Grup Whatsapp Komunitas
                                        Hydrowind untuk mendapatkan informasi
                                        peringatan bencana secara aktual yang
                                        terjadi di Desa Gebangan.
                                    </p>
                                    <div
                                        class="flex justify-center md:justify-start"
                                    >
                                        <a
                                            href="https://chat.whatsapp.com/I2N74ilIQdbJtZCsnk1HeW"
                                            target="_blank"
                                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg text-sm hover:bg-blue-700 transition w-max"
                                        >
                                            <i
                                                class="fa-solid fa-link mr-1"
                                            ></i>
                                            Grup WhatsApp Hydrowind
                                        </a>
                                    </div>
                                </div>

                                <!-- Gambar -->
                                <div
                                    class="md:flex-[2] w-full max-w-[200px] md:max-w-none"
                                >
                                    <img
                                        src="/assets/media/komunitas.png"
                                        alt="Komunitas Hydrowind"
                                        class="hidden md:block w-full object-contain"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Test Push Notification Component -->
    <div class="max-w-8xl mx-auto px-4 py-8">
        <TestPushNotification />
    </div>
</template>

<style scoped>
/* Custom animations */
@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Custom hover effects */
.group:hover .fas,
.group:hover .fab {
    transform: scale(1);
    transition: transform 0.3s ease;
}

/* Gradient text */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}
</style>
