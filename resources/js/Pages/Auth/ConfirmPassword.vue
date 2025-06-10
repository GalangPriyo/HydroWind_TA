<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Konfirmasi Password" />

    <div class="min-h-screen bg-blue-50 flex items-center justify-center p-4">
        <div
            class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden"
        >
            <div class="p-8 text-center">
                <div class="flex justify-center mb-6">
                    <img
                        src="/assets/media/confirm.png"
                        alt="Konfirmasi Password"
                        class="w-2/3 object-contain"
                    />
                </div>

                <h1 class="text-2xl font-bold text-gray-800 mb-2">
                    Konfirmasi Password
                </h1>

                <p class="text-gray-600 mb-6">
                    Ini adalah area aman dari aplikasi. Harap konfirmasi
                    password Anda sebelum melanjutkan.
                </p>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="text-left">
                        <InputLabel
                            for="password"
                            value="Password"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        />
                        <TextInput
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            autofocus
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            placeholder="Masukkan password Anda"
                        />
                        <InputError
                            class="mt-1 text-sm text-red-600"
                            :message="form.errors.password"
                        />
                    </div>

                    <div>
                        <PrimaryButton
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                            class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500"
                        >
                            <span v-if="!form.processing">Konfirmasi</span>
                            <span
                                v-else
                                class="flex items-center justify-center"
                            >
                                <svg
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                Memproses...
                            </span>
                        </PrimaryButton>
                    </div>
                </form>

                <div class="pt-4 border-t border-gray-100 mt-6">
                    <p class="text-xs text-gray-500">
                        Pastikan Anda memasukkan password yang benar untuk
                        mengakses halaman selanjutnya.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth transitions for interactive elements */
* {
    transition-property: background-color, border-color, color, fill, stroke,
        opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>
