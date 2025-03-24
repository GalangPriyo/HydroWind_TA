<script setup>
import { Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

defineOptions({ layout: GuestLayout });
</script>

<template>
    <div class="min-h-screen flex items-center justify-center pt-20 xl:pt-0">
        <div
            class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 grid-rows-2 gap-4"
        >
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
                                        Saat Terjadi Banjir
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Jaga Keselamatan
                                                    Diri:</span
                                                >
                                                Jika air mulai naik, segera
                                                pindah ke tempat yang lebih
                                                tinggi dan hindari berjalan di
                                                air yang mengalir deras.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Matikan Listrik dan
                                                    Gas:</span
                                                >
                                                Cabut semua peralatan listrik
                                                dan matikan aliran listrik serta
                                                gas untuk menghindari korsleting
                                                atau ledakan.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Gunakan Peralatan
                                                    Keselamatan:</span
                                                >
                                                Jika harus melewati air, gunakan
                                                pelampung atau benda yang bisa
                                                mengapung seperti jerigen atau
                                                ban bekas.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Hindari Air yang Terlihat
                                                    Tercemar:</span
                                                >
                                                Jangan menyentuh atau meminum
                                                air banjir karena bisa
                                                terkontaminasi bahan kimia atau
                                                limbah berbahaya.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Jangan Panik dan Tetap
                                                    Tenang:</span
                                                >
                                                Cari pertolongan dan hubungi
                                                pihak berwenang jika diperlukan.
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
                                        Setelah Banjir Surut
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Periksa Keselamatan
                                                    Lingkungan:</span
                                                >
                                                Hindari kabel listrik yang
                                                terendam, lubang tersembunyi,
                                                atau struktur bangunan yang
                                                tampak rapuh.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Bersihkan Rumah dengan
                                                    Hati-hati:</span
                                                >
                                                Gunakan sarung tangan dan masker
                                                saat membersihkan lumpur atau
                                                air yang menggenang.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Cek Kesehatan:</span
                                                >
                                                Jika merasa tidak sehat atau
                                                terkena penyakit akibat banjir,
                                                segera periksakan diri ke
                                                fasilitas kesehatan.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Pastikan Air Minum
                                                    Aman:</span
                                                >
                                                Rebus air sebelum dikonsumsi
                                                atau gunakan air kemasan yang
                                                aman.
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
                                        Sebelum Angin Kencang
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
                                                    >Amankan Barang di Luar
                                                    Rumah:</span
                                                >
                                                Pastikan benda-benda seperti pot
                                                bunga, jemuran, dan perabotan
                                                luar ruangan tidak mudah terbawa
                                                angin.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Periksa Kondisi
                                                    Rumah:</span
                                                >
                                                Perkuat atap, jendela, dan pintu
                                                agar tidak mudah rusak saat
                                                terkena angin kencang.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Siapkan Peralatan
                                                    Darurat:</span
                                                >
                                                Sediakan senter, power bank,
                                                radio, makanan tahan lama, dan
                                                air minum dalam jumlah cukup.
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
                                        Saat Angin Kencang Terjadi
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Tetap di Dalam
                                                    Ruangan:</span
                                                >
                                                Jangan keluar rumah kecuali
                                                dalam keadaan darurat, dan
                                                hindari berada di dekat jendela
                                                atau pintu kaca.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Matikan Peralatan Listrik
                                                    yang Tidak Diperlukan:</span
                                                >
                                                Untuk mencegah korsleting akibat
                                                kabel listrik yang putus, cabut
                                                peralatan elektronik yang tidak
                                                digunakan.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Jangan Berlindung di Bawah
                                                    Pohon atau Baliho:</span
                                                >
                                                Hindari berteduh di bawah pohon,
                                                baliho, atau bangunan yang
                                                tampak rapuh karena berisiko
                                                roboh.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Cari Tempat Perlindungan
                                                    yang Aman:</span
                                                >
                                                Jika berada di luar ruangan,
                                                segera cari bangunan kokoh untuk
                                                berlindung.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Tetap Tenang dan
                                                    Waspada:</span
                                                >
                                                Jangan panik, tetap pantau
                                                informasi terbaru, dan ikuti
                                                arahan dari pihak berwenang.
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
                                        Setelah Angin Kencang
                                    </h3>
                                    <ul class="list-none space-y-2">
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Periksa Kondisi
                                                    Lingkungan:</span
                                                >
                                                Waspadai kabel listrik yang
                                                jatuh, puing bangunan, atau
                                                pohon tumbang yang bisa
                                                membahayakan.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Cek Keadaan Rumah:</span
                                                >
                                                Periksa atap, jendela, dan
                                                dinding rumah untuk memastikan
                                                tidak ada kerusakan yang
                                                berbahaya.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Bersihkan dengan
                                                    Hati-hati:</span
                                                >
                                                Gunakan sarung tangan dan sepatu
                                                saat membersihkan puing atau
                                                benda tajam yang berhamburan.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Laporkan Kerusakan kepada
                                                    Pihak Berwenang:</span
                                                >
                                                Jika ada kabel listrik putus
                                                atau infrastruktur rusak, segera
                                                hubungi dinas terkait.
                                            </span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span
                                                class="text-blue-700 font-bold"
                                                >•</span
                                            >
                                            <span class="pl-1 text-gray-800">
                                                <span class="font-semibold"
                                                    >Pastikan Air dan Makanan
                                                    Aman Dikonsumsi:</span
                                                >
                                                Jika listrik padam, periksa
                                                bahan makanan di kulkas dan
                                                pastikan air minum dalam keadaan
                                                bersih.
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
