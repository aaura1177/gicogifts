import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

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
                    300: '#D08A6B',
                    400: '#C27454',
                    500: '#A0522D',
                    600: '#8A4526',
                    700: '#6D3620',
                },
                gold: {
                    300: '#E6CFA0',
                    400: '#D9BC82',
                    500: '#C9A96E',
                    600: '#A88A52',
                },
                chocolate: {
                    600: '#6B4430',
                    700: '#4A2A1C',
                    800: '#3A1F14',
                    900: '#2C1810',
                },
                warm: {
                    50: '#FAFAF8',
                    100: '#F2F0EC',
                    200: '#E3DFD7',
                    300: '#C8C2B5',
                    500: '#8A8273',
                    700: '#4F4A3F',
                },
            },
            fontSize: {
                'display-xxl': ['clamp(44px,6vw,72px)', { lineHeight: '1.05', letterSpacing: '-0.02em', fontWeight: '400' }],
                'display-xl': ['clamp(36px,4.5vw,56px)', { lineHeight: '1.1', letterSpacing: '-0.02em', fontWeight: '400' }],
                'display-lg': ['clamp(32px,3.5vw,44px)', { lineHeight: '1.15', letterSpacing: '-0.015em', fontWeight: '400' }],
                'display-md': ['clamp(28px,2.8vw,36px)', { lineHeight: '1.2', letterSpacing: '-0.01em', fontWeight: '400' }],
                'display-sm': ['clamp(22px,2vw,28px)', { lineHeight: '1.25', fontWeight: '500' }],
                'display-xs': ['22px', { lineHeight: '1.3', fontWeight: '500' }],
                overline: ['11px', { lineHeight: '1.4', letterSpacing: '0.14em', fontWeight: '600' }],
            },
            boxShadow: {
                warm: '0 10px 30px -10px rgba(160, 82, 45, 0.15)',
                lift: '0 20px 40px -12px rgba(160, 82, 45, 0.22)',
                'warm-sm': '0 4px 12px -4px rgba(160, 82, 45, 0.10)',
            },
            transitionTimingFunction: {
                'out-soft': 'cubic-bezier(0.22, 1, 0.36, 1)',
            },
            animation: {
                'fade-up': 'fadeUp 700ms cubic-bezier(0.22,1,0.36,1) both',
                'fade-up-fast': 'fadeUp 400ms cubic-bezier(0.22,1,0.36,1) both',
            },
            keyframes: {
                fadeUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms, typography],
};
