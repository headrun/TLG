<?php

class Courses extends \Eloquent {
	//protected $fillable = ['courseName','masterCourse'];
	protected $table = 'courses';
	
//	private $rules = array(
//			"courseName" => "required", //|unique:courses_master, course_name
//			"masterCourse" => "required",
//	);
	
	private $errors = array();
	
	public function Users(){
		
		return $this->belongsTo('User','created_by');
	}
	
	public function Classes(){
	
		return $this->hasMany('Classes','course_id');
	}
	
	public function StudentSchedule(){
	
		return $this->hasMany('Courses','course_id');
	}
	
	
	public function CoursesMaster(){
	
		return $this->belongsTo('CoursesMaster','master_course_id');
	}
	
	
	public function validate() {
	
		$validation = Validator::make(Input::all(), $this->rules);
		if ($validation->fails()) {
			$this->errors = $validation->errors();
			return false;
		}
		return true;
	}
	
	public function errors() {
		return $this->errors;
	}
	
//	static function addCourse($inputs){
//	
//		
//		$Course = new Courses();
//		$Course->course_name      = $inputs['courseName'];
//		$Course->master_course_id = $inputs['masterCourse'];
//		$Course->franchisee_id    = Session::get('franchiseId');
//		$Course->created_by       = Session::get('userId');
//		$Course->created_at       = date("Y-m-d H:i:s");
//		$Course->save();
//		
//		return $Course;
//	
//	
//	}
        static function addCourse($inputs){
	
		
		$Course = new Courses();
		$Course->course_name      = $inputs['courseName'];
                $Course->slug             = $inputs['slug'];
		$Course->master_course_id = $inputs['masterCourseList'];
		$Course->franchisee_id    = Session::get('franchiseId');
		$Course->created_by       = Session::get('userId');
		$Course->created_at       = date("Y-m-d H:i:s");
		$Course->save();
		
		return $Course;
	
	
	}
	 static function updateCourses($inputs, $coureIds) {
		$Course = new Courses();
		$Course->course_name      = $coureIds[0]['course_name'];
        $Course->slug     		  = $coureIds[0]['slug'];
		$Course->master_course_id = $coureIds[0]['id'];
		$Course->franchisee_id    = $inputs['franchiseeId'];
		$Course->created_by       = Session::get('userId');
		$Course->created_at       = date("Y-m-d H:i:s");
		$Course->save();
		
		return $Course;
	 }

	static function checkWhetherCourseAdded($franchiseeID, $masterCourseId){
		
		$coursesCount = Courses::where('franchisee_id', '=', $franchiseeID)
								->where('master_course_id', '=', $masterCourseId)
								->count();
		return $coursesCount;
	}
	
	static function getFranchiseCoursesList($franchiseeID = null){
	
		$courses = Courses::where('franchisee_id', '=', $franchiseeID)->lists('course_name', 'id');
	
		/* if($franchiseeID != null){
			$query->where('franchisee_id', '=', $franchiseeID);
		}
	
		$courses = $query->get(); */
		return $courses;
	}
	
	static function getFranchiseCourses($franchiseeID = null){
		
		$query = Courses::with('Users','CoursesMaster');
		
		if($franchiseeID != null){
			$query->where('franchisee_id', '=', $franchiseeID);
		}  
		
		$courses = $query->get();
		return $courses;
	}
	
	static function getBatchID($courseId, $classId, $startYear, $startMonth,$seasonId){
		
		
		if(Batches::count()){
			
			$batchCount = (Batches::where("class_id","=",$classId)->where('season_id','=',$seasonId)->count()+1);
		}else{
			$batchCount = 1;
		}
		
		if($batchCount < 10){
			$batchCount =  str_pad($batchCount, 2, '0', STR_PAD_LEFT);
		}
		
		$courseSlug = Courses::select("slug")->where("id", "=", $courseId)->get();
		
		$classSlug  = Classes::select("slug")->where("id", "=", $classId)->get();
		$yearSlug   = date("Y", strtotime($startYear));
		$monthSlug  = date("M", strtotime($startYear));
		
		$batchSlug = $courseSlug['0']->slug.'-'.$classSlug['0']->slug.'-'.strtoupper($monthSlug).'-'.$yearSlug.'-'.$batchCount;
		//print_r($classSlug['0']->slug);
		return $batchSlug;
		
	}
	
	
}