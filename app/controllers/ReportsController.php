<?php
use Carbon\Carbon;
class ReportsController extends \BaseController {
        
   
        public static function view_reports(){
            if(Auth::check()){
                if(Session::get('userType') == 'ADMIN'){
                    $currentPage  =  "ViewReoprt_LI";
                    $mainMenu     =  "REPORTS_MENU_MAIN";
                    $presentdate  =  date("Y-m-d");
                    $viewData= compact('currentPage','mainMenu','presentdate');
                    return View::make('pages.reports.report_view',$viewData);
                }else{
                    return Redirect::action('DashboardController@index');
                }    
            }else{
                return Redirect::action('VaultController@logout');
            }
        }

        public static function daily_reports($id){
            if(Auth::check()){
                if(Session::get('userType') == 'ADMIN'){
                    $currentPage  =  "DailyReoprt_LI";
                    $mainMenu     =  "REPORTS_MENU_MAIN";
                    $presentdate  =  date("Y-m-d");
                    $dataDisplay = $id;
                    $viewData= compact('currentPage','mainMenu','presentdate','dataDisplay');
                    return View::make('pages.reports.daily_reports',$viewData);
                }else{
                    return Redirect::action('DashboardController@index');
                }    
            }else{
                return Redirect::action('VaultController@logout');
            }
        }

        public static function mismatch_enrollments(){
            if(Auth::check()){
                if(Session::get('userType') == 'ADMIN'){
                    $currentPage  =  "mismatch_enrollments";
                    $mainMenu     =  "REPORTS_MENU_MAIN";
                    $presentdate  =  date("Y-m-d");
                    $viewData= compact('currentPage','mainMenu','presentdate');
                    return View::make('pages.reports.mismatch_enrollments',$viewData);
                }else{
                    return Redirect::action('DashboardController@index');
                }    
            }else{
                return Redirect::action('VaultController@logout');
            }
        }

        public static function kids_deleted_batch(){
            if(Auth::check()){
                if(Session::get('userType') == 'ADMIN'){
                    $currentPage  =  "kids_deleted_batch";
                    $mainMenu     =  "REPORTS_MENU_MAIN";
                    $presentdate  =  date("Y-m-d");
                    $viewData= compact('currentPage','mainMenu','presentdate');
                    return View::make('pages.reports.kids_deleted_batch',$viewData);
                }else{
                    return Redirect::action('DashboardController@index');
                }    
            }else{
                return Redirect::action('VaultController@logout');
            }
        }

