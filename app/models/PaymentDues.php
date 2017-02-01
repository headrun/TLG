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
    
    static function getAllBirthdayPaymentsforReport($inputs){
        $birthdayReportDetails['data'] =PaymentDues::where('payment_due_for','=','birthday')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('birthday_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->orderBy('id','desc')
                                                    ->get();
        for($i=0;$i<count($birthdayReportDetails['data']);$i++){
            $temp=  Customers::find($birthdayReportDetails['data'][$i]['customer_id']);
            $birthdayReportDetails['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
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
        $enrollmentReportDetails['totalAmount']=PaymentDues::where('payment_due_for','=','enrollment')
                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                    ->where('payment_status','=','paid')
                                                    ->where('student_class_id','<>',0)
                                                    ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                                                    ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                                                    ->sum('payment_due_amount_after_discount');
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
            $reportData['data'][$i]['customer_name']=$temp->customer_name.$temp->customer_lastname;
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
                                        ->orderBy('id','desc')
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
  
}