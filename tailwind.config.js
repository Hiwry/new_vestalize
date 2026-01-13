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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
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
