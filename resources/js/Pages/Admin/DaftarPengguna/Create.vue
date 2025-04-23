<script setup>
import { useForm } from "@inertiajs/vue3";
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
    <div class="bg-base200 flex flex-col gap-4 items-start justify-center">
        <!-- Tombol Back -->
        <button
            @click="$inertia.visit(route('admin.pengguna'))"
            class="flex items-center gap-2 text-blue-900 hover:text-blue-600 font-semibold"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>
        <div
            class="card w-full max-w-5xl bg-gradient-to-b from-blue-200 to-cyan-200 shadow-xl self-center my-2"
        >
            <div class="card-body">
                <h1 class="text-3xl font-bold">Tambah Pengguna</h1>
                <div class="flex flex-col md:flex-row items-start gap-8">
                    <div class="w-full md:w-1/2 flex flex-col">
                        <!-- Title & Description -->
                        <div class="text-left mb-4">
                            <p class="text-md text-gray-600 pt-1">
                                Silakan isi data berikut untuk menambahkan
                                pengguna baru ke dalam sistem.
                            </p>
                        </div>

                        <!-- Gambar di tengah -->
                        <div class="flex justify-center">
                            <img
                                src="/assets/media/add-user.png"
                                alt="Tambah Pengguna"
                                class="w-3/4 object-contain"
                            />
                        </div>
                    </div>

                    <!-- Kanan: Form -->
                    <div class="w-full md:w-1/2">
                        <form
                            @submit.prevent="submit"
                            class="flex flex-col gap-1 w-full"
                        >
                            <div class="form-control">
                                <label class="label px-0">
                                    <span class="label-text">Nama</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="input input-bordered w-full"
                                    placeholder="Masukkan nama pengguna"
                                />
                                <span
                                    v-if="form.errors.name"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.name }}
                                </span>
                            </div>

                            <div class="form-control">
                                <label class="label px-0">
                                    <span class="label-text">Email</span>
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="input input-bordered w-full"
                                    placeholder="Masukkan email pengguna"
                                />
                                <span
                                    v-if="form.errors.email"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.email }}
                                </span>
                            </div>

                            <div class="form-control">
                                <label class="label px-0">
                                    <span class="label-text"
                                        >Nomor WhatsApp</span
                                    >
                                </label>
                                <input
                                    v-model="form.phone_number"
                                    type="text"
                                    class="input input-bordered w-full"
                                    placeholder="Masukkan nomor WhatsApp"
                                />
                                <span
                                    v-if="form.errors.phone_number"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.phone_number }}
                                </span>
                            </div>

                            <div class="form-control mt-6">
                                <button
                                    type="submit"
                                    class="btn btn-primary w-full"
                                >
                                    Simpan Pengguna
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
