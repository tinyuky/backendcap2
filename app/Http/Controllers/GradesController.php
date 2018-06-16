<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Grades;
use App\Http\Resources\Grades as GradesResource;

class GradesController extends Controller
{
    public function add(Request $request){
        $db = new Grades();
        $db->name = $request->input('Name');
        $db->save();
        return response()->json(['message'=>'Add Success'], 200);
    }
    public function update(Request $request){
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
   
}
