<script setup>
import { computed } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
    <Head title="Email Verification" />

    <div
        class="min-h-screen bg-gradient-to-b from-blue-200 to-cyan-200 flex items-center justify-center py-10"
    >
        <div class="card glass w-full max-w-xl shadow-lg">
            <div class="card-body items-center text-center">
                <h1 class="text-3xl font-bold">Verifikasi Email Anda</h1>
                <img
                    src="/assets/media/verifikasi1.png"
                    alt="Verifikasi Email"
                    class="w-2/3 py-6"
                />
                <p class="text-md text-gray-600">
                    Terima kasih telah mendaftar! <br />
                    Silakan klik tautan verifikasi yang telah kami kirimkan ke
                    email Anda. Jika belum menerimanya, kami dapat mengirim
                    ulang email untuk Anda.
                </p>

                <div
                    class="text-sm font-medium text-green-600"
                    v-if="verificationLinkSent"
                >
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>

                <form @submit.prevent="submit" class="w-full mt-4">
                    <div
                        class="flex flex-col sm:flex-row items-center justify-between gap-4"
                    >
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            class="w-full sm:w-72"
                        >
                            Kirim Ulang Email Verifikasi
                        </PrimaryButton>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full sm:w-72 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-center"
                        >
                            KELUAR
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
