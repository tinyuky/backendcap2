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
use App\Http\Resources\Student_ClassInPlan_Rs as Student_ClassInPlan_Resource;
use App\Rules\GradePlanUnique;
use Excel;
use File;
use Validator;
use DB;
use Config;

class GradeManagementController extends Controller
{
    public function exportGrade($class_id){
        $sheets = DB::table('gradestructure')->select('sheet')
            ->where('classinplan_id',$class_id)->distinct()->get();
        $allgradestructure = GradeStructure::where('classinplan_id',$class_id)->get();
        $students = Students_ClassInPlan::where('classinplan_id',$class_id)->get();
        $allstudents = Students::all();
        $allgradedata = GradeData::all();
        $stdlst = [];
        foreach ($students as $key => $value) {
            preg_match('/[a-zA-Z0-9]*$/', $value->student->name, $firstreg);
            $stdlst[] = $firstreg[0].'-'.$value->student_id;
        }
        asort($stdlst);
        $edit = [];
        foreach ($sheets as $key => $value) {
            $sheet = [];
            $stt = 1 ;
            $sheet[0] = ['#','Student ID','First Name','Middle Name','Last Name','Class'] ;
            $allgradestructurebysheet = $allgradestructure->where('sheet',$value->sheet);
            foreach ($allgradestructurebysheet as $struc) {
                $sheet[0][] = $struc->name;
            }
            
            foreach ($stdlst as $std) {
                $idlst = explode('-',$std);
                $id = $idlst[1];
                $student = $allstudents->firstWhere('id',$id);
                $studentname = $student->name; 
                preg_match('/[a-zA-Z0-9]*$/', $studentname, $firstreg);
                $newname1 = preg_replace('/[a-zA-Z0-9]*$/', '', $studentname);
                preg_match('/^[^\s]*/', trim($newname1), $lastreg);
                $newname2 = preg_replace('/^[^\s]*/', '', $newname1);
                
                $firstname = '';
                $middlename = '';
                $lastname = '';
                
                if($firstreg[0]){
                    $firstname = $firstreg[0];
                }
                if($lastreg[0]){
                    $lastname = $lastreg[0];
                }
                if(strlen (trim($newname2)) > 0){
                    $middlename = trim($newname2);
                }
                
                $sheet[$stt][] = $stt;
                $sheet[$stt][] = $student->student_id;
                $sheet[$stt][] = $firstname;
                $sheet[$stt][] = $middlename;
                $sheet[$stt][] = $lastname;
                $sheet[$stt][] = $student->class->name;
                foreach ($allgradestructurebysheet as $struc) {
                    $grades = $allgradedata->where('student_id',$student->id)
                        ->where('gradestructure_id',$struc->id)->first();
                    if(is_null($grades)){
                        $sheet[$stt][] = 0;
                    }
                    else{
                        $sheet[$stt][] = $grades->grade;
                    }
                }
                $stt++;
                
            }
            
            $edit[$value->sheet]= $sheet;
        }
        Excel::create('GradeExport', function($excel) use($edit) {
            foreach ($edit as $key => $value) { 
                $excel->sheet($key.'', function($sheet) use($value) {
                    $start = 1;
                    
                    foreach ($value as $row) {
                        $sheet->row($start,$row);
                        $sheet->row($start, function($row){
                            $row->setFontFamily('Times New Roman');
                        });
                        $start++;
                    }
                    
                    $columnborder = '';
                    $countborder = count($value[0]);
                    $fromborder = 1;
                    foreach (range('A', 'Z') as $column){
                        if($fromborder == $countborder){
                            $columnborder = $column;
                            break;
                        }
                        $fromborder++;
                    } 
                    $sheet->setBorder('A1:'.$columnborder.($start-1), 'thin', "D8572C");
                });
            }


        })->download('xlsx');
        # Changing properties
        
    }
    
