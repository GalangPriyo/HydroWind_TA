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
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Tambah Pengguna</h2>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-gray-700">Nama</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full border p-2 rounded mt-1"
                    placeholder="Masukkan nama pengguna"
                />
                <span v-if="form.errors.name" class="text-red-500 text-sm">{{
                    form.errors.name
                }}</span>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="w-full border p-2 rounded mt-1"
                    placeholder="Masukkan email pengguna"
                />
                <span v-if="form.errors.email" class="text-red-500 text-sm">{{
                    form.errors.email
                }}</span>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Nomor WhatsApp</label>
                <input
                    v-model="form.phone_number"
                    type="text"
                    class="w-full border p-2 rounded mt-1"
                    placeholder="Masukkan nomor WhatsApp"
                />
                <span
                    v-if="form.errors.phone_number"
                    class="text-red-500 text-sm"
                    >{{ form.errors.phone_number }}</span
                >
            </div>

            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                Simpan Pengguna
            </button>
        </form>
    </div>
</template>
