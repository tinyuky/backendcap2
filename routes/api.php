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

    Route::post('handleCourse','CoursesController@handle');
    Route::get('importCourse/{name}/{grade_id}','CoursesController@import');  
    Route::post('updateCourse','CoursesController@update');
    Route::get('getCourse/{id}','CoursesController@get');
    Route::get('getAllCourses','CoursesController@getall');
    Route::get('deleteCourse/{id}','CoursesController@delete');
    Route::get('getCourseList/{grade_id}/{hk}','CoursesController@getCourseList');
    
    Route::get('getCourseInPlan/{grade_id}/{hk}','GradesPlansController@getCourses');
    Route::get('getEducationPlan/{id}','GradesPlansController@get');
    Route::get('getAllEducationPlans','GradesPlansController@getall');
    Route::post('createEducationPlan','GradesPlansController@create');
    Route::post('updateEducationPlan','GradesPlansController@update');

    Route::get('getAllLogs','LogController@getall');
    Route::get('undoLogs/{id}','LogController@undo');
});


