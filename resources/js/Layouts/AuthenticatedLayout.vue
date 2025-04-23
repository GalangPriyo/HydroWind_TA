<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed, onBeforeUnmount } from "vue";
import Swal from "sweetalert2";

// State
const darkMode = ref(localStorage.getItem("theme") === "dark");
const isMinimized = ref(false);
const time = ref("");
const day = ref("");
const isMobile = ref(window.innerWidth <= 768);
const page = usePage();

const isActive = (href) => {
    if (href === "/") {
        return page.url === "/";
    }
    return page.url.startsWith(href);
};

// Fungsi Toggle Sidebar
const toggleSidebar = () => {
    isMinimized.value = !isMinimized.value;
};

// Fungsi untuk Memperbarui Waktu
const updateTime = () => {
    const now = new Date();
    const days = [
        "Minggu",
        "Senin",
        "Selasa",
        "Rabu",
        "Kamis",
        "Jumat",
        "Sabtu",
    ];
    const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];

    day.value = `${days[now.getDay()]}, ${now.getDate()} ${
        months[now.getMonth()]
    } ${now.getFullYear()}`;
    time.value = now
        .toLocaleTimeString("id-ID", { hour12: false })
        .replace(/\./g, ":");
};

// Ambil data pengguna dari Props
const props = defineProps({
    user: Object,
});

const handleScreenChange = (e) => {
    isMobile.value = e.matches;
};

// Lifecycle Hooks
onMounted(() => {
    updateTime();
    setInterval(updateTime, 1000);
    document.documentElement.setAttribute(
        "data-theme",
        darkMode.value ? "dark" : "light"
    );

    // Gunakan matchMedia untuk memantau ukuran layar
    const mediaQuery = window.matchMedia("(max-width: 768px)");
    mediaQuery.addEventListener("change", handleScreenChange);
    handleScreenChange(mediaQuery);

    onBeforeUnmount(() => {
        mediaQuery.removeEventListener("change", handleScreenChange);
    });
});

// Menu Sidebar berdasarkan Role
const menuItems = computed(() => {
    if (props.user?.role === "admin") {
        return [
            {
                name: "Dashboard",
                link: "/admin/dashboard",
                icon: "fa-solid fa-house",
            },
            {
                name: "Daftar Pengguna",
                link: "/admin/pengguna",
                icon: "fa-solid fa-brands fa-whatsapp",
            },
            {
                name: "Daftar Alat",
                link: "/admin/devices",
                icon: "fa-solid fa-toolbox",
            },
            {
                name: "Riwayat",
                link: "/admin/riwayat",
                icon: "fa-solid fa-database",
            },
            { name: "Homepage", link: "/", icon: "fa-regular fa-map" },
            {
                name: "Logout",
                link: "/logout",
                icon: "fa-solid fa-right-from-bracket text-red-500",
                method: "post",
            },
        ];
    } else {
        return [
            {
                name: "Dashboard",
                link: "/user/dashboard",
                icon: "fa-solid fa-house",
            },
            {
                name: "WhatsApp",
                link: "/user/whatsapp",
                icon: "fa-solid fa-brands fa-whatsapp",
            },
            {
                name: "Riwayat",
                link: "/user/riwayat",
                icon: "fa-solid fa-database",
            },
            { name: "Homepage", link: "/", icon: "fa-regular fa-map" },
            {
                name: "Logout",
                link: "/logout",
                icon: "fa-solid fa-right-from-bracket text-red-500",
                method: "post",
            },
        ];
    }
});

const confirmLogout = () => {
    Swal.fire({
        title: "Konfirmasi Logout",
        text: "Apakah Anda yakin ingin keluar dari akun Anda?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Keluar",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post("/logout");
        }
    });
};
</script>

