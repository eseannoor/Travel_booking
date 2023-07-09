<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events;
use App\Favorites;
use App\Feedbacks;
use App\buses;
use App\car;
use App\car_bookings;
use App\bus_booking;
use App\tour;
use App\tour_bookings;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\UserDts;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Redirect, Response;

class TourController extends Controller
{



public function showCars(){

return view('Tour.UserConsultation');}

public function GetCars() {  
    $events = DB::table('tours')
            ->select('Reference', 'Title', 'Package', 'image','period', 'price', 'Start', 'Ends', 'Includes', 'Excludes', 'created_at', 'updated_at')
            ->get();
    return datatables($events)->make(true);

}



protected function Delete(Request $request){

$refr =  $request->input('id');

$query = DB::table('tours')
                            ->where('Reference', $refr)
                            ->limit(1)
                            ->delete();


 if ($query > 0) {
     echo "successfully deleted";
 } else {
       echo "could not be deleted, please try again!";

 }

}


   public function MainEvents(Request $request){



$c = "";

if(count($_GET)) {


if ($request->has('Name') && $request->filled('Name')) {
        $c .= " AND Title LIKE '%".trim($request->query('Name'))."%' ";
      }

if ($request->has('Package') && $request->filled('Package')) {
        $c .= " AND Package = '".$request->query('Package')."' ";
}


if ($request->has('Available') && $request->filled('Available')) {
        $c .= " AND Start <= '".$request->query('Available')."' ";
}


if ($request->has('MAprice') && $request->filled('MAprice')) {
if($request->has('MIprice') && $request->filled('MIprice')){
$c .= " AND price BETWEEN ".$request->query('MIprice')." AND ".$request->query('MAprice')."";}
else{ $c .= " AND price <= ".$request->query('MAprice')." ";}
}


if($request->has('MIprice') && $request->filled('MIprice')){
$c .= " AND price >= ".$request->query('MIprice')." ";}




 $events = DB::select("select * from tours where '1' = '1' ".$c);

return view('Main_Tour',['events' => $events]);}
else{
$events = DB::table('tours')
            ->select('Reference', 'Title', 'Package', 'image', 'period','price', 'Start', 'Ends', 'Includes', 'Excludes', 'created_at', 'updated_at')
            ->get();
return view('Main_Tour',['events'=>$events]);}
}


////////////////////////////////////////////////////////////////////////////////////////////////





