<?php

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
class TryController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
			
		echo "<pre>";
		

		
		echo "harsha";
		
		
		
		$enrolledCustomers = StudentClasses::getEnrolledCustomers();
		print_r($enrolledCustomers);
		
		
		
		
		exit();
		
		$data = array("customerName" => "Prasath Aru");
		
		Mail::send('emails.account.customer', $data, function($msg) {
			$email = 'prasath@sincerity.in';
			$msg->from('prasath@sincerity.in', 'The Little Gym');
			$msg->to($email, "Prasath Arumugam")->subject('Test mail');
		});
		
		
		
		
		
		exit();
		
		/* $batchEndDate = Batches::select('end_date')->where("id","=", 2)->get();
		print_r($batchEndDate['0']->end_date); */
		
		$batches = BatchSchedule::where('batch_id', '=', '2')
								->whereBetween('schedule_date', array('2016-1-1', '2016-3-31'))
								->count();
								//->get();
		echo $batches;
	}
	
	
	public static function send_mail($message){
	    Log::info("Started sending error e-mail");
	    $from = 'mohan@headrun.com';
	    $to = 'mohan@headrun.com';
	    $curdate = date('Y-m-d');
	    $subject = "TLG PROD ERROR ALERTS";
	    $from_name = "TLG Support";
	    $sender_message = "<html><head></head><body>".
	                "<p>Hi Team,</p>".
	                "<p>Got below error :<br><strong>".$message."</strong></p>".
	                "<p>The franchisee is : <strong>".Session::get('franchiseId')."</strong></p>".
	                "<p>The user is : <strong>".Session::get('userId')."</strong></p>";
	    $boundary = md5("sanwebe.com");
	    $headers = "MIME-Version: 1.0\r\n";
	    $headers .= "From: ".$from_name." <".$from.">\r\n";
	    $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n";
	    $body = "--$boundary\r\n";
	    $body .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
	    $body .= chunk_split(base64_encode($sender_message));
		// now send the email
	    $retval = @mail($to, $subject, $body, $headers);
	    Log::info("Error e-mail has been successfully sent");
	    return $retval;
	}


}
