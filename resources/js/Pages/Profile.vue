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
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
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
    <div class="flex flex-col gap-4 items-start justify-center">
        <!-- Tombol Back -->
        <button
            @click="goBack"
            class="flex items-center gap-2 text-blue-900 hover:text-blue-600 font-semibold"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>

        <div
            class="card w-full max-w-5xl bg-gradient-to-b from-blue-200 to-cyan-200 shadow-xl self-center my-2"
        >
            <div class="card-body">
                <h1 class="text-3xl font-bold text-center mb-4">Profil Saya</h1>

                <div class="flex flex-col md:flex-row gap-8">
                    <!-- KIRI: Info Akun dan Hapus -->
                    <div class="w-full md:w-1/2 space-y-4">
                        <!-- Info Akun -->
                        <div class="card glass shadow-md">
                            <div class="card-body py-4">
                                <h2
                                    class="card-title text-blue-800 text-xl font-semibold"
                                >
                                    Informasi Akun
                                </h2>

                                <div>
                                    <label class="font-medium text-blue-500"
                                        >Nama:</label
                                    >
                                    <div class="text-gray-800">
                                        {{ user.name }}
                                    </div>
                                </div>
                                <div>
                                    <label class="font-medium text-blue-500"
                                        >Email:</label
                                    >
                                    <div class="text-gray-800">
                                        {{ user.email }}
                                    </div>
                                </div>
                                <div>
                                    <label class="font-medium text-blue-500"
                                        >Nomor WA:</label
                                    >
                                    <div class="text-gray-800">
                                        {{
                                            user.whatsapp?.phone_number ??
                                            "Belum terdaftar"
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hapus Akun -->
                        <div class="card glass shadow-md">
                            <div class="card-body py-4">
                                <h3 class="card-title text-red-600 text-xl">
                                    Bahaya
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Tindakan ini akan menghapus akun Anda
                                    beserta seluruh data terkait secara permanen
                                    dan tidak dapat dipulihkan.
                                </p>
                                <div class="card-actions mt-4">
                                    <button
                                        @click="confirmDelete"
                                        class="btn bg-red-600 text-white border-none hover:bg-red-700"
                                    >
                                        <i
                                            class="fa-solid fa-trash-can mr-1"
                                        ></i>
                                        Hapus Akun
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KANAN: Ubah Password -->
                    <div class="card glass w-full md:w-1/2 shadow-md">
                        <div class="card-body py-4">
                            <h2 class="card-title text-blue-800 text-xl">
                                Ubah Password
                            </h2>
                            <p class="text-sm text-gray-600">
                                Silakan isi form di bawah untuk mengganti
                                password akun Anda.
                            </p>
                            <form
                                @submit.prevent="updatePassword"
                                class="space-y-1"
                            >
                                <div class="form-control m-0">
                                    <label class="label">
                                        <span class="label-text"
                                            >Password Saat Ini</span
                                        >
                                    </label>
                                    <input
                                        type="password"
                                        v-model="form.current_password"
                                        class="input input-bordered w-full"
                                    />
                                    <div
                                        v-if="form.errors.current_password"
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{ form.errors.current_password }}
                                    </div>
                                </div>

                                <div class="form-control m-0">
                                    <label class="label">
                                        <span class="label-text"
                                            >Password Baru</span
                                        >
                                    </label>
                                    <input
                                        type="password"
                                        v-model="form.new_password"
                                        class="input input-bordered w-full"
                                    />
                                    <div
                                        v-if="form.errors.new_password"
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{ form.errors.new_password }}
                                    </div>
                                </div>

                                <div class="form-control m-0">
                                    <label class="label">
                                        <span class="label-text"
                                            >Konfirmasi Password Baru</span
                                        >
                                    </label>
                                    <input
                                        type="password"
                                        v-model="form.confirm_password"
                                        class="input input-bordered w-full"
                                    />
                                    <div
                                        v-if="form.errors.confirm_password"
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{ form.errors.confirm_password }}
                                    </div>
                                </div>

                                <div class="card-actions pt-4 justify-end">
                                    <button
                                        type="submit"
                                        class="btn bg-blue-700 border-none text-white hover:bg-blue-800"
                                        :disabled="form.processing"
                                    >
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        Simpan Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
