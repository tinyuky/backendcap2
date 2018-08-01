<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Http\Resources\User as UserResource;

class LectureAccountController extends Controller
{
    public function getall(Request $request)
    {
        return UserResource::collection(User::where('role','lecturer')->get());
    }
    //Get account from id function
    public function get($id)
    {
        return new UserResource(User::find($id));
    }

    public function getallactive(Request $request)
    {
        return UserResource::collection(User::where('role','lecturer')->where('status',1)->get());
    }
    //Add account function
    public function add(Request $request)
    {
        //Validate data
        $messages = [
            'StaffId.required' => 'Mã nhân viên không để trống',
            'StaffId.unique' => 'Mã nhân viên đã sử dụng',
            'StaffId.not_regex' => 'Mã nhân viên không đúng định dạng',
            'StaffId.regex' => 'Mã nhân viên không đúng định dạng',
            'Name.required' => 'Họ và tên không để trống',
            'Name.not_regex' => 'Họ và tên không đúng định dạng',
            'Name.regex' => 'Họ và tên không đúng định dạng',
            'Email.unique' => 'Email đã sử dụng',
            'Email.required' => 'Email không để trống',
            'Email.email' => 'Email không đúng định dạng',
            'Email.not_regex' => 'Email không đúng định dạng',
            'OtherEmail.required' => 'Email cá nhân không để trống',
            'OtherEmail.email' => 'Email cá nhân không đúng định dạng',
            'OtherEmail.not_regex' => 'Email cá nhân không đúng định dạng',
            'Password.required' => 'Mật khẩu không được để trống',
            'Password.not_regex' => 'Mật khẩu không đúng định dạng',
            'Phone1.required' => 'SĐT1 không được để trống',
            'Phone1.numeric' => 'SĐT1 không đúng định dạng',
            'Phone2.required' => 'SĐT2 không được để trống',
            'Phone2.numeric' => 'SĐT2 không đúng định dạng',
            'Role.required' => 'Vai trò không được để trống',
            'Role.in' => 'Vai trò không tồn tại',
        ];
        $validator = Validator::make($request->all(), [
            'StaffId' => array(
                'required',
                'unique:users,staffid',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                'not_regex:/\s/s',
            ),
            'Name' => array(
                'required',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                'regex:/[A-Za-z]+([\s{2,}\t{0,}][A-Za-z]+)/s',
            ),
            'Email' => 'required|unique:users,email|email',
            'Email' => array(
                'required',
                'unique:users,email',
                'email',
                'not_regex:/\s/s',
            ),
            'OtherEmail' => array(
                'required',
                'not_regex:/\s/s',
                'email',
            ),
            'Password' => array(
                'required',
                'not_regex:/\s/s',
            ),
            'Phone1' => 'required|numeric',
            'Phone2' => 'required|numeric',
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
        $user->role = 'lecturer';
        $user->save();
        return response()->json(['message' => 'Add account success']);
    }
    //Edit account function
    public function update(Request $request)
    {
        //messega for validate
        $messages = [
            'Id.required' => 'Id không tồn tại',
            'Id.not_regex' => 'Id sai định dạng',
            'StaffId.required' => 'Mã nhân viên không để trống',
            'StaffId.unique' => 'Mã nhân viên đã sử dụng',
            'StaffId.not_regex' => 'Mã nhân viên không đúng định dạng',
            'StaffId.regex' => 'Mã nhân viên không đúng định dạng',
            'Name.required' => 'Họ và tên không để trống',
            'Name.not_regex' => 'Họ và tên không đúng định dạng',
            'Name.regex' => 'Họ và tên không đúng định dạng',
            'Email.unique' => 'Email đã sử dụng',
            'Email.required' => 'Email không để trống',
            'Email.email' => 'Email không đúng định dạng',
            'Email.not_regex' => 'Email không đúng định dạng',
            'OtherEmail.required' => 'Email cá nhân không để trống',
            'OtherEmail.email' => 'Email cá nhân không đúng định dạng',
            'OtherEmail.not_regex' => 'Email cá nhân không đúng định dạng',
            'Password.required' => 'Mật khẩu không được để trống',
            'Password.not_regex' => 'Mật khẩu không đúng định dạng',
            'Phone1.required' => 'SĐT1 không được để trống',
            'Phone1.numeric' => 'SĐT1 không đúng định dạng',
            'Phone2.required' => 'SĐT2 không được để trống',
            'Phone2.numeric' => 'SĐT2 không đúng định dạng',
            'Role.required' => 'Vai trò không được để trống',
            'Role.in' => 'Vai trò không tồn tại',
            'Status.required' => 'Trạng thái không được để trống',
            'Status.in' => 'Trạng thái không tồn tại',
        ];
        //validate id account
        $validator2 = Validator::make($request->all(), [
            'Id' => array(
                'required',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
            ),
        ], $messages)->validate();
        // if($validator2->fails()){
        //    return $validator2->errors();
        // }

        // get account
        $user = User::find($request->input('Id'));

        // validate anothor field
        $validator = Validator::make($request->all(), [
            'StaffId' => [
                'required',
                Rule::unique('users')->ignore($user->id),
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                'not_regex:/\s/s',
            ],
            'Email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
                'not_regex:/\s/s',
                'email',
            ],
            'Name' => array(
                'required',
                'not_regex:/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/s',
                'regex:/[A-Za-z]+([\s{2,}\t{0,}][A-Za-z]+)/s',
            ),
            'OtherEmail' => array(
                'required',
                'not_regex:/\s/s',
                'email',
            ),
            'Password' => array(
                'required',
                'not_regex:/\s/s',
            ),
            'Phone1' => 'required|numeric',
            'Phone2' => 'required|numeric',
            'Status' => 'required|numeric|in:0,1',
        ], $messages)->validate();
        // if($validator->fails()){
        //    return $validator->errors();
        // }

        //save data to database
        $user->staffid = $request->input('StaffId');
        $user->name = $request->input('Name');
        $user->email = $request->input('Email');
        $user->otheremail = $request->input('OtherEmail');
        $user->password = bcrypt($request->input('Password'));
        $user->phone1 = $request->input('Phone1');
        $user->phone2 = $request->input('Phone2');
        $user->status = $request->input('Status');;
        $user->role = 'lecturer';
        $user->save();
        return response()->json(['message' => 'Update account success']);
    }
}
