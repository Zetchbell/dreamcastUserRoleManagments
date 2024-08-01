<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Validator;

class UserController extends Controller
{
    //
    public function __construct(){

    }

    public function index()
    {
        $roles = Role::all();
        return view('welcome',compact('roles'));
    }

    public function saveUserData(Request $request)
    {
    
        $result = array();    
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|regex:/^(\+\d{1,3}[- ]?)?\d{10}$/',
                'description' => 'nullable|string',
                'role_id' => 'required|exists:roles,id',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png',
            ]);

            if ($validator->fails()) {
                $result = array('status' => 0, 'message' => 'Some error occured.','errors' => $validator->errors());
                return response()->json($result, 422);
            }

            $user = new User($request->except('profile_image'));
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $extenstion = $file->getClientOriginalExtension();
                $filename = time().'.'.$extenstion;
                $file->move('uploads/profile_images/', $filename);
                $user->profile_image = $filename;
            }

            $saveUser = $user->save();
            if($saveUser){
                $result = array('status' => 1, 'message' => 'User saved successfully!');
            }else{
                $result = array('status' => 0, 'message' => 'Some error occured');
            }
        }else{
            $result = array('status' => 0, 'message' => 'Some error occured');
        }
        return response()->json($result);
    }

    public function getData()
    {
        
        $users = User::with('role')->get()->map(function ($user) {
            $imgpath = asset('uploads/profile_images/' . $user->profile_image);
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'description' => ($user->description)?$user->description:'',
                'role' => $user->role->name,
                'image' => ($user->profile_image)?'<a href="'.$imgpath.'" download target="_blank" ><img src="'.$imgpath.'" class="img img-thumbnail" width="50"></a>':''
            ];
        });
        return response()->json($users);
    }


}
