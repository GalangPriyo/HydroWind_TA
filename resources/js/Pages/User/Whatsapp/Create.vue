<script setup>
import { ref } from "vue";
import { useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

defineOptions({ layout: AuthenticatedLayout });
defineProps({
    user: Object, // User yang sedang login
});

const form = useForm({
    phone_number: ref(""),
});

const isValidPhoneNumber = (number) => {
    // Hilangkan spasi
    const trimmed = number.trim();

    // Regex: +62 atau 08, diikuti digit, panjang minimal 10-13 angka
    return /^(\+62|62|08)[0-9]{8,13}$/.test(trimmed);
};

const submit = () => {
    if (!isValidPhoneNumber(form.phone_number)) {
        Swal.fire({
            icon: "warning",
            title: "Nomor Tidak Valid",
            text: "Masukkan nomor WhatsApp dengan awalan 08xxx",
        });
        return;
    }

    form.post(route("user.whatsapp.store"), {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Nomor WhatsApp berhasil disimpan!",
                timer: 3000,
                showConfirmButton: false,
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Gagal menyimpan nomor. Silakan coba lagi.",
            });
        },
    });
};
</script>

<template>
    <Head title="WhatsApp" />
    <div class="flex flex-col gap-4 items-start justify-center">
        <!-- Tombol Back -->
        <button
            @click="$inertia.visit(route('user.whatsapp'))"
            class="flex items-center gap-2 text-blue-900 hover:text-blue-600 font-semibold"
        >
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>

        <!-- Card -->
        <div
            class="card w-full max-w-5xl bg-gradient-to-b from-blue-200 to-cyan-200 shadow-xl self-center my-2"
        >
            <div class="card-body flex flex-col md:flex-row items-center gap-8">
                <!-- Form -->
                <div class="w-full md:w-1/2">
                    <h1 class="text-3xl font-bold mb-4">
                        Tambah Nomor WhatsApp
                    </h1>
                    <p class="text-md text-gray-600 mb-4">
                        Dengan mendaftarkan nomor WhatsApp, Anda akan menerima
                        notifikasi otomatis jika terdeteksi adanya potensi
                        bencana di wilayah Anda.
                    </p>
                    <form
                        @submit.prevent="submit"
                        class="flex flex-col sm:flex-row gap-4 w-full"
                    >
                        <div class="form-control flex-1">
                            <label for="phone_number" class="label px-0">
                                <span class="label-text">Nomor WhatsApp</span>
                            </label>
                            <input
                                v-model="form.phone_number"
                                type="text"
                                id="phone_number"
                                class="input input-bordered w-full"
                                required
                            />
                        </div>

                        <div class="form-control w-full sm:w-auto sm:self-end">
                            <button
                                type="submit"
                                class="btn btn-primary w-full sm:w-auto"
                            >
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Gambar -->
                <div class="w-full md:w-1/2 flex justify-center">
                    <img
                        src="/assets/media/add-wa.png"
                        alt="Tambah Nomor WhatsApp"
                        class="w-3/4 object-contain"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
