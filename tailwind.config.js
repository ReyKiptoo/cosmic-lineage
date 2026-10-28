import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Typography from design.md & design_theme.md: Plus Jakarta Sans / Google Sans Flex
            fontFamily: {
                sans: ['"Google Sans Flex"', '"Plus Jakarta Sans"', 'Arial', 'Helvetica', ...defaultTheme.fontFamily.sans],
            },
            // Exact color tokens from design.md
            colors: {
                mku: {
                    primary: '#121317',       // Primary dark UI
                    'on-primary': '#ffffff',  // Text on dark UI
                    background: '#ffffff',    // Page canvas
                    surface: '#f8f9fa',       // Subtle section backgrounds / card surfaces
                    text: '#202124',          // Primary body & heading text
                    'text-muted': '#5f6368',  // Secondary / supporting text
                    border: '#dadce0',        // Hairline boundary
                    accent: '#1a73e8',        // Links & active actions
                    'accent-hover': '#185abc',// Interactive hover
                },
            },
            // Spacing scale additions (36px, 72px, 96px, 128px)

            spacing: {
                '18': '4.5rem', // 72px (fills the gap between 16 (64px) and 20 (80px))
            },
            // Maximum container width matching design.md (1276px)
            maxWidth: {
                'mku-container': '1276px',
            },
            // Radii from design.md
            borderRadius: {
                'sm': '2px',
                'md': '4px',
                'lg': '16px',
                'xl': '24px',
                'pill': '9999px',
            },
            // Shadows from design.md
            boxShadow: {
                'mku-card': 'rgba(33, 34, 38, 0.12) 0px 0px 0px 1px',
            },
        },
    },

    plugins: [forms],
};