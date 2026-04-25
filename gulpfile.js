var browserSync      = require('browser-sync').create();

var gulp = require('gulp');
const minify = require('gulp-minify');
const include = require('gulp-include')
const sass = require('gulp-sass')(require('sass'))
const uglify = require('gulp-uglify');
const cssnano = require('gulp-cssnano')

concat      = require('gulp-concat');


gulp.task('browser-sync', function(done) { 
  browserSync.init({
    server: {
      baseDir: 'front'
    },
    notify: false
  });
  
  browserSync.watch('front/').on('change', browserSync.reload);
  
  done()
}); 

gulp.task('scripts', function() {
  return gulp.src([ // Берем все необходимые библиотеки
    'dev/assets/js/**/*.js'
    ])
    .pipe(concat('common.js')) // Собираем их в кучу в новом файле 
    .pipe(minify()) // Сжимаем JS файл
    .pipe(gulp.dest('./front/assets/js/')) // Выгружаем в папку app/js
    .pipe(browserSync.reload({stream: true}));

    done()
});

gulp.task('sass', function() {
  return gulp.src('dev/assets/css/main.scss')
    .pipe(sass())
    // concat will combine all files declared in your "src"
    .pipe(concat('main.css'))
    // .pipe(cssnano())
    .pipe(gulp.dest('./front/assets/css/'))
    .pipe(browserSync.reload({
        stream: true
    }))
});

gulp.task('pages-sass', function(){
  return gulp.src('dev/assets/css/pages/**/*.scss') // Берем все sass файлы из папки sass и дочерних, если таковые будут
    .pipe(sass())
    // .pipe(cssnano())
    .pipe(gulp.dest('./front/assets/css/pages/'))
    .pipe(browserSync.reload({
        stream: true
    }))
    done()
});



gulp.task('templates',async function(done) {
  gulp.src('dev/**/*.html')
    .pipe(include())
      .on('error', console.log)
    .pipe(gulp.dest('./front/'))
    .pipe(browserSync.reload({stream: true}));

    done()
})

gulp.task('watch', gulp.series('sass','pages-sass','scripts','browser-sync', function(done) {
  gulp.watch('dev/assets/js/**/*.js', gulp.parallel('scripts'));
  gulp.watch('dev/assets/css/**/*.scss', gulp.parallel('sass'));
  gulp.watch('dev/assets/css/pages/**/*.scss', gulp.parallel('pages-sass'));
  gulp.watch('dev/assets/css/**/*.scss', gulp.parallel('pages-sass'));
  gulp.watch('dev/**/*.html', gulp.parallel('templates'));
  
  done()
}));