        public static function kbi_reports(){
            if(Auth::check()){
                if(Session::get('userType') == 'ADMIN'){
                    $currentPage  =  "KbiReoprt_LI";
                    $mainMenu     =  "REPORTS_MENU_MAIN";
                    $presentdate  =  date("Y-m-d");
                    
                    $presentdate= Carbon::now();
                    $weeekdate= new carbon();
                    $time = strtotime($presentdate);
                    $end = strtotime('next sunday, 11:59pm', $time);
                    $timestamp = strtotime($presentdate);
					$day_of_the_week = date('N', $timestamp); // N returns mon-sun as digits 1 - 7. 
					$sunday_ts = $timestamp + ( 7 - $day_of_the_week) * 24 * 60 * 60;
					$monday_ts = $timestamp - ( $day_of_the_week - 1) * 24 * 60 * 60;
					$dates = array();
					for($i = 1; $i < 5; $i++) {
		    				$dates_key = $i;
		    				$dates[$dates_key] = array(
		        			'start' => date('Y-m-d', $monday_ts - $i * 7 * 24 * 60 * 60),
		        			'end' => date('Y-m-d', $sunday_ts - $i * 7 * 24 * 60 * 60)
		        			);
					}
					
					$weeks = array();
					for($i = 1; $i < 5; $i++) {
		                                $dates_key = $i;
		                                $weeks[$dates_key] = array(
		                                'start' => date('d-M', $monday_ts - $i * 7 * 24 * 60 * 60),
		                                'end' => date('d-M', $sunday_ts - $i * 7 * 24 * 60 * 60)
		                                );
		                        }
					
					//***********Current DAY, WEEK********//

		                        $endOfWeek = strtotime('last sunday, 11:59pm', $time);
					$endOfWeekDate = date('d-M', $endOfWeek);
					$currentMonth = new Carbon('first day of this month');
					$currentMonthStartDate = date('d-M', strtotime($currentMonth));
					$currentMonth = date('Y-m-d',strtotime($currentMonth));
					//************ProspectS Info**********//

					        $customer_members=  CustomerMembership::where('membership_start_date','<=',$presentdate->toDateString())
		                                  ->where('membership_end_date','>=',$presentdate->toDateString())
		                                  ->select('customer_id')
		                                  ->get();

		            		$id;
		            		foreach($customer_members as $c){
		                		$id[]=$c['customer_id'];
		            		}
		            		$customers = Customers::where('franchisee_id','=',Session::get('franchiseId'))
		                        			->whereNotIn('id',$id)
		                        			->orderBy('id','Desc')
		                        			->get();
					            $customer_id = '';
		                        if(!empty($customers)){
		                         foreach($customers as $c){
		                                $customer_id[]=$c['id'];
		                         }
		                        }		
			
					//*************NewLeads Information************//
									
						$newLeadsForcurrentWeek = Comments::getThisWeekNewLeads($customer_id, $presentdate, $endOfWeek);
						$newLeadsForWeek1 = Comments::getNewLeadsForWeekWise($customer_id, $dates[1]['start'],  $dates[1]['end']);
						$newLeadsForWeek2 = Comments::getNewLeadsForWeekWise($customer_id, $dates[2]['start'],  $dates[2]['end']);
						$newLeadsForWeek3 = Comments::getNewLeadsForWeekWise($customer_id, $dates[3]['start'],  $dates[3]['end']);
						$newLeadsForWeek4 = Comments::getNewLeadsForWeekWise($customer_id, $dates[4]['start'],  $dates[4]['end']);
						$currentMonthNewLeads = Comments::getNewLeadsForThisMonth($customer_id, $presentdate, $currentMonth);

					//**************IV Attended Information**********//

						$currentWeekIvAttended = Comments::getCurrentWeekIvAttended($customer_id, $presentdate, $endOfWeek);
						$IvAttendedInWeek1 = Comments::getWeekWiseIvAtteded($customer_id, $dates[1]['start'],  $dates[1]['end']);
						$IvAttendedInWeek2 = Comments::getWeekWiseIvAtteded($customer_id, $dates[2]['start'],  $dates[2]['end']);
						$IvAttendedInWeek3 = Comments::getWeekWiseIvAtteded($customer_id, $dates[3]['start'],  $dates[3]['end']);
						$IvAttendedInWeek4 = Comments::getWeekWiseIvAtteded($customer_id, $dates[4]['start'],  $dates[4]['end']);
						$IvAttendedInThisMonth = Comments::getThisMonthIvAttended($customer_id, $presentdate, $currentMonth);	
					//*************Outstanding Leads Info ***********//
						
						$currentWeekOutStandLeads = Comments::getThisWeekOsLeads($customer_id, $presentdate, $endOfWeek);
						$outStandLeadsWeek1 = Comments::getWeekWiseOsLeads($customer_id, $dates[1]['start'],  $dates[1]['end']);
						$outStandLeadsWeek2 = Comments::getWeekWiseOsLeads($customer_id, $dates[2]['start'],  $dates[2]['end']);
						$outStandLeadsWeek3 = Comments::getWeekWiseOsLeads($customer_id, $dates[3]['start'],  $dates[3]['end']);
						$outStandLeadsWeek4 = Comments::getWeekWiseOsLeads($customer_id, $dates[4]['start'],  $dates[4]['end']);		
						$thisMonthOutStandLeads = Comments::getThisMonthOutStands($customer_id, $presentdate, $currentMonth);	

					//************IV Scheduled Info **************//

						$currentWeekIvScheduled = Comments::getCurrentWeekIvScheduled($customer_id, $presentdate, $endOfWeek);
						$IvScheduledInWeek1 = Comments::getWeekWiseIvScheduled($customer_id, $dates[1]['start'],  $dates[1]['end']);
						$IvScheduledInWeek2 = Comments::getWeekWiseIvScheduled($customer_id, $dates[2]['start'],  $dates[2]['end']);
						$IvScheduledInWeek3 = Comments::getWeekWiseIvScheduled($customer_id, $dates[3]['start'],  $dates[3]['end']);
						$IvScheduledInWeek4 = Comments::getWeekWiseIvScheduled($customer_id, $dates[4]['start'],  $dates[4]['end']);
						$IvScheduledInThisMonth = Comments::getThisMonthIvScheduled($customer_id, $presentdate, $currentMonth);
					//*********** HOT Leads YES*****************//
					
						$currentWeekHotLeadsYes = Comments::getCurrentWeekHotLeadsYes($customer_id, $presentdate, $endOfWeek);
						$hotLeadsYesWeek1 = Comments::getWeekWiseHotLeadsYes($customer_id, $dates[1]['start'],  $dates[1]['end']);
						$hotLeadsYesWeek2 = Comments::getWeekWiseHotLeadsYes($customer_id, $dates[2]['start'],  $dates[2]['end']);
						$hotLeadsYesWeek3 = Comments::getWeekWiseHotLeadsYes($customer_id, $dates[3]['start'],  $dates[3]['end']);
						$hotLeadsYesWeek4 = Comments::getWeekWiseHotLeadsYes($customer_id, $dates[4]['start'],  $dates[4]['end']);
						$currentMonthHotLeads = Comments::getHotLeadsForThisMonth($customer_id, $presentdate, $currentMonth);

					//********* HOT Leads NO******************//
					
						$currentWeekHotLeadsNo = Comments::getCurrentWeekHotLeadsNo($customer_id, $presentdate, $endOfWeek);
                        $hotLeadsNoWeek1 = Comments::getWeekWiseHotLeadsNo($customer_id, $dates[1]['start'],  $dates[1]['end']);
                        $hotLeadsNoWeek2 = Comments::getWeekWiseHotLeadsNo($customer_id, $dates[2]['start'],  $dates[2]['end']);
                        $hotLeadsNoWeek3 = Comments::getWeekWiseHotLeadsNo($customer_id, $dates[3]['start'],  $dates[3]['end']);
                        $hotLeadsNoWeek4 = Comments::getWeekWiseHotLeadsNo($customer_id, $dates[4]['start'],  $dates[4]['end']);
		                $currentMonthNoLeads = Comments::getNoLeadsForThisMonth($customer_id, $presentdate, $currentMonth);
				
					//******** Hot Leads May be *************//

						$currentWeekHotLeadsMaybe = Comments::getCurrentWeekHotLeadsMaybe($customer_id, $presentdate, $endOfWeek);
                        $hotLeadsMaybeWeek1 = Comments::getWeekWiseHotLeadsMaybe($customer_id, $dates[1]['start'],  $dates[1]['end']);
                        $hotLeadsMaybeWeek2 = Comments::getWeekWiseHotLeadsMaybe($customer_id, $dates[2]['start'],  $dates[2]['end']);
                        $hotLeadsMaybeWeek3 = Comments::getWeekWiseHotLeadsMaybe($customer_id, $dates[3]['start'],  $dates[3]['end']);
                        $hotLeadsMaybeWeek4 = Comments::getWeekWiseHotLeadsMaybe($customer_id, $dates[4]['start'],  $dates[4]['end']);
						$currentMonthMaybeLeads = Comments::getMaybeLeadsForThisMonth($customer_id, $presentdate, $currentMonth);
						
					//********* Renewals Due ****************//

						$currentWeekRenewalDue = PaymentDues::getCurrentWeekRenewalsDue($presentdate, $endOfWeek);				       		       
                        $renewalDueWeek1 = PaymentDues::getWeekWiseRenewalsDue($dates[1]['start'],  $dates[1]['end']);
                        $renewalDueWeek2 = PaymentDues::getWeekWiseRenewalsDue($dates[2]['start'],  $dates[2]['end']);
                        $renewalDueWeek3 = PaymentDues::getWeekWiseRenewalsDue($dates[3]['start'],  $dates[3]['end']);
                        $renewalDueWeek4 = PaymentDues::getWeekWiseRenewalsDue($dates[4]['start'],  $dates[4]['end']);				       		       
                        $currentMonthRenewalDue = PaymentDues::getCurrentMonthRenewalsDue($presentdate, $currentMonth);
                        $currentMonthRenewalDue = count($currentMonthRenewalDue);
                    //*********** Get New Prospects **************//				
                        $getNewProspects = Comments::getNewProspects($presentdate, $currentMonth);

                    //*********** No. of new enrollments ****************//

                        $NoOfNewEnrollments = PaymentDues::getNoOfNewEnrollments($presentdate, $currentMonth);


                    //************ Total Enrollments *******************//

                        $totalEnrollmetns = PaymentDues::getTotalEnrollments();

                    //************* Current month Marketing budget ***************//

                        $marketingBudget = PaymentDues::getMarketingBudget($presentdate, $currentMonth);


                    //************* No.of Renewals done *****************//    

                        $noOfRenewalsDoneInthisMonth = PaymentDues::getNoOfRenwalsDone($presentdate, $currentMonth);
                        
                        if ($noOfRenewalsDoneInthisMonth !== 0) {
                            $noOfRenewalsDoneInthisMonth = count($noOfRenewalsDoneInthisMonth);
                        }

                    //********************Student Retention ******************//
                        $noOfRenewalsPending = $currentMonthRenewalDue - $noOfRenewalsDoneInthisMonth;

                        if ($noOfRenewalsPending !== 0) {
                            $studentRetention = (($noOfRenewalsDoneInthisMonth/$noOfRenewalsPending)*100);
                            $studentRetention = round($studentRetention, 2);
                        } else {
                            $studentRetention = 0;
                        }


                    //************ Intro conversation ********************//

                        if ($IvAttendedInThisMonth !== 0) {
                            $introConversation = (($NoOfNewEnrollments/$IvAttendedInThisMonth)*100);
                            $introConversation = round($introConversation, 2);
                        } else {
                            $introConversation = 0;
                        }

                    //************* Inquiry to Intro Experience ****************//

                        if ($getNewProspects !== 0) {
                            $inqToIntroExp = (($IvScheduledInThisMonth/$getNewProspects)*100);
                            $inqToIntroExp = round($inqToIntroExp, 2);
                        } else {
                            $inqToIntroExp = 0;
                        }

                    //************ Intro Attendance efficiency *****************//    
                        if($IvAttendedInThisMonth !== 0) {
                            $introAttendaceEff = (($IvScheduledInThisMonth/$IvAttendedInThisMonth)*100);
                            $introAttendaceEff = round($introAttendaceEff, 2);
                        } else {
                            $introAttendaceEff = 0;
                        }

                    //************* Marketing efficiency **********************//

                        if ($getNewProspects !== 0) {
                            $MarketingEff = (($marketingBudget/$getNewProspects)*100);
                            $MarketingEff = round($MarketingEff, 2);
                        } else {
                            $MarketingEff = 0;
                        }


					    $viewData= compact('currentPage','mainMenu','presentdate','weeks',
                                        'newLeadsForWeek1','newLeadsForWeek2','newLeadsForcurrentWeek','newLeadsForWeek3','newLeadsForWeek4',
                                        'currentWeekIvAttended','IvAttendedInWeek1','IvAttendedInWeek2','IvAttendedInWeek3','IvAttendedInWeek4',
                                        'currentWeekIvScheduled','IvScheduledInWeek1','IvScheduledInWeek2','IvScheduledInWeek3','IvScheduledInWeek4',
                                        'currentWeekHotLeadsYes','hotLeadsYesWeek1','hotLeadsYesWeek2','hotLeadsYesWeek3','hotLeadsYesWeek4',
                                        'currentWeekHotLeadsNo','hotLeadsNoWeek1','hotLeadsNoWeek2','hotLeadsNoWeek3','hotLeadsNoWeek4',
                                        'currentWeekHotLeadsMaybe', 'hotLeadsMaybeWeek1','hotLeadsMaybeWeek2','hotLeadsMaybeWeek3','hotLeadsMaybeWeek4','endOfWeekDate',
                                        'currentMonthStartDate','currentMonthNoLeads','currentMonthNewLeads','currentMonthHotLeads','currentMonthMaybeLeads',
                                        'IvScheduledInThisMonth','IvAttendedInThisMonth','currentWeekRenewalDue','currentMonthRenewalDue','renewalDueWeek1',
                                        'renewalDueWeek2','renewalDueWeek3','renewalDueWeek4',
                                        'currentWeekOutStandLeads','thisMonthOutStandLeads','outStandLeadsWeek1','outStandLeadsWeek2','outStandLeadsWeek3','outStandLeadsWeek4',
                                        'introAttendaceEff', 'getNewProspects','inqToIntroExp','NoOfNewEnrollments','totalEnrollmetns',
                                        'introConversation', 'studentRetention','MarketingEff','marketingBudget','noOfRenewalsPending','noOfRenewalsDoneInthisMonth');
                    return View::make('pages.reports.kbi_view',$viewData);
                }else{
                    return Redirect::action('DashboardController@index');
                }    
            }else{
                return Redirect::action('VaultController@logout');
            }
        }

