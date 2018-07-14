<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Grades_Plans;
use App\Course_Plans;
use App\Courses;
use App\Grades;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\GradePlan as GradePlanResource;

class GradesPlansController extends Controller
{
    public function create(Request $request){
        $input = $request->input('Data');
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
        return response()->json('Add success');  
    }

    public function update(Request $request){
        $input = $request->input('Data');
        $input = json_decode($input);

        $plan = Grades_Plans::find($rquest->input('Id'));
        $plan->name = $input['Name'];
        $plan->save();
        
        Course_Plans::where('plan_id',$plan->id)->delete();

        $courses = $input['CoursesList'];
        foreach ($courses as $course) {
            $row = new Course_Plans();
            $row->course_id = $course['id'];
            $row->plan_id = $plan->id;
            $row->hk = $course['hk'];
            $row->save();
        }
        return response()->json('Update success');  
    }

    public function getCourses($grade_id,$hk){
        return CourseResource::collection(Courses::where('grade_id',$grade_id)
                            ->where('hk',$hk)->get());
    }

    public function get($id){
        return new GradePlanResource(Grades_Plans::find($id));
    }

    public function getAll(){
         return GradePlanResource::collection(Grades_Plans::all());
    }

}