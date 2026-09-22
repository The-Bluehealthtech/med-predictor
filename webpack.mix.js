const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .vue({ version: 3 })
   .css('resources/css/app.css', 'public/css');

mix.copy('resources/css/fifa-design-system.css', 'public/css/fifa-design-system.css');