<template>
    <div class="flex h-screen bg-base200">
        <!-- Sidebar -->
        <aside
            :class="[
                'bg-primary text-base100 min-h-screen transition-all duration-300 p-4 flex flex-col items-start z-10',
                isMinimized || isMobile ? 'w-[72px]' : 'w-64',
            ]"
        >
            <!-- Logo & Toggle Sidebar -->
            <div
                @click="toggleSidebar"
                class="flex items-center cursor-pointer"
            >
                <img
                    src="/assets/media/HydroWind.jpeg"
                    alt="Logo"
                    class="w-10 h-10 rounded-full transition-all"
                    :class="{
                        'mr-0': isMinimized || isMobile,
                        'mr-2': !isMinimized && !isMobile,
                    }"
                />
                <h2
                    class="text-xl font-bold transition-all"
                    :class="{
                        'opacity-0 w-0': isMinimized || isMobile,
                        'opacity-100 w-auto': !isMinimized && !isMobile,
                    }"
                >
                    HydroWind
                </h2>
            </div>

            <nav class="mt-4 space-y-2 w-full">
                <template v-for="item in menuItems" :key="item.name">
                    <button
                        v-if="item.name === 'Logout'"
                        @click="confirmLogout"
                        class="flex items-center p-2 rounded w-full text-left transition-all min-w-[200px] hover:bg-secondary hover:text-white"
                    >
                        <i :class="`${item.icon} w-6 text-xl text-center`"></i>
                        <span
                            class="ml-3 transition-all whitespace-nowrap text-red-500"
                            :class="{
                                'opacity-0 w-0 overflow-hidden':
                                    isMinimized || isMobile,
                                'opacity-100 w-auto': !isMinimized && !isMobile,
                            }"
                        >
                            {{ item.name }}
                        </span>
                    </button>

                    <Link
                        v-else
                        :href="item.link"
                        :method="item.method || 'get'"
                        :as="item.method === 'post' ? 'button' : 'a'"
                        class="flex items-center p-2 rounded min-w-[200px] transition-all"
                        :class="[
                            isActive(item.link) && !(isMinimized || isMobile)
                                ? 'bg-secondary text-blue-400'
                                : !(isMinimized || isMobile)
                                ? 'hover:bg-secondary hover:text-white'
                                : '',
                        ]"
                    >
                        <i
                            :class="[
                                `${item.icon} w-6 text-xl text-center`,
                                isActive(item.link) ? 'text-blue-400' : '',
                            ]"
                        ></i>
                        <span
                            class="ml-3 transition-all whitespace-nowrap"
                            :class="{
                                'opacity-0 w-0 overflow-hidden':
                                    isMinimized || isMobile,
                                'opacity-100 w-auto': !isMinimized && !isMobile,
                            }"
                        >
                            {{ item.name }}
                        </span>
                    </Link>
                </template>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <nav
                class="bg-base100 shadow-md py-2 px-4 flex justify-between items-center"
            >
                <div
                    class="hidden md:flex items-center gap-4 text-md text-textMain"
                >
                    <span>
                        <i class="fa-regular fa-calendar mr-1"></i> {{ day }}
                        <i class="fa-regular fa-clock ml-2"></i> {{ time }}
                    </span>
                </div>

                <div
                    class="md:hidden absolute left-1/2 transform -translate-x-1/2 text-xl font-bold text-textMain"
                >
                    HydroWind
                </div>

                <div class="flex items-center gap-2 ml-auto md:ml-0">
                    <span class="hidden md:block text-md text-textMain">
                        {{ props.user.name }}
                    </span>

                    <div class="dropdown dropdown-end">
                        <button
                            tabindex="0"
                            class="p-1 rounded hover:bg-base200 transition-all flex items-center"
                        >
                            <div class="w-10 h-10 rounded-full overflow-hidden">
                                <img
                                    src="/assets/media/profil.jpg"
                                    alt="Profil"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </button>
                        <ul
                            tabindex="0"
                            class="mt-3 z-10 p-2 shadow menu menu-sm dropdown-content bg-base100 rounded-box w-72"
                        >
                            <li
                                class="md:hidden pointer-events-none bg-base200 rounded-t-lg"
                            >
                                <span
                                    class="block px-4 pt-2 text-center font-bold"
                                >
                                    {{ props.user.name }}
                                </span>
                            </li>
                            <li
                                class="md:hidden pointer-events-none bg-base200 rounded-b-lg mb-2"
                            >
                                <span class="block px-4 py-2">
                                    <i class="fa-regular fa-calendar mr-1"></i>
                                    {{ day }}
                                    <i class="fa-regular fa-clock ml-2"></i>
                                    {{ time }}
                                </span>
                            </li>
                            <li>
                                <Link
                                    href="/profile"
                                    class="block px-4 py-2 hover:bg-base200"
                                >
                                    Profil
                                </Link>
                            </li>
                            <li>
                                <button
                                    @click="confirmLogout"
                                    class="block px-4 py-2 text-red-500 hover:bg-red-100"
                                >
                                    Logout
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Konten Halaman -->
            <main class="p-6 flex-1 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
