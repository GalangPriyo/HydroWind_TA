<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";

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
            // { name: "Riwayat", link: "/admin/history", icon: "fa-database" },
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
            // { name: "Riwayat", link: "/user/history", icon: "fa-database" },
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
</script>

<template>
    <div class="flex h-screen bg-base-200">
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
                    <i :class="`${item.icon} w-6 text-xl text-center`"></i>
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
                    <span>
                        <i class="fa-regular fa-calendar mr-1"></i> {{ day }}
                        <i class="fa-regular fa-clock ml-2"></i> {{ time }}
                    </span>
                </div>

                <!-- Kanan: Tema, Nama Akun, & Profil -->
                <div class="flex items-center gap-2">
                    <!-- Toggle Tema -->
                    <!-- <button
                        type="button"
                        class="btn-sm btn-ghost hover:bg-transparent"
                    >
                        <label class="inline-flex items-center relative">
                            <input
                                class="peer hidden"
                                id="toggle"
                                type="checkbox"
                                @click="toggleTheme"
                            />
                            <div
                                class="relative w-[60px] h-[28px] bg-white peer-checked:bg-zinc-500 rounded-full after:absolute after:content-[''] after:w-[20px] after:h-[20px] after:bg-gradient-to-r from-orange-500 to-yellow-400 peer-checked:after:from-zinc-900 peer-checked:after:to-zinc-900 after:rounded-full after:top-[4px] after:left-[4px] active:after:w-[22px] peer-checked:after:left-[36px] peer-checked:after:translate-x-0 shadow-sm duration-300 after:duration-300 after:shadow-md"
                            ></div>
                            <svg
                                height="0"
                                width="80"
                                viewBox="0 0 24 24"
                                data-name="Layer 1"
                                id="Layer_1"
                                xmlns="http://www.w3.org/2000/svg"
                                class="fill-white peer-checked:opacity-60 absolute w-4 h-4 left-[6px]"
                            >
                                <path
                                    d="M12,17c-2.76,0-5-2.24-5-5s2.24-5,5-5,5,2.24,5,5-2.24,5-5,5ZM13,0h-2V5h2V0Zm0,19h-2v5h2v-5ZM5,11H0v2H5v-2Zm19,0h-5v2h5v-2Zm-2.81-6.78l-1.41-1.41-3.54,3.54,1.41,1.41,3.54-3.54ZM7.76,17.66l-1.41-1.41-3.54,3.54,1.41,1.41,3.54-3.54Zm0-11.31l-3.54-3.54-1.41,1.41,3.54,3.54,1.41-1.41Zm13.44,13.44l-3.54-3.54-1.41,1.41,3.54,3.54,1.41-1.41Z"
                                ></path>
                            </svg>
                            <svg
                                height="512"
                                width="512"
                                viewBox="0 0 24 24"
                                data-name="Layer 1"
                                id="Layer_1"
                                xmlns="http://www.w3.org/2000/svg"
                                class="fill-black opacity-60 peer-checked:opacity-70 peer-checked:fill-white absolute w-4 h-4 right-[6px]"
                            >
                                <path
                                    d="M12.009,24A12.067,12.067,0,0,1,.075,10.725,12.121,12.121,0,0,1,10.1.152a13,13,0,0,1,5.03.206,2.5,2.5,0,0,1,1.8,1.8,2.47,2.47,0,0,1-.7,2.425c-4.559,4.168-4.165,10.645.807,14.412h0a2.5,2.5,0,0,1-.7,4.319A13.875,13.875,0,0,1,12.009,24Zm.074-22a10.776,10.776,0,0,0-1.675.127,10.1,10.1,0,0,0-8.344,8.8A9.928,9.928,0,0,0,4.581,18.7a10.473,10.473,0,0,0,11.093,2.734.5.5,0,0,0,.138-.856h0C9.883,16.1,9.417,8.087,14.865,3.124a.459.459,0,0,0,.127-.465.491.491,0,0,0-.356-.362A10.68,10.68,0,0,0,12.083,2ZM20.5,12a1,1,0,0,1-.97-.757l-.358-1.43L17.74,9.428a1,1,0,0,1,.035-1.94l1.4-.325.351-1.406a1,1,0,0,1,1.94,0l.355,1.418,1.418.355a1,1,0,0,1,0,1.94l-1.418.355-.355,1.418A1,1,0,0,1,20.5,12ZM16,14a1,1,0,0,0,2,0A1,1,0,0,0,16,14Zm6,4a1,1,0,0,0,2,0A1,1,0,0,0,22,18Z"
                                ></path>
                            </svg>
                        </label>
                    </button> -->

                    <!-- Nama Akun -->
                    <span class="text-md">{{ props.user.name }}</span>

                    <!-- Dropdown Profil -->
                    <div class="dropdown dropdown-end">
                        <button
                            tabindex="0"
                            class="p-1 rounded hover:bg-base-300 transition-all flex items-center"
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
