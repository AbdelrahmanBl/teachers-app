<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'MainAuth'], function () { 
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
Route::group(['middleware' => 'StudentAuth'], function () {
	Route::get('/student/get_exam','Api\studentController@get_exam');
	Route::get('/student/get_notifications','Api\studentController@get_notifications');
	// Route::get('/student/get_notifications_count','Api\studentController@get_notifications_count');

Route::group(['middleware' => 'StudentAuthInExam'], function () {
	Route::get('/student/get_profile','Api\studentController@get_profile');
	Route::get('/student/get_published','Api\studentController@get_published');
	Route::get('/student/get_exams_mark','Api\studentController@get_exams_mark');
	Route::get('/student/get_exam_mark','Api\studentController@get_exam_mark');
	Route::get('/student/get_messages','Api\studentController@get_messages');
	Route::get('/student/get_teachers_unsubscribed','Api\studentController@get_teachers_unsubscribed');
	Route::get('/student/get_appointments','Api\studentController@get_appointments');
});
	/*------------------------------------------------------------*/
	Route::post('/student/start_exam','Api\studentController@start_exam');
	Route::post('/student/end_solve_exam','Api\studentController@end_solve_exam');
	Route::post('/student/change_password','Api\studentController@change_password');
	Route::post('/student/update_profile','Api\studentController@update_profile');
	Route::post('/student/update_profile_image','Api\studentController@update_profile_image');
	Route::post('/student/subscribe','Api\studentController@subscribe');
});
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
Route::group(['middleware' => 'TeacherAuth'], function () {
	Route::get('/teacher/get_profile','Api\teacherController@get_profile');
	Route::get('/teacher/get_package','Api\teacherController@get_package');
	Route::get('/teacher/get_appointments','Api\teacherController@get_appointments');
	Route::get('/teacher/get_filter_appointments','Api\teacherController@get_filter_appointments');
	Route::get('/teacher/get_students','Api\teacherController@get_students');
	Route::get('/teacher/get_registers','Api\teacherController@get_registers');
	Route::get('/teacher/get_students_for_publish','Api\teacherController@get_students_for_publish');
	Route::get('/teacher/get_exam','Api\teacherController@get_exam');
	Route::get('/teacher/get_exam_mark','Api\teacherController@get_exam_mark');
	Route::get('/teacher/get_exams','Api\teacherController@get_exams');
	Route::get('/teacher/get_exams_mark','Api\teacherController@get_exams_mark');
	Route::get('/teacher/get_messages','Api\teacherController@get_messages');
	Route::get('/teacher/get_student_exams','Api\teacherController@get_student_exams');
	/*------------------------------------------------------------*/
	Route::post('/teacher/change_accept_register','Api\teacherController@change_accept_register');
	Route::post('/teacher/change_password','Api\teacherController@change_password');
	Route::post('/teacher/update_profile','Api\teacherController@update_profile');
	Route::post('/teacher/update_profile_image','Api\teacherController@update_profile_image');
	Route::post('/teacher/accept_students','Api\teacherController@accept_students');
	Route::post('/teacher/change_student_appointment','Api\teacherController@change_student_appointment');
	Route::post('/teacher/delete_register','Api\teacherController@delete_register');
	Route::post('/teacher/close_student','Api\teacherController@close_student');
	Route::post('/teacher/add_exam','Api\teacherController@add_exam');
	Route::post('/teacher/copy_exam','Api\teacherController@copy_exam');
	Route::post('/teacher/merge_exams','Api\teacherController@merge_exams');
	Route::post('/teacher/publish_exam','Api\teacherController@publish_exam');
	Route::post('/teacher/update_exam','Api\teacherController@update_exam');
	Route::post('/teacher/delete_exam','Api\teacherController@delete_exam');
	Route::post('/teacher/close_exam','Api\teacherController@close_exam');
	Route::post('/teacher/add_mcq_question','Api\teacherController@add_mcq_question');
	Route::post('/teacher/add_write_question','Api\teacherController@add_write_question');
	Route::post('/teacher/update_mcq_question','Api\teacherController@update_mcq_question');
	Route::post('/teacher/update_write_question','Api\teacherController@update_write_question');
	// Route::post('/teacher/update_question_image','Api\teacherController@update_question_image');
	Route::post('/teacher/upload_image_url','Api\teacherController@upload_image_url');
	Route::post('/teacher/delete_question','Api\teacherController@delete_question');
	
	Route::post('/teacher/add_appointment','Api\teacherController@add_appointment');
	Route::post('/teacher/update_appointment','Api\teacherController@update_appointment');
	Route::post('/teacher/change_appointment_status','Api\teacherController@change_appointment_status');
	Route::post('/teacher/mark_exam','Api\teacherController@mark_exam');
	Route::post('/teacher/send_exam_degree','Api\teacherController@send_exam_degree');
	Route::post('/teacher/send_message','Api\teacherController@send_message');
	Route::post('/teacher/update_message','Api\teacherController@update_message');
	Route::post('/teacher/delete_message','Api\teacherController@delete_message');
	/*------------------------------------------------------------*/
	Route::resource('/teacher/attendance', 'Api\attendanceController');
	Route::get('/teacher/student_statistics/{id}', 'Api\attendanceController@student_statistics');
	Route::post('/teacher/attend_students', 'Api\attendanceController@attend_students');
	Route::resource('/teacher/payment', 'Api\paymentController');
	Route::post('/teacher/payment_students/{id}', 'Api\paymentController@payment_students');
});
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
Route::group(['middleware' => 'AdminAuth'], function () {
	Route::get('/admin/get_packages','Api\adminController@get_packages');
	Route::get('/admin/get_teachers','Api\adminController@get_teachers');
	/*------------------------------------------------------------*/
	Route::post('/admin/add_teacher','Api\adminController@add_teacher');
	Route::post('/admin/update_teacher','Api\adminController@update_teacher');
	Route::post('/admin/change_teacher_is_rtl','Api\adminController@change_teacher_is_rtl');
	Route::post('/admin/change_teacher_status','Api\adminController@change_teacher_status');
	Route::post('/admin/change_teacher_accept_register','Api\adminController@change_teacher_accept_register');
	Route::post('/admin/add_package','Api\adminController@add_package');
	Route::post('/admin/update_package','Api\adminController@update_package');
	Route::post('/admin/change_package_status','Api\adminController@change_package_status');
	Route::post('/admin/change_package_image','Api\adminController@change_package_image');
});
/*----------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------*/
Route::get('/main/get_teachers','Api\mainController@get_teachers');
Route::get('/main/get_appointments','Api\mainController@get_appointments');
Route::get('/main/get_days','Api\mainController@get_days');

Route::post('login','Api\mainController@login');
Route::post('register','Api\mainController@register');
});
Route::get('connection','Api\mainController@connection');