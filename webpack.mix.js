const mix = require('laravel-mix')

require('laravel-mix-tailwind')

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

mix
    .setPublicPath('src/public/')
    .js(`${srcRoot}/js/app.js`, 'js')
    .sass(`${srcRoot}/scss/app.scss`, 'css')
    .tailwind()
    .version()

mix.options({
    processCssUrls: false
})

// Aliases
mix.webpackConfig(require('./webpack.config'))
