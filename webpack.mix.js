const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const autoprefixer = require('autoprefixer');

mix.js('resources/js/app.js', 'public/js')
    .vue({ version: 3 });

mix.postCss('resources/css/app.css', 'public/css', [
    tailwindcss('./tailwind.config.js'),
    autoprefixer,
]);

mix.copy(
    'resources/css/fifa-design-system.css',
    'public/css/fifa-design-system.css'
);