        public static function activityReport(){
        	
        	if(Auth::check()){
        		
        		$inputs=  Input::all();
                 
                $data = array(PaymentDues::getAllBirthdayPaymentsforActivityReport($inputs));
                $iv_data = array(IntroVisit::getIvForActivityReport($inputs));
                $retention_data = array(Retention::getRetentionForActivityReport($inputs));
                $inquiry_data = array(Inquiry::getInquiryForActivityReport($inputs));
                $complaint_data = array(Complaint::getComplaintsForActivityReport($inputs));

                if (isset($data) && !empty($data)) {
                	$data = json_decode($data[0]['data'],true);
                }

                if (isset($iv_data) && !empty($iv_data)) {
                	$iv_data = json_decode($iv_data[0]['data'],true);
                }

                if (isset($retention_data)) {
                	$retention_data = json_decode($retention_data[0]['data'],true);//json_decode($retention_data[0]['data'],true);
                }

                if (isset($inquiry_data) && !empty($inquiry_data)) {
                	$inquiry_data = json_decode($inquiry_data[0]['data'],true);
                }

                if (isset($complaint_data) && !empty($complaint_data)) {
                	$complaint_data = json_decode($complaint_data[0]['data'],true);
                }
               
                $output = json_encode(
                	array_merge(
                		$data,
                		$iv_data,
                		$retention_data,
                		$inquiry_data,
                		$complaint_data
                	)
                );
                return Response::json(array('status'=> 'success', 'data'=> json_decode($output, true)));
        	}
        	
        }
    
