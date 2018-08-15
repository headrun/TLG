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
       	$students = StudentClasses::join('students', 'students.id','=' ,'student_classes.student_id')
                                                        ->where('student_classes.franchisee_id', '=', $franchiseeId)
                                                        ->where('student_classes.status','!=','introvisit')
                                                        ->whereDate('student_classes.enrollment_end_date', '>=',date('Y-m-d'))
                                                        ->selectRaw('min(student_classes.enrollment_start_date) as enrollment_start_date,max(student_classes.enrollment_end_date) as enrollment_end_date,student_classes.student_id, students.student_name, students.student_gender, students.student_date_of_birth,students.franchisee_id, students.customer_id')  
                                                        ->groupBy('student_classes.student_id')
                                                       // ->groupBy(DB::Raw("date('student_classes.created_at')"))
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
	//		var_dump($student); die();
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
                /*
                $students=StudentClasses::with(array('Students'=>function($q){
                    $q->where('franchisee_id','=', Session::get('franchiseId'))
                       ->get();
                       
                }))//->whereDate('enrollment_start_date','<=',$present_date->toDateString())
                   ->whereDate('enrollment_end_date','>=', $present_date->toDateString())
                   ->distinct('student_id')
                   ->groupBy('student_id')
                   ->select('student_id')
                   ->get();
                
                
                for($i=0;$i<count($students);$i++){
                    $student_id[]=$students[$i]->student_id;
                }
                
                if(isset($student_id)){
                $nonEnrolledStudents=  Students::where('franchisee_id','=', Session::get('franchiseId'))
                                                 ->whereNotIn('id',$student_id)
                                                 ->get();
                }else{                               
                $nonEnrolledStudents[0]='';                      
                }
                */
                return $students;
	}
        
        
        static function getEnrolledStudentBatch($studentId){
                
		$present_date=Carbon::now();
                $students=StudentClasses:://->whereDate('enrollment_start_date','<=',$present_date->toDateString())
                          whereDate('enrollment_end_date','>=', $present_date->toDateString())
                   ->where('student_id','=',$studentId)     
                   ->orderBy('id','DESC')
                   ->get();
                                      
		return $students;
	}

    
	static function getMultipleEnrolledList(){
		$total;
		$multipleEnrollments = '';
		$totalEnrollments = StudentClasses::join('students', 'students.id','=' ,'student_classes.student_id')
                                                        ->where('student_classes.franchisee_id', '=', Session::get('franchiseId'))
                                                        ->where('student_classes.status','!=','introvisit')
                                                        ->where('student_classes.enrollment_end_date', '>=',date('Y-m-d'))
                                                        ->selectRaw('min(student_classes.enrollment_start_date) as enrollment_start_date,max(student_classes.enrollment_end_date) as enrollment_end_date,student_classes.student_id, students.student_name, students.student_gender, students.student_date_of_birth,students.franchisee_id')            
                                                        ->groupBy('student_classes.student_id')
                                                        ->get();

		if(count($totalEnrollments) > 0){
			foreach($totalEnrollments as $c){
                		$total[] = $c['student_id'];
                		$list = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                				   ->where('student_id', '=', $c['student_id'])
						   ->where('payment_due_for', '=', 'enrollment')
                				   ->where('end_order_date', '>=', date('Y-m-d') )
                				   ->count();
                	if($list >1){
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
		$No = [];
		$totalEnrollments = StudentClasses::join('students', 'students.id','=' ,'student_classes.student_id')
                                                        ->where('student_classes.franchisee_id', '=', Session::get('franchiseId'))
                                                        ->where('student_classes.status','!=','introvisit')
                                                        ->where('student_classes.enrollment_end_date', '>=',date('Y-m-d'))
                                                        ->selectRaw('min(student_classes.enrollment_start_date) as enrollment_start_date,max(student_classes.enrollment_end_date) as enrollment_end_date,student_classes.student_id, students.student_name, students.student_gender, students.student_date_of_birth,students.franchisee_id')
                                                        ->groupBy('student_classes.student_id')
                                                        ->get();
		if(count($totalEnrollments) > 0){
			foreach($totalEnrollments as $c){
                		$total[] = $c['student_id'];
                		$list = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                				   ->where('student_id', '=', $c['student_id'])
                				   ->where('payment_due_for', '=', 'enrollment')
                				   ->where('end_order_date', '>=', date('Y-m-d'))
                				   ->count();
				
                		if($list == 1){
                			$singleEnrollments[] = $list;
                		}else if($list == 0){
					$No[] = $list;
				}   		
        		}
			$single = count($singleEnrollments);
			$no = count($No);
			$totalSingle = $single + $no;
			return $totalSingle;
		}else{
			return 0;
		}
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
}
