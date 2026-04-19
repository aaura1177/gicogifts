import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', 'Georgia', 'serif'],
            },
            colors: {
                ivory: {
                    50: '#FDF8F3',
                    100: '#F7EEE3',
                    200: '#ECDBC4',
                },
                sienna: {
                    400: '#C27454',
                    500: '#A0522D',
                    600: '#8A4526',
                    700: '#6D3620',
                },
                gold: {
                    400: '#D9BC82',
                    500: '#C9A96E',
                    600: '#A88A52',
                },
                chocolate: {
                    700: '#4A2A1C',
                    800: '#3A1F14',
                    900: '#2C1810',
                },
            },
            boxShadow: {
                warm: '0 10px 30px -10px rgba(160, 82, 45, 0.15)',
            },
        },
    },

    plugins: [forms],
};
