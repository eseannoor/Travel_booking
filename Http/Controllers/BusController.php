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
use App\bus_booking;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\UserDts;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Redirect, Response;

class BusController extends Controller
{



public function showBus(){

return view('bus.UserConsultation');}



public function GetBus() {  
    $events = DB::table('buses')
            ->select('Reference','pickup','destination','Bus_company','Departure_time','Arrival_time','duration','updated_at','Date')
            ->get();
    return datatables($events)->make(true);

}





protected function Delete(Request $request){

$refr =  $request->input('id');

$query = DB::table('buses')
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




$user = $request->query('State');

$c = "";

if(count($_GET)) {


if ($request->has('State') && $request->filled('State')) {
        $c .= " AND pickup = '".$request->query('State')."' ";
      }

if ($request->has('City') && $request->filled('City')) {
        $c .= " AND destination = '".$request->query('City')."' ";
}


if ($request->has('CheckOut') && $request->filled('CheckOut')) {
        $c .= " AND Date <= '".$request->query('CheckOut')."' ";
}

if ($request->has('CheckIn') && $request->filled('CheckIn')) {
        $c .= " AND Date = '".$request->query('CheckIn')."' ";
}




if ($request->has('Type') && $request->filled('Type')) {
        $c .= " AND Bus_company = '".$request->query('Type')."' ";
}

if ($request->has('MAprice') && $request->filled('MAprice')) {
if($request->has('MIprice') && $request->filled('MIprice')){
$c .= " AND price BETWEEN ".$request->query('MIprice')." AND ".$request->query('MAprice')."";}
else{ $c .= " AND price <= ".$request->query('MAprice')." ";}
}


if($request->has('MIprice') && $request->filled('MIprice')){
$c .= " AND price >= ".$request->query('MIprice')." ";}




 $events = DB::select("select * from buses where '1' = '1' ".$c);

return view('Main_bus',['events' => $events]);}
else{
$events = DB::table('buses')
            ->select('Reference','pickup','price','destination','Date','Bus_company','avalable_seat','Departure_time','Arrival_time','duration','updated_at')
            ->get();
return view('Main_bus',['events'=>$events]);}
}


////////////////////////////////////////////////////////////////////////////////////////////////





   protected function Busbooking(Request $request){

$random = rand();

  $Matric = Auth::user()->Reference;
$Email = Auth::user()->email;
$name = Auth::user()->name;
 bus_booking::create([
            'Reference' => $random,
            'Bus_company' => $request->input('Bus_company'),
            'Pickup' =>$request->input('pickup'),
            'Dropoff' =>$request->input('destination'),
            'User' => $Matric,

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

$mail->addAddress($Email, Auth::user()->name);

$mail->isHTML(true);

$mail->Subject = "Booking.com You have made a new booking (pending payment)";
$mail->Body = "

Dear ".$name."<br><br><br>
<B>Wowww YOU MADE IT ! : </B> You have been made a new booking of bus ticket <b>".$request->input('Bus_company')."</b> from <B>". $request->input('pickup')."</b> to <B>".$request->input('destination') ."</b> <br><br> Booking.com team";

$mail->AltBody = "Booking.com";

try {
    $mail->send();
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}


if(bus_booking::where('Reference', '=', $random)){
return 'Booking In progress, please now check Out for comfirmation';}
else{
    return 'please fill the form correctly for Booking';
}

}

////////////////////////////////////////////////////////////////////////////////

protected function Mybookings(Request $request){

  $Matric = Auth::user()->Reference;


$Users = DB::table('bus_bookings')
            ->select('*')
            ->where('User', $Matric)
            ->get();
return datatables($Users)->make(true);

}


///////////////////////////////////////////////////////////////////////////////////

protected function cancelBus(Request $request){


$refr =  $request->input('id');

$query = DB::table('bus_bookings')
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

protected function updateBookingBus(Request $request){
$Email = Auth::user()->email;
$name = Auth::user()->name;

$refr =  $request->input('id');


$updated = bus_booking::where('Reference', $refr)
       ->update([
           'status' => 'confirmed',
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

$mail->Subject = "Booking.com You have made a new booking (pending payment)";
$mail->Body = "

Dear ".$name."<br><br><br>
<B>Wowww YOU MADE IT ! : </B> You have confirmed your booking of bus ticket of Reference : <b>".$request->input('id')."</b> <br><br> Booking.com team";

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


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////






protected function create(Request $request){


  $Matric = Auth::user()->Reference;

$ref = Rand();

$validator = Validator::make($request->all(), [
'image' => ['required'],
'company' =>['required'],
'Destination' =>['required'],
'Price' =>['required'],
'Seats' =>['required'],
'Arrival' =>['required'],
'Pickup'=>['required'],
'Depature' =>['required'],
'Date' =>['required'],
'Duration' =>['required'],
]);

if ($validator->fails()){
return redirect()->route('AddFlight')->withErrors($validator->errors());   
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

buses::create([
            'Reference' => $ref,
           'Bus_company' => $request->input('company'),
           'destination' => $request->input('Destination'),
           'price' => $request->input('Price'),
           'pickup' => $request->input('Pickup'),
           'avalable_seat' => $request->input('Seats'),
           'Arrival_time' => $request->input('Arrival'),
           'Departure_time' => $request->input('Depature'),
           'Date' => $request->input('Date'),
           'duration' => $request->input('Duration'),
           'image' =>$imageName,

            //'Location' => $request->input('Sexe'),


        ]);




return redirect()->route('AddFlight', ['msg' => "success"]);

}




}
