<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::check() && Session::get('userType')!='SUPER_ADMIN'){
      

			$currentPage  =  "";
			$mainMenu     =  "DASHBOARD";
			  

                        //Enrolled Kid's
                        $singleEnrollments = StudentClasses::getSingleEnrolledList();
                        $multipleEnrollments = StudentClasses::getMultipleEnrolledList();
			$enrolledCustomers = StudentClasses::getEnrolledCustomers();


                        //customers or Inquiries
                        $todaysCustomerReg= Customers::getCustomertodaysRegCount();
                        $customerCount = Customers::getCustomerCount();
                        
                        //Members or Family Members
                        $todaysMemberReg= CustomerMembership::getMembertodaysRegCount();
                        $membersCount= CustomerMembership::getMemberCount();
                        
                        //Non Members or prospects
                        $todaysNonmemberReg= CustomerMembership::getNonMembertodaysRegCount();
                        $NonmembersCount= CustomerMembership::getNonMemberCount();
                        foreach($todaysNonmemberReg as $emp){
                           $todaysNonmemberReg = $emp->total;
                        }
                        foreach($NonmembersCount as $emp){
                           $NonmembersCount = $emp->total;

                        }
                        $weeekdate= new carbon();
                        $presentdate= Carbon::now();
                        //return $presentdate;
                        $time = strtotime($presentdate);
                        $end = strtotime('last sunday, 11:59pm', $time);
                        //Enrolled Information
                        $todayEnrolledList = StudentClasses::getTodayEnrollment();
                        $thisMonthEnrollment = StudentClasses::getThisMonthEnrollment();
                        $thisWeekEnrollment = StudentClasses::getThisWeekEnrollment();
                       
                        //Enrolled customers(kids)
                        $todaysEnrolledCustomers=StudentClasses::getTodaysEnrolledCustomers();
			                  $enrolledCustomers = StudentClasses::getEnrolledCustomers();
			     
                        //Revenue Details
                        $todayRevenueDetails = PaymentDues::whereDate('created_at','=',date('Y-m-d', $time))
                                            ->where('franchisee_id','=',Session::get('franchiseId'))
                                            ->sum('payment_due_amount_after_discount');
                       

                        $thisWeekRevenueDetails = PaymentDues::whereDate('created_at','<=',date('Y-m-d'))
                                            ->where('franchisee_id','=',Session::get('franchiseId'))
                                            ->whereDate('created_at','>=',date('Y-m-d', $end))
                                            ->sum('payment_due_amount_after_discount');


                        $thisMonthRevenueDetails = PaymentDues::where('franchisee_id','=',Session::get('franchiseId'))
                                            ->whereRaw('MONTH(created_at) = MONTH(NOW())')
                                            ->whereRaw('YEAR(created_at) = YEAR(NOW())')
                                            ->sum('payment_due_amount_after_discount');

                        $reminderCount = Comments::getReminderCountByFranchiseeId();

                        //get Leads Information
                        $openLeads = Customers::getOpenLeads();
                        $hotLeads = Customers::getHotLeads();
                        //return $openLeads;
                        
			                   //Introvisit
                        $totalIntrovisitCount = IntroVisit::getIntrovistCount();
			                  $introVisitCount = IntroVisit::getIntrovisitBytoday();
			                  $allIntrovisits  = IntroVisit::getAllActiveIntrovisit();
                        $thisMonthIvScheduled = IntroVisit::getThisMonthIv();
                        $thisMonthAttendedIvs = IntroVisit::getThisMonthAttendedIv();
                        $todayScheduledIvs = Introvisit::getTodayScheduledIvs();
                        $todayAttendedIvs = IntroVisit::getTodayAttendedIvs();
                        $thisWeekScheduledIvs = IntroVisit::getThisWeekScheduledIv();
                        $thisWeekAttendedIvs = IntroVisit::getThisWeekAttendedIvs();
                        
                        
                        for($i=0;$i<count($allIntrovisits);$i++){
                            $data=  Comments::where('introvisit_id','=',$allIntrovisits[$i]['id'])
                                                ->orderBy('id','DESC')
                                                ->first();
                            if(isset($data)){
                            $allIntrovisits[$i]['followup_status']=$data['followup_status'];
                            }
                        } 
                         
                         
			
			                  //for courses
                        $courses=Courses::where('franchisee_id','=',Session::get('franchiseId'))->select('course_name','id')->get();
                        $present_date=Carbon::now();
                        $totalclasses=0;
                        foreach($courses as $course){
                          $temp= DB::select(DB::raw("SELECT student_id,".$course->id." FROM payments_dues 
                                                     WHERE franchisee_id = '".Session::get('franchiseId')."' AND end_order_date >= '".date('Y-m-d')."' AND class_id IN (select id from classes where course_id =".$course->id.") AND payments_dues.payment_due_for IN ('enrollment') GROUP BY student_id ORDER BY count('student_id')"));
                 //        	var_dump($temp); die;
			 if($temp){
                              $course->totalno=count($temp);
                              $totalclasses+=count($temp);
                            }else{
                              $course->totalno=0;
                              $totalclasses+=0;
                            } 
                        
			}	
			
           	/*		$classes = Courses::join('classes','classes.course_id','=','courses.id')
					 ->join('payments_dues','payments_dues.class_id','=','classes.id')
					 ->where('courses.franchisee_id','=',Session::get('franchiseId'))
					 ->where('classes.franchisee_id','=',Session::get('franchiseId'))
					 ->where('payments_dues.franchisee_id','=',Session::get('franchiseId'))
					 ->whereDate('payments_dues.end_order_date', '>=', date('Y-m-d'))
					 ->where('payments_dues.payment_due_for','=','enrollment')
					 ->select('payments_dues.student_id','classes.course_id')
					 ->get();
			//	return count($classes); 
			$student = array();
			$final_array = array();
			foreach($classes as $k => $class){	
				$key  = $class['course_id'];
				if (!array_key_exists($key, $final_array)) {
					array_push($student, $class['student_id']);
				} else {
					array_push($student, $class['student_id']);
				}
				$final_array[$key] = $student;
				
			 }
			return $final_array;
			$final_dict = array();
			$single = 0 ;
			$multiple = 0;
			foreach($final_array as $k => $v){
				$course_id = Courses::where('franchisee_id','=',Session::get('franchiseId'))
						    ->where('id','=',$k)
						    ->get();
				$value = array_count_values($v);
				foreach($value as $id => $count) {
				        if($count > 1){
						$multiple = $multiple + 1;
						$final_dict[$course_id[0]['course_name']]['multiple'] = $multiple;
					}else{
						$single = $single + 1;
						$final_dict[$course_id[0]['course_name']]['single'] = $single;
					}				
				}
			}    

			return $final_dict;  */
	
                        $totalbpartyCount = BirthdayParties::getBpartyCount();
                        $todaysbpartycount = BirthdayParties::getBpartyCountBytoday();
                        $bdayPartyInThisWeek = BirthdayParties::whereDate('birthday_party_date','<=',date('Y-m-d', $time))
                                            ->where('franchisee_id','=',Session::get('franchiseId'))
                                            ->whereDate('birthday_party_date','>=',date('Y-m-d', $end))
                                            ->count();

                        $bdayPartyInThisMonth  = BirthdayParties::whereRaw('MONTH(birthday_party_date) = MONTH(NOW())')
                                            ->whereRaw('YEAR(birthday_party_date) = YEAR(NOW())')
                                            ->where('franchisee_id','=',Session::get('franchiseId'))
                                            ->orderBy('birthday_party_date','ASC')
                                            ->count();
                                
			                  $todaysFollowup = Comments::getAllFollowup();
                        $futurefollowups= Comments::getFutureFollowup();
                        
			                  $todaysIntrovisit = BatchSchedule::getTodaysIntroVisits();
			
			                  $activeRemindersCount = Comments::getAllFollowupActive();



                        //get birthday dates
                        $startdate=new carbon();
                        $startdate->startOfYear();
                        $endofyear=new carbon();
                        $endofyear=$endofyear->endOfYear();
                        $student_id=array();
                        $birthday_celebration_data=BirthdayParties::where('created_at','>=',$startdate->toDateString())
                                                                    ->where('created_at','<=',$endofyear->toDateString())
                                                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                                                    ->select('student_id')
                                                                    ->get();
                        
                        for($i=0;$i<count($birthday_celebration_data);$i++){
                           $student_id[$i]=$birthday_celebration_data[$i]['student_id'];
                        }
                        $dat=new carbon();
                        $month=$dat->month;
                        $presentdate=$dat->day;
                        
                        // for rest of the days of month
                        $birthday_data= Students::whereNotIn('id',$student_id)
                                                  ->where('student_date_of_birth','<>','')
                                                  ->where( DB::raw('MONTH(student_date_of_birth)'), '=', $month)
                                                  ->where('franchisee_id','=',Session::get('franchiseId'))
                                                  ->orderBy(DB::raw('DAY(student_date_of_birth)'))
                                                  -> get();
                        
                        for($i=0;$i<count($birthday_data);$i++){
                            $customer_data= Customers::where('id','=',$birthday_data[$i]['customer_id'])->get();
                            $birthday_data[$i]['customer_name']=  $customer_data[0]['customer_name'];
                            $birthday_data[$i]['mobile_no']=  $customer_data[0]['mobile_no'];
                            $birthday_data[$i]['membership']=  CustomerMembership::where('customer_id','=',$birthday_data[$i]['customer_id'])->count();
                            
                        }
                        
                         $m=$month;
                         $m++;
                        while($m<=12){
                            $birthday_data_month[]=
                                                  Students::whereNotIn('id',$student_id)
                                                  ->where('student_date_of_birth','<>','')
                                                  ->where( DB::raw('MONTH(student_date_of_birth)'), '=', $m )
                                                  ->where('franchisee_id','=',Session::get('franchiseId'))
                                                  ->orderBy(DB::raw('DAY(student_date_of_birth)'))
                                                  -> get();
                            $m++;
                        
                        }
                        // for starting months
                      $m=1;
                        while($m<$month){
                          
                            $birthday_data_month[]=
                                                  Students::whereNotIn('id',$student_id)
                                                 //  where('student_date_of_birth','>',$startdate->toDateString())
                                                  ->where('student_date_of_birth','<>','')
                                                  ->where( DB::raw('MONTH(student_date_of_birth)'), '=', $m )
                                                //  ->where( DB::raw('DATE(student_date_of_birth)'), '>', $presentdate )
                                                  ->where('franchisee_id','=',Session::get('franchiseId'))
                                                  //->where('student_date_of_birth','=','0000-'.$month.'-00') 
                                                  ->orderBy(DB::raw('DAY(student_date_of_birth)'))
                                                  -> get();
                          
                            
                            $m++;
                        }
                        
                        
                        for($i=0;$i<count($birthday_data_month);$i++){
                            for($j=0;$j<count($birthday_data_month[$i]);$j++){
                            $customer_data= Customers::where('id','=',$birthday_data_month[$i][$j]['customer_id'])->get();
                            $birthday_data_month[$i][$j]['customer_name']=  $customer_data[0]['customer_name'];
                            $birthday_data_month[$i][$j]['mobile_no']=  $customer_data[0]['mobile_no'];
                            $birthday_data_month[$i][$j]['membership']=  CustomerMembership::where('customer_id','=',$birthday_data_month[$i][$j]['customer_id'])->count();
                            }
                        }
                        
                        
                        $birthday_month_startdays= Students::whereNotIn('id',$student_id)
                                                 //  where('student_date_of_birth','>',$startdate->toDateString())
                                                  ->where('student_date_of_birth','<>','')
                                                  ->where( DB::raw('MONTH(student_date_of_birth)'), '=', $month )
                                                  ->where( DB::raw('DAY(student_date_of_birth)'), '<', $presentdate )
                                                  ->where('franchisee_id','=',Session::get('franchiseId'))
                                                  //->where('student_date_of_birth','=','0000-'.$month.'-00') 
                                                  ->orderBy(DB::raw('DAY(student_date_of_birth)'))
                                                  -> get();
                        
                        
                        for($i=0;$i<count($birthday_month_startdays);$i++){
                            $customer_data= Customers::where('id','=',$birthday_month_startdays[$i]['customer_id'])->get();
                            $birthday_month_startdays[$i]['customer_name']=  $customer_data[0]['customer_name'];
                            $birthday_month_startdays[$i]['mobile_no']=  $customer_data[0]['mobile_no'];   
                            $birthday_month_startdays[$i]['membership']=  CustomerMembership::where('customer_id','=',$birthday_month_startdays[$i]['customer_id'])->count();
                            
                        }
                        
                        //for birthday celebration this week
                        
                        $presentdate = Carbon::now();
                        $weeekdate = new carbon();
                        $time = strtotime($presentdate);
                        $end = strtotime('next sunday, 11:59pm', $time);
                       
                        $birthdayPresentWeek = BirthdayParties::whereDate('birthday_party_date','>=',date('Y-m-d', $time))
                                ->whereDate('birthday_party_date','<=',date('Y-m-d', $end))
                                ->where('franchisee_id','=',Session::get('franchiseId'))
                                ->orderBy('birthday_party_date','ASC')
                                ->get();

                        for($i=0;$i<count($birthdayPresentWeek);$i++){
                          $customer_data = Customers::where('id','=',$birthdayPresentWeek[$i]['customer_id'])->distinct()->get();
                          $birthdayPresentWeek[$i]['customer_name'] = $customer_data[0]['customer_name'];
                          $birthdayPresentWeek[$i]['mobile_no'] = $customer_data[0]['mobile_no'];
                          $birthdayPresentWeek[$i]['franchisee_id'] = $customer_data[0]['franchisee_id'];
                          $student_data = Students::where('id','=',$birthdayPresentWeek[$i]['student_id'])->distinct()->get();
                          $birthdayPresentWeek[$i]['student_name'] = $student_data[0]['student_name'];
                        }
                        $expiringbatch= Batches::getExpiringBatchData();
          


			$viewData = array('currentPage', 'mainMenu',
                                                           'birthday_data','birthday_data_month','birthday_month_startdays','birthdayPresentWeek',
                                                           'todaysMemberReg','membersCount',
                                                           'todaysNonmemberReg','NonmembersCount',
                                                            'customerCount', "reminderCount", 
                                                            'totalbpartyCount','todaysbpartycount',
                                                           'courses','futurefollowups',
							  'todaysCustomerReg','todaysEnrolledCustomers','enrolledCustomers','totalIntrovisitCount', 'introVisitCount', 'allIntrovisits', 'todaysFollowup', 
							  'todaysIntrovisit','activeRemindersCount','totalclasses', 'expiringbatch', 'bdayPartyInThisWeek', 'bdayPartyInThisMonth', 'todayEnrolledList', 'thisMonthEnrollment', 'thisWeekEnrollment', 'todayRevenueDetails', 'thisWeekRevenueDetails', 'thisMonthRevenueDetails', 'openLeads', 'hotLeads', 'singleEnrollments', 'multipleEnrollments', 'thisMonthIvScheduled', 'thisMonthAttendedIvs', 'todayScheduledIvs','thisWeekScheduledIvs','todayAttendedIvs','thisWeekAttendedIvs');
   
			return View::make('pages.dashboard.upcoming',compact($viewData));
     
		}elseif(Auth::check() && Session::get('userType')=='SUPER_ADMIN'){
      
      $currentPage  =  "";
      $mainMenu     =  "DASHBOARD";

      $viewData = array('currentPage','mainMenu');
      return View::make('pages.dashboard.admindashboard',compact($viewData)); 
      
    }else{

			return Redirect::action('VaultController@logout');
		
    }
	}


  public function BdayPartiesFiltering(){
      $inputs = Input::all();

      if($inputs['value'] == "Week"){
          $presentdate= Carbon::now();
          $weeekdate= new carbon();
          $time = strtotime($presentdate);
          $end = strtotime('next sunday, 11:59pm', $time);
          //print_r(date('Y-m-d', strtotime('+1', $end))); die;
          //$weekdatemon= $presentdate->endOfWeek();
          //$weeekdate->addDays(7);
          //return date('Y-m-d', $end);
         
          $birthdayCelebrationsData=BirthdayParties::whereDate('birthday_party_date','>=',date('Y-m-d', $time))
                              
                              ->where('franchisee_id','=',Session::get('franchiseId'))
                              ->whereDate('birthday_party_date','<=',date('Y-m-d', $end))
                              ->orderBy('birthday_party_date','ASC')
                              ->get();
                        
                       

                        for($i=0;$i<count($birthdayCelebrationsData);$i++){
                          $customer_data=Customers::where('id','=',$birthdayCelebrationsData[$i]['customer_id'])->distinct()->get();
                          $birthdayCelebrationsData[$i]['customer_name']= $customer_data[0]['customer_name'];
                          $birthdayCelebrationsData[$i]['mobile_no']= $customer_data[0]['mobile_no'];
                          $birthdayCelebrationsData[$i]['franchisee_id']= $customer_data[0]['franchisee_id'];
                          $student_data=  Students::where('id','=',$birthdayCelebrationsData[$i]['student_id'])->distinct()->get();
                          $birthdayCelebrationsData[$i]['student_name']=$student_data[0]['student_name'];
                        }


      }elseif($inputs['value'] == "Month"){
          $birthdayCelebrationsData  = BirthdayParties::whereRaw('MONTH(birthday_party_date) = MONTH(NOW())')
                                                      ->where('franchisee_id','=',Session::get('franchiseId'))
                                                      ->orderBy('birthday_party_date','ASC')
                                                      ->get();
          

          for($i=0;$i<count($birthdayCelebrationsData);$i++){
              $customer_data=Customers::where('id','=',$birthdayCelebrationsData[$i]['customer_id'])->get();
              $birthdayCelebrationsData[$i]['customer_name']= $customer_data[0]['customer_name'];
              $birthdayCelebrationsData[$i]['mobile_no']= $customer_data[0]['mobile_no'];
              $birthdayCelebrationsData[$i]['franchisee_id']= $customer_data[0]['franchisee_id'];
              $student_data=  Students::where('id','=',$birthdayCelebrationsData[$i]['student_id'])->get();
              $birthdayCelebrationsData[$i]['student_name']=$student_data[0]['student_name'];
          }
          

      }elseif($inputs['value'] == "Year"){
                 
            $birthdayCelebrationsData  = BirthdayParties::whereRaw('YEAR(birthday_party_date) = YEAR(NOW())')
                                                      ->where('franchisee_id','=',Session::get('franchiseId'))
                                                      ->orderBy('birthday_party_date','ASC')
                                                      ->get();
       


          for($i=0;$i<count($birthdayCelebrationsData);$i++){
              $customer_data=Customers::where('id','=',$birthdayCelebrationsData[$i]['customer_id'])->get();
              $birthdayCelebrationsData[$i]['customer_name']= $customer_data[0]['customer_name'];
              $birthdayCelebrationsData[$i]['mobile_no']= $customer_data[0]['mobile_no'];
              $birthdayCelebrationsData[$i]['franchisee_id']= $customer_data[0]['franchisee_id'];
              $student_data=  Students::where('id','=',$birthdayCelebrationsData[$i]['student_id'])->get();
              $birthdayCelebrationsData[$i]['student_name']=$student_data[0]['student_name'];
          }

      }  

      if($birthdayCelebrationsData){
        return Response::json(array('status'=> 'success', 'data'=> $birthdayCelebrationsData));
      }else{
        return Response::json(array('status'=> 'failure'));
      }
  }



  public function terms_conditions(){
      if(Auth::check()){
      $currentPage  =  "TERMS_CONDITIONS";
      $mainMenu     =  "TERMS_CONDITIONS_MAIN";
      $getTermsAndConditions = TermsAndConditions::all();
      $getTermsAndConditions = $getTermsAndConditions[0];
      //return $getTermsAndConditions;
      $data = compact('currentPage', 'mainMenu', 'getTermsAndConditions');
      return View::make('pages.dashboard.terms_conditions', $data);
      }else{
      return Redirect::action('VaultController@logout');
      }
  }



  public function addTermsAndConditionscont(){
        $inputs = Input::all();
        //return Response::json(array('status'=> "success", $inputs)); 
        $sendDetails = TermsAndConditions::addTermsAndConditions($inputs);
        if($sendDetails){
          return Response::json(array('status'=> "success", $sendDetails));  
        }else{
          return Response::json(array('status'=> "failure"));        
      }
  }
  public function toDeleteMultiple(){
      $value = array();
      $total = array();
      $toDeleteMultile = Comments::toDeleteMultile();
      $toGetMultileRecords = Comments::toGetMultileRecords();
      //return $toDeleteMultile;
     
      foreach ($toGetMultileRecords as $key => $value){
          if (!in_array($value, $toDeleteMultile)) {
              $update = Comments::where('id','=',$value)
                                ->update(['reminder_date' => 'NULL']);
          }
      }
  }
  public function UpdateBatchSchedule(){
      $getBatchesData = Batches::getBatches();

      for ($i=0; $i <count($getBatchesData) ; $i++) { 
        for ($j=0; $j < 1000 ; $j++) {
          $batch_schedule = BatchSchedule::where('batch_id', '=', $getBatchesData[$i]['id'])
                                       ->selectRaw('max(schedule_date) as schedule_date, batch_id')
                                       ->get();
          $batch_date[$i]['data'] = $batch_schedule[0]['schedule_date'];
          $date[$j]['data'] = date('Y-m-d', strtotime('+1 week', strtotime($batch_date[$i]['data'])));;
          $insert = BatchSchedule::where('franchisee_id', '=', Session::get('franchiseId'))
                                   ->insert(
                                     array(['schedule_date' => $date[$j]['data'], 
                                            'batch_id' => $getBatchesData[$i]['id'],
                                            'franchisee_id' => Session::get('franchiseId'),
                                            'season_id' => $getBatchesData[$i]['season_id'],
                                            'start_time' => $getBatchesData[$i]['preferred_time'],
                                            'end_time' => $getBatchesData[$i]['preferred_end_time'],
                                            'schedule_type' => 'class'])
                                    );
                }
        } 
       
      
  }

  public function toEditTheEnrollmentEndDates(){
      $enrollments = StudentClasses::where('franchisee_id','=',Session::get('franchiseId'))
                                    ->where('status','=','enrolled')
                                    ->get();
      foreach ($enrollments as $enrollment) {
        $selected_sessions = $enrollment['selected_sessions']-1;
        $enrollment_end_date = date('Y-m-d', strtotime('+'.$selected_sessions.' week', strtotime($enrollment['enrollment_start_date'])));
        $update_enrollment = StudentClasses::where('id','=',$enrollment['id'])
                                           ->update(['enrollment_end_date' => $enrollment_end_date]);
      
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
