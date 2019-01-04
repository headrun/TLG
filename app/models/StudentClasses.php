<?php
use Carbon\Carbon;
class StudentClasses extends \Eloquent {
	protected $fillable = [];
	public $table = "student_classes";
	
	
	
	public function Courses(){
	
		return $this->belongsTo('Courses', 'course_id');
	}
	
	
	public function Classes(){
		
		return $this->belongsTo('Classes', 'class_id');
	}
	
	public function Students(){
		return $this->belongsTo("Students", 'student_id');
	}
	
	public function Orders(){
		return $this->hasMany('Orders','student_classes_id');
	}
	
  public function batches(){
      return $this->hasOne('Batches','batch_id');
  }
	
	static function getAllEnrolledStudents($franchiseeId){
		$present_date=Carbon::now();
    $students = PaymentDues::join('students', 'students.id','=' ,'payments_dues.student_id')
            ->where('payments_dues.franchisee_id', '=', Session::get('franchiseId'))
            ->where('payments_dues.end_order_date', '>=',date('Y-m-d'))
            ->where('payments_dues.payment_due_for', '=', 'enrollment')
            ->selectRaw('min(payments_dues.start_order_date) as start_order_date,max(payments_dues.end_order_date) as enrollment_end_date,payments_dues.student_id, students.student_name, students.student_gender, students.student_date_of_birth,students.franchisee_id')
            ->groupBy('payments_dues.student_id')
            ->get();
        return $students;
	}

  static function getAllNonEnrolledStudents($franchiseeId){
    $present_date=Carbon::now();
    $studentDeatils = array();
    $students=DB::select(DB::raw(
            "SELECT * from students where id NOT IN (SELECT distinct(student_classes.student_id)
             FROM student_classes where  enrollment_end_date >= '".$present_date->toDateString()."' AND student_classes.status 
             IN ('enrolled', 'transferred_to_other_class', 'transferred_class')) and students.franchisee_id= '".$franchiseeId."'")
                       );
		foreach($students as $key => $student){
			$student_end_date = StudentClasses::where('student_id', '=', $student->id)
                                        ->selectRaw('max(enrollment_end_date) as end_date')
                                        ->get();
			$studentDetails[$student->id] = array('end_date' => $student_end_date[0]->end_date);
		}
	
		foreach($students as $key => $value){
			$students[$key]->end_date = '';
			if(array_key_exists($value->id, $studentDetails)) {
         $students[$key]->end_date = $studentDetails[$value->id]['end_date'];
      }
		}  
    return $students;
	}
        
  static function getEnrolledStudentBatch($studentId){          
		$present_date=Carbon::now();
    $students=StudentClasses::whereDate('enrollment_end_date','>=', $present_date->toDateString())
       ->where('student_id','=',$studentId)     
       ->orderBy('id','DESC')
       ->get();
                                      
		return $students;
	}

    
	static function getMultipleEnrolledList(){
		$total;
		$multipleEnrollments = '';
		    $totalEnrollments = PaymentDues::join('students', 'students.id','=' ,'payments_dues.student_id')
                  ->where('payments_dues.franchisee_id', '=', Session::get('franchiseId'))
                  ->where('payments_dues.end_order_date', '>=',date('Y-m-d'))
                  ->where('payments_dues.payment_due_for', '=', 'enrollment')
                  ->groupBy('payments_dues.student_id')
                  ->get();

		if(count($totalEnrollments) > 0){
			foreach($totalEnrollments as $c){
                		$total[] = $c['student_id'];
                		$list = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                				   ->where('student_id', '=', $c['student_id'])
						               ->where('payment_due_for', '=', 'enrollment')
                				   ->where('end_order_date', '>=', date('Y-m-d') )
                				   ->count();
                	if($list > 1){
                		$multipleEnrollments[] = $list;
                	}   	
		     }		
		     return count($multipleEnrollments);	
        	}else{
			return 0;
		}
	}

	static function getSingleEnrolledList(){
		$total;
		$singleEnrollments = [];
    $totalEnrollments = PaymentDues::join('students', 'students.id','=' ,'payments_dues.student_id')
              ->where('payments_dues.franchisee_id', '=', Session::get('franchiseId'))
              ->where('payments_dues.end_order_date', '>=',date('Y-m-d'))
              ->where('payments_dues.payment_due_for', '=', 'enrollment')
              ->groupBy('payments_dues.student_id')
              ->get();
      if(count($totalEnrollments) > 0){
        foreach($totalEnrollments as $c){
                      $total[] = $c['student_id'];
                      $list = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                             ->where('student_id', '=', $c['student_id'])
                             ->where('payment_due_for', '=', 'enrollment')
                             ->where('end_order_date', '>=', date('Y-m-d') )
                             ->count();
                    if($list <= 1){
                      $singleEnrollments[] = $list;
                    }     
           }    
           return count($singleEnrollments);  
            }else{
        return 0;
      }
	}
	
