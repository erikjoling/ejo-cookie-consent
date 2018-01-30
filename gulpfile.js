/**
 * Gulpfile.
 *
 * Gulp with WordPress. Inspired by WPGulp from Ahmad Awais
 *
 * @author Erik Joling
 * @version 0.1
 *
 * Implements:
 *      - CSS: Sass to CSS conversion, error catching, Autoprefixing, Sourcemaps,
 *        and CSS minification.
 *      - JS: Concatenates & uglifies Vendor and Custom JS files.
 *      - Watches files for changes in CSS or JS.
 *      - Corrects the line endings.
 *
 * More ideas to implement from WPGulp;
 *      - Browsersync
 *      - POT translations
 *      - Image minification
 */

/**
 * Configuration.
 *
 * Project Configuration for gulp tasks.
 *
 * In paths you can add <<glob or array of globs>>. Edit the variables as per your project requirements.
 */

// START Editing Project Variables.
// Style related.
var styleSRC                = './assets/css/sass/style.scss'; // Path to main .scss file.
var styleDestination        = './assets/css/'; // Path to place the compiled CSS file.
var styleFile               = 'plugin'; // Path to place the compiled CSS file.
// Default set to root folder.

// JS Vendor related.
var jsVendorSRC             = './assets/js/vendor/*.js'; // Path to JS vendor folder.
var jsVendorDestination     = './assets/js/'; // Path to place the compiled JS vendors file.
var jsVendorFile            = 'vendor'; // Compiled JS vendors file name.
// Default set to vendors i.e. vendors.js.

// JS Custom related.
var jsCustomSRC             = './assets/js/custom/*.js'; // Path to JS custom scripts folder.
var jsCustomDestination     = './assets/js/'; // Path to place the compiled JS custom scripts file.
var jsCustomFile            = 'plugin'; // Compiled JS custom file name.
// Default set to custom i.e. custom.js.

// Watch files paths.
var styleWatchFiles         = './assets/css/sass/**/*.scss'; // Path to all *.scss files inside css folder and inside them.
var vendorJSWatchFiles      = './assets/js/vendor/*.js'; // Path to all vendor JS files.
var customJSWatchFiles      = './assets/js/custom/*.js'; // Path to all custom JS files.


// Browsers you care about for autoprefixing.
// Browserlist https        ://github.com/ai/browserslist
const AUTOPREFIXER_BROWSERS = [
   'last 2 version',
   'ie >= 11',
   'safari >= 10',
   'ios >= 10'
];

// STOP Editing Project Variables.

/**
 * Load Plugins.
 *
 * Load gulp plugins and passing them semantic names.
 */
var gulp         = require('gulp'); // Gulp of-course

// CSS related plugins.
var sass         = require('gulp-sass'); // Gulp pluign for Sass compilation.
var minifycss    = require('gulp-uglifycss'); // Minifies CSS files.
var autoprefixer = require('gulp-autoprefixer'); // Autoprefixing magic.

// JS related plugins.
var concat       = require('gulp-concat'); // Concatenates JS files
var uglify       = require('gulp-uglify'); // Minifies JS files

// Utility related plugins.
var rename       = require('gulp-rename'); // Renames files E.g. style.css -> style.min.css
var lineec       = require('gulp-line-ending-corrector'); // Consistent Line Endings for non UNIX systems. Gulp Plugin for Line Ending Corrector (A utility that makes sure your files have consistent line endings)
var filter       = require('gulp-filter'); // Enables you to work on a subset of the original files by filtering them using globbing.
var sourcemaps   = require('gulp-sourcemaps'); // Maps code in a compressed file (E.g. style.css) back to it’s original position in a source file (E.g. structure.scss, which was later combined with other css files to generate style.css)


/**
 * Task: `styles`.
 *
 * Compiles Sass, Autoprefixes it and Minifies CSS.
 *
 * This task does the following:
 *    1. Gets the source scss file
 *    2. Compiles Sass to CSS
 *    3. Autoprefixes it and generates styleFile.css
 *    4. Renames the CSS file with suffix .min.css
 *    5. Minifies the CSS file
 *    6. Writes Sourcemaps
 *    7. Generates styleFile.min.css
 */
gulp.task('styles', function () {
    gulp.src( styleSRC )
    .pipe( sass( {
       errLogToConsole: true, // errLogToConsole is deprecated (or doesn't work) with Gulp 2.0+. Try using .pipe(sass().on('error', sass.logError))
       outputStyle: 'expanded' // 'compact', 'compressed', 'nested',
    } ) )
    .on('error', console.error.bind(console))
    .pipe( autoprefixer( AUTOPREFIXER_BROWSERS ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( rename({ basename: styleFile }) )
    .pipe( gulp.dest( styleDestination ) )

    .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
    .pipe( sourcemaps.init() )
    .pipe( rename({
        basename: styleFile,
        suffix: '.min' 
    }))
    .pipe( minifycss() )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( sourcemaps.write('.') )
    .pipe( gulp.dest( styleDestination ) )
});


/**
 * Task: `vendorJS`.
 *
 * Concatenate and uglify vendor JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS vendor files
 *     2. Concatenates all the files and generates vendors.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates vendors.min.js
 */
gulp.task( 'vendorsJS', function() {
  gulp.src( jsVendorSRC )
  .pipe( sourcemaps.init() )
  .pipe( concat( jsVendorFile + '.js' ) )
  .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
  .pipe( gulp.dest( jsVendorDestination ) )
  .pipe( rename( {
       basename: jsVendorFile,
       suffix: '.min'
  }))
  .pipe( uglify() )
  .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
  .pipe( sourcemaps.write('.') )
  .pipe( gulp.dest( jsVendorDestination ) )
});


/**
 * Task: `customJS`.
 *
 * Concatenate and uglify custom JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS custom files
 *     2. Concatenates all the files and generates custom.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates custom.min.js
 */
gulp.task( 'customJS', function() {
   gulp.src( jsCustomSRC )
   .pipe( sourcemaps.init() )
   .pipe( concat( jsCustomFile + '.js' ) )
   .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
   .pipe( gulp.dest( jsCustomDestination ) )
   .pipe( rename( {
     basename: jsCustomFile,
     suffix: '.min'
   }))
   .pipe( uglify() )
   .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
   .pipe( sourcemaps.write('.') )
   .pipe( gulp.dest( jsCustomDestination ) )
});

/**
 * Watch Tasks.
 *
 * Watches for file changes and runs specific tasks.
 */
gulp.task( 'default', ['styles', 'vendorsJS', 'customJS'], function () {
    gulp.watch( styleWatchFiles, [ 'styles' ] );
    gulp.watch( vendorJSWatchFiles, [ 'vendorsJS' ] );
    gulp.watch( customJSWatchFiles, [ 'customJS' ] );
});
