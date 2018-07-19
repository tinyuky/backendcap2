<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes;
use App\Http\Resources\Classes as ClassesResource;
use Validator;
use Illuminate\Validation\Rule;

class ClassesController extends Controller
{
    public function add(Request $request){
        // message for validate
        $messages = [
            'Name.required' => 'Tên lớp không để trống',
            'Name.not_regex' => 'Tên lớp không đúng định dạng',
            'Name.unique' => 'Tên lớp đã sử dụng',
            'Name.regex' => 'Họ và tên không đúng định dạng',
            'GradeId.required' => 'Mã khối không được để trống',
            'GraId.numeric' => 'Mã khối không đúng định dạng',
        ];
        // validate data
        $validator = Validator::make($request->all(), [
            'Name' => array(
                'required',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                'unique:classes,name',
                'not_regex:/\s/s',
            ),
            'GradeId' => 'required|numeric',
        ], $messages)->validate();
        // if($validator->fails()){
        //    return $validator->errors();
        // }
        // add data to database
        $db = new Classes();
        $db->name = $request->input('Name');
        $db->grade_id = $request->input('GradeId');
        $db->save();
        return response()->json(['message'=>'Add Success'], 200);
    }
    public function update(Request $request){
        // message for validate
        $messages = [
            'Id.required' => 'Id lớp không để trống',
            'Name.required' => 'Tên lớp không để trống',
            'Name.not_regex' => 'Tên lớp không đúng định dạng',
            'Name.unique' => 'Tên lớp đã sử dụng',
            'Name.regex' => 'Họ và tên không đúng định dạng',
            'GradeId.required' => 'Mã khối không được để trống',
            'GraId.numeric' => 'Mã khối không đúng định dạng',
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
        
        $db = Classes::find($request['Id']);

        // validate data
        $validator = Validator::make($request->all(), [
            'Name' => array(
                'required',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                Rule::unique('classes')->ignore($db->id),
                'not_regex:/\s/s',
            ),
            'GradeId' => 'required|numeric',
        ], $messages)->validate();
        // if($validator->fails()){
        //    return $validator->errors();
        // }

        
        $db->name = $request->input('Name');
        $db->grade_id = $request->input('GradeId');
        $db->save();
        return response()->json(['message'=>'Update Success'], 200);
    }
    public function get($id){
        return new ClassesResource(Classes::find($id));
    }
    public function getall(){
        return ClassesResource::collection(Classes::all()->sortByDesc('name'));
    }
}
