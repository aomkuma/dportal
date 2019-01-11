var gulp = require('gulp'); 
var browserify = require('gulp-browserify');
var concat = require('gulp-concat');
var refresh = require('gulp-livereload');
var each = require('gulp-each');
var fc2json = require('gulp-file-contents-to-json');

gulp.task('buildFonts', function () {
	return gulp.src(['./fonts/*.*'])
		.pipe(each(function (content, file, callback) {
			var newContent = new Buffer(content).toString('base64');
			callback(null, newContent);
		}, 'buffer'))
		.pipe(fc2json('build/vfs_fonts.js'))
		.pipe(each(function (content, file, callback) {
			var newContent = 'window.pdfMake = window.pdfMake || {}; window.pdfMake.vfs = ' + content + ';';
			callback(null, newContent);
		}, 'buffer'))
		.pipe(gulp.dest('build'));
});