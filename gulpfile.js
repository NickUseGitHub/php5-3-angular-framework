var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglifyjs');
var runSequence = require('run-sequence').use(gulp);

/**
 * Build deploy
 */
gulp.task('build:prepare-for-deploy', function() {
  runSequence([ 
        'compress:core-js',
        'build:CMS'
    ]);
});

//core
gulp.task('compress:core-js', function () {
    return gulp.src([
                    'app/bower_components/jquery/dist/jquery.min.js',
                    'app/bower_components/jquery-ui/jquery-ui.min.js',
                    'app/bower_components/bootstrap/dist/js/bootstrap.min.js',
                    'app/bower_components/angular/angular.js',
                    'app/bower_components/is_js/is.min.js'
                ])
                .pipe(uglify('core.js', {
                mangle: false,
                output: {
                    beautify: false
                }
                }))
                .pipe(gulp.dest('dist/js'))
});

/***********************
 * CMS admin module    *
 ***********************/
/**
 * js
 */
//build modules
gulp.task('build:CMS', function() {
  runSequence([ 
        'build:cms-home-js',
        'compress:cms-member-js'
    ]);
});

// Home cms base
gulp.task('build:cms-home-js', function () {
    return gulp.src([
                    'app/bower_components/angular-route/angular-route.min.js',
                    'app/bower_components/angular-cookies/angular-cookies.min.js',
                    'app/bower_components/angular-modal-service/dst/angular-modal-service.js',
                    'app/bower_components/ng-file-upload/ng-file-upload.min.js',
                    'app/bower_components/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js',
                    'app/bower_components/textAngular/dist/textAngular-rangy.min.js',
                    'app/bower_components/textAngular/dist/textAngular-sanitize.min.js',
                    'app/bower_components/textAngular/dist/textAngular.min.js',
                    'app/bower_components/scriptjs/src/script.js',
                    'app/js/Cms/Admin/main.js',
                    'app/js/ng/service/SessionStorage.js',
                    'app/js/Cms/service/StaffService.js',
                    'app/js/ng/service/HttpService.js'
                ])
                .pipe(uglify('home.bundle.js', {
                mangle: false,
                output: {
                    beautify: false
                }
                }))
                .pipe(gulp.dest('dist/js/Cms/Admin'))
});
//memberModule
gulp.task('compress:cms-member-js', function () {
    return gulp.src([
                    'app/js/Cms/Admin/Members/MemberController.js'
                ])
                .pipe(uglify('MemberController.js', {
                mangle: false,
                output: {
                    beautify: false
                }
                }))
                .pipe(gulp.dest('dist/js/Cms/Admin/Members'))
});

//scss & css
gulp.task('cms-design-admin-sass', function () {
    return gulp.src('./app/css/cms/*.scss')
    .pipe(sass())
    .pipe(gulp.dest('./dist/css/cms'))
});

gulp.task('watch:cms-design-admin-sass', function(){
  gulp.watch('./app/css/cms/*.scss', ['cms-design-admin-sass']); 
});

/**
 * Mobile design
 */
gulp.task('mobile-design-main-sass', function () {
    return gulp.src('./css/style/mobile/*.scss')
    .pipe(sass())
    .pipe(gulp.dest('./dist/css/mobile'))
});

gulp.task('watch:mobile-design', function(){
  gulp.watch('./css/style/mobile/*.scss', ['mobile-design-main-sass']); 
});

/**
 * master design
 */
gulp.task('base-sass', function () {
    return gulp.src('./css/style/*.scss')
    .pipe(sass())
    .pipe(gulp.dest('./dist/css'))
});

gulp.task('watch:base-design', function(){
  gulp.watch('./css/style/*.scss', ['base-sass']); 
});