<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;

use Illuminate\Support\Facades\DB;
use App\User;
use App\UserDts;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;

use Illuminate\Foundation\Auth\RegistersUsers;


use Redirect, Response;

class UsersController extends Controller
{
   

use RegistersUsers;


/////////////////////////////////////////////
public function showUsers(){
return view('UserConsultation.UserConsultation');
}
/////////////////////////////////////////////





///////////////////////////////////////////////////////////////////////////////////////////////
public function GetUsers(){

$Users = DB::table('users')
            ->join('user_dts', 'users.Reference', '=', 'user_dts.user_Ref')
            ->select('Reference', 'name', 'email', 'email_verified_at', 'password', 'status', 'Role','photo', 'Tel', 'Address')
            ->get();



return datatables($Users)->make(true);

}

////////////////////////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////////////////////////////////////

public function ReportM(){

$Users = DB::table('users')
            ->join('user_dts', 'users.Reference', '=', 'user_dts.user_Ref')
            ->select('users.Reference','Name','Matric','Tel','Email','Passport','Role','GraduationYear','Programme','Vkey','photo')
            ->get();

$count = DB::table('users')->count();


return view('ReportM',['users'=>$Users,'member'=>$count]);
}




/////////////////////////////////////////////////////////////////////////////////////////////////
protected function create(Request $request){
$ref = $request->input('Ref');

$data = request()->validate([
'image' => ['required'],
'Name' =>['required', 'regex:/^[A-Za-z ]+$/', 'max:255'],
'Tel' => ['required', 'regex:/^[0-9]+$/'],
'Privilege' =>['required','regex:/^[A-Za-z0-9 ]+$/', 'max:255'],
'Address' =>['required'],
'Email' =>['required', 'string', 'email', 'max:255', 'unique:users'],
'Password' =>['required', 'string', 'min:8']
]);




//image upload
if($request->hasFile('image')){
  
$extention = $request->file('image')->extension();
$request->image->storeAs('/images/profile_Images',$ref.'.'.$extention,'public');
$imageName = $ref.'.'.$extention;
}else{
	$imageName = '';
}

//upload data in database

User::create([
            'Reference' => $ref,
            'name' => $request->input('Name'),
            'email' =>$request->input('Email'),
            'password' => Hash::make($request->input('Password')),
            'Role' => $request->input('Privilege'),
        ]);

 UserDts::create([
         'photo' => $imageName,
         'Tel' => $request->input('Tel'),
         'Address' => $request->input('Address'),
         'user_Ref' => $ref,
        ]);

return '<center><b style="color:green">New User has been successfully created</b><br>
<img src="https://media4.giphy.com/media/rNeXmdHjB155PXhtUk/200w.gif" width="80"></center> 
<hr>
<b>Ref : </b>-<span style="color:green">'.$request->input('Ref').'</span><br><hr>
<b>Name : </b>-'.$request->input('Name').'<br>
<b>Tel : </b>-'.$request->input('Tel').'<BR>
<b>Email : </b>-<span style="color:blue">'.$request->input('Email').'</Span><BR><br>
<hr>

Status of the Image of the User : <span style="color:green"><b>Saved</b></span>
';




}


///////////////////////////////////////////////////////////////////////////////////////////////////////

protected function Delete(Request $request){

$refr =  $request->input('id');

$query = DB::table('users')
                            ->where('Reference', $refr)
                            ->limit(1)
                            ->delete();

 if ($query > 0) {


$query2 = DB::table('user_dts')
                            ->where('user_Ref', $refr)
                            ->limit(1)
                          ->delete();
 if ($query2 > 0) {
     echo "successfully deleted";}
     else{
     echo "Deleted but some data are left behind"; }
 } 

 else {
       echo "could not be deleted, please try again!";

 }

}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////








protected function Update(Request $request){

$ref = $request->input('Ref');

$validator = Validator::make($request->all(), [
/*'image' => ['mimes:jpeg,png|max:1014'],
'Name' =>['required', 'regex:/^[A-Za-z ]+$/'],
'Reference' =>['required'],
'Tel' => ['required', 'regex:/^[0-9]+$/'],
'Privilege' =>['required','regex:/^[A-Za-z0-9 ]+$/'],
'Address' =>['required'],
'Email' =>['required', 'string', 'email'],*/
]);



if ($validator->fails()){
return Redirect('/UserConsultation/Update/'.$ref)->withErrors($validator->errors());   
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
            'email' => $request->input('Email'),
            'password' => Hash::make($request->input('Password')),
            'Role' => $request->input('Privilege'),
        ]);

$Userdts = UserDts::where('user_Ref', $ref)
       ->update([
            'Tel' => $request->input('Tel'),
            'Address' =>$request->input('Address'),
            'photo' =>$imageName,
        ]);

}
else{

$User = User::where('Reference', $ref)
              ->update([
           'Reference' => $ref,
            'name' => $request->input('Name'),
            'email' => $request->input('Email'),
            'Role' => $request->input('Privilege'),
        ]);

$Userdts = UserDts::where('user_Ref', $ref)
       ->update([
            'Tel' => $request->input('Tel'),
            'Address' =>$request->input('Address'),
            'photo' =>$imageName,
        ]);

}


}else{
 

if(!empty($request->input('Password'))){
$User = User::where('Reference', $ref)
              ->update([
           'Reference' => $ref,
            'name' => $request->input('Name'),
            'email' => $request->input('Email'),
            'password' => Hash::make($request->input('Password')),
            'Role' => $request->input('Privilege'),
        ]);

$Userdts = UserDts::where('user_Ref', $ref)
       ->update([
            'Tel' => $request->input('Tel'),
            'Address' =>$request->input('Address'),
        ]);


}
else{

$User = User::where('Reference', $ref)
              ->update([
           'Reference' => $ref,
            'name' => $request->input('Name'),
            'email' => $request->input('Email'),
            'Role' => $request->input('Privilege'),
        ]);

$Userdts = UserDts::where('user_Ref', $ref)
       ->update([
            'Tel' => $request->input('Tel'),
            'Address' =>$request->input('Address'),
        ]);

}

}



    return Redirect('/UserConsultation/Update/'.$ref.'?rslt=success');




}

























///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function Select($ref) {
      $users = DB::select('select * from users
Join user_dts on users.Reference = user_dts.user_Ref
where Reference = ?',[$ref]);

if($users){

      return view('UserConsultation.UpdateUser',['users'=>$users]);}
      else{
        return view('UserConsultation.Userconsultation');
      }
   }











}
