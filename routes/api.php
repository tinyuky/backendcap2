<?php

use Illuminate\Http\Request;
Route::group([
    'prefix' => 'auth'
],function($router){    
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('me', 'AuthController@me');
});

Route::group([
    'prefix' => 'admin',
    'middleware'=>'auth.jwtad'
],function($router){    
    Route::post('addAccount','AdminController@add');
    Route::post('updateAccount','AdminController@update');
    Route::get('getAllAccounts', 'AdminController@getall');
    Route::get('getAccount/{id}','AdminController@get');
});

Route::group([
    'prefix' => 'assistant',
    'middleware'=>'auth.jwtass'
],function($router){
    Route::post('addGrade','GradesController@add');
    Route::post('updateGrade','GradesController@update');
    Route::get('getGrade/{id}','GradesController@get');
    Route::get('getAllGrades','GradesController@getall');
    Route::get('getHKFromGradeId/{id}','GradesController@getHKFromGradeId');

    Route::post('addClass','ClassesController@add');
    Route::post('updateClass','ClassesController@update');
    Route::get('getClass/{id}','ClassesController@get');
    Route::get('getAllClasses','ClassesController@getall');

    Route::post('handleFile','StudentsController@handle');    
    Route::get('importFile/{name}','StudentsController@import'); 
    Route::post('updateStudent','StudentsController@update');
    Route::get('getStudent/{id}','StudentsController@get');
    Route::get('getAllStudents','StudentsController@getall');

    Route::post('handleCourse/{id}','CoursesController@handle');
    Route::get('importCourse/{name}/{grade_id}','CoursesController@import');  
    Route::post('updateCourse','CoursesController@update');
    Route::get('getCourse/{id}','CoursesController@get');
    Route::get('getAllCourses','CoursesController@getall');
    Route::get('deleteCourse/{id}','CoursesController@delete');
    Route::get('getCourseList/{grade_id}/{hk}','CoursesController@getCourseList');
    Route::post('importCourseWord/{grade_id}','CoursesController@importWord');
    
    Route::get('getCourseInPlan/{grade_id}/{hk}','GradesPlansController@getCourses');
    Route::get('getEducationPlan/{id}','GradesPlansController@get');
    Route::get('getAllEducationPlans','GradesPlansController@getall');
    Route::post('createEducationPlan','GradesPlansController@create');
    Route::post('updateEducationPlan','GradesPlansController@update');
    Route::get('getCoursePlan/{id}','GradesPlansController@getCoursePlan');
    Route::post('updateCoursePlan','GradesPlansController@updateCoursePlan');
    Route::post('deleteCoursePlan/{$id}','GradesPlansController@deleteCoursePlan');
    Route::post('assignClassInPlan','GradesPlansController@createClassInPlan');
    Route::get('getAllClassInPlan','GradesPlansController@getAllClassInPlan');
    Route::get('getClassInPlan/{id}','GradesPlansController@getClassInPlan');
    Route::get('deleteClassInPlan/{id}','GradesPlansController@deleteClassInPlan');
    Route::get('deleteEducationPlan/{id}','GradesPlansController@deleteEducationPlan');
    Route::get('getTrueFalseCourseInPlan/{id}','GradesPlansController@getTrueFalseCourseInPlan');

    
    
    Route::get('getGradeByPlan/{planid}','GradesPlansController@getGradeByPlan');
    Route::get('getCourseByPlan/{planid}','GradesPlansController@getCourseByPlan');


    Route::get('getAllLogs','LogController@getall');
    Route::get('undoLogs/{id}','LogController@undo');

    Route::post('addLecturer','LectureAccountController@add');
    Route::post('updateLecturer','LectureAccountController@update');
    Route::get('getAllLecturers', 'LectureAccountController@getall');
    Route::get('getLecturer/{id}','LectureAccountController@get');
    Route::get('getAllActiveLecturers','LectureAccountController@getallactive');
});

Route::get('exportEducationPlanByGrade/{planid}/{gradeid}','GradesPlansController@exportByGrade');
Route::get('exportEducationPlan/{id}','GradesPlansController@export');
Route::get('downCourseWord/{grade_id}','CoursesController@downWord');