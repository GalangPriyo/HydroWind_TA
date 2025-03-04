<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

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
            alert("Nomor WhatsApp berhasil diperbarui!");
        },
    });
};
</script>

<template>
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Edit Nomor WhatsApp</h2>
        <form @submit.prevent="submit">
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
                >
                    {{ form.errors.phone_number }}
                </span>
            </div>
            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded"
                :disabled="form.processing"
            >
                Simpan Perubahan
            </button>
        </form>
    </div>
</template>
