/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'honda-red': '#E52020',
        'honda-dark': '#2D2D2D',
        'honda-light': '#F5F5F5',
      }
    },
  },
  plugins: [],
}