        public static function generatereport(){
            if(Auth::check()){
                $inputs=  Input::all();
                if($inputs['reportType']=='Birthday'){
                    return Response::json(array(PaymentDues::getAllBirthdayPaymentsforReport($inputs),'Birthday'));
                }else if($inputs['reportType']=='Enrollment'){
                    return Response::json(array(PaymentDues::getAllEnrollmentPaymentsforReport($inputs),'Enrollment'));
                }else if($inputs['reportType']=='both'){
                    return Response::json(array(PaymentDues::getAllEnrollmentBirthdayPaymentsforReport($inputs),'both'));
                }else if($inputs['reportType']=='Membership'){
                    return Response::json(array(PaymentDues::getAllMembershipPaymentsforReport($inputs),'Membership'));
                }else if($inputs['reportType']=='Introvisit'){
                    return Response::json(array(IntroVisit::getAllIntrovisitforReport($inputs),'Introvisit'));
                }else if($inputs['reportType']=='Inquiry'){
                    return Response::json(array(Inquiry::getAllInquiryforReport($inputs),'Inquiry'));
                }else if($inputs['reportType']=='Customer_mails'){
                    return Response::json(array(Customers::getCustomersReport($inputs),'Customer_mails'));
                }else if($inputs['reportType']=='Renewal_due'){
                    return Response::json(array(PaymentDues::getRenewalsDueReport($inputs),'Renewal_due'));
                }else if($inputs['reportType']=='Renewal_done'){
                    return Response::json(array(PaymentDues::getRenewalsDoneReport($inputs),'Renewal_done'));
                }else if($inputs['reportType']=='Renewal_pending'){
                    return Response::json(array(PaymentDues::getRenewalsPendingReport($inputs),'Renewal_pending'));
		            }else if($inputs['reportType']=='Calls'){
                    return Response::json(array(Comments::getAllFollowupReportsForCalls($inputs),'Calls'));
                }else if($inputs['reportType']=='Calls_Made'){
                    return Response::json(array(Comments::getAllFollowupReports($inputs),'Calls_Made'));
                }else if($inputs['reportType']=='BySchool'){
                    return Response::json(array(PaymentDues::getBySchoolEnrollmentReport($inputs),'BySchool'));
                }else if($inputs['reportType']=='ByLocality'){
                    return Response::json(array(PaymentDues::getByLocalityEnrollmentReport($inputs),'ByLocality'));
                }else if($inputs['reportType']=='ByApartment'){
                    return Response::json(array(PaymentDues::getByApartmentEnrollmentReport($inputs),'ByApartment'));
                }
                return Response::json(array($inputs));
            }
        }

