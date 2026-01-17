import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#006644',
                'primary-foreground': '#ffffff',
                secondary: '#f0f9f5',
                'secondary-foreground': '#006644',
                accent: '#4ade80',
                muted: '#f1f5f9',
                'muted-foreground': '#64748b',
                border: '#e2e8f0',
                input: '#e2e8f0',
                ring: '#006644',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
