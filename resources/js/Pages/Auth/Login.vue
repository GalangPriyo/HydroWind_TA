<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

defineOptions({ layout: GuestLayout });
</script>

<template>
    <Head title="Log in" />
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row-reverse">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold">Login now!</h1>
                <p class="py-6">
                    Provident cupiditate voluptatem et in. Quaerat fugiat ut
                    assumenda excepturi exercitationem quasi. In deleniti eaque
                    aut repudiandae et a id nisi.
                </p>
            </div>
            <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
                <form class="card-body" @submit.prevent="submit">
                    <div class="form-control">
                        <label class="label">
                            <InputLabel
                                for="email"
                                value="Email"
                                class="label-text"
                            />
                        </label>
                        <TextInput
                            id="email"
                            type="email"
                            placeholder="Email"
                            class="input input-bordered"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <InputLabel
                                for="password"
                                value="Password"
                                class="label-text"
                            />
                        </label>
                        <TextInput
                            id="password"
                            type="password"
                            placeholder="Password"
                            class="input input-bordered"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                        <label class="label" v-if="canResetPassword">
                            <Link
                                :href="route('password.request')"
                                class="label-text-alt link link-hover"
                                >Forgot password?</Link
                            >
                        </label>
                    </div>
                    <div class="form-control mt-4">
                        <label class="flex items-center">
                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember"
                            />
                            <span class="ms-2 text-sm text-gray-600"
                                >Remember me</span
                            >
                        </label>
                    </div>
                    <div class="form-control mt-6">
                        <PrimaryButton
                            class="btn btn-primary w-full"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Log in
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
