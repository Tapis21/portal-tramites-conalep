import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    // ✅ Ya lo tienes
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Http/Livewire/**/*.php",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",

    // 👇 AGREGAR ESTO (es lo que te falta)
    "./resources/views/filament/**/*.blade.php",
    "./app/Filament/**/*.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    forms,
  ],
};