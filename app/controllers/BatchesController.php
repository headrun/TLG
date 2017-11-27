<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
class BatchesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		
		if(Auth::check()){
			
			$currentPage  =  "BATCHES";
			$mainMenu     =  "COURSES_MAIN";
			
			$inputs = Input::all();
                        
			if(isset($inputs['startTime'])){
				
				//$startDate = //date('Y-m-d', strtotime($season_data['end_date']));
                                $season_data=Seasons::where('id','=',$inputs['selectSeason'])->get();
                                $season_data=$season_data[0];
                                $startDate=new Carbon();
                                $endDate=new Carbon();
                                $startDate =$startDate->createFromFormat('Y-m-d', $season_data['start_date']);
                                $endDate =$endDate->createFromFormat('Y-m-d', $season_data['end_date']);
                              
                             //taking season start date, end date and making calculation to start to specific day(MON,TUE...)
                                switch ($inputs['day']){
                                    case 0:  break;
                                    case 1: $startDate->addDays(1); break;
                                    case 2: $startDate->addDays(2); break;
                                    case 3: $startDate->addDays(3); break;
                                    case 4: $startDate->addDays(4); break;
                                    case 5: $startDate->addDays(5); break;
                                    case 6: $startDate->addDays(6); break;
                                    
                                }
                              
                                switch ($inputs['day']){
                                    case 0: $endDate->subDays(6); break;
                                    case 1: $endDate->subDays(5);break;
                                    case 2: $endDate->subDays(4);break;
                                    case 3: $endDate->subDays(3);break;
                                    case 4: $endDate->subDays(2);break;
                                    case 5: $endDate->subDays(1);break;
                                    case 6: break;
                                  
                                }
                                $date=$startDate->day;
                                $yr=$startDate->year;
                                $month=$startDate->month;
                                
                                $inputs['startDate']=$date.'-'.$month.'-'.$yr;
                                $courseId  = $inputs['franchiseeCourse'];
				$classId   = $inputs['className'];
				$startTime = $inputs['startTime'];
				$endTime   = $inputs['endTime'];
				$leadInstructor   = $inputs['leadInstructor'];
				$alternateInstructor   = $inputs['alternateInstructor'];
				
				
				$day       = date('N', strtotime($inputs['startDate']));
				
				
				
				if(date('m', strtotime($inputs['startDate'])) >= 1 && date('m', strtotime($inputs['startDate'])) <= 1){
					
					$endDateYear = date('Y', strtotime($inputs['startDate']));
				}else{
					$endDateYear = date('Y', strtotime($inputs['startDate']));
					$endDateYear = ($endDateYear+1);
				}
				/*
				echo $startDate.'  =   '.$endDate ;//Year.'-03-31';
				 exit(); 
                                */
				$months = getMonthsBetweenDates($startDate, $endDate);//Year.'-03-31');
				
				/*
				 echo "<pre>";
				print_r($months);
				echo "</pre>";
				
				exit(); 
				*/
				
				$i = 0;
				foreach($months as $month){
						
					$yearAndMonth = explode('-', $month['month']);
					$daysFound[$i] = getDaysFromMonth($yearAndMonth['1'], $yearAndMonth['0'], $day);
					$i++;
				}
				
				
				
				$timeString = date('Y-m-d', strtotime($inputs['startDate'])).$inputs['startTime'];
				$timestamp =  strtotime($timeString);
				$startTime24Hours = date('H:i:s',$timestamp);
					
				$timeString = $endDateYear.'-3-5 '.$inputs['endTime'];
				$timestamp =  strtotime($timeString);
				
				$endTime24Hours = date('H:i:s',$timestamp);
			
				
				
				$batchSlug =  Courses::getBatchID($courseId, $classId, $startDate, null,$inputs['selectSeason']);
				$inputBatch['batchName']     = $batchSlug;
				$inputBatch['classId']       = $classId;
				$inputBatch['courseId']      = $courseId;
				$inputBatch['startDate']     = $startDate->toDateString();
                                $inputBatch['endDate']     = $endDate->toDateString();
                                $inputBatch['season_id']   =$inputs['selectSeason'];
				$inputBatch['preferredTime'] = $startTime24Hours;
				$inputBatch['preferredEndTime'] = $endTime24Hours;
				$inputBatch['leadInstructor'] = $leadInstructor;
				$inputBatch['alternateInstructor'] = $alternateInstructor;
				$inputBatch['location_id']=$inputs['seasonLocation'];
                                 $inputBatch['batchLimitCbx']=$inputs['batchLimitCbx'];
				
				$newBatch = Batches::addBatches($inputBatch);
				/*
				$days = 1;
				foreach($daysFound as $monthdays){
					
					foreach($monthdays as $dayFound){
						
						if($days <= 40){
						$batchScheduleInput['batchId']      = $newBatch->id;
                                                $batchScheduleInput['seasonId']      = $inputs['selectSeason'];
						$batchScheduleInput['scheduleDate'] = $dayFound;
						$batchScheduleInput['startTime']    = $startTime24Hours;
						$batchScheduleInput['endTime']      = $endTime24Hours;
						$batchScheduleInput['scheduleType']= 'class';
						BatchSchedule::addSchedule($batchScheduleInput);
						}
						
						$days++;
					}
				}
                                */
                                
                                //calculating dates and adding all the dates to batchschedule for new batch 
                                
                                $batch_schedule=new BatchSchedule();
                                            do{
                                                $batchScheduleInput['batchId']      = $newBatch->id;
                                                $batchScheduleInput['seasonId']      = $inputs['selectSeason'];
						$batchScheduleInput['scheduleDate'] = $startDate->toDateString();
						$batchScheduleInput['startTime']    = $startTime24Hours;
						$batchScheduleInput['endTime']      = $endTime24Hours;
						$batchScheduleInput['scheduleType']= 'class';
						BatchSchedule::addSchedule($batchScheduleInput);
                                                $startDate->addDays(7);
                                            }while($startDate->eq($endDate)==FALSE);
                                            //for last date
                                                $batchScheduleInput['batchId']      = $newBatch->id;
                                                $batchScheduleInput['seasonId']      = $inputs['selectSeason'];
						$batchScheduleInput['scheduleDate'] = $startDate->toDateString();
						$batchScheduleInput['startTime']    = $startTime24Hours;
						$batchScheduleInput['endTime']      = $endTime24Hours;
						$batchScheduleInput['scheduleType']= 'class';
						BatchSchedule::addSchedule($batchScheduleInput);
                                
                                //check for holidays if exists make
                                        if((Holidays::where('season_id','=',$inputs['selectSeason'])->count())>0){        
                                        $holiday_data=  Holidays::where('season_id','=',$inputs['selectSeason'])->select('startdate','enddate')->get();
                                            for($i=0;$i<count($holiday_data);$i++){
                                               DB::table('batch_schedule')
                                                                      ->where('batch_id','=',$newBatch->id)
                                                                      ->whereBetween('schedule_date',array($holiday_data[$i]['startdate'],$holiday_data[$i]['enddate']))
                                                                      ->update(array('holiday'=>1));
                                                    
                                            }
                                        
                                        }
                                        
				Session::flash('msg', "Batch added successfully.");
				return Redirect::to('batches');
			}
			$franchiseeId = Session::get('franchiseId');
                        $season_display_data=Seasons::  where('franchisee_id','=',Session::get('franchiseId'))
                                                      ->orderBy('id', 'DESC')
                                                      ->get();
                        if(isset($season_display_data[0])){
                        $s_id=$season_display_data[0]['id'];
			$batches = Batches::getAllBatchesDatabySeasonId($franchiseeId,$s_id);
                        for($i=0;$i<count($batches);$i++){
                          $batches[$i]['preferred_time']=date("H:i",strtotime($batches[$i]['preferred_time']));
                          $batches[$i]['preferred_end_time']=date("H:i",strtotime($batches[$i]['preferred_end_time']));
                          
                          $location_data=  Location::where('id','=',$batches[$i]['location_id'])->get();
                          $batches[$i]['location_name']=$location_data[0]['location_name'];
                          $batches[$i]['created']=date("Y-m-d",strtotime($batches[$i]['created_at']));
                          $batches[$i]['day']=date('l',strtotime($batches[$i]['start_date']));
                          if($batches[$i]['lead_instructor']!=''){
                          $user_data=User::find($batches[$i]['lead_instructor']);
                          $batches[$i]['instructor_name']=$user_data['first_name'].''.$user_data['last_name'];
                          }else{
                              $batches[$i]['instructor_name']='';
                          }
                          $batches[$i]['count']=  StudentClasses::where('batch_id','=',$batches[$i]['id'])
                                                                ->count();
                        }
                        }
			$courseList = Courses::getFranchiseCoursesList($franchiseeId);
			$franchiseeCourses = Courses::getFranchiseCoursesList(Session::get('franchiseId'));
			$Instructors = User::getInstructors();
			            $batches_limit=  BatchLimit::where('franchisee_id','=',session::get('franchiseId'))->get();
			
			$dataToView = array('batches','courseList','currentPage', 'mainMenu','franchiseeCourses','mondays','Instructors','batches_limit');
			
			return View::make('pages.batches.batchesfinal', compact($dataToView));
			
		
		}else{
			return Redirect::action('VaultController@logout');
		}
	}

	
	
	
	public function view($id){
		
		if(Auth::check()){
			
			$currentPage  =  "BATCHES";
			$mainMenu     =  "COURSES_MAIN";
			$batchSchedules = BatchSchedule::getBatcheSchedulesbyCourseandClassID($id);
			$batchSchedules = json_encode($batchSchedules['event']);
                        
			$dataToView = array('currentPage', 'mainMenu', 'batchSchedules');
			return View::make('pages.batches.batchview', compact($dataToView));
		
		}else{
			return Redirect::action('VaultController@logout');
		}		
	}
	
        
        public function getBatchData(){
            $inputs=  Input::all();
            $present_date = carbon::now();
            $franchisee_id=Session::get('franchiseId');
            $batch_data=  Batches::getAllBatchesbySeasonId($franchisee_id,$inputs['session_id']);
           
            for($i=0;$i<count($batch_data);$i++){
				$batch_data[$i]['preferred_time']=date("h:i",  strtotime($batch_data[$i]['preferred_time']));
				$batch_data[$i]['preferred_end_time']=date("h:i",  strtotime($batch_data[$i]['preferred_end_time']));
				$batch_limit = BatchLimit::where('franchisee_id','=', $franchisee_id)->orderby('batch_limit_admin','DESC')->get();
				if(Session::get('userType') == 'ADMIN'){
					$batch_data[$i]['batch_limit'] = $batch_limit[0]['batch_limit_admin'];
				}else{
					$batch_data[$i]['batch_limit'] = $batch_limit[0]['batch_limit_receptionist'];
				}
				$location_data=  Location::where('id','=',$batch_data[$i]['location_id'])->get();
				$batch_data[$i]['location_name']=$location_data[0]['location_name'];
				$batch_data[$i]['created']=date("Y-m-d",strtotime($batch_data[$i]['created_at']));
				$batch_data[$i]['day']=date('l',strtotime($batch_data[$i]['start_date']));
				if($batch_data[$i]['lead_instructor']!=''){
				$user_data=User::find($batch_data[$i]['lead_instructor']);
				$batch_data[$i]['instructor_name']=$user_data['first_name'].''.$user_data['last_name'];
				}else{
				  $batch_data[$i]['instructor_name']='';
				}
				$batch_data[$i]['count'] = StudentClasses::with('Students')
								->where('batch_id','=',$batch_data[$i]['id'])
                                ->whereIn('status',array('enrolled','makeup','introvisit','transferred_class'))
								->whereDate('enrollment_start_date', '<=', $present_date)
								->whereDate('enrollment_end_date', '>=', $present_date)
								->distinct('student_id')
								->count();
            }       
            if($batch_data){
            	return Response::json(array('status'=>'success','data'=>$batch_data));
            }else{
                return Response::json(array('status','failure'));
            }
        }
	
	public function getBatchesSchedules(){
		
		$inputs    = Input::all();
                $batch_data=  Batches::find($inputs['batchId']);
                $eachClassCost=$batch_data->class_amount;
		$batchId   = $inputs['batchId'];
		$startDate = date('Y-m-d', strtotime($inputs['enrollmentStartDate']));
		$endDate   = date('Y-m-d', strtotime($inputs['enrollmentEndDate']));
                $seasonId  =  $inputs['seasonId'];
                /*
                if(Holidays::where('season_id','=',$seasonId)->count()){
                    $holiday_data = Holidays::getHolidayDatabySeasonId($inputs['seasonId']);
                    $holiday_start_date=new carbon();
                    $holiday_end_date=new carbon();
                    $h_data=0;
                
                   for($i=0;$i<count($holiday_data);$i++){
                    $holiday_start_date=$holiday_start_date->createFromFormat('Y-m-d',$holiday_data[$i]['startdate']);
                    $holiday_end_date=$holiday_end_date->createFromFormat('Y-m-d',$holiday_data[$i]['enddate']);
                    
                         if(($startDate<=$holiday_start_date) &&($endDate>=$holiday_end_date)){
                                $holiday_sessionno=($holiday_end_date->diffInHours($holiday_start_date));
                                $holiday_sessionno=((floor($holiday_sessionno/24)+1)/7);
                                $h_data+= $holiday_sessionno;
                        }
                    }
                    
                    $countofSessions = BatchSchedule::getScheduleCountForBatch($batchId, $startDate, $endDate);
                    $countofSessions = $countofSessions-$h_data;
                    $amountTotal     = ($countofSessions*500);
                    */
                
                  $countofSessions=  BatchSchedule::where('batch_id','=',$batchId)
                                     //               ->whereRaw('holiday = 0 ')
                                                    ->whereBetween('schedule_date',array($startDate,$endDate))
                                                    ->count();
                  $countholidaysessions=BatchSchedule::where('batch_id','=',$batchId)
                                                      ->whereBetween('schedule_date',array($startDate,$endDate))
                                                      ->where('holiday','=',1)
                                                      ->count();
                 $countofSession=$countofSessions -$countholidaysessions;
                 
                 $amountTotal     = ($countofSession*$eachClassCost);
		
		
		if($countofSessions){
				
			return Response::json(array("status"=>"success",
					"availableSession"=>$countofSession,
					"amountTotal"=>$amountTotal,
					"eachClassAmount"=>$eachClassCost,	
                                            ));
		}else{
			return Response::json(array("status"=>"failed"));
		}
	}
	
        
        
	public function checkslots(){
		
		$inputs = Input::all();
		$datetime     = $inputs['datetime'];
		$date = date('Y-m-d H:i:s', strtotime($datetime));
		$dateSplit = explode(" ", $date);
		
		$splitDate = $dateSplit['0'];
		$splitTime = $dateSplit['1'];
		
		$batchSchedule = BatchSchedule::where("schedule_date", "=", $splitDate)
		->where("start_time", "=",$splitTime)
		->count();
		//->get();
		
		if(!$batchSchedule){
			return Response::json(array("status"=>"success"));
		}
		return Response::json(array("status"=>"failed"));
	}
	
	
	public function attendance($id){
		
		if(Auth::check()){
				
			$currentPage  =  "BATCHES";
			$mainMenu     =  "COURSES_MAIN";
			$studentsInBatch = StudentClasses::with('Students')->where('batch_id', '=', $id)->count();
			$batch  =  Batches::where('id', '=', $id)->first();
                        
			$attendanceArray = BatchSchedule::getAttendanceTable($id);
			
			$lead = Batches::with('LeadInstructors')->find($id);
			$alternate = Batches::with('AlternateInstructors')->find($id);
			
			if(isset($lead->LeadInstructors)){
				$leadInstructor      = $lead->LeadInstructors->first_name.' '.$lead->LeadInstructors->last_name;
			}else{
				
				$leadInstructor      = "";
			}
			
			if(isset($alternate->AlternateInstructors)){
				$alternateInstructor = $alternate->AlternateInstructors->first_name.' '.$alternate->AlternateInstructors->last_name;
			}else{
				$alternateInstructor = "";
			}
			
			$dataToView = array('currentPage', 'mainMenu', 'attendanceArray', 'batch', 'studentsInBatch','leadInstructor','alternateInstructor');
			return View::make('pages.batches.attendance', compact($dataToView));
		
		}else{
			return Redirect::action('VaultController@logout');
		}
		
	}
	
	
	
        public function checkBatchExistBySeasonIdLocationId(){
            $inputs=Input::all();
            $startDate=new carbon();
            $season_data=Seasons::where('id','=',$inputs['season_id'])->get();
            $season_data=$season_data[0];
            $startDate =$startDate->createFromFormat('Y-m-d', $season_data['start_date']);
              switch ($inputs['day']){
                                    case 0:  break;
                                    case 1: $startDate->addDays(1); break;
                                    case 2: $startDate->addDays(2); break;
                                    case 3: $startDate->addDays(3); break;
                                    case 4: $startDate->addDays(4); break;
                                    case 5: $startDate->addDays(5); break;
                                    case 6: $startDate->addDays(6); break;
                                    
                                }
            //converting to string to time (24hr format)
            $timeString = $inputs['startTime'];
	    $timestamp =  strtotime($timeString);
	    $startTime24Hours = date('H:i:s',$timestamp);
	    $timeString = $inputs['endTime'];
	    $timestamp =  strtotime($timeString);
            $endTime24Hours = date('H:i:s',$timestamp);
                                
           
                              
            $batch_data=Batches::where('season_id','=',$inputs['season_id'])
                               ->where('location_id','=',$inputs['location_id'])
                                ->where('start_date','=',$startDate->toDateString())
                                 //->where('preferred_time','=',$startTime24Hours)
                                 //->where('preferred_end_time','=',$endTime24Hours)
                    
                                 // whereRaw('"preferred_time" >="'.$startTime24Hours.'"')
                                //   whereRaw("'preferred_end_time' >= '".$endTime24Hours."'")
                                  ->select('preferred_time','preferred_end_time')
                                  ->get();
            
            
            /*$batch_data2=Batches::where('season_id','=',$inputs['season_id'])
                                ->where('location_id','=',$inputs['location_id'])
                                ->where('start_date','=',$startDate->toDateString())
                                //->whereRaw('"preferred_time" >"'.$startTime24Hours.'"')
                    //            ->whereRaw('"preferred_end_time" < "'.$endTime24Hours.'"')
                                ->whereBetween('preferred_end_time',array($startTime24Hours,$endTime24Hours))   
                                ->orWhereBetween('preferred_time',array($startTime24Hours,$endTime24Hours))
                                ->get();
             */           
            $flag='notexist';
            $input_start_time=new carbon();
            $input_end_time=new carbon();
            $input_start_time=$input_start_time->createFromFormat('H:i:s',$startTime24Hours);
            $input_end_time=$input_end_time->createFromFormat('H:i:s',$endTime24Hours);
            if($input_start_time->lt($input_end_time)){
              for($i=0;$i<count($batch_data);$i++){
                $batch_start_time=new carbon();
                $batch_start_time=$batch_start_time->createFromFormat('H:i:s',$batch_data[$i]['preferred_time']);
                $batch_end_time=$batch_start_time->createFromFormat('H:i:s',$batch_data[$i]['preferred_end_time']);
                if(($input_start_time->eq($batch_start_time))&&($input_end_time->eq($batch_end_time))){
                    $flag='exist';
                }
                if(($input_start_time->lt($batch_start_time))&&($input_end_time->gt($batch_start_time))){
                    $flag='exist';
                }
                if(($input_start_time->eq($batch_start_time))&&($input_end_time->lt($batch_end_time))){
                    $flag='exist';
                }
                if(($input_start_time->gt($batch_start_time))&&($input_end_time->lt($batch_end_time))){
                    $flag='exist';
                }
                if(($input_start_time->eq($batch_start_time))&&($input_end_time->gt($batch_end_time))){
                    $flag='exist';
                }
                if(($input_start_time->gt($batch_start_time))&&($input_start_time->lt($batch_end_time))){
                    $flag='exist';
                }
                
              }
            }else{
                $flag='invalid selection';
            }
            return Response::json(array("status"=>'success',"batch_status"=>$flag));
        }
        
        

        
        public function getBatchRemainingClassesByBatchId(){
            $inputs = Input::all();
            $result = DB::select(DB::raw("select * from student_classes where student_id = '$inputs[studentId]' and batch_id = '$inputs[batchId]' and class_id = '$inputs[classId]' and '$inputs[preferredStartDate]' <= enrollment_end_date and '$inputs[preferredStartDate]'>= enrollment_start_date"));
           
            if($result){
           		return Response::json(array('status'=>'available'));
        	}
            $batchClassesData = BatchSchedule::where('franchisee_id','=',Session::get('franchiseId'))
                                              ->where('batch_id','=',$inputs['batchId'])
                                              ->whereDate('schedule_date','>=',$inputs['preferredStartDate'])
                                              ->get();

            $batchClassesCount = count($batchClassesData);
            $lastEndDate = '';

            if(isset($batchClassesCount) && !empty($batchClassesCount)){
            	$sCount = $inputs['selectedNoOfClass'] - $inputs['removalbleClasses'];
            	if ($inputs['removalbleClasses'] != 0) {
	            	if ($sCount <= $batchClassesCount) {
	            		$lastEndDate = $batchClassesData[$sCount-1]['schedule_date'];
	            	}else{
	            		$lastEndDate = $batchClassesData[$batchClassesCount-1]['schedule_date'];
	            	}
	            }else{
	            	if ($sCount <= $batchClassesCount) {
	            		$lastEndDate = $batchClassesData[$sCount-1]['schedule_date'];	
	            	}else{
	            		$batchClassesCount = $batchClassesCount - $inputs['removalbleClasses'];
	            		$lastEndDate = $batchClassesData[($batchClassesCount-1)]['schedule_date'];
	            	}
	            }
	            $date = Carbon::now();
	            $date = $date->createFromFormat('Y-m-d', $lastEndDate);
	            $date = $date->next(Carbon::MONDAY);
	            $base_price_no = Batches::find($inputs['batchId'])
	                                    ->classes()
	                                    ->select('base_price_no')
	                                    ->get();
	            $base_price = ClassBasePrice::where('base_price_no', '=', $base_price_no[0]['base_price_no'])->where('franchise_id', '=', Session::get('franchiseId'))
	                                ->get();
	            $base_price = $base_price[0]['base_price'];
            }
            if($batchClassesCount){
                return Response::json(array('status'=>'success',
                							'classCount'=>$batchClassesCount,
                							'lastdate'=>$date->toDateString(),
                							'classAmount'=>$base_price,
                							'enrollment_end_date'=>$lastEndDate,
                							'enrollment_start_date'=>$batchClassesData[0]['schedule_date'],
                							'batch_Schedule_data'=>$batchClassesData));
            }else{
                return Response::json(array('status'=>'failure'));
            }
        }
        
        
        
        
        public function getBatchDetailsById(){
            $inputs = Input::all();
            $batch_data = Batches::getBatchDetailsById($inputs['batch_id']);
            $instructor_data = User::getTeachersByFranchiseeId($batch_data['franchisee_id']);
            $location_data = Location::getLocationBySeasonId($batch_data['season_id']);
            $startDate = new DateTime($batch_data['preferred_time']);
            $endDate = new DateTime($batch_data['preferred_end_time']);
            $batch_data['preferred_time'] = $startDate->format('G:i A');
            $batch_data['preferred_end_time'] = $endDate->format('G:i A');
            if($batch_data){
                return Response::json(array('status'=>'success','batchData'=>$batch_data,'instructorData'=>$instructor_data,'locationData'=>$location_data));
            }else{
                return Response::json(array('status'=>'failure'));
            }
        }
        
        
        
        public function editbatchByBatchId(){
            $inputs=Input::all();
            $timeString = '2000-3-5 '.$inputs['batchEndTime'];
            $timestamp =  strtotime($timeString);
            $endTime24Hours = date('H:i:s',$timestamp);
            $timeString = '2000-3-5 '.$inputs['batchStartTime'];
            $timestamp =  strtotime($timeString);
            $startTime24Hours = date('H:i:s',$timestamp);
            $data=Batches::where('id','=',$inputs['batch_id'])
                    ->update(array('lead_instructor'=>$inputs['l_instructor_id'],'location_id'=>$inputs['location_id'],
                                   'preferred_time'=>$startTime24Hours,
                                   'preferred_end_time'=>$endTime24Hours));
            return Response::json(array('status'=>'success','data'=>$data));
        }
        public function deleteBatchById(){
            $inputs['batch_id']=Input::get('batch_id');
            // deleting from batch_schedule table 
            $batchSchedule_delete=  BatchSchedule::deleteBatchScheduleById($inputs['batch_id']);
            if($batchSchedule_delete){
                // deleting from batch table
                $batch_delete=  Batches::deleteBatchById($inputs['batch_id']);
                if($batch_delete){
                    return Response::json(array('status'=>'success'));
                }
                else{
                    return Response::json(array('status'=>'failure'));
                }
            }
            
            
            return Response::json(array('status'=>'failure'));
        }
        
        
        public function CheckNoofStudentsinBatch(){
            $inputs=  Input::all();
             //$batch_details=  Batches::where('')
                     
             $StudentsBatchesDetials = StudentClasses::  where('batch_id','=',$inputs['batch_id'])
                                             ->where('season_id','=',$inputs['season_id'])
                                             ->where('status','=','enrolled')
                                             ->where('enrollment_end_date','>',$inputs['start_date'])
                                             ->count();
             $user_details=User::find(Session::get('userId'));
             
             
        }

