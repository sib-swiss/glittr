const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Models/Repository.php',
    ],

    theme: {
        container: {
            center: true,
            padding: '2rem',
        },
        extend: {
            colors: {
                'primary': {  DEFAULT: '#E30613',  '50': '#FDA4A9',  '100': '#FC9096',  '200': '#FB6871',  '300': '#FA414C',  '400': '#F91926',  '500': '#E30613',  '600': '#AC050E',  '700': '#76030A',  '800': '#3F0205',  '900': '#080001'},
                'category-color': 'rgb(var(--category-color) / <alpha-value>)'
            },
            fontFamily: {
                sans: ['"Source Sans 3"', 'sans-serif'],
            },
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
