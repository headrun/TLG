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
                        
                        //Enrolled customers(kids)
                        $todaysEnrolledCustomers=StudentClasses::getTodaysEnrolledCustomers();
			                  $enrolledCustomers = StudentClasses::getEnrolledCustomers();
			
                        
                        //for followups
                        $reminderCount = Comments::getReminderCountByFranchiseeId();
                        
			                   //Introvisit
                        $totalIntrovisitCount = IntroVisit::getIntrovistCount();
			                  $introVisitCount = IntroVisit::getIntrovisitBytoday();
			                  $allIntrovisits  = IntroVisit::getAllActiveIntrovisit();

                        
                        
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
                          $temp= DB::select(DB::raw("SELECT count('student_id') as totalno
                                                     FROM student_classes 
                                                     WHERE franchisee_id = '".Session::get('franchiseId')."' AND enrollment_end_date >= '".date('Y-m-d')."' AND class_id IN (select id from classes where course_id =".$course->id .") AND student_classes.status IN ('enrolled')"));


                            if($temp[0]->totalno){
                              $course->totalno=$temp[0]->totalno;
                              $totalclasses+=$temp[0]->totalno;
                            }else{
                              $course->totalno=0;
                              $totalclasses+=0;
                            }
                        }

                        /*
                        foreach($courses as $course){
                          $temp= DB::select(DB::raw("SELECT sum(selected_sessions) as totalno
                                                     FROM student_classes 
                                                     WHERE franchisee_id = ".Session::get('franchiseId').
                                                     " AND class_id IN (select id from classes where course_id =".$course->id .")".
                                                     " AND student_classes.status IN ('enrolled')"));
                          if($temp[0]->totalno){
                            $course->totalno=$temp[0]->totalno;
                            $totalclasses+=$temp[0]->totalno;
                          }else{
                            $course->totalno=0;
                            $totalclasses+=0;
                          }
                        }
                        */
                       
                        
			//for birthdayparty
                        
                        $totalbpartyCount=BirthdayParties::getBpartyCount();
                        $todaysbpartycount=BirthdayParties::getBpartyCountBytoday();
                                
			                  $todaysFollowup = Comments::getAllFollowup();
                        $futurefollowups= Comments::getFutureFollowup();
                        
                        //return $todaysFollowup;
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
                                                  ->where( DB::raw('DAY(student_date_of_birth)'), '>', $presentdate )
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
                        
                        $presentdate= Carbon::now();;
                        $weeekdate= new carbon();
                        $time = strtotime($presentdate);
                        $end = strtotime('next sunday, 11:59pm', $time);
                        //print_r(date('Y-m-d', strtotime('+1', $end))); die;
                        //$weekdatemon= $presentdate->endOfWeek();
                        //$weeekdate->addDays(7);
                        //return date('Y-m-d', $end);
                       
                        $birthdayPresentWeek=BirthdayParties::whereDate('birthday_party_date','>=',date('Y-m-d', $time))
                              // ->where('birthday_party_date','>=',$weekdatemon)
                              //  ->where('birthday_party_date','<=',$weeekdate->toDateString())
                                //->whereDate('birthday_party_date','<=',$weekdatemon->toDateString())
                                ->whereDate('birthday_party_date','<=',date('Y-m-d', $end))
                                ->orderBy('birthday_party_date','ASC')
                                ->get();
                        //print_r(array($presentdate, $weeekdate->toDateString(), $end));
                        //return $birthdayPresentWeek;

                        for($i=0;$i<count($birthdayPresentWeek);$i++){
                          $customer_data=Customers::where('id','=',$birthdayPresentWeek[$i]['customer_id'])->distinct()->get();
                          $birthdayPresentWeek[$i]['customer_name']= $customer_data[0]['customer_name'];
                          $birthdayPresentWeek[$i]['mobile_no']= $customer_data[0]['mobile_no'];
                          $birthdayPresentWeek[$i]['franchisee_id']= $customer_data[0]['franchisee_id'];
                          $student_data=  Students::where('id','=',$birthdayPresentWeek[$i]['student_id'])->distinct()->get();
                          $birthdayPresentWeek[$i]['student_name']=$student_data[0]['student_name'];
                        }
                        //return Session::get('franchisee_id');

                        //return $birthdayPresentWeek;
                        $expiringbatch= Batches::getExpiringBatchData();
                        
                        //return $birthday_data; die();
			$viewData = array('currentPage', 'mainMenu',
                                                           'birthday_data','birthday_data_month','birthday_month_startdays','birthdayPresentWeek',
                                                           'todaysMemberReg','membersCount',
                                                           'todaysNonmemberReg','NonmembersCount',
                                                            'customerCount', "reminderCount", 
                                                            'totalbpartyCount','todaysbpartycount',
                                                           'courses','futurefollowups',
							  'todaysCustomerReg','todaysEnrolledCustomers','enrolledCustomers','totalIntrovisitCount', 'introVisitCount', 'allIntrovisits', 'todaysFollowup', 
							  'todaysIntrovisit','activeRemindersCount','totalclasses', 'expiringbatch');
   
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
          $presentdate= Carbon::now();;
                        $weeekdate= new carbon();
                        $time = strtotime($presentdate);
                        $end = strtotime('next sunday, 11:59pm', $time);
                        //print_r(date('Y-m-d', strtotime('+1', $end))); die;
                        //$weekdatemon= $presentdate->endOfWeek();
                        //$weeekdate->addDays(7);
                        //return date('Y-m-d', $end);
                       
                        $birthdayCelebrationsData=BirthdayParties::whereDate('birthday_party_date','>=',date('Y-m-d', $time))
                              // ->where('birthday_party_date','>=',$weekdatemon)
                              //  ->where('birthday_party_date','<=',$weeekdate->toDateString())
                                //->whereDate('birthday_party_date','<=',$weekdatemon->toDateString())
                                ->whereDate('birthday_party_date','<=',date('Y-m-d', $end))
                                ->orderBy('birthday_party_date','ASC')
                                ->get();
                        //print_r(array($presentdate, $weeekdate->toDateString(), $end));
                       

                        for($i=0;$i<count($birthdayCelebrationsData);$i++){
                          $customer_data=Customers::where('id','=',$birthdayCelebrationsData[$i]['customer_id'])->distinct()->get();
                          $birthdayCelebrationsData[$i]['customer_name']= $customer_data[0]['customer_name'];
                          $birthdayCelebrationsData[$i]['mobile_no']= $customer_data[0]['mobile_no'];
                          $birthdayCelebrationsData[$i]['franchisee_id']= $customer_data[0]['franchisee_id'];
                          $student_data=  Students::where('id','=',$birthdayCelebrationsData[$i]['student_id'])->distinct()->get();
                          $birthdayCelebrationsData[$i]['student_name']=$student_data[0]['student_name'];
                        }


      }elseif($inputs['value'] == "Month"){
          $birthdayCelebrationsData  = BirthdayParties::whereRaw('MONTH(birthday_party_date) = MONTH(NOW())')->get();

          for($i=0;$i<count($birthdayCelebrationsData);$i++){
              $customer_data=Customers::where('id','=',$birthdayCelebrationsData[$i]['customer_id'])->get();
              $birthdayCelebrationsData[$i]['customer_name']= $customer_data[0]['customer_name'];
              $birthdayCelebrationsData[$i]['mobile_no']= $customer_data[0]['mobile_no'];
              $birthdayCelebrationsData[$i]['franchisee_id']= $customer_data[0]['franchisee_id'];
              $student_data=  Students::where('id','=',$birthdayCelebrationsData[$i]['student_id'])->get();
              $birthdayCelebrationsData[$i]['student_name']=$student_data[0]['student_name'];
          }

      }elseif($inputs['value'] == "Year"){
          $birthdayCelebrationsData  = BirthdayParties::whereRaw('YEAR(birthday_party_date) = YEAR(NOW())')->get();

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
