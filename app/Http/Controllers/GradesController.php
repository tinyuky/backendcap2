<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Grades;
use App\Http\Resources\Grades as GradesResource;
use Validator;
use Illuminate\Validation\Rule;

class GradesController extends Controller
{
    public function add(Request $request){
        // message for validate
        $messages = [
            'Name.required' => 'Tên khối không để trống',
            'Name.not_regex' => 'Tên khối không đúng định dạng',
            'Name.unique' => 'Tên khối đã sử dụng',
            'Name.regex' => 'Họ và tên không đúng định dạng',
        ];
        // validate data
        $validator = Validator::make($request->all(), [
            'Name' => array(
                'required',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                'unique:grades,name',
                'not_regex:/\s/s',
            ),
        ], $messages)->validate();
        // if($validator->fails()){
        //    return $validator->errors();
        // }

        $db = new Grades();
        $db->name = $request->input('Name');
        $db->save();
        return response()->json(['message'=>'Add Success'], 200);
    }
    public function update(Request $request){
        // message for validate
        $messages = [
            'Id.required' => 'Id khối không để trống',
            'Name.required' => 'Tên khối không để trống',
            'Name.not_regex' => 'Tên khối không đúng định dạng',
            'Name.unique' => 'Tên khối đã sử dụng',
            'Name.regex' => 'Họ và tên không đúng định dạng',
        ];
        $validator2 = Validator::make($request->all(), [
            'Id' => array(
                'required',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
            ),
        ], $messages)->validate();
        // if($validator2->fails()){
        //    return $validator2->errors();
        // }
        
        $db = Grades::find($request['Id']);

        // validate data
        $validator = Validator::make($request->all(), [
            'Name' => array(
                'required',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                Rule::unique('grades')->ignore($db->id),
                'not_regex:/\s/s',
            ),
        ], $messages)->validate();
        // if($validator->fails()){
        //    return $validator->errors();
        // }
        $db = Grades::find($request['Id']);
        $db->name = $request->input('Name');
        $db->save();
        return response()->json(['message'=>'Update Success'], 200);
    }
    public function get($id){
        return new GradesResource(Grades::find($id));
    }
    public function getall(){
        return GradesResource::collection(Grades::orderBy('name','desc')->get());
    }
    public function getHKFromGradeId($id){
        $list = Grades::find($id)->courses;
        $rsr = [];
        if(count($list)){
            foreach ($list as $key) {
            $rs[] = $key->hk;
            }
            $rs= array_unique($rs);
            sort($rs, SORT_NUMERIC); 
            foreach ($rs as $key) {
                $rsr[] = ['Name'=>$key];
            }
        }
        return response()->json($rsr);    
    }
}
