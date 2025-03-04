<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const isScrolled = ref(false);
const darkMode = ref(localStorage.getItem("theme") === "dark");
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

// Fungsi Toggle Tema
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
                    ? 'bg-opacity-50 bg-base-100 shadow-md'
                    : 'bg-base-100',
            ]"
        >
            <div
                class="container mx-auto px-6 py-3 flex justify-between items-center"
            >
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <img
                        src="/assets/media/HydroWind.jpeg"
                        alt="HydroWind Logo"
                        class="w-10 h-10 rounded-full"
                    />
                    <span class="text-lg font-bold">HydroWind</span>
                </div>

                <!-- Navigation Links -->
                <div class="flex space-x-7 items-center">
                    <Link
                        href="/"
                        class="text-base hover:text-blue-600 dark:hover:text-blue-400"
                        >Home</Link
                    >
                    <Link
                        href="/peta"
                        class="text-base hover:text-blue-600 dark:hover:text-blue-400"
                        >Peta</Link
                    >
                    <Link
                        href="/panduan"
                        class="text-base hover:text-blue-600 dark:hover:text-blue-400"
                        >Panduan</Link
                    >
                    <Link
                        href="/prakiraan-cuaca"
                        class="text-base hover:text-blue-600 dark:hover:text-blue-400"
                        >Prakiraan Cuaca</Link
                    >
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
                            class="px-4 py-2 rounded-full border-2 border-blue-500 text-xs text-blue-500 hover:bg-blue-500 hover:text-white"
                            >Login</Link
                        >
                        <Link
                            href="/register"
                            class="px-4 py-2 rounded-full border-2 border-blue-500 text-xs text-blue-500 hover:bg-blue-500 hover:text-white"
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