        public static function generateDailyReport () {
            if(Auth::check()) {
                $inputs = Input::all();
                if($inputs['reportType']=='dailyPhoneCalls'){
                    return Response::json(array(
                        StudentClasses::getAllmissedClasses($inputs),
                        StudentClasses::getAllTmrwClassesIntr($inputs),
                        BirthdayParties::todayBdaysForDailyRepo($inputs),
                        StudentClasses::getAllMissedIntro($inputs),
                        Inquiry::lastTwoInqNotShed($inputs),
                        IntroVisit::before2days_introvisitnotset($inputs),
                        'dailyPhoneCalls'));
                }
            }
        }
        public static function UpdateDataBatch(){
        	if(Auth::check()){
    	        $inputs=  Input::all();
    	        $batch_ids = explode(',', $inputs['batch_id']);
    	        
        		$attendance = Attendance::whereIn('batch_id', $batch_ids)
        							    ->update(['batch_id' => $inputs['update_id']]);

               /* $bacthes = BatchSchedule::whereIn('batch_id', $batch_ids)
                				        ->update(['batch_id' => $inputs['update_id']]);	*/

                $estimate = Estimate::whereIn('batch_id', $batch_ids)
                				    ->update(['batch_id' => $inputs['update_id']]);

                $introvisit = IntroVisit::whereIn('batch_id', $batch_ids)
                				        ->update(['batch_id' => $inputs['update_id']]);

                $paymnet_dues = PaymentDues::whereIn('batch_id', $batch_ids)
                				           ->update(['batch_id' => $inputs['update_id']]);

                $student_classes = StudentClasses::whereIn('batch_id', $batch_ids)
                				  ->update(['batch_id' => $inputs['update_id']]);
	            
        	}
        }

