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
</script>

<template>
    <div>
        <!-- Navbar -->
        <nav
            :class="[
                'fixed w-full top-0 left-0 z-50 transition-all duration-300',
                isScrolled
                    ? 'bg-base-100 shadow-md'
                    : 'bg-opacity-50 bg-base-100 shadow-md',
            ]"
        >
            <div
                class="container mx-auto px-6 py-3 flex justify-between items-center"
            >
                <!-- Logo -->
                <div
                    class="flex-1 flex justify-center lg:justify-start items-center"
                >
                    <img
                        src="/assets/media/HydroWind.jpeg"
                        alt="HydroWind Logo"
                        class="w-10 h-10 rounded-full"
                    />
                    <span class="text-lg font-bold ml-2">HydroWind</span>
                </div>

                <!-- Hamburger Menu (Mobile) -->
                <div class="lg:hidden dropdown dropdown-end">
                    <button @click="isMenuOpen = !isMenuOpen">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <ul
                        v-if="isMenuOpen"
                        tabindex="0"
                        class="mt-3 z-100 p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52"
                    >
                        <li>
                            <Link
                                href="/"
                                class="block px-4 py-2 hover:text-blue-600"
                                >Home</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/monitoring"
                                class="block px-4 py-2 hover:text-blue-600"
                                >Monitoring</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/peta"
                                class="block px-4 py-2 hover:text-blue-600"
                                >Peta</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/panduan"
                                class="block px-4 py-2 hover:text-blue-600"
                                >Panduan</Link
                            >
                        </li>
                        <template v-if="user">
                            <li>
                                <Link
                                    :href="
                                        user.role === 'admin'
                                            ? '/admin/dashboard'
                                            : '/user/dashboard'
                                    "
                                    class="block px-4 py-2 hover:text-blue-600"
                                    >Dashboard</Link
                                >
                            </li>
                            <li>
                                <Link
                                    href="/profile"
                                    class="block px-4 py-2 hover:text-blue-600"
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
                        </template>
                        <template v-else>
                            <li>
                                <Link
                                    href="/login"
                                    class="block px-4 my-2 py-2 border-2 border-blue-500 text-xs text-blue-500 rounded-full hover:bg-blue-500 hover:text-white"
                                    >Login</Link
                                >
                            </li>
                            <li>
                                <Link
                                    href="/register"
                                    class="block px-4 py-2 border-2 border-blue-500 text-xs text-blue-500 rounded-full hover:bg-blue-500 hover:text-white"
                                    >Register</Link
                                >
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- Navigation Links -->
                <div class="hidden lg:flex items-center space-x-7">
                    <Link href="/" class="block px-4 py-2 hover:text-blue-600"
                        >Home</Link
                    >
                    <Link
                        href="/monitoring"
                        class="block px-4 py-2 hover:text-blue-600"
                        >Monitoring</Link
                    >
                    <Link
                        href="/peta"
                        class="block px-4 py-2 hover:text-blue-600"
                        >Peta</Link
                    >
                    <Link
                        href="/panduan"
                        class="block px-4 py-2 hover:text-blue-600"
                        >Panduan</Link
                    >

                    <!-- User Authentication -->
                    <template v-if="user">
                        <div class="dropdown dropdown-end">
                            <button
                                tabindex="0"
                                class="p-1 rounded hover:bg-base-300 transition-all flex items-center"
                            >
                                <div
                                    class="w-10 h-10 rounded-full overflow-hidden"
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
                                class="mt-3 z-10 p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-40"
                            >
                                <li>
                                    <Link
                                        :href="
                                            user.role === 'admin'
                                                ? '/admin/dashboard'
                                                : '/user/dashboard'
                                        "
                                        class="block px-4 py-2 hover:bg-base-200"
                                        >Dashboard</Link
                                    >
                                </li>
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
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="block px-4 py-2 border-2 border-blue-500 text-xs text-blue-500 rounded-full hover:bg-blue-500 hover:text-white"
                            >Login</Link
                        >
                        <Link
                            href="/register"
                            class="block px-4 py-2 border-2 border-blue-500 text-xs text-blue-500 rounded-full hover:bg-blue-500 hover:text-white"
                            >Register</Link
                        >
                    </template>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
