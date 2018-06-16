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
        ];
        $validator = Validator::make($request->all(), [
            'StaffId' => 'required|unique:users,staffid|',
            'Name' => 'required',
            'Email' => 'required|unique:users,email|email',
            'OtherEmail' => 'required|email',
            'Password' => 'required',
            'Phone1' => 'required|numeric',
            'Phone2' => 'required|numeric',
            'Role' => 'required',
        ], $messages)->validate();

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
            'Status.required' => 'Trạng thái không được để trống',
        ];
        $validator = Validator::make($request->all(), [
            'StaffId' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
            'Email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
            'Name' => 'required',
            'OtherEmail' => 'required|email',
            'Password' => 'required',
            'Phone1' => 'required',
            'Phone2' => 'required',
            'Role' => 'required',
            'Status' => 'required',
        ], $messages)->validate();

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