  static function getTotalEnrolls () {
    $totalEnrollments = PaymentDues::join('students', 'students.id','=' ,'payments_dues.student_id')
              ->where('payments_dues.franchisee_id', '=', Session::get('franchiseId'))
              ->where('payments_dues.end_order_date', '>=',date('Y-m-d'))
              ->where('payments_dues.payment_due_for', '=', 'enrollment')
              ->get();
    return $totalEnrollments;
  }    
        
	static function addStudentClass($input){
		$StudentClasses = new StudentClasses();
		$StudentClasses->student_id    = $input['studentId'];
    		$StudentClasses->season_id     = $input['seasonId'];
		$StudentClasses->class_id      = $input['classId'];		
		$StudentClasses->enrollment_start_date  = $input['enrollment_start_date'];
		$StudentClasses->enrollment_end_date  = $input['enrollment_end_date'];		
		$StudentClasses->selected_sessions  = $input['selected_sessions'];
      if(isset($input['introvisit_id']) && array_key_exists('introvisit_id',$input) && $input['introvisit_id']!=''){
        
        $StudentClasses->introvisit_id=$input['introvisit_id'];
      }
      if(isset($input['attendance_id']) && array_key_exists('attendance_id',$input) && $input['attendance_id']!=''){
                    
        $StudentClasses->attendance_id=$input['attendance_id'];
      }
      if(isset($input['status']) && array_key_exists('status',$input) && $input['status']!=''){
        
        $StudentClasses->status=$input['status'];
      }else{
                    
        $StudentClasses->status="enrolled";
      }
		$StudentClasses->batch_id      = $input['batchId'];
                $StudentClasses->franchisee_id = Session::get('franchiseId');
		$StudentClasses->created_by    = Session::get('userId');
		$StudentClasses->created_at    = date("Y-m-d H:i:s");
		$StudentClasses->save();
		
		return $StudentClasses;
	}
	
	static function getStudentEnrollments($id){
		
		return StudentClasses::with('Classes')->where('student_id', '=', $id)->get();
	}
	
