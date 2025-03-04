<script setup>
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

// Deklarasi props dengan defineProps
const props = defineProps({
    user: Object,
    pengguna: Object,
    whatsapp: Object,
});

// Inisialisasi form dengan data dari props
const form = useForm({
    name: props.pengguna.name,
    email: props.pengguna.email,
    phone_number: props.whatsapp ? props.whatsapp.phone_number : "",
});

// Fungsi submit untuk update data
const submit = () => {
    form.put(route("admin.pengguna.update", { id: props.pengguna.id }));
};
</script>

<template>
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Edit Pengguna</h2>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-gray-700">Nama</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full border p-2 rounded mt-1"
                    required
                />
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="w-full border p-2 rounded mt-1"
                    required
                />
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Nomor WhatsApp</label>
                <input
                    v-model="form.phone_number"
                    type="text"
                    class="w-full border p-2 rounded mt-1"
                />
            </div>
            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                Simpan Perubahan
            </button>
            <Link :href="route('admin.pengguna')" class="ml-2 text-gray-600"
                >Kembali</Link
            >
        </form>
    </div>
</template>
