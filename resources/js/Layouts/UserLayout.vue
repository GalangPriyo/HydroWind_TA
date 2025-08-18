<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

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

const toggleTheme = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem("theme", darkMode.value ? "dark" : "light");
    document.documentElement.setAttribute(
        "data-theme",
        darkMode.value ? "dark" : "light"
    );
};

// Close mobile menu when clicking outside
const closeMenuOnClickOutside = (event) => {
    if (isMenuOpen.value && !event.target.closest(".dropdown")) {
        isMenuOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", closeMenuOnClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", closeMenuOnClickOutside);
});
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav
            :class="[
                'fixed w-full top-0 left-0 z-50 transition-all duration-300',
                isScrolled
                    ? 'bg-base-100 shadow-md'
                    : 'bg-opacity-90 bg-base-100 shadow-sm backdrop-blur-sm',
            ]"
        >
            <div
                class="container mx-auto px-4 sm:px-6 py-3 flex justify-between items-center"
            >
                <!-- Logo -->
                <Link href="/" class="flex items-center">
                    <img
                        src="/assets/media/HydroWind.jpeg"
                        alt="HydroWind Logo"
                        class="w-10 h-10 rounded-full"
                    />
                    <span class="text-lg font-bold ml-2 hidden sm:inline"
                        >HydroWind</span
                    >
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
                                class="p-1 rounded-full hover:bg-base-300 transition-all flex items-center"
                            >
                                <div
                                    class="w-9 h-9 rounded-full overflow-hidden border-2 border-transparent hover:border-primary"
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
                                        class="block px-4 py-2 hover:bg-base-200 rounded"
                                        >Dashboard</Link
                                    >
                                </li>
                                <li>
                                    <Link
                                        href="/profile"
                                        class="block px-4 py-2 hover:bg-base-200 rounded"
                                        >Profil</Link
                                    >
                                </li>
                                <li>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        class="block px-4 py-2 text-error hover:bg-error hover:text-error-content rounded"
                                        >Logout</Link
                                    >
                                </li>
                            </ul>
                        </div>
                    </template>
                    <template v-else>
                        <div class="flex items-center space-x-3">
                            <Link
                                href="/login"
                                class="px-4 py-2 border border-primary text-sm text-primary rounded-full hover:bg-primary hover:text-primary-content transition-colors"
                                >Login</Link
                            >
                            <!-- <Link
                                href="/register"
                                class="px-4 py-2 bg-primary text-sm text-primary-content rounded-full hover:bg-primary-focus transition-colors"
                                >Register</Link
                            > -->
                        </div>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center space-x-4">
                    <button
                        @click.stop="isMenuOpen = !isMenuOpen"
                        class="p-2 rounded-full hover:bg-base-300 transition-colors"
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
            <div
                v-if="isMenuOpen"
                class="lg:hidden bg-base-100 shadow-lg"
                @click.stop
            >
                <div class="container mx-auto px-4 py-3">
                    <ul class="space-y-2">
                        <li>
                            <Link
                                href="/"
                                class="block px-4 py-3 hover:bg-base-200 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Home</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/monitoring"
                                class="block px-4 py-3 hover:bg-base-200 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Monitoring</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/peta"
                                class="block px-4 py-3 hover:bg-base-200 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Peta</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/panduan"
                                class="block px-4 py-3 hover:bg-base-200 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Panduan</Link
                            >
                        </li>
                    </ul>

                    <div class="mt-4 pt-4 border-t border-base-300">
                        <template v-if="user">
                            <Link
                                :href="
                                    user.role === 'admin'
                                        ? '/admin/dashboard'
                                        : '/user/dashboard'
                                "
                                class="block px-4 py-3 hover:bg-base-200 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Dashboard</Link
                            >
                            <Link
                                href="/profile"
                                class="block px-4 py-3 hover:bg-base-200 rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Profil</Link
                            >
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="block px-4 py-3 text-error hover:bg-error hover:text-error-content rounded transition-colors"
                                @click="isMenuOpen = false"
                                >Logout</Link
                            >
                        </template>
                        <template v-else>
                            <div class="flex flex-col space-y-3 mt-2">
                                <Link
                                    href="/login"
                                    class="w-full text-center px-4 py-3 border border-primary text-primary rounded-full hover:bg-primary hover:text-primary-content transition-colors"
                                    @click="isMenuOpen = false"
                                    >Login</Link
                                >
                                <!-- <Link
                                    href="/register"
                                    class="w-full text-center px-4 py-3 bg-primary text-primary-content rounded-full hover:bg-primary-focus transition-colors"
                                    @click="isMenuOpen = false"
                                    >Register</Link
                                > -->
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="flex-grow pt-16">
            <slot />
        </main>
    </div>
</template>
