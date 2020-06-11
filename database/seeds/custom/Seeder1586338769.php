<?php

namespace Database\Seeds\Custom;

use App\Activity;
use App\CancellationPolicies;
use App\CancelType;
use App\NotificationText;
use App\EmailTemplates;
use App\Helpers\ActivityEnum;
use App\Permission;
use App\Category;
use App\Role;
use App\Settings;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Seeder1586338769 extends Seeder
{
    public function run()
    {
        // Settings
        $settingsToSeed = [
            'limit_number_of_students_per_class' => '5',
            'number_of_days_use_payment_points' => '30',
            'use_points' => 'false',
            'use_monthly_payments' => 'true',
            'library_expected_checkin_days' => '7',
            'library_book_levels' => 'A1,A2,B1,B2,C1,A0,INT1,K1,K2,K3,INT2,INT3,INT4,INT5,INT6',
            'class_student_levels' => 'A1,A2,B1,A0,B2,C1,E1,E2,E3,E4,E5,E6,K1,K2,K3,Int1,Int2,Int3,Int4,Int5,Int6,JH1,JH2,E0,その他',
            'default_calendar_view' => 'agendaWeek',
            'default_calendar_color_coding' => 'blue',
            'default_signup_role' => 'student',
            'default_show_calendar' => '09:30;22:00',
            'working_days' => 'mon,tue,wed,thu,fri,sat,sun',
            'default_class_length' => '00:30',
            'school_name' => 'uTeach',
            'default_lang' => 'ja',
            'payment_categories' =>  '',
            'google_map_api_key' =>  '',
            'week_start_day' =>  'sun',
            'leftover_class_expiration_period' => '12',
            'student_reminder_email_time' =>  '00:00',
            'student_reminder_email_lesson_types' => '',
            'school_timezone' => 'Asia/Tokyo',
            'email_header_footer_color' => NULL,
            'email_header_image' => NULL,
            'email_header_text_size' => '20',
            'email_body_text_size' => '16',
            'email_header_text_en' => 'uTeach',
            'email_header_text_ja' => 'uTeach',
            'email_footer_text_en' => '© {year} uTeach. All rights reserved.',
            'email_footer_text_ja' => '©{year}年uTeach。全著作権所有。',
            'show_other_teachers_classes' => '0',
            'new_student_tag_attachment_duration_days' => 90,
            'smtp_settings' => NULL,
            'stripe_publishable_key' => NULL,
            'stripe_secret_key' => NULL,
            'stripe_webhook_signing_secret_key' => NULL,
            'payment_methods' => 'Stripe,Cash,Bank Transfter',
            'generate_payment_info_for_roles' => '',
            'use_stripe' => 0,
            'use_zoom' => 0,
            'zoom_api_key' => '',
            'zoom_secret_key' => '',
            'zoom_email_notification_to' => '',
            'zoom_email_notification_before' => 5,
            'zoom_webhook_verification_token' => NULL,
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }

        // Permissions

        $permissionsToSeed = array(
            'class-list',
            'class-show',
            'class-create',
            'class-edit',
            'class-delete',
            'contact-list',
            'contact-show',
            'contact-delete',
            'payment-list',
            'payment-create',
            'payment-delete',
            'payment-plan-list',
            'payment-plan-create',
            'payment-plan-delete',
            'schedule-list',
            'student-list',
            'student-create',
            'student-show',
            'student-edit',
            'student-delete',
            'teacher-list',
            'teacher-show',
            'teacher-create',
            'teacher-edit',
            'teacher-delete',
            'attendance-add',
            'Role-Management',
            'User-Management',
            'calendar',
            'reservation-list',
            'school-settings',
            'cancel-class',
            'user-settings',
            'course',
            'library',
            'test',
            'assessment',
            'class-category',
            'event',
            'calendar-hide-full-class',
            'schedule-details',
            'take-student-test',
            'take-assessment',
            'schedule-add',
            'email-settings',
            'student-impersonate',
            'student-map',
            'todo-mytodos',
            'todo-list',
            'todo-create',
            'todo-edit',
            'todo-delete',
            'student-search',
            'class-usage',
            'todo-progress',
            'activity-logs',
            'allow-reservation-on-past-classes',
            'children',
            'st-classes',
            'st-class-details',
            'st-assessments',
            'todo-progress-details',
            'dev-logs',
            'stats',
            'waitlisted-students',
            'control-attendance',
            'manage-tags',
            'view-student-tags',
            'edit-student-tags',
            'edit-schedule',
            'manage-availability-selection-calendars',
            'manage-availability-timeslots',
            'view-availability-responses',
            'payment-settings',
            'payment-mark-as-paid',
            'payment-edit',
            'st-payments',
            'manage-monthly-payments',
            'edit-assessment-response',
            'accounting-payments',
            'schedule-settings',
            'manage-school-off-days',
            'manage-zoom-meetings-for-class',
        );
        foreach($permissionsToSeed as $permission) {
            Permission::create([ 'name' => $permission ]);
        }

        
        // Roles
        $rolesToSeed = [
            [
                'name' => 'student',
                'login_redirect_path' => '/schedule/calendar',
                'is_student' => 1,
                'send_login_details' => 1,
                'can_login' => 1
            ],
            [
                'name' => 'Teacher',
                'login_redirect_path' => '/',
                'is_student' => 0,
                'send_login_details' => 1,
                'can_login' => 1
            ],
            [
                'name' => 'Super Admin',
                'login_redirect_path' => '/',
                'is_student' => 0,
                'send_login_details' => 1,
                'can_login' => 1
            ],
            [
                'name' => 'librarian',
                'login_redirect_path' => '/',
                'is_student' => 0,
                'send_login_details' => 1
            ],
            [
                'name' => 'admin',
                'login_redirect_path' => '/',
                'is_student' => 0,
                'send_login_details' => 1,
                'can_login' => 1
            ],
            [
                'name' => 'New Contact',
                'login_redirect_path' => '/schedule/calendar',
                'is_student' => 1,
                'send_login_details' => 0,
                'can_login' => 1
            ],
            [
                'name' => 'Private Student',
                'login_redirect_path' => '/schedule/calendar',
                'is_student' => 1,
                'send_login_details' => 1,
                'can_login' => 1
            ],
            [
                'name' => 'parent',
                'login_redirect_path' => '/children',
                'is_student' => 0,
                'send_login_details' => 1,
                'can_login' => 1
            ],
            [
                'name' => Role::ARCHIVED_STUDENT,
                'login_redirect_path' => '/',
                'is_student' => 1,
                'send_login_details' => 0,
                'can_login' => 1
            ],
            [
                'name' => Role::ARCHIVED_TEACHER,
                'login_redirect_path' => '/',
                'is_student' => 0,
                'send_login_details' => 0,
                'can_login' => 1
            ],
        ];
        foreach($rolesToSeed as $record) {
            $role = new Role();
            $role->name = $record['name'];
            $role->login_redirect_path = $record['login_redirect_path'];
            $role->is_student = $record['is_student'];
            $role->send_login_details = $record['send_login_details'];
            $role->save();
        }

        // Role Has Permissions
        $roleHasPermissionsToSeed = array(
            array('role' => 'student','permission' => 'calendar'),
            array('role' => 'student','permission' => 'reservation-list'),
            array('role' => 'student','permission' => 'user-settings'),
            array('role' => 'student','permission' => 'take-assessment'),
            array('role' => 'student','permission' => 'class-usage'),
            array('role' => 'student','permission' => 'st-classes'),
            array('role' => 'student','permission' => 'st-class-details'),
            array('role' => 'Teacher','permission' => 'schedule-list'),
            array('role' => 'Teacher','permission' => 'attendance-add'),
            array('role' => 'Teacher','permission' => 'course'),
            array('role' => 'Teacher','permission' => 'assessment'),
            array('role' => 'Teacher','permission' => 'schedule-details'),
            array('role' => 'Teacher','permission' => 'take-assessment'),
            array('role' => 'Teacher','permission' => 'view-student-tags'),
            array('role' => 'Teacher','permission' => 'edit-student-tags'),
            array('role' => 'Super Admin','permission' => 'class-list'),
            array('role' => 'Super Admin','permission' => 'class-show'),
            array('role' => 'Super Admin','permission' => 'class-create'),
            array('role' => 'Super Admin','permission' => 'class-edit'),
            array('role' => 'Super Admin','permission' => 'class-delete'),
            array('role' => 'Super Admin','permission' => 'contact-list'),
            array('role' => 'Super Admin','permission' => 'contact-show'),
            array('role' => 'Super Admin','permission' => 'contact-delete'),
            array('role' => 'Super Admin','permission' => 'payment-list'),
            array('role' => 'Super Admin','permission' => 'payment-create'),
            array('role' => 'Super Admin','permission' => 'payment-delete'),
            array('role' => 'Super Admin','permission' => 'payment-plan-list'),
            array('role' => 'Super Admin','permission' => 'payment-plan-create'),
            array('role' => 'Super Admin','permission' => 'payment-plan-delete'),
            array('role' => 'Super Admin','permission' => 'schedule-list'),
            array('role' => 'Super Admin','permission' => 'student-list'),
            array('role' => 'Super Admin','permission' => 'student-create'),
            array('role' => 'Super Admin','permission' => 'student-show'),
            array('role' => 'Super Admin','permission' => 'student-edit'),
            array('role' => 'Super Admin','permission' => 'student-delete'),
            array('role' => 'Super Admin','permission' => 'teacher-list'),
            array('role' => 'Super Admin','permission' => 'teacher-show'),
            array('role' => 'Super Admin','permission' => 'teacher-create'),
            array('role' => 'Super Admin','permission' => 'teacher-edit'),
            array('role' => 'Super Admin','permission' => 'teacher-delete'),
            array('role' => 'Super Admin','permission' => 'attendance-add'),
            array('role' => 'Super Admin','permission' => 'Role-Management'),
            array('role' => 'Super Admin','permission' => 'User-Management'),
            array('role' => 'Super Admin','permission' => 'school-settings'),
            array('role' => 'Super Admin','permission' => 'cancel-class'),
            array('role' => 'Super Admin','permission' => 'user-settings'),
            array('role' => 'Super Admin','permission' => 'course'),
            array('role' => 'Super Admin','permission' => 'library'),
            array('role' => 'Super Admin','permission' => 'test'),
            array('role' => 'Super Admin','permission' => 'assessment'),
            array('role' => 'Super Admin','permission' => 'class-category'),
            array('role' => 'Super Admin','permission' => 'event'),
            array('role' => 'Super Admin','permission' => 'schedule-details'),
            array('role' => 'Super Admin','permission' => 'schedule-add'),
            array('role' => 'Super Admin','permission' => 'email-settings'),
            array('role' => 'Super Admin','permission' => 'student-impersonate'),
            array('role' => 'Super Admin','permission' => 'student-map'),
            array('role' => 'Super Admin','permission' => 'todo-mytodos'),
            array('role' => 'Super Admin','permission' => 'todo-list'),
            array('role' => 'Super Admin','permission' => 'todo-create'),
            array('role' => 'Super Admin','permission' => 'todo-edit'),
            array('role' => 'Super Admin','permission' => 'todo-delete'),
            array('role' => 'Super Admin','permission' => 'student-search'),
            array('role' => 'Super Admin','permission' => 'todo-progress'),
            array('role' => 'Super Admin','permission' => 'activity-logs'),
            array('role' => 'Super Admin','permission' => 'allow-reservation-on-past-classes'),
            array('role' => 'Super Admin','permission' => 'todo-progress-details'),
            array('role' => 'Super Admin','permission' => 'dev-logs'),
            array('role' => 'Super Admin','permission' => 'stats'),
            array('role' => 'Super Admin','permission' => 'waitlisted-students'),
            array('role' => 'Super Admin','permission' => 'control-attendance'),
            array('role' => 'Super Admin','permission' => 'manage-tags'),
            array('role' => 'Super Admin','permission' => 'view-student-tags'),
            array('role' => 'Super Admin','permission' => 'edit-student-tags'),
            array('role' => 'Super Admin','permission' => 'edit-schedule'),
            array('role' => 'Super Admin','permission' => 'manage-availability-selection-calendars'),
            array('role' => 'Super Admin','permission' => 'manage-availability-timeslots'),
            array('role' => 'Super Admin','permission' => 'view-availability-responses'),
            array('role' => 'Super Admin','permission' => 'payment-settings'),
            array('role' => 'Super Admin','permission' => 'payment-mark-as-paid'),
            array('role' => 'Super Admin','permission' => 'payment-edit'),
            array('role' => 'Super Admin','permission' => 'manage-monthly-payments'),
            array('role' => 'Super Admin','permission' => 'edit-assessment-response'),
            array('role' => 'Super Admin','permission' => 'accounting-payments'),
            array('role' => 'Super Admin','permission' => 'schedule-settings'),
            array('role' => 'Super Admin','permission' => 'manage-zoom-meetings-for-class'),
            array('role' => 'admin','permission' => 'class-list'),
            array('role' => 'admin','permission' => 'class-show'),
            array('role' => 'admin','permission' => 'class-create'),
            array('role' => 'admin','permission' => 'class-edit'),
            array('role' => 'admin','permission' => 'class-delete'),
            array('role' => 'admin','permission' => 'contact-list'),
            array('role' => 'admin','permission' => 'contact-show'),
            array('role' => 'admin','permission' => 'contact-delete'),
            array('role' => 'admin','permission' => 'payment-list'),
            array('role' => 'admin','permission' => 'payment-create'),
            array('role' => 'admin','permission' => 'payment-delete'),
            array('role' => 'admin','permission' => 'payment-plan-list'),
            array('role' => 'admin','permission' => 'payment-plan-create'),
            array('role' => 'admin','permission' => 'payment-plan-delete'),
            array('role' => 'admin','permission' => 'schedule-list'),
            array('role' => 'admin','permission' => 'student-list'),
            array('role' => 'admin','permission' => 'student-create'),
            array('role' => 'admin','permission' => 'student-show'),
            array('role' => 'admin','permission' => 'student-edit'),
            array('role' => 'admin','permission' => 'student-delete'),
            array('role' => 'admin','permission' => 'teacher-list'),
            array('role' => 'admin','permission' => 'teacher-show'),
            array('role' => 'admin','permission' => 'teacher-create'),
            array('role' => 'admin','permission' => 'teacher-edit'),
            array('role' => 'admin','permission' => 'teacher-delete'),
            array('role' => 'admin','permission' => 'attendance-add'),
            array('role' => 'admin','permission' => 'reservation-list'),
            array('role' => 'admin','permission' => 'school-settings'),
            array('role' => 'admin','permission' => 'cancel-class'),
            array('role' => 'admin','permission' => 'user-settings'),
            array('role' => 'New Contact','permission' => 'calendar'),
            array('role' => 'New Contact','permission' => 'calendar-hide-full-class'),
            array('role' => 'Private Student','permission' => 'calendar'),
            array('role' => 'Private Student','permission' => 'calendar-hide-full-class'),
            array('role' => 'parent','permission' => 'student-impersonate'),
            array('role' => 'parent','permission' => 'children'),
        );
        foreach($roleHasPermissionsToSeed as $record) {
            $role = Role::where('name', $record['role'])->first();
            $role->givePermissionTo($record['permission']);
        }

        // User & their role
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@gmail.com';
        $user->username = 'admin';
        $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->password = Hash::make('admin');
        $user->lang = 'en';
        $user->receive_emails = 1;
        $user->calendar_view = 'agendaWeek';
        $user->stripe_customer_id = NULL;
        $user->save();
        $user->assignRole('Super Admin');

        // Cancel Types
        $cancelTypesToSeed = [
            [1, 'Cancellation 24 hours before ', 'cancel'],
            [2, 'Cancellation 1 hour before', 'partial-penalty-cancel'],
            [3, 'Cancellation without previous warning', 'full-penalty-cancel']
        ];
        foreach($cancelTypesToSeed as $record){
            $cancelType = new CancelType();
            $cancelType->id = $record[0];
            $cancelType->name = $record[1];
            $cancelType->alias = $record[2];
            $cancelType->save();
        }

        // Cancellation Policies
        $cancellationPoliciesToSeed = [
            [1, 1, 0, 0.00, 500],
            [2, 2, 0, 2.50, 700],
            [3, 3, 0, 5.00, 1000],
            [5, 1, 19, 0.00, 500],
            [6, 2, 19, 7.00, 700],
            [7, 3, 19, 15.00, 1000],
            [8, 1, 20, 0.00, 0],
            [9, 2, 20, 15.00, 500],
            [10, 3, 20, 30.00, 1000]
        ];
        foreach($cancellationPoliciesToSeed as $record){
            $cancellationPolicy = new CancellationPolicies();
            $cancellationPolicy->id = $record[0];
            $cancellationPolicy->cancel_type_id = $record[1];
            $cancellationPolicy->payment_plan_id = $record[2];
            $cancellationPolicy->points = $record[3];
            $cancellationPolicy->salary = $record[4];
            $cancellationPolicy->save();
        }

        // Email Templates
        $emailTemplatesToSeed = array(
            [   'name' => 'register_class_notification_repeat',
                'subject_en' => 'You are registered to the class {class_name}!',
                'content_en' => 'Hi {student_name},
                                <br><br>
                                You are registered to the class {class_name}.
                                {class_name} will be opened on every {class_weekday}, at {time}.
                                The reminder email will be sent to you a day before class opening day.
                                <br><br>
                                Regards,
                                <br>
                                uTeach Admin',
                'subject_ja' => 'あなたはクラスに登録されています {class_name}!',
                'content_ja' => 'もしもし {student_name},
                                <br><br>
                                あなたはクラスに登録されています {class_name}.
                                {class_name} 上に開かれます {date}, で {time}.
                                リマインダーEメールは、クラスの開講日の前日に送信されます。
                                <br><br>
                                よろしく,
                                <br>
                                uTeach 管理者',
                'enable' => '0'
            ],
            [   'name' => 'cancel_reservation_notify_waitlist',
                'subject_en' => 'There is a student canceling the reservation {class_name} class in {date}!',
                'content_en' => 'Hi {student_name},
                                <br><br>
                                There is a student canceling the reservation {class_name} class in {date}!
                                Please check again and reserve if you can.
                                <br><br>
                                Regards,
                                <br>
                                uTeach Admin',
                'subject_ja' => '{date}の予約{class_name}クラスをキャンセルしている生徒がいます。',
                'content_ja' => 'もしもし {student_name},
                                <br><br>
                                {date}の予約{class_name}クラスをキャンセルしている生徒がいます。
                                もう一度確認し、可能であれば予約してください。
                                <br><br>
                                よろしく,
                                <br>
                                uTeach 管理者',
                'enable' => '0'
            ],
            [   'name' => 'new_user_notification',
                'subject_en' => 'Welcome to uTeach',
                'content_en' => 'You are registered to uTeach.
                                <br><br>
                                Your credentials:
                                <br>
                                username: {username}
                                <br>
                                password: {password}
                                <br><br>
                                After login, please click on this link below for verification:
                                <br>
                                {verification_url}
                                <br><br>
                                Regards,
                                <br>
                                uTeach Admin',
                'subject_ja' => 'uTeachへようこそ',
                'content_ja' => 'あなたはuTeachに登録されています。
                                <br><br>
                                あなたの資格情報：
                                <br>
                                ユーザー名：{username}
                                <br>
                                パスワード：{password}
                                <br><br>
                                ログイン後、確認のために下のこのリンクをクリックしてください。
                                <br>
                                {verification_url}
                                <br><br>
                                よろしく,
                                <br>
                                uTeach 管理者',
                'enable' => '0'
            ],
            [   'name' => 'test_notification',
                'subject_en' => 'Please take a test before continue!',
                'content_en' => 'Hi {student_name},
                                <br><br>
                                You\'ve already finished lesson {lesson}, unit {unit}, course {course} on {date}.
                                <br><br>
                                Before continue, you will need to take a test {test} on {test_url},
                                <br><br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => '続行する前にテストを受けてください。',
                'content_ja' => 'こんにちは{student_name}、
                                <br><br>
                                {date}のレッスン{lesson}、ユニット{unit}、コース{course}はすでに終了しています。
                                <br><br>
                                続行する前に、{test_url}のテスト{test}を受ける必要があります。
                                <br><br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => '0'
            ],
            [   'name' => 'automatic_assessment_notification',
                'subject_en' => 'Please take a assessment before continue!',
                'content_en' => 'Hi {user_name},
                                <br><br>
                                You\'ve already finished lesson {lesson}, unit {unit}, course {course} on {date}.
                                <br><br>
                                Before continue, you will need to take a assessment {assessment} on {assessment_url},
                                <br><br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => '続行する前に評価を受けてください',
                'content_ja' => 'こんにちは{user_name}、
                                <br><br>
                                {date}のレッスン{lesson}、ユニット{unit}、コース{course}はすでに終了しています。
                                <br><br>
                                続行する前に、{assessment_url}のテスト{assessment}を受ける必要があります。
                                <br><br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => '0'
            ],
            [   'name' => 'manual_assessment_notification',
                'subject_en' => 'Please take a assessment before continue!',
                'content_en' => 'Hi {user_name},
                                <br><br>
                                You will need to take a assessment {assessment} on {assessment_url},
                                <br><br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => '続行する前に評価を受けてください',
                'content_ja' => 'こんにちは{user_name}、
                                <br><br>
                                {assessment_url}のテスト{assessment}を受ける必要があります。
                                <br><br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => '0'
            ],
            [   'name' => 'paper_test_notification',
                'subject_en' => 'Please add paper tests before continue!',
                'content_en' => 'Hi {teacher_name},
                                <br><br>
                                You\'ve already finished lesson {lesson}, unit {unit}, course {course} on {date}.
                                <br><br>
                                Before continue, you will need to add paper tests {test} on {test_url},
                                <br><br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => '続行する前にテストを受けてください。',
                'content_ja' => 'こんにちは{teacher_name}、
                                <br><br>
                                {date}のレッスン{lesson}、ユニット{unit}、コース{course}はすでに終了しています。
                                <br><br>
                                続行する前に、{test_url}のテスト{test}を受ける必要があります。
                                <br><br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => '0'
            ],
            [   'name' => 'checkin_notification',
                'subject_en' => 'Checked in Successfully',
                'content_en' => 'Hi {student_name},
                                <br><br>
                                You have successfully checked in at {date_time}
                                <br><br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => '正常にチェックインしました',
                'content_ja' => 'こんにちは{student_name}、
                                <br><br>
                                に正常にチェックインしました {date_time}
                                <br><br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => 0
            ],
            [   'name' => 'checkout_notification',
                'subject_en' => 'Checked out Successfully',
                'content_en' => 'Hi {student_name},
                                <br><br>
                                You have successfully checked out at {date_time}
                                <br><br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => '正常にチェックアウトしました',
                'content_ja' => 'こんにちは{student_name}、
                                <br><br>
                                で正常にチェックアウトしました {date_time}
                                <br><br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => 0
            ],
            [   'name' => 'daily_reservation_reminder',
                'subject_en' => 'Your reservations of the day',
                'content_en' => 'Hi {student_name},
                                <br><br>
                                Here is your reservations of the day {date}
                                {reserved_classes_section}
                                {reserved_events_section}
                                <br>
                                You may reserve more lessons by signing in via below button
                                <br>
                                {signin_btn}
                                <br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => 'その日の予約',
                'content_ja' => 'こんにちは{student_name},
                                <br><br>
                                こちらがその日の予約です {date}
                                {reserved_classes_section}
                                {reserved_events_section}
                                <br>
                                下のボタンからサインインすることで、さらにレッスンを予約できます
                                <br>
                                {signin_btn}
                                <br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => 0
            ],
            [   'name' => 'assessment_result_available_notification',
                'subject_en' => 'Assessment Result is Available',
                'content_en' => 'Hi {student_name},
                                <br><br>
                                Your Assessment result for class {class_name} is available.<br>
                                You may look at it from asessments page or click this link {view_assessment_url} to view assessment details.<br>
                                <br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => '評価結果が利用可能です',
                'content_ja' => 'こんにちは{student_name},
                                <br><br>
                                クラス{class_name}の評価結果が利用可能です。 <br>
                                評価のページで確認するか、このリンク{view_assessment_url}をクリックして評価の詳細を表示できます。<br>
                                下のボタンからサインインすることで、さらにレッスンを予約できます.<br>
                                <br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => 0
            ],
            [   'name' => 'password_reset_link_notification',
                'subject_en' => 'Reset Your Password',
                'content_en' => 'Hi {user_name},
                                <br><br>
                                Here is the link to rest your password {reset_password_link}
                                <br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => 'あなたのパスワードをリセット',
                'content_ja' => 'こんにちは{user_name},
                                <br><br>
                                パスワードをリセットするためのリンクはこちら {reset_password_link}
                                <br>
                                よろしく、<br>
                                uTeach管理者',
                'enable' => 0
            ],
            [   'name' => 'verify_email_notification',
                'subject_en' => 'Verify your email',
                'content_en' => 'Hi {username},
                                <br><br>
                                Please click the link below to verify your email address. <br>
                                {verify_email_link}<br>
                                <br>
                                If you did not create an account, no further action is required.
                                <br>
                                <br>
                                Regards,<br>
                                uTeach Admin',
                'subject_ja' => 'あなたの電子メールを確認します',
                'content_ja' => 'こんにちは{username},
                                <br><br>
                                以下のリンクをクリックして、メールアドレスを確認してください. <br>
                                {verify_email_link}<br>
                                <br>
                                アカウントを作成しなかった場合、それ以上のアクションは不要です.
                                <br>
                                <br>
                                よろしく、<br>
                uTeach管理者',
                'enable' => 0
            ],
            [   'name' => 'cancel_class_notification',
                'subject_en' => 'Your reservation for class {class_name} is cancelled',
                'content_en' => 'Dear {student_name},
                                <br><br>
                                This is an automated email.
                                <br><Br>
                                Your reservation for class {class_name} on following date(s) at {time} is cancelled. <br>
                                Cancellled on date(s): {date}
                                <br><br>
                                Have a great day!
                                <br>
                                - BCE Staff',
                'subject_ja' => 'クラス{class_name}の予約はキャンセルされました',
                'content_ja' => '{student_name}様,,
                                <br><br>
                                これは自動メールです。
                                <br><Br>
                                次の日付{time}のクラス{class_name}の予約はキャンセルされました。<br>
                                キャンセルされた日付：{date}
                                <br><br>
                                よろしくお願いします。
                                <br>
                                ベンちゃん英会話　スタッフ',
                'enable' => 0
            ],
            [   'name' => 'waitlist_class_notification',
                'subject_en' => 'You are added to waitlist of the class {class_name}!',
                'content_en' => 'Dear {student_name},
                                <br><br>
                                This is an automated email.
                                <br><Br>
                                You are added to waitlist of the class {class_name}.<br>
                                {class_name} will be held on {date} at {time}.
                                <br><br>
                                You will receive an email notification if reservations for this class opens up again.
                                <br><br>
                                Have a great day!
                                <br>
                                - BCE Staff',
                'subject_ja' => 'クラス{class_name}の待機リストに追加されました！',
                'content_ja' => '{student_name}様,
                                <br><br>
                                これは自動メールです。
                                <br><Br>
                                クラス{class_name}の待機リストに追加されます。<br>
                                {class_name}は{date}の{time}に開催されます。
                                <br><br>
                                このクラスの予約が再び開かれると、メール通知が届きます。
                                <br><br>
                                よろしくお願いします。
                                <br>
                                ベンちゃん英会話　スタッフ',
                'enable' => 0
            ],
            [   'name' => 'register_class_notification_to_teacher',
                'subject_en' => 'Student has joined your class {class_name}!',
                'content_en' => 'Dear {teacher_name},
                                <br><br>
                                This is an automated email.
                                <br><Br>
                                {student_name} has joined your class {class_name} held on {date} at {time}.
                                <br><br>
                                Have a great day!
                                <br>
                                - BCE Staff',
                'subject_ja' => '学生があなたのクラス{class_name}に参加しました！',
                'content_ja' => '{teacher_name}様,
                                <br><br>
                                これは自動メールです。
                                <br><Br>
                                {student_name}が{date}の{time}に開催されたクラス{class_name}に参加しました。
                                <br><br>
                                よろしくお願いします。
                                <br>
                                ベンちゃん英会話　スタッフ',
                'enable' => 0
            ],
            [   'name' => 'zoom_meeting_reminder_for_class',
                'subject_en' => 'Join zoom meeting for class {class_name}!',
                'content_en' => 'Hi {username},
                                <br><br>
                                This is an automated email.
                                <br><Br>
                                Please join zoom meeting on {date} at {time} for class {class_name} by clicking the link below. <br>
                                <a href="{zoom_meeting_url}" target="_blank">{zoom_meeting_url}</a>
                                <br><br>
                                Meeting ID: {zoom_meeting_id} <br>
                                Password: {zoom_meeting_password}
                                <br><br>
                                Have a great day!
                                <br>
                                - BCE Staff',
                'subject_ja' => 'クラス{class_name}のズーム会議に参加してください！',
                'content_ja' => 'こんにちは{username},
                                <br><br>
                                これは自動メールです。
                                <br><Br>
                                以下のリンクをクリックして、{date}の{time}にクラス{class_name}のズーム会議に参加してください。<br>
                                <a href="{zoom_meeting_url}" target="_blank">{zoom_meeting_url}</a>
                                <br><br>
                                会議ID: {zoom_meeting_id} <br>
                                パスワード: {zoom_meeting_password}
                                <br><br>
                                よろしくお願いします。
                                <br>
                                ベンちゃん英会話　スタッフ',
                'enable' => 0
            ]
        );
        
        foreach($emailTemplatesToSeed as $record) {
            $emailTemplate = new EmailTemplates();
            $emailTemplate->name = $record['name'];
            $emailTemplate->subject_en = $record['subject_en'];
            $emailTemplate->content_en = $this->formatMultilineWhiteSpace($record['content_en']);
            $emailTemplate->subject_ja = $record['subject_ja'];
            $emailTemplate->content_ja = $this->formatMultilineWhiteSpace($record['content_ja']);
            $emailTemplate->enable = $record['enable'];
            $emailTemplate->save();
        }

        // Email Template button texts
        $emailTemplateButtonTextsToSeed = [
            [
                'email_template_name' => 'cancel_reservation_notify_waitlist',
                'key' => 'reserve',
                'text_en' => 'Reserve',
                'text_ja' => '予約する'
            ],
            [
                'email_template_name' => 'cancel_reservation_notify_waitlist',
                'key' => 'cancel-waitlist',
                'text_en' => 'Sorry, i can\'t make it',
                'text_ja' => 'すみません、できません'
            ],

            [
                'email_template_name' => 'daily_reservation_reminder',
                'key' => 'login',
                'text_en' => 'Login',
                'text_ja' => 'ログイン'
            ],
            [
                'email_template_name' => 'daily_reservation_reminder',
                'key' => 'cancel-class-reservation',
                'text_en' => 'Cancel',
                'text_ja' => 'キャンセル'
            ],
            [
                'email_template_name' => 'daily_reservation_reminder',
                'key' => 'cancel-event-reservation',
                'text_en' => 'Cancel',
                'text_ja' => 'キャンセル'
            ]
        ];

        foreach($emailTemplateButtonTextsToSeed as $record) {
            $notificationText = new NotificationText();
            $notificationText->email_template_id = EmailTemplates::where('name',$record['email_template_name'])->first()->id;
            $notificationText->type = NotificationText::TYPE_BUTTON_TEXT;
            $notificationText->key = $record['key'];
            $notificationText->text_en = $record['text_en'];
            $notificationText->text_ja = $record['text_ja'];
            $notificationText->save();
        }

        // Activity
        $activityToSeed = [
            [
                'id' => ActivityEnum::RESERVERATION_CANCELLED_VIA_EMAIL,
                'name' => 'reservation-cancelled-via-email'
            ],
            [
                'id' => ActivityEnum::USER_LOGGEDIN,
                'name' => 'user-loggedin'
            ],
            [
                'id' => ActivityEnum::CONTACT_CREATED,
                'name' => 'contact-created'
            ],
            [
                'id' => ActivityEnum::RESERVATION_MADE,
                'name' => 'reservation-made'
            ],
            [
                'id' => ActivityEnum::RESERVATION_CANCELLED,
                'name' => 'reservation-cancelled'
            ],
            [
                'id' => ActivityEnum::PAYMENT_CREATED,
                'name' => 'payment-created'
            ],
            [
                'id' => ActivityEnum::TODO_CREATED,
                'name' => 'todo-created'
            ],
            [
                'id' => ActivityEnum::TODO_COMPLETED,
                'name' => 'todo-completed'
            ],
            [
                'id' => ActivityEnum::CLASS_SCHEDULED,
                'name' => 'class-scheduled'
            ],
            [
                'id' => ActivityEnum::CLASS_CANCELLED,
                'name' => 'class-cancelled'
            ],
            [
                'id' => ActivityEnum::RESERVATION_DELETED,
                'name' => 'reservation-deleted'
            ],
            [
                'id' => ActivityEnum::PAYMENT_UPDATED,
                'name' => 'payment-updated'
            ],
            [
                'id' => ActivityEnum::PAYMENT_DELETED,
                'name' => 'payment-deleted'
            ]
        ];

        foreach($activityToSeed as $record) {
            $activity = new Activity();
            $activity->id = $record['id'];
            $activity->name = $record['name'];
            $activity->save();
        }

        // Tags
        $tagsToSeed = [
            [
                'name' => Tag::UPCOMMING_BIRTHDAY,
                'color' => "#ED960F",
                'icon' => "fa-birthday-cake",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::NEW_STUDENT,
                'color' => "#2A6F08",
                'icon' => "fa-star",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::DUE_TODO,
                'color' => "#670873",
                'icon' => "fa-list",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::LONG_TIME_STUDENT_1,
                'color' => "#FFDC00",
                'icon' => "fa-star-o",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::LONG_TIME_STUDENT_2,
                'color' => "#FFDC00",
                'icon' => "fa-star-o",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::LONG_TIME_STUDENT_3,
                'color' => "#FFDC00",
                'icon' => "fa-star-o",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::LONG_TIME_STUDENT_4,
                'color' => "#FFDC00",
                'icon' => "fa-star-o",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::LONG_TIME_STUDENT_5,
                'color' => "#FFDC00",
                'icon' => "fa-star-o",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::OUTSTANDING_PAYMENT,
                'color' => "#FFDC00",
                'icon' => "fa-usd",
                'is_automated' => 1,
            ],
            [
                'name' => Tag::RFID_REGISTERED,
                'color' => "#076351",
                'icon' => "fa-address-card-o",
                'is_automated' => 1,
            ],
        ];

        foreach ($tagsToSeed as $record) {
            $tag = new Tag();
            $tag->name = $record['name'];
            $tag->color = $record['color'];
            $tag->icon = $record['icon'];
            $tag->is_automated = $record['is_automated'];
            $tag->save();
        }
    }

    public function formatMultilineWhiteSpace($content)
    {
        $new_line_delimeter = "\n";
        $new_content = '';
        $lines = explode($new_line_delimeter, $content);
        foreach($lines as $line) {
            $new_content .= trim($line) . $new_line_delimeter;
        }
        return trim($new_content);
    }

}