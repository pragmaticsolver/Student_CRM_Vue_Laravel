<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true,'register' => false]);
Route::group(['middleware' => ['guest']], function () {
    Route::get('signup', 'Auth\RegisterController@signup')->name('signup.create');;
    Route::post('signup', 'Auth\RegisterController@doStoreSignup')->name('signup.store');

    Route::get('forgot-password','CustomForgotPasswordController@forgotPassword')->name('forgot-password');
    Route::post('reset-password-link', 'CustomForgotPasswordController@sendResetPasswordLink')->name('reset-password-link');
    Route::get('reset-password/{token}', 'CustomForgotPasswordController@resetPassword')->name('reset-password');
    Route::post('reset-password', 'CustomForgotPasswordController@resetPasswordSubmit')->name('reset-password-submit');

    Route::get('login-with-line', 'Auth\LoginController@loginWithLine')->name('login.line');
    Route::get('login-with-line/callback', 'Auth\LoginController@loginWithLineCallback')->name('login.line.callback');
});

Route::get('link-line-force-login/{link_token}', 'UserController@linkLineForceLogin')->name('link.line.force.login');

Route::group(['middleware' => ['auth']], function () {
    Route::post('change-language', 'HomeController@doLanguageChange')->name('change-language');
});
Route::get('/js/lang', function() {
    $lang = config('app.locale');

    $files   = glob(resource_path('lang/' . $lang . '/*.php'));
    $strings = [];

    foreach ($files as $file) {
        $name           = basename($file, '.php');
        $strings[$name] = require $file;
    }

    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode($strings) . ';');
    exit();
})->name('assets.lang');

Route::group(['prefix' => 'terminal'], function () {
    Route::get('','TerminalController@index')->name('terminal.index');
    Route::get('checkin','TerminalController@checkin')->name('terminal.checkin');
    Route::post('checkin','TerminalController@checkinSubmit')->name('terminal.checkin_submit');
    Route::post('checkout','TerminalController@checkoutSubmit')->name('terminal.checkout_submit');
    Route::get('checkout_book','TerminalController@checkoutBook')->name('terminal.checkout_book');
    Route::post('checkout_book','TerminalController@checkoutBookSubmit')->name('terminal.checkout_book_submit');
    Route::get('make_reservation','TerminalController@makeReservation')->name('terminal.make_reservation');
});

// Email Actions
Route::group(['prefix' => 'ea'], function () {
    Route::get('cancel_reservation/{id}','CancelReservationController@index')->name('cancel_reservation');
    Route::post('cancel_reservation','CancelReservationController@cancelReservation')->name('cancel_reservation_submit');

    Route::get('waitlist/reserve/{id}','WaitlistController@reserve_watitlist')->name('waitlist.reserve');
    Route::post('waitlist/reserve/{id}','WaitlistController@reserve_waitlist_submit')->name('waitlist.reserve.submit');

    Route::get('waitlist/cancel/{id}','WaitlistController@cancel_waitlist')->name('waitlist.cancel');
    Route::post('waitlist/cancel/{id}','WaitlistController@cancel_waitlist_submit')->name('waitlist.cancel.submit');

    Route::get('status/{lang}','WaitlistController@status_page')->name('statuspage');
});


Route::group(['middleware' => ['auth', 'checkpermission']], function () {
    Route::get('user/switch/stop', 'UserController@student_switch_stop' )->name('stop_impersonate');
    Route::get('link-line-account/{link_token}', 'UserController@linkLineAccount')->name('link.line.account');
});

Route::post('roles/categories', 'RoleController@getCategories');

