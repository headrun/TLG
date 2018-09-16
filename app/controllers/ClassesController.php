<?php
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
class ClassesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function InsertNewClass(){
		$inputs = Input::all();
		$send_details = ClassesMaster::InsertNewClass($inputs);
		if($send_details){
			return Response::json(array('status'=> 'success', $send_details));
		}else{
			return Response::json(array('status'=> 'failure', $send_details));
		}
	}
        
        

	public function InsertNewClassFromFranchise(){
		$inputs = Input::all();
		$send_details = Classes::InsertNewClassFromFranchise($inputs);
		if($send_details){
			return Response::json(array('status'=> 'success', 'test'=> $send_details));
		}else{
			return Response::json(array('status'=> 'failure', $send_details));
		}
	}




	public function index()
	{
		//
		$currentPage  =  "COURSES";
		$mainMenu     =  "COURSES_MAIN";
		
		$inputs = Input::all();
		
			
		if(isset($inputs['className']) && isset($inputs['franchiseeCourse'])){
			
			
			
			$classadd = Classes::addClasses($inputs);
			if ($classadd == "exists") {
				Session::flash('error', "Sorry, Class you are trying to add already exists");				
					
			} elseif(!$classadd) {
					
				Session::flash('error', "Sorry, Class Could not be added at the moment.");
				
					
			}else{
				Session::flash('msg', "Class added successfully.");
			}
		}
		
		$courseList = CoursesMaster::getCoursesList();
		//$classesMaster = ClassesMaster::getClassesMasterForDropDown();
		
		$franchiseeCourses = Courses::getFranchiseCoursesList(Session::get('franchiseId'));
		$classes = Classes::getAllClasses(1);
		return View::make('pages.classes.classes', compact('classesMaster', 'classes', 'courseList','franchiseeCourses','currentPage','mainMenu'));
	}



	public function add_new_classes(){
               if(Auth::check() && Session::get('userType')=='ADMIN'){
		$currentPage  =  "CLASSES";
		$mainMenu     =  "CLASSES_MAIN";
		$franchiseeCourses = CoursesMaster::getAllCourses();
		$getAllClassesMasters = ClassesMaster::getAllClassesMasters();
		for($i=0;$i<sizeof($getAllClassesMasters);$i++){
                $courseName=CoursesMaster::where('id','=',$getAllClassesMasters[$i]['course_master_id'])->get();
                $courseName=$courseName[0];
                $getAllClassesMasters[$i]['course_master_name']=$courseName['course_name'];
                }
		return View::make('pages.classes.add_new_classes', compact('currentPage','mainMenu', 'franchiseeCourses', 'getAllClassesMasters'));
               }else{
                   return Redirect::action('DashboardController@index');
               }
                
        }

    public function addNewClass() {
	    if(Auth::check() && Session::get('userType')=='SUPER_ADMIN'){
			$currentPage  =  "CLASSES";
			$mainMenu     =  "CLASSES_MAIN";
			$franchiseeCourses = CoursesMaster::getAllCourses();
			$getAllClassesMasters = ClassesMaster::getAllClassesMasters();
			for($i=0;$i<sizeof($getAllClassesMasters);$i++){
	            $courseName=CoursesMaster::where('id','=',$getAllClassesMasters[$i]['course_master_id'])->get();
	            $courseName=$courseName[0];
	            $getAllClassesMasters[$i]['course_master_name']=$courseName['course_name'];
            }
			return View::make('pages.classes.super_admin_new_classes', compact('currentPage','mainMenu', 'franchiseeCourses', 'getAllClassesMasters'));
           }else{
               return Redirect::action('DashboardController@index');
           }
    }

	public function add_new_class_franchise(){
            if(Auth::check()){
		$currentPage  =  "CLASSES_FRANCHISE";
		$mainMenu     =  "CLASSES_MAIN";
		$franchiseeCourses = Courses::where('franchisee_id', '=', Session::get('franchiseId'))->get();
		$franchiseeBaseprice = ClassBasePrice::getBasePricebyFranchiseeId();
		$getAllClassesForFranchise = Classes::getAllClassesForFranchise();
                

		for($i=0;$i<sizeof($getAllClassesForFranchise);$i++){
			$courseMasterId = Courses::where('id','=',$getAllClassesForFranchise[$i]['course_id'])->get();
			$courseMasterId = $courseMasterId[0];
			$courseName = CoursesMaster::where('id', '=', $courseMasterId['master_course_id'])->get();
			$courseName = $courseName[0];

			$getAllClassesForFranchise[$i]['course_name']=$courseName['course_name'];

//			$getBasePrice = ClassBasePrice::where('base_price_no', '=', $getAllClassesForFranchise[$i]['base_price_no'])->get();
//			$getBasePrice = ClassBasePrice::where('base_price_no', '=', $getAllClassesForFranchise[$i]['base_price_no'])->get();
//                //$getBasePrice = $getBasePrice[0];
//                if($getAllClassesForFranchise[$i]['base_price_no'] == 0){
//                	$getAllClassesForFranchise[$i]['base_price'] = '';
//                }
//                else{
//                	$getAllClassesForFranchise[$i]['base_price']=$getBasePrice[0]['base_price'];	
//                }
                  $temp=ClassBasePrice::where('base_price_no','=',$getAllClassesForFranchise[$i]['base_price_no'])->where('franchise_id','=',Session::get('franchiseId'))->get();
                  $getAllClassesForFranchise[$i]['base_price']=$temp[0]['base_price'];
                    
                        
                }
		
        //return $getAllClassesForFranchise;
		return View::make('pages.classes.add_new_class_franchise', compact('currentPage','mainMenu', 'franchiseeCourses', 'franchiseeBaseprice', 'getAllClassesForFranchise'));
        }else{
            return Redirect::action('VaultController@logout');
        }
        }


    public function addNewClassFranchisee() {
    	if(Auth::check() && Session::get('userType')=='SUPER_ADMIN'){
    			$currentPage  =  "CLASSES_FRANCHISE";
    			$mainMenu     =  "CLASSES_MAIN";
    			$franchiseeCourses = Courses::where('franchisee_id', '=', Session::get('franchiseId'))->get();
    			$franchiseeBaseprice = ClassBasePrice::getBasePricebyFranchiseeId();
    			$getAllClassesForFranchise = Classes::getAllClassesForFranchise();
    			$franchiseelist = Franchisee::getFList();
    	                
    			for($i=0;$i<sizeof($getAllClassesForFranchise);$i++){
    				$courseMasterId = Courses::where('id','=',$getAllClassesForFranchise[$i]['course_id'])->get();
    				$courseMasterId = $courseMasterId[0];
    				$courseName = CoursesMaster::where('id', '=', $courseMasterId['master_course_id'])->get();
    				$courseName = $courseName[0];

    				$getAllClassesForFranchise[$i]['course_name']=$courseName['course_name'];
	                  $temp=ClassBasePrice::where('base_price_no','=',$getAllClassesForFranchise[$i]['base_price_no'])->where('franchise_id','=',Session::get('franchiseId'))->get();
	                  $getAllClassesForFranchise[$i]['base_price']=$temp[0]['base_price'];       
	                }
    				return View::make('pages.classes.super_admin_add_class_franchisee', compact('currentPage','mainMenu', 'franchiseeCourses', 'franchiseeBaseprice', 'getAllClassesForFranchise', 'franchiseelist'));
    	        }else{
    	            return Redirect::action('VaultController@logout');
    	        }
    }

	public function updateClassesMaster(){
		$inputs = Input::all();
		$send_details = ClassesMaster::updateClassesMaster($inputs);
		if($send_details){
			return Response::json(array('status'=>'success', $send_details));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}


	public function updateClassesBasePrice(){
		$inputs = Input::all();
		$send_details = Classes::find($inputs['class_id']);
                $send_details->base_price_no=$inputs['BasePriceNo'];
                $send_details->save();
		if($send_details){
			return Response::json(array('status'=>'success', $send_details));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}

	public function getClassesByCourseId(){
		$inputs = Input::all();
		$ClassName = ClassesMaster::where('course_master_id', '=', $inputs['CoursemasterId'])->get();
		if($ClassName){
			return Response::json(array('status'=>'success', $ClassName));
		}else{
			return Response::json(array('status'=>'failure'));
		}
	}


	
	public function classesbymaster(){
		$franchiseeCourse = Input::get('franchiseeCourse');
		//$classess = ClassesMaster::getClassesMasterForDropDown($courseMasterId);
		$classess = $classess = Classes::getClassessByFranchiseeCourseId(Session::get('franchiseId'), $franchiseeCourse);
		header('Access-Control-Allow-Origin: *');
		return Response::json($classess);
		
	}
	
	
	public function classesbyCourse(){
		
		$franchiseeCourse = Input::get('franchiseeCourse');
		//$classess = ClassesMaster::getClassesMasterForDropDown($courseMasterId);
		$classess = Classes::getAllClassesLists(Session::get('franchiseId'), $franchiseeCourse);
		header('Access-Control-Allow-Origin: *');
		return Response::json($classess);
		
	}
	
	
	public function eligibleClassess(){

		$ageYear  = Input::get('ageYear');
		$ageMonth = Input::get('ageMonth');
		$gender   = Input::get('gender');
		$yearandMonth = Input::get('yearAndMonth');
      if($yearandMonth >= 4){
        $classesMaster = ClassesMaster::select('id')->where("class_start_age", "<=", $yearandMonth)
																	->where("class_end_age", ">=", $yearandMonth)
																				->where("age_start_limit_unit", "=", "months")
                        								->where("age_end_limit_unit", "=", "months")
																				->get();
      }
		$masterClassIDs = array();
		$i = 0;
		foreach($classesMaster->toArray() as $masterClass){
				
			$masterClassIDs[$i] = $masterClass['id'];
			$i++;
		}
		
		
		
		$classesEligible = DB::table('classes')
														->whereIn('class_master_id', $masterClassIDs)
														->where('franchisee_id', '=', Session::get('franchiseId'))
														->select('id','class_name')
														->get();
		
		header('Access-Control-Allow-Origin: *');
		return Response::json(array('status'=>'success','data'=>$classesEligible));
	}
        
        
        public function eligibleClassessForOtherBatches(){
            $inputs=Input::all();
            $ageYearForBatch2=date_diff(date_create(date('Y-m-d',strtotime($inputs['studentDob']))), date_create($inputs['FutureAgeDate']))->y;
            $ageMonthForBatch2=date_diff(date_create(date('Y-m-d',strtotime($inputs['studentDob']))), date_create($inputs['FutureAgeDate']))->m;
            $yearAndMonthForBatch2 =($ageYearForBatch2 * 12) + ($ageMonthForBatch2);
            
            if($yearAndMonthForBatch2 >= 4){
                     $classesMaster = ClassesMaster::select('id')->where("class_start_age", "<=", $yearAndMonthForBatch2)
			->where("class_end_age", ">=", $yearAndMonthForBatch2)
			->where("age_start_limit_unit", "=", "months")
                        ->where("age_end_limit_unit", "=", "months")
			->get();
            }
            $masterClassIDs = array();
		$i = 0;
		foreach($classesMaster->toArray() as $masterClass){
				
			$masterClassIDs[$i] = $masterClass['id'];
			$i++;
		}
		
		$classesEligible = DB::table('classes')
                                        ->whereIn('class_master_id', $masterClassIDs)
                                        ->where('franchisee_id', '=', Session::get('franchiseId'))
                                        ->get();
		
                header('Access-Control-Allow-Origin: *');
		return Response::json($classesEligible);
        }
	
        public function eligibleClassessForIV(){
            $classesMaster = ClassesMaster::select('id')
			->where("age_start_limit_unit", "=", "months")
                        ->where("age_end_limit_unit", "=", "months")
			->get();
            $masterClassIDs = array();
		$i = 0;
		foreach($classesMaster->toArray() as $masterClass){
				
			$masterClassIDs[$i] = $masterClass['id'];
			$i++;
		}
		
		$classesEligible = DB::table('classes')
                                        ->whereIn('class_master_id', $masterClassIDs)
                                        ->where('franchisee_id', '=', Session::get('franchiseId'))
                                        ->get();
		
                header('Access-Control-Allow-Origin: *');
		return Response::json($classesEligible);
        
        }
        
	public function batchesByClassSeasonId(){
		$inputs=Input::all();
		$classId = Input::get('classId');
                $seasonId=Input::get('seasonId');
		$batches = Batches::batchesByClassIdSeasonId($classId,$seasonId);
           
		$batchesJson = array();
		$i = 0;
		foreach ($batches as $batch){
				
			$batchesJson[$i]['id'] = $batch->id;
			$batchesJson[$i]['batch_name'] = $batch->batch_name;
			$batchesJson[$i]['day'] = date('l', strtotime($batch->start_date));
			$batchesJson[$i]['start_time'] = date('G:i a', strtotime($batch->preferred_time));
			$batchesJson[$i]['end_time'] = date('G:i a', strtotime($batch->preferred_end_time));
			if(isset($batch->LeadInstructors->first_name)){
				$batchesJson[$i]['instructor'] = '('.$batch->LeadInstructors->first_name.' '.$batch->LeadInstructors->last_name.')';
			}else{
				$batchesJson[$i]['instructor'] = '';
			}
			$i++;
		}
		
		header('Access-Control-Allow-Origin: *');
		return Response::json($batchesJson);
	}
	
	
	public function getDiscount(){
       $inputs=Input::all();
       //return Response::json(array('status'=>'success'));
       $discount_second_child;
       $discount_second_class;
       
       $discount_second_child_elligible=0;
       $discount_second_class_elligible=0;
       
       $DiscountApprove = Discounts::where('franchiseId', '=', Session::get('franchiseId'))->first();
       if($DiscountApprove['discount_second_child_approve'] != 0 && $DiscountApprove['discount_second_class_approve'] != 0){
        $discount_second_child_elligible=1;
        $discount_second_class_elligible=1;
       	$discount_second_child = $DiscountApprove['discount_second_child'];
       	$discount_second_class = $DiscountApprove['discount_second_class'];
       }elseif($DiscountApprove['discount_second_child_approve'] == 0 && $DiscountApprove['discount_second_class_approve'] != 0){
       	$discount_second_class_elligible=1;
        $discount_second_class = $DiscountApprove['discount_second_class'];
       }elseif($DiscountApprove['discount_second_child_approve'] != 0 && $DiscountApprove['discount_second_class_approve'] == 0){
       	$discount_second_child_elligible=1;
        $discount_second_child = $DiscountApprove['discount_second_child'];	
       }
       
       if($discount_second_class){
           
          
       }
       

       return Response::json(array('status'=>'success','discount_second_child'=> $discount_second_child, 'discount_second_class'=> $discount_second_class));
            
            
            
            
//        $inputs=Input::all();
//        $classes_count=  StudentClasses::where('student_id','=',$inputs['studentId'])
//                                        ->where('status','=','enrolled')
//                                        //->whereDate('enrollment_start_date','>=',date("Y-m-d"))
//                                        //->whereDate('enrollment_end_date','<=',date("Y-m-d"))
//                                        ->distinct('class_id')
//                                        ->count();
//        $discount=0;
//        if($classes_count>=1){
//            $discount_data= Discounts::where('season_id','=',$inputs['seasonId'])->get();
//            $discount=$discount_data[0]['discount_second_class'];
//        }else{
//            
//            $student_data = Students::where('id','=',$inputs['studentId'])->get();
//            $student_data = Students::where('customer_id','=',$student_data[0]['customer_id'])->get();
//            $sid;
//            foreach($student_data as $s){
//                $sid[]=$s['id'];
//            }
//            $count=0;
//            for($i=0;$i<count($sid);$i++){
//                if(StudentClasses::where('student_id','=',$sid[$i])->exists()){
//                 $count++;   
//                }
//            }
//            if($count>=1){
//                $discount_data= Discounts::where('season_id','=',$inputs['seasonId'])->get();
//                $discount=$discount_data[0]['discount_second_child'];
//            }
//            
//        }
        
//        return Response::json(array('status'=>'success','discount'=>$discount));
        }
       public function UpdateEaDate(){
       		if(Auth::check()){
               	$inputs=Input::all();
//		return $inputs;
		$getClassAndSeasonIds = Batches::where('franchisee_id','=',Session::get('franchiseId'))
						->where('id','=',$inputs['updateToBatchId'])
						->get();
               	$present_date = Carbon::now();
          	$update_attendance = DB::table('attendance')->insert(['student_id' => $inputs['studentId'], 'student_classes_id' =>$inputs['classId'], 'status' => 'EA','makeup_class_given' => '1', 'batch_id' => $inputs['batchId'], 'attendance_date' => $inputs['attDate'], 'description_absent' => $inputs['desc'], 'created_at'=>$present_date , 'updated_at'=>$present_date ]);
          		$insert_into_student_classes = StudentClasses::insert(['student_id' => $inputs['studentId'],
								'class_id' => $getClassAndSeasonIds[0]['class_id'],
								'season_id' => $getClassAndSeasonIds[0]['season_id'],
								'franchisee_id' => Session::get('franchiseId'),
								'batch_id' => $inputs['updateToBatchId'],
								'enrollment_start_date'	=> $inputs['date'],
								'enrollment_end_date' => $inputs['date'],
								'selected_sessions' => '1',
								'status' => 'makeup',
								'created_at' => $present_date,
								'updated_at' => $present_date,
								'introvisit_id' => '0'
										
							]);
            							
	  //      return $insert_into_student_classes;
	        if($insert_into_student_classes){
                    return Response::json(array('status'=>'success','data'=>$inputs));
                }else{
                    return Response::json(array('status'=>'failure'));   
                }
           }
       }

      public function UpdateLeadStatus(){
  		if(Auth::check()){
          	$inputs=Input::all();

			if($inputs['leadStatus'] == 'Yes'){  
                $leadStatus = 'very_interested';
			}else if($inputs['leadStatus'] == 'May be'){
				$leadStatus = 'interested';
			}else {
				$leadStatus = 'not_interested';
			}
			$updateCommentType = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
										->where('introvisit_id', '=', $inputs['ivId'])
										->update(['lead_status' => $leadStatus]);
          	/* $present_date = Carbon::now();
     		$update_attendance = DB::table('attendance')->insert(['student_id' => $inputs['studentId'], 'student_classes_id' =>$inputs['classId'], 'status' => 'EA','makeup_class_given' => '1', 'batch_id' => $inputs['batchId'], 'attendance_date' => $inputs['attDate'], 'description_absent' => $inputs['desc'], 'created_at'=>$present_date , 'updated_at'=>$present_date ]);
     		$insert_into_student_classes = StudentClasses::insert(['student_id' => $inputs['studentId'],
							'class_id' => $getClassAndSeasonIds[0]['class_id'],
							'season_id' => $getClassAndSeasonIds[0]['season_id'],
							'franchisee_id' => Session::get('franchiseId'),
							'batch_id' => $inputs['updateToBatchId'],
							'enrollment_start_date'	=> $inputs['date'],
							'enrollment_end_date' => $inputs['date'],
							'selected_sessions' => '1',
							'status' => 'makeup',
							'created_at' => $present_date,
							'updated_at' => $present_date,
							'introvisit_id' => '0'
									
						]);*/
       							
  //      return $insert_into_student_classes;
        	if($updateCommentType){
               return Response::json(array('status'=>'success','data'=>$inputs));
           }else{
               return Response::json(array('status'=>'failure'));   
           }
      	}
      }
      
	
        
        
        
       public function createMakeupClass(){
           
           if(Auth::check()){
               $inputs=Input::all();
                 //create makeup
                  if(
                   StudentClasses::where('student_id','=',$inputs['student_id'])
                                   ->where('season_id','=',$inputs['mu_season_id'])
                                   ->where('class_id','=',$inputs['mu_class_id'])
                                   ->where('batch_id','=',$inputs['mu_batches_id'])
                                   ->whereDate('enrollment_start_date','<=',$inputs['mu_date'])
                                   ->whereDate('enrollment_end_date','>=',$inputs['mu_date'])
                                   ->exists()
                     ){
                      
                      return Response::json(array('status'=>'exists'));
                     }else{
                        // return Response::json(array('status'=>'success','data'=>$inputs));
                   $student_data['studentId']=$inputs['student_id'];
                   $student_data['seasonId']=$inputs['mu_season_id'];
                   $student_data['classId']=$inputs['mu_class_id'];
                   $student_data['batchId']=$inputs['mu_batches_id'];
                   $student_data['enrollment_start_date']=$inputs['mu_date'];
                   $student_data['enrollment_end_date']=$inputs['mu_date'];
                   $student_data['selected_sessions']='1';
                   $student_data['attendance_id']=$inputs['attendance_id'];
                   $student_data['status']='makeup';
                   $created_makeup_class=StudentClasses::addStudentClass($student_data);
                   $update_attendance=Attendance::where('batch_id','=',$inputs['ea_batch_id'])
                                  ->where('student_id','=',$inputs['student_id'])
                                  ->where('attendance_date','=',$inputs['eadate'])
                                  ->where('status','=','EA')
                                  ->update(array('makeup_class_given'=>'1','student_class_id'=>$created_makeup_class->id));
                   
                   if($update_attendance){
                    return Response::json(array('status'=>'success','data'=>$inputs));
                   }else{
                    return Response::json(array('status'=>'failure'));   
                   }
                     }
                }
            }
       
       
       
       public function getMakeupdatabyBatchId(){
           if(Auth::check()){
               $inputs=Input::all();
               $student_classes;
               $attendance_data=Attendance::where('batch_id','=',$inputs['batch_id'])
                                            ->where('student_id','=',$inputs['student_id'])
                                            ->where('makeup_class_given','=',1)
                                            ->where('student_class_id','!=',0)
                                            ->get();
               if(count($attendance_data)>0){
               for($i=0;$i<count($attendance_data);$i++){
                   $student_classes[$i]=  StudentClasses::where('id','=',$attendance_data[$i]['student_class_id'])->get();
                   $temp=Batches::find($student_classes[$i][0]['batch_id']);
                   $timestamp = strtotime($temp->start_date);
                   $temp2=User::find($temp['lead_instructor']);
                   $student_classes[$i][0]['batch_name']=$temp->batch_name." ". date('l', $timestamp) ."(".$temp->preferred_time."-".$temp->preferred_end_time.")".$temp2->first_name.$temp2->last_name;
                   $temp3=User::find($student_classes[$i][0]['created_by']);
                   $student_classes[$i][0]['receivedby']=$temp3->first_name.$temp3->last_name;
                   
               }
               }else{
                   return Response::json(array('status'=>'nomakeupmade'));
               }
               if($student_classes){
                return Response::json(array('status'=>'success','attendancedata'=>$attendance_data,'student_class_data'=>$student_classes));
               }else{
                return Response::json(array('status'=>'failure'));   
               }
           }
       }
       
       
       
       public function getTransferkiddatabyBatchId(){
            $inputs = Input::all();
            $student_class_data['student_class_data']=StudentClasses::where('student_id','=',$inputs['student_id'])
                                                                     ->where('batch_id','=',$inputs['batch_id'])
                                                                     ->whereIn('status',array('enrolled','transferred_class'))
                                                                     ->get();
            $count=0;
            for($i=0;$i<count($student_class_data['student_class_data']);$i++){
                $student_class_data['student_class_data'][$i]['attendance_count']=Attendance::where('batch_id','=',$inputs['batch_id'])
                                                               ->where('student_id','=',$inputs['student_id'])
                                                               ->whereIn('status',array('P','A','EA'))
                                                               ->whereDate('attendance_date','>=',$student_class_data['student_class_data'][$i]['enrollment_start_date'])
                                                               ->whereDate('attendance_date','<=',$student_class_data['student_class_data'][$i]['enrollment_end_date'])
                                                               ->count();
                if($student_class_data['student_class_data'][$i]['attendance_count']!=$student_class_data['student_class_data'][$i]['selected_sessions']){
                    $student_class_data['student_class_data'][$i]['attendance_incomplete_count']=$student_class_data['student_class_data'][$i]['selected_sessions']-$student_class_data['student_class_data'][$i]['attendance_count'];
                    $count+=$student_class_data['student_class_data'][$i]['attendance_incomplete_count'];
                }
            }
            $season_data=Seasons::where('franchisee_id','=',Session::get ( 'franchiseId' ))
                                  ->whereNotIn('season_type', ['Summer Season'])
                                  ->orderBy('id', 'DESC')
                                  ->get();
            $classData = Classes::where('franchisee_id', '=', Session::get('franchiseId'))->get();
            return Response::json(array('status'=>'success','remainingclass_count'=>$count,'season_data'=>$season_data,'class_data'=>$classData));
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