    public function importgrade(Request $request,$classid){
        set_time_limit(0);
        $classinplan = $classid;
        $file = $request->file('File');
        $filename = uniqid();
        // save file
        Storage::disk('public_uploads')->put($filename, File::get($file));
        $data = Excel::load(Storage::disk('public_uploads')->getDriver()->getAdapter()->getPathPrefix().$filename, function($reader) {
        })->all();
        
        $students = Students::all();
        $stdlist = [];
        
        foreach ($students as $value) {
            $stdlist[$value->id] = $value->student_id;
        }
        Excel::load(Storage::disk('public_uploads')->getDriver()->getAdapter()->getPathPrefix().$filename, function($reader) use($filename,$stdlist,$classinplan,$request,$classid) {

            $reader->each(function($sheet) use($reader,$filename,$stdlist,$classinplan,$request,$classid) {
                
                for ($i=1; $i < 1000 ; $i++) { 
                    $check = $reader->getSheetByName($sheet->getTitle())->getCell('A'.$i)->getValue();
                    if($check == "#"){
                        $struclst = [];
                        $strucid = [];
                        $notinlst = ['Student ID','First Name','Middle Name','Last Name','Class','G.','D.O.B','P.O.B','Team'];
                        for ($a='B'; $a <= 'Z' ; $a++) { 
                            $struc = $reader->getSheetByName($sheet->getTitle())->getCell($a.$i)->getValue();
                            if((trim($struc) != "") && (!in_array(trim($struc),$notinlst))){
                                $struclst[] = trim($struc);
                            }
                        }
                        
                        foreach ($struclst as $row) {
                            $old = GradeStructure::where('classinplan_id',$classinplan)
                                    ->where('name',$row)->where('sheet',$sheet->getTitle())->first();
                            if($old){
                                $strucid[] = $old->id;
                            }
                            else{
                                $add = new GradeStructure();
                                $add->classinplan_id = $classinplan;
                                $add->name = $row;
                                $add->sheet = $sheet->getTitle();
                                $add->save();
                                $strucid[] = $add->id;
                            }
                        }
                        
                        Config::set('excel.import.startRow',$i);
                        $abc = Excel::selectSheets($sheet->getTitle())->load(Storage::disk('public_uploads')->getDriver()->getAdapter()->getPathPrefix().$filename, function($reader) {
                            $reader->calculate();
                        })->get();
                        
                        $newstdlst = [];
                        foreach ($abc as $key => $value) {
                            $regrs = preg_match('/^T[0-9]*/', $value->student_id,$matches, PREG_OFFSET_CAPTURE);
                            if( $regrs > 0){
                                for ($r=0; $r < count($strucid) ; $r++) { 
                                    $new = new GradeData();
                                    if(in_array($value->student_id.'',$stdlist)){
                                        $key = array_search($value->student_id, $stdlist);
                                        $label = str_replace(' ','_',strtolower($struclst[$r]));
                                        $newstdlst[] = array('student_id'=>$key,'gradestructure_id'=>$strucid[$r],'grade'=>strlen($value->$label) == 0?'0':$value->$label);
                                    }
                                }
                            }
                        }
                        
                        
                        GradeData::whereIn('gradestructure_id',$strucid)->delete();
                        DB::table('gradedata')->insert($newstdlst);
                        break;
                    } 
                }

            });
            
        });
        
        return response()->json(['message' => 'Add success']);
    }

    public function getGradeByClass($class_id,$student_id){
        $rs = [];   
        $sheets = DB::table('gradestructure')->select('sheet')
            ->where('classinplan_id',$class_id)->distinct()->get();
        $allgradestructure = GradeStructure::where('classinplan_id',$class_id)->get();
        $students = Students_ClassInPlan::where('classinplan_id',$class_id)->get();
        $allstudents = Students::all();
        $allgradedata = GradeData::all();
        $stdlst = [];
        foreach ($students as $key => $value) {
            preg_match('/[a-zA-Z0-9]*$/', $value->student->name, $firstreg);
            $stdlst[] = $firstreg[0].'-'.$value->student_id;
        }
        asort($stdlst);
        foreach ($sheets as $key => $value) {
            $edit = [];
            $sheet = [];
            $stt = 1 ;
            // $sheet[0] = ['#','Student ID','First Name','Middle Name','Last Name','Class'] ;
            $allgradestructurebysheet = $allgradestructure->where('sheet',$value->sheet);
            foreach ($stdlst as $std) {
                $sheetrow = [];
                $idlst = explode('-',$std);
                $id = $idlst[1];
                $student = $allstudents->firstWhere('id',$id);
                $studentname = $student->name; 
                preg_match('/[a-zA-Z0-9]*$/', $studentname, $firstreg);
                $newname1 = preg_replace('/[a-zA-Z0-9]*$/', '', $studentname);
                preg_match('/^[^\s]*/', trim($newname1), $lastreg);
                $newname2 = preg_replace('/^[^\s]*/', '', $newname1);
                
                $firstname = '';
                $middlename = '';
                $lastname = '';
                
                if($firstreg[0]){
                    $firstname = $firstreg[0];
                }
                if($lastreg[0]){
                    $lastname = $lastreg[0];
                }
                if(strlen (trim($newname2)) > 0){
                    $middlename = trim($newname2);
                }
                
                $sheetrow['STT'] = $stt;
                $sheetrow['Student ID'] = $student->student_id;
                $sheetrow['First Name'] = $firstname;
                $sheetrow['Middle Name'] = $middlename;
                $sheetrow['Last Name'] = $lastname;
                $sheetrow['Class'] = $student->class->name;
                foreach ($allgradestructurebysheet as $struc) {
                    $label = $allgradestructure->where('id',$struc->id)->first();
                    $grades = $allgradedata->where('student_id',$student->id)
                        ->where('gradestructure_id',$struc->id)->first();
                    if(is_null($grades)){
                        $sheetrow[$label->name] = 0;
                    }
                    else{
                        $sheetrow[$label->name] = $grades->grade;
                    }
                }
                $stt++;
                $sheet[] = $sheetrow;   
            }
            $headerlblst = GradeStructure::where('sheet',$value->sheet)->where('classinplan_id',$class_id)->get();
            $headlb = ['STT','Student ID','First Name','Middle Name','Last Name','Class'];
            foreach ($headerlblst as $head) {
                $headlb[] = $head->name;
            }
            $edit['Name'] = $value->sheet;
            $edit['Data'] = $sheet;
            $edit['Header'] = $headlb;
            $rs[] = $edit;
        }
        return $rs;
    }

