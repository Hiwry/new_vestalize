/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ["class"],
  content: [
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        background: "#F6F7FB",
        section: "#F1F2F8",
        surface: "#FFFFFF",
        footer: "#EEF0F6",
        border: "#E6E8F2",
        text: {
          primary: "#0F1222",
          secondary: "#4A4F6A",
          muted: "#8B90A8",
        },
        brand: {
          DEFAULT: "#6D3DFF",
          hover: "#5A2EE6",
          soft: "#F7F4FF",
        },
      },
      boxShadow: {
        card: "0 12px 30px rgba(15, 18, 34, 0.08)",
        strong: "0 20px 50px rgba(109, 61, 255, 0.25)",
      },
      borderRadius: {
        xl: "16px",
      },
    },
  },
  plugins: [],
};
