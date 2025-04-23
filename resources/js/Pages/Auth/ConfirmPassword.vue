<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
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
    <GuestLayout>
        <Head title="Konfirmasi Password" />

        <div
            class="min-h-screen bg-gradient-to-b from-blue-200 to-cyan-200 flex items-center justify-center pt-20 pb-10"
        >
            <div class="card glass w-full max-w-xl shadow-lg">
                <div class="card-body items-center text-center">
                    <h1 class="text-3xl font-bold">Konfirmasi Password</h1>
                    <img
                        src="/assets/media/confirm.png"
                        alt="Konfirmasi Password"
                        class="w-1/2 py-6"
                    />
                    <p class="text-md text-gray-600 mb-4">
                        Ini adalah area aman dari aplikasi. <br />
                        Harap konfirmasi password Anda sebelum melanjutkan.
                    </p>

                    <form @submit.prevent="submit" class="w-full mt-4">
                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-end gap-4"
                        >
                            <div class="flex-1 form-control w-full">
                                <InputLabel
                                    for="password"
                                    value="Password"
                                    class="text-left w-full"
                                />
                                <TextInput
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    autofocus
                                    class="input input-bordered w-full mt-1"
                                />
                                <InputError
                                    class="mt-1 text-red-600 text-sm"
                                    :message="form.errors.password"
                                />
                            </div>

                            <div class="form-control w-full sm:w-auto">
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="btn btn-primary w-full sm:w-auto"
                                >
                                    Konfirmasi
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
