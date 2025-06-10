<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

defineOptions({ layout: AuthenticatedLayout });
const props = defineProps({
    user: Object,
    whatsapp: Object,
});

// Formulir dengan nilai default kosong
const form = useForm({
    phone_number: props.whatsapp ? props.whatsapp.phone_number : "",
});

// Fungsi Submit Form
const submit = () => {
    form.put(route("user.whatsapp.update"), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Nomor WhatsApp berhasil diperbarui!",
                timer: 3000,
                showConfirmButton: false,
            });
        },
    });
};
</script>

<template>
    <Head title="WhatsApp" />
    <div class="flex flex-col gap-4 items-start justify-center">
        <!-- Tombol Back -->
        <button
            @click="$inertia.visit(route('user.dashboard'))"
            class="flex items-center gap-2 text-blue-900 hover:text-blue-600 font-semibold"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>

        <!-- Header Section -->
        <div class="flex items-center w-full justify-center">
            <div>
                <h1 class="text-2xl text-center font-bold text-gray-900 mb-1">
                    Perbarui Nomor WhatsApp
                </h1>
                <p class="text-gray-600 text-center">
                    Perbarui nomor WhatsApp Anda untuk tetap menerima notifikasi
                    otomatis tentang potensi bencana di wilayah Anda
                </p>
            </div>
        </div>

        <!-- Card -->
        <div
            class="card w-full max-w-5xl bg-white border border-gray-100 shadow-xl self-center my-2"
        >
            <div class="card-body">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <!-- Gambar -->
                    <div class="w-full md:w-1/2 flex justify-center">
                        <img
                            src="/assets/media/editWA.png"
                            alt="Edit Nomor WhatsApp"
                            class="w-4/5 object-contain"
                        />
                    </div>

                    <!-- Form -->
                    <div class="w-full md:w-1/2">
                        <p class="mb-4 text-center text-gray-600">
                            Pastikan nomor WhatsApp aktif untuk menerima
                            notifikasi peringatan bencana secara otomatis.
                        </p>
                        <form
                            @submit.prevent="submit"
                            class="flex flex-col gap-4 w-full py-6 border-t border-gray-200"
                        >
                            <div class="form-control">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-700"
                                >
                                    Nomor WhatsApp
                                </label>
                                <div class="relative">
                                    <input
                                        v-model="form.phone_number"
                                        type="text"
                                        id="phone_number"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-gray-400"
                                        placeholder="Contoh: 081234567890"
                                        required
                                    />
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    Gunakan format 08 (tanpa + atau 62 di depan)
                                </div>
                                <span
                                    v-if="form.errors.phone_number"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.phone_number }}
                                </span>
                            </div>

                            <div class="form-control mt-4">
                                <button
                                    type="submit"
                                    class="btn text-white bg-blue-600 hover:bg-blue-700 transition-all duration-200 font-medium flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-xl w-full"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing"
                                        >Memperbarui...</span
                                    >
                                    <span v-else>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
