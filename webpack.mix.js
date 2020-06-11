const mix = require('laravel-mix');
const path = require('path');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/lesson-form.js', 'public/js/lesson-form.js')
    .js('resources/js/assessment/details.js', 'public/js/assessment/details.js')
    .js('resources/js/page/student/list.js', 'public/js/page/student/list.js')
    .js('resources/js/page/schedule/details/tabs/paper_test/fields.js', 'public/js/page/schedule/details/tabs/paper_test/fields.js')
    .js('resources/js/vendor/jquery.ui.touch-punch.js', 'public/js/vendor/jquery.ui.touch-punch.js')
    .js('resources/js/page/course/details.js', 'public/js/page/course/details.js')
    .js('resources/js/page/student/details.js', 'public/js/page/student/details.js')
    .js('resources/js/page/student/edit.js', 'public/js/page/student/edit.js')
    .js('resources/js/page/student/create.js', 'public/js/page/student/create.js')
    .js('resources/js/page/schedule/monthly.js', 'public/js/page/schedule/monthly.js')
    .js('resources/js/page/schedule/calendar.js', 'public/js/page/schedule/calendar.js')
    .js('resources/js/page/student/class_usage_tab.js', 'public/js/page/student/class_usage_tab.js')
    .js('resources/js/page/home.js', 'public/js/page/home.js')
    .js('resources/js/page/stats.js', 'public/js/page/stats.js')
    .js('resources/js/page/schedule/waitlisted_students.js', 'public/js/page/schedule/waitlisted_students.js')
    .js('resources/js/page/schedule/details/tabs/course-progress.js', 'public/js/page/schedule/details/tabs/course-progress.js')
    .js('resources/js/page/tags/index.js', 'public/js/page/tags/index.js')
    .js('resources/js/page/availability_selection_calendars/index.js', 'public/js/page/availability_selection_calendars/index.js')
    .js('resources/js/page/availability_selection_calendars/edit_calendar/index.js', 'public/js/page/availability_selection_calendars/edit_calendar/index.js')
    .js('resources/js/page/user/take.js', 'public/js/page/user/take.js')
    .js('resources/js/page/assessment/assessment_user/details.js', 'public/js/page/assessment/assessment_user/details.js')
    .js('resources/js/page/availability_selection_calendars/responses.js', 'public/js/page/availability_selection_calendars/responses.js')
    .js('resources/js/page/assessment/list.js', 'public/js/page/assessment/list.js')
    .js('resources/js/page/setting/email_templates.js', 'public/js/page/setting/email_templates.js')
    .js('resources/js/page/accounting/manage-monthly-payments.js', 'public/js/page/accounting/manage-monthly-payments.js')
    .js('resources/js/page/accounting/payments.js', 'public/js/page/accounting/payments.js')
    .js('resources/js/page/teacher/list.js', 'public/js/page/teacher/list.js')
    .js('resources/js/page/filename.js', 'public/js/page/filename.js')
    .js('resources/js/page/schedule/details/tabs/comments.js', 'public/js/page/schedule/details/tabs/comments.js')
    .js('resources/js/page/setting/notification-settings.js', 'public/js/page/setting/notification-settings.js')
    .js('resources/js/page/setting/line-settings.js', 'public/js/page/setting/line-settings.js')
    .sass('resources/sass/app.scss', 'public/css/app.css')
    .version();

mix.options({
    processCssUrls: false
});
mix.copy('resources/css', 'public/css');

mix.webpackConfig({
    resolve: {
        alias: {
            ziggy: path.resolve('vendor/tightenco/ziggy/dist/js/route.js'),
            ziggyRoutes: path.resolve('resources/generated/ziggy.js'),
        },
    },
})

mix.copy('./node_modules/font-awesome/fonts/**', 'public/fonts');

// if (!mix.inProduction()) {
//     mix.webpackConfig({
//         devtool: 'source-map'
//     })
//     .sourceMaps()
// }