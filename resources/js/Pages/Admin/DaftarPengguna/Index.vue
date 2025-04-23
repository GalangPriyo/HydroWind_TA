<script setup>
import { computed, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

defineOptions({ layout: AuthenticatedLayout });
const { user, users } = defineProps({
    user: Object,
    users: Object,
});
const searchQuery = ref("");

const filteredLinks = computed(() => {
    return users.links.filter((link) => link.label !== "...");
});

const confirmDelete = (id) => {
    Swal.fire({
        title: "Hapus Akun Pengguna?",
        text: "Tindakan ini akan menghapus Akun Pengguna yang Anda pilih.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.pengguna.delete", { id }));
        }
    });
};

const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.data;
    const keyword = searchQuery.value.toLowerCase();
    return users.data.filter((u) => {
        const name = u.name.toLowerCase();
        const email = u.email.toLowerCase();
        const phone = u.whatsapp?.phone_number?.toLowerCase() || "";
        return (
            name.includes(keyword) ||
            email.includes(keyword) ||
            phone.includes(keyword)
        );
    });
});
</script>

<template>
    <div class="bg-base200 flex items-center justify-center">
        <div
            class="card bg-gradient-to-b from-blue-200 to-cyan-200 w-full max-w-6xl shadow-lg"
        >
            <div class="card-body">
                <h2 class="text-3xl font-bold text-center">Daftar Pengguna</h2>

                <div
                    v-if="filteredUsers.length === 0"
                    class="flex flex-col items-center justify-center mt-6"
                >
                    <img
                        src="/assets/media/no-data.png"
                        alt="Tambah Pengguna"
                        class="w-1/3 object-contain"
                    />
                    <p class="text-lg text-gray-600 py-4">
                        Tidak ada pengguna yang terdaftar.
                    </p>
                    <Link
                        :href="route('admin.pengguna.create')"
                        class="btn border-none bg-blue-600 text-white text-md rounded-md shadow hover:bg-blue-700 transition"
                    >
                        <i class="fa-solid fa-user-plus"></i>
                        Tambah Pengguna
                    </Link>
                </div>

                <template v-else>
                    <div class="flex justify-between items-center my-2">
                        <!-- Kolom Search di kiri -->
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari berdasarkan nama, email, atau nomor WA"
                            class="input input-bordered w-full max-w-xs input-sm"
                        />

                        <!-- Tombol-tombol di kanan -->
                        <div class="flex gap-2">
                            <a
                                :href="route('admin.pengguna.download')"
                                class="btn btn-sm border-none bg-green-600 text-white text-sm rounded-md shadow hover:bg-green-700 transition"
                            >
                                <i class="fa-solid fa-download"></i> Download
                                Data
                            </a>

                            <Link
                                :href="route('admin.pengguna.create')"
                                class="btn btn-sm border-none bg-blue-600 text-white text-sm rounded-md shadow hover:bg-blue-700 transition"
                            >
                                <i class="fa-solid fa-user-plus"></i>
                                Tambah Pengguna
                            </Link>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg">
                        <table class="table w-full">
                            <thead
                                class="bg-blue-100 text-blue-800 text-center text-base"
                            >
                                <tr>
                                    <th class="px-4 py-2">No</th>
                                    <th
                                        class="px-4 py-2 border-l border-blue-200"
                                    >
                                        Nama
                                    </th>
                                    <th
                                        class="px-4 py-2 border-l border-blue-200"
                                    >
                                        Email
                                    </th>
                                    <th
                                        class="px-4 py-2 border-l border-blue-200"
                                    >
                                        Nomor WA
                                    </th>
                                    <th
                                        class="px-4 py-2 border-l border-blue-200"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(user, index) in filteredUsers"
                                    :key="user.id"
                                    class="bg-blue-50"
                                >
                                    <td class="px-4 py-2 text-center">
                                        {{
                                            (users.current_page - 1) *
                                                users.per_page +
                                            index +
                                            1
                                        }}
                                    </td>
                                    <td class="px-4 py-2 border-l">
                                        {{ user.name }}
                                    </td>
                                    <td class="px-4 py-2 border-l">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-4 py-2 border-l">
                                        {{
                                            user.whatsapp
                                                ? user.whatsapp.phone_number
                                                : "Belum ada"
                                        }}
                                    </td>
                                    <td
                                        class="px-4 py-2 space-x-2 border-l text-center"
                                    >
                                        <Link
                                            :href="
                                                route('admin.pengguna.edit', {
                                                    id: user.id,
                                                })
                                            "
                                            class="bg-yellow-500 text-xs text-white btn btn-sm border-none rounded-md shadow hover:bg-yellow-600 transition"
                                        >
                                            <i
                                                class="fa-solid fa-pen-to-square"
                                            ></i>
                                            Edit
                                        </Link>
                                        <button
                                            @click="confirmDelete(user.id)"
                                            class="bg-red-600 text-xs text-white btn btn-sm border-none rounded-md shadow hover:bg-red-700 transition"
                                        >
                                            <i
                                                class="fa-solid fa-trash-can"
                                            ></i>
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-row gap-1 justify-end">
                        <Link
                            v-for="(link, index) in filteredLinks"
                            :key="index"
                            :href="link.url || ''"
                            class="px-3 py-1 rounded text-sm font-medium"
                            :class="{
                                'bg-blue-800 text-blue-100': link.active,
                                'bg-blue-50 hover:bg-blue-100': !link.active,
                                'pointer-events-none text-gray-400': !link.url,
                            }"
                        >
                            <template v-if="link.label.includes('laquo')">
                                <i class="fa-solid fa-chevron-left"></i>
                            </template>
                            <template v-else-if="link.label.includes('raquo')">
                                <i class="fa-solid fa-chevron-right"></i>
                            </template>
                            <template v-else>
                                {{ link.label }}
                            </template>
                        </Link>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
