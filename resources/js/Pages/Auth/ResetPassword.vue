<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div
            class="flex items-center justify-center min-h-screen bg-gradient-to-b from-blue-200 to-cyan-200 px-4"
        >
            <div class="card glass w-full max-w-4xl shadow-xl py-5 px-8">
                <!-- Flexbox dalam card-body -->
                <div
                    class="card-body md:flex md:flex-row md:items-center md:gap-8 gap-10"
                >
                    <!-- Gambar reset password -->
                    <div
                        class="md:w-1/2 w-full flex justify-center mb-6 md:mb-0"
                    >
                        <img
                            src="/assets/media/reset.png"
                            alt="Reset Password"
                            class="w-full object-contain"
                        />
                    </div>

                    <!-- Form reset password -->
                    <div class="md:w-1/2 w-full">
                        <h2 class="text-3xl font-bold text-center">
                            Reset Password
                        </h2>
                        <p class="text-md text-gray-600 text-center my-4">
                            Masukkan email dan password baru Anda untuk mengatur
                            ulang kata sandi.
                        </p>

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
                                <InputLabel
                                    for="password"
                                    value="Password Baru"
                                />
                                <TextInput
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    class="input input-bordered w-full mt-1"
                                />
                                <InputError
                                    class="mt-1 text-red-600 text-sm"
                                    :message="form.errors.password"
                                />
                            </div>

                            <div class="form-control">
                                <InputLabel
                                    for="password_confirmation"
                                    value="Konfirmasi Password"
                                />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="input input-bordered w-full mt-1"
                                />
                                <InputError
                                    class="mt-1 text-red-600 text-sm"
                                    :message="form.errors.password_confirmation"
                                />
                            </div>

                            <div class="form-control">
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="btn btn-primary w-full"
                                >
                                    Reset Password
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
