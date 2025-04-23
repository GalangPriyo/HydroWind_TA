<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";

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
        <Head title="Forgot Password" />

        <div
            class="flex items-center justify-center min-h-screen bg-gradient-to-b from-blue-200 to-cyan-200 px-4"
        >
            <div class="card glass w-full max-w-4xl shadow-xl p-8">
                <!-- Gunakan flex di dalam card-body -->
                <div
                    class="card-body md:flex md:flex-row md:items-center md:gap-8 gap-10"
                >
                    <!-- Gambar di kiri -->
                    <div
                        class="md:w-1/2 w-full flex justify-center mb-6 md:mb-0"
                    >
                        <img
                            src="/assets/media/forgot1.png"
                            alt="Verifikasi Email"
                            class="w-full object-contain"
                        />
                    </div>

                    <!-- Form di kanan -->
                    <div class="md:w-1/2 w-full">
                        <h2 class="text-3xl font-bold text-center">
                            Lupa Kata Sandi
                        </h2>
                        <p class="text-md text-gray-600 text-center my-8">
                            Lupa kata sandi Anda? Tidak masalah. <br />
                            Beri tahu kami alamat email Anda dan kami akan
                            mengirimkan tautan pengaturan ulang kata sandi
                            melalui email yang memungkinkan Anda memilih kata
                            sandi baru.
                        </p>

                        <div
                            v-if="status"
                            class="mb-4 text-sm font-medium text-green-600 text-center"
                        >
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="form-control">
                                <InputLabel for="email" value="Email" />

                                <TextInput
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    class="input input-bordered w-full mt-1"
                                />

                                <InputError
                                    class="mt-1 text-red-600 text-sm"
                                    :message="form.errors.email"
                                />
                            </div>

                            <div class="form-control">
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="btn btn-primary w-full"
                                >
                                    Kirim Link Reset Password
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
