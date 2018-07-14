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
use App\Rules\CourseUnique;

class CoursesController extends Controller
{
    private $coureslist;

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
            'DVHT.numeric'=> 'DVHTTC không đúng định dạng',
            'DVHT.required' => 'DVHTTC không để trống',
            'Tongtiet.numeric'=> 'Tổng tiết không đúng định dạng',
            'Tongtiet.required' => 'Tổng tiết không để trống',
            'LT.numeric'=> 'LT không đúng định dạng',
            'BT.numeric'=> 'BT không đúng định dạng',
            'TH.numeric'=> 'TH không đúng định dạng',
            'HK.required' => 'Học kì không để trống',
            'HK.numeric' => 'Học kì không đúng định dạng',
            'GradeId.required' => 'Khối không để trống',
            'GradeId.numeric' => 'Khối không đúng định dạng',
        ];
        if(count($data)>0){
            // check data
            $list = $this->getCheckList();

            foreach($data as $row ){
                $erstt = '';
                $new = [];
                if(!empty($row['stt'])){
                    $new['STT'] = trim($row['stt']);
                    $new['MaMH'] = trim($row['ma_mh']);
                    $new['Name'] = trim($row['ten_mon_hoc']);
                    $new['DVHT'] = trim($row['dvhttc']);
                    $new['Tongtiet'] = trim($row['tong_tiet']);
                    $new['LT'] = trim($row['lt']);
                    $new['BT'] = trim($row['bt']);
                    $new['TH'] = trim($row['th']);
                    $new['HK'] = trim($row['hk']);
                    
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
                        ),
                        'MaMH' => array(
                            'nullable',
                            'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',    
                        ),
                        'DVHT' => [
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
                        'HK' => [
                            'required',
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
                    $checkunique = trim($row['ma_mh']).trim($row['ten_mon_hoc']).trim($row['hk']).$request->input('GradeId');
                    if($this->validateUniques($checkunique,$list)){
                        $count += 1;
                        $erstt .= '/Môn học đã tồn tại';
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

    public function import(Request $request,$filename,$grade_id){
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
                $new->hk = trim($row['hk']);
                $new->grade_id = $grade_id;
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
        $dbunique = $db->code.$db->name.$db->hk.$db->grade_id;
        $rsunique = $request['Code'].$request['Name'].$request['HK'].$request['GradeId'];
        if( $dbunique != $rsunique ){
            $request->request->add(['unique'=> $rsunique]);
            $request->validate([
                'unique'=> new CourseUnique(),
            ]);
        }
        
        $db->code = $request->input('Code');
        $db->name = $request->input('Name');
        $db->dvht = $request->input('DVHT');
        $db->tong_tiet = $request->input('TongTiet');
        $db->lt = $request->input('LT');
        $db->bt = $request->input('BT');
        $db->th = $request->input('TH');
        $db->hk = $request->input('HK');
        $db->grade_id = $request->input('GradeId');
        $db->save();
        return response()->json(['message'=>'Update Success'], 200);
    }

    public function get($id){
        return new CourseResource(Courses::find($id));
    }

    public function getall(){
        return CourseResource::collection(Courses::all());
    }

    public function delete($id){
        Courses::find($id)->delete();
        return response()->json(['message'=>'Delete Success'], 200);
    }

    //Check list for 4 column Courses: code, name, hk, grade_id
    private function getCheckList(){
        $list = Courses::all();
        $rs = [];
        foreach ($list as $row) {
            $rs[] = $row->code.$row->name.$row->hk.$row->grade_id;
        }
        return $rs;
    }

    //check unique
    private function validateUniques($value,$list){
        return in_array($value,$list);
    }
}
