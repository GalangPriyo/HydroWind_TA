<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });
defineProps({
    user: Object,
});

const form = useForm({
    name: "",
    email: "",
    phone_number: "",
});

const submit = () => {
    form.post(route("admin.pengguna.create"));
};
</script>

<template>
    <Head title="Daftar Pengguna" />
    <div class="flex flex-col gap-4 items-start justify-center">
        <!-- Tombol Back -->
        <button
            @click="$inertia.visit(route('admin.pengguna'))"
            class="flex items-center gap-2 text-blue-900 hover:text-blue-600 font-semibold"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>
        <div class="flex items-center w-full justify-center">
            <div>
                <h1 class="text-2xl text-center font-bold text-gray-900 mb-1">
                    Tambah Pengguna
                </h1>
                <p class="text-gray-600 text-center">
                    Masukkan informasi pengguna baru untuk memberikan akses ke
                    sistem
                </p>
            </div>
        </div>

        <div
            class="card w-full max-w-5xl bg-white border border-gray-100 shadow-xl self-center my-2"
        >
            <div class="card-body">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="w-full md:w-1/2 flex flex-col">
                        <!-- Gambar di tengah -->
                        <div class="flex justify-center">
                            <img
                                src="/assets/media/add-user.png"
                                alt="Tambah Pengguna"
                                class="w-4/5 object-contain"
                            />
                        </div>
                    </div>

                    <!-- Kanan: Form -->
                    <div class="w-full md:w-1/2">
                        <form
                            @submit.prevent="submit"
                            class="flex flex-col gap-4 w-full"
                        >
                            <div class="form-control">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-700"
                                >
                                    Nama
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-gray-400"
                                    placeholder="Masukkan nama pengguna"
                                />
                                <span
                                    v-if="form.errors.name"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.name }}
                                </span>
                            </div>

                            <div class="form-control">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-700"
                                >
                                    Email
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-gray-400"
                                    placeholder="Masukkan email pengguna"
                                />
                                <span
                                    v-if="form.errors.email"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.email }}
                                </span>
                            </div>

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
                                        class="w-full pl-4 pr-10 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-gray-400"
                                        placeholder="Contoh: 6281234567890"
                                    />
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    Gunakan format 62 (tanpa + atau 0 di depan)
                                </div>
                                <span
                                    v-if="form.errors.phone_number"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.phone_number }}
                                </span>
                            </div>

                            <div class="form-control mt-4">
                                <div
                                    class="flex flex-col sm:flex-row gap-4 justify-end"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            $inertia.visit(
                                                route('admin.pengguna')
                                            )
                                        "
                                        class="btn text-gray-700 bg-gray-100 hover:bg-gray-200 transition-all duration-200 font-medium flex items-center justify-center"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn text-white bg-blue-600 hover:bg-blue-700 transition-all duration-200 font-medium flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-xl"
                                        :disabled="form.processing"
                                    >
                                        <span v-if="form.processing"
                                            >Menyimpan...</span
                                        >
                                        <span v-else>Simpan Pengguna</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
