import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
                'ibm-sans': ['"IBM Plex Sans"', 'system-ui', 'sans-serif'],
                'ibm-mono': ['"IBM Plex Mono"', 'ui-monospace', 'monospace'],
                outfit: ['Outfit', 'sans-serif'],
            },
            colors: {
                brand: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    DEFAULT: '#166534',
                    dark: '#124D2A',
                    soft: '#E8F5EE'
                },
                ink: '#0F1A15',
                muted: '#5A6B62',
                line: '#E2E8E4',
                surface: '#F7F8F6',
                navy: {
                    800: '#0f172a',
                    900: '#091e3a',
                    950: '#071529',
                }
            },
            boxShadow: {
                'soft': '0 4px 24px -4px rgba(15, 26, 21, 0.07)',
                'card': '0 14px 40px -16px rgba(15, 26, 21, 0.12)',
                'receipt': '0 22px 50px -18px rgba(15, 26, 21, 0.16)'
            }
        },
    },

    plugins: [forms],
};
