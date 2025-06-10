<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

defineOptions({ layout: GuestLayout });
</script>

<template>
    <Head title="Daftar Akun" />
    <div
        class="min-h-screen flex items-center justify-center pt-20 px-4 pb-4 xl:pt-12"
    >
        <div
            class="w-full max-w-6xl bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100"
        >
            <div class="flex flex-col lg:flex-row-reverse">
                <!-- Left Column - Illustration -->
                <div
                    class="lg:w-1/2 bg-blue-600 p-6 lg:p-8 flex flex-col items-center justify-center"
                >
                    <div class="text-center max-w-md w-full">
                        <h1
                            class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-4"
                        >
                            Mulai Daftar Sekarang!
                        </h1>
                        <img
                            src="/assets/media/register.png"
                            alt="Ilustrasi Daftar"
                            class="w-2/3 max-w-xs mx-auto my-6 lg:mb-6"
                        />
                        <p class="text-white text-sm sm:text-base lg:text-lg">
                            Daftar untuk membuat akun baru dan mulai menerima
                            notifikasi bencana terkini.
                        </p>
                        <div class="mt-4 lg:mt-6">
                            <Link
                                :href="route('login')"
                                class="text-blue-200 hover:text-white font-medium inline-flex items-center text-sm sm:text-base"
                            >
                                <span>Sudah punya akun? Masuk disini</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Register Form -->
                <div class="lg:w-1/2 p-6 flex items-center justify-center">
                    <div class="w-full max-w-md">
                        <div class="text-center mb-6">
                            <h2
                                class="text-xl sm:text-2xl font-bold text-gray-800"
                            >
                                Buat Akun Baru
                            </h2>
                            <p class="text-gray-600 mt-1 text-sm sm:text-base">
                                Isi form berikut untuk mendaftar
                            </p>
                        </div>

                        <form
                            @submit.prevent="submit"
                            class="space-y-4 xl:space-y-6"
                        >
                            <div>
                                <InputLabel
                                    for="name"
                                    value="Nama Lengkap"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                />
                                <TextInput
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    class="w-full px-4 py-2 xl:py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 text-sm sm:text-base"
                                    placeholder="Masukkan nama lengkap"
                                />
                                <InputError
                                    class="mt-1 sm:mt-2 text-sm text-red-600"
                                    :message="form.errors.name"
                                />
                            </div>

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
                                    autocomplete="email"
                                    class="w-full px-4 py-2 xl:py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 text-sm sm:text-base"
                                    placeholder="contoh@email.com"
                                />
                                <InputError
                                    class="mt-1 sm:mt-2 text-sm text-red-600"
                                    :message="form.errors.email"
                                />
                            </div>

                            <div>
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
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2 xl:py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 text-sm sm:text-base"
                                    placeholder="Buat password"
                                />
                                <InputError
                                    class="mt-1 sm:mt-2 text-sm text-red-600"
                                    :message="form.errors.password"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="password_confirmation"
                                    value="Konfirmasi Password"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2 xl:py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 text-sm sm:text-base"
                                    placeholder="Ulangi password"
                                />
                                <InputError
                                    class="mt-1 sm:mt-2 text-sm text-red-600"
                                    :message="form.errors.password_confirmation"
                                />
                            </div>

                            <div>
                                <PrimaryButton
                                    :class="{ 'opacity-70': form.processing }"
                                    :disabled="form.processing"
                                    class="w-full flex justify-center items-center gap-2 px-4 py-2 sm:px-6 sm:py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium disabled:cursor-not-allowed text-sm sm:text-base"
                                >
                                    <span v-if="!form.processing">
                                        <i class="fas fa-user-plus"></i>
                                    </span>
                                    <span v-else>
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                    Daftar
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
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