	static function getEnrolledCustomers(){
		
		$franchiseeId = Session::get('franchiseId');
		$present_date=Carbon::now();
	
		$enrolledCustomers = DB::select(DB::raw("SELECT count(distinct(students.id)) as enrollmentno
                    FROM student_classes INNER JOIN students ON student_classes.student_id = students.id
                    WHERE  students.franchisee_id='".$franchiseeId."' AND student_classes.status IN ('enrolled', 'transferred_to_other_class', 'transferred_class') AND enrollment_end_date >= '".$present_date->toDateString()."'")
      );


                $enrolledCustomers=$enrolledCustomers[0]->enrollmentno;
                if($enrolledCustomers){
	                return $enrolledCustomers;
                }
                return false;		
 
	}
	
  static function getTodaysEnrolledCustomers(){
    $franchiseeId = Session::get('franchiseId');
    $present_date=Carbon::now();
		$todaysEnrolledCustomers = 
                        DB::select(DB::raw("SELECT count(distinct(student_classes.student_id)) as enrollmentno
                                                FROM student_classes INNER JOIN students ON student_classes.student_id = students.id
                                                WHERE student_classes.created_at like '".$present_date->toDateString()."%' AND students.franchisee_id='".$franchiseeId."' AND student_classes.status='enrolled' AND enrollment_end_date >= '".$present_date->toDateString()."'")
                                  );
		$todaysEnrolledCustomers=$todaysEnrolledCustomers[0]->enrollmentno;	
                if($todaysEnrolledCustomers){ 
                    return $todaysEnrolledCustomers;
                    
                }return false;
                
        }
        
	static function getStudentByBatchId($batchId, $selectedDate){
		$selectedDate = date('Y-m-d', strtotime($selectedDate));
		$studentByBatchId = StudentClasses::with('Students')
				  ->where('batch_id', '=', $batchId)
        		  ->whereIn('status',array('enrolled','makeup','introvisit','transferred_class'))
			   	  ->whereDate('enrollment_start_date', '<=', $selectedDate)
				  ->whereDate('enrollment_end_date', '>=', $selectedDate)
		          ->groupBy('student_id')
		          ->get();

		return $studentByBatchId;	
	}
	
	static function discount($studentId, $customerId){
		$isEligibleforTwenty = "NO";
		$isEligibleforFifty  = "NO";
		$Students = Students::where("customer_id", "=", $customerId)
							  ->where("id", '<>', $studentId)
							  ->get();
		$enrolledStudent = 0;
		foreach($Students as $student){	
				$enrolledStudent = StudentClasses::where('student_id', '=', $student->id)
												 ->count();
		}
		if($enrolledStudent > 0){			
			$isCurrentStudentEnrolled = StudentClasses::where('student_id', '=', $studentId)
			->count();			
			if($isCurrentStudentEnrolled == 0){
				$isEligibleforTwenty = "YES";
			}			
		}
		$isCurrentStudentEnrolled = StudentClasses::where('student_id', '=', $studentId)
                                            ->count();
		if($isCurrentStudentEnrolled > 0){
			$isEligibleforFifty = "YES";
		}
		
		return array("eligibleForTwenty"=>$isEligibleforTwenty, "eligibleForFifty"=>$isEligibleforFifty);
		
	}
	
        static function getStudentClassbyId($student_class_id){
            return StudentClasses::where('id','=',$student_class_id)
                                   ->get();
        }
	
        static function getAllClassCountByBatchId($inputs){
            return StudentClasses::where('batch_id', '=', $inputs['batchId'])
                          ->where('student_id', '=', $inputs['studentId'])
                          ->whereIn('status', array('enrolled','transferred_class','introvisit','makeup'))
                          ->sum('selected_sessions');
            
        }

        static function getTransferToOtherCls($inputs){
            return StudentClasses::where('batch_id', '=', $inputs['batchId'])
                          ->where('student_id', '=', $inputs['studentId'])
                          ->where('status', '=', 'transferred_to_other_class')
                          ->count();
            
        }

        static function getmakeupClassesForThisStuId($inputs) {
        	return StudentClasses::where('batch_id', '=', $inputs['batchId'])
        	              ->where('student_id', '=', $inputs['studentId'])
        	              ->where('status', '=', 'makeup')
        	              ->count();
        }
	static public function getTodayEnrollment(){
        	$presentDate = Carbon::now();
		return PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
			  ->where('payment_due_for', '=', 'enrollment')
                          ->whereDate('created_at', '=', date('Y-m-d', strtotime($presentDate)))
                          ->count();
    	}
	
	static public function getThisWeekEnrollment(){
        	$weeekdate= new carbon();
        	$presentdate= Carbon::now();
        	$time = strtotime($presentdate);
        	$end = strtotime('last monday, 11:59pm', $time);
        	return PaymentDues::whereDate('created_at','<=',date('Y-m-d', $time))
                            ->where('payment_due_for', '=', 'enrollment')
                            ->where('franchisee_id','=',Session::get('franchiseId'))
                            ->whereDate('created_at','>=',date('Y-m-d', $end))
                            ->count();

    	}

	static public function getThisMonthEnrollment(){
    	$presentDate = Carbon::now();
    	return PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                         ->where('payment_due_for', '=', 'enrollment')
                         ->whereRaw('MONTH(created_at) = MONTH(NOW())')
                         ->whereRaw('YEAR(created_at) = YEAR(NOW())')
                         ->count();

	}

  static public function getAllmissedClasses ($inputs) {
    $yesterDay = date('Y-m-d', strtotime('-1 day', strtotime($inputs['start_date'])));
    $dataAtte = Attendance::where('status', '=', 'A')
                      ->where('attendance_date', '=', $yesterDay)
                      ->get();
    $att_id = array();
    for ($i=0; $i < count($dataAtte); $i++) { 
      $student_classes = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                       ->where('id', '=', $dataAtte[$i]['student_classes_id'])
                                       ->where('status', '=', 'enrolled')
                                       ->get();
      if (count($student_classes) > 0) {
        $att_id[] = $dataAtte[$i]['id'];
      }
    }      
    $data = Attendance::whereIn('id', $att_id)
                      ->where('status', '=', 'A')
                      ->where('attendance_date', '=', $yesterDay)
                      ->get();
    for ($i=0; $i < count($data); $i++) { 
      $student_classes = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                       ->where('id', '=', $data[$i]['student_classes_id'])
                                       ->get();
      if (count($student_classes) > 0) {
        $batch = Batches::where('franchisee_id', '=', Session::get('franchiseId'))
                        ->where('id', '=', $student_classes[0]['batch_id'])
                        ->get();
        $data[$i]['batch_name'] = $batch[0]['batch_name'];
        $student = Students::where('franchisee_id', '=', Session::get('franchiseId'))
                         ->where('id', '=', $data[$i]['student_id'])
                         ->get();
        $data[$i]['student_name'] = $student[0]['student_name'];
        if ($batch[0]['lead_instructor'] !== '' && isset($batch[0]['lead_instructor'])) {
          $user = User::where('franchisee_id', '=', Session::get('franchiseId'))
                       ->where('id', '=', $batch[0]['lead_instructor'])
                       ->get();
          $data[$i]['instructor_name'] = $user[0]['first_name'] . $user[0]['last_name'];
        } else {
          $data[$i]['instructor_name'] = '';
        }
        $customers = Customers::where('franchisee_id', '=', Session::get('franchiseId'))
                              ->where('id', '=', $student[0]['customer_id'])
                              ->get();
        $data[$i]['customer_name'] = $customers[0]['customer_name'] . $customers[0]['customer_lastname'];
        $data[$i]['mobile_no'] = $customers[0]['mobile_no'];
        $data[$i]['email'] = $customers[0]['customer_email'];
      }
    }
    return $data;
  }

  static public function getAllTmrwClassesIntr ($inputs) {
    $futureDay = date('Y-m-d', strtotime('+1 day', strtotime($inputs['start_date'])));
    $data = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                          ->where('enrollment_start_date', '=', $futureDay)
                          ->where('status', '=', 'introvisit')
                          ->get();
    for ($i=0; $i < count($data); $i++) { 
      $student_classes = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                       ->where('id', '=', $data[$i]['id'])
                                       ->get();
      if (count($student_classes) > 0) {
        $batch = Batches::where('franchisee_id', '=', Session::get('franchiseId'))
                        ->where('id', '=', $student_classes[0]['batch_id'])
                        ->get();
        $data[$i]['batch_name'] = $batch[0]['batch_name'];
        $student = Students::where('franchisee_id', '=', Session::get('franchiseId'))
                         ->where('id', '=', $data[$i]['student_id'])
                         ->get();
        $data[$i]['student_name'] = $student[0]['student_name'];
        if ($batch[0]['lead_instructor'] !== '' && isset($batch[0]['lead_instructor'])) {
          $user = User::where('franchisee_id', '=', Session::get('franchiseId'))
                       ->where('id', '=', $batch[0]['lead_instructor'])
                       ->get();
          $data[$i]['instructor_name'] = $user[0]['first_name'] . $user[0]['last_name'];
        } else {
          $data[$i]['instructor_name'] = '';
        }
        $customers = Customers::where('franchisee_id', '=', Session::get('franchiseId'))
                              ->where('id', '=', $student[0]['customer_id'])
                              ->get();
        $data[$i]['customer_name'] = $customers[0]['customer_name'] . $customers[0]['customer_lastname'];
        $data[$i]['mobile_no'] = $customers[0]['mobile_no'];
        $data[$i]['email'] = $customers[0]['customer_email'];
      }
    }
    return $data;
  }

  static public function getAllMissedIntro ($inputs) {
    $yesterDay = date('Y-m-d', strtotime('-1 day', strtotime($inputs['start_date'])));
    $intro = IntroVisit::where('franchisee_id', '=', Session::get('franchiseId'))
                       ->where('iv_date', '=', $inputs['start_date'])
                       ->get();
    $iv_ids = array();
    foreach ($intro as $key => $value) {
      $iv_ids[] = $value['id'];        
    }      
    $data = Attendance::whereIn('introvisit_id', $iv_ids)
                      ->where('status', '=', 'A')
                      ->where('attendance_date', '=', $inputs['start_date'])
                      ->get();
    for ($i=0; $i < count($data); $i++) { 
      $student_classes = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                       ->where('id', '=', $data[$i]['student_classes_id'])
                                       ->get();
      if (count($student_classes) > 0) {
        $batch = Batches::where('franchisee_id', '=', Session::get('franchiseId'))
                        ->where('id', '=', $student_classes[0]['batch_id'])
                        ->get();
        $data[$i]['batch_name'] = $batch[0]['batch_name'];
        $student = Students::where('franchisee_id', '=', Session::get('franchiseId'))
                         ->where('id', '=', $data[$i]['student_id'])
                         ->get();
        $data[$i]['student_name'] = $student[0]['student_name'];
        if ($batch[0]['lead_instructor'] !== '' && isset($batch[0]['lead_instructor'])) {
          $user = User::where('franchisee_id', '=', Session::get('franchiseId'))
                       ->where('id', '=', $batch[0]['lead_instructor'])
                       ->get();
          $data[$i]['instructor_name'] = $user[0]['first_name'] . $user[0]['last_name'];
        } else {
          $data[$i]['instructor_name'] = '';
        }
        $customers = Customers::where('franchisee_id', '=', Session::get('franchiseId'))
                              ->where('id', '=', $student[0]['customer_id'])
                              ->get();
        $data[$i]['customer_name'] = $customers[0]['customer_name'] . $customers[0]['customer_lastname'];
        $data[$i]['mobile_no'] = $customers[0]['mobile_no'];
        $data[$i]['email'] = $customers[0]['customer_email'];
      }
    }
    return $data;
  }
      
}
