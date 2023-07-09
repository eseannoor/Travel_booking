<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events;
use App\Favorites;
use App\Feedbacks;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\UserDts;
use Illuminate\Support\Facades\Validator;
use Redirect, Response;

class EventController extends Controller
{
    


///////////////////////////////////////////////////////////////////////////////////////////////////


public function GetEvents(){

$Users = DB::table('Events')
            ->select('image','Reference','E_title','Type','Categorie','price','bedroom','bathroom','Street_Address','City','State','sqft','Furnishing','build_year','about','postor','updated_at')
            ->get();
return datatables($Users)->make(true);

}

//////////////////////////////////////////////////////////////////////////////////////////////////////


public function GetEventsCommitte(){

  $Num = Auth::user()->Reference;

$Users = DB::table('events')
            ->select('image','Reference','E_title','Type','Categorie','price','bedroom','bathroom','Street_Address','City','State','sqft','Furnishing','build_year','about','postor','updated_at')
            
            ->get();

return datatables($Users)->make(true);


}
////////////////////////////////////////////////////////////////////////////



public function GetFeedbacks(){

  $Num = Auth::user()->Reference;

$Users = DB::table('feedbacks')
            ->select('Reference','Type','Sender','Email','Message','updated_at')
            
            ->get();

return datatables($Users)->make(true);


}
///////////////////////////////////////////////////////////////////////////////////////////////


protected function SendFeedback(Request $request){

$random = rand();

$validator = Validator::make($request->all(), [
'name' => ['required'],
'Email' =>['required'],
'Objects' =>['required'],
'Message' =>['required'],
]);

if ($validator->fails()){
return 'please fill the form correctly';
        }


 Feedbacks::create([
            'Reference' => $random,
            'Sender' => $request->input('name'),
            'Email' =>$request->input('Email'),
            'User_Ref' =>$request->input('ref'),
            'Type' => $request->input('Objects'),
            'Message' => $request->input('Message'),
        ]);



if(Feedbacks::where('Reference', '=', $random)){
return 'Request submitted successfully, we will get back to you as soon as possible';}
else{
    return 'please fill the form correctly';
}

}
////////////////////////////////////////////////////////////////////////////////////////////////




////////////////////////////////////////////////////////////////////////////////////////////////



public function ReportE(){

$Users = DB::table('events')
            ->select('image','Reference','E_title','about','date','duration','Location','created_at')
            ->get();

$count = DB::table('events')->count();


return view('ReportE',['users'=>$Users,'member'=>$count]);
}

////////////////////////////////////////////////////////////////////////////////////


public function favorites(Request $request){


$Users = DB::table('favorites')
            ->select('propertyRef','User_Ref')
            ->where('propertyRef',$request->input("Ref"))
            ->where('User_Ref',$request->input('UserRef'))
            ->count() > 0;


if($Users > 0 ){


$delete = DB::table('favorites')
->where('propertyRef',$request->input("Ref"))
->where('User_Ref',$request->input('UserRef'))
->delete();

echo "Unsaved";
}
else{
Favorites::create([   
            'propertyRef' => $request->input('Ref'),
            'User_Ref' => $request->input('UserRef'),
        ]);
echo "saved successfully";

}


}
////////////////////////////////////////////////////////////////////////////////////



public function favoritesViews(){


$Users = DB::table('events')
            ->select('*')
            ->join('favorites', 'favorites.PropertyRef', '=', 'events.Reference')
            ->where('User_Ref',Auth::user()->Reference)
            ->get();


return view('Favorites',['events'=>$Users]);



}


/////////////////////////////////////////////////////////////////////////////////////

public function MainEvents(Request $request){




$user = $request->query('State');

$c = "";

if(count($_GET)) {


if ($request->has('State') && $request->filled('State')) {
        $c .= " AND State = '".$request->query('State')."' ";
      }

if ($request->has('City') && $request->filled('City')) {
        $c .= " AND City = '".$request->query('City')."' ";
}

if ($request->has('City') && $request->filled('City')) {
        $c .= " AND City = '".$request->query('City')."' ";
}

if ($request->has('Type') && $request->filled('Type')) {
        $c .= " AND type = '".$request->query('Type')."' ";
}

if ($request->has('Categorie') && $request->filled('Categorie')) {
        $c .= " AND Categorie = '".$request->query('Categorie')."' ";
}

if ($request->has('MAprice') && $request->filled('MAprice')) {
if($request->has('MIprice') && $request->filled('MIprice')){
$c .= " AND price BETWEEN ".$request->query('MIprice')." AND ".$request->query('MAprice')."";}
else{ $c .= " AND price <= ".$request->query('MAprice')." ";}
}


if($request->has('MIprice') && $request->filled('MIprice')){
$c .= " AND price >= ".$request->query('MIprice')." ";}


if ($request->has('Bedroom') && $request->filled('Bedroom')) {
        $c .= " AND bedroom = ".$request->query('Bedroom')." ";
}


if ($request->has('Bathroom') && $request->filled('Bathroom')) {
        $c .= " AND bathroom = ".$request->query('Bathroom')." ";
}


if ($request->has('Furnishing') && $request->filled('Furnishing')) {
        $c .= " AND Furnishing = '".$request->query('Furnishing')."' ";
}

if ($request->has('CheckIn') && $request->filled('CheckIn')) {
        $c .= " AND checkIn = '".$request->query('CheckIn')."' ";
}

 $events = DB::select("select * from events where '1' = '1' ".$c);



return view('Main_Events',['events' => $events]);}










else{
$events = DB::table('Events')
            ->select('image','Reference','E_title','Type','Categorie','price','bedroom','bathroom','Street_Address','City','State','sqft','Furnishing','build_year','about','updated_at')
            ->get();
return view('Main_Events',['events'=>$events]);}
}




////////////////////////////////////////////////////////////////////////////////////////////////////







public function property(Request $request){

$property = $request->query('id');


$P = DB::table('events')
            ->select('image','Reference','E_title','Type','Categorie','price','bedroom','bathroom','Street_Address','City','State','sqft','Furnishing','build_year','postor','about','updated_at')
            ->where('Reference',$property)
            ->get();


return view('Events_View',['events'=>$P]);
}








/////////////////////////////////////////////////////////////////////////////////////////////////////


protected function create(Request $request){


  $Matric = Auth::user()->Reference;

$ref = Rand();

$validator = Validator::make($request->all(), [
'image' => ['required'],
'Title' =>['required','regex:/^[A-Za-z0-9 ]/', 'max:500'],
'Categories' =>['required'],
'types' =>['required'],
'Bedroom' =>['required'],
'Bathroom' =>['required'],
'Furninshing' =>['required'],
'Sqft' =>['required'],
'State' =>['required'],
'City' =>['required','regex:/^[A-Za-z0-9 ]/', 'max:500'],
'Street_Address' =>['regex:/^[A-Za-z0-9 ]/', 'max:500'],
'Price' =>['required'],

]);

if ($validator->fails()){
return redirect()->route('EventsForm')->withErrors($validator->errors());   
        }



//image upload
if($request->hasFile('image')){
  
$extention = $request->file('image')->extension();
$request->image->storeAs('/images/Events',$ref.'.'.$extention,'public');
$imageName = $ref.'.'.$extention;
}else{
	$imageName = '';
}

//upload data in database

events::create([
            'Type' => $ref,
           'Categorie'=> $request->input('Categories'),
           'Reference' => $ref,
           'E_title' => $request->input('Title'),
           'price' => $request->input('Price'),
           'bedroom' => $request->input('Bedroom'),
           'bathroom' => $request->input('Bathroom'),
           'Street_Address' => $request->input('Street_Address'),
           'City' => $request->input('City'),
           'State' => $request->input('State'),
           'sqft' => $request->input('Sqft'),
           'Furnishing' => $request->input('Furninshing'),
           'build_year' => $request->input('build_year'),
           'about' => $request->input('About'),
           'postor' => $Matric,
           'image' =>$imageName,

            //'Location' => $request->input('Sexe'),


        ]);




return redirect()->route('EventsForm', ['msg' => "success"]);

}
///////////////////////////////////////////////////////////////////////////////////////////////////////




protected function createCom(Request $request){


$ref = $request->input('Ref');




$validator = Validator::make($request->all(), [
'image' => ['required'],
'Title' =>['required','regex:/^[A-Za-z0-9 ]/', 'max:500'],
'Categorie' =>['required'],
'types' =>['required'],
'Bedroom' =>['required'],
'Bathroom' =>['required'],
'Furninshing' =>['required'],
'Sqft' =>['required'],
'State' =>['required'],
'City' =>['required','regex:/^[A-Za-z0-9 ]/', 'max:500'],
'Price' =>['required'],

]);

if ($validator->fails()){
return redirect()->route('EventsFormCom')->withErrors($validator->errors());   
        }



//image upload
if($request->hasFile('image')){
  
$extention = $request->file('image')->extension();
$request->image->storeAs('/images/Events',$ref.'.'.$extention,'public');
$imageName = $ref.'.'.$extention;
}else{
  $imageName = '';
}

//upload data in database
  $Matric = Auth::user()->Reference;

events::create([
          'Type' => $request->input('types'),
           'Categorie'=> $request->input('Categorie'),
           'Reference' => $ref,
           'E_title' => $request->input('Title'),
           'price' => $request->input('Price'),
           'bedroom' => $request->input('Bedroom'),
           'bathroom' => $request->input('Bathroom'),
           'Street_Address' => $request->input('Street_Address'),
           'City' => $request->input('City'),
           'State' => $request->input('State'),
           'sqft' => $request->input('Sqft'),
           'Furnishing' => $request->input('Furninshing'),
           'build_year' => $request->input('build_year'),
           'about' => $request->input('About'),
           'postor' => $Matric,
           'image' =>$imageName,
        
        ]);


return redirect()->route('EventsFormCom', ['msg' => "success"]);

}



//////////////////////////////////////////////////////////////////////////////////////////////////////




public function Select($ref) {
      $users = DB::select('select * from events where Reference = ?',[$ref]);

if($users){

      return view('Events.UpdateUser',['users'=>$users]);}
      else{
        return view('Events.Userconsultation');
      }
   }

////////////////////////////////////////////////////////////////////////////////////////////////////


protected function Update(Request $request){
$ref = $request->input('Ref');


$validator = Validator::make($request->all(), [
'Title' =>['required','regex:/^[A-Za-z0-9 ]/', 'max:500'],
'Sqft' =>['required'],
'City' =>['required','regex:/^[A-Za-z0-9 ]/', 'max:500'],
'Street_Address' =>['regex:/^[A-Za-z0-9 ]/', 'max:500'],
'Price' =>['required'],

]);



if ($validator->fails()){
return Redirect('/Events/Update_Events/'.$ref)->withErrors($validator->errors());   
        }



if($request->hasFile('image')){
  
$extention = $request->file('image')->extension();
$request->image->storeAs('/images/Events',$ref.'.'.$extention,'public');
$imageName = $ref.'.'.$extention;


Events::where('Reference', $ref)
       ->update([
           'Type' => $request->input('types'),
           'Categorie'=> $request->input('Categorie'),
           'Reference' => $ref,
           'E_title' => $request->input('Title'),
           'price' => $request->input('Price'),
           'bedroom' => $request->input('Bedroom'),
           'bathroom' => $request->input('Bathroom'),
           'Street_Address' => $request->input('Street_Address'),
           'City' => $request->input('City'),
           'State' => $request->input('State'),
           'sqft' => $request->input('Sqft'),
           'Furnishing' => $request->input('Furninshing'),
           'build_year' => $request->input('build_year'),
           'about' => $request->input('About'),
           'image' =>$imageName,
        ]);


}else{

	
	Events::where('Reference', $ref)
       ->update([
           'Type' => $request->input('types'),
           'Categorie'=> $request->input('Categorie'),
           'Reference' => $ref,
           'E_title' => $request->input('Title'),
           'price' => $request->input('Price'),
           'bedroom' => $request->input('Bedroom'),
           'bathroom' => $request->input('Bathroom'),
           'Street_Address' => $request->input('Street_Address'),
           'City' => $request->input('City'),
           'State' => $request->input('State'),
           'sqft' => $request->input('Sqft'),
           'Furnishing' => $request->input('Furninshing'),
           'build_year' => $request->input('build_year'),
           'about' => $request->input('About'),
        ]);


}




return Redirect('/Events/Update_Events/'.$ref.'?rslt=success');




}

//////////////////////////////////////////////////////////////////










public function SelectCom($ref) {

      $users = DB::table('events')
            ->select('image','Reference','E_title','Type','Categorie','price','bedroom','bathroom','Street_Address','City','State','sqft','Furnishing','build_year','postor','about','updated_at')
            ->where('Reference',$ref)
            ->get();

if($users){

      return view('Committee.UpdateUser',['users'=>$users]);}

      else{
        return view('Committee.Userconsultation');
      }
   }









/////////////////////////////////////////////////////////////////





protected function Delete(Request $request){

$refr =  $request->input('id');

$query = DB::table('events')
                            ->where('Reference', $refr)
                            ->limit(1)
                            ->delete();


 if ($query > 0) {
     echo "successfully deleted";
 } else {
       echo "could not be deleted, please try again!";

 }

}




















}