   protected function Busbooking(Request $request){

$random = rand();

  $Matric = Auth::user()->Reference;
$Email = Auth::user()->email;
$name = Auth::user()->name;

$validator = Validator::make($request->all(), [
'name' => ['required'],
'Email' =>['required'],
'Arrival' =>['required'],
'Departure' =>['required'],
]);

if ($validator->fails()){
return 'please fill the form correctly';
        }

tour_bookings::create([
            'Reference' => $random,
            'Title' => $request->input('Title'),
            'Name' => $request->input('name'),
            'Email' => $request->input('Email'),
            'Pickup_date' =>$request->input('Arrival'),
            'Return_date' =>$request->input('Departure'),
            'User' =>  $Matric,
            'price'=>$request->input('price'),

        ]);



$mail = new PHPMailer(true);
//Enable SMTP debugging.
$mail->SMTPDebug = SMTP::DEBUG_OFF;                               
//Set PHPMailer to use SMTP.
$mail->isSMTP();            
//Set SMTP host name                          
$mail->Host = "smtp.gmail.com";
//Set this to true if SMTP host requires authentication to send email
$mail->SMTPAuth = true;                          
//Provide username and password     
$mail->Username = "afmedhoumad8@gmail.com";                 
$mail->Password = "gwabicllrqhxddsh";                           
//If SMTP requires TLS encryption then set it
$mail->SMTPSecure = "tls";                           
//Set TCP port to connect to
$mail->Port = 587;                                   

$mail->From = "afmedhoumad8@gmail.com";
$mail->FromName = "Booking.com";

$mail->addAddress($request->input('Email'), $request->input('name'));

$mail->isHTML(true);

$mail->Subject = "Booking.com You have made a new booking (pending payment)";
$mail->Body = "

Dear ".$request->input('name')."<br><br><br>
<B>Wowww YOU MADE IT ! : </B> You have been made a new booking of Tour <b>".$request->input('Title')."</b> from <B>". $request->input('Arrival')."</b> to <B>".$request->input('Departure') ."</b> <br><br> Booking.com team";

$mail->AltBody = "Booking.com";

try {
    $mail->send();
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}


if(tour_bookings::where('Reference', '=', $random)){
return 'Booking In progress, please now check Out for comfirmation';}
else{
    return 'please fill the form correctly for Booking';
}

}

////////////////////////////////////////////////////////////////////////////////

protected function Mybookings(Request $request){

  $Matric = Auth::user()->Reference;


$Users = DB::table('tour_bookings')
            ->select('*')
            ->where('User', $Matric)
            ->get();
return datatables($Users)->make(true);

}


///////////////////////////////////////////////////////////////////////////////////

protected function cancelTour(Request $request){


$refr =  $request->input('id');

$query = DB::table('tour_bookings')
                            ->where('Reference', $refr)
                            ->limit(1)
                            ->delete();
 if ($query > 0) {
     echo "Booking Bus successfully canceled";
 } else {
       echo "could not be canceled, please try again!";

 }

}


/////////////////////////////////////////////////////////////////////////////////////

protected function updateBookingTour(Request $request){
$Email = Auth::user()->email;
$name = Auth::user()->name;

$refr =  $request->input('id');


$updated = tour_bookings::where('Reference', $refr)
       ->update([
           'Status' => 'confirmed',
                   ]);

if($updated){

$mail = new PHPMailer(true);
//Enable SMTP debugging.
$mail->SMTPDebug = SMTP::DEBUG_OFF;                               
//Set PHPMailer to use SMTP.
$mail->isSMTP();            
//Set SMTP host name                          
$mail->Host = "smtp.gmail.com";
//Set this to true if SMTP host requires authentication to send email
$mail->SMTPAuth = true;                          
//Provide username and password     
$mail->Username = "afmedhoumad8@gmail.com";                 
$mail->Password = "gwabicllrqhxddsh";                           
//If SMTP requires TLS encryption then set it
$mail->SMTPSecure = "tls";                           
//Set TCP port to connect to
$mail->Port = 587;                                   

$mail->From = "afmedhoumad8@gmail.com";
$mail->FromName = "Booking.com";

$mail->addAddress($Email, Auth::user()->name);

$mail->isHTML(true);

$mail->Subject = "Booking.com You have made a new booking Successfully";
$mail->Body = "

Dear ".$name."<br><br><br>
<B>Wowww YOU MADE IT ! : </B> You have confirmed your booking of Tour : <b>".$request->input('$refr')."</b> <br><br> Booking.com team";

$mail->AltBody = "Booking.com";

try {
    $mail->send();
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}


    echo "you have made the payment and confirmed the booking successfully";
}
else{

        echo "you have not made the payment";

}

}



///////////////////////////////////////////////////////////////////////////////////////

protected function create(Request $request){


  $Matric = Auth::user()->Reference;

$ref = Rand();

$validator = Validator::make($request->all(), [
'image' => ['required'],
'Title' =>['required'],
'Fuel' =>['required'],
'DateE' =>['required'],
'DateS' =>['required'],
'Location' =>['required'],
'Price' =>['required'],

]);

if ($validator->fails()){
return redirect()->route('AddTour')->withErrors($validator->errors());   
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

tour::create([
            'Reference' => $ref,
           'Title' => $request->input('Title'),
           'Package' => $request->input('Fuel'),
           'price' => $request->input('Price'),
           'Start' => $request->input('DateS'),
           'Ends' => $request->input('DateE'),
           'Includes' => $request->input('Location'),
           'image' =>$imageName,

            //'Location' => $request->input('Sexe'),


        ]);




return redirect()->route('AddTour', ['msg' => "success"]);

}





}
