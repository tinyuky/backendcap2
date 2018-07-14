<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Grades_Plans;
use App\Course_Plans;
use App\Courses;

class GradesPlansController extends Controller
{
    public function create(Request $request){
        $input = $request->get('data');
        $input = json_decode($input);
        $plan = new Grades_Plans();
        $plan->name = $input['Name'];
        $plan->save();
        
        $courses = $input['CoursesList'];
        foreach ($courses as $course) {
            $row = new Course_Plans();
            $row->course_id = $course['id'];
            $row->plan_id = $plan->id;
            $row->hk = $course['hk'];
            $row->save();
        } 
    }

    public function getCoursesByGrade($grade_id){
        return Courses::where('grade_id',$grade_id)->get();
    }
}