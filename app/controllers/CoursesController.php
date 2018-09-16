<?php

use Illuminate\Support\Facades\View;
class CoursesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

//	public function addCourses(){
//		
//		$currentPage  =  "COURSES";
//		$mainMenu     =  "COURSES_MAIN";
//		
//		$inputs = Input::all();
//		if (isset($inputs['courseName'])) {
//		
//			$courses = new Courses();
//			if(!$courses->validate()){
//				return Redirect::back()->withInput()->withErrors($courses->errors());
//			}
//			else{
//				
//				$courseExistence = Courses::checkWhetherCourseAdded(Session::get('franchiseId'), $inputs['masterCourse']);
//				if($courseExistence == 0){
//					
//					if (Courses::addCourse($inputs)) {
//							
//						Session::flash('msg', "Course added successfully.");
//							
//					} else {
//							
//						Session::flash('warning', "Sorry, Course Could not be added at the moment.");
//					
//					}
//					
//				}elseif($courseExistence > 0){
//					Session::flash('error', "Sorry, Course already added to your franchise.");
//				}
//				
//				
//				return Redirect::to('/courses/add');
//			}
//		}
//		
//		$courseList = CoursesMaster::getCoursesList();
//		$courses    = Courses::getFranchiseCourses(Session::get('franchiseId'));
//		return View::make('pages.courses.addCourse', compact('courseList', 'courses','currentPage','mainMenu'));
//	
//	}
        public function addCourses(){
            if(Auth::check()){
		$currentPage  =  "COURSES";
		$mainMenu     =  "COURSES_MAIN";
		
		$inputs = Input::all();
               // var_dump($inputs); die();
		if (isset($inputs['courseName']) || isset($inputs['masterCourseList'])) {
		
			$courses = new Courses();
                        if(isset($inputs['masterCourseList'])){
                            $inputs['masterCourse'] = $inputs['masterCourseList'];
                            $courseName = json_decode(CoursesMaster::select('slug','course_name')->where("id", "=", $inputs['masterCourseList'])->get());
                           // var_dump($courseName); die();
                            $inputs['courseName'] = $courseName[0]->course_name; 
                            $inputs['slug'] = $courseName[0]->slug; 
                            $addCourse=Courses::addCourse($inputs);
                           if ($addCourse) {
							
						Session::flash('msg', "Course added successfully.");
							
                            } else {
							
						Session::flash('warning', "Sorry, Course Could not be added at the moment.");
					
                            }
                                         return Redirect::to('/courses/name_list');
                        }
                        
                     }
		
		$courseList = CoursesMaster::getCoursesList();
		$courses    = Courses::getFranchiseCourses(Session::get('franchiseId'));
		return View::make('pages.courses.addCourse', compact('courseList', 'courses','currentPage','mainMenu'));
                
                }else{
                    return Redirect::action('VaultController@logout');
                }
	
	}

	public function addCoursesForFranchisee() {
	    if(Auth::check()){ 
	        $currentPage  =  "COURSES";
	        $mainMenu     =  "COURSES_MAIN";
	        $courseList = CoursesMaster::getCoursesList();
	        $franchiseelist = Franchisee::getFList();
	        $courses    = Courses::getFranchiseCourses(Session::get('franchiseId'));
	        // return $courses;
	        return View::make('pages.franchisee.franchiseeCourse', compact('currentPage','mainMenu','courseList','courses','franchiseelist'));
	    }else{
	       return Redirect::action('VaultController@logout');
	    }
	}

    public function getCoursesFranchiseeWise () {
    	$inputs = Input::all();
    	$inputs['franchiseeId'] = (int)$inputs['franchiseeId'];
    	if(Auth::check() && Session::get('userType')=='SUPER_ADMIN'){
    	   $courseList = Courses::where('franchisee_id', '=', $inputs['franchiseeId'])->get();
    	   if ($courseList) {
    	   	return Response::json(array('status'=>'success', 'courseForSelectedFranchisee'=> $courseList));
    	   } else {
    	   	return Response::json(array('status'=>'failure'));
    	   }
    	}
    }  

    public function updateCoursesForFranchisee () {
    	$inputs = Input::all();
    	if(Auth::check() && Session::get('userType')=='SUPER_ADMIN'){
	    	$getCourseName = CoursesMaster::where('id', '=', $inputs['coursId'])->get();
	    	$updateCourse = Courses::updateCourses($inputs, $getCourseName);
	    	if ($updateCourse) {
	    		return Response::json(array('status'=>'success'));
	    	} else {
	    		return Response::json(array('status'=>'failure'));
	    	}
	    }
    }

        public function courseNameList() {
            if(Auth::check()){ 
                $currentPage  =  "COURSES";
                $mainMenu     =  "COURSES_MAIN";
                $courseList = CoursesMaster::getCoursesList();
                $courses    = Courses::getFranchiseCourses(Session::get('franchiseId'));
                return View::make('pages.courses.course-name-list', compact('currentPage','mainMenu','courseList','courses'));
           }else{
               return Redirect::action('VaultController@logout');
           }
        }
        
        
	public function viewCourses(){
            if(Auth::check() && Session::get('userType')=='ADMIN'){
		$currentPage  =  "COURSES";
		$mainMenu     =  "COURSES_MAIN";
		$eligibleForAction = array();
		$allCourse = CoursesMaster::getAllCourses();
		for ($i=0; $i < count($allCourse) ; $i++) { 
			$Master_course_id = $allCourse[$i]['id'];
			$getCoursesCount = Courses::where('master_course_id', '=', $Master_course_id)->get();
			if(count($getCoursesCount) == 0){
				array_push($eligibleForAction, $Master_course_id);
			}
		}
		//return $eligibleForAction;
		return View::make('pages.courses.courses', compact('currentPage','mainMenu', 'allCourse', 'eligibleForAction'));	
            }else{
                return Redirect::action('VaultController@logout');
            }
                
            }

    	public function viewCoursesAdmin(){
                if(Auth::check() && Session::get('userType')=='SUPER_ADMIN'){
    		$currentPage  =  "COURSES";
    		$mainMenu     =  "COURSES_MAIN";
    		$eligibleForAction = array();
    		$allCourse = CoursesMaster::getAllCourses();
    		for ($i=0; $i < count($allCourse) ; $i++) { 
    			$Master_course_id = $allCourse[$i]['id'];
    			$getCoursesCount = Courses::where('master_course_id', '=', $Master_course_id)->get();
    			if(count($getCoursesCount) == 0){
    				array_push($eligibleForAction, $Master_course_id);
    			}
    		}
    		//return $eligibleForAction;
    		return View::make('pages.courses.courses', compact('currentPage','mainMenu', 'allCourse', 'eligibleForAction'));	
                }else{
                    return Redirect::action('VaultController@logout');
                }
                    
                }


	public function deleteCoursesMaster(){
		$inputs = Input::all();
		$sendDataToDelete = CoursesMaster::deleteCoursesMaster($inputs);
		if($sendDataToDelete){
			return Response::json(array('status'=>'success', $sendDataToDelete));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}

	public function updateCoursesMaster(){
		$inputs = Input::all();
		//return Response::json(array('status'=>'success', Session::get('userId')));
		$sendDataToDelete = CoursesMaster::updateCoursesMaster($inputs);
		if($sendDataToDelete){
			return Response::json(array('status'=>'success', $sendDataToDelete));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}

	public function getAllCoursesForFranchisee () {
		$inputs = Input::all();
		$courses = Courses::where('franchisee_id', '=', $inputs['franchisee_id'])->get();
		$basePrice = ClassBasePrice::where('franchise_id','=',$inputs['franchisee_id'])
							->get();
		if($courses){
			return Response::json(array('status'=>'success', 'courses'=> $courses, 'baseBrice' => $basePrice));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	} 

	public function getBasePriceForAllClasses () {
		$inputs = Input::all();
		$basePrice = ClassBasePrice::where('base_price_no','=',$inputs['classId'])
							->where('franchise_id','=',$inputs['franchisee_id'])
							->get();
		if($basePrice){
			return Response::json(array('status'=>'success', 'basePrice'=> $basePrice));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	} 

	public function getAllClassesForFranchisee () {
		$inputs = Input::all();
		$classes = ClassesMaster::where('course_master_id', '=', $inputs['courseId'])
						  ->get();
		if($classes){
			return Response::json(array('status'=>'success', 'classesForCourse'=> $classes));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}

	public function addNewClassToFrnachisee () {
		$inputs = Input::all();
		$send_details = Classes::InsertNewClassThroughSuperAdmin($inputs);
		if($send_details){
			return Response::json(array('status'=> 'success', 'test'=> $send_details));
		}else{
			return Response::json(array('status'=> 'failure', $send_details));
		}
	}
	

	public function InsertNewCoursesMaster(){
		$inputs = Input::all();
		//return Response::json(array('status'=>'success', Session::get('userId')));
		$sendDataToDelete = CoursesMaster::InsertNewCoursesMaster($inputs);
		if($sendDataToDelete){
			return Response::json(array('status'=>'success', $sendDataToDelete));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}

	public function getAllClassesForFranchiseeWise() {
		$inputs = Input::all();
		$getAllClassesForFranchise = Classes::where('franchisee_id', '=', $inputs['franchisee_id'])->get();
		$franchiseeBaseprice = ClassBasePrice::where('franchise_id', '=', $inputs['franchisee_id'])->get();
		for($i=0;$i<sizeof($getAllClassesForFranchise);$i++){
			$courseMasterId = Courses::where('id','=',$getAllClassesForFranchise[$i]['course_id'])->get();
			$courseMasterId = $courseMasterId[0];
			$courseName = CoursesMaster::where('id', '=', $courseMasterId['master_course_id'])->get();
			$courseName = $courseName[0];
			$getAllClassesForFranchise[$i]['course_name']=$courseName['course_name'];
	        $temp=ClassBasePrice::where('base_price_no','=',$getAllClassesForFranchise[$i]['base_price_no'])->where('franchise_id','=',$inputs['franchisee_id'])->get();
	        $getAllClassesForFranchise[$i]['base_price']=$temp[0]['base_price'];        
	    }
	    if($getAllClassesForFranchise){
	    	return Response::json(array('status'=>'success', 'classesForFranchisee'=> $getAllClassesForFranchise));
	    }else{
	    	return Response::json(array('status'=>'failure'));
	    }
	}

	public function getBasePricesForFranchisee() {
		$inputs = Input::all();
		$base_price_no = Classes::where('id', '=', $inputs['class_id'])->get();
		$getExactBasePrice = ClassBasePrice::where('franchise_id', '=', $inputs['franchisee_id'])
							->where('base_price_no', '=', $base_price_no[0]['base_price_no'])
							->get();
		$franchiseeBaseprice = ClassBasePrice::where('franchise_id', '=', $inputs['franchisee_id'])->get();
		if($franchiseeBaseprice){
			return Response::json(array('status'=>'success', 'basePrice'=> $franchiseeBaseprice, 'getExactBasePrice' => $getExactBasePrice));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}

	public function updateClassesBasePriceForFranchisee () {
		$inputs = Input::all();
		$classes = Classes::where('franchisee_id', '=', $inputs['franchisee_id'])
						  ->where('id', '=', $inputs['class_id'])
						  ->update(['base_price_no' => $inputs['BasePriceNo'], 'updated_at' => date("Y-m-d H:i:s")]);
		if($classes){
			return Response::json(array('status'=>'success'));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
