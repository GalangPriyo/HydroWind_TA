<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const isScrolled = ref(false);
const darkMode = ref(localStorage.getItem("theme") === "dark");
const isMenuOpen = ref(false);
const user = computed(() => usePage().props.auth.user);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
    // Set initial theme
    document.documentElement.setAttribute(
        "data-theme",
        darkMode.value ? "dark" : "light"
    );
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});

// Close mobile menu when clicking outside
const closeMenuOnClickOutside = (event) => {
    if (isMenuOpen.value && !event.target.closest(".dropdown")) {
        isMenuOpen.value = false;
    }
};

const confirmLogout = () => {
    Swal.fire({
        title: "Konfirmasi Logout",
        text: "Apakah Anda yakin ingin keluar dari akun Anda?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
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

onMounted(() => {
    document.addEventListener("click", closeMenuOnClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", closeMenuOnClickOutside);
});
</script>

<template>
    <div>
        <!-- Navbar -->
        <nav
            :class="[
                'fixed w-full top-0 left-0 z-50 transition-all duration-300',
                isScrolled
                    ? 'bg-base-100 shadow-md'
                    : 'bg-opacity-50 bg-base-100 shadow-md backdrop-blur-sm',
            ]"
        >
            <div
                class="container mx-auto px-4 sm:px-6 py-3 flex justify-between items-center"
            >
                <!-- Logo -->
                <Link
                    href="/"
                    class="flex-1 flex justify-center lg:justify-start items-center"
                >
                    <img
                        src="/assets/media/HydroWind.jpeg"
                        alt="HydroWind Logo"
                        class="w-10 h-10 rounded-full"
                    />
                    <span class="text-lg font-bold ml-2">HydroWind</span>
                </Link>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-6">
                    <Link
                        href="/"
                        class="px-3 py-2 hover:text-primary transition-colors"
                        >Home</Link
                    >
                    <Link
                        href="/monitoring"
                        class="px-3 py-2 hover:text-primary transition-colors"
                        >Monitoring</Link
                    >
                    <Link
                        href="/peta"
                        class="px-3 py-2 hover:text-primary transition-colors"
                        >Peta</Link
                    >
                    <Link
                        href="/panduan"
                        class="px-3 py-2 hover:text-primary transition-colors"
                        >Panduan</Link
                    >

                    <!-- User Authentication Desktop -->
                    <template v-if="user">
                        <div class="dropdown dropdown-end">
                            <button
                                tabindex="0"
                                class="rounded-full transition-all flex items-center"
                            >
                                <div
                                    class="w-10 h-10 rounded-full overflow-hidden hover:border-2 hover:border-white transition-all"
                                >
                                    <img
                                        src="/assets/media/profil.jpg"
                                        alt="Profil"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                            </button>
                            <ul
                                tabindex="0"
                                class="mt-3 z-10 p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52"
                            >
                                <li>
                                    <Link
                                        :href="
                                            user.role === 'admin'
                                                ? '/admin/dashboard'
                                                : '/user/dashboard'
                                        "
                                        class="flex items-center gap-2 px-4 py-2 hover:bg-base-200"
                                        ><i
                                            class="fa-solid fa-house w-4 text-center"
                                        ></i>
                                        <span>Dashboard</span></Link
                                    >
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
                    </template>
                    <template v-else>
                        <div class="flex items-center space-x-3">
                            <Link
                                href="/login"
                                class="px-4 py-2 border border-blue-600 text-sm text-blue-600 rounded-full font-medium hover:bg-blue-600 hover:text-primary-content transition-colors"
                                >Login</Link
                            >
                            <Link
                                href="/register"
                                class="px-4 py-2 bg-blue-600 text-sm text-primary-content font-medium rounded-full hover:bg-blue-700 transition-colors"
                                >Register</Link
                            >
                        </div>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center space-x-4">
                    <button
                        @click.stop="isMenuOpen = !isMenuOpen"
                        class="p-2 transition-colors"
                        aria-label="Toggle menu"
                    >
                        <i
                            :class="[
                                'text-xl',
                                isMenuOpen
                                    ? 'fa-solid fa-xmark'
                                    : 'fa-solid fa-bars',
                            ]"
                        ></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="isMenuOpen" class="lg:hidden shadow-lg" @click.stop>
                <div class="container mx-auto px-4 py-2">
                    <ul class="space-y-2">
                        <li>
                            <Link
                                href="/"
                                class="block px-4 py-2 hover:bg-base-100 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Home</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/monitoring"
                                class="block px-4 py-2 hover:bg-base-100 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Monitoring</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/peta"
                                class="block px-4 py-2 hover:bg-base-100 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Peta</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/panduan"
                                class="block px-4 py-2 hover:bg-base-100 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Panduan</Link
                            >
                        </li>
                    </ul>

                    <div class="mt-4 py-4 border-t border-gray-500 font-medium">
                        <template v-if="user">
                            <Link
                                :href="
                                    user.role === 'admin'
                                        ? '/admin/dashboard'
                                        : '/user/dashboard'
                                "
                                class="block px-4 py-2 hover:bg-base-200 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Dashboard</Link
                            >
                            <Link
                                href="/profile"
                                class="block px-4 py-2 hover:bg-base-200 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Profil</Link
                            >
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="block px-4 py-2 text-error hover:bg-error hover:text-error-content rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Logout</Link
                            >
                        </template>
                        <template v-else>
                            <div class="flex flex-col space-y-3 mt-2">
                                <Link
                                    href="/login"
                                    class="w-full text-center px-4 py-2 border border-blue-600 text-sm text-blue-600 rounded-full font-medium hover:bg-blue-600 hover:text-primary-content transition-colors"
                                    @click="isMenuOpen = false"
                                    >Login</Link
                                >
                                <Link
                                    href="/register"
                                    class="w-full text-center px-4 py-2 bg-blue-600 text-sm text-primary-content font-medium rounded-full hover:bg-blue-700 transition-colors"
                                    @click="isMenuOpen = false"
                                    >Register</Link
                                >
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
