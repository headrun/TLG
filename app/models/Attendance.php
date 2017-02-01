<?php

class Attendance extends \Eloquent {
	protected $fillable = [];
	protected $table = 'attendance';
	
	
	public function Classes(){
		
		return $this->belongsTo('Classes', 'class_id');
	}
	
	
	static function getDaysAttendanceForStudent($studentId, $batchId, $attendanceDate){
		
		
		//echo "student_id".$studentId.' batchId'.$batchId.' attendanceDate'.$attendanceDate;
		
		$attendance =  Attendance::where('student_id', '=', $studentId)
							->where('batch_id', '=', $batchId)
							->where('attendance_date', '=', $attendanceDate)
							->first();
		
		//print_r(DB::getQueryLog());
		
		if($attendance)
		{
			return $attendance;
		}
		return false;
	}
	
	
	
	static function getAttendanceForStudent($data){
		//return $data;
		return $getAttendance = Attendance::where('student_id', '=', $data['studentId'])
							->where('batch_id', '=', $data['batchId'])
							->where('attendance_date', 'like', '%'.$data['year'].'%')
							->get();
	}
        
        static function getEAbybatchandStudentId($batch_id,$student_id){
            return Attendance::where('student_id', '=', $student_id)
                              ->where('batch_id', '=', $batch_id)
                              ->where('status','=','EA')
                              ->where('makeup_class_given','=',null)
                              ->get();
        }
	
	
	
	
}