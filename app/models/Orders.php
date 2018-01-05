
<?php
use Carbon\Carbon;
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
        $franchisee_name=Franchisee::find(Session::get('franchiseId'));
		$year = date('Y');
		$order->customer_id     = $input['customer_id'];
              
		if(isset($input['student_id']) && array_key_exists('student_id',$input) && $input['student_id']!=''){
			$order->student_id      = $input['student_id'];
		}
    $order->invoice_id=(Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;
    if(isset($input['payment_no']) && array_key_exists('payment_no',$input) && $input['payment_no']!=''){
      $order->payment_no= $input['payment_no'];
    }
		
		$order->payment_for     = $input['payment_for'];
		
    if(isset($input['payment_mode']) && array_key_exists('payment_mode',$input) && $input['payment_mode']!='' && $input['payment_mode']=='cheque'){
      $order->payment_mode    = $input['payment_mode'];
      $order->bank_name       = $input['bank_name'];
      $order->cheque_number   = $input['cheque_number'];
    }else if( isset($input['payment_mode']) && array_key_exists('payment_mode',$input) && $input['payment_mode']!='' && $input['payment_mode']=='card'){
      $order->payment_mode    = $input['payment_mode'];
      if(isset($input['payment_mode']) && array_key_exists('payment_mode',$input) && $input['payment_mode']!='' && $input['card_type']){
        $order->card_type       = $input['card_type'];
      }
      if(isset($input['bank_name']) && array_key_exists('bank_name',$input) && $input['bank_name']!=''){
        $order->bank_name       = $input['bank_name'];
      }
      if(isset($input['receipt_number'])){
      }
    }else if(isset($input['payment_mode']) && array_key_exists('payment_mode',$input) && $input['payment_mode']!='' && $input['payment_mode']=='cash'){ //for cash
      $order->payment_mode    = $input['payment_mode'];
    }
                
		if(isset($input['tax_amount']) && array_key_exists('tax_amount',$input) && $input['tax_amount']!=''){
      $order->tax_amount  =$input['tax_amount'];
    }
		
		$order->amount          = $input['amount'];
		$order->order_status    = $input['order_status'];
    $order->franchisee_id   = Session::get('franchiseId');
		$order->created_by      = Session::get('userId');

    if(isset($input['created_at']) && array_key_exists('created_at',$input) && $input['created_at']!=''){
      $order->created_at=$input['created_at'];
    }else{
      $order->created_at      = date("Y-m-d H:i:s");
    }
    if((isset($input['membershipType'])) && (array_key_exists('membershipType',$input)) && ($input['membershipType']!='')){
			$order->membership_type = $input['membershipType'];
		}else{
			$order->membership_type= null;
		}
		
		$order->save();
		
		return $order;
		
	}
	
	
   static function createBOrder($addbirthday,$addPaymentDues,$taxAmtapplied,$inputs){
   	
		$order = new Orders ();
		$order->customer_id = $addbirthday ['customer_id'];
		$order->student_id = $addbirthday ['student_id'];
        $order->franchisee_id=Session::get('franchiseId');
		$order->birthday_id = $addbirthday ['id'];
		$order->payment_for = "birthday";
        $order->invoice_id=(Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;
                if(isset($inputs['birthdayPaymentTypeRadio'])){
                    if($inputs['birthdayPaymentTypeRadio']=='cheque'){
                        if(isset($inputs['birthdayBankName'])){
                            $order->bank_name=$inputs['birthdayBankName'];
                        }
                        if(isset($inputs['birthdayChequeNumber'])){
                            $order->cheque_number=$inputs['birthdayChequeNumber'];
                        }
                        $order->payment_mode='cheque';
                    }
                    if($inputs['birthdayPaymentTypeRadio']=='card'){
                         $order->payment_mode='card';
                        if(isset($inputs['birthdayCardType'])){
                         $order->card_type=$inputs['birthdayCardType'];
                        }
                        if(isset($inputs['birthdayCard4digits'])){
                         //$order->card_last_digit=$inputs['birthdayCard4digits'];
                        }
                        if(isset($inputs['birthdayCardBankName'])){
                         $order->bank_name=$inputs['birthdayCardBankName'];
                        }
                        if(isset($inputs['birthdayCardRecieptNumber'])){
                           // $order->receipt_number=$inputs['birthdayCardRecieptNumber'];
                        }
                    }
                    if($inputs['birthdayPaymentTypeRadio']=='cash'){
                        $order->payment_mode='cash';
                    }
                }
                if(isset($addPaymentDues['id'])){
                $order->payment_dues_id=$addPaymentDues['id'];
                }
		// $order->payment_mode = 'cash';
		$order->amount = $addbirthday ['advance_amount_paid'];
                if(isset($inputs['taxPercentage'])){
                    $order->tax_percentage= $inputs['taxPercentage'];
                }
                $order->tax_amount=$taxAmtapplied;
		// $order->status = 'completed';
		$order->created_by = Session::get ( 'userId' );
		$order->created_at = date ( "Y-m-d H:i:s" );
		$order->save ();
		return $order->id;
	}
    static function createBOrderwithoutPaymentDue($addbirthday,$addPaymentDues,$taxAmtapplied){
                $order = new Orders ();
		$order->customer_id = $addbirthday ['customer_id'];
		$order->student_id = $addbirthday ['student_id'];
        $order->franchisee_id=Session::get('franchiseId');
		$order->birthday_id = $addbirthday ['id'];
		$order->payment_for = "birthday";
        $order->invoice_id=(Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;
                if(isset($addPaymentDues['id'])){
                $order->payment_dues_id=$addPaymentDues['id'];
                }
		// $order->payment_mode = 'cash';
		$order->amount = $addbirthday ['advance_amount_paid'];
                $order->tax_amount=$taxAmtapplied;
		// $order->status = 'completed';
		$order->created_by = Session::get ( 'userId' );
		$order->created_at = date ( "Y-m-d H:i:s" );
		$order->save ();
		return $order->id;
    }
    static function getBirthdayfulldata($customerId){
                $order= Orders:://join('students', 'orders.student_id','=','students.id')
                                //->join('birthday_parties','orders.birthday_id','=','birthday_parties.id')
                                //->join('users','orders.created_by','=','users.id')
                                where('orders.customer_id','=',$customerId)
                                ->where('franchisee_id', '=', Session::get('franchiseId'))
                                ->where('orders.birthday_id','<>','')
                                ->groupBy('orders.id')
                                ->orderBy('birthday_id','DESC')
                                ->get();
                return $order;
                
    }
    static function createPendingorder($paymentDuedata,$taxamount,$inputs){
        $order=new Orders();
        $order->customer_id=$paymentDuedata[0]['customer_id'];
        $order->student_id=$paymentDuedata[0]['student_id'];
        $order->franchisee_id=Session::get('franchiseId');
        $order->birthday_id=$paymentDuedata[0]['birthday_id'];
        $order->payment_dues_id=$paymentDuedata[0]['id'];
        $order->invoice_id=(Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;
        $order->payment_for = "birthday";
        if(isset($inputs['paymentType'])){
        $order->payment_mode=$inputs['paymentType'];
        }
        if(isset($inputs['birthdayReceivecard4digits'])){
        $order->card_last_digit=$inputs['birthdayReceivecard4digits'];
        }
        if(isset($inputs['birthdayReceivecardType'])){
        $order->card_type=$inputs['birthdayReceivecardType'];    
        }else
        if(isset($inputs['birthdayReceivechequeBankName'])){
        $order->card_type=$inputs['birthdayReceivechequeBankName'];    
        }else{
            $order->card_type='';
        }
        if(isset($inputs['birthdayReceivechequeNumber'])){
        $order->cheque_number=$inputs['birthdayReceivechequeNumber'];    
        }
        if(isset($inputs['birthdayReceivecardBankName'])){
        $order->bank_name=$inputs['birthdayReceivecardBankName'];
        }else 
            if(isset($inputs['birthdayReceivechequeBankName'])){
        $order->bank_name=$inputs['birthdayReceivechequeBankName'];
        }else{
            $order->bank_name='';
        }
        
        if(isset($inputs['birthdayReceivecardRecieptNumber'])){
        $order->receipt_number=$inputs['birthdayReceivecardRecieptNumber'];    
        }
        
        
        //$taxamount=(int)((14.5/100)*$paymentDuedata[0]['payment_due_amount']);
        $order->tax_amount=$taxamount;
        $order->amount=$paymentDuedata[0]['payment_due_amount'];
        $order->created_by = Session::get ( 'userId' );
        $order->created_at = date ( "Y-m-d H:i:s" );

        $order->save ();
        return $order;
    }
    static function createPendingOrderForEnrollment($paymentDuedata){
        $order=new Orders();
        $order->customer_id= $paymentDuedata[0]['customer_id'];
        $order->student_id=$paymentDuedata[0]['student_id'];
        $order->franchisee_id=Session::get('franchiseId');
        $order->invoice_id=(Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;
        $order->season_id=$paymentDuedata[0]['season_id'];
        $order->student_classes_id=$paymentDuedata[0]['student_class_id'];
        $order->amount=$paymentDuedata[0]['payment_due_amount'];
        $order->payment_for = "enrollment";
        $order->payment_dues_id=$paymentDuedata[0]['id'];
        $order->created_by = Session::get ( 'userId' );
        $order->created_at = date ( "Y-m-d H:i:s" );

        $order->save ();
        return $order;
    }
        static function createPendingOrderForEnrollmentCardType($paymentDuedata,$inputs){
        $order=new Orders();
        $order->customer_id= $paymentDuedata[0]['customer_id'];
        $order->student_id=$paymentDuedata[0]['student_id'];
        $order->franchisee_id=Session::get('franchiseId');
        $order->season_id=$paymentDuedata[0]['season_id'];
        $order->invoice_id=(Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;
        $order->student_classes_id=$paymentDuedata[0]['student_class_id'];
        $order->amount=$paymentDuedata[0]['payment_due_amount'];
        if(isset($inputs['paymentType'])){
        $order->payment_mode=$inputs['paymentType'];
        }
        if(isset($inputs['carddigits'])){
        $order->card_last_digit=$inputs['carddigits'];
        }
        if(isset($inputs['bankName'])){
        $order->bank_name=$inputs['bankName'];
        }
        $order->payment_for = "enrollment";
        $order->payment_dues_id=$paymentDuedata[0]['id'];
        $order->created_by = Session::get ( 'userId' );
        $order->created_at = date ( "Y-m-d H:i:s" );

        $order->save ();
        return $order;
    }
    static function createPendingOrderForEnrollmentChequeType($paymentDuedata,$inputs){
        $order=new Orders();
        $order->customer_id= $paymentDuedata[0]['customer_id'];
        $order->student_id=$paymentDuedata[0]['student_id'];
        $order->season_id=$paymentDuedata[0]['season_id'];
        $order->franchisee_id=Session::get('franchiseId');
        $order->student_classes_id=$paymentDuedata[0]['student_class_id'];
        $order->invoice_id=(Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;
        $order->amount=$paymentDuedata[0]['payment_due_amount'];
        if(isset($inputs['paymentType'])){
        $order->payment_mode=$inputs['paymentType'];
        }
        if(isset($inputs['chqueNo'])){
        $order->cheque_number=$inputs['chqueNo'];
        }
        if(isset($inputs['bankChequeName'])){
        $order->bank_name=$inputs['bankChequeName'];
        }
        $order->payment_for = "enrollment";
        $order->payment_dues_id=$paymentDuedata[0]['id'];
        $order->created_by = Session::get ( 'userId' );
        $order->created_at = date ( "Y-m-d H:i:s" );

        $order->save ();
        return $order;
    }
    static function getAllPaymentsMade($studentId){
        return Orders:: where ('student_id','=',$studentId)
                       ->where('payment_for','=','enrollment')
                       ->where('order_status','=','completed')
                       ->orderBy('id', 'DESC')
                       ->get();
    }
    static function getpendingPaymentsid($studentId){
        return Orders::where ('student_id','=',$studentId)
                     ->where('payment_for','=','enrollment')
                     ->where('order_status','=','completed')
                     ->distinct('payment_dues_id')->get();
    }
    static function getOrderDetailsbyPaydueId($paydueId){
        return Orders::where('payment_dues_id','=',$paydueId)->get();
    }

    static public function CreateMembershipOrder($inputs) {
        $order = new Orders();
        $order -> customer_id = $inputs['customer_id'];
        $order -> payment_dues_id = $inputs['payment_due_id'];
        $order->franchisee_id=Session::get('franchiseId');
        $order->invoice_id=(Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;

        $order -> payment_for = 'membership';
        $order -> membership_id = $inputs['membership_id'];
        $order -> membership_type = $inputs['membership_type_id'];
        $order -> membership_name = $inputs['membership_name'];
        $order -> payment_mode = $inputs['payment_mode'];
        
         if( $inputs['payment_mode'] == 'cheque'){

            $order -> bank_name = $inputs['chequeBankName'];
            $order -> cheque_number = $inputs['chequeNumber']; 
         
         }else if($inputs['payment_mode'] == 'card'){
            
            $order -> bank_name = $inputs['bankName'];
            $order -> card_type =$inputs['cardType'];
         } 
         
        $order -> amount = $inputs['payment_due_amount'];
        $order -> tax_percentage = $inputs['tax']; 
        $order -> tax_amount = $inputs['taxamt'];
        $order -> order_status = 'completed';
        $order -> created_by = Session::get ( 'userId' );
        $order -> created_at = date ( "Y-m-d H:i:s" );

        $order -> save();
        return $order;
        
    }

    static public function getByPaymentId($pid){
        return Orders::where('payment_no','=', $pid)->get();
    }
    
    static public function getSalesAllocReport($inputs){

            $final_sales_data = array();

            $final_sales_data[] = ['Parent Name', 'Child Name', 'Payment Date', 'Date of Birth','Type(enrollment/Birthday)', 'Name Of Class','Start Date', 'End Date', 'No.Of Classes Selected', '2nd Class', 'Membership', 'Membership Amount', 'Additional Guest Price', 'Additional Halfhour Price','Fees(Enrollement/Birthday)', 'Tax Amount', 'Discount', 'Discount For Siblings', 'Discount for Multi-class','Special Discount', 'Total', 'Mode Of Payment'];
            $Sales['data'] = Orders::where('franchisee_id','=',Session::get('franchiseId'))
                        //->where('student_classes_id','<>',0)
                        ->whereDate('created_at','>=',$inputs['reportGenerateStartdate1'])
                        ->whereDate('created_at','<=',$inputs['reportGenerateEnddate1'])
                        //->where('student_id', '=', '1804')
                        //->groupBy('student_id')
                        ->orderBy('id')
                        ->get();
             $Bday['data'] = BirthdayParties::where('franchisee_id','=',Session::get('franchiseId'))
                        ->whereDate('created_at','>=',$inputs['reportGenerateStartdate1'])
                        ->whereDate('created_at','<=',$inputs['reportGenerateEnddate1'])
                        ->orderBy('id')
                        ->get();    
            for($i=0;$i<count($Sales['data']);$i++){
                $payment_data = PaymentDues::
                                    where('payment_no', '=', $Sales['data'][$i]['payment_no'])
                                    ->where('student_id', '=', $Sales['data'][$i]['student_id'])
                                    ->where('customer_id', '=', $Sales['data'][$i]['customer_id'])
                                    ->where('birthday_id','=', $Sales['data'][$i]['birthday_id'])
                                    ->selectRaw('sum(payments_dues.selected_sessions) as selected_classes, min(start_order_date) as start_date, max(end_order_date) as end_date, class_id, membership_type_id, membership_amount, each_class_amount, tax_percentage, discount_amount, discount_sibling_amount,payment_due_for,payment_due_amount, discount_multipleclasses_amount, discount_admin_amount')
                                    ->get();


                $each_sales_data = array();
                if($payment_data[0]['payment_due_for'] == 'birthday'){
                    $temp=  Customers::find($Sales['data'][$i]['customer_id']);
                    $cus_name = $temp->customer_name.' '.$temp->customer_lastname;
                    $each_sales_data[]= $cus_name;

                    //Collecting Student Data
                    $temp2=  Students::find($Sales['data'][$i]['student_id']);
                    $each_sales_data[]=$temp2->student_name;

                    $temp1 = date_create($Sales['data'][$i]['created_at']);
                    $each_sales_data[] = date_format($temp1,"m/d/Y");

                    $dob = date_create($temp2->student_date_of_birth);
                    $each_sales_data[]=date_format($dob,"m/d/Y");
                    $each_sales_data[]= $payment_data[0]['payment_due_for'];
                    $each_sales_data[]= '***NA***';
                    $temp3 = BirthdayParties::where('student_id','=',$Sales['data'][$i]['student_id'])
                                            ->get();
                    $temp4 = BirthdayBasePrice::where('franchisee_id','=',Session::get('franchiseId'))->selectRaw('default_advance_amount')
                                      ->get();
                    
                    $each_sales_data[] = $temp3[0]['birthday_party_date'];
                    $each_sales_data[] = $temp3[0]['birthday_party_date'];
                    $each_sales_data[] = 'NA';
                    $each_sales_data[] = 'NA';
                    $each_sales_data[]= 'NA';
                    $each_sales_data[] = 'NA';
                    $fees = $Sales['data'][$i]['amount'];
                    if($temp4[0]['default_advance_amount'] != $fees){                      
                            $each_sales_data[] = $temp3[0]['additional_guest_price'];
                            $each_sales_data[] = $temp3[0]['additional_halfhour_price'];
                      
                    }else{
                        $each_sales_data[] = '0';
                        $each_sales_data[] = '0';
                    }
                    $each_sales_data[] =  $fees;
                    $total = ($Sales['data'][$i]['amount']);
                    
                    $tax_amt = (($total)/100) * $payment_data[0]['tax_percentage'];
                    if($tax_amt != ''){
                        $each_sales_data[] = $tax_amt;
                    }else{
                        $each_sales_data[] = '0';
                    }
                    $each_sales_data[] = 'NA';
                    $each_sales_data[] = 'NA';
                    $each_sales_data[] = 'NA';
                    $each_sales_data[] = 'NA';
                    $each_sales_data[] = $total + $tax_amt;
                    $each_sales_data[]= $Sales['data'][$i]['payment_mode'];
                    $final_sales_data[] = $each_sales_data;


                    }
                if($payment_data[0]['payment_due_for'] == 'enrollment'){
                                //Collecting customer Data
                    $temp=  Customers::find($Sales['data'][$i]['customer_id']);
                    $cus_name = $temp->customer_name.' '.$temp->customer_lastname;
                    $each_sales_data[]= $cus_name;

                    //Collecting Student Data
                    $temp2=  Students::find($Sales['data'][$i]['student_id']);
                    $each_sales_data[]=$temp2->student_name;

                    $temp1 = date_create($Sales['data'][$i]['created_at']);
                    $each_sales_data[] = date_format($temp1,"m/d/Y");

                    $dob = date_create($temp2->student_date_of_birth);
                    $each_sales_data[]=date_format($dob,"m/d/Y");


                    //$each_sales_data[] = '';//new retention
                   
                    $each_sales_data[]= $payment_data[0]['payment_due_for'];
                    // if($payment_data[0]['payment_due_for'] == 'enrollment'){
                       $get_class_name = Classes::where('id', '=', $payment_data[0]['class_id'])->select('class_name')->get();
                    // }else{
                    //     $get_class_name = 'Birthday Payment';
                    // }
                    
                    $get_class_name = array_filter(json_decode(json_encode($get_class_name),TRUE));
                    $class_name = "";
                    if (!empty($get_class_name)) {
                        $class_name = $get_class_name[0]['class_name'];
                    }
                    $each_sales_data[]= $class_name;
                    $each_sales_data[]= date_format(new Carbon($payment_data[0]['start_date']), 'F d Y');
                    $each_sales_data[]= date_format(new Carbon($payment_data[0]['end_date']), 'F d Y');
                    $each_sales_data[]= $payment_data[0]['selected_classes'];

                if ((int)$payment_data[0]['discount_multipleclasses_amount'])
                                    $each_sales_data[]= "Yes";//2nd class
                                else
                                    $each_sales_data[]= "No";//2nd class

                                $membership_amount = $payment_data[0]['membership_amount'];

                                $mem_name = $membership_amount == "5000" ? "Lifetime Membership" : "Annual Membership";

                                //$mem_name = $payment_data[0]['membership_name'] !== "" ? $payment_data[0]['membership_name'] : "Annual Membership";
                                $each_sales_data[]= $mem_name;
                                $each_sales_data[]= $membership_amount;

                                //$membership_amount;
                                /*$checkUser = Orders::checkNameExist($final_sales_data, $cus_name);
                                if($checkUser)
                                    $membership_amount = "-";
                                else
                                    $membership_amount = $mem_name == "Annual Membership" ? 2000 : 5000;
                                
                                $each_sales_data[]= $membership_amount;*/

                                //$fees = $payment_data[0]['each_class_amount'] * $payment_data[0]['selected_classes'];
                                $total_amt_after_disc = $Sales['data'][$i]['amount'] + $membership_amount - $payment_data[0]['discount_amount']-$payment_data[0]['discount_admin_amount'] -  $payment_data[0]['discount_sibling_amount'] - $payment_data[0]['discount_multipleclasses_amount'];
                                

                                
                                $each_sales_data[]= '0';
                                $each_sales_data[]= '0';
                                $each_sales_data[]= $Sales['data'][$i]['amount'];

                                $tax_amt = (($total_amt_after_disc)/100) * $payment_data[0]['tax_percentage'];

                                $each_sales_data[]= number_format($tax_amt, 2, '.', '');
                                $each_sales_data[]= $payment_data[0]['discount_amount'];
                                $each_sales_data[]= $payment_data[0]['discount_sibling_amount'];
                                $each_sales_data[]= $payment_data[0]['discount_multipleclasses_amount'];
                                $each_sales_data[]= $payment_data[0]['discount_admin_amount'];


                                $each_sales_data[]= number_format($total_amt_after_disc + $tax_amt , 2, '.', '');;
                                $each_sales_data[]= $Sales['data'][$i]['payment_mode'];

                                $final_sales_data[] = $each_sales_data;
                                           //array_push($final_sales_data, $each_sales_data);      
                            }


                        }
                        return $final_sales_data;
                    }


    public static function checkNameExist($array_name, $cus_name){
        $final_array = array();
        foreach ($array_name as $key => $value) {
            if(in_array($cus_name, $value)){
                array_push($final_array, 'undi');
            }else{
                array_push($final_array, 'ledu');
            }
        }

        if(in_array('undi' ,$final_array)){
            return true;
        }else{
            return false;
        }
    }
}