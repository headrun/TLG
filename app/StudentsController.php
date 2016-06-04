<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
class StudentsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//  for non enrolled kids
		if(Auth::check())
		{
			$currentPage  =  "NONENROLLEDSTUDENTS";
			$mainMenu     =  "STUDENTS_MAIN";
			
			$inputs = Input::all();
			if (isset($inputs['customerId'])) {
					
				if (Students::addStudent($inputs)) {
					Session::flash('msg', "Student added successfully.");
				} else {
					Session::flash('warning', "Student, Course Could not be added at the moment.");
				}
			}
			$franchiseeId = Session::get('franchiseId');
			$customers = Customers::getAllCustomersByFranchiseeId($franchiseeId);
			$customersDD = Customers::getAllCustomersForDropdown($franchiseeId);
                        
                        //getting nonenrolled students
			$students = StudentClasses::getAllNonEnrolledStudents(Session::get('franchiseId'));
                        
                        
                        //return $students;
			$dataToView = array("customers",'customersDD', 'students','currentPage', 'mainMenu');
			return View::make('pages.students.nonenrolledstudentlist', compact($dataToView));
			
		}else{
			return Redirect::to("/");
		}
		
	}
        
        
        
        
	
	public function enrolledstudents(){
            if(Auth::check()){
                $currentPage  =  "ENROLLEDSTUDENTS";
                $mainMenu     =  "STUDENTS_MAIN";
                $students = StudentClasses::getAllEnrolledStudents(Session::get('franchiseId'));
                //return $students;
                $dataToView = array('students','currentPage', 'mainMenu');
                return View::make('pages.students.enrolledstudentslist', compact($dataToView));
            }else{
			return Redirect::to("/");
            }
		
        }
	public function addstudent(){
		
		$inputs = Input::all();
		$addStudentResult = Students::addStudent($inputs);
		
		header('Access-Control-Allow-Origin: *');
		if($addStudentResult){
			return Response::json(array("status"=>"success"));
		}else{
			return Response::json(array("status"=>"failed"));
		}
	}
	
	
	public function getStudentById(){
		
		$inputs = Input::all();
		$id = $inputs['studentId'];
		
		$student = Students::getStudentById($id);
		
		header('Access-Control-Allow-Origin: *');
		if($student){
			return Response::json($student['0']);
		}else{
			return Response::json(array("status"=> "failed"));
		}
	}
	
        public function getStudentDetailsByIdForBatches(){
            $inputs = Input::all();
            
            $student=Students::getStudentById($inputs['studentId']);
            $student[0]['ageYear']=(date_diff(date_create(date('Y-m-d',strtotime($student[0]['student_date_of_birth']))), date_create('today'))->y);
            $student[0]['ageMonth']=(date_diff(date_create(date('Y-m-d',strtotime($student[0]['student_date_of_birth']))), date_create('today'))->m);
            header('Access-Control-Allow-Origin: *');
		if($student){
			return Response::json($student['0']);
		}else{
			return Response::json(array("status"=> "failed"));
		}
            
        }


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function view($id)
	{
		if(Auth::check())
		{
			$currentPage  =  "STUDENTS_LIST";
			$mainMenu     =  "STUDENTS_MAIN";
			$student = Students::getStudentById($id);
			$franchiseeCourses = Courses::getFranchiseCoursesList(Session::get('franchiseId'));
			$studentEnrollments = StudentClasses::getStudentEnrollments($id);
			$paymentDues = PaymentDues::getAllPaymentDuesByStudent($id);
			$customermembership = CustomerMembership::getCustomerMembership($student['0']->customer_id);
			$scheduledIntroVisits = IntroVisit::getIntrovisitByStudentId($id);
			$discountEligibility  = StudentClasses::discount($id, $student['0']->customer_id);
			
                        //for dues
                      //  $order_due_data=Orders::getpendingPaymentsid($id);
                       $order_due_data=  PaymentDues::getAllDuebyStudentId($id);
                      //  $dueAmountdata=PaymentDues::getAllDue($id);
                        for($i=0;$i<count($order_due_data);$i++){
                            
                           
                            $studentclasssectiondata=  Classes::getstudentclasses($order_due_data[0]['class_id']);
                            $order_due_data[$i]['class_name']=$studentclasssectiondata[0]['class_name'];
                            $user_Data=User::getUsersByUserId($order_due_data[$i]['created_by']);
                            $order_due_data[$i]['receivedname']=$user_Data[0]['first_name'].$user_Data[0]['last_name'];
                            
                            
                        }
                        //getting values for present Discount for enrollment
                        $discount_second_child=0;
                        $discount_second_class=0;
                        $discount_second_child_elligible=0;
                        $discount_second_class_elligible=0;
                        $count=0;
                        
                        $DiscountApprove = Discounts::where('franchisee_id', '=', Session::get('franchiseId'))->first();
                        if($DiscountApprove['discount_second_child_approve'] == 1){
                            $discount_second_child_elligible=1;
                            $discount_second_child = $DiscountApprove['discount_second_child'];
                        }
                        if($DiscountApprove['discount_second_class_approve'] == 1){
                            $discount_second_class_elligible=1;
                            $discount_second_class = $DiscountApprove['discount_second_class'];
                        }
                        
                        if($discount_second_class_elligible){
                            $classes_count=  StudentClasses::where('student_id','=',$id)
                                             ->where('status','=','enrolled')
                                           //->whereDate('enrollment_start_date','>=',date("Y-m-d"))
                                           //->whereDate('enrollment_end_date','<=',date("Y-m-d"))
                                           //->distinct('class_id')
                                             ->count();
                            
                            if($classes_count >= 1){
                                $discount_second_class_elligible=1;
                            }else{
                                $discount_second_class_elligible=0;
                            }
                        }
                        
                        if($discount_second_child_elligible){
                           $student_ids=  Students::where('customer_id','=',$student[0]['customer_id'])->select('id')->get()->toArray();
                           for($i=0;$i<count($student_ids);$i++){
                               if($student_ids[$i]['id']!=$id){
                               if(StudentClasses::where('student_id','=',$student_ids[$i]['id'])->where('status','=','enrolled')->exists()){
                                 $count++;   
                                }
                               }
                           }
                         //return $count;
                           //$discount_second_class_elligible=($count>1)?1:0;
                           if($count >= 1){
                                $discount_second_child_elligible=1;
                            }else{
                                $discount_second_child_elligible=0;
                            }
                        }
							// Getting latest batches for showing in header of student tab
                      $latestEnrolledData=  StudentClasses::where('student_id','=',$id)
                                                            ->orderBy('created_at','desc')
                                                            ->limit(2)
                                                            ->get();
                      for($i=0;$i<count($latestEnrolledData);$i++){
                          $temp=  Batches::find($latestEnrolledData[$i]['batch_id']);
                          $latestEnrolledData[$i]['batch_name']=$temp->batch_name;
                      }
                    $discountEnrollmentData=  Discounts::getEnrollmentDiscontByFranchiseId();    
                    //getting the data from payment_master
                        $payments_master_details=  PaymentMaster::where('student_id','=',$id)
                                                                  ->where('order_id','<>','0')
                                                                  ->distinct('payment_no')
                                                                  ->select('payment_no')
                                                                  ->get();
                        
                       
                        for($i=0; $i<count($payments_master_details); $i++){
                          $payment_made_data[$i]=  PaymentDues::where('student_id','=',$id)
                                                                ->where('payment_no','=',$payments_master_details[$i]['payment_no'])
                                                                ->get();
                          
                          for($j=0;$j<count($payment_made_data[$i]);$j++){
                              $temp=  Batches::where('id','=',$payment_made_data[$i][$j]['batch_id'])
                                              ->select('batch_name')
                                              ->get();
                              $temp2= User::find($payment_made_data[$i][$j]['created_by']);
                              $payment_made_data[$i][$j]['receivedname']=$temp2->first_name.$temp2->last_name;
                              
                              $payment_made_data[$i][$j]['class_name']=$temp[0]['batch_name'];
                              
                          }
                          $payments_master_details[$i]['encrypted_payment_no']=url().'/orders/print/'.Crypt::encrypt($payments_master_details[$i]['payment_no']);
                        }
                        
                        $AttendanceYeardata=DB::select("SELECT EXTRACT(year from enrollment_start_date) as year FROM student_classes WHERE student_id = $id GROUP BY year");   
                        
                         //return $student[0]['id'];
			$dataToView = array("student",'currentPage', 'mainMenu','franchiseeCourses', 
                                                                'discountEnrollmentData','latestEnrolledData',
                                                                'discount_second_class_elligible','discount_second_child_elligible','discount_second_child','discount_second_class',
								'studentEnrollments','customermembership','paymentDues',
								'scheduledIntroVisits', 'introvisit', 'discountEligibility','paidAmountdata','order_due_data',
                                                                'payment_made_data','payments_master_details', 'AttendanceYeardata');
			return View::make('pages.students.details',compact($dataToView));
		}else{
			return Redirect::to("/");
		}
	}
	

	public function getAttendanceForStudent(){
		$inputs = Input::all();
		$sendDetails = Attendance::getAttendanceForStudent($inputs);
                $totalsession=  StudentClasses::getAllClassCountByBatchId($inputs);
		if($sendDetails){
			return Response::json(array('status'=> "success", 'data'=> $sendDetails,'totalSession'=>$totalsession));
		}else{
			return Response::json(array('status'=> "failure",));
		}
	}
	public function getBatchNameByYear(){
		$inputs = Input::all();
		$sendDetails = StudentClasses::select('batch_id')->where('enrollment_start_date', 'like', '%'.$inputs["year"].'%')
									  ->where('student_id', '=', $inputs['studentId'])->distinct()->get();

		for ($i=0; $i < count($sendDetails); $i++) { 
			$name[] = Batches::where('id', '=', $sendDetails[$i]['batch_id'])->get();
		}
		if($name){
			return Response::json(array('status'=> "success", $name));
		}else{
			return Response::json(array('status'=> "failure",));
		}
	}


	
	public function saveKids() {
		
		$inputs = Input::all();
		$updateStudent = Students::updateStudent($inputs);
		header('Access-Control-Allow-Origin: *');
		if($updateStudent){
			return Response::json(array("status"=>"success"));
		}else{
			return Response::json(array("status"=>"failed"));
		}
	}
	
	
	public function uploadProfilePicture(){
		
		$file = Input::file('profileImage');
		$studentId = Input::get('studentId');
		
		$destinationPath = 'upload/profile/student/';
		
		$filename = $file->getClientOriginalName();		
		$fileExtension = '.'.$file->getClientOriginalExtension();
		
				
		$filename = 'student_profile_'.$studentId.'_medium'.$fileExtension;
				
		$result = Input::file('profileImage')->move($destinationPath, $filename);
		
		if($result){	
				
			$student = Students::find($studentId);
			$student->profile_image = $filename;
			$student->save();
		
		}
		
		Session::flash ( 'imageUploadMessage', "Profile picture updated successfully." );
		return Redirect::to("/students/view/".$studentId);
		
		
		
	}
        
        
        public function enrollOldCustomer(){
        	$inputs = Input::all();
            //return $inputs;
                $oldStudentId = $inputs['oldCustomerStudentId'];
        // There are no multiple batches, all classes are selected in one batch
        // *****
        // Inserting data to student_classes table
        //    
            $studentClasses['classId'] = $inputs['ClassesCbxForOld'];
            $studentClasses['batchId'] = $inputs['BatchesCbx'];
            $studentClasses['studentId'] = $inputs['oldCustomerStudentId'];
            $studentClasses['selected_sessions'] = $inputs['NoOfClassesForOld1'];
            $studentClasses['seasonId'] = $inputs['SeasonsCbxForOld'];
            $studentClasses['enrollment_start_date'] = $inputs['enrollmentStartDateForOld'];
            $studentClasses['enrollment_end_date'] = $inputs['enrollmentEndDateForOld'];
            $insertDataToStudentClassTable = StudentClasses::addStudentClass($studentClasses);
            
        // Inserting data to payments_dues table    
            
            $paymentDuesInput['student_id']        = $insertDataToStudentClassTable['student_id'];
            $paymentDuesInput['customer_id']       = $inputs['oldCustomerId'];
            $paymentDuesInput['batch_id']          = $insertDataToStudentClassTable['batch_id'];
            $paymentDuesInput['class_id']          = $insertDataToStudentClassTable['class_id'];
            $paymentDuesInput['franchisee_id']    = Session::get('franchiseId');
            $paymentDuesInput['selected_sessions'] = $insertDataToStudentClassTable['selected_sessions'];
            $paymentDuesInput['seasonId']          = $insertDataToStudentClassTable['season_id'];
            $paymentDuesInput['student_class_id']  = $insertDataToStudentClassTable['id'];
            $paymentDuesInput['each_class_cost']   = $inputs['EachClassAmountForOld'];
            
            
            if($inputs['MembershipTypeForOld']!=''){
                //                   adding record to membership table
                    $customerMembershipInput['customer_id']		=	$inputs['oldCustomerId'];
                    $customerMembershipInput['membership_type_id']=	$inputs['MembershipTypeForOld'];
                    $customerMembershipDetails=CustomerMembership::addMembership($customerMembershipInput);
                    $paymentDuesInput['membership_id']		=	$customerMembershipDetails->id;
                    $paymentDuesInput['membership_type_id']         =	$customerMembershipDetails->membership_type_id;
                    $paymentDuesInput['membership_amount']          =	$inputs['MembershipAmountForOld'];
            }
            
            
            $paymentDuesInput['discount_applied']       = $inputs['DiscountPercentageForOld'];
            $paymentDuesInput['discount_amount']        = $inputs['DiscountAmountForOld'];
             
           
            $paymentDuesInput['discount_sibling_applied'] = $inputs['SiblingPercentageForOld']; 
            $paymentDuesInput['discount_sibling_amount']  = $inputs['SiblingAmountForOld']; 
             
         
            $paymentDuesInput['discount_multipleclasses_applied'] = $inputs['MultiClassesPercentageForOld']; 
            $paymentDuesInput['discount_multipleclasses_amount']  = $inputs['MultiClassesAmountForOld']; 
             
            $paymentDuesInput['discount_admin_amount']                            = $inputs['AdminRupeeForOld'];    
            $paymentDuesInput['payment_due_amount']                     = $inputs['SubTotalForOld'];
            $paymentDuesInput['payment_due_amount_after_discount']      = $inputs['GrandTotalForOld'];
            $paymentDuesInput['payment_status']                         = "paid";
            $paymentDuesInput['selected_order_sessions']                = $inputs['NoOfClassesForOld1'];
            $paymentDuesInput['start_order_date']                       = $insertDataToStudentClassTable['enrollment_start_date'];
            $paymentDuesInput['end_order_date']                         = $insertDataToStudentClassTable['enrollment_end_date'];
            $paymentDuesInput['payment_batch_amount']                   = $paymentDuesInput['selected_order_sessions']*$paymentDuesInput['each_class_cost'];
            $sendPaymentDetailsToInsert = PaymentDues::createPaymentDues($paymentDuesInput);
            
            // Inserting data to  Paymemt_master table
                
                $sendPaymentMasterDetailsToInsert = PaymentMaster::createPaymentMaster($sendPaymentDetailsToInsert);
                
            // Inserting data to orders table
                
                $order['customer_id']     = $inputs['oldCustomerId'];
                $order['student_id']      = $insertDataToStudentClassTable['student_id'];
                $order['student_classes_id'] = $insertDataToStudentClassTable['id'];
                
                if($inputs['paymentTypeRadioForOld'] == 'card'){
                    $order['payment_mode']    =  $inputs['paymentTypeRadioForOld'];
                    $order['card_type']    =  $inputs['cardType3'];       
                    $order['card_last_digit']    =  $inputs['card4digits3'];       
                    $order['bank_name']    =  $inputs['cardBankName3'];       
                    $order['receipt_number']    =  $inputs['cardRecieptNumber3'];       
                }
                elseif($inputs['paymentTypeRadioForOld'] == 'cash'){
                    $order['payment_mode']    =  $inputs['paymentTypeRadioForOld'];
                }
                elseif($inputs['paymentTypeRadioForOld'] == 'cheque'){
                    $order['payment_mode']    =  $inputs['paymentTypeRadioForOld'];
                    $order['bank_name']    =  $inputs['bankName3'];
                    $order['cheque_number']    =  $inputs['chequeNumber3'];
                }      
                
                $order['tax_amount']     = $inputs['TaxForOld'];
                $order['payment_for']     = "enrollment";
                $order['payment_no']   = $sendPaymentMasterDetailsToInsert['payment_no'];
                $order['payment_dues_id']   = $sendPaymentDetailsToInsert['id'];
                $order['amount'] = $inputs['SubTotalForOld'];
                $order['order_status'] = "completed";
                $order['created_at']=date("Y-m-d H:i:s");
                    
                $sendOrderDetailsToInsert = Orders::createOrder($order);
                
                // update payments_dues table with payment_no
                
                $update_payment_due = PaymentDues::find($sendPaymentDetailsToInsert['id']);
                $final_payment_master_no=$update_payment_due->payment_no;
                $update_payment_due->payment_no=$sendPaymentMasterDetailsToInsert['payment_no'];
                $final_payment_master_no=$sendPaymentMasterDetailsToInsert['payment_no'];
                $update_payment_due->save();
                
                
                // update payment_masters table with order_id
                
                $updatePaymentMasterTable = PaymentMaster::find($sendPaymentMasterDetailsToInsert->id);
                $updatePaymentMasterTable->order_id = $sendOrderDetailsToInsert->id;
                $updatePaymentMasterTable->save();
                
                //Create followup records
                
                $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($sendPaymentDetailsToInsert,$final_payment_master_no);
                //creating logs/followup for first payment
                $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
                $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
                $customer_log_data['franchisee_id']=Session::get('franchiseId');
                $customer_log_data['paymentfollowup_id']=$payment_followup_data1->id;
                $customer_log_data['followup_type']='PAYMENT';
                $customer_log_data['followup_status']='REMINDER_CALL';
                $customer_log_data['comment_type']='VERYINTERESTED';
                $class_last_day = $sendPaymentDetailsToInsert->end_order_date;
                $customer_log_data['reminderDate']= date('Y-m-d H:i:s', strtotime('-1 day', strtotime($class_last_day)));
                $sendCommentsToInsert = Comments::addSinglePayComment($customer_log_data);
                
                // checking for email invoice
                $customer_data= Customers::find($sendPaymentDetailsToInsert->customer_id);  
                if(isset($inputs['emailOptionforoldcustomer']) && ($inputs['emailOptionforoldcustomer']=='yes') && ($customer_data->customer_email!='')){
                    $totalSelectedClasses = '';
                    $totalAmountForAllBatch = '';
                    $paymentDueDetails = PaymentDues::where('payment_no', '=', $final_payment_master_no)->get();
                    for($i = 0; $i < count($paymentDueDetails); $i++){
			$totalSelectedClasses = $totalSelectedClasses + $paymentDueDetails[$i]['selected_sessions'];
			$getBatchNname[]  = Batches::where('id', '=', $paymentDueDetails[$i]['batch_id'])->get();
			$getSeasonName[]  = Seasons::where('id', '=', $paymentDueDetails[$i]['season_id'])->get();
			$selectedSessionsInEachBatch[] = $paymentDueDetails[$i]['selected_sessions'];
			$classStartDate[] = $paymentDueDetails[$i]['start_order_date'];
			$classEndDate[] = $paymentDueDetails[$i]['end_order_date'];
			$totalAmountForEachBach[] = (int)$paymentDueDetails[$i]['payment_batch_amount'];
			$totalAmountForAllBatch = $totalAmountForAllBatch + (int)$paymentDueDetails[$i]['payment_batch_amount'];
                    }   
                    $getTermsAndConditions = TermsAndConditions::where('id', '=', (TermsAndConditions::max('id')))->get();
                    $getCustomerName = Customers::select('customer_name','customer_email')->where('id', '=', $paymentDueDetails[0]['customer_id'])->get();
                    //return Response::json(array($getCustomerName));
                    $getStudentName = Students::select('student_name')->where('id', '=', $paymentDueDetails[0]['student_id'])->get();
                    $paymentMode = Orders::where('payment_no', '=', $final_payment_master_no)->get();
                    $data = compact('totalSelectedClasses', 'getBatchNname',
                        'getSeasonName', 'selectedSessionsInEachBatch', 'classStartDate',
                        'classEndDate', 'totalAmountForEachBach', 'getCustomerName', 'getStudentName','getTermsAndConditions',
                        'paymentDueDetails', 'totalAmountForAllBatch', 'paymentMode');
                    Mail::send('emails.account.enrollment', $data, function($msg) use ($data){
					
				$msg->from(Config::get('constants.EMAIL_ID'), Config::get('constants.EMAIL_NAME'));
				$msg->to($data['getCustomerName'][0]['customer_email'], $data['getCustomerName'][0]['customer_name'])->subject('The Little Gym - Kids Enrollment Successful');
			
			});
                }
                if ($sendCommentsToInsert) {
                   return Redirect::to("/students/view/".$oldStudentId);
                }
        }
        
        public function enrollKid2(){
		$inputs = Input::all();
                $final_payment_master_no;
		//return Response::json(array('status'=>'success','inputs'=>$inputs));
                $getEstimateDetails =  Estimate::where('estimate_master_no', '=', $inputs['estimate_master_no'])
										->where('is_cancelled', '!=', '1')
										->where('franchise_id', '=', Session::get('franchiseId'))
										->get();
                //** checking if it is a one batch **//
                if(count($getEstimateDetails) == 1){
                    
                    $batch_data=  BatchSchedule::where('batch_id','=',$getEstimateDetails[0]['batch_id'])
                                                 ->where('schedule_date','>=',$getEstimateDetails[0]['enroll_start_date'])
                                                 ->where('franchisee_id','=',Session::get('franchiseId'))
                                                 ->where('season_id','=',$getEstimateDetails[0]['season_id'])
                                                 ->where('holiday','!=',1)  
                                                 ->take($getEstimateDetails[0]['no_of_opted_classes'])
                                                 ->get();
                    
                    $studentClasses['classId']               = $getEstimateDetails[0]['class_id'];
                    $studentClasses['batchId']               = $getEstimateDetails[0]['batch_id'];
                    $studentClasses['studentId']             = $getEstimateDetails[0]['student_id'];
                    $studentClasses['selected_sessions']     = $getEstimateDetails[0]['no_of_opted_classes'];
                    $studentClasses['seasonId']              = $getEstimateDetails[0]['season_id'];
                    $singleBatchstartDate=Carbon::createFromFormat('Y-m-d', $getEstimateDetails[0]['enroll_start_date']);
                    $studentClasses['enrollment_start_date'] = $getEstimateDetails[0]['enroll_start_date'];
                    $studentClasses['enrollment_end_date']   = $getEstimateDetails[0]['enroll_end_date'];
                    $singleBatchendDate=Carbon::createFromFormat('Y-m-d', $getEstimateDetails[0]['enroll_end_date']);
                    $insertDataToStudentClassTable           = StudentClasses::addStudentClass($studentClasses);
                        /* inserting into student class table is completed for single pay */
                        /* Working on preparing to payment due table for single pay */

                    $paymentDuesInput['student_id']        = $insertDataToStudentClassTable['student_id'];
                    $paymentDuesInput['customer_id']       = $getEstimateDetails[0]['customer_id'];
                    $paymentDuesInput['batch_id']          = $insertDataToStudentClassTable['batch_id'];
                    $paymentDuesInput['class_id']          = $insertDataToStudentClassTable['class_id'];
                    $paymentDuesInput['selected_sessions'] = $insertDataToStudentClassTable['selected_sessions'];
                    $paymentDuesInput['seasonId']          = $insertDataToStudentClassTable['season_id'];
                    $paymentDuesInput['student_class_id']  = $insertDataToStudentClassTable['id'];
                    $paymentDuesInput['each_class_cost']   = $getEstimateDetails[0]['base_price'];
                    
                //** checking for Membership **//
                if(isset($inputs['membershipType'])){
                	//** create membership for customer **//
                        $customerMembershipInput['customer_id']		=	$getEstimateDetails[0]['customer_id'];
                        $customerMembershipInput['membership_type_id']=	$inputs['membershipType'];
                        $customerMembershipDetails=CustomerMembership::addMembership($customerMembershipInput);
                        $paymentDuesInput['membership_id']		=	$customerMembershipDetails->id;
                        $paymentDuesInput['membership_type_id']         =	$customerMembershipDetails->membership_type_id;
                        $paymentDuesInput['membership_amount']          =	$inputs['membershipAmount'];
                }
                
                //** checking for discounts **//
                if(isset($inputs['discountTextBox'])){
                    $discount_amount = explode("-",$inputs['discountTextBox']);
                    $paymentDuesInput['discount_applied']       = $inputs['discountPercentage'];
                    $paymentDuesInput['discount_amount']        = $discount_amount[1];
                }
                if(isset($inputs['second_child_discount_to_form']) && $inputs['second_child_discount_to_form']!=''){
                    $explodedDiscountAmount=explode("-",$inputs['second_child_amount']);
                    $paymentDuesInput['discount_sibling_applied'] = $inputs['second_child_discount_to_form']; 
                    $paymentDuesInput['discount_sibling_amount']  = $explodedDiscountAmount[1];
                }
                if(isset($inputs['second_class_discount_to_form']) && $inputs['second_class_discount_to_form']!=''){
                    $explodedDiscountAmount=explode("-",$inputs['second_class_amount']);
                    $paymentDuesInput['discount_multipleclasses_applied'] = $inputs['second_class_discount_to_form']; 
                    $paymentDuesInput['discount_multipleclasses_amount']  = $explodedDiscountAmount[1];
                }
                if(isset($inputs['admin_discount_amount'])){
                    $paymentDuesInput['discount_admin_amount']=$inputs['admin_discount_amount'];
                }
                
                $paymentDuesInput['payment_due_amount']                     = $inputs['singlePayAmount'];
                $paymentDuesInput['payment_due_amount_after_discount']      = $inputs['grandTotal'];
                $paymentDuesInput['payment_status']                         = "paid";
                $paymentDuesInput['selected_order_sessions']                = $getEstimateDetails[0]['no_of_opted_classes'];
                $paymentDuesInput['start_order_date']                       = $insertDataToStudentClassTable['enrollment_start_date'];
                $paymentDuesInput['end_order_date']                         = $insertDataToStudentClassTable['enrollment_end_date'];
                $paymentDuesInput['payment_batch_amount']                   = $paymentDuesInput['selected_order_sessions']*$paymentDuesInput['each_class_cost'];
                
                $sendPaymentDetailsToInsert = PaymentDues::createPaymentDues($paymentDuesInput);
                
                /* inserting into payment Due table is completed for single pay */
                    /* Working on preparing to payment master table for single pay */
                $sendPaymentMasterDetailsToInsert = PaymentMaster::createPaymentMaster($sendPaymentDetailsToInsert);

                    /* inserting into payment Master table is completed for single pay */
                    /* Working on preparing to Orders table for single pay */

                    $order['customer_id']     = $inputs['customerId'];
                    $order['student_id']      = $insertDataToStudentClassTable['student_id'];
                    $order['student_classes_id'] = $insertDataToStudentClassTable['id'];
                    if($inputs['paymentTypeRadio'] == "card"){
                    	$order['payment_mode']    = $inputs['paymentTypeRadio'];	
                    	$order['card_last_digit'] = $inputs['card4digits'];
                    	$order['card_type']       = $inputs['cardType'];
                    	$order['bank_name']       = $inputs['cardBankName'];
                    	$order['receipt_number']       = $inputs['cardRecieptNumber'];
                    }elseif($inputs['paymentTypeRadio'] == "cheque"){
                    	$order['payment_mode']    = $inputs['paymentTypeRadio'];	
                    	$order['bank_name']       = $inputs['bankName'];
                    	$order['cheque_number']       = $inputs['chequeNumber'];

                	}elseif($inputs['paymentTypeRadio'] == "cash"){
                    	$order['payment_mode']    = $inputs['paymentTypeRadio'];	
                    }
                    
                    $order['payment_for']     = "enrollment";
                    $order['payment_no']   = $sendPaymentMasterDetailsToInsert['payment_no'];
                    $order['payment_dues_id']   = $sendPaymentDetailsToInsert['id'];
                    $order['amount'] = $inputs['singlePayAmount'];
                    $order['order_status'] = "completed";
                                      
                    $sendOrderDetailsToInsert = Orders::createOrder($order);
                    
                    
                    $update_payment_due = PaymentDues::find($sendPaymentDetailsToInsert->id);
                    $update_payment_due->payment_no=$sendPaymentMasterDetailsToInsert->payment_no;
                    $final_payment_master_no=$sendPaymentMasterDetailsToInsert->payment_no;
                    $update_payment_due->save();

                    $updatePaymentMasterTable = PaymentMaster::find($sendPaymentMasterDetailsToInsert->id);
                    $updatePaymentMasterTable->order_id = $sendOrderDetailsToInsert->id;
                    $updatePaymentMasterTable->save();
                
                    //** working on the payment_followups **//
                    
                    if(count($batch_data) >= 5){
                        $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($sendPaymentDetailsToInsert,$final_payment_master_no);
                        //creating logs/followup for first payment
                        $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
                        $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
                        $customer_log_data['franchisee_id']=Session::get('franchiseId');
                        $customer_log_data['paymentfollowup_id']=$payment_followup_data1->id;
                        $customer_log_data['followup_type']='PAYMENT';
                        $customer_log_data['followup_status']='REMINDER_CALL';
                        $customer_log_data['comment_type']='VERYINTERESTED';
                        $customer_log_data['reminderDate']=$batch_data[count($batch_data)-2]['schedule_date'];
                        Comments::addSinglePayComment($customer_log_data);
                    }
		

		
            	                
		//** checking if it is a 2 batch **//
		}elseif (count($getEstimateDetails) == 2) {
                        for($i=0;$i<2;$i++){
                         $batch_data[$i]=BatchSchedule::where('batch_id','=',$getEstimateDetails[$i]['batch_id'])
                                                 ->where('schedule_date','>=',$getEstimateDetails[$i]['enroll_start_date'])
                                                 ->where('franchisee_id','=',Session::get('franchiseId'))
                                                 ->where('season_id','=',$getEstimateDetails[$i]['season_id'])
                                                 ->where('holiday','!=',1)  
                                                 ->take($getEstimateDetails[$i]['no_of_opted_classes'])
                                                 ->get();
                    
                        }
			
				
                            for ($i=0; $i<2 ; $i++) { 
                                $studentClasses[$i]['classId']               = $getEstimateDetails[$i]['class_id'];
                                $studentClasses[$i]['batchId']               = $getEstimateDetails[$i]['batch_id'];
                                $studentClasses[$i]['studentId']             = $getEstimateDetails[$i]['student_id'];
                                $studentClasses[$i]['selected_sessions']     = $getEstimateDetails[$i]['no_of_opted_classes'];
                                $studentClasses[$i]['seasonId']              = $getEstimateDetails[$i]['season_id'];
                                $singleBatchstartDate=Carbon::createFromFormat('Y-m-d', $getEstimateDetails[$i]['enroll_start_date']);
                                $studentClasses[$i]['enrollment_start_date'] = $getEstimateDetails[$i]['enroll_start_date'];
                                $studentClasses[$i]['enrollment_end_date']   = $getEstimateDetails[$i]['enroll_end_date'];
                                $singleBatchendDate=Carbon::createFromFormat('Y-m-d', $getEstimateDetails[$i]['enroll_end_date']);
                                $insertDataToStudentClassTable               =  StudentClasses::addStudentClass($studentClasses[$i]);
                  	
                                $paymentDuesInput[$i]['student_id']          = $insertDataToStudentClassTable['student_id'];
                                $paymentDuesInput[$i]['customer_id']         = $inputs['customerId'];
                                $paymentDuesInput[$i]['batch_id']            = $insertDataToStudentClassTable['batch_id'];
                                $paymentDuesInput[$i]['class_id']            = $insertDataToStudentClassTable['class_id'];
                                $paymentDuesInput[$i]['selected_sessions']   = $insertDataToStudentClassTable['selected_sessions'];
                                $paymentDuesInput[$i]['seasonId']            = $insertDataToStudentClassTable['season_id'];
                                $paymentDuesInput[$i]['student_class_id']    = $insertDataToStudentClassTable['id'];
                                $paymentDuesInput[$i]['each_class_cost']     = $getEstimateDetails[$i]['base_price'];
                                
                                //** working on the customer_membership **//
                                if(isset($inputs['membershipType'])){
                                    //** create membership for customer **//
                                    $customerMembershipInput['customer_id']		=	$getEstimateDetails[$i]['customer_id'];
                                    $customerMembershipInput['membership_type_id']      =	$inputs['membershipType'];
                                    $customerMembershipDetails=CustomerMembership::addMembership($customerMembershipInput);

                                    $paymentDuesInput[$i]['membership_id']		=	$customerMembershipDetails->id;
                                    $paymentDuesInput[$i]['membership_type_id']         =	$customerMembershipDetails->membership_type_id;
                                    $paymentDuesInput[$i]['membership_amount']          =	$inputs['membershipAmount'];
                                }
                                //** checking for discounts **//
                                if($inputs['discountPercentage']!=''){
                                    $discount_amount                               = explode("-",$inputs['discountTextBox']);
                                    $paymentDuesInput[$i]['discount_amount']       = $discount_amount[1];
                                    $paymentDuesInput[$i]['discount_applied']      = $inputs['discountPercentage'];
                                }
                                if($inputs['second_class_discount_to_form']!=''){
                                    $discount_multipleclasses_amount                                = explode("-",$inputs['second_class_amount']);
                                    $paymentDuesInput[$i]['discount_multipleclasses_amount']        = $discount_multipleclasses_amount[1];
                                    $paymentDuesInput[$i]['discount_multipleclasses_applied']       = $inputs['second_class_discount_to_form'];
                                }
                                if($inputs['second_child_discount_to_form']!=''){
                                    $discount_sibling_amount                               = explode("-",$inputs['second_child_amount']);
                                    $paymentDuesInput[$i]['discount_sibling_amount']       = $discount_sibling_amount[1];
                                    $paymentDuesInput[$i]['discount_sibling_applied']      = $inputs['second_child_discount_to_form'];
                                }
                                
                                $paymentDuesInput[$i]['payment_due_amount']                 = $inputs['singlePayAmount'];
                                $paymentDuesInput[$i]['payment_due_amount_after_discount']  = $inputs['grandTotal'];
                                $paymentDuesInput[$i]['payment_status']                     = "paid";
                                $paymentDuesInput[$i]['selected_order_sessions']            = $getEstimateDetails[$i]['no_of_opted_classes'];
                                $paymentDuesInput[$i]['start_order_date']                   = $insertDataToStudentClassTable['enrollment_start_date'];
                                $paymentDuesInput[$i]['end_order_date']                     = $insertDataToStudentClassTable['enrollment_end_date'];
                                $paymentDuesInput[$i]['payment_batch_amount']               = $paymentDuesInput[$i]['selected_order_sessions']*$paymentDuesInput[$i]['each_class_cost'];
                                
                                $sendPaymentDetailsToInsert = PaymentDues::createPaymentDues($paymentDuesInput[$i]);
                                
                                //return Response::json(array('status'=>'success','inputs'=>$sendPaymentDetailsToInsert));              
                                /* inserting into payment Due table is completed for single pay[2batch] */
                                /* Working on preparing to payment master table for single pay [2 batch]*/
                                if($i == 0){
                                	$sendPaymentMasterDetailsToInsert1      = PaymentMaster::createPaymentMaster($sendPaymentDetailsToInsert);
                                        //** updating back to paymentDues **//
                                        $update_payment_due                     = PaymentDues::find($sendPaymentDetailsToInsert->id);
                                        $update_payment_due->payment_no=$sendPaymentMasterDetailsToInsert1->payment_no;
                                        $update_payment_due->save();


                                }else{
                                        //** creating the payment_master with same payment_no **//
                                	$sendPaymentMasterDetailsToInsert2 = PaymentMaster::createPaymentMasterWithSamePaymentNo($sendPaymentDetailsToInsert, $sendPaymentMasterDetailsToInsert1['payment_no']);
                                	
                                	//return Response::json(array('status'=>'success','inputs'=>$sendPaymentMasterDetailsToInsert2));

                                        //updating payment_dues
                                	$update_payment_due1 = PaymentDues::find($sendPaymentDetailsToInsert->id);
                                        $update_payment_due1->payment_no=$sendPaymentMasterDetailsToInsert2['payment_no'];
                                        $final_payment_master_no=$sendPaymentMasterDetailsToInsert2['payment_no'];
                                        $update_payment_due1->save();
                                        
                                        //updating order_id to payment_master for 1st batch
                		        $updateOrderNoInMaster = PaymentMaster::find($sendPaymentMasterDetailsToInsert1->id);
                		        $updateOrderNoInMaster->order_id = $sendOrderDetailsToInsert->id;
                		        $updateOrderNoInMaster->save();
                                        
                                        //updating order_id to payment_master for 2nd batch
                                        $updateOrderNoInMaster1 = PaymentMaster::find($sendPaymentMasterDetailsToInsert2->id);
                		        $updateOrderNoInMaster1->order_id = $sendOrderDetailsToInsert->id;
                		        $updateOrderNoInMaster1->save();

                		}
                                /* inserting into payment Master table is completed for single pay */
                                if($i == 0){
                                    $order['customer_id']     = $inputs['customerId'];
                                    $order['student_id']      = $insertDataToStudentClassTable['student_id'];
                                    $order['seasonId']        = $insertDataToStudentClassTable['season_id'];
                                    $order['student_classes_id'] = $insertDataToStudentClassTable['id'];
                                		//$order[$i]['payment_mode']    = $inputs['paymentTypeRadio'];

                                    if($inputs['paymentTypeRadio'] == "card"){
                                    	$order['payment_mode']    = $inputs['paymentTypeRadio'];	
                                    	$order['card_last_digit'] = $inputs['card4digits'];
                                    	$order['card_type']       = $inputs['cardType'];
                                    	$order['bank_name']       = $inputs['cardBankName'];
                                    	$order['receipt_number']  = $inputs['cardRecieptNumber'];
                                    }elseif($inputs['paymentTypeRadio'] == "cheque"){
                                    	$order['payment_mode']    = $inputs['paymentTypeRadio'];	
                                    	$order['bank_name']       = $inputs['bankName'];
                                    	$order['cheque_number']   = $inputs['chequeNumber'];
                                    }elseif($inputs['paymentTypeRadio'] == "cash"){
        		                $order['payment_mode']    = $inputs['paymentTypeRadio'];	
                		    }

                                    $order['payment_for']     = "enrollment";
                                    $order['payment_no']   = $sendPaymentMasterDetailsToInsert1['payment_no'];
		                    $order['payment_dues_id']   = $sendPaymentDetailsToInsert['id'];
        		            $order['amount'] = $inputs['singlePayAmount'];
                		    $order['order_status'] = "completed";
                                    $sendOrderDetailsToInsert = Orders::createOrder($order);
                                }
		            }
                            
                            
                    //** working on the payment_followups **//
                    
                    if((count($batch_data[0]) + count($batch_data[1])) >= 5){
                        $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($sendPaymentDetailsToInsert,$final_payment_master_no);
                        //creating logs/followup for first payment
                        $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
                        $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
                        $customer_log_data['franchisee_id']=Session::get('franchiseId');
                        $customer_log_data['paymentfollowup_id']=$payment_followup_data1->id;
                        $customer_log_data['followup_type']='PAYMENT';
                        $customer_log_data['followup_status']='REMINDER_CALL';
                        $customer_log_data['comment_type']='VERYINTERESTED';
                        if(count($batch_data[1])>=3){
                        $customer_log_data['reminderDate']=$batch_data[1][count($batch_data[1])-2]['schedule_date'];
                        }else{
                            if(count($batch_data[1])==2){
                                 $customer_log_data['reminderDate']=$batch_data[0][count($batch_data[0])-1]['schedule_date'];
                            }
                        }
                        Comments::addSinglePayComment($customer_log_data);
                    }
		

		            
		//** checking if it is a 3 batch **//
                }elseif (count($getEstimateDetails) == 3) {
                         
                            for($i=0;$i<3;$i++){
                                $batch_data[$i]=BatchSchedule::where('batch_id','=',$getEstimateDetails[$i]['batch_id'])
                                                 ->where('schedule_date','>=',$getEstimateDetails[$i]['enroll_start_date'])
                                                 ->where('franchisee_id','=',Session::get('franchiseId'))
                                                 ->where('season_id','=',$getEstimateDetails[$i]['season_id'])
                                                 ->where('holiday','!=',1)  
                                                 ->take($getEstimateDetails[$i]['no_of_opted_classes'])
                                                 ->get();
                            }
                    
                    
                    
				//return Response::json(array('status'=>'vasu', $inputs));
                            for ($i=0; $i<3 ; $i++) { 
                                $studentClasses[$i]['classId']               = $getEstimateDetails[$i]['class_id'];
                                $studentClasses[$i]['batchId']               = $getEstimateDetails[$i]['batch_id'];
                                $studentClasses[$i]['studentId']             = $getEstimateDetails[$i]['student_id'];
                                $studentClasses[$i]['selected_sessions']     = $getEstimateDetails[$i]['no_of_opted_classes'];
                                $studentClasses[$i]['seasonId']              = $getEstimateDetails[$i]['season_id'];
                                $singleBatchstartDate=Carbon::createFromFormat('Y-m-d', $getEstimateDetails[$i]['enroll_start_date']);
                                $studentClasses[$i]['enrollment_start_date'] = $getEstimateDetails[$i]['enroll_start_date'];
                                $studentClasses[$i]['enrollment_end_date']   = $getEstimateDetails[$i]['enroll_end_date'];
                                $singleBatchendDate=Carbon::createFromFormat('Y-m-d', $getEstimateDetails[$i]['enroll_end_date']);
                                $insertDataToStudentClassTable               = StudentClasses::addStudentClass($studentClasses[$i]);
                  	
                                $paymentDuesInput[$i]['student_id']        = $insertDataToStudentClassTable['student_id'];
                                //return Response::json(array('status'=>'success','inputs'=>$paymentDuesInput));
                                $paymentDuesInput[$i]['customer_id']       = $inputs['customerId'];
                                $paymentDuesInput[$i]['batch_id']          = $insertDataToStudentClassTable['batch_id'];
                                $paymentDuesInput[$i]['class_id']          = $insertDataToStudentClassTable['class_id'];
                                $paymentDuesInput[$i]['selected_sessions'] = $insertDataToStudentClassTable['selected_sessions'];
                                $paymentDuesInput[$i]['seasonId']          = $insertDataToStudentClassTable['season_id'];
                                              
                                
                                $paymentDuesInput[$i]['student_class_id']  = $insertDataToStudentClassTable['id'];
                                $paymentDuesInput[$i]['each_class_cost']   = $getEstimateDetails[$i]['base_price'];
                                 
                                


                                if(isset($inputs['membershipType'])){
                                    //** create membership for customer **//
                                    $customerMembershipInput['customer_id']		=	$getEstimateDetails[$i]['customer_id'];
                                    $customerMembershipInput['membership_type_id']      =	$inputs['membershipType'];
                                    $customerMembershipDetails=CustomerMembership::addMembership($customerMembershipInput);

                                    $paymentDuesInput[$i]['membership_id']		=	$customerMembershipDetails->id;
                                    $paymentDuesInput[$i]['membership_type_id']         =	$customerMembershipDetails->membership_type_id;
                                    $paymentDuesInput[$i]['membership_amount']          =	$inputs['membershipAmount'];
                                }
                                //** checking for discounts **//
                                if($inputs['discountPercentage']!=''){
                                    $discount_amount                                    =   explode("-",$inputs['discountTextBox']);
                                    $paymentDuesInput[$i]['discount_amount']            =   $discount_amount[1];
                                    $paymentDuesInput[$i]['discount_applied']           =   $inputs['discountPercentage'];
                                }
                                if($inputs['second_class_discount_to_form']!=''){
                                    $discount_multipleclasses_amount                               = explode("-",$inputs['second_class_amount']);
                                    $paymentDuesInput[$i]['discount_multipleclasses_amount']       = $discount_multipleclasses_amount[1];
                                    $paymentDuesInput[$i]['discount_multipleclasses_applied']      = $inputs['second_class_discount_to_form'];
                                }
                                if($inputs['second_child_discount_to_form']!=''){
                                    $discount_sibling_amount                               = explode("-",$inputs['second_child_amount']);
                                    $paymentDuesInput[$i]['discount_sibling_amount']       = $discount_sibling_amount[1];
                                    $paymentDuesInput[$i]['discount_sibling_applied']      = $inputs['second_child_discount_to_form'];
                                }
                                
                               
                                
                                
                                $paymentDuesInput[$i]['payment_due_amount_after_discount']  = $inputs['grandTotal'];
                                $paymentDuesInput[$i]['payment_status']                     = "paid";
                                $paymentDuesInput[$i]['selected_order_sessions']            = $getEstimateDetails[$i]['no_of_opted_classes'];
                                $paymentDuesInput[$i]['start_order_date']                   = $insertDataToStudentClassTable['enrollment_start_date'];
                                $paymentDuesInput[$i]['end_order_date']                     = $insertDataToStudentClassTable['enrollment_end_date'];
                                $paymentDuesInput[$i]['payment_batch_amount']               = $paymentDuesInput[$i]['selected_order_sessions']*$paymentDuesInput[$i]['each_class_cost'];
                                $paymentDuesInput[$i]['payment_due_amount']                 = $inputs['singlePayAmount'];
                                
                                $sendPaymentDetailsToInsert = PaymentDues::createPaymentDues($paymentDuesInput[$i]);
                                
                                /* inserting into payment Due table is completed for single pay[2batch] */
                                /* Working on preparing to payment master table for single pay [2 batch]*/
                                if($i == 0){
                                    $sendPaymentMasterDetailsToInsert1          = PaymentMaster::createPaymentMaster($sendPaymentDetailsToInsert);
                                    //** updating back to payment_due **//
                                    $update_payment_due = PaymentDues::find($sendPaymentDetailsToInsert->id);
        		            $update_payment_due->payment_no=$sendPaymentMasterDetailsToInsert1->payment_no;
                		    $update_payment_due->save();


                                }else{
                                	$sendPaymentMasterDetailsToInsert2 = PaymentMaster::createPaymentMasterWithSamePaymentNo($sendPaymentDetailsToInsert, $sendPaymentMasterDetailsToInsert1->payment_no);
                                	//** updating back to paymentdues **//
                                	$update_payment_due1 = PaymentDues::find($sendPaymentDetailsToInsert->id);
                                        $update_payment_due1->payment_no=$sendPaymentMasterDetailsToInsert2->payment_no;//$sendPaymentMasterDetailsToInsert2->payment_no;
                                        $final_payment_master_no=$sendPaymentMasterDetailsToInsert2->payment_no;
                                        $update_payment_due1->save();
                                         
                                            //**updating payment_no to order table **// 
                                            $updateOrderNoInMaster = PaymentMaster::find($sendPaymentMasterDetailsToInsert1->id);
                                            $updateOrderNoInMaster->order_id = $sendOrderDetailsToInsert->id;
                                            $updateOrderNoInMaster->save();
                                         
                                         
                                        //**  updating payment master the order_table **//
                                        $updateOrderNoInMaster1 = PaymentMaster::find($sendPaymentMasterDetailsToInsert2->id);
                                        $updateOrderNoInMaster1->order_id = $sendOrderDetailsToInsert->id;
                		        $updateOrderNoInMaster1->save();

                                }
                                /* inserting into payment Master table is completed for single pay */
                                /* Working on preparing to Orsers table for single pay */
                                if($i == 0){
                                		$order['customer_id']     = $inputs['customerId'];
                                		$order['student_id']      = $insertDataToStudentClassTable['student_id'];
                                		$order['seasonId']        = $insertDataToStudentClassTable['season_id'];
                                		$order['student_classes_id'] = $insertDataToStudentClassTable['id'];
                                		//$order[$i]['payment_mode']    = $inputs['paymentTypeRadio'];

                                		if($inputs['paymentTypeRadio'] == "card"){
                                    		$order['payment_mode']    = $inputs['paymentTypeRadio'];	
                                    		$order['card_last_digit'] = $inputs['card4digits'];
                                    		$order['card_type']       = $inputs['cardType'];
                                    		$order['bank_name']       = $inputs['cardBankName'];
                                    		$order['receipt_number']  = $inputs['cardRecieptNumber'];

                                		}elseif($inputs['paymentTypeRadio'] == "cheque"){
                                    		$order['payment_mode']    = $inputs['paymentTypeRadio'];	
                                    		$order['bank_name']       = $inputs['bankName'];
                                    		$order['cheque_number']   = $inputs['chequeNumber'];

		                                }elseif($inputs['paymentTypeRadio'] == "cash"){
        		                            $order['payment_mode']    = $inputs['paymentTypeRadio'];	
                		                }


                        		        $order['payment_for']     = "enrollment";
                                		$order['payment_no']   = $sendPaymentMasterDetailsToInsert1['payment_no'];
		                                $order['payment_dues_id']   = $sendPaymentDetailsToInsert['id'];
        		                        $order['amount'] = $inputs['singlePayAmount'];
                		                $order['order_status'] = "completed";
                        		        

		                                $sendOrderDetailsToInsert = Orders::createOrder($order);

		                        }
		                        
                                                            
			}
			//** working on the payment_followups **//
                    
                    if((count($batch_data[0]) + count($batch_data[1])+ count($batch_data[2])) >= 5){
                        $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($sendPaymentDetailsToInsert,$final_payment_master_no);
                        //creating logs/followup for first payment
                        $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
                        $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
                        $customer_log_data['franchisee_id']=Session::get('franchiseId');
                        $customer_log_data['paymentfollowup_id']=$payment_followup_data1->id;
                        $customer_log_data['followup_type']='PAYMENT';
                        $customer_log_data['followup_status']='REMINDER_CALL';
                        $customer_log_data['comment_type']='VERYINTERESTED';
                        
                        $customer_log_data['reminderDate']=$batch_data[2][count($batch_data[2])-2]['schedule_date'];
                        if(count($batch_data[2])>=3){
                        $customer_log_data['reminderDate']=$batch_data[2][count($batch_data[2])-2]['schedule_date'];
                        }else{
                            if(count($batch_data[2])==2){
                                 $customer_log_data['reminderDate']=$batch_data[1][count($batch_data[0])-1]['schedule_date'];
                            }
                        }
                        
                        Comments::addSinglePayComment($customer_log_data);
                    }
                    
                        
		}
                $customer_data= Customers::find($inputs['customerId']);
                
               // return Response::json(array("status"=>"success",'printUrl'=>''));
                
                if(isset($inputs['emailOption']) && $inputs['emailOption'] == 'yes' && $customer_data->customer_email!=''){
                    $totalSelectedClasses = '';
                    $totalAmountForAllBatch = '';
                    $paymentDueDetails = PaymentDues::where('payment_no', '=', $final_payment_master_no)->get();
                    for($i = 0; $i < count($paymentDueDetails); $i++){
			$totalSelectedClasses = $totalSelectedClasses + $paymentDueDetails[$i]['selected_sessions'];
			$getBatchNname[]  = Batches::where('id', '=', $paymentDueDetails[$i]['batch_id'])->get();
			$getSeasonName[]  = Seasons::where('id', '=', $paymentDueDetails[$i]['season_id'])->get();
			$selectedSessionsInEachBatch[] = $paymentDueDetails[$i]['selected_sessions'];
			$classStartDate[] = $paymentDueDetails[$i]['start_order_date'];
			$classEndDate[] = $paymentDueDetails[$i]['end_order_date'];
			$totalAmountForEachBach[] = (int)$paymentDueDetails[$i]['payment_batch_amount'];
			$totalAmountForAllBatch = $totalAmountForAllBatch + (int)$paymentDueDetails[$i]['payment_batch_amount'];
                    }   
                    $getTermsAndConditions = TermsAndConditions::where('id', '=', (TermsAndConditions::max('id')))->get();
                    $getCustomerName = Customers::select('customer_name','customer_email')->where('id', '=', $paymentDueDetails[0]['customer_id'])->get();
                    //return Response::json(array($getCustomerName));
                    $getStudentName = Students::select('student_name')->where('id', '=', $paymentDueDetails[0]['student_id'])->get();
                    $paymentMode = Orders::where('payment_no', '=', $final_payment_master_no)->get();
                    $data = compact('totalSelectedClasses', 'getBatchNname',
                        'getSeasonName', 'selectedSessionsInEachBatch', 'classStartDate',
                        'classEndDate', 'totalAmountForEachBach', 'getCustomerName', 'getStudentName','getTermsAndConditions',
                        'paymentDueDetails', 'totalAmountForAllBatch', 'paymentMode');
                    Mail::send('emails.account.enrollment', $data, function($msg) use ($data){
					
				$msg->from(Config::get('constants.EMAIL_ID'), Config::get('constants.EMAIL_NAME'));
				$msg->to($data['getCustomerName'][0]['customer_email'], $data['getCustomerName'][0]['customer_name'])->subject('The Little Gym - Kids Enrollment Successful');
			
			});
                }
                
                if(isset($inputs['invoicePrintOption']) && $inputs['invoicePrintOption'] == 'yes'){
                    
                    
			$printUrl = url().'/orders/print/'.Crypt::encrypt($final_payment_master_no);
		}else{
			$printUrl = "";
		}
		
                
                if($final_payment_master_no){
			return Response::json(array("status"=>"success", "printUrl"=>$printUrl));
		}else{
			return Response::json(array("status"=>"failed"));
            	}
	}
        
        public function enrollKid(){
		
		$inputs = Input::all();
                
                $batch_data=  Batches::find($inputs['batchCbx']);
                $eachClassCost=$batch_data->class_amount;
                
                //return Response::json(array('status'=>$inputs));
		//$inputs['discountPercentage']=$inputs['discountP'];
		$studentClasses['classId']               = $inputs['eligibleClassesCbx'];
		$studentClasses['batchId']               = $inputs['batchCbx'];
		$studentClasses['studentId']             = $inputs['studentId'];
                $season_data=Seasons::find( $inputs['SeasonsCbx']);
                //$inputs['enrollmentEndDate']=$season_data->end_date;
		//$studentClasses['enrollment_start_date'] = date('Y-m-d', strtotime($inputs['enrollmentStartDate']));
		//return Response::json(array('status'=>'working'));
                //$studentClasses['enrollment_end_date']   = date('Y-m-d', strtotime($inputs['enrollmentEndDate']));
		$studentClasses['selected_sessions']     = $inputs['selectedSessions'];
		$studentClasses['seasonId']             =$inputs['SeasonsCbx'];
                
                //for proper enrollment start date and end date
                
                
                        $end=Carbon::now();
                        $start=Carbon::now();
                        $start->year=date('Y', strtotime($inputs['enrollmentStartDate']));
                        $start->month=date('m', strtotime($inputs['enrollmentStartDate']));
                        $start->day=date('d', strtotime($inputs['enrollmentStartDate']));
                        $end->year=date('Y', strtotime($inputs['enrollmentEndDate']));
                        $end->month=date('m', strtotime($inputs['enrollmentEndDate']));
                        $end->day=date('d', strtotime($inputs['enrollmentEndDate']));
                        
                        $batch_data=  BatchSchedule::where('batch_id','=',$inputs['batchCbx'])
                                                              ->where('schedule_date','>=',$start->toDateString())
                                                              ->where('schedule_date','<=',$end->toDateString())
                                                              ->where('holiday','!=',1)  
                                                              ->orderBy('id')
                                                              ->get();
                        
                        $studentClasses['enrollment_start_date'] =$batch_data[0]['schedule_date'];
                        $studentClasses['enrollment_end_date']=$batch_data[(count($batch_data)-1)]['schedule_date'];
			
                        
                
                
                
                
                
		//Batch Start date
		
		$BatchDetails = Batches::where('id', '=', $inputs['batchCbx'])->first();
		$reminderStartDate = $BatchDetails->start_date;
		
		
		
		$enrollment = StudentClasses::addStudentClass($studentClasses);
		
		
		$paymentDuesInput['student_id']        = $inputs['studentId'];
		$paymentDuesInput['customer_id']       = $inputs['customerId'];
		$paymentDuesInput['batch_id']          = $inputs['batchCbx'];
		$paymentDuesInput['class_id']          = $inputs['eligibleClassesCbx'];
		$paymentDuesInput['selected_sessions'] = $inputs['selectedSessions'];
		$paymentDuesInput['seasonId']          = $inputs['SeasonsCbx'];
                $paymentDuesInput['each_class_cost']   =$eachClassCost;
		
		
		$order['customer_id']     = $inputs['customerId'];
		$order['student_id']      = $inputs['studentId'];
		$order['seasonId']        = $inputs['SeasonsCbx'];
		$order['student_classes_id'] = $enrollment->id;
		$order['payment_mode']    = $inputs['paymentTypeRadio'];
		$order['payment_for']     = "enrollment";
		$order['card_last_digit'] = $inputs['card4digits'];
		$order['card_type']       = $inputs['cardType'];
		$order['bank_name']       = $inputs['bankName'];
		$order['cheque_number']   = $inputs['chequeNumber'];
                //$order['each_class_cost']   =$eachClassCost;
		if(isset($inputs['membershipType'])){
			$order['membershipType'] = $inputs['membershipType'];
		}
		$paydue_id;
		if($inputs['paymentOptionsRadio'] == 'singlepay'){
			// for starting and end date of enrollment
                        $enddate=Carbon::now();
                        $startdate=Carbon::now();
                        $startdate->year=date('Y', strtotime($inputs['enrollmentStartDate']));
                        $startdate->month=date('m', strtotime($inputs['enrollmentStartDate']));
                        $startdate->day=date('d', strtotime($inputs['enrollmentStartDate']));
                        $enddate->year=date('Y', strtotime($inputs['enrollmentEndDate']));
                        $enddate->month=date('m', strtotime($inputs['enrollmentEndDate']));
                        $enddate->day=date('d', strtotime($inputs['enrollmentEndDate']));
                        
                        $batch_data=  BatchSchedule::where('batch_id','=',$inputs['batchCbx'])
                                                              ->where('schedule_date','>=',$startdate->toDateString())
                                                              ->where('schedule_date','<=',$enddate->toDateString())
                                                              ->where('holiday','!=',1)  
                                                              ->orderBy('id')
                                                              ->get();
                        
                        $paymentDuesInput['start_order_date']=$batch_data[0]['schedule_date'];
                        $paymentDuesInput['end_order_date']=$batch_data[(count($batch_data)-1)]['schedule_date'];
			$paymentDuesInput['payment_due_amount']  = $inputs['singlePayAmount'];
			$paymentDuesInput['payment_type']        = $inputs['paymentOptionsRadio'];
			$paymentDuesInput['payment_status']      = "paid";
			$paymentDuesInput['discount_applied']    = $inputs['discountPercentage'];
                        $paymentDuesInput['student_class_id']   =  $enrollment->id;
                        $paymentDuesInput['selected_order_sessions']=$inputs['selectedSessions'];
			//$paymentDuesInput['start_order_date']=$studentClasses['enrollment_start_date'];
			//$paymentDuesInput['end_order_date']=$studentClasses['enrollment_end_date'];
			$paymentDuesInput['discount_amount']=((($inputs['discountPercentage'])/100) *($inputs['singlePayAmount']));
                                                
                        $order['amount']   = $inputs['singlePayAmount'];
			if($inputs['CustomerType']=='OldCustomer'){
                            $paymentDuesInput['created_at']=date('Y-m-d H:i:s', strtotime($inputs['OrderDate']));
                            $order['created_at']=$paymentDuesInput['created_at'];
                            
                        }
                        
			$paymentDuesResult = PaymentDues::createPaymentDues($paymentDuesInput);
			$order['payment_dues_id']   = $paymentDuesResult->id;
			$order['order_status']      = "completed";
			
                        
                        if($inputs['selectedSessions']>8){
			// creating followup for the single pay
                        
                        $presentDate=Carbon::now();
                        $startdate=Carbon::now();
                        $startdate->year=date('Y', strtotime($inputs['enrollmentStartDate']));
                        $startdate->month=date('m', strtotime($inputs['enrollmentStartDate']));
                        $startdate->day=date('d', strtotime($inputs['enrollmentStartDate']));
                        $batch_schedule_data=  BatchSchedule::where('batch_id','=',$inputs['batchCbx'])
                                                              ->where('schedule_date','>=',$startdate->toDateString())
                                                              ->where('holiday','!=',1)  
                                                              ->orderBy('id')
                                                              ->get();
                        $session_number=(int)($inputs['selectedSessions']/2);
                        $reminder_date=$batch_schedule_data[$session_number]['schedule_date'];
                        
                        
                            $customer_log_data['customer_id']=$paymentDuesResult->customer_id;
                            $customer_log_data['student_id']=$paymentDuesResult->student_id;
                            $customer_log_data['franchisee_id']=Session::get('franchiseId');
                            $customer_log_data['followup_type']='PAYMENT';
                            $customer_log_data['followup_status']='REMINDER_CALL';
                            $customer_log_data['comment_type']='VERYINTERESTED';
                            $customer_log_data['reminderDate']=$reminder_date;
                            $remindDate=Carbon::now();
                            $remindDate=$remindDate->createFromFormat('Y-m-d',$reminder_date);
                            if($remindDate->gt($presentDate)){
                                $payment_followup_data=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                $customer_log_data['paymentfollowup_id']=$payment_followup_data->id;
                            
                                Comments::addSinglePayComment($customer_log_data);
                                
                            }
                        }
				
				
		}else if($inputs['paymentOptionsRadio'] == 'multipay'){
			$today=Carbon::now();
                        
			$paymentDuesInput['payment_type']        = $inputs['paymentOptionsRadio'];
                         for($i = 1; $i<=4; $i++){
                            
				if(isset($inputs['multipayAmount'.$i])){
                                        $firstBrushupCallReminderDate;
                                        $firstInitialPaymentCallReminderDate;
                                        $firstFinalPaymentCallReminderDate;
                                        
					if($i ==1){
						$paymentDuesInput['payment_status']      = "paid";
						$order['amount']   =  $inputs['multipayAmount'.$i];
                                                $enddate=Carbon::now();
                                                $startdate=Carbon::now();
                                                $startdate->year=date('Y', strtotime($inputs['enrollmentStartDate']));
                                                $startdate->month=date('m', strtotime($inputs['enrollmentStartDate']));
                                                $startdate->day=date('d', strtotime($inputs['enrollmentStartDate']));
                                                $enddate->year=date('Y', strtotime($inputs['enrollmentEndDate']));
                                                $enddate->month=date('m', strtotime($inputs['enrollmentEndDate']));
                                                $enddate->day=date('d', strtotime($inputs['enrollmentEndDate']));
                        
                                                  $batch_data=  BatchSchedule::where('batch_id','=',$inputs['batchCbx'])
                                                              ->where('schedule_date','>=',$startdate->toDateString())
                                                              ->where('schedule_date','<=',$enddate->toDateString())
                                                              ->where('holiday','!=',1)  
                                                              ->orderBy('id')
                                                              ->get();
                        
                                                $paymentDuesInput['start_order_date']=$batch_data[0]['schedule_date'];
                                                $endorderdate=$batch_data[(count($batch_data)-1)]['schedule_date'];
                        
                                                //$paymentDuesInput['start_order_date']=$studentClasses['enrollment_start_date'];
                                                //$startdate=  Carbon::create(date('Y,m,d,0', strtotime($inputs['enrollmentStartDate'])));
                                                $startdate=Carbon::now();
                                                $startdate->year=date('Y', strtotime($inputs['enrollmentStartDate']));
                                                $startdate->month=date('m', strtotime($inputs['enrollmentStartDate']));
                                                $startdate->day=date('d', strtotime($inputs['enrollmentStartDate']));
                                                $session_no=($inputs['multipayAmount'.$i]/$eachClassCost);
                                                $firstSessionNumber=$session_no;
                                                
                                                $batch_schedule_data=  BatchSchedule::where('batch_id','=',$inputs['batchCbx'])
                                                                                    //where('franchisee_id','=',Session::get('franchiseId'))
                                                                                   
                                                                                    //->where('season_id','=',$inputs['SeasonsCbx'])
                                                                                   ->where('schedule_date','>=',$startdate->toDateString())
                                                                                   ->where('holiday','!=',1)  
                                                                                   ->orderBy('id')
                                                                                   ->get();
                                                $session_no=$session_no-1;
                                                //$startdate=$startdate->addWeeks(($inputs['bipayAmount'.$i]/500));
                                                
                                                //$startdate=$startdate->addWeeks(($inputs['multipayAmount'.$i]/500));
                                                $paymentDuesInput['end_order_date']=$batch_schedule_data[$session_no]['schedule_date'];
                                                if($firstSessionNumber>=6){
                                                $firstBrushupCallReminderDate=$batch_schedule_data[($session_no-2)]['schedule_date'];
                                                $firstInitialPaymentCallReminderDate=$batch_schedule_data[($session_no-1)]['schedule_date'];
                                                $firstFinalPaymentCallReminderDate=$batch_schedule_data[$session_no]['schedule_date'];
                                                }else{
                                                    $firstFinalPaymentCallReminderDate=$batch_schedule_data[$session_no]['schedule_date'];
                                                }
                                                
                                                $nextstartdate=$batch_schedule_data[$session_no]['schedule_date'];
                                                
                                                //$paymentDuesInput['end_order_date']=$startdate->toDateString();
                                                $paymentDuesInput['discount_amount']=((($inputs['discountPercentage'])/100) *($inputs['multipayAmount'.$i]));
                                                
                                                
                                                if($inputs['CustomerType']=='OldCustomer'){
                                                     $paymentDuesInput['created_at']=date('Y-m-d H:i:s', strtotime($inputs['OrderDate']));
                                                     $order['created_at']=$paymentDuesInput['created_at'];
                            
                                                }
                                                
                                                }else{
                                                $paymentDuesInput['start_order_date']=   $nextstartdate;
						$batch_schedule_data=  BatchSchedule::where('batch_id','=',$inputs['batchCbx'])
                                                                                    //where('franchisee_id','=',Session::get('franchiseId'))
                                                                                   
                                                                                    //->where('season_id','=',$inputs['SeasonsCbx'])
                                                                                   ->where('schedule_date','>=',$startdate->toDateString())
                                                                                   ->where('holiday','!=',1)  
                                                                                   ->orderBy('id')
                                                                                   ->get();
                                                $session_no=$session_no+($inputs['multipayAmount'.$i]/$eachClassCost);
                                                $firstBrushupCallReminderDate=$batch_schedule_data[($session_no-2)]['schedule_date'];
                                                $firstInitialPaymentCallReminderDate=$batch_schedule_data[($session_no-1)]['schedule_date'];
                                                $firstFinalPaymentCallReminderDate=$batch_schedule_data[$session_no]['schedule_date'];
                                                
                                                //$startdate=$startdate->addWeeks(($inputs['multipayAmount'.$i]/500));
                                                $paymentDuesInput['end_order_date']=$batch_schedule_data[$session_no]['schedule_date'];
                                                if($i==4){
                                                    //$paymentDuesInput['end_order_date']=date('Y-m-d', strtotime($inputs['enrollmentEndDate']));
                                                        $paymentDuesInput['end_order_date']=$endorderdate;
                                                }
                                                $nextstartdate=$batch_schedule_data[$session_no]['schedule_date'];
                                                //$paymentDuesInput['end_order_date']=   $startdate->toDateString();
                                                $paymentDuesInput['payment_status']      = "pending";
                                                $paymentDuesInput['discount_amount']=((($inputs['discountPercentage'])/100) *($inputs['multipayAmount'.$i]));
                                                if(($i==2)&&($inputs['CustomerType']=='OldCustomer')&& ($inputs['OrderDate2']!='')){
                                                    $paymentDuesInput['created_at']=date('Y-m-d H:i:s', strtotime($inputs['OrderDate2']));
                                                    $paymentDuesInput['payment_status']="paid";
                                                   
                                                    
                                                }
                                                if(($i==3)&&($inputs['CustomerType']=='OldCustomer')&& ($inputs['OrderDate3']!='')){
                                                    $paymentDuesInput['created_at']=date('Y-m-d H:i:s', strtotime($inputs['OrderDate3']));
                                                    $paymentDuesInput['payment_status']="paid";
                                                    
                                                    
                                                }
                                                if(($i==4)&&($inputs['CustomerType']=='OldCustomer')&& ($inputs['OrderDate4']!='')){
                                                    $paymentDuesInput['created_at']=date('Y-m-d H:i:s', strtotime($inputs['OrderDate4']));
                                                    $paymentDuesInput['payment_status']="paid";
                                                    
                                                    
                                                }
                                                
                                                
					}
                                        
					$paymentDuesInput['payment_due_amount']  = $inputs['multipayAmount'.$i];
					$paymentDuesInput['discount_applied']    = $inputs['discountPercentage'];
                                        $paymentDuesInput['selected_order_sessions']=($inputs['multipayAmount'.$i]/$eachClassCost);
                                        $paymentDuesInput['student_class_id']   =  $enrollment->id;
					
                                        
                                       $paymentDuesResult = PaymentDues::createPaymentDues($paymentDuesInput);
					         
                                        if($i>=1 && $i<=3){
                                            if($i==1){
                                                    
                                                if($firstSessionNumber>=6){
                                                    
                                                    $firstremind=Carbon::now();
                                                    $firstremind=$firstremind->createFromFormat('Y-m-d',$firstBrushupCallReminderDate);
                                                    //return Response::json(array('status'=>$firstBrushupCallReminderDate,'today'=>$today));
                                                    if($firstremind->gt($today)){
                                                    //create 3 payment followup
                                                    $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                    $payment_followup_data2=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                    $payment_followup_data3=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                    //creating logs/followup for first payment
                                        
                                                    $customer_log_data['customer_id']=$paymentDuesResult->customer_id;
                                                    $customer_log_data['student_id']=$paymentDuesResult->student_id;
                                                    $customer_log_data['franchisee_id']=Session::get('franchiseId');
                                                    $customer_log_data['paymentfollowup_id']=$payment_followup_data1->id;
                                                    $customer_log_data['paymentfollowup_id2']=$payment_followup_data2->id;
                                                    $customer_log_data['paymentfollowup_id3']=$payment_followup_data3->id;
                                                    $customer_log_data['followup_type']='PAYMENT';
                                                    $customer_log_data['followup_status']='REMINDER_CALL';
                                                    $customer_log_data['comment_type']='VERYINTERESTED';
                                                    $customer_log_data['firstReminderDate']=$firstBrushupCallReminderDate;
                                                    $customer_log_data['secondReminderDate']=$firstInitialPaymentCallReminderDate;
                                                    $customer_log_data['thirdReminderDate']=$firstFinalPaymentCallReminderDate;
                                                    Comments::addPaymentComments($customer_log_data);
                                                    }else{
                                                    $final_remind=Carbon::now();
                                                    $final_remind=$final_remind->createFromFormat('Y-m-d',$firstFinalPaymentCallReminderDate);
                                                      if($final_remind->gt($today)){
                                                            $payment_followup_data=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                            $customer_log_data['customer_id']=$paymentDuesResult->customer_id;
                                                             $customer_log_data['student_id']=$paymentDuesResult->student_id;
                                                            $customer_log_data['franchisee_id']=Session::get('franchiseId');
                                                            $customer_log_data['paymentfollowup_id']=$payment_followup_data->id;
                                                             //$customer_log_data['paymentfollowup_id2']=$payment_followup_data2->id;
                                                             //$customer_log_data['paymentfollowup_id3']=$payment_followup_data3->id;
                                                            $customer_log_data['followup_type']='PAYMENT';
                                                            $customer_log_data['followup_status']='REMINDER_CALL';
                                                             $customer_log_data['comment_type']='VERYINTERESTED';
                                                              //  $customer_log_data['firstReminderDate']=$firstBrushupCallReminderDate;
                                                             //  $customer_log_data['secondReminderDate']=$firstInitialPaymentCallReminderDate;
                                                            $customer_log_data['reminderDate']=$firstFinalPaymentCallReminderDate;
                                                            Comments::addOnebiPaymentComment($customer_log_data);
                                                        }
                                                    
                                                      }
                                                }else{
                                                    $final_r=Carbon::now();
                                                    $final_r=$final_r->createFromFormat('Y-m-d',$firstFinalPaymentCallReminderDate);
                                                      if($final_r->gt($today)){
                                                          
                                                        //create 1 followup
                                                        $payment_followup_data=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                   
                                                        $customer_log_data['customer_id']=$paymentDuesResult->customer_id;
                                                        $customer_log_data['student_id']=$paymentDuesResult->student_id;
                                                        $customer_log_data['franchisee_id']=Session::get('franchiseId');
                                                        $customer_log_data['paymentfollowup_id']=$payment_followup_data->id;
                                                        //$customer_log_data['paymentfollowup_id2']=$payment_followup_data2->id;
                                                        //$customer_log_data['paymentfollowup_id3']=$payment_followup_data3->id;
                                                        $customer_log_data['followup_type']='PAYMENT';
                                                        $customer_log_data['followup_status']='REMINDER_CALL';
                                                        $customer_log_data['comment_type']='VERYINTERESTED';
                                                        //  $customer_log_data['firstReminderDate']=$firstBrushupCallReminderDate;
                                                        //  $customer_log_data['secondReminderDate']=$firstInitialPaymentCallReminderDate;
                                                        $customer_log_data['reminderDate']=$firstFinalPaymentCallReminderDate;
                                                        Comments::addOnebiPaymentComment($customer_log_data);
                                                      }
                                                }
                                            }else{ //when i>=3
                                                
                                                $firstre=Carbon::now();
                                                $firstre=$firstre->createFromFormat('Y-m-d',$firstBrushupCallReminderDate);
                                                if($firstre->gt($today)){
                                               
                                        
                                                $customer_log_data['customer_id']=$paymentDuesResult->customer_id;
                                                $customer_log_data['student_id']=$paymentDuesResult->student_id;
                                                $customer_log_data['franchisee_id']=Session::get('franchiseId');
                                                
                                                $customer_log_data['followup_type']='PAYMENT';
                                                $customer_log_data['followup_status']='REMINDER_CALL';
                                                $customer_log_data['comment_type']='VERYINTERESTED';
                                                $customer_log_data['firstReminderDate']=$firstBrushupCallReminderDate;
                                                $customer_log_data['secondReminderDate']=$firstInitialPaymentCallReminderDate;
                                                $customer_log_data['thirdReminderDate']=$firstFinalPaymentCallReminderDate;
                                                    if($i==2 && isset($inputs['multipayAmount3'])){
                                                         $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $payment_followup_data2=  PaymentFollowup