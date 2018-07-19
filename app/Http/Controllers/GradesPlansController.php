<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Grades_Plans;
use App\Course_Plans;
use App\Courses;
use App\Grades;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\GradePlan as GradePlanResource;
use App\Rules\GradePlanUnique;

class GradesPlansController extends Controller
{
    public function create(Request $request){
        $name = $request['Year'];
        $hk = $request['HK'];
        $list = $request['List'];
        $rsunique = $request['Year'].$request['HK'];
        $request->request->add(['unique'=> $rsunique]);
        $request->validate([
            'unique'=> new GradePlanUnique(),
        ]);

        $plan = new Grades_Plans();
        $plan->name = $name;
        $plan->hk = $hk;
        $plan->save();
        
        foreach ($list as $key) {
            $find = Courses::find($key['CourseId'])->get();
            $add = new Course_Plans();
            $add->course_id = $key['CourseId'];
            $add->plan_id = $plan->id;
            $add->dvht = $find->dvht;
            $add->tong_tiet = $find->tong_tiet;
            $add->lt = $find->lt;
            $add->bt = $find->bt;
            $add->th = $find->th;
            $add->da = $find->da;
            $add->save();
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