/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/landing-personalizados/**/*.blade.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#7c3aed',
        'primary-hover': '#6d28d9',
      },
    },
  },
  plugins: [],
}