        public static function addMarketingBudget(){
            $inputs=  Input::all();
            $budgetAmount = $inputs['budgetAmount'];
            $budgetMonth = date('M',strtotime($inputs['budgetMonth']));
            $budgetYear = date('Y',strtotime($inputs['budgetMonth']));
            $getDataForThisYM = MarketingBudget::where('franchisee_id', '=', Session::get('franchiseId'))
                                               ->where('year', '=', $budgetYear)
                                               ->where('month', '=', $budgetMonth)
                                               ->get();
            if (count($getDataForThisYM) > 0) {
                $insert = MarketingBudget::where('franchisee_id', '=', Session::get('franchiseId'))
                                         ->where('year', '=', $budgetYear)
                                         ->where('month', '=', $budgetMonth)
                                         ->update([
                                                'franchisee_id'=> Session::get('franchiseId'),
                                                'year'=> $budgetYear,
                                                'month'=> $budgetMonth,
                                                'budget_amount'=> $budgetAmount
                                            ]);    
            } else {
                $insert = MarketingBudget::insert(
                                            array([
                                                'franchisee_id'=> Session::get('franchiseId'),
                                                'year'=> $budgetYear,
                                                'month'=> $budgetMonth,
                                                'budget_amount'=> $budgetAmount
                                            ])
                    );
            }
            if ($insert) {
                return Response::json(array('status'=>'success','data'=>$inputs));
            } else {
                return Response::json(array('status'=>'failed','data'=>$inputs));
            }
        }

        public static function checkMbExist(){
            $inputs=  Input::all();
            $budgetAmount = $inputs['budgetAmount'];
            $budgetMonth = date('M',strtotime($inputs['budgetMonth']));
            $budgetYear = date('Y',strtotime($inputs['budgetMonth']));
            $getDataForThisYM = MarketingBudget::where('franchisee_id', '=', Session::get('franchiseId'))
                                               ->where('year', '=', $budgetYear)
                                               ->where('month', '=', $budgetMonth)
                                               ->get();
            if (count($getDataForThisYM) > 0) {
                return Response::json(array('status'=>'success','data'=> $getDataForThisYM));
            } else {
                return Response::json(array('status'=>'failed','data'=> $inputs));
            }
        }
    