//        public function batcheslimit(){
//            if(Auth::check()){
//                if(Session::get('userType') == 'ADMIN'){
//                    $currentPage  =  "BATCHESLIMIT";
//                    $mainMenu     =  "COURSES_MAIN";
//                    $viewData= compact('currentPage','mainMenu');
//                    return View::make('pages.batches.batcheslimit',$viewData);
//                }else{
//                    return Redirect::action('DashboardController@index');
//                }
//            }    
//        }

        /** added following methods **/

       public function batcheslimit(){
            if(Auth::check()){
                if(Session::get('userType') == 'ADMIN'){
                    $currentPage  =  "BATCHESLIMIT";
                    $mainMenu     =  "COURSES_MAIN";
                    $batchLimit   =  BatchLimit::getAllBatchesLimitbyFranchiseId();
                    $viewData= compact('currentPage','mainMenu','batchLimit');
                    return View::make('pages.batches.batcheslimit',$viewData);
                }else{
                    return Redirect::action('VaultController@logout');
                }
            }    
        }
        
        /**
         * This method adds batch limit to batches_limit table
         * 
         */
        
        public function addBatchLimit(){
            if(Auth::check()){
                if(Session::get('userType') == 'ADMIN'){
                    $currentPage  =  "BATCHESLIMIT";
                    $mainMenu     =  "COURSES_MAIN";
                    
                    $inputs = Input::all();
                    $addBatchLimit = BatchLimit::addBatchLimit($inputs);
                    if ($addBatchLimit) {
                       Session::flash('msg', "Batch Limit added successfully.");
                    } else {
                       Session::flash('warning', "Sorry, Could not add batch limit at the moment.");
                    }
                    return Redirect::action('BatchesController@batcheslimit');
                }else{
                    return Redirect::action('VaultController@logout');
                }
            }    
        }
        
        public function deleteBatchLimitById(){
            $inputs['batchlimit_id']=Input::get('batchlimit_id');
            
            $batchLimit_delete=  BatchLimit::deleteBatchLimitById($inputs['batchlimit_id']);
            
            if ($batchLimit_delete) {
                return Response::json(array('status' => 'success'));
            } else {
                return Response::json(array('status' => 'failure'));
            }

        }
        
        public function editBatchLimitByBatchId(){
            $inputs=Input::all();
            $data=BatchLimit::where('id','=',$inputs['batchlimit_id'])
                    ->update(['batch_limit_receptionist'=>$inputs['batchlimit_recep'],'batch_limit_admin'=>$inputs['batchlimit_admin']]);
            return Response::json(array('status'=>'success','data'=>$data));
        }


        public function getBatchesForOldCustomer(){
           $inputs = Input::all();
           //return Response::json(array('status' => 'success', 'batch_data'=> $inputs));
           $batchData = Batches::where('season_id','=', $inputs['seasonId'])
           	->where('class_id', '=', $inputs['classId'])
           	->where('franchisee_id', '=', Session::get('franchiseId'))->get();
           for($i=0;$i<count($batchData);$i++){
               $timestamp = strtotime($batchData[$i]['start_date']);
               $batchData[$i]['day']=date('l', $timestamp);
               $startDate=new DateTime($batchData[$i]['preferred_time']);
               $endDate=new DateTime($batchData[$i]['preferred_end_time']);
               $batchData[$i]['preferred_time']=$startDate->format('G:i A');
               $batchData[$i]['preferred_end_time']=$endDate->format('G:i A');
               if(isset($batchData[$i]['lead_instructor']) && $batchData[$i]['lead_instructor']!='' && $batchData[$i]['lead_instructor']!=null){
               		$t1=User::find($batchData[$i]['lead_instructor']);
               		$batchData[$i]['Leadinstructor']=$t1->first_name.$t1->last_name;
               }
               
              $batch_count=  StudentClasses::where('batch_id','=',$batchData[$i]['batch_id'])                                              
              ->whereIn('status',array('enrolled','makeup'))->count();
           }
           if ($batchData) {
               return Response::json(array('status' => 'success', 'batch_data'=> $batchData));
           } else {
               return Response::json(array('status' => 'failure'));
           }

        }
        
        public function getbatchesbybatchidanddate(){
            $inputs=Input::all();
            $class_count=  BatchSchedule::whereDate('schedule_date','>=',$inputs['startdate'])
                                  ->whereDate('schedule_date','<=',$inputs['enddate'])
                                  ->where('batch_id','=',$inputs['batch_id'])
                                  ->count();
            
            return Response::json(array('status'=>'success','class_count'=>$class_count));
        }
        
        public  function getBatchDatesByBatchId(){
            if(Auth::check()){
                $inputs=Input::all();
                $data=BatchSchedule::getBatchDatesByBatchId($inputs);
                if($data){
                return Response::json(array('status'=>'success','dates'=>$data));
                }else{
                return Response::json(array('status'=>'failure'));
                }
            }
        }
        
        public function getBatchRemainingClassesCountByBatchId(){
           if(Auth::check()){
                $inputs=Input::all();
                //return  Response::json(array($inputs));
                $class_data=BatchSchedule::where('batch_id','=',$inputs['batch_id'])
                                           ->whereDate('schedule_date','>=',$inputs['start_date'])
                                           ->where('holiday','<>','1')
                                           ->count();
                return Response::json(array('status'=>'success','class_count'=>$class_data));
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
