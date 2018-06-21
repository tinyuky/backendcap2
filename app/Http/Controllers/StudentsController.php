<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Students;
use App\Grades;
use App\Classes;
use App\Logs;
use Excel;
use File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Students as StudentsResource;
use Illuminate\Foundation\Http\FormRequest;
use JWTAuth;
use Validator;
use Illuminate\Validation\Rule;

class StudentsController extends Controller
{
    public function handle(Request $request){
        $file = $request->file('File');
        $filename = uniqid();
        Storage::disk('public_uploads')->put($filename, File::get($file));
        $data = Excel::load(Storage::disk('public_uploads')->getDriver()->getAdapter()->getPathPrefix().$filename, function($reader) {
        })->get();
        $students = [];
        $count = 0;
        foreach($data as $row ){
            $erstt = '';
            $new = [];
            $new['STT'] = $row['tt'];
            $new['Student_id'] = $row['ma_sv'];
            $new['Name'] = $row['ho_ten_sinh_vien'].$row['ten'];
            $new['Dob'] = $row['ngay_sinh'];
            $new['Class'] = $row['lop'];
            $new['Grade'] = $row['khoi'];
            $new['Gender'] = $row['phai'];
            $new['Pob'] = $row['noi_sinh'];
            $new['Code1'] = $row['ma_khoa'];
            $new['Code2'] = $row['ma_nganh'];
            $new['Note'] = $row['ghi_chu'];
            
            $findst = Students::where('student_id',$row['ma_sv'])->first();           
            if(!empty($findst)){
                $count += 1;
                $erstt .= 'Mã sinh viên đã tồn tại';
            }
            else{
                $findcl = Classes::where('name',$row['lop'])->first();
                if(empty($findcl)){
                    $count += 1;
                    $erstt .= 'Lớp không tồn tại';
                }
                else{
                    $findgr = Grades::where('name',$row['khoi'])->first();
                    if(empty($findgr)){
                        $count += 1;
                        $erstt .= 'Khối không tồn tại';
                    }
                    elseif($findgr->id != $findcl->grade_id){
                        $count += 1;
                        $erstt .= 'Khối và lớp không quan hệ';
                    }
                }
            }

            $new['Error'] = $erstt;
            $students[] = $new;
        }

        $rs = [];
        $rs['ErrorCount'] = $count;
        $rs['File'] = $filename;
        if($count > 0){
            $rs['File'] = '';
            Storage::disk('public_uploads')->delete($filename);
        }
        $rs['Students'] = $students;
        return response()->json($rs);
    }

    public function import(Request $request,$filename){
        $data = Excel::load(Storage::disk('public_uploads')->getDriver()->getAdapter()->getPathPrefix().$filename, function($reader) {
        })->get();

        $user = JWTAuth::parseToken()->authenticate($request);
        $old = Logs::where('user_id',$user->id)->get();
        foreach ($old as $key) {
            $key->status = '0';
            $key->save();
        }

        foreach($data as $row ){
            $new = new Students();
            $new->student_id = $row['ma_sv'];
            $new->name = $row['ho_ten_sinh_vien'].$row['ten'];
            $new->dob = date('Y-m-d', strtotime($row['ngay_sinh']));
            switch ($row['phai']){
                case 'Nam':
                    $new->gender = 1;
                    break;
                case 'Nữ':
                    $new->gender = 2;
                    break;
                
                default:
                    $new->gender = 0;
                    break;
            }  
            $grade = Grades::where('name',$row['khoi'])->first();
            $class = Classes::where('name',$row['lop'])->first();
            $new->grade_id = $grade->id;
            $new->class_id = $class->id;
            $new->status = 1;
            $new->save();

            $newlog = new Logs();
            $newlog->student_id = $new->student_id;
            $newlog->action = "add";
            $newlog->status = '1';
            
            $newlog->user_id = $user->id;
            $newlog->save();
        }
        Storage::disk('public_uploads')->delete($filename);
        return response()->json('Add success');
    }

    public function undo(Request $request){
        $user = JWTAuth::parseToken()->authenticate($request);
        $students = Logs::select('student_id')->where([
            ['user_id',$user->id],
            ['status','1']
        ])->get();
        Students::whereIn('student_id',$students)->delete();
        Logs::whereIn('student_id',$students)->update(['status' => 0]); 
        foreach ($students as $key) {
            $newlog = new Logs();
            $newlog->student_id = $key->student_id;
            $newlog->action = "remove";
            $newlog->status = '0';
            
            $newlog->user_id = $user->id;
            $newlog->save();
        }
        return response()->json('Undo success');
    }

    public function update(Request $request){
        $db = Students::find($request['Id']);
        $messages = [
            'Id.required' => 'Id không được trống',
            'student_id.unique' => 'Mã sinh viên đã sử dụng',
            'student_id.required' => 'Mã sinh viên không để trống',
            'Name.required' => 'Họ và tên không để trống',
            'Dob.required' => 'Ngày sinh không để trống',
            'Dob.date' => 'Ngày sinh không đúng định dạng',
            'Gender.required' => 'Phái không để trống',
            'ClassId.required' => 'Lóp không để trống',
            'GradeId.required' => 'Khối không để trống',
            'Status.required' => 'Trạng thái không để trống',
        ];
        $validator = Validator::make($request->all(), [
            'Id' => 'required',
            'student_id' => [
                'required',
                Rule::unique('students')->ignore($db->student_id,"student_id"),
            ],
            'Name' => 'required',
            'Dob' => 'required|date',
            'Gender' => 'required|numeric',
            'ClassId' => 'required|numeric',
            'GradeId' => 'required|numeric',
            'Status' => 'required|numeric',
        ],$messages);

        if($validator->fails()){
           return $validator->errors();
        }

        $db->student_id = $request->input('student_id');
        $db->name = $request->input('Name');
        $db->dob = date('Y-m-d', strtotime($request->input('Dob')));
        $db->gender = $request->input('Gender');
        $db->status = $request->input('Status');
        $db->class_id = $request->input('ClassId');
        $db->grade_id = Grades::find($request->input('GradeId'))->id;
        $db->save();
        return response()->json(['message'=>'Update Success'], 200);
    }
    public function get($id){
        $student = Students::find($id);
        $rs = [
            'Id'=> $student->id,
            'StudentId' => $student->student_id,
            'Name' => $student->name,
            'Dob' =>$student->dob,
            'Gender' => $student->gender,
            'Class'=> $student->class->name,
            'Grade'=> $student->grade->name,
            'Status'=> $student->status,
            'ClassId'=> $student->class_id,
            'GradeId'=> $student->grade_id,
        ];
        return response()->json($rs);
    }
    public function getall(){
        $rs = [];
        $students = Students::all();
        foreach($students as $student){
            $row = [
                'Id'=> $student->id,
                'StudentId' => $student->student_id,
                'Name' => $student->name,
                'Dob' =>$student->dob,
                'Gender' => $student->gender,
                'Class'=> $student->class->name,
                'Grade'=> $student->grade->name,
                'Status'=> $student->status,
                'ClassId'=> $student->class_id,
                'GradeId'=> $student->grade_id,
            ];
            $rs[] = $row;
        }
        return response()->json($rs);
    }
}