        public static function salesAllocreport(){

                       if(Auth::check()){
                               $inputs=  Input::all();

                               $start_date = date_create($inputs['reportGenerateStartdate1']);
                               $end_date = date_create($inputs['reportGenerateEnddate1']);


                               $salesFile = Orders::getSalesAllocReport($inputs);

        		
        		//$salesFile = PaymentDues::getSalesAllocReport($inputs);
        		//return $salesFile;

        		//$sheetheaders = ['Parent Name', 'Child Name', 'Payment Date', 'Date of Birth', 'Name Of Class', 'Start Date', 'End Date', 'No.Of Classes Selected', '2nd Class', 'Membership', 'Membership Amount', 'Fees', 'Tax Amount', 'Discount', 'Discount For Siblings', 'Discount for Multi-class', 'Total', 'Mode Of Payment'];
        		//$sheetData = [];
        		//$sheetData[] = $sheetheaders;
				//$sheetData[] =$salesFile;
				//array_push($sheetData, $sheetheaders);
				//array_push($sheetData, $salesFile);

				//print_r($sheetData); die;

				Excel::create('Sales_Allocation_Report', function($excel) use($salesFile, $start_date, $end_date) {
		              $excel->sheet('Sheet 1', function($sheet) use($salesFile, $start_date, $end_date){
		                  
		                  //Styles in Row wise
		                  $sheet->mergeCells('A1:V1');
		                  $sheet->setAllBorders('thin');
		                  $heightArray = array(
		                      1     =>  50,
		                      2     =>  50,
		                  );
		                  for ($i=3; $i < count($salesFile); $i++) { 
		                  	$heightArray[$i] = 22; 
		                  }

		                  $sheet->setHeight($heightArray);
		                  $sheet->row(1, function ($row) {
		                      $row->setFontFamily('Calibri');
		                      $row->setFontSize(11);
		                      $row->setFontColor('#ffffff');
		                      $row->setAlignment('center');
		                      $row->setFontWeight('normal');
		                      $row->setValignment('center');
		                      $row->setBackground('#205867');
		                  });
		                  $sheet->row(2, function ($row) {
		                      $row->setFontFamily('Calibri');
		                      $row->setFontSize(9);
		                      $row->setFontColor('#ffffff');
		                      $row->setAlignment('center');
		                      $row->setFontWeight('normal');
		                      $row->setValignment('center');
		                      $row->setBackground('#205867');
		                  });

		                  //Set Headers in row wise
		                  $sheet->row(1, array('MASTER SALES ALLOCATION FOR THE MONTH OF '. date_format($start_date,"Y/m/d"). " To ". date_format($end_date,"Y/m/d")));

		                  //Writing into file 
		                  $sheet->fromArray($salesFile, null, 'A2', false, false);
		              });
		          })->store('xls', storage_path('sales-allocation'));//->download('xlsx');

				$filename = url()."/app/storage/sales-allocation/Sales_Allocation_Report.xls";
				return Response::json(array('status'=> "success", 'data'=> $filename));

        		//'Discount For Siblings', 'Discount for Multi-class', 'Tax %', 'Tax Amount', 'Total',
        		/*$sheetheaders = ['ROLL NUMBER', 'INVOICE NUMBER', "Date of Billing\nMM/DD/YYYY", "Date of Birth\nMM/DD//YYYY", 'Child Name', 'Parent Name', 'Class', 'No. Of Weeks', '2nd Class', "Start Date\nMM/DD/YYYY", 'End Date', 'Membership', 'Membership Amount', 'Classes', 'Discount', 'Discount For Siblings', 'Discount for Multi-class', 'Tax %', 'Tax Amount', 'Total', "Mode Of\nPayment"];

        		//Concatinating shet headers and body
				$sheetData[0] = $sheetheaders;
				$sheetData = $sheetData + $salesFile;

        		Excel::create('Sales Allocation Report', function($excel) use($sheetData) {
		              $excel->sheet('Sheet 1', function($sheet) use($sheetData){
		                  
		                  //Styles in Row wise
		                  $sheet->mergeCells('A1:U1');
		                  $sheet->setAllBorders('thin');
		                  $heightArray = array(
		                      1     =>  50,
		                      2     =>  50,
		                  );
		                  for ($i=3; $i < count($sheetData); $i++) { 
		                  	$heightArray[$i] = 22; 
		                  }

		                  $sheet->setHeight($heightArray);
		                  $sheet->row(1, function ($row) {
		                      $row->setFontFamily('Calibri');
		                      $row->setFontSize(11);
		                      $row->setFontColor('#ffffff');
		                      $row->setAlignment('center');
		                      $row->setFontWeight('normal');
		                      $row->setValignment('center');
		                      $row->setBackground('#205867');
		                  });
		                  $sheet->row(2, function ($row) {
		                      $row->setFontFamily('Calibri');
		                      $row->setFontSize(9);
		                      $row->setFontColor('#ffffff');
		                      $row->setAlignment('center');
		                      $row->setFontWeight('normal');
		                      $row->setValignment('center');
		                      $row->setBackground('#205867');
		                  });

		                  //Set Headers in row wise
		                  $sheet->row(1, array('MASTER SALES ALLOCATION FOR THE MONTH OF '. date("F Y")));

		                  //Writing into file 
		                  $sheet->fromArray($sheetData);
		              });
		          })->export('xls');*/


        	}else{
        		return Redirect::action('VaultController@logout');
        	}
        }
        
