const mix = require('laravel-mix')

require('laravel-mix-tailwind');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

let srcRoot = `${__dirname}/src/resources/assets`

mix.js(`${srcRoot}/js/app.js`, 'src/public/js')
   .sass(`${srcRoot}/scss/app.scss`, 'src/public/css')
   .tailwind()

mix.options({
    processCssUrls: false
})

// Aliases
mix.webpackConfig(require('./webpack.config'))
