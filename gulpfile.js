var gulp = require('gulp');
var sass = require('gulp-sass');
var browserSync = require('browser-sync').create();

gulp.task('browserSync', ['sass', 'js'],  function () {
    browserSync.init({
        server: {
            baseDir: 'app'
        }
    })
});

gulp.task('sass', function () {
    return gulp.src(['node_modules/bootstrap/scss/bootstrap.scss', 'app/assets/scss/*.sass'])
        .pipe(sass())
        .pipe(gulp.dest('app/assets/css'))
        .pipe(browserSync.reload({
            stream: true
        }))
});

gulp.task('js', function () {
   return gulp.src(['node_modules/bootstrap/dist/js/bootstrap.min.js', 'node_modules/jquery/dist/jquery.min.js', 'node_modules/pwacompat/pwacompat.min.js'])
       .pipe(gulp.dest("app/assets/js"))
});

gulp.task('start', ['browserSync'], function () {
    gulp.watch(['node_modules/bootstrap/scss/bootstrap.scss', 'app/assets/scss/*.sass'], ['sass']);
    gulp.watch("app/*.html").on('change', browserSync.reload);
});

