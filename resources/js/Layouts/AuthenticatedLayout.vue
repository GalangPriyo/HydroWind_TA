<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed, onBeforeUnmount, watch } from "vue";
import Swal from "sweetalert2";

// State
const darkMode = ref(localStorage.getItem("theme") === "dark");
const isMinimized = ref(
    localStorage.getItem("sidebarMinimized") === "true" || false
);
const time = ref("");
const day = ref("");
const isMobile = ref(window.innerWidth <= 768);
const page = usePage();

// Responsive handling
const handleScreenChange = (e) => {
    isMobile.value = e.matches;
    if (isMobile.value) {
        isMinimized.value = true;
    }
};

// Active link detection
const isActive = (href) => {
    if (href === "/") {
        return page.url === "/";
    }
    return page.url.startsWith(href);
};

// Toggle sidebar and persist state
const toggleSidebar = () => {
    isMinimized.value = !isMinimized.value;
    localStorage.setItem("sidebarMinimized", isMinimized.value);
};

// Time and date formatting
const updateTime = () => {
    const now = new Date();
    const options = {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    };
    day.value = now.toLocaleDateString("id-ID", options);
    time.value = now.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
    });
};

// Menu items based on role
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
        ];
    }
});

// Logout confirmation
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
        customClass: {
            popup: "rounded-xl",
            confirmButton: "rounded-lg",
            cancelButton: "rounded-lg",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.post("/logout");
        }
    });
};

// Lifecycle hooks
onMounted(() => {
    updateTime();
    const timeInterval = setInterval(updateTime, 1000);

    const mediaQuery = window.matchMedia("(max-width: 768px)");
    mediaQuery.addEventListener("change", handleScreenChange);
    handleScreenChange(mediaQuery);

    // Set initial theme
    document.documentElement.setAttribute(
        "data-theme",
        darkMode.value ? "dark" : "light"
    );

    onBeforeUnmount(() => {
        clearInterval(timeInterval);
        mediaQuery.removeEventListener("change", handleScreenChange);
    });
});

// Props
const props = defineProps({
    user: Object,
});
</script>

