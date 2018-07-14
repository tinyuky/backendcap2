<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Logs as LogResource;
use App\Logs;
use App\Students;

class LogController extends Controller
{
    public function getAll(){
        return LogResource::collection(Logs::all());
    }
    
    public function undo($id){
        $log = Logs::find($id);
        switch ($log->option) {
            case 'students':
                Students::where('log_id',$id)->delete();
                Logs::find($id)->delete();
                break;
            
            default:
                # code...
                break;
        };
        return response()->json('Undo success');  
    }
}

