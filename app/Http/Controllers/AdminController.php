<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Http\Resources\User as UserResource;

class AdminController extends Controller
{
    //Get all accounts function
    public function getall(Request $request)
    {
        return UserResource::collection(User::all());
    }
    //Get account from id function
    public function get($id)
    {
        return new UserResource(User::find($id));
    }
    //Add account function
    public function add(Request $request)
    {
        //Validate data
        $messages = [
            'StaffId.required' => 'Mã nhân viên không để trống',
            'StaffId.unique' => 'Mã nhân viên đã sử dụng',
            'Name.required' => 'Họ và tên không để trống',
            'Name.not_regex' => 'Họ và tên không đúng định dạng',
            'Email.unique' => 'Email đã sử dụng',
            'Email.required' => 'Email không để trống',
            'Email.email' => 'Email không đúng định dạng',
            'OtherEmail.required' => 'Email cá nhân không để trống',
            'OtherEmail.email' => 'Email cá nhân không đúng định dạng',
            'Password.required' => 'Mật khẩu không được để trống',
            'Phone1.required' => 'SĐT1 không được để trống',
            'Phone1.numeric' => 'SĐT1 không đúng định dạng',
            'Phone2.required' => 'SĐT2 không được để trống',
            'Phone2.numeric' => 'SĐT2 không đúng định dạng',
            'Role.required' => 'Vai trò không được để trống',
            'Role.in' => 'Vai trò không tồn tại',
        ];
        $validator = Validator::make($request->all(), [
            'StaffId' => 'required|unique:users,staffid|',
            // 'Name' => 'required|regex:/^[_A-z]*((-|\s)*[_A-z])/g',
            'Name' => array(
                'required',
                'regex:/^[-@./#&+\w\s]*$/',
            ),
            'Email' => 'required|unique:users,email|email',
            'OtherEmail' => 'required|email',
            'Password' => 'required',
            'Phone1' => 'required|numeric',
            'Phone2' => 'required|numeric',
            'Role' => 'required|in:admin,assistant,student,studentservice,board,lecturer',
        ], $messages);
        if($validator->fails()){
           return $validator->errors();
        }

        //Add account to database
        $user = new User;
        $user->staffid = $request->input('StaffId');
        $user->name = $request->input('Name');
        $user->email = $request->input('Email');
        $user->otheremail = $request->input('OtherEmail');
        $user->password = bcrypt($request->input('Password'));
        $user->phone1 = $request->input('Phone1');
        $user->phone2 = $request->input('Phone2');
        $user->status = true;
        $user->role = $request->input('Role');
        $user->save();
        return response()->json(['message' => 'Add account success']);
    }
    //Edit account function
    public function update(Request $request)
    {
        $user = User::find($request->input('Id'));

        $messages = [
            'StaffId.required' => 'Mã nhân viên không để trống',
            'StaffId.unique' => 'Mã nhân viên đã sử dụng',
            'Name.required' => 'Họ và tên không để trống',
            'Name.regex' => 'Họ và tên không đúng định dạng',
            'Email.unique' => 'Email đã sử dụng',
            'Email.required' => 'Email không để trống',
            'Email.email' => 'Email không đúng định dạng',
            'OtherEmail.required' => 'Email cá nhân không để trống',
            'OtherEmail.email' => 'Email cá nhân không đúng định dạng',
            'Password.required' => 'Mật khẩu không được để trống',
            'Phone1.required' => 'SĐT1 không được để trống',
            'Phone1.numeric' => 'SĐT1 không đúng định dạng',
            'Phone2.required' => 'SĐT2 không được để trống',
            'Phone2.numeric' => 'SĐT2 không đúng định dạng',
            'Role.required' => 'Vai trò không được để trống',
            'Role.in' => 'Vai trò không tồn tại',
            'Status.required' => 'Trạng thái không được để trống',
            'Status.min' => 'Trạng thái không đúng định dạng',
            'Status.max' => 'Trạng thái không đúng định dạng',
        ];
        $validator2 = Validator::make($request->all(), [
            'Name' => 'required|string|regex:/^[a-zA-Z]/',
            'OtherEmail' => 'required|email',
            'Password' => 'required',
            'Phone1' => 'required',
            'Phone2' => 'required',
            'Role' => 'required|in:admin,assistant,student,studentservice,board,lecturer',
            'Status' => 'required|numeric|min:0|max:1',
        ], $messages);

        if($validator2->fails()){
           return $validator2->errors();
        }

        $validator = Validator::make($request->all(), [
            'StaffId' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
            'Email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
        ], $messages);

        

        if($validator->fails()){
           return $validator->errors();
        }

        

        $user->staffid = $request->input('StaffId');
        $user->name = $request->input('Name');
        $user->email = $request->input('Email');
        $user->otheremail = $request->input('OtherEmail');
        $user->password = bcrypt($request->input('Password'));
        $user->phone1 = $request->input('Phone1');
        $user->phone2 = $request->input('Phone2');
        $user->status = $request->input('Status');;
        $user->role = $request->input('Role');
        $user->save();
        return response()->json(['message' => 'Update account success']);
    }
}