        public static function deleted_customers(){
            if((Auth::check()) && (Session::get('userType'))=='ADMIN'){
                $currentPage  =  "ViewDeletedCustomer_LI";
                $mainMenu     =  "REPORTS_MENU_MAIN";
                $deletedCustomer_data=DeletedCustomers::where('franchisee_id','=',Session::get('franchiseId'))
                                                        ->orderBy('id','desc')
                                                        ->get();
                $viewData= compact('currentPage','mainMenu','deletedCustomer_data');
                return View::make('pages.reports.deletedcustomer_view',$viewData);
            }else{
                return Redirect::action('VaultController@logout');
            }
        }

        public static function getMisMatchReports () {
            if((Auth::check()) && (Session::get('userType'))=='ADMIN'){
                $data = DB::select(DB::raw("SELECT student_id,enrollment_start_date, enrollment_end_date,selected_sessions, 
                    ROUND((DATEDIFF(enrollment_end_date,enrollment_start_date))/7) as count from student_classes 
                    where ROUND((DATEDIFF(enrollment_end_date,enrollment_start_date))/7) != (selected_sessions-1) and franchisee_id = ".Session::get('franchiseId')."
                    and enrollment_end_date >= '2018-10-18'"));
                if ($data) {
                    return Response::json(array('status'=>'success', 'data' => $data));
                } else {
                    return Response::json(array('status'=>'failed'));
                }
            }
        }

        public static function getDeletedBatchIdReports () {
            if((Auth::check()) && (Session::get('userType'))=='ADMIN'){
                $data = DB::select(DB::raw("SELECT s.student_name, sc.student_id, sc.batch_id, sc.class_id, c.class_name FROM student_classes sc, students s, classes c WHERE sc.batch_id NOT IN (SELECT id FROM batches) and sc.franchisee_id = ".Session::get('franchiseId')." and sc.student_id = s.id and c.id = sc.class_id ORDER BY sc.batch_id"));
                //return $data;
                if ($data) {
                    return Response::json(array('status'=>'success', 'data' => $data));
                } else {
                    return Response::json(array('status'=>'failed'));
                }
            }
        }

        public static function updateEnrollmentEndDate () {
            if((Auth::check()) && (Session::get('userType'))=='ADMIN'){
                $data = DB::select(DB::raw("SELECT id,student_id,enrollment_start_date, enrollment_end_date,selected_sessions, 
                   ROUND((DATEDIFF(enrollment_end_date,enrollment_start_date))/7) as count from student_classes 
                   where ROUND((DATEDIFF(enrollment_end_date,enrollment_start_date))/7) != (selected_sessions-1) and franchisee_id = ".Session::get('franchiseId')."
                   and enrollment_end_date >= '2018-10-18'"));

                foreach ($data as $key => $value) {
                   $date = $value->enrollment_start_date;
                   $add_days = 7*($value->selected_sessions - 1);
                   $date = date('Y-m-d',strtotime($date.'+'.$add_days.'days'));
                   $update = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                         ->where('id', '=', $value->id)
                                         ->update(['enrollment_end_date' => $date]);
                   $update_paymentDues = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                                         ->where('student_class_id', '=', $value->id)
                                         ->update(['end_order_date' => $date]);                                         
                }

                if ($update_paymentDues) {
                   return Response::json(array('status'=>'success', 'data' => $data));
                } else {
                   return Response::json(array('status'=>'failed'));
                }
            }    
        }
	/**
	 * Display a listing of the resource.
	 * GET /reports
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /reports/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /reports
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /reports/{id}
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
	 * GET /reports/{id}/edit
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
	 * PUT /reports/{id}
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
	 * DELETE /reports/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
