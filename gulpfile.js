const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const removeEmptyLines = require('gulp-remove-empty-lines');
const zip = require('gulp-zip');

const sassOptions = {
	errLogToConsole: true,
	outputStyle: 'compact'
};

const zipSrc = [
	'./',
	'./**/*',

	'!node_modules/',
	'!node_modules/**/*',

	'!admin/assets/sass/',
	'!admin/assets/sass/**/*',

	'!admin/assets/sourcemaps/',
	'!admin/assets/sourcemaps/**/*',

	'!public/sass/',
	'!public/sass/**/*',

	'!public/sourcemaps/',
	'!public/sourcemaps/**/*',

	'!.gitignore',
	'!gulpfile.js',
	'!package.json',
	'!package-lock.json',
	'!prepros-6.config',
	'!.editorconfig',
	'!npm-debug.log'
];

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

gulp.task('sass-no-maps', function() {
	return gulp
		.src('public/sass/*.scss')
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(removeEmptyLines())
		.pipe(gulp.dest('public/css'));
});

gulp.task('admin-sass-no-maps', function() {
	return gulp
		.src('admin/assets/sass/*.scss')
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(removeEmptyLines())
		.pipe(gulp.dest('admin/assets/css'));
});

gulp.task('zip', function() {
	return gulp
		.src(zipSrc, { base: '../' })
		.pipe(zip('wp-review-pro.zip'))
		.pipe(gulp.dest('../'));
});

gulp.task('dev', ['sass', 'admin-sass']);
gulp.task('default', ['sass-no-maps', 'admin-sass-no-maps']);