Route::group(['middleware' => ['verified', 'auth', 'checkpermission']], function () {
    Route::get('student/switch/start/{id}', 'UserController@student_switch_start' )->name('student.start_impersonate');

    Route::group(['prefix' => 'book'], function () {
        Route::get('checkin', 'BookController@checkin')->name('book.checkin.create');
        Route::get('checkout', 'BookController@checkout')->name('book.checkout.create');;
        Route::post('checkin', 'BookController@update_checkin')->name('book.checkin.store');
        Route::post('checkout', 'BookController@update_checkout')->name('book.checkout.store');
    });

    Route::get('library-settings', 'SettingController@library')->name('library-settings.edit');
    Route::post('library-settings', 'SettingController@library_update')->name('library-settings.update');

    Route::group(['middleware' => 'valid_user_schedule'], function() {
        Route::group(['prefix' => 'paper-test'], function() {
            Route::get('student/create/{schedule_id}', 'PaperTestController@schedule_create')->name('student.paper_test.create');
            Route::get('student/edit/{student_paper_test_id}', 'PaperTestController@schedule_edit')->name('student.paper_test.edit');
            Route::post('student/store/{schedule_id}/{student_paper_test_id?}', 'PaperTestController@schedule_store')->name('student.paper_test.store');
            Route::delete('student/destroy/{schedule_id}/{student_paper_test_id}', 'PaperTestController@student_destroy')->name('student.paper_test.destroy');
        });

        Route::get('details/{schedule_id}', 'ScheduleController@show')->name('schedule.show');
    });

    Route::group(['prefix' => 'paper-test'], function() {
        Route::post('get-total-score', 'PaperTestController@get_total_score')->name('paper_test.get_total_score');
    });

    Route::get('email-settings', 'EmailTemplateController@get')->name('email-settings.edit');
    Route::post('email-settings-update', 'EmailTemplateController@update')->name('email-settings.update');
    Route::get('preview_drr_email', 'EmailTemplateController@preview_drr_email')->name('preview_drr_email');

    Route::get('terminal-settings', 'SettingController@terminalSettings')->name('terminal-settings.edit');
    Route::post('terminal-settings-update', 'SettingController@terminalSettingsUpdate')->name('terminal-settings.update');

    Route::group(['prefix' => 'test'], function() {
        Route::get('add', 'TestController@create')->name('test.create');
        Route::post('store', 'TestController@store')->name('test.store');
        Route::get('list', 'TestController@index')->name('test.index');
        Route::delete('delete/{id}', 'TestController@destroy')->name('test.destroy');
        Route::get('edit/{id}', 'TestController@edit')->name('test.edit');
        Route::patch('update/{id}', 'TestController@update')->name('test.update');
        Route::get('details/{id}', 'TestController@show')->name('test.show');
    });

    Route::group(['prefix' => 'question'], function() {
        Route::get('add', 'QuestionController@create')->name('question.create');
        Route::post('store', 'QuestionController@store')->name('question.store');
        Route::get('list', 'QuestionController@index')->name('question.index');
        Route::delete('delete/{id}', 'QuestionController@destroy')->name('question.destroy');
        Route::get('edit/{id}', 'QuestionController@edit')->name('question.edit');
        Route::patch('update/{id}', 'QuestionController@update')->name('question.update');
        Route::get('details/{id}', 'QuestionController@show')->name('question.show');
    });

    Route::group(['prefix' => 'answer'], function() {
        Route::get('add', 'AnswerController@create')->name('answer.create');
        Route::post('store', 'AnswerController@store')->name('answer.store');
        Route::delete('delete/{id}', 'AnswerController@destroy')->name('answer.destroy');
        Route::get('edit/{id}', 'AnswerController@edit')->name('answer.edit');
        Route::patch('update/{id}', 'AnswerController@update')->name('answer.update');
    });

    Route::group(['prefix' => 'schedule'], function() {
        Route::patch('lesson/complete', 'ScheduleLessonController@complete')->name('schedule.lesson.complete');
        Route::delete('lesson/{id}', 'ScheduleLessonController@destroy')->name('schedule.lesson.destroy');
        Route::post('lesson/comments', 'ScheduleLessonController@saveComments')->name('schedule.lesson.comments');
    });

    Route::group(['prefix' => 'student-test'], function() {
        Route::delete('destroy/{student_test_id}', 'StudentTestController@destroy')->name('student-test.destroy');
    });

    Route::group(['prefix' => 'paper-test'], function() {
        Route::delete('delete/{id}', 'PaperTestController@destroy')->name('paper_test.destroy');
        Route::get('edit/{id}', 'PaperTestController@edit')->name('paper_test.edit');
        Route::patch('update/{id}', 'PaperTestController@update')->name('paper_test.update');
        Route::get('details/{id}', 'PaperTestController@show')->name('paper_test.show');
    });

    Route::group(['prefix' => 'comment-template'], function() {
        Route::get('add', 'CommentTemplateController@create')->name('comment_template.create');
        Route::post('store', 'CommentTemplateController@store')->name('comment_template.store');
        Route::get('list', 'CommentTemplateController@index')->name('comment_template.index');
        Route::delete('delete/{id}', 'CommentTemplateController@destroy')->name('comment_template.destroy');
        Route::get('edit/{id}', 'CommentTemplateController@edit')->name('comment_template.edit');
        Route::patch('update/{id}', 'CommentTemplateController@update')->name('comment_template.update');
    });

    Route::group(['prefix' => 'course'], function() {
        Route::get('add', 'CourseController@create')->name('course.create');
        Route::post('store', 'CourseController@store')->name('course.store');
        Route::get('list', 'CourseController@index')->name('course.index');
        Route::delete('delete/{id}', 'CourseController@destroy')->name('course.destroy');
        Route::get('edit/{id}', 'CourseController@edit')->name('course.edit');
        Route::patch('update/{id}', 'CourseController@update')->name('course.update');
        Route::get('details/{id}', 'CourseController@show')->name('course.show');
        Route::get('units','CourseController@units')->name('course.units');
        Route::get('reorder_units/{id}','CourseController@reorderUnitsForm')->name('course.reorder_units.form');
        Route::post('reorder_units/{id}','CourseController@reorderUnitsSave')->name('course.reorder_units.save');
    });

    Route::group(['prefix' => 'unit'], function() {
        Route::get('add', 'UnitController@create')->name('unit.create');
        Route::post('store', 'UnitController@store')->name('unit.store');
        Route::get('list', 'UnitController@index')->name('course.index');
        Route::delete('delete/{id}', 'UnitController@destroy')->name('unit.destroy');
        Route::get('edit/{id}', 'UnitController@edit')->name('course.edit');
        Route::get('edit_modal/{id}', 'UnitController@edit_modal')->name('course.edit.modal');
        Route::patch('update/{id}', 'UnitController@update')->name('unit.update');
        Route::get('details/{id}', 'UnitController@show')->name('unit.show');
        Route::get('reorder_lessons/{id}','UnitController@reorderLessonsForm')->name('unit.reorder_lessons.form');
        Route::post('reorder_lessons/{id}','UnitController@reorderLessonsSave')->name('unit.reorder_lessons.save');
    });

    Route::group(['prefix' => 'lesson'], function() {
        Route::get('add', 'LessonController@create')->name('lesson.create');
        Route::post('store', 'LessonController@store')->name('lesson.store');
        Route::get('list', 'LessonController@index')->name('lesson.index');
        Route::delete('delete/{id}', 'LessonController@destroy')->name('lesson.destroy');
        Route::get('edit/{id}', 'LessonController@edit')->name('lesson.edit');
        Route::get('edit_fields/{id}', 'LessonController@edit_fields')->name('lesson.edit.fields');
        Route::patch('update/{id}', 'LessonController@update')->name('lesson.update');
        Route::get('details/{id}', 'LessonController@show')->name('lesson.show');
        Route::post('upload_lesson_file/{section}/{id}', 'ImageUploadController@uploadLessonFile')->name('lessonfile.upload');
        Route::post('delete_lesson_file/{id}', 'ImageUploadController@deleteLessonFile')->name('lessonfile.delete');
        
        Route::post('update_exercise_status', 'LessonController@update_exercise_status')->name('lesson.update_exercise_status');
        Route::post('update_homework_status', 'LessonController@update_homework_status')->name('lesson.update_homework_status');
    });
    Route::post('update_file_name/{id}', 'ImageUploadController@updateFileName')->name('filename.update');
    
    Route::post('upload_temp_file','ImageUploadController@upload_temp_file')->name('upload_temp_file');

    Route::group(['prefix' => 'assessment'], function() {
        Route::get('add', 'AssessmentController@create')->name('assessment.create');
        Route::post('store', 'AssessmentController@store')->name('assessment.store');
        Route::get('list', 'AssessmentController@index')->name('assessment.index');
        Route::delete('delete/{id}', 'AssessmentController@destroy')->name('assessment.destroy');
        Route::get('edit/{id}', 'AssessmentController@edit')->name('assessment.edit');
        Route::patch('update/{id}', 'AssessmentController@update')->name('assessment.update');
        Route::get('details/{id}', 'AssessmentController@show')->name('assessment.show');

        Route::get('questions/{id}', 'AssessmentController@questions')->name('assessment.questions');
        Route::get('reorder_questions/{id}','AssessmentController@reorderQuestionsForm')->name('assessment.reorder_questions.form');
        Route::post('reorder_questions/{id}','AssessmentController@reorderQuestionsSave')->name('assessment.reorder_questions.save');

        Route::get('responses/{assessment_id}', 'AssessmentController@responses')->name('assessment.responses');
        Route::get('preview/{assessment_id}', 'AssessmentController@preview')->name('assessment.preview');
    });

    Route::group(['prefix' => 'assessment-question'], function() {
        Route::get('add', 'AssessmentQuestionController@create')->name('assessment-question.create');
        Route::post('store', 'AssessmentQuestionController@store')->name('assessment-question.store');
        Route::delete('delete/{id}', 'AssessmentQuestionController@destroy')->name('assessment-question.destroy');
        Route::get('edit/{id}', 'AssessmentQuestionController@edit')->name('assessment-question.edit');
        Route::get('edit_fields/{id}', 'AssessmentQuestionController@edit_fields')->name('assessment-question.edit_fields');
        Route::patch('update/{id}', 'AssessmentQuestionController@update')->name('assessment-question.update');
    });

    Route::group(['prefix' => 'assessment-user'], function() {
        Route::delete('destroy/{assessment_user_id}', 'AssessmentUserController@destroy')->name('assessment_user.destroy');
        Route::get('create/{schedule_id}', 'AssessmentUserController@create')->name('assessment_user.create');
        Route::post('store/{schedule_id}', 'AssessmentUserController@store')->name('assessment_user.store');
        Route::get('details/{assessment_user_id}', 'AssessmentUserController@show')->name('assessment_user.show');

        Route::get('assign/{assessment_id}', 'AssessmentUserController@assignAssessmentData')->name('assessment.data');
        Route::post('assign', 'AssessmentUserController@assignSubmit')->name('assessment.assign');
    });

    Route::group(['prefix' => 'student'], function() {
        Route::post('reconfirm/{user_id}', 'StudentController@doForceReconfirm')->name('student.reconfirm');
        Route::post('force-verify/{user_id}', 'StudentController@doForceVerify')->name('student.force-verify');
        
        Route::get('online-test/list', 'StudentController@test_list')->name('student.online_test.index');
        Route::get('paper-test/list', 'StudentController@paper_test_list')->name('student.paper_test.index');

        Route::group(['middleware' => 'valid_student_test'], function() {
            Route::get('online-test/{student_test_id}', 'StudentController@take_test')->name('student.online_test.take');
            Route::post('online-test/{student_test_id}', 'StudentTestController@store_result')->name('student.online_test.store_result');
        });

        Route::get('map', 'StudentController@map')->name('student.map');
        Route::get('search', 'StudentController@search')->name('student.search');
        Route::get('attendance_calendar_data','StudentController@attendance_calendar_data')->name('student.att_cal_data');
        Route::get('class_usage_details','StudentController@class_usage_details')->name('student.class_usage_details');

        Route::get('class_usage', 'StudentController@class_usage')->name('student.class_usage');
        Route::get('classes', 'StudentController@classes')->name('student.classes');
        Route::get('class/{schedule_id}', 'StudentController@class_details')->name('student.class_details');
        Route::get('assessments','StudentController@assessments')->name('student.assessments');
        Route::get('assessment/{id}','StudentController@view_assessment')->name('student.view_assessment');

        Route::post('payment-settings/{id}','StudentController@savePaymentSettings')->name('student.payement-settings.save');
        Route::post('archive/{id}','StudentController@archiveStudent')->name('student.archive');
        Route::post('comment/{id}','StudentController@updateComment')->name('student.comment');
    });

    Route::get('payments', 'StudentController@payments')->name('payments.index');

    Route::get('user/assessment/list', 'UserController@assessment_list')->name('user.assessment.index');

    Route::group(['middleware' => 'valid_user_assessment'], function() {
        Route::get('user/assessment/{assessment_user_id}', 'UserController@take_assessment')->name('user.assessment.take');
        Route::post('user/assessment/{assessment_user_id}', 'AssessmentUserController@store_result')->name('user.assessment.store_result');
    });

    Route::group(['prefix' => ''], function () {
        Route::get('', 'HomeController@index')->name('home');
        Route::get('date/{date}', 'HomeController@byDate')->name('schedule.listbydate');
        Route::post('date', 'HomeController@getDate')->name('schedule.get.date');
        Route::get('student_row','HomeController@student_row')->name('student.row');

        Route::get('daily_stats', 'HomeController@daily_stats')->name('daily_stats');
        Route::get('stats', 'HomeController@stats')->name('stats');

        Route::get('stats_data/non_zero_class', 'HomeController@statsDataNonZeroClass')->name('stats_data.non_zero_class');
        Route::get('stats_data/attendances', 'HomeController@statsDataAttendances')->name('stats_data.attendances');
    });

    Route::group(['prefix' => 'accounting'], function () {
        Route::get('plan/list', 'PaymentPlanController@index')->name('plan.index');
        Route::get('plan/add', 'PaymentPlanController@create')->name('plan.create');
        Route::post('plan/store', 'PaymentPlanController@store')->name('plan.store');
        Route::delete('plan/delete/{id}', 'PaymentPlanController@destroy')->name('plan.destroy');
        Route::get('add/{id}', 'PaymentController@create')->name('plan.create');
        Route::post('store/{id}', 'PaymentController@store')->name('payment.store');
        Route::delete('delete/{id}/{user_id}', 'PaymentController@destroy')->name('payment.destroy');
        Route::get('monthly', 'PaymentController@index')->name('monthly.payment.index');
        Route::get('monthly-payments/add/{id}', 'PaymentController@create_monthly_payments');
        Route::post('monthly-payments/store/{id}', 'PaymentController@store_monthly_payments')->name('payment.monthly.store');
        Route::get('monthly-payments/edit/{id}', 'PaymentController@edit_monthly_payments')->name('payment.monthly.edit');
        Route::post('monthly-payments/update/{id}', 'PaymentController@update_payment')->name('payment.monthly.update');
        
        Route::delete('monthly-payments/delete/{id}', 'PaymentController@destroy_monthly_payments')->name('monthly.payment.destroy');
        Route::post('payment-paid','PaymentController@markPaymentAsPaid')->name('payment.paid');
        Route::post('payment-send-stripe-invoice','PaymentController@sendStripeInvoice')->name('payment.send.stripe.invoice');
        Route::post('payment-send-mutiple-stripe-invoice','PaymentController@sendMutlipleStripeInvoice')->name('payment.send.multiple.stripe.invoice');

        Route::get('payments', 'PaymentController@payments')->name('accounting.payments');
        Route::get('payments/records', 'PaymentController@paymentRecords')->name('accounting.payments.records');

        Route::group(['prefix' => 'manage-monthly-payments'], function () {
            Route::get('/{month_year?}', 'ManageMonthlyPaymentsController@index')->name('manage.monthly.payments.index');
            Route::get('/data/{month_year}', 'ManageMonthlyPaymentsController@data')->name('manage.monthly.payments.data');
            Route::post('/generate-records', 'ManageMonthlyPaymentsController@generatePaymentRecords')->name('manage.monthly.payments.generate.records');
        });
    });

    Route::group(['prefix' => 'contact'], function () {
        Route::get('list', 'ContactController@index')->name('contact.index');
        Route::post('store', 'ContactController@store')->name('contact.store');
        Route::delete('delete/{id}', 'ContactController@destroy')->name('contact.destroy');

        Route::get('form_data', 'ContactController@getFromData')->name('contact.form.data');
    });

    Route::group(['prefix' => 'schedule'], function () {
        Route::get('add/{type}', 'ScheduleController@create')->name('schedule.add');
        Route::post('store/{type}', 'ScheduleController@store')->name('schedule.store');
        Route::post('save', 'ScheduleController@save')->name('schedule.save');
        Route::get('monthly', 'ScheduleController@index')->name('schedule.monthly');
        Route::get('cal_data', 'ScheduleController@cal_data')->name('schedule.cal_data');
        Route::get('event_data', 'ScheduleController@event_data')->name('schedule.event_data');
        Route::get('calendar', 'ScheduleController@calendar')->name('schedule.calendar');
        Route::get('cal_data_1', 'ScheduleController@cal_data_1')->name('schedule.cal_data_1');
        Route::get('event_data_1', 'ScheduleController@event_data_1')->name('schedule.event_data_1');
        Route::get('calendar_data', 'ScheduleController@calendar_data');
        Route::get('details', 'ScheduleController@schedule_details');
        Route::get('details_students', 'ScheduleController@schedule_details_student_list');
        Route::get('reservation', 'ScheduleController@reservation');
        Route::get('reservation_by_teacher', 'ScheduleController@reservation_by_teacher');
        Route::get('list', 'ScheduleController@list')->name('schedule.list');
        Route::get('cancel_details', 'ScheduleController@schedule_cancel_details');
        Route::get('cancel_classes', 'ScheduleController@schedule_cancel_classes');
        Route::post('cancel_class', 'ScheduleController@schedule_cancel_class')->name('cancel.class');
        Route::get('waitlist', 'ScheduleController@waitlist');
        Route::get('waitlist_by_teacher', 'ScheduleController@waitlist_by_teacher');
        Route::get('waitlist_delete', 'ScheduleController@waitlist_delete')->name('waitlist.delete');
        Route::get('waitlisted_students', 'ScheduleController@waitlisted_students')->name('waitlisted_students');
        Route::get('student_row','ScheduleController@student_row')->name('schedule.student.row');
        Route::get('waitlist_student_row','ScheduleController@waitlist_student_row')->name('schedule.waitlist_student.row');
        Route::get('details_student', 'ScheduleController@schedule_details_student');
        Route::get('cancel_multiple_modal', 'ScheduleController@cancel_multiple_modal')->name('schedule.cancel_multiple_modal');
        Route::post('cancel_multiple', 'ScheduleController@cancel_multiple')->name('schedule.cancel_multiple');

        Route::get('edit', 'ScheduleController@getEditScheduleData')->name('schedule.edit.data');
        Route::post('update', 'ScheduleController@updateSchedule')->name('schedule.update');

        Route::post('off-day/add', 'ScheduleController@addSchoolOffDay')->name('offday.add');
        Route::post('off-day/delete', 'ScheduleController@deleteSchoolOffDay')->name('offday.delete');

        Route::post('zoom-meeting', 'ScheduleController@createZoomMeeting')->name('zoom.create.meeting');
        Route::delete('zoom-meeting/{zoom_meeting_id}', 'ScheduleController@deleteZoomMeeting')->name('zoom.delete.meeting');
        Route::post('zoom-meeting/send-reminder/{to}', 'ScheduleController@sendZoomMeetingReminder')->name('zoom.send-meeting-reminder');
        Route::post('zoom-meeting/sync/{zoom_meeting_id}', 'ScheduleController@syncZoomMeeting')->name('zoom.meeting.sync');

        Route::post('comment/add', 'ScheduleController@addComment')->name('comments.add');
        Route::post('file/add', 'ScheduleController@uploadFile')->name('files.upload');
        
        Route::get('class/details/{schedule_id}', 'ScheduleController@student_classs_details')->name('schedule.class.details');
    });

    Route::group(['prefix' => 'attendance'], function () {
        Route::get('add/yoyaku/{type}', 'YoyakuController@create')->name('yoyaku.create');
        Route::post('add/yoyaku/{type}', 'YoyakuController@store')->name('yoyaku.store');
        Route::post('add', 'AttendanceController@store')->name('attendance.store');
        Route::post('levelcheck', 'YoteiController@store')->name('yotei.store');
        Route::post('levelcheckfinished', 'YoteiController@update')->name('yotei.update');
        Route::get('cancel/{type?}', 'YoyakuController@cancel')->name('attendance.cancel');
        Route::post('undo', 'YoyakuController@undo_attendance')->name('attendance.undo');
    });

    Route::post('delete_reservation', 'YoyakuController@deleteReservation')->name('delete_reservation');

    Route::get('yoyaku/simple-cancel', 'YoyakuController@simple_cancel')->name('yoyaku.simple-cancel');

    Route::post('mail/send/{id}', 'MailController@send')->name('mail.send');
    Route::post('mail/send-test-email', 'MailController@sendTestEmail')->name('mail.send-test-email');

    Route::group(['prefix' => 'image'], function () {
        Route::post('upload/store/{id}','ImageUploadController@fileStore')->name('image.store');
        Route::post('delete/{id}','ImageUploadController@fileDestroy')->name('image.delete');
    });

    Route::get('school-settings', 'SettingController@school')->name('school-setting.edit');
    Route::post('school-settings-update', 'SettingController@update')->name('school-settings.update');
    
    Route::get('schedule-settings', 'SettingController@scheduleSettings')->name('schedule-settings.edit');
    Route::post('schedule-settings', 'SettingController@saveScheduleSettings')->name('schedule-settings.update');

    Route::get('payment-settings', 'SettingController@paymentSettings')->name('payment-settings.edit');
    Route::post('payment-settings', 'SettingController@savePaymentSettings')->name('payment-settings.update');

    Route::get('line-settings', 'SettingController@lineSettings')->name('line-settings.edit');
    Route::post('line-login-settings', 'SettingController@saveLineLoginSettings')->name('line-login-settings.save');
    Route::post('line-messaging-settings', 'SettingController@saveLineMessagingSettings')->name('line-messaging-settings.save');

    Route::get('notification-settings', 'SettingController@notificationSettings')->name('notification-settings.edit');
    Route::post('notification-status', 'SettingController@saveNotificationStatus')->name('notification-status.save');
    Route::post('notification-text', 'SettingController@saveNotificationText')->name('notification-text.save');

    Route::get('user-settings', 'SettingController@user');
    Route::post('user-settings-update', 'SettingController@userupdate')->name('user-settings.update');

    Route::get('lesson-settings', 'SettingController@lesson')->name('lesson-settings.edit');
    Route::post('lesson-settings-update', 'SettingController@lessonupdate')->name('lesson-settings.update');
   
    Route::resources([
        'users' => 'UserController',
        'roles' => 'RoleController',
        'class' => 'ClassController',
        'class-category' => 'ClassCategoryController',
        'event' => 'EventController',
        'teacher' => 'TeacherController',
        'student' => 'StudentController',
        'book' => 'BookController'
    ]);

    Route::post('teacher/archive', 'TeacherController@archiveTeacher')->name('teacher.archive');

    Route::group(['prefix' => 'todo'], function () {
        Route::get('', 'TodoController@index')->name('todo.index');
        Route::get('create', 'TodoController@create')->name('todo.create');
        Route::post('store', 'TodoController@store')->name('todo.store');
        Route::get('edit/{id}', 'TodoController@edit')->name('todo.edit');
        Route::post('update/{id}', 'TodoController@update')->name('todo.update');
        Route::post('destroy/{id}', 'TodoController@destroy')->name('todo.destroy');
        Route::get('mytodos', 'TodoController@mytodos')->name('mytodos');
        Route::get('details','TodoController@details')->name('todo.details');
        Route::get('progress_details/{id}','TodoController@progress_details')->name('todo.progress_details');
        Route::get('progress','TodoController@progress')->name('todo.progress');

        Route::post('update_task_status', 'TodoController@update_task_status')->name('todo.update_task_status');
        Route::post('update_task_note', 'TodoController@update_task_note')->name('todo.update_task_note');
        Route::post('update_duedate', 'TodoController@update_duedate')->name('todo.update_duedate');
    });

    Route::group(['prefix' => 'activity_logs'], function () {
        Route::get('', 'ActivityLogsController@index')->name('activity_logs.index');
        Route::get('data', 'ActivityLogsController@data')->name('activity_logs.data');
    });

    Route::group(['prefix' => 'tags'], function(){
        Route::get('', 'TagsController@index')->name('tags.index');
        Route::get('/records', 'TagsController@getTags')->name('tags.records');
        Route::post('', 'TagsController@saveTag')->name('tags.save');
        Route::delete('delete/{id}',  'TagsController@deleteTag')->name('tags.delete');
        Route::post('save_student_tags', 'TagsController@saveStudentTags')->name('tags.save_student_tags');

        Route::get('/settings', 'TagsController@getSettings')->name('tags.get_settings');
        Route::post('/settings', 'TagsController@saveSettings')->name('tags.save_settings');
    });

    Route::group(['prefix' => 'availability_selection_calendars'], function(){
        Route::get('', 'AvailabilitySelectionCalendarController@index')->name('availability_selection_calendars.index');
        Route::get('/records', 'AvailabilitySelectionCalendarController@getRecords')->name('availability_selection_calendars.records');
        
        Route::post('', 'AvailabilitySelectionCalendarController@saveRecord')->name('availability_selection_calendars.save');
        Route::delete('delete/{id}',  'AvailabilitySelectionCalendarController@deleteRecord')->name('availability_selection_calendars.delete');
        
        Route::get('/edit_calendar/{id}', 'AvailabilitySelectionCalendarController@editCalendar')->name('edit_calendar.index');
        Route::get('/get_calendar_data/{id}', 'AvailabilitySelectionCalendarController@getCalendarData')->name('edit_calendar.data');
        Route::post('/timeslot', 'AvailabilitySelectionCalendarController@saveTimeSlot')->name('edit_calender.save_timeslot');
        Route::delete('/timeslot/delete/{id}', 'AvailabilitySelectionCalendarController@deleteTimeSlot')->name('edit_calender.timeslot.delete');

        Route::get('/responses/{id}', 'AvailabilitySelectionCalendarController@responses')->name('availability_selection_calendars.responses');
        Route::get('/responses-initial-data/{id}', 'AvailabilitySelectionCalendarController@responseData')->name('availability_selection_calendars.responses.initialdata');
        Route::get('/responses-data/{id}', 'AvailabilitySelectionCalendarController@responseEvents')->name('availability_selection_calendars.responses.data');

        Route::get('/timeslot_picker/{assessment_question_id}', 'AvailabilitySelectionCalendarController@getTimeslotPickerData')->name('timeslotpicker.data');
    });

    Route::group(['prefix' => 'children'], function(){
        Route::get('', 'ChildrenController@index')->name('children.index');
    });

    Route::get('que-test','TerminalController@queTest');
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('logs');
});

Route::get('helper', 'HelperController@index');