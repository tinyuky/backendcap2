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
use Excel;
use Illuminate\Support\Facades\Storage;

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
                $sheet->setBorder('A7:O'.$start, 'thin');
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

}