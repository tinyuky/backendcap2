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
use Config;
use App\Courses;
use App\Http\Resources\Course as CourseResource;

class CoursesController extends Controller
{
    public function handle(Request $request){
        // return $request;
        // get file
        $file = $request->file('File');
        $filename = uniqid();
        // save file
        Storage::disk('public_uploads')->put($filename, File::get($file));
        // read file
        Config::set('excel.import.startRow',6);
        $data = Excel::selectSheets('CTDT_HK1')->load(Storage::disk('public_uploads')->getDriver()->getAdapter()->getPathPrefix().$filename, function($reader) {
        })->get();
        // create array to store data
        $students = [];
        $count = 0;
        $valid = 0;

        // message for validate
        $messages = [
            'STT.numeric'=> 'Số thứ tự không đúng định dạng',
            'MaMH.not_regex' => 'Mã môn học sai định dạng',
            'Name.required' => 'Tên môn học không để trống',
            'Name.not_regex' => 'Tên môn học sai định dạng',
            'Name.unique' => 'Tên môn học đã tồn tại',
            'DVHTTC.numeric'=> 'DVHTTC không đúng định dạng',
            'DVHTTC.required' => 'DVHTTC không để trống',
            'Tongtiet.numeric'=> 'Tổng tiết không đúng định dạng',
            'Tongtiet.required' => 'Tổng tiết không để trống',
            'LT.numeric'=> 'LT không đúng định dạng',
            'BT.numeric'=> 'BT không đúng định dạng',
            'TH.numeric'=> 'TH không đúng định dạng',
        ];
        if(count($data)>0){
            // check data
            foreach($data as $row ){
                $erstt = '';
                $new = [];
                if(!empty($row['stt'])){
                    $new['STT'] = trim($row['stt']);
                    $new['MaMH'] = trim($row['ma_mh']);
                    $new['Name'] = trim($row['ten_mon_hoc']);
                    $new['DVHTTC'] = trim($row['dvhttc']);
                    $new['Tongtiet'] = trim($row['tong_tiet']);
                    $new['LT'] = trim($row['lt']);
                    $new['BT'] = trim($row['bt']);
                    $new['TH'] = trim($row['th']);
                    //validate data
                    $validator = Validator::make($new, [
                        'STT' => [
                            'nullable',
                            'numeric',
                        ],
                        'Name' => array(
                            'required',
                            'not_regex:/\`|\~|\!|\@|\$|\%|\^|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;/s',
                            'regex:/^\S+(\s\S+)+$/s',
                            'unique:courses,name'
                        ),
                        'MaMH' => array(
                            'nullable',
                            'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                        ),
                        'DVHTTC' => [
                            'required',
                            'numeric',
                        ],
                        'Tongtiet' => [
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
                    ],$messages);
                    

                    if($validator->fails()){
                        $count += 1;
                        $aa = $validator->errors()->all();
                        foreach($aa as $k){
                            $erstt .= '/'.$k;
                        }
                    }
                    $new['Error'] = $erstt;
                    $students[] = $new;
                    $valid += 1;
                }                
            }

            $rs = [];
            $rs['ErrorCouses'] = $count;
            $rs['SumCourses'] = $valid;
            $rs['File'] = $filename;
            if($count > 0){
                $rs['File'] = '';
                Storage::disk('public_uploads')->delete($filename);
            }
            $rs['Students'] = $students;
            return response()->json($rs);
        }
        else{
            return response()->json(['error'=>'Không đọc được file']);
        }
    }

    public function import(Request $request,$filename){
        Config::set('excel.import.startRow',6);
        $data = Excel::selectSheets('CTDT_HK1')->load(Storage::disk('public_uploads')->getDriver()->getAdapter()->getPathPrefix().$filename, function($reader) {
        })->get();

        $user = JWTAuth::parseToken()->authenticate($request);

        foreach($data as $row ){
            if(!empty($row['stt'])){
                $new = new Courses();
                $new->name = trim($row['ten_mon_hoc']);
                if($row['ma_mh']!=""){
                    $new->code = trim($row['ma_mh']);
                }
                $new->dvht = trim($row['dvhttc']);
                $new->tong_tiet = trim($row['tong_tiet']);
                if($row['lt']!=""){
                    $new->lt = trim($row['lt']);
                }
                if($row['th']!=""){
                    $new->th = trim($row['th']);
                }
                if($row['bt']!=""){
                    $new->bt = trim($row['bt']);
                }
                $new->save();
            }
            
        }
        Storage::disk('public_uploads')->delete($filename);
        return response()->json('Add success');
    }

    public function update(Request $request){
        $messages = [
            'STT.numeric'=> 'Số thứ tự không đúng định dạng',
            'MaMH.not_regex' => 'Mã môn học sai định dạng',
            'Name.required' => 'Tên môn học không để trống',
            'Name.not_regex' => 'Tên môn học sai định dạng',
            'Name.unique' => 'Tên môn học đã tồn tại',
            'DVHTTC.numeric'=> 'DVHTTC không đúng định dạng',
            'DVHTTC.required' => 'DVHTTC không để trống',
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
                Rule::unique('courses')->ignore($db->id),
                'not_regex:/\`|\~|\!|\@|\$|\%|\^|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;/s',
                'regex:/^\S+(\s\S+)+$/s',
            ],
            'MaMH' => array(
                        'nullable',
                        'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                    ),
            'DVHTTC' => [
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
        ],$messages)->validate();

        $db->code = $request->input('Code');
        $db->name = $request->input('Name');
        $db->dvht = $request->input('DVHTTC');
        $db->tong_tiet = $request->input('TongTiet');
        $db->lt = $request->input('LT');
        $db->bt = $request->input('BT');
        $db->th = $request->input('TH');
        $db->save();
        return response()->json(['message'=>'Update Success'], 200);
    }

    public function get($id){
        return new CourseResource(Courses::find($id));
    }

    public function getall(){
        return CourseResource::collection(Courses::all());
    }
}
