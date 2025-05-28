<script setup>
import { computed, ref } from "vue";
import { Link, router, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

defineOptions({ layout: AuthenticatedLayout });
const { user, users, search, stats } = defineProps({
    user: Object,
    users: Object,
    search: String,
    stats: Object,
});
const searchQuery = ref(search || "");
const viewMode = ref("table");

const filteredLinks = computed(() => {
    return users.links.filter((link) => link.label !== "...");
});

const confirmDelete = (id) => {
    Swal.fire({
        title: "Hapus Akun Pengguna?",
        text: "Tindakan ini akan menghapus Akun Pengguna yang Anda pilih.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
        customClass: {
            popup: "rounded-2xl",
            confirmButton: "rounded-xl",
            cancelButton: "rounded-xl",
        },
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

const userStats = computed(() => {
    // Ambil nilai dari prop 'stats' jika ada, jika tidak fallback ke 0
    const total = stats?.totalUsers || 0;
    const withWhatsapp = stats?.usersWithWhatsapp || 0;
    const withoutWhatsapp = total - withWhatsapp;
    return { total, withWhatsapp, withoutWhatsapp };
});

const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString("id-ID", options);
};
</script>

<template>
    <Head title="Daftar Pengguna" />

    <div class="py-2">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <!-- Header Section -->
            <div class="mb-8">
                <div
                    class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 mb-2 flex items-center"
                        >
                            Daftar Pengguna
                        </h1>
                        <p class="text-gray-600">
                            Kelola semua akun pengguna sistem
                        </p>
                    </div>

                    <!-- Search and Actions -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 lg:items-center"
                    >
                        <div class="relative">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-600"
                            ></i>
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Cari pengguna"
                                class="w-full sm:w-72 pl-8 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 bg-white"
                            />
                        </div>

                        <div class="flex gap-3">
                            <!-- View Mode Toggle -->
                            <div
                                class="flex bg-white rounded-xl border-2 border-gray-200 p-1"
                            >
                                <button
                                    @click="viewMode = 'grid'"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                        viewMode === 'grid'
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'text-gray-600 hover:text-gray-800',
                                    ]"
                                >
                                    <span
                                        ><i
                                            class="fa-solid fa-table-cells-large"
                                        ></i
                                    ></span>
                                </button>
                                <button
                                    @click="viewMode = 'table'"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                        viewMode === 'table'
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'text-gray-600 hover:text-gray-800',
                                    ]"
                                >
                                    <span
                                        ><i class="fa-solid fa-bars"></i
                                    ></span>
                                </button>
                            </div>

                            <Link
                                :href="route('admin.pengguna.create')"
                                class="btn border-2 border-gray-200 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium"
                            >
                                <span
                                    ><i class="fa-solid fa-user-plus"></i
                                ></span>
                                Tambah Pengguna
                            </Link>
                            <a
                                :href="route('admin.pengguna.download')"
                                class="btn border-2 border-gray-200 bg-green-600 text-white text-sm rounded-xl hover:bg-green-700 transition-all duration-200 font-medium"
                            >
                                <i class="fa-solid fa-download"></i> Download
                                Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8"
            >
                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Total Pengguna
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ userStats.total }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl"
                                ><i class="fa-solid fa-users text-blue-600"></i
                            ></span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Terhubung WhatsApp
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ userStats.withWhatsapp }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl"
                                ><i
                                    class="fa-brands fa-whatsapp text-emerald-600"
                                ></i
                            ></span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Belum Terhubung WhatsApp
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ userStats.withoutWhatsapp }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl"
                                ><i
                                    class="fa-solid fa-phone-slash text-amber-600"
                                ></i
                            ></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div
                class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
            >
                <!-- Empty State -->
                <div
                    v-if="!users.data || users.data.length === 0"
                    class="text-center py-16"
                >
                    <div
                        class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <span class="text-4xl text-gray-400"
                            ><i class="fa-solid fa-users"></i
                        ></span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Belum ada pengguna
                    </h3>
                    <p class="text-gray-600">Mulai tambahkan pengguna</p>
                </div>

                <!-- Search Empty State -->
                <div
                    v-else-if="filteredUsers.length === 0"
                    class="text-center py-16"
                >
                    <div
                        class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <span class="text-4xl text-gray-400"
                            ><i class="fa-solid fa-triangle-exclamation"></i
                        ></span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Tidak ditemukan
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Tidak ada hasil untuk pencarian: "<strong>{{
                            searchQuery
                        }}</strong
                        >"
                    </p>
                    <button
                        @click="searchQuery = ''"
                        class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium"
                    >
                        <span><i class="fa-solid fa-rotate-left"></i></span>
                        Reset Pencarian
                    </button>
                </div>

                <!-- Grid View -->
                <div v-else-if="viewMode === 'grid'" class="p-6">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="user in filteredUsers"
                            :key="user.id"
                            class="bg-white border-2 border-gray-200 rounded-2xl p-6 hover:border-blue-300 hover:shadow-lg transition-all duration-200 group"
                        >
                            <!-- User Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3"
                                        >
                                            <span class="text-blue-600"
                                                ><i class="fa-solid fa-user"></i
                                            ></span>
                                        </div>
                                        <div>
                                            <h3
                                                class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors"
                                            >
                                                {{ user.name }}
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="mb-4">
                                <div class="flex text-sm text-gray-600 mb-1">
                                    <span class="mr-3 text-base"
                                        ><i class="fa-solid fa-phone"></i
                                    ></span>
                                    {{
                                        user.whatsapp?.phone_number ||
                                        "Belum ada nomor WA"
                                    }}
                                </div>
                            </div>

                            <!-- Registration Date -->
                            <div class="mb-4">
                                <span class="mr-3 text-gray-600"
                                    ><i class="fa-solid fa-calendar-days"></i
                                ></span>
                                <span class="text-sm text-gray-600">{{
                                    formatDate(user.created_at)
                                }}</span>
                            </div>

                            <!-- Actions -->
                            <div
                                class="flex gap-2 justify-center mt-4 pt-4 border-t border-gray-100"
                            >
                                <Link
                                    :href="
                                        route('admin.pengguna.edit', user.id)
                                    "
                                    class="text-center border border-amber-600 px-3 py-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors text-sm font-medium tooltip"
                                    data-tip="Edit"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </Link>
                                <button
                                    @click="confirmDelete(user.id)"
                                    class="text-center border border-red-600 px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium tooltip"
                                    data-tip="Hapus"
                                >
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table View -->
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    No
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Nama
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Nomor WA
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Tanggal Daftar
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="(user, index) in filteredUsers"
                                :key="user.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{
                                        (users.current_page - 1) *
                                            users.per_page +
                                        index +
                                        1
                                    }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        {{ user.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ user.email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ user.whatsapp?.phone_number || "-" }}
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                >
                                    <div class="flex justify-center gap-2">
                                        <Link
                                            :href="
                                                route(
                                                    'admin.pengguna.edit',
                                                    user.id
                                                )
                                            "
                                            class="text-center border border-amber-600 px-2 py-1 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors text-sm font-medium tooltip"
                                            data-tip="Edit"
                                        >
                                            <i
                                                class="fa-solid fa-pen-to-square"
                                            ></i>
                                        </Link>
                                        <button
                                            @click="confirmDelete(user.id)"
                                            class="text-center border border-red-600 px-2 py-1 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium tooltip"
                                            data-tip="Hapus"
                                        >
                                            <i
                                                class="fa-solid fa-trash-can"
                                            ></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div
                        class="flex flex-col sm:flex-row justify-between items-center gap-4"
                    >
                        <div class="text-sm text-gray-700">
                            <span v-if="users.from && users.to && users.total">
                                Menampilkan {{ users.from }} -
                                {{ users.to }} dari {{ users.total }} pengguna
                            </span>
                            <span v-else>Tidak ada data untuk ditampilkan</span>
                        </div>
                        <div
                            v-if="users.links && users.links.length > 3"
                            class="flex gap-1"
                        >
                            <Link
                                v-for="(link, i) in filteredLinks"
                                :key="`link-${i}`"
                                :href="link.url || ''"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                                    link.active
                                        ? 'bg-blue-500 text-white shadow-md'
                                        : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
                                ]"
                            >
                                <template v-if="link.label.includes('laquo')">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </template>
                                <template
                                    v-else-if="link.label.includes('raquo')"
                                >
                                    <i class="fa-solid fa-chevron-right"></i>
                                </template>
                                <template v-else>
                                    {{ link.label }}
                                </template>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

/* Smooth transitions */
* {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Hover effects */
.group:hover .group-hover\:text-blue-600 {
    color: #2563eb;
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
