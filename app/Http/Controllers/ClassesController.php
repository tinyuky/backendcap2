<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes;
use App\Http\Resources\Classes as ClassesResource;
class ClassesController extends Controller
{
    public function add(Request $request){
        $db = new Classes();
        $db->name = $request->input('Name');
        $db->grade_id = $request->input('GradeId');
        $db->save();
        return response()->json(['message'=>'Add Success'], 200);
    }
    public function update(Request $request){
        $db = Classes::find($request['Id']);
        $db->name = $request->input('Name');
        $db->grade_id = $request->input('GradeId');
        $db->save();
        return response()->json(['message'=>'Update Success'], 200);
    }
    public function get($id){
        return new ClassesResource(Classes::find($id));
    }
    public function getall(){
        //Lấy tất cả classes
        $all  = Classes::all();
        //Tạo mảng chứa name cần sort
        $sort  = [];
        foreach ($all as $k) {
            // bỏ 'T' trong từng name rồi add vào mảng sort
            $sort[] = str_replace('T','',$k->name);
        }
        //Sắp xếp name trong mảng sort
        sort($sort, SORT_NUMERIC);
        //Tạo mảng chứa kết quả
        $rs = [];
        //Tìm từng class theo name 
        foreach ($sort as $k) {
            $find = Classes::where('name','T'.$k)->first();
            // tạo cấu trúc json
            $rs[] = ['Id'=>$find->id,'Name'=>$find->name];
        }
        // trả kết quả
        return json_encode($rs);
    }
}
