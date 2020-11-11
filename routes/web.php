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
/*          Main Pages          */
// Route::view('/','index');
// Route::view('/signup','signup');
// Route::view('/signin','signin');
/*  --------------------------  */

/*        Student Pages         */
// Route::view('/exams','exams');
// Route::view('/viewexams','viewexams');
// Route::view('/solveExam','solveExam');
/*  --------------------------  */

/*        Dashborad Page         */
// Route::view('/home','dashboard');
/*  --------------------------  */

/* Data Entry && Founder Pages  */
// Route::view('/bank','bank');
// Route::get('/SignOut','studentController@SignOut');
/*  --------------------------  */

/*        Founder Pages         */
// Route::view('/control','begin');
// Route::view('/numbers','numbers');
// Route::view('/delete','delete');
// Route::view('/addings','addings');
/*  --------------------------  */

// Route::view('/{id}','error404');


/* --------- Student Controllers ------------- */	

Route::post('/signup','studentController@signup');
Route::post('/signin','studentController@login');
Route::post('/filter','studentController@filter');
Route::post('/filterDate','studentController@filterDate');
Route::post('/deleteStudent','studentController@deleteStudent');
Route::post('/publishMessage','studentController@publishMessage');
Route::post('/DeleteMessage','studentController@DeleteMessage');
Route::post('/DeletePerson','studentController@DeletePerson');
Route::post('/registerFCM','studentController@registerFCM');

/*  -----------------------------------------  */

/* --------- Exam Controllers ------------- */

Route::post('/chkExam','examController@chkExam');
Route::post('/generateCode','examController@generateCode');
Route::post('/smartSearchBank','examController@smartSearchBank');
/*Route::post('/searchExamView','examController@searchExamView');
Route::post('/copyToAnother','examController@copyToAnother');*/
Route::post('/searchExam','examController@searchExam');
Route::post('/addMCQ','examController@addMCQ');
Route::post('/findExam','examController@findExam');
Route::post('/findStudentExam','examController@findStudentExam');
Route::post('/backviewexams','examController@backviewexams');
Route::post('/timer','examController@timer');
Route::post('/end','examController@end');
Route::post('/editQuestion','examController@editQuestion');
Route::post('/endEdit','examController@endEdit');
Route::post('/deleteExam','examController@deleteExam');
Route::post('/copyExam','examController@copyExam');
Route::post('/deleteQ','examController@deleteQ');
Route::post('/updateMCQ','examController@updateMCQ');
Route::post('/exitStudentView','examController@exitStudentView');
Route::post('/setPublish','examController@setPublish');
Route::post('/updateETime','examController@updateETime');
Route::post('/viewExam2','examController@viewExam2');
Route::post('/getMaterialInfo','examController@getMaterialInfo');
Route::post('/backEX','examController@backEX');
Route::post('/markup','examController@addMark');
/*  -----------------------------------------  */