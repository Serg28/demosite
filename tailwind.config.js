/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/views/**/*.{blade.php,html,js}",
    "./resources/js/**/*.js",
    "./vendor/livewire/livewire/src/features/supportView/view.php",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // UI Kit brand palette (UI Kit.html)
        brand: {
          DEFAULT: '#2563EB',
          dark: '#1D4ED8',
          light: '#EFF6FF',
          muted: '#DBEAFE',
        },
        ink: {
          DEFAULT: '#111827',
          muted: '#6B7280',
          light: '#9CA3AF',
        },
        surface: '#F5F6F8',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
