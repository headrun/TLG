<?php

class PaymentDues extends \Eloquent { 
	protected $table = 'payments_dues';
	protected $fillable = [];
	
	public function Batches(){
		
		return $this->belongsTo('Batches', 'batch_id');
	}
	
	public function Orders(){
	
		return $this->belongsTo('Orders', 'batch_id');
	}
	
	static function createPaymentDues($inputs){
		
		$paymentDues = new PaymentDues();
		$paymentDues->student_id           = $inputs['student_id'];
		
		$paymentDues->customer_id          = $inputs['customer_id'];
		$paymentDues->batch_id             = $inputs['batch_id'];
		$paymentDues->class_id             = $inputs['class_id'];
		$paymentDues->payment_due_amount   = $inputs['payment_due_amount'];
		$paymentDues->payment_type         = $inputs['payment_type'];
		$paymentDues->payment_status       = $inputs['payment_status'];
		$paymentDues->selected_sessions    = $inputs['selected_sessions'];
		$paymentDues->discount_applied     = $inputs['discount_applied'];
		$paymentDues->created_by              = Session::get('userId');
		$paymentDues->created_at              = date("Y-m-d H:i:s");
		$paymentDues->save();
		
		return $paymentDues;
		
	}
	
	static function getAllPaymentDuesByStudent($studentId){
		
		return PaymentDues::with('Batches')->where('student_id','=',$studentId)->get();
		
		
	}
        static function  getAllPaymentsMade($studentId){
                return PaymentDues::
                                      where('student_id','=',$studentId)
                                    ->where('payment_status','=','paid')
                                    ->get();
                                    
        }
        static function getAllDue($studentId){
            return PaymentDues::
                                      where('student_id','=',$studentId)
                                    ->where('payment_status','=','pending')
                                    ->get();
        }
        static function createBirthdaypaymentdues($addBirthday){
                $paymentDues = new PaymentDues();
		$paymentDues->student_id           = $addBirthday['student_id'];
                $paymentDues->birthday_id          = $addBirthday['id'];
		$paymentDues->customer_id          = $addBirthday['customer_id'];
		$paymentDues->payment_due_amount   = $addBirthday['remaining_due_amount'];
		$paymentDues->payment_type         = 'birthday';
		$paymentDues->payment_status       = 'pending';
		//$paymentDues->discount_applied     = $taxAmtapplied;
		$paymentDues->created_by           = Session::get('userId');
		$paymentDues->created_at           = date("Y-m-d H:i:s");
		$paymentDues->save();		
		return $paymentDues;
        }
}