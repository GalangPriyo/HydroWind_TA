<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

// State
const darkMode = ref(localStorage.getItem("theme") === "dark");
const isMinimized = ref(false);
const time = ref("");
const day = ref("");

// Fungsi Toggle Tema
const toggleTheme = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem("theme", darkMode.value ? "dark" : "light");
    document.documentElement.setAttribute(
        "data-theme",
        darkMode.value ? "dark" : "light"
    );
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

// Lifecycle Hooks
onMounted(() => {
    updateTime();
    setInterval(updateTime, 1000);
    document.documentElement.setAttribute(
        "data-theme",
        darkMode.value ? "dark" : "light"
    );
});

// Menu Sidebar
const menuItems = ref([
    { name: "Dashboard", link: "/user/dashboard", icon: "fa-house" },
    { name: "Akun", link: "/user/akun", icon: "fa-user" },
    { name: "Riwayat", link: "/user/history", icon: "fa-database" },
    {
        name: "Logout",
        link: "/logout",
        icon: "fa-right-from-bracket text-red-500",
        method: "post",
    },
]);
</script>

<template>
    <div class="flex h-screen bg-base-300">
        <!-- Sidebar -->
        <aside
            :class="[
                'bg-base-100 min-h-screen transition-all duration-300 p-4 flex flex-col items-start',
                isMinimized ? 'w-20' : 'w-64',
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
                    class="w-12 h-12 rounded-full transition-all"
                    :class="{ 'mr-0': isMinimized, 'mr-2': !isMinimized }"
                />
                <h2
                    class="text-xl font-bold transition-all"
                    :class="{
                        'opacity-0 w-0': isMinimized,
                        'opacity-100 w-auto': !isMinimized,
                    }"
                >
                    HydroWind
                </h2>
            </div>

            <!-- Menu Navigasi -->
            <nav class="mt-4 space-y-2 w-full">
                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.link"
                    :method="item.method || 'get'"
                    :as="item.method === 'post' ? 'button' : 'a'"
                    class="flex items-center p-2 rounded hover:bg-base-200 transition-all min-w-[150px]"
                >
                    <i
                        :class="`fa-solid ${item.icon} w-6 text-lg text-center`"
                    ></i>
                    <span
                        class="ml-3 transition-all whitespace-nowrap"
                        :class="{
                            'opacity-0 w-0 overflow-hidden': isMinimized,
                            'opacity-100 w-auto': !isMinimized,
                            'text-red-500': item.name === 'Logout',
                        }"
                    >
                        {{ item.name }}
                    </span>
                </Link>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <nav class="bg-base-100 p-4 flex justify-between items-center">
                <!-- Kiri: Tanggal & Waktu -->
                <div class="flex items-center gap-4 text-md">
                    <span
                        ><i class="fa-regular fa-calendar mr-1"></i> {{ day }}
                        <i class="fa-regular fa-clock ml-2"></i>
                        {{ time }}</span
                    >
                </div>

                <!-- Kanan: Tema, Nama Akun, & Profil -->
                <div class="flex items-center gap-2">
                    <!-- Toggle Tema -->
                    <button @click="toggleTheme" class="btn btn-sm btn-ghost">
                        <i
                            :class="
                                darkMode
                                    ? 'fa-regular fa-moon text-xl'
                                    : 'fa-regular fa-sun text-xl'
                            "
                        ></i>
                    </button>

                    <!-- Nama Akun -->
                    <span class="text-md">{{ props.user.name }}</span>

                    <!-- Dropdown Profil -->
                    <div class="dropdown dropdown-end">
                        <button
                            tabindex="0"
                            class="p-1 rounded hover:bg-base-300 transition-all flex items-center"
                        >
                            <div class="w-10 h-10 rounded overflow-hidden">
                                <img
                                    src="/assets/media/profil.jpg"
                                    alt="Profil"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </button>
                        <ul
                            tabindex="0"
                            class="mt-3 z-10 p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-40"
                        >
                            <li>
                                <Link
                                    href="/profile"
                                    class="block px-4 py-2 hover:bg-base-200"
                                    >Profil</Link
                                >
                            </li>
                            <li>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="block px-4 py-2 text-red-500 hover:bg-red-100"
                                    >Logout</Link
                                >
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
