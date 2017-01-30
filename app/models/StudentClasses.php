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
                
                $students=DB::select(DB::raw(
                        "SELECT * from students where id IN (SELECT distinct (students.id)
                         FROM student_classes INNER JOIN students ON student_classes.student_id = students.id
                         WHERE students.franchisee_id = ".$franchiseeId." AND enrollment_start_date <= '".$present_date->toDateString().
                         "' AND enrollment_end_date >='".$present_date->toDateString()."' AND student_classes.status 
                         IN ('enrolled','transferred_class'))")
                                   );
                /*
                $students=StudentClasses::with(array('Students'=>function($q){
                    $q->where('franchisee_id','=', Session::get('franchiseId'))
                      ->select('id','students.*');
                }))//->whereDate('enrollment_start_date','<=',$present_date->toDateString())
                   ->whereDate('enrollment_end_date','>=', $present_date->toDateString())
                   ->distinct('student_id')
                   ->groupBy('student_id')
                   ->get();
                */                      
		return $students;
	}
        static function getAllNonEnrolledStudents($franchiseeId){
		
                $present_date=Carbon::now();
                
                $students=DB::select(DB::raw(
                        "SELECT * from students where students.id NOT IN (SELECT distinct(student_id)
                         FROM student_classes ". 
                         " where enrollment_start_date <= '".$present_date->toDateString()."' AND enrollment_end_date >= '".$present_date->toDateString()."' AND status 
                         IN ('enrolled','transferred_class')) and franchisee_id= ".$franchiseeId)
                                   );
                
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
		$enrolledCustomers = 
                        DB::select(DB::raw("SELECT count(distinct(student_classes.student_id)) as enrollmentno
                                                FROM student_classes INNER JOIN students ON student_classes.student_id = students.id
                                                WHERE  students.franchisee_id='".$franchiseeId."' AND student_classes.status='enrolled' AND enrollment_end_date > '".$present_date->toDateString()."'")
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
                                                WHERE student_classes.created_at like '".$present_date->toDateString()."%' AND students.franchisee_id='".$franchiseeId."' AND student_classes.status='enrolled' AND enrollment_end_date > '".$present_date->toDateString()."'")
                                  );
		$todaysEnrolledCustomers=$todaysEnrolledCustomers[0]->enrollmentno;	
                if($todaysEnrolledCustomers){ 
                    return $todaysEnrolledCustomers;
                    
                }return false;
                
        }
        
	static function getStudentByBatchId($batchId, $selectedDate){
		$selectedDate = date('Y-m-d', strtotime($selectedDate));
		$studentByBatchId  =   StudentClasses::with('Students')
								->where('batch_id', '=', $batchId)
                                                                ->whereIn('status',array('enrolled','makeup','introvisit','transferred_class'))
								->whereDate('enrollment_start_date', '<=', $selectedDate)
								->whereDate('enrollment_end_date', '>=', $selectedDate)
								->get();
		//dd(DB::getQueryLog());
		return $studentByBatchId;
		
		
	}
	
	static function discount($studentId, $customerId){
		
		/* return StudentClasses::where('student_id', '=', $studentId)
						->where('class_id', '=', $classid)
						->first(); */
		
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
            return StudentClasses::where('batch_id','=',$inputs['batchId'])
                          ->where('student_id','=',$inputs['studentId'])
                          ->whereIn('status', array('enrolled','transferred_class'))
                          ->sum('selected_sessions');
            
        }
	
	
}