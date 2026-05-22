import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                    800: '#1a3d5c',
                    900: '#0c2340',
                    950: '#06152a',
                },
                institutional: {
                    surface: '#f8fafc',
                    'surface-alt': '#f5f7fb',
                    'blue-800': '#1e3a5f',
                    'blue-900': '#0f2847',
                    'blue-950': '#0a1d33',
                    gold: '#c9a227',
                    'gold-light': '#e4c76a',
                    'gold-soft': '#f5ecd4',
                    accent: '#3b82f6',
                    'accent-soft': '#dbeafe',
                },
                brand: {
                    red: '#9e2a3a',
                    yellow: '#e4bc0f',
                },
                plomo: {
                    DEFAULT: '#64748b',
                    light: '#94a3b8',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                premium: '0 24px 80px -12px rgba(12, 35, 64, 0.18)',
                glow: '0 0 60px -10px rgba(228, 188, 15, 0.35)',
                institutional: '0 4px 24px -4px rgba(15, 40, 71, 0.08)',
                'institutional-lg': '0 20px 50px -12px rgba(15, 40, 71, 0.12)',
            },
            backgroundImage: {
                'mesh-light':
                    'radial-gradient(at 40% 20%, rgba(228, 188, 15, 0.12) 0px, transparent 50%), radial-gradient(at 80% 0%, rgba(179, 25, 48, 0.08) 0px, transparent 50%), radial-gradient(at 0% 50%, rgba(12, 35, 64, 0.06) 0px, transparent 50%)',
                'mesh-dark':
                    'radial-gradient(at 40% 20%, rgba(228, 188, 15, 0.15) 0px, transparent 50%), radial-gradient(at 80% 0%, rgba(179, 25, 48, 0.12) 0px, transparent 50%), radial-gradient(at 0% 50%, rgba(96, 165, 250, 0.08) 0px, transparent 50%)',
            },
        },
    },

    plugins: [forms],
};
