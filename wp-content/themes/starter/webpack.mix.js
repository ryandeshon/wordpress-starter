const mix = require('laravel-mix');
const local = require('./assets/js/utils/local-config');
require('laravel-mix-versionhash');
require('laravel-mix-tailwind');

mix.setPublicPath('./dist');

mix.webpackConfig({
    externals: {
        "jquery": "jQuery",
    }
});

if (local.proxy) {
    mix.browserSync({
        proxy: local.proxy,
        injectChanges: true,
        open: true,
        files: [
            '**/*.php',
            'assets/**/*.js',
            'assets/**/*.scss',
        ],
    });
}

mix.tailwind();

mix.js('assets/js/app.js', 'js');
mix.sass('assets/scss/app.scss', 'css');

mix.copy('assets/fonts', 'dist/fonts');
mix.copy('assets/images', 'dist/images');

if (mix.inProduction()) {
    mix.versionHash();
    mix.sourceMaps();
}
