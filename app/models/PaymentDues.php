<?php
use Carbon\Carbon;

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
		$paymentDues->season_id            = $inputs['seasonId'];
		$paymentDues->customer_id          = $inputs['customer_id'];
    $paymentDues->franchisee_id        = Session::get('franchiseId');
		$paymentDues->batch_id             = $inputs['batch_id'];
		$paymentDues->class_id             = $inputs['class_id'];
    $paymentDues->student_class_id     = $inputs['student_class_id'];
      if(isset($inputs['membership_id']) && array_key_exists('membership_id',$inputs)
        && $inputs['membership_id']!=''){

        $paymentDues->membership_id=$inputs['membership_id'];
        $paymentDues->membership_type_id=$inputs['membership_type_id'];
        $paymentDues->membership_amount=$inputs['membership_amount'];
        if(isset($inputs['membership_name']) && array_key_exists('membership_name',$inputs)
           && $inputs['membership_name']!=''){

          $paymentDues->membership_name=$inputs['membership_name'];
                    
        }
      }
		$paymentDues->payment_due_amount   = $inputs['payment_due_amount'];
      if(isset($inputs['payment_due_amount_after_discount']) && 
         array_key_exists('payment_due_amount_after_discount',$inputs) && 
         $inputs['payment_due_amount_after_discount']!=''){

        $paymentDues->payment_due_amount_after_discount   = $inputs['payment_due_amount_after_discount'];
      
      }
      if(isset($inputs['tax']) && array_key_exists('tax',$inputs) && $inputs['tax']!='' ){
        $paymentDues->tax_percentage=$inputs['tax'];
      }
		$paymentDues->payment_type         = 'singlepay';
    $paymentDues->payment_due_for      = 'enrollment';
		$paymentDues->payment_status       = $inputs['payment_status'];
		$paymentDues->selected_sessions    = $inputs['selected_sessions'];
      if(isset($inputs['payment_batch_amount']) && array_key_exists('payment_batch_amount',$inputs) && $inputs['payment_batch_amount']!=''){

         $paymentDues->payment_batch_amount=$inputs['payment_batch_amount'];
      }
      if(isset($inputs['discount_multipleclasses_amount']) && array_key_exists('discount_multipleclasses_amount',$inputs) && $inputs['discount_multipleclasses_amount']!=''){
        
        $paymentDues->discount_multipleclasses_amount    = $inputs['discount_multipleclasses_amount'];
      }
      if(isset($inputs['discount_sibling_amount']) && array_key_exists('discount_sibling_amount',$inputs) && $inputs['discount_sibling_amount']!=''){
        
        $paymentDues->discount_sibling_amount    = $inputs['discount_sibling_amount'];
      }
      if(isset($inputs['discount_sibling_applied']) && array_key_exists('discount_sibling_applied',$inputs) && $inputs['discount_sibling_applied']!=''){
                 
        $paymentDues->discount_sibling_applied=$inputs['discount_sibling_applied'];
      }
      if(isset($inputs['discount_multipleclasses_applied']) && array_key_exists('discount_multipleclasses_applied',$inputs) && $inputs['discount_multipleclasses_applied']!=''){
        
        $paymentDues->discount_multipleclasses_applied=$inputs['discount_multipleclasses_applied'];
      }
      if(isset($inputs['discount_admin_amount'])  && array_key_exists('discount_admin_amount',$inputs) && $inputs['discount_admin_amount']!=''){
        
        $paymentDues->discount_admin_amount=$inputs['discount_admin_amount'];
      }else{
        $paymentDues->discount_admin_amount=0;
      }
      if(isset($inputs['each_class_cost']) && array_key_exists('each_class_cost',$inputs) && $inputs['each_class_cost']!=''){
        
        $paymentDues->each_class_amount=$inputs['each_class_cost'];
      }
      if(isset($inputs['selected_order_sessions']) && array_key_exists('selected_order_sessions',$inputs) && $inputs['selected_order_sessions']!=''){

        $paymentDues->selected_order_sessions    = $inputs['selected_order_sessions'];
      }
      if(isset($inputs['start_order_date']) && array_key_exists('start_order_date',$inputs) && $inputs['start_order_date']!=''){
        
        $paymentDues->start_order_date     =$inputs['start_order_date'];
      }
      if(isset($inputs['end_order_date']) && array_key_exists('end_order_date',$inputs) && $inputs['end_order_date']!='' ){
          
        $paymentDues->end_order_date=$inputs['end_order_date'];
      }
      if(isset($inputs['discount_amount']) && array_key_exists('discount_amount',$inputs) && $inputs['discount_amount']!=''){

        $paymentDues->discount_amount=$inputs['discount_amount'];
      }
      
      $paymentDues->discount_applied     = $inputs['discount_applied'];
		  $paymentDues->created_by              = Session::get('userId');

      if(isset($inputs['created_at']) && array_key_exists('created_at',$inputs) && $inputs['created_at']!=''){
                    $paymentDues->created_at = $inputs['created_at'];
      }else{
		                $paymentDues->created_at              = date("Y-m-d H:i:s");
      }
                $paymentDues->save();
		
		return $paymentDues;
		
	}
	
	static function getAllPaymentDuesByStudent($studentId){
		
		return PaymentDues::with('Batches')->where('student_id','=',$studentId)->get();
		
		
	}
                                    
        static function getAllDue($studentId){
            return PaymentDues::
                                      where('student_id','=',$studentId)
                                    ->where('payment_status','=','pending')
                                    ->where('payment_due_for','=','enrollment')
                                    ->Orderby('id','DESC')
                                    ->get();
        }
        static function createBirthdaypaymentFirstdues($addBirthday){
                $paymentDues = new PaymentDues();
		$paymentDues->student_id           = $addBirthday['student_id'];
                $paymentDues->birthday_id          = $addBirthday['id'];
		$paymentDues->customer_id          = $addBirthday['customer_id'];
		$paymentDues->franchisee_id        = Session::get('franchiseId');
                $paymentDues->payment_due_amount   = $addBirthday['advance_amount_paid'];
		$paymentDues->discount_amount      = $addBirthday['discount_amount'];
                if(isset($addBirthday['membership_id'])){
                    $paymentDues->membership_id=$addBirthday['membership_id'];
                }
                if(isset($addBirthday['membership_amount'])){
                    $paymentDues->membership_amount=$addBirthday['membership_amount'];
                }
                if(isset($addBirthday['taxpercent'])){
                    $paymentDues->tax_percentage=$addBirthday['taxpercent'];
                }
                $paymentDues->payment_type = $addBirthday['payment_type'];
                $paymentDues->payment_status       = 'paid';
		//$paymentDues->discount_applied     = $taxAmtapplied;
                $paymentDues->payment_due_for       ="birthday";
		$paymentDues->created_by           = Session::get('userId');
		$paymentDues->created_at           = date("Y-m-d H:i:s");
		$paymentDues->save();		
		return $paymentDues;
        }
        static function createBirthdaypaymentdues($addBirthday){
                $paymentDues = new PaymentDues();
		$paymentDues->student_id           = $addBirthday['student_id'];
                $paymentDues->birthday_id          = $addBirthday['id'];
		$paymentDues->customer_id          = $addBirthday['customer_id'];
                $paymentDues->franchisee_id        = Session::get('franchiseId');
                if(isset($addBirthday['membership_id'])){
               //     $paymentDues->membership_id=$addBirthday['membership_id'];
                }
                if(isset($addBirthday['membership_amount'])){
               //     $paymentDues->membership_amount=$addBirthday['membership_amount'];
                }
		$paymentDues->payment_due_amount   = $addBirthday['remaining_due_amount'];
                if($addBirthday['remaining_due_amount']!='0'){
                    $paymentDues->payment_type         = 'bipay';
                    $paymentDues->payment_status       = 'pending';
                }else{
                    $paymentDues->payment_type         = 'singlepay';
                    $paymentDues->payment_status       = 'paid';
                }
                if(isset($addBirthday['taxpercent'])){
                    $paymentDues->tax_percentage=$addBirthday['taxpercent'];
                }
		//$paymentDues->discount_applied     = $taxAmtapplied;
                $paymentDues->payment_due_for       ="birthday";
		$paymentDues->created_by           = Session::get('userId');
		$paymentDues->created_at           = date("Y-m-d H:i:s");
		$paymentDues->save();		
		return $paymentDues;
        }
        static function getPaymentpendingfulldata($id){
            return PaymentDues:: where ('customer_id','=',$id)
                                 ->where ('payment_status','=','pending')
                                 ->where('payment_due_for','=','birthday')
                                 ->Orderby('id','DESC')
                                 ->get();
            
        }
        static function getPaymentpendingdata($id){
            return PaymentDues::where('id','=',$id)
                    ->where('payment_status','=','pending')
                    ->get();
        }
        static function changeStatustopaid($pendingId,$discountAmount){
            $pending=PaymentDues::where('id','=',$pendingId)->update(array('payment_status'=> 'paid','discount_amount'=>$discountAmount));
            return $pending;
            
    }
    static function getAllDuebyStudentId($student_id){
        return PaymentDues:: where('student_id','=',$student_id)
                            ->where('payment_due_for','=','enrollment')
                            ->where('payment_status','=','pending')
                            ->where('student_class_id','!=','0')
                            ->get();
    }
    static function getAllPaymentsMade($student_id){
        return PaymentDues::where('student_id','=',$student_id)
                            ->where('payment_due_for','=','enrollment')
                            ->where('payment_status','=','paid')
                            ->where('student_class_id','!=','0')
                            ->get();
    }

    static function getAllBirthdayPaymentsforActivityReport($inputs){
        $birthdayActivityReportDetails['data'] =PaymentDues::where('franchisee_id','=',Session::get('franchiseId'))
                          ->where('payment_status','=','paid')
                         
                          ->whereDate('created_at','>=',$inputs['reportStartDate'])
                          ->whereDate('created_at','<=',$inputs['reportEndDate'])
                          ->select('student_id','customer_id','created_at')
                          ->orderBy('id','desc')
                          ->get();
        for($i = 0; $i < count($birthdayActivityReportDetails['data']); $i++){

            $temp = Customers::find($birthdayActivityReportDetails['data'][$i]['customer_id']);

            $birthdayActivityReportDetails['data'][$i]['customer_name'] = $temp->customer_name." ".$temp->customer_lastname;

            $temp2 = Students::find($birthdayActivityReportDetails['data'][$i]['student_id']);

            $birthdayActivityReportDetails['data'][$i]['student_name'] = $temp2->student_name;

            $temp3 = PaymentDues::where('student_id','=',$birthdayActivityReportDetails['data'][$i]['student_id'])->select('payment_due_for')->get();
            //print_r($temp3); die();
            for($j = 0; $j < count($temp3); $j++){
              if($temp3[$j]['payment_due_for'] == 'enrollment'){
                $temp4 = StudentClasses::where('student_id','=',$birthdayActivityReportDetails['data'][$i]['student_id'])
                                    ->select('enrollment_start_date')
                                    ->orderby('created_at','DESC')
                                    ->get();
                $birthdayActivityReportDetails['data'][$i]['payment_due_for']='ENROLLMENT';
                $birthdayActivityReportDetails['data'][$i]['created_at']=$temp4[0]['enrollment_start_date'];
              }
              if($temp3[$j]['payment_due_for'] == 'birthday'){
                $temp5 = BirthdayParties::where('student_id','=',$birthdayActivityReportDetails['data'][$i]['student_id'])->select('birthday_party_date')->get();
                $birthdayActivityReportDetails['data'][$i]['payment_due_for']='BIRTHDAY';
                $birthdayActivityReportDetails['data'][$i]['created_at']=$temp5[0]['birthday_party_date'];
              }
            }

        }
        //print_r($temp3[$i]['payment_due_for']); die();
        return $birthdayActivityReportDetails;
    }
    
    static function getAllBirthdayPaymentsforReport($inputs){
        $birthdayReportDetails['data'] =PaymentDues::where('payment_due_for','=','birthday')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('birthday_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                 //   ->orderBy('birthday_party_date','desc')
                                                    ->get();
        for($i=0;$i<count($birthdayReportDetails['data']);$i++){
            $temp=  Customers::find($birthdayReportDetails['data'][$i]['customer_id']);
            $birthdayReportDetails['data'][$i]['customer_name']=$temp->customer_name." ".$temp->customer_lastname;
            $temp2=  Students::find($birthdayReportDetails['data'][$i]['student_id']);
            $birthdayReportDetails['data'][$i]['student_name']=$temp2->student_name;
        }
        $birthdayReportDetails['totalAmount']=PaymentDues::where('payment_due_for','=','birthday')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('birthday_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->sum('payment_due_amount');
        $birthdayReportDetails['membershipAmount']=PaymentDues::where('payment_due_for','=','birthday')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('birthday_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->sum('membership_amount');
        return $birthdayReportDetails;
    }
    
    static function getAllEnrollmentPaymentsforReport($inputs){
        $enrollmentReportDetails['data']=PaymentDues::where('payment_due_for','=','enrollment')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('student_class_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->orderBy('created_at','desc')
                                                    ->get();
        for($i=0;$i<count($enrollmentReportDetails['data']);$i++){
            $temp=  Customers::find($enrollmentReportDetails['data'][$i]['customer_id']);
            $enrollmentReportDetails['data'][$i]['customer_name']=$temp->customer_name." ".$temp->customer_lastname;
	    if (isset($temp->source) || !empty($temp->source)){
              $enrollmentReportDetails['data'][$i]['source'] = $temp->source;
            } else {
              $enrollmentReportDetails['data'][$i]['source'] = '';
            }
            $temp2=  Students::find($enrollmentReportDetails['data'][$i]['student_id']);
            $enrollmentReportDetails['data'][$i]['student_name']=$temp2->student_name;
            $temp3= Batches::find($enrollmentReportDetails['data'][$i]['batch_id']);
            $enrollmentReportDetails['data'][$i]['batch_name']=$temp3['batch_name'];
        }
        $enrollmentReportDetails['totalAmount']=PaymentDues::where('payment_due_for','=','enrollment')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('student_class_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
						    ->selectRaw('sum(selected_order_sessions) as selected_order_sessions, sum(payment_due_amount_after_discount) as payment_due_amount_after_discount')
						    ->groupBy('created_at')
						    ->get();
        $enrollmentReportDetails['membershipAmount']=PaymentDues::where('payment_due_for','=','enrollment')
                                                    ->where('payment_status','=','paid')
                                                    ->where('birthday_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->sum('membership_amount');
        return $enrollmentReportDetails;
        
    }
    static function getAllEnrollmentBirthdayPaymentsforReport($inputs){
        $reportData['data']=PaymentDues::whereIn('payment_due_for',array('enrollment','birthday'))
                                 ->where('franchisee_id','=',Session::get('franchiseId'))
                                 ->where('payment_status','=','paid')
                                 ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                 ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                 ->orderBy('id','desc')
                                 ->get();
        for($i=0;$i<count($reportData['data']);$i++){
            $temp=  Customers::find($reportData['data'][$i]['customer_id']);
            $reportData['data'][$i]['customer_name']=$temp->customer_name." ".$temp->customer_lastname;
            $temp2= Students::find($reportData['data'][$i]['student_id']);
            $reportData['data'][$i]['student_name']=$temp2->student_name;
            if($reportData['data'][$i]['batch_id']!=null){
                $temp3= Batches::find($reportData['data'][$i]['batch_id']);
                $reportData['data'][$i]['batch_name']=$temp3->batch_name;
            }
        }
        $reportData['totalAmount']=PaymentDues::where('payment_due_for','=','birthday')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('birthday_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->sum('payment_due_amount');
        $reportData['totalAmount']+=PaymentDues::where('payment_due_for','=','enrollment')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('student_class_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->sum('payment_due_amount_after_discount');
        $reportData['membershipAmount']=PaymentDues::whereIn('payment_due_for',array('enrollment','birthday'))
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->sum('membership_amount');
        return $reportData;
    }
  static function getAllMembershipPaymentsforReport($inputs){
      $membership['data']=PaymentDues::whereIn('payment_due_for',array('enrollment','birthday'))
                                        ->where('franchisee_id','=',Session::get('franchiseId'))
                                        ->where('payment_status','=','paid')
                                        ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                        ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                        ->where('membership_id','<>',0)
                                        ->orderBy('created_at','desc')
                                        ->get();
      for($i=0;$i<count($membership['data']);$i++){
            $temp=  Customers::find($membership['data'][$i]['customer_id']);
            $membership['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
            $temp2=  Students::find($membership['data'][$i]['student_id']);
            $membership['data'][$i]['student_name']=$temp2->student_name;
        }
        $membership['membershipAmount']=PaymentDues::whereIn('payment_due_for',array('enrollment','birthday'))
                                        ->where('franchisee_id','=',Session::get('franchiseId'))
                                        ->where('payment_status','=','paid')
                                        ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                        ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                        ->where('membership_id','<>',0)
                                        ->sum('membership_amount');
        return $membership;
  }
  
  static function getWeeklyEnrollmentReport(){
      $now = Carbon::now();
      $enrollmentReportDetails['data']=PaymentDues::where('payment_due_for','=','enrollment')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('student_class_id','<>',0)
                                                    ->whereDate('created_at','>=',new Carbon('last monday'))
                                                    ->whereDate('created_at','<=',$now->toDateString())
                                                    ->orderBy('id','desc')
                                                    ->get();
      for($i=0;$i<count($enrollmentReportDetails['data']);$i++){
            $temp=  Customers::find($enrollmentReportDetails['data'][$i]['customer_id']);
            $enrollmentReportDetails['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
            $temp2=  Students::find($enrollmentReportDetails['data'][$i]['student_id']);
            $enrollmentReportDetails['data'][$i]['student_name']=$temp2->student_name;
            $temp3= Batches::find($enrollmentReportDetails['data'][$i]['batch_id']);
            $enrollmentReportDetails['data'][$i]['batch_name']=$temp3->batch_name;
            $temp4= Orders::where('payment_no','=',$enrollmentReportDetails['data'][$i]['payment_no'])->get();
            $franchisee_name=Franchisee::find(Session::get('franchiseId'));
            
            $yrdata= strtotime($enrollmentReportDetails['data'][$i]['created_at']);
                                                  
                                                  
                                                  switch (strlen($temp4[0]['id'])){
                                                    
                                                    case 1:
                                                        $enrollmentReportDetails['data'][$i]['invoice_no']= 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|00000'.$temp4[0]['id'];
                                                        break;
                                                    case 2:
                                                        $enrollmentReportDetails['data'][$i]['invoice_no']= 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|0000'.$temp4[0]['id'];
                                                        break;
                                                    case 3:
                                                        $enrollmentReportDetails['data'][$i]['invoice_no']= 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|000'.$temp4[0]['id'];
                                                        break;
                                                    case 4: 
                                                        $enrollmentReportDetails['data'][$i]['invoice_no']= 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|00'.$temp4[0]['id'];
                                                        break;
                                                    case 5:
                                                        $enrollmentReportDetails['data'][$i]['invoice_no']= 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|0'.$temp4[0]['id'];
                                                        break;
                                                    default:
                                                        $enrollmentReportDetails['data'][$i]['invoice_no']= $temp4[0]['id'];
                                                        break;
                                                    }
           
            
      }
      return $enrollmentReportDetails;
  }
  static public function getBySchoolEnrollmentReport($inputs){
      $student_data=Students::where('school','=',$inputs['reportOptionSelect'])->select('id')->get();
      
      for($i=0;$i<count($student_data);$i++){
        $student_ids[]=$student_data[$i]['id'];                                            
      }
      
        $enrollmentReportDetails['data']=PaymentDues::whereIn('student_id',$student_ids)
                                                    ->where('payment_due_for','=','enrollment')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('student_class_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->orderBy('id','desc')
                                                    ->get();
        for($i=0;$i<count($enrollmentReportDetails['data']);$i++){
            $temp=  Customers::find($enrollmentReportDetails['data'][$i]['customer_id']);
            $enrollmentReportDetails['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
            $temp2=  Students::find($enrollmentReportDetails['data'][$i]['student_id']);
            $enrollmentReportDetails['data'][$i]['student_name']=$temp2->student_name;
            $temp3= Batches::find($enrollmentReportDetails['data'][$i]['batch_id']);
            $enrollmentReportDetails['data'][$i]['batch_name']=$temp3->batch_name;
        }
        
        return $enrollmentReportDetails;
  }
  
  static public function getByLocalityEnrollmentReport($inputs){
      $customer_data=Customers::where('locality','=',$inputs['reportOptionSelect'])->select('id')->get();
      
      for($i=0;$i<count($customer_data);$i++){
        $customer_ids[]=$customer_data[$i]['id'];                                            
      }
        $enrollmentReportDetails['data']=PaymentDues::whereIn('customer_id',$customer_ids)
                                                    ->where('payment_due_for','=','enrollment')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('student_class_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->orderBy('id','desc')
                                                    ->get();
        for($i=0;$i<count($enrollmentReportDetails['data']);$i++){
            $temp=  Customers::find($enrollmentReportDetails['data'][$i]['customer_id']);
            $enrollmentReportDetails['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
            $temp2=  Students::find($enrollmentReportDetails['data'][$i]['student_id']);
            $enrollmentReportDetails['data'][$i]['student_name']=$temp2->student_name;
            $temp3= Batches::find($enrollmentReportDetails['data'][$i]['batch_id']);
            $enrollmentReportDetails['data'][$i]['batch_name']=$temp3->batch_name;
        }
        
        return $enrollmentReportDetails;
  }
  static public function getByApartmentEnrollmentReport($inputs){
      $customer_data=Customers::where('apartment_name','=',$inputs['reportOptionSelect'])->select('id')->get();
      
      for($i=0;$i<count($customer_data);$i++){
        $customer_ids[]=$customer_data[$i]['id'];                                            
      }
        $enrollmentReportDetails['data']=PaymentDues::whereIn('customer_id',$customer_ids)
                                                    ->where('payment_due_for','=','enrollment')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('student_class_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->orderBy('id','desc')
                                                    ->get();
        for($i=0;$i<count($enrollmentReportDetails['data']);$i++){
            $temp=  Customers::find($enrollmentReportDetails['data'][$i]['customer_id']);
            $enrollmentReportDetails['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
            $temp2=  Students::find($enrollmentReportDetails['data'][$i]['student_id']);
            $enrollmentReportDetails['data'][$i]['student_name']=$temp2->student_name;
            $temp3= Batches::find($enrollmentReportDetails['data'][$i]['batch_id']);
            $enrollmentReportDetails['data'][$i]['batch_name']=$temp3->batch_name;
        }
        return $enrollmentReportDetails;
  }


  static public function createMembershipPaymentDues($inputs) {

        $paymentDues = new PaymentDues();
        $paymentDues->customer_id          = $inputs['customer_id'];
        $paymentDues->franchisee_id        = Session::get('franchiseId');
            if(isset($inputs['membership_id'])){
                    $paymentDues->membership_id=$inputs['membership_id'];
                    $paymentDues->membership_type_id=$inputs['membership_type_id'];
                    $paymentDues->membership_amount=$inputs['membership_amount'];
                    $paymentDues->membership_name=$inputs['membership_name'];
            }
        $paymentDues->payment_due_amount   = $inputs['payment_due_amount'];
                if(isset($inputs['payment_due_amount_after_discount'])){
                  $paymentDues->payment_due_amount_after_discount   = $inputs['payment_due_amount_after_discount'];
                }
                if(isset($inputs['tax'])){
                    $paymentDues->tax_percentage=$inputs['tax'];
                }
        $paymentDues->payment_type         = 'singlepay';
        $paymentDues->payment_due_for      = 'membership';
        $paymentDues->payment_status       = 'paid';
        
        $paymentDues->created_by              = Session::get('userId');
                if(isset($inputs['created_at'])){
                    $paymentDues->created_at = $inputs['created_at'];
                }else{
        $paymentDues->created_at              = date("Y-m-d H:i:s");
                }
                $paymentDues->save();
        
        return $paymentDues;
  }

  static public function getSalesAllocReport($inputs){

    $final_sales_data = [];
    $Sales['data']=PaymentDues::where('franchisee_id','=',Session::get('franchiseId'))
            ->where('payment_status','=','paid')
            ->where('student_class_id','<>',0)
            ->whereDate('created_at','>=',$inputs['reportGenerateStartdate1'])
            ->whereDate('created_at','<=',$inputs['reportGenerateEnddate1'])
            ->orderBy('id','desc')
            ->get();       

    for($i=0;$i<count($Sales['data']);$i++){

        $each_sales_data = [];

        $temp5 = PaymentMaster::where('payment_master.payment_no', '=', $Sales['data'][$i]['payment_no'])->join('orders', 'orders.payment_no', '=', 'payment_master.payment_no')->get();

        /***********  Putting values in fixed order for excel sheet  **********/
        $each_sales_data[] = "";
        if (isset($temp5[0]->invoice_id) && !empty($temp5[0]->invoice_id))
            $each_sales_data[] = $temp5[0]->invoice_id;
        else
            $each_sales_data[] = 0;
        //$each_sales_data[] = 0;
        $billing_date = date_create($Sales['data'][$i]['created_at']);
        $each_sales_data[] = date_format($billing_date,"m/d/Y");

        //Collecting customer Data
        $temp=  Customers::find($Sales['data'][$i]['customer_id']);

        //Collecting Student Data
        $temp2=  Students::find($Sales['data'][$i]['student_id']);
        $dob = date_create($temp2->student_date_of_birth);
        $each_sales_data[]=date_format($dob,"m/d/Y");
        $each_sales_data[]=$temp2->student_name;
        $each_sales_data[]=$temp->customer_name.' '.$temp->customer_lastname;

        //Collecting Classes Data
        $temp4 = Classes::getstudentclasses($Sales['data'][$i]['class_id']);
        $each_sales_data[] = $temp4[0]->class_name;
        $sDate = new Carbon($Sales['data'][$i]['start_order_date']);
        $eDate = new Carbon($Sales['data'][$i]['end_order_date']);
        $each_sales_data[] = $Sales['data'][$i]['selected_order_sessions'];
        $each_sales_data[] = "";
        $each_sales_data[] = date_format($sDate,"m/d/Y");
        $each_sales_data[] = date_format($eDate, 'F d Y');
        $mem_name = $Sales['data'][$i]['membership_name'] !== "" ? $Sales['data'][$i]['membership_name'] : "Annual Membership";
        $each_sales_data[] = $mem_name;
        $each_sales_data[] = $mem_name == "Annual Membership" ? 2000 : 5000;
        $each_sales_data[] = $Sales['data'][$i]['selected_order_sessions'];
        $each_sales_data[] = $Sales['data'][$i]['discount_amount'];
        $each_sales_data[] = $Sales['data'][$i]['discount_sibling_amount'];
        $each_sales_data[] = $Sales['data'][$i]['discount_multipleclasses_amount'];
        $each_sales_data[] = $Sales['data'][$i]['tax_percentage'].' %';
        $total = $Sales['data'][$i]['each_class_amount'] * $Sales['data'][$i]['selected_order_sessions'];
        $tax_amt = $total/100*$Sales['data'][$i]['tax_percentage'];
        $each_sales_data[] = $tax_amt;
        $each_sales_data[] = $total;
        $each_sales_data[] = $temp5[0]->payment_mode;

        //Apending array to final array
        $final_sales_data[] = $each_sales_data;
    }
    return $final_sales_data;

  }
	
  static function getCurrentWeekRenewalsDue($presentdate, $endOfWeek){
	$end_date = date('Y-m-d', $endOfWeek);
     	$classes = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
			      ->where('end_order_date', '<=', $presentdate)
		     	      ->where('end_order_date', '>=', $end_date)
			      ->where('payment_due_for','=', 'enrollment')
			      ->groupBy('student_id')
			      ->get();	
	return count($classes);
  }  

  static function getWeekWiseRenewalsDue($start_date, $end_date){
	$classes = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
			      ->where('end_order_date', '<=', $end_date)
			      ->where('end_order_date', '>=', $start_date)
			      ->where('payment_due_for','=', 'enrollment')
			      ->groupBy('student_id')
			      ->get();
	return count($classes);

  }

  static function getCurrentMonthRenewalsDue($presentdate, $currentMonth){
  	$classes = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
  			      ->where('end_order_date', '<=', $presentdate)
              ->where('end_order_date', '>=', $currentMonth)
  			      ->where('payment_due_for','=', 'enrollment')
  			      ->groupBy('student_id')
              ->get();
    return $classes;
  }
   
  static function insertPaymentsData($customerId,$inputs,$typeOfClass,$totalAmount,$payment_no,$end_date,$userId,$presentDate){
	$userId =  Session::get('userId');
         $data = new PaymentDues();
         $data->franchisee_id = Session::get('franchiseId');
         $data->customer_id = $customerId[0]->customer_id;
         $data->student_id = $inputs['studentId'];
         $data->payment_due_for = $typeOfClass;
         $data->each_class_amount = $inputs['amountForSummer'];
         $data->payment_due_amount = $totalAmount;
         $data->payment_due_amount_after_discount =  $inputs['totalAmountForSummer'];
         $data->discount_applied = $inputs['discountPercentageForSummer'];
         $data->tax_percentage = $inputs['taxPercentageForSummer'];
         $data->payment_type = 'singlepay';
         $data->payment_status = 'paid';
         $data->payment_no = $payment_no;
         $data->selected_sessions = $inputs['NoOfWeeksForSummer'];
         $data->start_order_date = $inputs['startDateForSummer'];
         $data->end_order_date = $end_date;
         $data->created_by = $userId;
         $data->created_at = $presentDate;
         $data->updated_at = $presentDate;
         $data->save();

	 return $data;
  }

  static public function getNoOfNewEnrollments($presentdate, $currentMonthStartDate) {
     $students = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                            ->where('payment_due_for', '=', 'enrollment')
                            ->whereDate('created_at', '>=', $currentMonthStartDate)
                            ->whereDate('created_at', '<=', '2018-07-09')
                            ->groupBy('student_id')
                            ->get();
     $student_id = [];
     if (isset($students) || !empty($students)) {
       foreach($students as $student){
            $student_id[] = $student['student_id'];
       }
       $student_data = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                              ->where('payment_due_for', '=', 'enrollment')
                              ->whereDate('created_at', '<=', '2018-07-09')
                              // ->whereNotIn('student_id', $student_id)
                              ->groupBy('student_id')
                              ->get();
       $id = array();
       foreach($student_data as $student){
            $id[] = $student['student_id'];
       }          
       $result = array_diff($student_id,$id);                
       return count($result);
     } else {
       return 0;
     }
  }

  static public function getTotalEnrollments() {
    $single = StudentClasses::getSingleEnrolledList();
    $multiple = StudentClasses::getMultipleEnrolledList();
    $totalCurrentEnrolled = $single + $multiple;
    return $totalCurrentEnrolled;
  }

  static public function getMarketingBudget($presentdate, $currentMonthStartDate) {
    $currentMonth = date('M',strtotime($presentdate));
    $currentYear = date('Y',strtotime($presentdate));
    $getDataForThisYM = MarketingBudget::where('franchisee_id', '=', Session::get('franchiseId'))
                                       ->where('year', '=', $currentYear)
                                       ->where('month', '=', $currentMonth)
                                       ->get();
    if (count($getDataForThisYM) > 0) {
      if (isset($getDataForThisYM[0]['budget_amount']) && !empty($getDataForThisYM[0]['budget_amount'])) {
        return $getDataForThisYM[0]['budget_amount'];
      } else {
        return 0;
      }
    } else {
      return 0;
    }
  }
  static function getNoOfRenwalsDone($presentdate, $currentMonth){
    $renewals = PaymentDues::getCurrentMonthRenewalsDue($presentdate, $currentMonth);
    $student_id = array();
    if (isset($renewals) && !empty($renewals)) {
      foreach($renewals as $renew) {
        array_push($student_id, $renew['student_id']);
      }
      $classes = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                ->whereDate('created_at', '<=', $presentdate)
                ->whereDate('created_at', '>=', $currentMonth)
                ->whereIn('student_id',$student_id)
                ->where('payment_due_for','=', 'enrollment')
                ->groupBy('student_id')
                ->get();
      return $classes;
    } else {
      return 0;
    }
  }

  static function getRenewalsDueReport($inputs){
    $enrollmentReportDetails['data'] = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
              ->whereDate('end_order_date', '<=', $inputs['reportGenerateEnddate'])
              ->whereDate('end_order_date', '>=', $inputs['reportGenerateStartdate'])
              ->where('payment_due_for','=', 'enrollment')
              ->groupBy('student_id')
              ->get();

    for($i=0;$i<count($enrollmentReportDetails['data']);$i++){
        $temp=  Customers::find($enrollmentReportDetails['data'][$i]['customer_id']);
        $enrollmentReportDetails['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
        $temp2=  Students::find($enrollmentReportDetails['data'][$i]['student_id']);
        $enrollmentReportDetails['data'][$i]['student_name']=$temp2->student_name;
        $temp3= Batches::find($enrollmentReportDetails['data'][$i]['batch_id']);
        $enrollmentReportDetails['data'][$i]['batch_name']=$temp3['batch_name'];
    }
    return $enrollmentReportDetails;
  }

  static function getRenewalsDoneReport ($inputs) {
    $enrollmentReportDetails['data'] = PaymentDues::getNoOfRenwalsDone($inputs['reportGenerateEnddate'], $inputs['reportGenerateStartdate']);
    
    for($i=0;$i<count($enrollmentReportDetails['data']);$i++){
        $temp=  Customers::find($enrollmentReportDetails['data'][$i]['customer_id']);
        $enrollmentReportDetails['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
        $temp2=  Students::find($enrollmentReportDetails['data'][$i]['student_id']);
        $enrollmentReportDetails['data'][$i]['student_name']=$temp2->student_name;
        $temp3= Batches::find($enrollmentReportDetails['data'][$i]['batch_id']);
        $enrollmentReportDetails['data'][$i]['batch_name']=$temp3['batch_name'];
    }
    return $enrollmentReportDetails;
  }

  static function getRenewalsPendingReport($inputs){
    $renewals = PaymentDues::getCurrentMonthRenewalsDue($inputs['reportGenerateEnddate'], $inputs['reportGenerateStartdate']);
    $done = PaymentDues::getNoOfRenwalsDone($inputs['reportGenerateEnddate'], $inputs['reportGenerateStartdate']);
    $student_id = array();
    $renewDone_id = array();
    $enrollmentReportDetails['data'] = array();
    if (isset($renewals) && !empty($renewals)) {
      foreach($done as $renewDone) {
        array_push($student_id, $renewDone['student_id']);
      }
      foreach($renewals as $renew) {
        if(!in_array($renew['student_id'], $student_id)){
          $finalData = $renew;
          $temp = Customers::find($finalData['customer_id']);
          $finalData['customer_name']=$temp->customer_name.$temp->customer_lastname;
          $temp2 = Students::find($finalData['student_id']);
          $finalData['student_name']=$temp2->student_name;
          array_push($enrollmentReportDetails['data'], $finalData);
        }
      }
    }
    return $enrollmentReportDetails;
  }
}
