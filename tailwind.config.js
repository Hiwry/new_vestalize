import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                // Trocar Figtree por Inter - fonte mais leve
                sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            },
            fontWeight: {
                thin: '200',
                extralight: '300',
                light: '300',
                normal: '400',
                medium: '400', // Reduzido de 500 para 400
                semibold: '500', // Reduzido de 600 para 500
                bold: '500', // Reduzido de 600 para 500
                extrabold: '600', // Reduzido de 700 para 600
                black: '600', // Reduzido de 800 para 600
            },
            colors: {
                hubspot: {
                    purple: '#5b21b6', // Violet 800 (Deeper, Premium)
                    'purple-hover': '#4c1d95', // Violet 900
                    dark: '#191e48',
                    slate: '#33475b',
                    light: '#f5f8fa',
                }
            }
        },
    },

    plugins: [forms],
};

