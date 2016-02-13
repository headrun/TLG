<?php

class Orders extends \Eloquent {
	protected $table = 'orders';
	protected $fillable = [];
	
	
	public function Customers(){
	
		return $this->belongsTo("Customers", "customer_id");
	}
	
	
	public function Students(){
	
		return $this->belongsTo("Students", "student_id");
	}
	
	public function StudentClasses(){
	
		return $this->belongsTo("StudentClasses", "student_classes_id");
	}
	
	static function createOrder($input){
		
		$order = new Orders();
		
		$order->customer_id     = $input['customer_id'];
		
		if(isset($input['student_id'])){
			$order->student_id      = $input['student_id'];
		}
		
		if(isset($input['student_classes_id'])){
			$order->student_classes_id     = $input['student_classes_id'];
		}
		
		$order->payment_for     = $input['payment_for'];
		$order->payment_dues_id = $input['payment_dues_id'];
		$order->payment_mode    = $input['payment_mode'];
		$order->card_last_digit = $input['card_last_digit'];
		$order->card_type       = $input['card_type'];
		$order->bank_name       = $input['bank_name'];
		$order->cheque_number   = $input['cheque_number'];
		$order->amount          = $input['amount'];
		$order->order_status    = $input['order_status'];
		$order->created_by      = Session::get('userId');
		$order->created_at      = date("Y-m-d H:i:s");
		if(isset($input['membershipType'])){
			$order->membership_type = $input['membershipType'];
		}else{
			$order->membership_type = null;
		}
		
		$order->save();
		
		return $order;
		
	}
	
	
   static function createBOrder($addbirthday,$addPaymentDues,$taxAmtapplied){
   	
		$order = new Orders ();
		$order->customer_id = $addbirthday ['customer_id'];
		$order->student_id = $addbirthday ['student_id'];
		$order->birthday_id = $addbirthday ['id'];
		$order->payment_for = "birthday";
                $order->payment_dues_id=$addPaymentDues['id'];
		// $order->payment_mode = 'cash';
		$order->amount = $addbirthday ['advance_amount_paid'];
                $order->tax_amount=$taxAmtapplied;
		// $order->status = 'completed';
		$order->created_by = Session::get ( 'userId' );
		$order->created_at = date ( "Y-m-d H:i:s" );
		$order->save ();
		return $order->id;
	}
}