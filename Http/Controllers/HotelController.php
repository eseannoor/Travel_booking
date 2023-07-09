<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\hotel_booking;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\UserDts;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class HotelController extends Controller
{



    //////////////////////////////////////////////////////////////////////////////////
   protected function Hotelbooking(Request $request){

$random = rand();

  $Matric = Auth::user()->Reference;


$validator = Validator::make($request->all(), [ 
'name' => ['required'],
'Email' =>['required'],
'DOB' =>['required'],
'NumberIn' =>['required'],
'Arrival' =>['required'],
'Departure' =>['required'],
'Tel' =>['required'],

]);

if ($validator->fails()){
return 'please fill the form correctly';
        }


 hotel_booking::create([
            'Reference' => $random,
            'Hotel_name' => $request->input('HotelName'),
            'Email' =>$request->input('Email'),
            'Name' =>$request->input('name'),
            'DOB' => $request->input('DOB'),
            'Number_of_person' => $request->input('NumberIn'),
            'checkIn' => $request->input('Arrival'),
            'Tel' => $request->input('Tel'),
            'checkOut' => $request->input('Departure'),
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

$mail->addAddress($request->input('Email'), $request->input('name'));

$mail->isHTML(true);

$mail->Subject = "Booking.com You have made a new booking (pending payment)";
$mail->Body = "

Dear ".$request->input('name')."<br><br><br>
<B>Wowww YOU MADE IT ! : </B> You have been made a new booking to <b>".$request->input('HotelName')."</b> from <B>". $request->input('Arrival')."</b> to <B>".$request->input('Departure') ."</b> <br><br> Booking.com team";
$mail->AltBody = "Booking.com";

try {
    $mail->send();
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}









if(hotel_booking::where('Reference', '=', $random)){
return 'Booking In progress, please now check Out for comfirmation';}
else{
    return 'please fill the form correctly for Booking';
}

}

/////////////////////////////////////////////////////////////////////////////////////


   protected function Mybookings(Request $request){

  $Matric = Auth::user()->Reference;


$Users = DB::table('hotel_bookings')
            ->select('*')
            ->where('user', $Matric)
            ->get();
return datatables($Users)->make(true);

}

/////////////////////////////////////////////////////////////////////////////////////



protected function cancelHotel(Request $request){

$refr =  $request->input('id');

$query = DB::table('hotel_bookings')
                            ->where('Reference', $refr)
                            ->limit(1)
                            ->delete();
 if ($query > 0) {
     echo "Booking hotel successfully canceled";
 } else {
       echo "could not be deleted, please try again!";

 }

}

////////////////////////////////////////////////////////////


protected function updateBookingHotel(Request $request){

$refr =  $request->input('id');


$updated = hotel_booking::where('Reference', $refr)
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

$mail->addAddress($request->input('Email'), $request->input('name'));

$mail->isHTML(true);

$mail->Subject = "Booking.com You have confirmed the Booking ";
$mail->Body = "

Dear ".$request->input('name')."<br><br><br>
<B>Wowww YOU MADE IT ! : </B> You have been canfirmed your booking to <b>".$request->input('HotelName')."</b> from <B>". $request->input('Arrival')."</b> to <B>".$request->input('Departure') ."</b> information of booking details :<br>

<hr>
            Reference Number: <B>".$refr."</B> <br><hr>
            Hotel_name : <B>". $request->input('HotelName')."</B> <br><hr>
            Email : ".$request->input('Email')."<br><hr>
            Tel : ".$request->input('Tel')."<br><hr>
            Name : <B>".$request->input('name')."</B><br><hr>
            Date of Birth : ".$request->input('DOB')."<br><hr>
            Number_of_person : ".$request->input('NumberIn')."<br><hr>
            checkIn : <B>".$request->input('Arrival')."</B><br><hr>
            checkOut : <B>".$request->input('Departure')."</B><br><hr>
            Status : <B>".$request->input('status')."</B><br><hr>




 <br><br> Booking.com team";
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










}
