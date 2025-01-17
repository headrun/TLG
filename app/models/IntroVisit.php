<?php
use Carbon\Carbon;

class IntroVisit extends \Eloquent {
	protected $fillable = [];
	protected $table= 'introvisit';
	
	public function Customers(){
		return $this->belongsTo('Customers','customer_id');
	}
	
	public function Students(){
		return $this->belongsTo('Students','student_id');
	}
	
	public function Classes(){
	
		return $this->belongsTo('Classes', 'class_id');
	}
	
	public function Batches(){
		return $this->belongsTo('Batches', 'batch_id');
	}
	
	static function getIvForActivityReport($inputs){

        $getIvForActivityReport['data'] = IntroVisit::where('franchisee_id','=',Session::get('franchiseId'))
						 ->whereDate('created_at','>=',$inputs['reportStartDate'])
        				 ->whereDate('created_at','<=',$inputs['reportEndDate'])
        				 ->select('student_id','customer_id','created_at')
        				 ->get();
        for($i=0;$i<count($getIvForActivityReport['data']);$i++){

            $temp=  Customers::find($getIvForActivityReport['data'][$i]['customer_id']);
            
            $getIvForActivityReport['data'][$i]['customer_name']=$temp->customer_name." ".$temp->customer_lastname;
            
            $temp2=  Students::find($getIvForActivityReport['data'][$i]['student_id']);
            
            $getIvForActivityReport['data'][$i]['student_name']=$temp2->student_name;
            
            $getIvForActivityReport['data'][$i]['payment_due_for']= 'INTROVISIT';
            
            $temp3=  IntroVisit::where('student_id','=',$getIvForActivityReport['data'][$i]['student_id'])
                              ->where('iv_date','!=','null')
                              ->orderby('created_at','DESC')
                              ->limit(1)
                              ->get();
            if(isset($temp3) && !empty($temp3)){
                $getIvForActivityReport['data'][$i]['created_at']=$temp3[0]['iv_date'];  
            }


        }
        return $getIvForActivityReport;
    }
	static function addSchedule($inputs){
		
		$introVisit = new IntroVisit();
		
		$introVisit['customer_id']      = $inputs['customerId'];
		$introVisit->student_id         = $inputs['studentIdIntroVisit'];
		
		$introVisit->class_id           = $inputs['eligibleClassesCbx'];
		$introVisit->batch_id           = $inputs['introbatchCbx'];	
		//$introVisit->status             = 'ACTIVE/SCHEDULED';
		
		$introVisit->franchisee_id      = Session::get('franchiseId');		
		if(isset($inputs['introVisitTxtBox']) && $inputs['introVisitTxtBox'] !=''){
			$introVisit->iv_date            = date('Y-m-d',strtotime($inputs['introVisitTxtBox']));
		}
		$introVisit->created_by         = Session::get('userId');
		$introVisit->created_at         = date("Y-m-d H:i:s");
		$introVisit->save();
		
		
		$customerObj = Customers::find($inputs['customerId']);
		if($customerObj){
			
			$customerObj->stage = "IV SCHEDULED";
			$customerObj->save();
		}
		
		return $introVisit;
		
	}
	
	static function getIntrovisitBytoday(){
	
		return IntroVisit::whereDate('iv_date', '=',  date("Y-m-d"))
						//	->whereIn('status',array('ACTIVE/SCHEDULED','RESCHEDULED'))
                                                       
							->where('franchisee_id', '=',  Session::get('franchiseId'))
							->count();
	}
	
	static function getAllActiveIntrovisit(){
	
		return IntroVisit::with('Customers', 'Classes', 'Batches', 'Students')

						//	->whereIn('status',array('ACTIVE/SCHEDULED','RESCHEDULED'))
							->where('franchisee_id', '=',  Session::get('franchiseId'))
							->orderBy('iv_date','DESC')
							->get();
	}
	
	static function getIntrovisitByStudentId($studentId){
		
		return IntroVisit::with('Classes','Batches')
							->where('student_id', '=', $studentId)
							->where('franchisee_id', '=',  Session::get('franchiseId'))
							->get();
	}

