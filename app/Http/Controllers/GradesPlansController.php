<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Grades_Plans;
use App\Course_Plans;
use App\Courses;
use App\Grades;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\GradePlan as GradePlanResource;
use App\Http\Resources\CoursesPlans as CoursesPlansResource;
use App\Rules\GradePlanUnique;
use Excel;
use Illuminate\Support\Facades\Storage;
use File;
use App\Classes_CoursesPlan;
use App\Http\Resources\Classes_CoursesPlanRs as Classes_CoursesPlanResource;

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
        
        
        $db = Courses::find($request['Id']);

        $validator = Validator::make($request->all(), [
            'Name' => [
                'required',
                'not_regex:/\`|\~|\!|\@|\$|\%|\^|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;/s',
                'regex:/^\S+(\s\S+)+$/s',
            ],
            'MaMH' => array(
                        'nullable',
                        'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                    ),
            'DVHT' => [
                'required',
                'numeric',
            ],
            'TongTiet' => [
                'required',
                'numeric',
            ],
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
            'HK' => [
                'required',
                'numeric',
            ],
            'GradeId' => [
                'required',
                'numeric',
            ],
        ],$messages)->validate();

        //Check 4 column unique
        // $dbunique = $db->code.$db->name.$db->hk.$db->grade_id;
        // $rsunique = $request['Code'].$request['Name'].$request['HK'].$request['GradeId'];
        // if( $dbunique != $rsunique ){
        //     $request->request->add(['unique'=> $rsunique]);
        //     $request->validate([
        //         'unique'=> new CourseUnique(),
        //     ]);
        // }
        
        // $db->code = $request->input('Code');
        // $db->name = $request->input('Name');
        $db->dvht = $request->input('DVHT');
        $db->tong_tiet = $request->input('TongTiet');
        $db->lt = $request->input('LT');
        $db->bt = $request->input('BT');
        $db->th = $request->input('TH');
        // $db->hk = $request->input('HK');
        $db->da = $request->input('ĐA');
        $db->tc = $request->input('TC');
        $db->sg = $request->input('SG');
        $db->ghi_chu = $request->input('GhiChu');
        $db->save();
        return response()->json(['message'=>'Update Success'], 200);
    }

    public function deleteCoursePlan($id){
        Course_Plans::delete($id);
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

    public function deleteClassInPlan($id){
        Classes_CoursesPlan::delete($id);
        return response()->json(['message' => 'Delete success', 200]);
    }

    

}