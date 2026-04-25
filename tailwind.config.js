/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/views/**/*.{blade.php,html,js}",
    "./resources/js/**/*.js",
    "./vendor/livewire/livewire/src/features/supportView/view.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
