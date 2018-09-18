<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Grades_Plans;
use App\Course_Plans;
use App\Courses;
use App\Grades;
use App\Classes_CoursesPlan;
use App\Students_ClassInPlan;
use App\Students;
use App\GradeStructure;
use App\GradeData;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\GradePlan as GradePlanResource;
use App\Http\Resources\CoursesPlans as CoursesPlansResource;
use App\Http\Resources\Classes_CoursesPlanRs as Classes_CoursesPlanResource;
use App\Http\Resources\Student_In_ClassInPlan_Rs as Student_In_ClassInPlan_Rs;
use App\Rules\GradePlanUnique;
use Excel;
use File;
use Validator;
use Config;

class StudiedClassController extends Controller
{
    public function getAllClassByLecture($lecture_id){
        return Classes_CoursesPlanResource::collection(Classes_CoursesPlan::where('lecturer_id',$lecture_id)->get());
    }


}
