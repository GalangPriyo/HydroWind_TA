<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

defineProps({
    user: Object, // User yang sedang login
    whatsapp: Object,
});

const confirmDelete = () => {
    if (confirm("Apakah Anda yakin ingin menghapus nomor WhatsApp ini?")) {
        router.delete(route("user.whatsapp.destroy"));
    }
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
            <div
                class="xl:col-span-2 bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300"
            >
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-14 h-14 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm"
                        >
                            <i class="fab fa-whatsapp text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">
                                Nomor WhatsApp
                            </h2>
                            <p class="text-blue-100">
                                Kelola nomor WhatsApp Anda
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div v-if="whatsapp" class="space-y-6">
                        <!-- Current WhatsApp Number -->
                        <div
                            class="bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-6 border border-green-200"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-phone text-green-600 text-xl"
                                        ></i>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-600 mb-1"
                                        >
                                            Nomor Terdaftar
                                        </p>
                                        <p
                                            class="text-2xl font-bold text-gray-900"
                                        >
                                            {{ whatsapp.phone_number }}
                                        </p>
                                        <p
                                            class="text-sm text-green-600 flex items-center gap-1 mt-1"
                                        >
                                            <i class="fas fa-check-circle"></i>
                                            Terverifikasi
                                        </p>
                                    </div>
                                </div>
                                <div class="hidden sm:block">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-2xl flex items-center justify-center"
                                    >
                                        <i
                                            class="fas fa-check text-white text-2xl"
                                        ></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <Link
                                :href="route('user.whatsapp.edit')"
                                class="flex-1 group bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-2xl p-4 text-center font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                            >
                                <div
                                    class="flex items-center justify-center gap-3"
                                >
                                    <i
                                        class="fas fa-edit text-lg group-hover:rotate-12 transition-transform"
                                    ></i>
                                    <span>Edit Nomor</span>
                                </div>
                            </Link>
                            <button
                                @click="confirmDelete"
                                class="flex-1 group bg-red-50 hover:bg-red-100 text-red-600 border-2 border-red-200 hover:border-red-300 rounded-2xl p-4 font-semibold transition-all duration-300 transform hover:scale-105"
                            >
                                <div
                                    class="flex items-center justify-center gap-3"
                                >
                                    <i
                                        class="fas fa-trash text-lg group-hover:rotate-12 transition-transform"
                                    ></i>
                                    <span>Hapus Nomor</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div v-else class="space-y-6 text-center">
                        <!-- Empty State -->
                        <div class="py-12">
                            <div
                                class="w-24 h-24 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-6"
                            >
                                <i
                                    class="fab fa-whatsapp text-gray-400 text-4xl"
                                ></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                Belum Ada Nomor WhatsApp
                            </h3>
                            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                                Daftarkan nomor WhatsApp Anda untuk mendapatkan
                                notifikasi dan update terbaru dari Hydrowind.
                            </p>
                            <Link
                                :href="route('user.whatsapp.create')"
                                class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-2xl px-8 py-4 font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                            >
                                <i class="fas fa-plus text-lg"></i>
                                <span>Tambah Nomor WhatsApp</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Community Card -->
            <div
                class="xl:col-span-3 bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300"
            >
                <div class="bg-gradient-to-br from-green-500 to-teal-600 p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div
                            class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm"
                        >
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white">Komunitas</h2>
                    </div>
                    <p class="text-green-100 text-sm">
                        Bergabung dengan komunitas pengguna Hydrowind
                    </p>
                </div>

                <div class="p-6 space-y-6">
                    <div class="text-center">
                        <img
                            src="/assets/media/komunitas.png"
                            alt="Komunitas Hydrowind"
                            class="w-32 h-32 object-contain mx-auto mb-4 rounded-2xl"
                        />
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            Komunitas WhatsApp
                        </h3>
                        <p class="text-gray-600 text-sm mb-6">
                            Diskusi, sharing pengalaman, dan dapatkan tips
                            terbaru dari komunitas pengguna Hydrowind.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div
                            class="flex items-center gap-3 text-sm text-gray-600"
                        >
                            <i class="fas fa-check text-green-500"></i>
                            <span>Diskusi & Sharing Pengalaman</span>
                        </div>
                        <div
                            class="flex items-center gap-3 text-sm text-gray-600"
                        >
                            <i class="fas fa-check text-green-500"></i>
                            <span>Tips & Trik Penggunaan</span>
                        </div>
                        <div
                            class="flex items-center gap-3 text-sm text-gray-600"
                        >
                            <i class="fas fa-check text-green-500"></i>
                            <span>Update Terbaru</span>
                        </div>
                    </div>

                    <a
                        href="https://chat.whatsapp.com/I2N74ilIQdbJtZCsnk1HeW"
                        target="_blank"
                        class="block w-full bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white rounded-2xl p-4 text-center font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                    >
                        <div class="flex items-center justify-center gap-3">
                            <i class="fab fa-whatsapp text-lg"></i>
                            <span>Gabung Sekarang</span>
                            <i class="fas fa-external-link-alt text-sm"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
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
    transform: scale(1.1);
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