    static function getIntrovistCount(){
        return IntroVisit::where('franchisee_id', '=',  Session::get('franchiseId'))
                           ->count();
    }

    static function getTodayScheduledIvs(){
    	return IntroVisit::where('franchisee_id', '=', Session::get('franchiseId'))
    					 ->where('iv_date', '=', date('Y-m-d'))
    					 ->count();
    }

    static function getTodayAttendedIvs(){
    	$iv_dates = '';
    	$todayIvs = IntroVisit::where('franchisee_id', '=', Session::get('franchiseId'))
    					 ->where('iv_date', '=', date('Y-m-d'))
    					 ->get();
         if(!empty($todayIvs) && isset($todayIvs)){
		    foreach ($todayIvs as $iv) {
		    	$attended = Attendance::where('introvisit_id', $iv['id'])
		    						   ->where('student_id', '=', $iv['student_id'])
		    						   ->count();	
		    	if($attended >= 1){
		    		$iv_dates[] = $attended;
		    	}
		    }
		    return count($iv_dates);                         
		}  
    }

    static function getThisWeekScheduledIv(){
    	$weeekdate= new carbon();
        $presentdate= Carbon::now();
        $time = strtotime($presentdate);
        $end = strtotime('last sunday, 11:59pm', $time);
        return IntroVisit::whereDate('iv_date','<=',date('Y-m-d', $time))
                            ->where('franchisee_id','=',Session::get('franchiseId'))
                            ->whereDate('iv_date','>=',date('Y-m-d', $end))
                            ->count();
    }

    static function getThisWeekAttendedIvs(){
    	$weeekdate= new carbon();
        $presentdate= Carbon::now();
        $time = strtotime($presentdate);
        $end = strtotime('last sunday, 11:59pm', $time);
        $iv_dates = '';
        $week = IntroVisit::whereDate('iv_date','<=',date('Y-m-d', $time))
                            ->where('franchisee_id','=',Session::get('franchiseId'))
                            ->whereDate('iv_date','>=',date('Y-m-d', $end))
                            ->get();
        if(!empty($week) && isset($week)){
		    foreach ($week as $iv) {
		    	$attended = Attendance::where('introvisit_id', $iv['id'])
		    						   ->where('student_id', '=', $iv['student_id'])
		    						   ->count();	
		    	if($attended >= 1){
		    		$iv_dates[] = $attended;
		    	}
		    }
		    return count($iv_dates);                         
		}
    }

    static function getThisMonthAttendedIv(){
    	$iv_dates = '';
    	$ivs = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
    		         	->whereRaw('MONTH(created_at) = MONTH(NOW())')
	                        ->whereRaw('YEAR(created_at) = YEAR(NOW())')
	                        ->where('followup_status','=','ATTENDED')
				->groupBy('customer_id')
				->get();
	return count($ivs);                         
                                
    }
	
	static function getThisMonthIv(){
    	return IntroVisit::where('franchisee_id', '=', Session::get('franchiseId'))
    		   			    ->whereRaw('MONTH(iv_date) = MONTH(NOW())')
	                        ->whereRaw('YEAR(iv_date) = YEAR(NOW())')
	                        ->count();
                                
    }

    static function getAllIntrovisitforReport($inputs){
    	$present_date = Carbon::now();
        $introvisit['data']= Introvisit::where('franchisee_id','=',Session::get('franchiseId'))
                           ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                           ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                           ->get();
        //return $present_date;
        for($i=0;$i<count($introvisit['data']);$i++){
            $temp=  Customers::find($introvisit['data'][$i]['customer_id']);
            $introvisit['data'][$i]['customer_name']=$temp->customer_name." ".$temp->customer_lastname;
            $temp2=  Students::find($introvisit['data'][$i]['student_id']);
            $introvisit['data'][$i]['student_name']=$temp2->student_name;
            $temp4= StudentClasses::find($introvisit['data'][$i]['student_id']);
            $introvisit['data'][$i]['status']=$temp2->status;
            if($introvisit['data'][$i]['status']==''){
            	if($introvisit['data'][$i]['iv_date'] <= $present_date){
            	   $introvisit['data'][$i]['status'] ='Attended';
            	}
            	else{
            	   $introvisit['data'][$i]['status'] ='IV SCHEDULED';
            	}
            }
            if($introvisit['data'][$i]['batch_id']!=null){
                $temp3= Batches::find($introvisit['data'][$i]['batch_id']);
                $introvisit['data'][$i]['batch_name']=$temp3->batch_name;
            }
        }
        return $introvisit;
    }

