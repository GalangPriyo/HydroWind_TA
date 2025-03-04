<script setup>
import { Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

defineOptions({ layout: GuestLayout });
</script>

<template>
    <div class="min-h-screen flex items-center justify-center">
        <div class="grid grid-cols-3 grid-rows-2 gap-4">
            <div class="flex justify-center items-center">
                <div class="card bg-base-100 w-96 shadow-xl">
                    <div class="card-body flex justify-center items-center p-0">
                        <!-- You can open the modal using ID.showModal() method -->
                        <button
                            class="w-64 h-64 p-0 border-none bg-black"
                            onclick="my_modal_1.showModal()"
                        >
                            <img
                                src="/assets/media/SebelumBanjir.gif"
                                alt="Sebelum Banjir"
                                class="w-64 h-auto"
                            />
                        </button>
                        <dialog id="my_modal_1" class="modal">
                            <div class="modal-box w-11/12 max-w-5xl">
                                <form method="dialog">
                                    <button
                                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                                    >
                                        ✕
                                    </button>
                                </form>
                                <h3
                                    class="text-lg font-bold text-blue-700 mb-2"
                                >
                                    Sebelum Banjir
                                </h3>
                                <ul class="list-none space-y-2">
                                    <li class="flex gap-2">
                                        <span class="text-blue-700 font-bold"
                                            >•</span
                                        >
                                        <span class="pl-1 text-gray-800">
                                            <span class="font-semibold"
                                                >Pantau Informasi Cuaca:</span
                                            >
                                            Selalu periksa prakiraan cuaca dan
                                            peringatan dini dari BMKG atau
                                            otoritas setempat.
                                        </span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="text-blue-700 font-bold"
                                            >•</span
                                        >
                                        <span class="pl-1 text-gray-800">
                                            <span class="font-semibold"
                                                >Siapkan Tas Darurat:</span
                                            >
                                            Isi dengan dokumen penting (KTP, KK,
                                            sertifikat rumah), makanan tahan
                                            lama, obat-obatan, senter, power
                                            bank, dan pakaian ganti.
                                        </span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="text-blue-700 font-bold"
                                            >•</span
                                        >
                                        <span class="pl-1 text-gray-800">
                                            <span class="font-semibold"
                                                >Amankan Peralatan
                                                Listrik:</span
                                            >
                                            Pindahkan barang elektronik dan
                                            dokumen penting ke tempat yang lebih
                                            tinggi.
                                        </span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="text-blue-700 font-bold"
                                            >•</span
                                        >
                                        <span class="pl-1 text-gray-800">
                                            <span class="font-semibold"
                                                >Buat Rencana Evakuasi:</span
                                            >
                                            Tentukan jalur dan tempat evakuasi
                                            terdekat, serta berkomunikasi dengan
                                            tetangga atau keluarga tentang
                                            rencana darurat.
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </dialog>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-center items-center">
                    <div class="card bg-base-100 w-96 shadow-xl">
                        <div
                            class="card-body flex justify-center items-center p-0"
                        >
                            <!-- You can open the modal using ID.showModal() method -->
                            <button
                                class="w-64 h-64 p-0 border-none bg-black"
                                onclick="my_modal_2.showModal()"
                            >
                                <img
                                    src="/assets/media/SaatBanjir.gif"
                                    alt="Sebelum Banjir"
                                    class="w-64 h-auto"
                                />
                            </button>
                            <dialog id="my_modal_2" class="modal">
                                <div class="modal-box w-11/12 max-w-5xl">
                                    <form method="dialog">
                                        <button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                                        >
                                            ✕
                                        </button>
                                    </form>
                                    <h3
                                        class="text-lg font-bold text-blue-700 mb-2"
                                    >
                                        Sebelum Banjir
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Pantau Informasi
                                                    Cuaca:</span
                                                >
                                                Selalu periksa prakiraan cuaca
                                                dan peringatan dini dari BMKG
                                                atau otoritas setempat.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Siapkan Tas Darurat:</span
                                                >
                                                Isi dengan dokumen penting (KTP,
                                                KK, sertifikat rumah), makanan
                                                tahan lama, obat-obatan, senter,
                                                power bank, dan pakaian ganti.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Amankan Peralatan
                                                    Listrik:</span
                                                >
                                                Pindahkan barang elektronik dan
                                                dokumen penting ke tempat yang
                                                lebih tinggi.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Buat Rencana
                                                    Evakuasi:</span
                                                >
                                                Tentukan jalur dan tempat
                                                evakuasi terdekat, serta
                                                berkomunikasi dengan tetangga
                                                atau keluarga tentang rencana
                                                darurat.
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </dialog>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-center items-center">
                    <div class="card bg-base-100 w-96 shadow-xl">
                        <div
                            class="card-body flex justify-center items-center p-0"
                        >
                            <!-- You can open the modal using ID.showModal() method -->
                            <button
                                class="w-64 h-64 p-0 border-none bg-black"
                                onclick="my_modal_3.showModal()"
                            >
                                <img
                                    src="/assets/media/SetelahBanjir.gif"
                                    alt="Sebelum Banjir"
                                    class="w-64 h-auto"
                                />
                            </button>
                            <dialog id="my_modal_3" class="modal">
                                <div class="modal-box w-11/12 max-w-5xl">
                                    <form method="dialog">
                                        <button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                                        >
                                            ✕
                                        </button>
                                    </form>
                                    <h3
                                        class="text-lg font-bold text-blue-700 mb-2"
                                    >
                                        Sebelum Banjir
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Pantau Informasi
                                                    Cuaca:</span
                                                >
                                                Selalu periksa prakiraan cuaca
                                                dan peringatan dini dari BMKG
                                                atau otoritas setempat.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Siapkan Tas Darurat:</span
                                                >
                                                Isi dengan dokumen penting (KTP,
                                                KK, sertifikat rumah), makanan
                                                tahan lama, obat-obatan, senter,
                                                power bank, dan pakaian ganti.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Amankan Peralatan
                                                    Listrik:</span
                                                >
                                                Pindahkan barang elektronik dan
                                                dokumen penting ke tempat yang
                                                lebih tinggi.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Buat Rencana
                                                    Evakuasi:</span
                                                >
                                                Tentukan jalur dan tempat
                                                evakuasi terdekat, serta
                                                berkomunikasi dengan tetangga
                                                atau keluarga tentang rencana
                                                darurat.
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </dialog>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-center items-center">
                    <div class="card bg-base-100 w-96 shadow-xl">
                        <div
                            class="card-body flex justify-center items-center p-0"
                        >
                            <!-- You can open the modal using ID.showModal() method -->
                            <button
                                class="w-52 h-64 p-0 border-none bg-transparent"
                                onclick="my_modal_4.showModal()"
                            >
                                <img
                                    src="/assets/media/SebelumAngin.gif"
                                    alt="Sebelum Banjir"
                                    class="w-52 h-auto"
                                />
                            </button>
                            <dialog id="my_modal_4" class="modal">
                                <div class="modal-box w-11/12 max-w-5xl">
                                    <form method="dialog">
                                        <button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                                        >
                                            ✕
                                        </button>
                                    </form>
                                    <h3
                                        class="text-lg font-bold text-blue-700 mb-2"
                                    >
                                        Sebelum Banjir
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Pantau Informasi
                                                    Cuaca:</span
                                                >
                                                Selalu periksa prakiraan cuaca
                                                dan peringatan dini dari BMKG
                                                atau otoritas setempat.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Siapkan Tas Darurat:</span
                                                >
                                                Isi dengan dokumen penting (KTP,
                                                KK, sertifikat rumah), makanan
                                                tahan lama, obat-obatan, senter,
                                                power bank, dan pakaian ganti.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Amankan Peralatan
                                                    Listrik:</span
                                                >
                                                Pindahkan barang elektronik dan
                                                dokumen penting ke tempat yang
                                                lebih tinggi.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Buat Rencana
                                                    Evakuasi:</span
                                                >
                                                Tentukan jalur dan tempat
                                                evakuasi terdekat, serta
                                                berkomunikasi dengan tetangga
                                                atau keluarga tentang rencana
                                                darurat.
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </dialog>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-center items-center">
                    <div class="card bg-base-100 w-96 shadow-xl">
                        <div
                            class="card-body flex justify-center items-center p-0"
                        >
                            <!-- You can open the modal using ID.showModal() method -->
                            <button
                                class="w-64 h-64 p-0 border-none bg-black"
                                onclick="my_modal_5.showModal()"
                            >
                                <img
                                    src="/assets/media/SaatAngin.gif"
                                    alt="Sebelum Banjir"
                                    class="w-64 h-auto"
                                />
                            </button>
                            <dialog id="my_modal_5" class="modal">
                                <div class="modal-box w-11/12 max-w-5xl">
                                    <form method="dialog">
                                        <button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                                        >
                                            ✕
                                        </button>
                                    </form>
                                    <h3
                                        class="text-lg font-bold text-blue-700 mb-2"
                                    >
                                        Sebelum Banjir
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Pantau Informasi
                                                    Cuaca:</span
                                                >
                                                Selalu periksa prakiraan cuaca
                                                dan peringatan dini dari BMKG
                                                atau otoritas setempat.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Siapkan Tas Darurat:</span
                                                >
                                                Isi dengan dokumen penting (KTP,
                                                KK, sertifikat rumah), makanan
                                                tahan lama, obat-obatan, senter,
                                                power bank, dan pakaian ganti.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Amankan Peralatan
                                                    Listrik:</span
                                                >
                                                Pindahkan barang elektronik dan
                                                dokumen penting ke tempat yang
                                                lebih tinggi.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Buat Rencana
                                                    Evakuasi:</span
                                                >
                                                Tentukan jalur dan tempat
                                                evakuasi terdekat, serta
                                                berkomunikasi dengan tetangga
                                                atau keluarga tentang rencana
                                                darurat.
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </dialog>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-center items-center">
                    <div class="card bg-base-100 w-96 shadow-xl">
                        <div
                            class="card-body flex justify-center items-center p-0"
                        >
                            <!-- You can open the modal using ID.showModal() method -->
                            <button
                                class="w-48 h-64 p-0 border-none bg-transparent"
                                onclick="my_modal_6.showModal()"
                            >
                                <img
                                    src="/assets/media/SetelahAngin.gif"
                                    alt="Sebelum Banjir"
                                    class="w-48 h-auto"
                                />
                            </button>
                            <dialog id="my_modal_6" class="modal">
                                <div class="modal-box w-11/12 max-w-5xl">
                                    <form method="dialog">
                                        <button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                                        >
                                            ✕
                                        </button>
                                    </form>
                                    <h3
                                        class="text-lg font-bold text-blue-700 mb-2"
                                    >
                                        Sebelum Banjir
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Pantau Informasi
                                                    Cuaca:</span
                                                >
                                                Selalu periksa prakiraan cuaca
                                                dan peringatan dini dari BMKG
                                                atau otoritas setempat.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Siapkan Tas Darurat:</span
                                                >
                                                Isi dengan dokumen penting (KTP,
                                                KK, sertifikat rumah), makanan
                                                tahan lama, obat-obatan, senter,
                                                power bank, dan pakaian ganti.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Amankan Peralatan
                                                    Listrik:</span
                                                >
                                                Pindahkan barang elektronik dan
                                                dokumen penting ke tempat yang
                                                lebih tinggi.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Buat Rencana
                                                    Evakuasi:</span
                                                >
                                                Tentukan jalur dan tempat
                                                evakuasi terdekat, serta
                                                berkomunikasi dengan tetangga
                                                atau keluarga tentang rencana
                                                darurat.
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </dialog>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
