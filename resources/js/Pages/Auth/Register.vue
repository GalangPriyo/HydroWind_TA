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
    errors: {},
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

defineOptions({ layout: GuestLayout });
</script>

<template>
    <Head title="Register" />
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold">Register now!</h1>
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
                                for="name"
                                value="Name"
                                class="label-text"
                            />
                        </label>
                        <TextInput
                            id="name"
                            type="text"
                            placeholder="Name"
                            class="input input-bordered"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
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
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <InputLabel
                                for="password_confirmation"
                                value="Confirm Password"
                                class="label-text"
                            />
                        </label>
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            placeholder="Confirm Password"
                            class="input input-bordered"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password_confirmation"
                        />
                    </div>
                    <div class="form-control mt-4">
                        <Link
                            :href="route('login')"
                            class="label-text-alt link link-hover"
                            >Already registered?</Link
                        >
                    </div>
                    <div class="form-control mt-6">
                        <PrimaryButton
                            class="btn btn-primary w-full"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Register
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
