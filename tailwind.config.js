import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
                jakarta: ['"Plus Jakarta Sans"', "sans-serif"],
            },
            colors: {
                forest: {
                    DEFAULT: "#064E3B",
                    800: "#065F46",
                    900: "#064E3B",
                },
            },
        },
    },

    plugins: [forms],
};
