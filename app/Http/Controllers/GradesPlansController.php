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
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\GradePlan as GradePlanResource;
use App\Http\Resources\CoursesPlans as CoursesPlansResource;
use App\Http\Resources\Classes_CoursesPlanRs as Classes_CoursesPlanResource;
use App\Http\Resources\Student_In_ClassInPlan_Rs as Student_In_ClassInPlan_Rs;
use App\Rules\GradePlanUnique;
use Excel;
use File;
use Validator;

class GradesPlansController extends Controller
{
    public function create(Request $request){
        $name = $request['Year'];
        $hk = $request['HK'];
        $list = $request['List'];

        $rsunique = "YEAR:".$request['Year'].",HK:".$request['HK'];
        $request->request->add(['unique'=> $rsunique]);
        $request->validate([
            'unique'=> new GradePlanUnique(),
        ]);

        $plan = new Grades_Plans();
        $plan->name = $name;
        $plan->hk = $hk;
        $plan->save();
        
        foreach ($list as $key) {
            $find = Courses::find($key['CourseId']);
            $add = new Course_Plans();
            $add->course_id = $key['CourseId'];
            $add->plan_id = $plan->id;
            $add->dvht = $find->dvht;
            $add->tong_tiet = $find->tong_tiet;
            $add->lt = $find->lt;
            $add->bt = $find->bt;
            $add->th = $find->th;
            $add->da = $find->da;
            $add->tc = $find->tc;
            $add->sg = $find->sg;
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

    public function export($id){
        $list = Grades_Plans::find($id)->courses;
        $list1 = Grades_Plans::find($id);
        $add = [];
        $add['year'] = $list1->name; 
        $add['hk'] = $list1->hk;
        $add['list'] = [];
        $stt = 1;
        foreach ($list as $key) {
            $row = [];
            $row[] = $stt;
            $find = Courses::find($key->course_id)->code;
            if($find != null){
                $row[] = $find;
            }
            else{
                $row[] = '';
            }
            $find = Courses::find($key->course_id)->name;
            if($find != null){
                $row[] = $find;
            }
            else{
                $row[] = '';
            }
            if($key->dvht){
                $row[] = $key->dvht;
            }
            else{
                $row[] = '';
            }
            if($key->tong_tiet){
                $row[] = $key->tong_tiet;
            }
            else{
                $row[] = '';
            }
            if($key->lt){
                $row[] = $key->lt;
            }
            else{
                $row[] = '0';
            }
            if($key->bt){
                $row[] = $key->bt;
            }
            else{
                $row[] = '0';
            }
            if($key->th){
                $row[] = $key->th;
            }
            else{
                $row[] = '0';
            }
            $row[]='';
            if($key->da){
                $row[] = $key->da;
            }
            else{
                $row[] = '0';
            }
            $row[] = '';
            if($key->tc){
                $row[] = $key->tc;
            }
            else{
                $row[] = '0';
            }
            if($key->sg){
                $row[] = $key->sg;
            }
            else{
                $row[] = '0';
            }
            $row[] = Courses::find($key->id)->grade->name;
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $add['list'][] = $row;
            $stt += 1;
        }
        Excel::load(Storage::disk('public_uploads_template')->getDriver()->getAdapter()->getPathPrefix().'TrainingPlan.xlsx', function($file) use($add) {
            $file->sheet('Sheet1',function($sheet) use($add){
                // draw data
                $start = 7;
                foreach ($add['list'] as $row) {
                    $sheet->row($start,$row);
                    $sheet->row($start, function($row){
                        $row->setFontFamily('Times New Roman');
                    });
                    $start++;
                }
                $start--;
                $sheet->setBorder('A7:Q'.$start, 'thin');
                $start++;

                // draw header
                $nextyear = $add['year'] + 1;
                $sheet->setCellValue('A4','CHƯƠNG TRÌNH ĐÀO TẠO NGÀNH KỸ THUẬT PHẦN MỀM HỌC KỲ '.$add['hk'].' - NĂM HỌC '.$add['year'].' - '.$nextyear);
                $sheet->cell('A4', function($cell){
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontFamily('Times New Roman');
                });

                // draw footer
                $sheet->mergeCells('K'.$start.':'.'O'.$start);
                $sheet->setCellValue('K'.$start,'TP.HCM, ngày ... tháng ... năm 2018');
                $sheet->cell('K'.$start, function($cell){
                    //  $cell->setFontStyle('italic');
                    $cell->setFontSize('13');
                    $cell->setFontFamily('Times New Roman');
                });
                $start++;

                $sheet->setCellValue('C'.$start,'TRƯỞNG KHOA');
                $sheet->mergeCells('K'.$start.':'.'O'.$start);
                $sheet->setCellValue('K'.$start,'NGƯỜI LẬP BẢNG');
                $sheet->cell('C'.$start, function($cell){
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('13');
                    $cell->setFontFamily('Times New Roman');
                });
                $sheet->cell('K'.$start, function($cell){
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('13');
                    $cell->setFontFamily('Times New Roman');
                });
                          
            });
        })->download();
        return response()->json('Export success');  
    }


    public function exportByGrade($id, $grid){
        $list = Grades_Plans::find($id)->courses;
        $list1 = Grades_Plans::find($id);
        $gradeplan = Grades::find($grid);
        $add = [];
        $add['year'] = $list1->name; 
        $add['hk'] = $list1->hk;
        $add['grade'] = $gradeplan->name;
        $add['list'] = [];
        $stt = 1;
        foreach ($list as $key) {
            if($key->course->grade_id == $grid){
                $row = [];
                $row[] = $stt;
                $find = Courses::find($key->course_id)->code;
                if($find != null){
                    $row[] = $find;
                }
                else{
                    $row[] = '';
                }
                $find = Courses::find($key->course_id)->name;
                if($find != null){
                    $row[] = $find;
                }
                else{
                    $row[] = '';
                }
                if($key->dvht){
                    $row[] = $key->dvht;
                }
                else{
                    $row[] = '';
                }
                if($key->tong_tiet){
                    $row[] = $key->tong_tiet;
                }
                else{
                    $row[] = '';
                }
                if($key->lt){
                    $row[] = $key->lt;
                }
                else{
                    $row[] = '0';
                }
                if($key->bt){
                    $row[] = $key->bt;
                }
                else{
                    $row[] = '0';
                }
                if($key->th){
                    $row[] = $key->th;
                }
                else{
                    $row[] = '0';
                }
                $row[]='';
                if($key->da){
                    $row[] = $key->da;
                }
                else{
                    $row[] = '0';
                }
                $row[] = '';
                if($key->tc){
                    $row[] = $key->tc;
                }
                else{
                    $row[] = '0';
                }
                if($key->sg){
                    $row[] = $key->sg;
                }
                else{
                    $row[] = '0';
                }
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $add['list'][] = $row;
                $stt += 1;
            }
        }
        $a = Excel::load(Storage::disk('public_uploads_template')->getDriver()->getAdapter()->getPathPrefix().'TrainingPlanByGrade.xlsx', function($file) use($add) {
            $file->sheet('Sheet1',function($sheet) use($add){
                // draw data
                $start = 7;
                foreach ($add['list'] as $row) {
                    $sheet->row($start,$row);
                    $sheet->row($start, function($row){
                        $row->setFontFamily('Times New Roman');
                    });
                    $start++;
                }
                $start--;
                $sheet->setBorder('A7:P'.$start, 'thin');
                $start++;

                // draw header
                $nextyear = $add['year'] + 1;
                $sheet->setCellValue('A4','CHƯƠNG TRÌNH ĐÀO TẠO NGÀNH KỸ THUẬT PHẦN MỀM KHÓA '.$add['grade'].' HỌC KỲ '.$add['hk'].' - NĂM HỌC '.$add['year'].' - '.$nextyear);
                $sheet->cell('A4', function($cell){
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontFamily('Times New Roman');
                });

                // draw footer
                $sheet->mergeCells('K'.$start.':'.'O'.$start);
                $sheet->setCellValue('K'.$start,'TP.HCM, ngày ... tháng ... năm 2018');
                $sheet->cell('K'.$start, function($cell){
                    //  $cell->setFontStyle('italic');
                    $cell->setFontSize('13');
                    $cell->setFontFamily('Times New Roman');
                });
                $start++;

                $sheet->setCellValue('C'.$start,'TRƯỞNG KHOA');
                $sheet->mergeCells('K'.$start.':'.'O'.$start);
                $sheet->setCellValue('K'.$start,'NGƯỜI LẬP BẢNG');
                $sheet->cell('C'.$start, function($cell){
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('13');
                    $cell->setFontFamily('Times New Roman');
                });
                $sheet->cell('K'.$start, function($cell){
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('13');
                    $cell->setFontFamily('Times New Roman');
                });
                          
            });
        })->export('xls');
    }

    public function getGradeByPlan($id){
        $list = Course_Plans::where('plan_id',$id)->get();
        $rs = [];
        foreach ($list as $row) {
            $rs[] = $row->course->grade_id;
        }
        $rs = array_unique($rs);
        return Grades::whereIn('id',$rs)->get();
    }

    public function getCoursePlan($id){
        return new CoursesPlansResource(Course_Plans::find($id));
    }

    public function updateCoursePlan(Request $request){
        $messages = [
            'STT.numeric'=> 'Số thứ tự không đúng định dạng',
            'MaMH.not_regex' => 'Mã môn học sai định dạng',
            'Name.required' => 'Tên môn học không để trống',
            'Name.not_regex' => 'Tên môn học sai định dạng',
            'Name.unique' => 'Tên môn học đã tồn tại',
            'DVHT.numeric'=> 'DVHTTC không đúng định dạng',
            'DVHT.required' => 'DVHTTC không để trống',
            'TongTiet.numeric'=> 'Tổng tiết không đúng định dạng',
            'TongTiet.required' => 'Tổng tiết không để trống',
            'LT.numeric'=> 'LT không đúng định dạng',
            'BT.numeric'=> 'BT không đúng định dạng',
            'TH.numeric'=> 'TH không đúng định dạng',
        ];
        //validate id account
        $validator2 = Validator::make($request->all(), [
            'Id' => array(
                'required',
                'numeric',
            ),
        ], $messages)->validate();
        
        
        $db = Course_Plans::find($request['Id']);

        $validator = Validator::make($request->all(), [
            'LT' => [
                'nullable',
                'numeric',
            ],
            'BT' => [
                'nullable',
                'numeric',
            ],
            'TH' => [
                'nullable',
                'numeric',
            ],
            'DA' => [
                'nullable',
                'numeric',
            ],
        ],$messages)->validate();

        //Check 4 column unique

        $db->tong_tiet = $request->input('LT')+$request->input('BT')+$request->input('TH')+$request->input('DA');
        $db->lt = $request->input('LT');
        $db->bt = $request->input('BT');
        $db->th = $request->input('TH');
        $db->da = $request->input('DA');
        $db->save();
        return response()->json(['message'=>'Update Success'], 200);
    }

    public function deleteCoursePlan($id){
        Course_Plans::find($id)->delete();
        return response()->json(['message' => 'Delete success', 200]);
    }


    public function createClassInPlan(Request $request){
        $classeslst = $request['AssClass'];
        foreach ($classeslst as $value) {
            foreach ($value['Classes'] as $row) {
                $add = new Classes_CoursesPlan();
                $add->name = $row;
                $add->courseplan_id = $value['CourseId'];
                $add->lecturer_id = $value['LecturerId'];
                $add->save();
            }
        }
        return response()->json(['message' => 'Add success', 200]);
    }

    public function getAllClassInPlan(){
        return Classes_CoursesPlanResource::collection(Classes_CoursesPlan::all());
    }

    public function getClassInPlan($id){
        $courses = Course_Plans::where('plan_id',$id)->get();
        $courseslst = [];
        foreach ($courses as $value) {
            array_push($courseslst,$value->id);
        }
        return Classes_CoursesPlanResource::collection(Classes_CoursesPlan::whereIn('courseplan_id',$courseslst)->get());
    }

    public function deleteClassInPlan($id){
        Classes_CoursesPlan::find($id)->delete();
        return response()->json(['message' => 'Delete success', 200]);
    }

    public function deleteEducationPlan($id){
        $courselst = [];
        $courses = Course_Plans::where('plan_id',$id)->get();
        foreach ($courses as $key => $value) {
            $courselst[] = $value->id;
        }
        Classes_CoursesPlan::whereIn('courseplan_id',$courselst)->delete();
        Course_Plans::where('plan_id',$id)->delete();
        Grades_Plans::find($id)->delete();
        return response()->json(['message' => 'Delete success', 200]);
    }    

    public function getTrueFalseCourseInPlan($id){
        $rs = [];
        $courses = Course_Plans::where('plan_id',$id)->get();
        foreach ($courses as $key => $value) {
            $status = true;
            $class = Classes_CoursesPlan::where('courseplan_id',$value->id)->get();
            if( count($class) > 0){
                $status = false;
            }
            $row = [
                'Id' => $value->id,
                'Name'=>$value->course->name,
                'Status'=>$status,
                'Grade'=> $value->course->grade->name,
            ];
            if(!in_array($row,$rs,TRUE)){
                $rs[] = $row;
            }
        }
        return json_encode($rs);
    }

    public function getStudentInClassInPlan($classinplan_id,$class_id){
        $students = Students::where('class_id',$class_id)->get();
        $studentsinclass = Students_ClassInPlan::where('classinplan_id',$classinplan_id)->get();
        $idlst = [];
        $rs=[];
        foreach ($studentsinclass as $key => $value) {
            $idlst[] = $value->student_id;
        }
        foreach ($students as $key => $value) {
            if( in_array($value->id,$idlst) ){
                $rs[] =['Id'=> $value->id,
            'StudentId' => $value->student_id,
            'Name' => $value->name,
            'Dob' =>$value->dob,
            'Gender' => $value->gender,
            'Status'=> $value->status,
            // 'Class' => $this->whenLoaded('class')->name,
            'InClass' => 'true',];
            }
            else{
                $rs[] =['Id'=> $value->id,
            'StudentId' => $value->student_id,
            'Name' => $value->name,
            'Dob' =>$value->dob,
            'Gender' => $value->gender,
            'Status'=> $value->status,
            // 'Class' => $this->whenLoaded('class')->name,
            'InClass' => 'false',];
            }
        }
        // return Student_In_ClassInPlan_Rs::collection($students); 
        return response()->json($rs); 
    }

    public function assignStudentInClassInPlan(Request $request){
        $class = Students_ClassInPlan::where('classinplan_id',$request['ClassId'])->get();
        $newstudents = $request['Students'];
        $oldstudents = [];
        foreach ($class as $key => $value) {
            $oldstudents[] = $value->student_id;
        }
        
        if( (count($oldstudents) > 0) || (count($newstudents) > 0)){
            $remove = array_diff($oldstudents,$newstudents);
            if(count($remove) > 0 ){
                Students_ClassInPlan::whereIn('student_id',$remove)->where('classinplan_id',$request['ClassId'])->delete();
            }
            $add = array_diff($newstudents,$oldstudents);
            if(count($add) > 0){
                foreach ($add as $key => $value) {
                    $student = new Students_ClassInPlan();
                    $student->student_id = $value;
                    $student->classinplan_id = $request['ClassId'];
                    $student->save();
                }
            }
        }
        
        return response()->json(['message' => 'Update success', 200]);
    }

}