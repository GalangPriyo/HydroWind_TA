<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Kata Sandi" />
        <div
            class="min-h-screen flex items-center justify-center bg-gray-100 pt-20 px-4 pb-4 xl:pt-12"
        >
            <div
                class="w-full max-w-6xl bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100"
            >
                <div class="flex flex-col lg:flex-row-reverse">
                    <!-- Changed to flex-row-reverse -->
                    <!-- Right Column - Illustration (now first in DOM order) -->
                    <div
                        class="lg:w-1/2 bg-blue-600 p-6 lg:p-8 flex flex-col items-center justify-center"
                    >
                        <div class="text-center max-w-md w-full">
                            <h1
                                class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-4"
                            >
                                Lupa Kata Sandi
                            </h1>
                            <img
                                src="/assets/media/forgot1.png"
                                alt="Reset Password"
                                class="w-1/2 max-w-xs mx-auto my-6 lg:mb-6"
                            />
                            <p
                                class="text-white text-sm sm:text-base lg:text-lg"
                            >
                                Masukkan email Anda untuk menerima tautan reset
                                password.
                            </p>
                            <div class="mt-4 lg:mt-6">
                                <Link
                                    :href="route('login')"
                                    class="text-blue-200 hover:text-white font-medium inline-flex items-center text-sm sm:text-base"
                                >
                                    <span>Kembali ke halaman login</span>
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Left Column - Form -->
                    <div
                        class="lg:w-1/2 p-6 sm:p-8 flex items-center justify-center"
                    >
                        <div class="w-full max-w-md">
                            <div class="text-center mb-6 sm:mb-8">
                                <img
                                    src="/assets/media/HydroWind.jpeg"
                                    alt="HydroWind Logo"
                                    class="w-12 h-12 sm:w-16 sm:h-16 rounded-full mx-auto mb-3 sm:mb-4"
                                />
                                <h2
                                    class="text-xl sm:text-2xl font-bold text-gray-800"
                                >
                                    Lupa Kata Sandi
                                </h2>
                                <p
                                    class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base"
                                >
                                    Masukkan email Anda untuk menerima link
                                    reset password
                                </p>
                            </div>

                            <!-- Status Message -->
                            <div
                                v-if="status"
                                class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 text-green-700 rounded-xl text-center text-sm sm:text-base"
                            >
                                {{ status }}
                            </div>

                            <form
                                @submit.prevent="submit"
                                class="space-y-4 sm:space-y-6"
                            >
                                <div>
                                    <InputLabel
                                        for="email"
                                        value="Alamat Email"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    />
                                    <TextInput
                                        id="email"
                                        type="email"
                                        v-model="form.email"
                                        required
                                        autofocus
                                        autocomplete="email"
                                        class="w-full px-4 py-2 sm:py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 text-sm sm:text-base"
                                        placeholder="contoh@email.com"
                                    />
                                    <InputError
                                        class="mt-1 sm:mt-2 text-sm text-red-600"
                                        :message="form.errors.email"
                                    />
                                </div>

                                <div>
                                    <PrimaryButton
                                        :class="{
                                            'opacity-70': form.processing,
                                        }"
                                        :disabled="form.processing"
                                        class="w-full flex justify-center items-center gap-2 px-4 py-2 sm:px-6 sm:py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium disabled:cursor-not-allowed text-sm sm:text-base"
                                    >
                                        <span v-if="!form.processing">
                                            <i class="fas fa-paper-plane"></i>
                                        </span>
                                        <span v-else>
                                            <i
                                                class="fas fa-spinner fa-spin"
                                            ></i>
                                        </span>
                                        Kirim Link Reset
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
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
