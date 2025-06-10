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

    <div class="min-h-screen bg-blue-50 flex items-center justify-center p-4">
        <div
            class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden"
        >
            <div class="p-8 text-center">
                <div class="flex justify-center mb-6">
                    <img
                        src="/assets/media/verifikasi1.png"
                        alt="Email Verification"
                        class="w-2/3 object-contain"
                    />
                </div>

                <h1 class="text-2xl font-bold text-gray-800 mb-2">
                    Verifikasi Email Anda
                </h1>

                <p class="text-gray-600 mb-6">
                    Terima kasih telah mendaftar! Sebelum melanjutkan, silakan
                    verifikasi alamat email Anda dengan mengklik tautan yang
                    kami kirimkan.
                </p>

                <div
                    v-if="verificationLinkSent"
                    class="mb-6 p-3 bg-green-50 text-green-700 rounded-lg text-sm"
                >
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <PrimaryButton
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        class="w-full justify-center py-3"
                    >
                        <span v-if="!form.processing"
                            >Kirim Ulang Email Verifikasi</span
                        >
                        <span v-else>Mengirim...</span>
                    </PrimaryButton>

                    <div class="pt-2 border-t border-gray-100">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full text-center text-gray-600 hover:text-gray-800 font-medium"
                        >
                            Keluar
                        </Link>
                    </div>
                </form>

                <p class="mt-6 text-xs text-gray-500">
                    Jika Anda tidak menerima email, periksa folder spam atau
                    hubungi dukungan kami.
                </p>
            </div>
        </div>
    </div>
</template>
