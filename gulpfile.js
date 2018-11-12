var gulp = require('gulp');
var sass = require('gulp-sass');
var browserSync = require('browser-sync').create();

gulp.task('browserSync', ['sass'],  function () {
    browserSync.init({
        server: {
            baseDir: 'app'
        }
    })
});

gulp.task('sass', function () {
    return gulp.src('app/scss/style.sass')
        .pipe(sass())
        .pipe(gulp.dest('app/css'))
        .pipe(browserSync.reload({
            stream: true
        }))
});

gulp.task('watch', ['browserSync'], function () {
    gulp.watch('app/scss/style.sass', ['sass']);
    gulp.watch("app/*.html").on('change', browserSync.reload);
});