    public function getClassBySTID($student_id){
        $student = Students::where('student_id',$student_id)->first();
        if($student){
            return Student_ClassInPlan_Resource::collection(Students_ClassInPlan::where('student_id',$student->id)->get());
        }
        else{
            return response()->json(['message' => 'Không có dữ liệu'],400);
        }
    }

    public function getGradeBySTID($class_id,$student_id){
        $rs = [];   
        $sheets = DB::table('gradestructure')->select('sheet')
            ->where('classinplan_id',$class_id)->distinct()->get();
        $allgradestructure = GradeStructure::where('classinplan_id',$class_id)->get();
        $allstudents = Students::where('student_id',$student_id)->get(); 
        if(count($allstudents) === 0 ){
            return response()->json(['message' => 'Không có dữ liệu'],400);
        }
        $students = Students_ClassInPlan::where('classinplan_id',$class_id)
            ->where('student_id',$allstudents[0]->id)->get();
        // return $allstudents;
        $allgradedata = GradeData::all();
        $stdlst = [];
        foreach ($students as $key => $value) {
            preg_match('/[a-zA-Z0-9]*$/', $value->student->name, $firstreg);
            $stdlst[] = $firstreg[0].'-'.$value->student_id;
        }
        asort($stdlst);
        foreach ($sheets as $key => $value) {
            $edit = [];
            $sheet = [];
            $stt = 1 ;
            // $sheet[0] = ['#','Student ID','First Name','Middle Name','Last Name','Class'] ;
            $allgradestructurebysheet = $allgradestructure->where('sheet',$value->sheet);
            foreach ($stdlst as $std) {
                $sheetrow = [];
                $idlst = explode('-',$std);
                $id = $idlst[1];
                $student = $allstudents[0];
                $studentname = $student->name; 
                preg_match('/[a-zA-Z0-9]*$/', $studentname, $firstreg);
                $newname1 = preg_replace('/[a-zA-Z0-9]*$/', '', $studentname);
                preg_match('/^[^\s]*/', trim($newname1), $lastreg);
                $newname2 = preg_replace('/^[^\s]*/', '', $newname1);
                
                $firstname = '';
                $middlename = '';
                $lastname = '';
                
                if($firstreg[0]){
                    $firstname = $firstreg[0];
                }
                if($lastreg[0]){
                    $lastname = $lastreg[0];
                }
                if(strlen (trim($newname2)) > 0){
                    $middlename = trim($newname2);
                }
                
                $sheetrow['STT'] = $stt;
                $sheetrow['Student ID'] = $student->student_id;
                $sheetrow['First Name'] = $firstname;
                $sheetrow['Middle Name'] = $middlename;
                $sheetrow['Last Name'] = $lastname;
                $sheetrow['Class'] = $student->class->name;
                foreach ($allgradestructurebysheet as $struc) {
                    $label = $allgradestructure->where('id',$struc->id)->first();
                    $grades = $allgradedata->where('student_id',$student->id)
                        ->where('gradestructure_id',$struc->id)->first();
                    if(is_null($grades)){
                        $sheetrow[$label->name] = 0;
                    }
                    else{
                        $sheetrow[$label->name] = $grades->grade;
                    }
                }
                $stt++;
                $sheet[] = $sheetrow;   
            }
            $headerlblst = GradeStructure::where('sheet',$value->sheet)->where('classinplan_id',$class_id)->get();
            $headlb = ['STT','Student ID','First Name','Middle Name','Last Name','Class'];
            foreach ($headerlblst as $head) {
                $headlb[] = $head->name;
            }
            $edit['Name'] = $value->sheet;
            $edit['Data'] = $sheet;
            $edit['Header'] = $headlb;
            $rs[] = $edit;
        }
        return $rs;   
    }

}