    static function getIntrovisitForThisStuId($inputs) {
    	return IntroVisit::where('student_id', '=', $inputs['studentId'])
    					 ->where('batch_id', '=', $inputs['batchId'])
    					 ->count();
    }


    static public function before2days_introvisitnotset ($inputs) {
       $daybeforeyesterDay = date('Y-m-d', strtotime('-2 day', strtotime($inputs['start_date'])));
	    $intro = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
	                     ->where('enrollment_start_date', '=', $daybeforeyesterDay)
	                     ->where('status', '=', 'introvisit')
	                     ->get();

	    $iv_ids = array();
	    foreach ($intro as $key => $value) {
	      $iv_ids[] = $value['id'];        
	    }  
	    $data = Attendance::whereIn('student_classes_id', $iv_ids)
	                      ->where('status', '=', 'P')
	                      ->where('attendance_date', '=', $daybeforeyesterDay)
	                      ->get(); 
	    $enrollment_array = array();
	    foreach ($data as $key => $value) {
	    	$notenrolled_data = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
	    	                                ->where('student_id', '=', $value['student_id'])
	    	                                ->where('end_order_date', '>=', $daybeforeyesterDay)
	    	                                ->where('payment_due_for', '=', 'enrollment')
	    	                                ->get();
	    	if (count($notenrolled_data) == 0) {
	    		$student_classes = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
	    						 ->where('student_id', '=', $value['student_id'])
	    		                 ->where('enrollment_start_date', '=', $daybeforeyesterDay)
	    		                 ->where('status', '=', 'introvisit')
	    		                 ->get();
	    		if (count($student_classes) > 0) {	    			
		    		$batch = Batches::where('franchisee_id', '=', Session::get('franchiseId'))
		    		                ->where('id', '=', $student_classes[0]['batch_id'])
		    		                ->get();
		    		$enrollment_array[$key]['batch_name'] = $batch[0]['batch_name'];
		    		$student = Students::where('franchisee_id', '=', Session::get('franchiseId'))
		    		                 ->where('id', '=', $student_classes[0]['student_id'])
		    		                 ->get();
		    		
		    		if (count($student) > 0) {
		    			$enrollment_array[$key]['student_name'] = $student[0]['student_name'];
		    			if ($batch[0]['lead_instructor'] !== '' && isset($batch[0]['lead_instructor'])) {
		    			  $user = User::where('franchisee_id', '=', Session::get('franchiseId'))
		    			               ->where('id', '=', $batch[0]['lead_instructor'])
		    			               ->get();
		    			  $enrollment_array[$key]['instructor_name'] = $user[0]['first_name'] . $user[0]['last_name'];
		    			} else {
		    			  $enrollment_array[$key]['instructor_name'] = '';
		    			}

		    			$customers = Customers::where('franchisee_id', '=', Session::get('franchiseId'))
		    			                      ->where('id', '=', $student[0]['customer_id'])
		    			                      ->get();
		    			$enrollment_array[$key]['customer_name'] = $customers[0]['customer_name'] . $customers[0]['customer_lastname'];
		    			$enrollment_array[$key]['mobile_no'] = $customers[0]['mobile_no'];
		    			$enrollment_array[$key]['email'] = $customers[0]['customer_email'];
		    			$enrollment_array[$key]['date'] = $daybeforeyesterDay;
		    		}
	    		}
	    	}
	    }
	    return $enrollment_array;

  }
	
	
}
