import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            colors: {
                navy: {
                    50: '#f0f4fa',
                    100: '#e1eaf7',
                    900: '#0c2340',
                    950: '#06152a',
                },
                brand: {
                    red: '#b31930',
                    yellow: '#e4bc0f',
                },
                plomo: {
                    DEFAULT: '#6d7285',
                    light: '#9aa0b4',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
