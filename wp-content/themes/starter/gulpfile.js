var gulp   		= require('gulp'),
	concat 		= require('gulp-concat'),
	uglify 		= require('gulp-uglify'),
	rename 		= require('gulp-rename'),
	sass   		= require('gulp-sass'),
	livereload  = require('gulp-livereload'),
	svgSprite 	= require('gulp-svg-sprite');

var config = {
	scripts: [
		'./assets/js/vendor/bootstrap/util.js',
		'./assets/js/vendor/bootstrap/alert.js',
		'./assets/js/vendor/bootstrap/button.js',
		'./assets/js/vendor/bootstrap/carousel.js',
		'./assets/js/vendor/bootstrap/collapse.js',
		'./assets/js/vendor/bootstrap/dropdown.js',
		'./assets/js/vendor/bootstrap/modal.js',
		'./assets/js/vendor/bootstrap/scrollspy.js',
		'./assets/js/vendor/bootstrap/tab.js',
		// Tooltip requires Tether to work properly
		// './assets/js/vendor/bootstrap/tooltip.js',
		// './assets/js/vendor/bootstrap/popover.js',
		// Modernizr
		'./assets/js/vendor/modernizr/modernizr.shiv.js',
		// Any Custom Scripts
		'./assets/js/app/**/*.js'
	]
};


// Outputs a minfiied and non-minfied version of all scripts
gulp.task('scripts', function() {
	return gulp.src(config.scripts)
			.pipe(concat('scripts.js'))
			.pipe(gulp.dest('./assets/js/'))
			.pipe(uglify())
			.pipe(rename({ extname: '.min.js' }))
			.pipe(livereload())
			.pipe(gulp.dest('./assets/js/'));
});

gulp.task('sass', function () {
	return gulp.src('./assets/sass/style.scss')
			.pipe(sass.sync({outputStyle: 'compressed'}).on('error', sass.logError))
			.pipe(livereload())
			.pipe(gulp.dest('./'));
});

gulp.task('sprites', function () {
	return gulp.src('**/*.svg', {cwd: './assets/svg/individual'})
			.pipe(svgSprite({ shape: { transform: ['svgo'] }, mode: { defs: {dest: '.'} } } ) )
			.pipe(gulp.dest('./assets/'));
});
gulp.task('icons', ['sprites']);

gulp.task('watch', function () {
	livereload.listen(35729);
	gulp.watch('**/*.php').on('change', function(file) {
	      livereload.changed(file.path);
	  });
	gulp.watch('./assets/sass/**/*.scss', ['sass']);
	gulp.watch('./assets/js/**/*.js', ['scripts']);
});

gulp.task('default', ['sass', 'scripts', 'watch']);