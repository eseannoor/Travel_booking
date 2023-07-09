<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\UserDts;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class ProfileController extends Controller
{
   

////////////////////////////////////////////////////////////////////////////////
public function index(){
$ref = Auth::user()->Reference;


$Users = DB::table('users')
            ->join('user_dts', 'users.Reference', '=', 'user_dts.user_Ref')
            ->select('name','Email','Tel','Address','password','Role')
            ->where('Reference', $ref)
            ->get();


return view('ProfileMain')->with('users',$Users);



}
////////////////////////////////////////////////////////////////////////////////



protected function UpdateProfile(Request $request){

  $Matric = Auth::user()->Reference;

$validator = Validator::make($request->all(), [
'name' =>['required'],
'Tel' =>['required'],
'password' =>['required'],


]);

if ($validator->fails()){
return $validator->errors(); }

$User = user::where('Reference', $Matric)
       ->update([
           'name' => $request->input('name'),
           'password' => Hash::make($request->input('password')),
           
        ]);

$Userdts = UserDts::where('user_Ref',  $Matric )
       ->update([
           'Tel'=> $request->input('Tel'),
           'Address' => $request->input('Address'),
        ]);


if($User && $Userdts){
    return 'profile updated';
}


}

//////////////////////////////////////////////////////////////////////////////



protected function Update(Request $request){
$ref = $request->input('Ref');


$validator = Validator::make($request->all(), [
'image' => ['mimes:jpeg,png|max:1014'],
'Name' =>['required', 'regex:/^[A-Za-z ]+$/', 'max:255'],
'Matric' =>['required', 'regex:/^[0-9]+$/', 'max:255'],
'Passport' =>['required', 'regex:/^[A-Za-z0-9 ]+$/', 'max:255'],
'Tel' => ['required', 'regex:/^[0-9]+$/'],
'Privilege' =>['required','regex:/^[A-Za-z0-9 ]+$/', 'max:255'],
'Programme' =>['required','regex:/^[A-Za-z0-9 ]+$/', 'max:255'],
'Graduation' =>['required','regex:/^[0-9 ]+$/', 'max:255'],
'Email' =>['required', 'string', 'email', 'max:255'],
]);



if ($validator->fails()){
return Redirect('/Profile')->withErrors($validator->errors());   
        }


if($request->hasFile('image')){
  
$extention = $request->file('image')->extension();
$request->image->storeAs('/images/profile_Images',$ref.'.'.$extention,'public');
$imageName = $ref.'.'.$extention;


if(!empty($request->input('Password'))){


$User = User::where('Reference', $ref)
       ->update([
           'Reference' => $ref,
            'name' => $request->input('Name'),
            'Matric' =>$request->input('Matric'),
            'Passport' => $request->input('Passport'),
            'email' => $request->input('Email'),
            'password' => Hash::make($request->input('Password')),
            'Role' => $request->input('Privilege'),
        ]);

$Userdts = UserDts::where('user_Ref', $ref)
       ->update([
            'Tel' => $request->input('Tel'),
            'GraduationYear' =>$request->input('Graduation'),
            'photo' =>$imageName,
            'Programme' => $request->input('Programme'),
        ]);

}
else{
$User = User::where('Reference', $ref)
       ->update([
           'Reference' => $ref,
            'name' => $request->input('Name'),
            'Matric' =>$request->input('Matric'),
            'Passport' => $request->input('Passport'),
            'email' => $request->input('Email'),
            'Role' => $request->input('Privilege'),
        ]);

$Userdts = UserDts::where('user_Ref', $ref)
       ->update([
            'Tel' => $request->input('Tel'),
            'GraduationYear' =>$request->input('Graduation'),
            'photo' =>$imageName,
            'Programme' => $request->input('Programme'),
        ]);

}


}else{
 

if(!empty($request->input('Password'))){
$User = User::where('Reference', $ref)
       ->update([
           'Reference' => $ref,
            'name' => $request->input('Name'),
            'Matric' =>$request->input('Matric'),
            'Passport' => $request->input('Passport'),
            'email' => $request->input('Email'),
            'password' => Hash::make($request->input('Password')),
            'Role' => $request->input('Privilege'),

        ]);

$Userdts = UserDts::where('user_Ref', $ref)
       ->update([
            'Tel' => $request->input('Tel'),
            'GraduationYear' =>$request->input('Graduation'),
            'Programme' => $request->input('Programme'),
        ]);


}
else{

$User = User::where('Reference', $ref)
       ->update([
           'Reference' => $ref,
            'name' => $request->input('Name'),
            'Matric' =>$request->input('Matric'),
            'Passport' => $request->input('Passport'),
            'email' => $request->input('Email'),
            'Role' => $request->input('Privilege'),

        ]);

$Userdts = UserDts::where('user_Ref', $ref)
       ->update([
            'Tel' => $request->input('Tel'),
            'GraduationYear' =>$request->input('Graduation'),
            'Programme' => $request->input('Programme'),
        ]);

}

}



    return Redirect('/Profile?rslt=success');




}

///////////////////////////////////////////////////////////////////////////////






}
