var gulp        = require('gulp');
var sass        = require('gulp-sass')(require('sass'));
var cleanCSS    = require('gulp-clean-css');

const paths = {
    scss: {
        src: './scss/*.scss',
        dest: './css',
        watch: './scss/*.scss',
    }
};

// Compile sass into CSS & auto-inject into browsers
gulp.task('sass', function() {
    return gulp.src(paths.scss.src)
        .pipe(sass({outputStyle: 'expanded'}))
        .pipe(cleanCSS({
            compatibility: 'ie8',
            format: 'beautify'
        }))
        .pipe(gulp.dest("css"));
});

// Static Server + watching scss/html files
gulp.task('serve', gulp.series('sass', function() {
    gulp.watch("scss/component/*.scss", gulp.series('sass'));
}));

gulp.task('default', gulp.series('serve'));
gulp.task('build', gulp.series('sass'));