<template>
    <div class="flex h-screen bg-base-100">
        <!-- Sidebar -->
        <aside
            :class="[
                'bg-primary text-primary-content min-h-screen transition-all duration-300 p-4 flex flex-col z-10',
                isMinimized || isMobile ? 'w-16' : 'w-64',
            ]"
        >
            <!-- Logo & Toggle -->
            <div class="flex items-center justify-between mb-6">
                <div
                    @click="toggleSidebar"
                    class="flex items-center cursor-pointer hover:opacity-80 transition-opacity"
                >
                    <img
                        src="/assets/media/HydroWind.jpeg"
                        alt="Logo"
                        class="rounded-full object-cover ml-1"
                        :class="{
                            'w-6 h-6': isMinimized || isMobile,
                            'w-10 h-10 ': !isMinimized && !isMobile,
                        }"
                    />
                    <h2
                        class="text-xl font-bold ml-2 transition-all whitespace-nowrap"
                        :class="{
                            'opacity-0 w-0': isMinimized || isMobile,
                            'opacity-100': !isMinimized && !isMobile,
                        }"
                    >
                        HydroWind
                    </h2>
                </div>
                <button
                    v-if="!isMobile"
                    @click="toggleSidebar"
                    class="p-1 rounded-full hover:bg-primary-focus transition-all"
                    :class="{ 'ml-auto': isMinimized }"
                ></button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 flex flex-col space-y-1 w-full overflow-y-auto">
                <template v-for="item in menuItems" :key="item.name">
                    <Link
                        :href="item.link"
                        :method="item.method || 'get'"
                        :as="item.method === 'post' ? 'button' : 'a'"
                        class="flex items-center p-2 rounded-lg transition-all"
                        :class="[
                            isActive(item.link)
                                ? 'bg-white/10 text-white font-bold'
                                : 'hover:hover:bg-white/10 hover:text-primary-content',
                            item.class,
                        ]"
                    >
                        <i :class="`${item.icon} text-lg w-6 text-center`"></i>
                        <span
                            class="ml-3 transition-all whitespace-nowrap"
                            :class="{
                                'opacity-0 w-0': isMinimized || isMobile,
                                'opacity-100': !isMinimized && !isMobile,
                            }"
                        >
                            {{ item.name }}
                        </span>
                    </Link>
                </template>
            </nav>

            <!-- Sidebar Footer -->
            <div class="pt-2 border-t border-primary-focus mt-auto">
                <button
                    @click="confirmLogout"
                    class="flex items-center gap-2 px-2 py-2 text-error hover:bg-error/10 w-full rounded-lg justify-left transition-all"
                >
                    <i
                        class="fa-solid fa-right-from-bracket w-4 text-center text-error text-lg"
                    ></i>
                    <span
                        v-show="!isMinimized && !isMobile"
                        class="ml-2 text-base font-medium"
                    >
                        Logout
                    </span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <nav class="bg-base-100 shadow-lg py-2 px-4 flex items-center">
                <!-- Mobile Toggle -->
                <button
                    @click="toggleSidebar"
                    class="md:hidden p-2 mr-2 rounded-full hover:bg-base-200"
                >
                    <i class="fa-solid fa-bars"></i>
                </button>

                <!-- Date & Time -->
                <div
                    class="hidden md:flex items-center gap-2 text-sm text-base-content"
                >
                    <div class="flex items-center">
                        <i class="fa-regular fa-calendar mr-1"></i>
                        <span>{{ day }}</span>
                    </div>
                    <div class="flex items-center ml-2">
                        <i class="fa-regular fa-clock mr-1"></i>
                        <span>{{ time }}</span>
                    </div>
                </div>

                <!-- Mobile Title -->
                <div
                    class="md:hidden mx-auto text-lg font-semibold text-base-content"
                >
                    HydroWind
                </div>

                <!-- User Profile -->
                <div class="ml-auto flex items-center gap-3">
                    <span
                        class="hidden md:block text-sm font-medium text-base-content"
                    >
                        {{ user.name }}
                    </span>

                    <div class="dropdown dropdown-end">
                        <button
                            tabindex="0"
                            class="flex items-center gap-2 p-1 rounded-full hover:bg-base-200 transition-all"
                        >
                            <div
                                class="w-8 h-8 rounded-full overflow-hidden border-2 border-primary"
                            >
                                <img
                                    src="/assets/media/profil.jpg"
                                    alt="Profil"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <i
                                class="fa-solid fa-chevron-down text-xs hidden md:block"
                            ></i>
                        </button>

                        <ul
                            tabindex="0"
                            class="mt-2 z-20 py-2 shadow-lg menu menu-sm dropdown-content bg-base-100 rounded-box w-56 border border-base-200"
                        >
                            <li
                                class="md:hidden px-4 py-2 border-b border-base-200"
                            >
                                <div class="font-medium">{{ user.name }}</div>
                                <div class="text-xs opacity-70">
                                    <i class="fa-regular fa-calendar mr-1"></i>
                                    {{ day }}
                                </div>
                            </li>
                            <li>
                                <Link
                                    href="/profile"
                                    class="flex items-center gap-2 px-4 py-2 hover:bg-base-200"
                                >
                                    <i
                                        class="fa-solid fa-user w-4 text-center"
                                    ></i>
                                    <span>Profil</span>
                                </Link>
                            </li>
                            <li>
                                <button
                                    @click="confirmLogout"
                                    class="flex items-center gap-2 px-4 py-2 text-error hover:bg-error/10"
                                >
                                    <i
                                        class="fa-solid fa-right-from-bracket w-4 text-center"
                                    ></i>
                                    <span>Logout</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-4 md:p-6 bg-base-100/50">
                <div class="max-w-full mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style>
/* Smooth transitions */
.sidebar-transition {
    transition: all 0.3s ease;
}

/* Custom scrollbar for sidebar */
aside nav::-webkit-scrollbar {
    width: 4px;
}

aside nav::-webkit-scrollbar-track {
    background: transparent;
}

aside nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

aside nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Animation for sidebar toggle */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.sidebar-text {
    animation: fadeIn 0.2s ease-out;
}
</style>
