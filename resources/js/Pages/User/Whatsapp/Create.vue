<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });
defineProps({
    user: Object, // User yang sedang login
});

const form = useForm({
    phone_number: ref(""),
});

const submit = () => {
    form.post(route("user.whatsapp.store"), {
        onSuccess: () => alert("Nomor WhatsApp berhasil disimpan!"),
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <h1 class="text-2xl font-bold mb-4">Tambah Nomor WhatsApp</h1>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block">Nomor WhatsApp</label>
                <input
                    v-model="form.phone_number"
                    type="text"
                    class="border p-2 w-full"
                    required
                />
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2">
                Simpan
            </button>
        </form>
    </div>
</template>
