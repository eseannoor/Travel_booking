<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\userDts;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;




class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/Register';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'regex:/^[A-Za-z ]+$/', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'Address' => ['required', 'string'],
            'Tel'=> ['required','max:14'],


        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {



       $random = 'RA-'.rand(0,1).''.rand();

       $SUser = UserDts::create([    
         'user_Ref' => $random,
         'Tel' => $data['Tel'],
         'Address' => $data['Address'],
        ]);



         $SUserD =  User::create([
            'Reference' => $random,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
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

$mail->addAddress($data['email'], $data['name']);

$mail->isHTML(true);

$mail->Subject = "Booking.com You have been successfully registered";
$mail->Body = "

Dear ".$data['name']."<br><br><br>
<B>You have been registered </B> You can login to new account.




 <br><br> Booking.com team";
$mail->AltBody = "Booking.com";

try {
    $mail->send();
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}










    }




}
