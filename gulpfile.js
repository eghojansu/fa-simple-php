var gulp = require('gulp')
var $ = require('gulp-load-plugins')()
var path = (function() {
    var p = {}

    p.sass = function(suffix) {
        return 'dev/sass/' + (suffix || '')
    }

    p.dest = function(suffix) {
        return 'asset/' + (suffix || '')
    }

    return p
})()

gulp.task('compile-sass', function() {
    gulp.src(path.sass('style.scss'))
        .pipe($.sass())
        .pipe(gulp.dest(path.dest('css')))
})

gulp.task('watch-changes', function() {
    gulp.watch(path.sass('**/*.scss'), ['compile-sass'])
})

gulp.task('default', ['compile-sass', 'watch-changes']);
