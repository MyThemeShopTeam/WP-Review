const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');

const sassOptions = {
	errLogToConsole: true,
	outputStyle: 'compressed'
};

gulp.task('sass', function() {
	return gulp
		.src('public/sass/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(sourcemaps.write('../sourcemaps'))
		.pipe(gulp.dest('public/css'));
});

gulp.task('admin-sass', function() {
	return gulp
		.src('admin/assets/sass/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(sourcemaps.write('../sourcemaps'))
		.pipe(gulp.dest('admin/assets/css'));
});

gulp.task('default', gulp.series('sass', 'admin-sass'));
