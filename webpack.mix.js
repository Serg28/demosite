const mix = require('laravel-mix');
const path = require('path');
const {default: ImageminPlugin} = require("imagemin-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const {glb} = require('laravel-mix-glob');
const glob = require('glob-all');
// const HTMLWebpackPlugin = require('html-webpack-plugin');

mix.setPublicPath('public'); // Устанавливаем выходную директорию

mix.webpackConfig({
    plugins: [
        new TerserPlugin({
            terserOptions: {
                compress: {
                    dead_code: false, // Удаление недостижимого кода
                    drop_debugger: true, // Удаление операторов debugger
                    drop_console: true, // Удаление вызовов console.* (console.log, console.error и т.д.)
                },
                // Другие опции Terser...
            },
            extractComments: false,
        }),
    ],
    stats: {
        children: true,
    },
});


// Копирование HTML файлов
//mix.copy('resources/dev/**/*.html', 'public/');

// Компиляция и сжатие JavaScript
//mix.js('resources/dev/assets/js/common.js', 'public/assets/js/common.js').minify("public/assets/js/common.js")
mix.copy('resources/dev/assets/js/zepto.min.js', 'public/assets/js/zepto.min.js');
mix.copy('resources/dev/assets/js/zepto-slide-transition.min.js', 'public/assets/js/zepto-slide-transition.min.js');
mix.copy('resources/dev/assets/js/fine-min.js', 'public/assets/js/fine-min.js');
mix.copy('resources/dev/assets/js/swiper-bundle.min.js', 'public/assets/js/swiper-bundle.min.js');
mix.copy('resources/dev/assets/js/validation.js', 'public/assets/js/validation.js');

mix.js(glb.array('resources/dev/assets/js/base/**/*.js'), 'public/assets/js/scripts.js').minify("public/assets/js/scripts.js").version();

// Компиляция и сжатие SCSS
mix.sass('resources/dev/assets/css/main.scss', 'public/assets/css')
   .options({
      processCssUrls: false, // Отключаем обработку URL в CSS
      postCss: [require('autoprefixer')]
   }).minify("public/assets/css/main.css");

// Компиляция scss-файлов из папки pages
const scssFiles = glob.sync('resources/dev/assets/css/pages/**/*.scss');

// Для каждого scss файла создадим отдельную сборку
scssFiles.forEach(file => {
    const outputPath = file.replace(/^resources\/dev\/assets\/css\/pages\//, 'public/assets/css/pages/').replace(/\.scss$/, '.css');
    mix.sass(file, outputPath).minify(outputPath);
});
//--
/*
|--------------------------------------------------------------
|        Notification Template
|--------------------------------------------------------------
*/
mix.sass('resources/dev/assets/css/notification.scss', 'public/assets/css')

    .options({
        processCssUrls: false, // Отключаем обработку URL в CSS
        postCss: [require('autoprefixer')]
    }).minify("public/assets/css/notification.css");


/*
|--------------------------------------------------------------
|        Функционал чекаута
|--------------------------------------------------------------
*/
//mix.js('resources/dev/assets/js/checkout/*.js', 'public/assets/js/checkout.js').minify("public/assets/js/checkout.js");

mix.scripts(
    [
        "resources/dev/assets/js/base/analytic.js",
        "resources/dev/assets/js/choices.min.js",
        "resources/dev/assets/js/choices-searchable-ajax.js",
        'resources/dev/assets/js/checkout/*.js'
    ],
    "public/assets/js/checkout.js"
).minify("public/assets/js/checkout.js").version();

//Компиляция choices
//mix.styles(glb.array('resources/dev/assets/css/components/choices/*.css'), 'public/assets/css/choices.css').minify('public/assets/css/choices.css');
//mix.styles(['resources/dev/assets/css/components/choices/choices.min.css','resources/dev/assets/css/components/choices/сhoices-custom.css'], 'public/assets/css/choices.css').minify('public/assets/css/choices.css');
mix.styles(glb.array('resources/dev/assets/css/components/choices/*.css'), 'public/assets/css/choices.css').minify('public/assets/css/choices.css');

 // Компиляция и сжатие SCSS
/*mix.sass('resources/dev/css/components/_media.scss', 'public/css')
.options({
   processCssUrls: false, // Отключаем обработку URL в CSS
   postCss: [require('autoprefixer')]
});*/

mix.copyDirectory("resources/dev/assets/images", "public/assets/images");

/*
|--------------------------------------------------------------
|      Разные js-файлы
|--------------------------------------------------------------
*/
mix.scripts('resources/dev/assets/js/swiper.js', 'public/assets/js/swiper.js').minify("public/assets/js/swiper.js");

/*
|--------------------------------------------------------------
|      Функционал каталога и фильтра
|--------------------------------------------------------------
*/
mix.scripts(glb.array('resources/dev/assets/js/catalog/*.js'), 'public/assets/js/catalog.js').minify("public/assets/js/catalog.js");

/*
|--------------------------------------------------------------
|      Функционал карточки товара
|--------------------------------------------------------------
*/
mix.scripts(glb.array('resources/dev/assets/js/product/*.js'), 'public/assets/js/product.js').minify("public/assets/js/product.js");

/*
|--------------------------------------------------------------
|      Функционал для оплаты частями
|--------------------------------------------------------------
*/
//Калькулятор рассчета количества платежей по рассрочке
//ПриватБанк ОЧ
mix.scripts(glb.array('resources/dev/assets/js/payparts/*.js'), 'public/assets/js/payparts.js').minify("public/assets/js/payparts.js");

/*
|--------------------------------------------------------------
|        Кастомные CSS и JS для админки - НЕ УДАЛЯТЬ!!!
|--------------------------------------------------------------
*/
mix.js('resources/admin/js/app.js', 'public/packages/vis/builder/admin/js')
    .js('resources/admin/js/custom-admin-js.js', 'public/packages/vis/builder/admin/js');
mix.scripts(['resources/admin/js/imagegallery-popup-admin.js'], 'public/packages/vis/builder/admin/js/imagegallery-popup-admin.js');
mix.scripts(['resources/admin/js/orders-admin-js.js'], 'public/packages/vis/builder/admin/js/orders-admin-js.js');
mix.styles('resources/admin/css/custom-admin-styles.css', 'public/packages/vis/builder/admin/css/custom-admin-styles.css');

mix.webpackConfig({
    plugins: [
        new ImageminPlugin({
            test: /\.(jpe?g|png|gif|svg)$/i,
            pngquant: {
                quality: "65-80",
            },
        }),
    ],
});

// const templatesHTML = () => {
//    const pages = [];
//    const templates = fs
//        .readdirSync(path.resolve(__dirname, environment.paths.source, 'dev'))
//        .filter(file => /\.html$/.test(file));

//    pages.push(...templates);

//    /*
//        NOTE: How many pages you will get
//    */
//    log(chalk.black.bgWhite.bold(`### Get pages: ${chalk.red.bgWhite.bold(pages.join(', '))}`));

//    return pages.map(
//        page =>
//            new HTMLWebpackPlugin({
//                filename: page,
//                template: path.resolve(environment.paths.source, 'dev', page),
//                minify: {
//                    collapseWhitespace: isProd || isStats,
//                },
//            })
//    );
// };
 

// const plugins = () => {
//    const base = [
      
//        new CleanWebpackPlugin(),
    
//        ...templatesHTML(),
//    ];

//    if (isStats) base.push(new BundleAnalyzerPlugin());

//    return base;
// };
 
