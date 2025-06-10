import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#04133b",
                secondary: "#1B2E59",
                accent: "#90E0EF",
                base100: "#FFFFFF",
                base200: "#EDF6F9",
                danger: "#EF4444",
                textMain: "#1E293B",
                textSecondary: "#ffdf20",
            },
        },
    },
    daisyui: {
        themes: ["light", "dark"],
    },

    plugins: [forms, require("daisyui")],
};
