/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#2575fc',
        secondary: '#6a11cb',
      },
      backgroundImage: {
        gradient: 'linear-gradient(45deg, #6a11cb, #2575fc)',
      }
    },
  },
  plugins: [],
}
