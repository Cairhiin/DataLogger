import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],
    theme: {
        extend: {},

        fontFamily: {
            sans: ["Inter", "ui-sans-serif"],
            serif: ["ui-serif", "Georgia"],
            mono: ["ui-monospace", "SFMono-Regular"],
            heading: ['"Roboto Condensed"', "ui-serif", "ui-sans-serif"],
        },
    },

    plugins: [forms, typography],
};
