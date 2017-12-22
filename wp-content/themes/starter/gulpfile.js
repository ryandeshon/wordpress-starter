var gulp   		= require('gulp'),
	concat 		= require('gulp-concat'),
	uglify 		= require('gulp-uglify'),
	rename 		= require('gulp-rename'),
	sass   		= require('gulp-sass'),
	livereload  = require('gulp-livereload'),
	svgSprite 	= require('gulp-svg-sprite');

var config = {
	scripts: [
		'./node_modules/bootstrap/dist/js/bootstrap.js',
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
	return gulp.src(['node_modules/bootstrap/scss/bootstrap.scss', './assets/sass/style.scss'])
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
	gulp.watch('./assets/js/**/*.js');
});

gulp.task('default', ['sass', 'scripts', 'watch']);