<script setup>
import { ref } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

defineOptions({ layout: AuthenticatedLayout });

const { user } = defineProps({
    user: Object,
});

// Form update password
const form = useForm({
    current_password: "",
    new_password: "",
    confirm_password: "",
});

// Submit update password
const updatePassword = () => {
    form.post(route("profile.password.update"), {
        onSuccess: () => {
            form.reset();
            Swal.fire({
                icon: "success",
                title: "Password Berhasil Diubah",
                text: "Password baru Anda telah disimpan.",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    popup: "rounded-2xl",
                },
            });
        },
    });
};

// Konfirmasi dan hapus akun
const confirmDelete = () => {
    Swal.fire({
        title: "Hapus Akun?",
        text: "Setelah akun dihapus, semua informasi Anda akan hilang dan tidak bisa dikembalikan. Harap pertimbangkan dengan matang.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
        customClass: {
            popup: "rounded-2xl",
            confirmButton: "rounded-xl",
            cancelButton: "rounded-xl",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("profile.destroy"));
        }
    });
};

function goBack() {
    if (typeof window !== "undefined" && window.history) {
        window.history.back();
    }
}
</script>

<template>
    <Head title="Profil Saya" />

    <div class="max-w-6xl mx-auto">
        <!-- Tombol Back -->
        <button
            @click="goBack"
            class="flex items-center gap-2 text-blue-900 hover:text-blue-600 font-semibold"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>
        <div class="flex items-center w-full justify-center">
            <div>
                <h1 class="text-2xl text-center font-bold text-gray-900 mb-1">
                    Profil Akun
                </h1>
                <p class="text-gray-600 text-center">
                    Informasi dasar akun Anda ditampilkan di sini.
                </p>
            </div>
        </div>

        <!-- Main Card -->
        <div>
            <div class="px-6 pt-6">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left Column - Account Info -->
                    <div class="w-full lg:w-1/2 space-y-6">
                        <!-- Account Information Card -->
                        <div
                            class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden"
                        >
                            <div
                                class="px-5 py-3 border-b border-gray-200 bg-white"
                            >
                                <h2
                                    class="font-medium text-gray-800 flex items-center gap-2"
                                >
                                    <i class="fas fa-user-circle text-xl"></i>
                                    Informasi Akun
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-500 mb-1"
                                        >Nama Lengkap</label
                                    >
                                    <div class="text-gray-900 font-medium">
                                        {{ user.name }}
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-500 mb-1"
                                        >Alamat Email</label
                                    >
                                    <div class="text-gray-900 font-medium">
                                        {{ user.email }}
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-500 mb-1"
                                        >Nomor WhatsApp</label
                                    >
                                    <div class="text-gray-900 font-medium">
                                        {{
                                            user.whatsapp?.phone_number ??
                                            "Belum terdaftar"
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone Card -->
                        <div
                            class="bg-white rounded-xl shadow-md border border-red-200 overflow-hidden"
                        >
                            <div
                                class="px-5 py-3 border-b border-red-200 bg-red-100"
                            >
                                <h2
                                    class="font-semibold text-red-600 flex items-center gap-2"
                                >
                                    <i
                                        class="fas fa-exclamation-triangle text-xl"
                                    ></i>
                                    Zona Bahaya
                                </h2>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-900 mb-4">
                                    Tindakan ini akan menghapus akun Anda
                                    beserta seluruh data terkait secara permanen
                                    dan tidak dapat dipulihkan.
                                </p>
                                <div class="flex justify-end">
                                    <button
                                        @click="confirmDelete"
                                        class="flex justify-center items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                        Hapus Akun Saya
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Change Password -->
                    <div class="w-full lg:w-1/2">
                        <div
                            class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden h-full"
                        >
                            <div class="px-5 py-3 border-b border-gray-200">
                                <h2
                                    class="font-medium text-gray-800 flex items-center gap-2"
                                >
                                    <i class="fas fa-lock text-lg"></i>
                                    Ubah Password
                                </h2>
                            </div>
                            <div class="p-6">
                                <p class="text-sm text-gray-600 mb-6">
                                    Silakan isi form di bawah untuk mengganti
                                    password akun Anda.
                                </p>

                                <form
                                    @submit.prevent="updatePassword"
                                    class="space-y-4"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                        >
                                            Password Saat Ini
                                        </label>
                                        <input
                                            type="password"
                                            v-model="form.current_password"
                                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200"
                                            placeholder="Masukkan password saat ini"
                                        />
                                        <div
                                            v-if="form.errors.current_password"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors.current_password }}
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                        >
                                            Password Baru
                                        </label>
                                        <input
                                            type="password"
                                            v-model="form.new_password"
                                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200"
                                            placeholder="Masukkan password baru"
                                        />
                                        <div
                                            v-if="form.errors.new_password"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors.new_password }}
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                        >
                                            Konfirmasi Password Baru
                                        </label>
                                        <input
                                            type="password"
                                            v-model="form.confirm_password"
                                            class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200"
                                            placeholder="Konfirmasi password baru"
                                        />
                                        <div
                                            v-if="form.errors.confirm_password"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors.confirm_password }}
                                        </div>
                                    </div>

                                    <div class="pt-2 flex justify-end">
                                        <button
                                            type="submit"
                                            :disabled="form.processing"
                                            class="flex justify-end items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium disabled:opacity-70 disabled:cursor-not-allowed"
                                        >
                                            <i
                                                class="fa-solid fa-floppy-disk"
                                            ></i>
                                            Ubah Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth transitions */
* {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
