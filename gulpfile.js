let preprocessor = 'sass', // Preprocessor (sass, less, styl); 'sass' also work with the Scss syntax in blocks/ folder.
	fileswatch = 'html,htm,txt,json,md,woff2,js,php,sass' // List of files extensions for watching & hard reload

const { src, dest, parallel, series, watch } = require('gulp')
const browserSync = require('browser-sync').create()
const bssi = require('browsersync-ssi')
const ssi = require('ssi')
const webpack = require('webpack-stream')
const sass = require('gulp-sass')
const sassglob = require('gulp-sass-glob')
const less = require('gulp-less')
const lessglob = require('gulp-less-glob')
const styl = require('gulp-stylus')
const stylglob = require("gulp-noop")
const cleancss = require('gulp-clean-css')
const autoprefixer = require('gulp-autoprefixer')
const rename = require('gulp-rename')
const imagemin = require('gulp-imagemin')
const newer = require('gulp-newer')
const rsync = require('gulp-rsync')
const del = require('del')
const webp = require('gulp-webp')
const webphtml = require('gulp-webp-html')
const webpcss = require('gulp-webp-css')

function browsersync() {
	browserSync.init({
		proxy: "http://test.local",
		host: "test.local",
		open: "external",
	})
}

function php() {
	return src('**/*.php')
		.pipe(dest('app/../'))
		.pipe(browserSync.stream())
}
function phpbuild() {
	return src('**/*.php')
		.pipe(webphtml())
		.pipe(dest('app/../'))
		.pipe(browserSync.stream())
}
function scripts() {
	return src(['app/js/*.js', '!app/js/*.min.js'])
		.pipe(dest('app/../js'))
		.pipe(browserSync.stream())
}

function styles() {
	return src([`app/${preprocessor}/*.*`, `!app/${preprocessor}/_*.*`])
		.pipe(eval(`${preprocessor}glob`)())
		.pipe(eval(preprocessor)())
		.pipe(webpcss())
		.pipe(autoprefixer({ overrideBrowserslist: ['last 10 versions'], grid: true }))
		.pipe(cleancss({ level: { 1: { specialComments: 0 } }, /* format: 'beautify' */ }))
		//.pipe(rename({ suffix: ".min" }))
		.pipe(dest('app/../'))
		.pipe(browserSync.stream())
}

function images() {
	return src(['app/img/**/*', '../../uploads/**/*'])
		.pipe(newer('images/'))
		.pipe(
			webp({
				quality: 70
			})
		)
		.pipe(dest('images/'))
		.pipe(src(['app/img/**/*', '../../uploads/**/*']))
		.pipe(newer('images/'))
		.pipe(
			imagemin({
				progressive: true,
				svgoPluginus: [{ removeVievBox: false }],
				interlaced: true,
				optimizationLevel: 3,
			})
		)
		.pipe(dest('images/'))
		.pipe(browserSync.stream())
}


function deploy() {
	return src('dist/')
		.pipe(rsync({
			root: 'dist/',
			hostname: 'username@yousite.com',
			destination: 'yousite/public_html/',
			// clean: true, // Mirror copy with file deletion
			include: [/* '*.htaccess' */], // Included files to deploy,
			exclude: ['**/Thumbs.db', '**/*.DS_Store'],
			recursive: true,
			archive: true,
			silent: false,
			compress: true
		}))
}

function startwatch() {
	watch(`app/${preprocessor}/**/*`, { usePolling: true }, styles)
	watch(['app/js/**/*.js', '!app/js/**/*.min.js'], { usePolling: true }, scripts)
	watch('images/**/*.{jpg,jpeg,png,webp,svg,gif}', { usePolling: true }, images)
	watch(`**/*.{${fileswatch}}`, { usePolling: true }).on('change', browserSync.reload)
}

exports.php = php
exports.scripts = scripts
exports.styles = styles
exports.images = images
exports.deploy = deploy
exports.build = series(scripts, styles, images, phpbuild)
exports.default = series(php, scripts, styles, images, parallel(browsersync, startwatch))
