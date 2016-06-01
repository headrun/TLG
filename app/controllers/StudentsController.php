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
		if($sendDetails){
			return Response::json(array('status'=> "success", 'data'=> $sendDetails));
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
                
                if(isset($inputs['emailOptionforoldcustomer']) && ($inputs['emailOptionforoldcustomer']=='yes')){
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
               // return Response::json(array("status"=>"success",'printUrl'=>''));
                
                if(isset($inputs['emailOption']) && $inputs['emailOption'] == 'yes'){
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
                                                         $payment_followup_data2=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $payment_followup_data3=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $customer_log_data['paymentfollowup_id']=$payment_followup_data1->id;
                                                         $customer_log_data['paymentfollowup_id2']=$payment_followup_data2->id;
                                                         $customer_log_data['paymentfollowup_id3']=$payment_followup_data3->id;
                                                
                                                    Comments::addPaymentComments($customer_log_data);
                                                    }else
                                                    if($i==3 && isset($inputs['multipayAmount4'])){
                                                         $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $payment_followup_data2=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $payment_followup_data3=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $customer_log_data['paymentfollowup_id']=$payment_followup_data1->id;
                                                         $customer_log_data['paymentfollowup_id2']=$payment_followup_data2->id;
                                                         $customer_log_data['paymentfollowup_id3']=$payment_followup_data3->id;
                                                
                                                    Comments::addPaymentComments($customer_log_data);
                                                    }else if($i!=3 && $i!=2){
                                                         $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $payment_followup_data2=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $payment_followup_data3=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                         $customer_log_data['paymentfollowup_id']=$payment_followup_data1->id;
                                                         $customer_log_data['paymentfollowup_id2']=$payment_followup_data2->id;
                                                         $customer_log_data['paymentfollowup_id3']=$payment_followup_data3->id;
                                                    Comments::addPaymentComments($customer_log_data);
                                                    }
                                                }else{
                                                    
                                                    $final=Carbon::now();
                                                    $final=$final->createFromFormat('Y-m-d',$firstFinalPaymentCallReminderDate);
                                                      if($final->gt($today)){
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
                                            }
                                        }
                                        $paydue_id[]=$paymentDuesResult->id;
					

						
					if($i ==1){
		
						$order['payment_dues_id']   = $paymentDuesResult->id;
						$order['order_status']      = "completed";
					}
				}
		
			}
			
                        /*
			$weeksDues = array("6", "9", "14", "19", "24", "29", "34");
			$date = date('Y/m/d', strtotime($reminderStartDate));
			foreach($weeksDues as $weeksDue){
					
				$weeks = $weeksDue;
				// Create and modify the date.
				$dateTime = DateTime::createFromFormat('Y/m/d', $date);
				$dateTime->add(DateInterval::createFromDateString($weeks . ' weeks'));
				//$dateTime->modify('next monday');
					
				// Output the new date.
				//echo $dateTime->format('Y-m-d')."<br>";
			
			
				$paymentReminderInput['customerId']    = $inputs['customerId'];
				$paymentReminderInput['studentId']     = $inputs['studentId'];
                                $paymentReminderInput['seasonId']     = $inputs['SeasonsCbx'];
				$paymentReminderInput['classId']       = $inputs['eligibleClassesCbx'];
				$paymentReminderInput['batchId']       = $inputs['batchCbx'];
				$paymentReminderInput['reminder_date'] = $dateTime->format('Y-m-d');
			
				PaymentReminders::addReminderDates($paymentReminderInput);
			
			
					
			}*/
			
			
				
		}else if($inputs['paymentOptionsRadio'] == 'bipay'){
			$followupFirstDate;
                        $followupSecondDate;
                        $followupThirdDate;
                        
			$paymentDuesInput['payment_type']        = $inputs['paymentOptionsRadio'];
			for($i = 1; $i<=2; $i++){
					
				if(isset($inputs['bipayAmount'.$i])){
						
					if($i ==1){
                                                //for bipay enrollment classes
                                                 
						$paymentDuesInput['payment_status']      = "paid";
						$order['amount']   =  $inputs['bipayAmount'.$i];
                                                //$paymentDuesInput['start_order_date']=$studentClasses['enrollment_start_date'];
                                                //$startdate=  Carbon::create(date('Y,m,d,0', strtotime($inputs['enrollmentStartDate'])));
                                                $startdate=Carbon::now();
                                                $enddate=Carbon::now();
                                                $startdate->year=date('Y', strtotime($inputs['enrollmentStartDate']));
                                                $startdate->month=date('m', strtotime($inputs['enrollmentStartDate']));
                                                $startdate->day=date('d', strtotime($inputs['enrollmentStartDate']));
                                                $enddate->year=date('Y', strtotime($inputs['enrollmentEndDate']));
                                                $enddate->month=date('m', strtotime($inputs['enrollmentEndDate']));
                                                $enddate->day=date('d', strtotime($inputs['enrollmentEndDate']));
                                                //return Response::json(array('status'=>'success'));
                                                $session_no=($inputs['bipayAmount'.$i]/$eachClassCost);
                                                
                                                $firstsessionno=$session_no;
                                                $batch_schedule_data=  BatchSchedule::where('batch_id','=',$inputs['batchCbx'])
                                                                                    //where('franchisee_id','=',Session::get('franchiseId'))
                                                                                   
                                                                                    //->where('season_id','=',$inputs['SeasonsCbx'])
                                                                                   ->where('schedule_date','>=',$startdate->toDateString())
                                                                                   ->where('schedule_date','<=',$enddate->toDateString())
                                                                                   ->where('holiday','!=',1)  
                                                                                   ->orderBy('id')
                                                                                   ->get();
                                                $session_no=$session_no-1;
                                                //$startdate=$startdate->addWeeks(($inputs['bipayAmount'.$i]/500));
                                                $paymentDuesInput['start_order_date']=$batch_schedule_data[0]['schedule_date'];
                                                $paymentDuesInput['end_order_date']=$batch_schedule_data[$session_no]['schedule_date'];
                                                $endorderdateforbipay=$batch_schedule_data[(count($batch_schedule_data)-1)]['schedule_date'];
                                                if($firstsessionno>=10){
                                                $followupFirstDate=$batch_schedule_data[($session_no-2)]['schedule_date'];
                                                $followupSecondDate=$batch_schedule_data[($session_no-1)]['schedule_date'];
                                                $followupThirdDate=$batch_schedule_data[$session_no]['schedule_date'];
                                                }else{
                                                $followupdate=$batch_schedule_data[$session_no]['schedule_date'];
                                                }
                                                $paymentDuesInput['discount_amount']=((($inputs['discountPercentage'])/100) *($inputs['bipayAmount'.$i]));
                                               
                                                if($inputs['CustomerType']=='OldCustomer'){
                                                       $paymentDuesInput['created_at']=date('Y-m-d H:i:s', strtotime($inputs['OrderDate']));
                                                       $order['created_at']=$paymentDuesInput['created_at'];
                            
                                                    }
                                                
					}else{
                                                $paymentDuesInput['start_order_date']=   $batch_schedule_data[$session_no]['schedule_date'];
                                                
						//$startdate=$startdate->addWeeks(($inputs['bipayAmount'.$i]/500));
                                                //$paymentDuesInput['end_order_date']=   date('Y-m-d', strtotime($inputs['enrollmentEndDate']));
						$paymentDuesInput['end_order_date']=$endorderdateforbipay;
                                                $paymentDuesInput['payment_status']      = "pending";
                                                $paymentDuesInput['discount_amount']=((($inputs['discountPercentage'])/100) *($inputs['bipayAmount'.$i]));
                                        
                                                if(($inputs['CustomerType']=='OldCustomer')&& ($inputs['OrderDate2']!='')){
                                                       $paymentDuesInput['created_at']=date('Y-m-d H:i:s', strtotime($inputs['OrderDate2']));
                                                      // $order['created_at']=$paymentDuesInput['created_at'];
                            
                                                    }
                                                
                                        }
                                        $paymentDuesInput['selected_order_sessions']=($inputs['bipayAmount'.$i]/$eachClassCost);
					$paymentDuesInput['payment_due_amount']  = $inputs['bipayAmount'.$i];
					$paymentDuesInput['discount_applied']    = $inputs['discountPercentage'];
					$paymentDuesInput['student_class_id']   =  $enrollment->id;	
					$paymentDuesResult = PaymentDues::createPaymentDues($paymentDuesInput);
					
					if($i ==1){
						$order['payment_dues_id']   = $paymentDuesResult->id;
						$order['order_status']      = "completed";
                                                $paydueid1=$paymentDuesResult->id;
                                                
					}
                                        if($i==2){
                                            if($firstsessionno>=10){
                                               
                                                // creating customer followup
                                                $customer_log_data['customer_id']=$paymentDuesResult->customer_id;
                                                $customer_log_data['student_id']=$paymentDuesResult->student_id;
                                                $customer_log_data['franchisee_id']=Session::get('franchiseId');
                                                $paydue_id2=$paymentDuesResult->id;
                                                $paymentDuesResult->id=$paydueid1;
                                                $customer_log_data['followup_type']='PAYMENT';
                                                $customer_log_data['followup_status']='REMINDER_CALL';
                                                $customer_log_data['comment_type']='VERYINTERESTED';
                                                $customer_log_data['firstReminderDate']=$followupFirstDate;
                                                $customer_log_data['secondReminderDate']=$followupSecondDate;
                                                $customer_log_data['thirdReminderDate']=$followupThirdDate;
                                                
                                                $brushupremind=Carbon::now();
                                                $finalremind=Carbon::now();
                                                
                                                $brushupremind=$brushupremind->createFromFormat('Y-m-d',$followupFirstDate);
                                                $finalremind=$finalremind->createFromFormat('Y-m-d',$followupThirdDate);
                                                $today=Carbon::now();
                                                if($brushupremind->gt($today)){
                                                $payment_followup_data=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                $payment_followup_data2=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                $payment_followup_data3=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                $customer_log_data['paymentfollowup_id']=$payment_followup_data->id;
                                                $customer_log_data['paymentfollowup_id2']=$payment_followup_data2->id;
                                                $customer_log_data['paymentfollowup_id3']=$payment_followup_data3->id;
                                                Comments::addPaymentComments($customer_log_data);
                                                }else if($finalremind->gt($today)){
                                                    //create single followup
                                                    $payment_followup_data=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                    $customer_log_data['customer_id']=$paymentDuesResult->customer_id;
                                                    $customer_log_data['student_id']=$paymentDuesResult->student_id;
                                                    $customer_log_data['franchisee_id']=Session::get('franchiseId');
                                                    $customer_log_data['paymentfollowup_id']=$payment_followup_data->id;
                                                    $customer_log_data['followup_type']='PAYMENT';
                                                    $customer_log_data['reminderDate']=$followupThirdDate;
                                                    $customer_log_data['followup_status']='REMINDER_CALL';
                                                    $customer_log_data['comment_type']='VERYINTERESTED';
                                                    Comments::addOnebiPaymentComment($customer_log_data);
                                                }
                                                
                                            }else{
                                                $today=Carbon::now();
                                                $reminddate=Carbon::now();
                                                $reminddate=$reminddate->createFromFormat('Y-m-d',$followupdate);
                                                if($reminddate->gt($today)){
                                                $paymentDuesResult->id=$paydueid1;
                                                $payment_followup_data=  PaymentFollowups::createPaymentFollowup($paymentDuesResult);
                                                $customer_log_data['customer_id']=$paymentDuesResult->customer_id;
                                                $customer_log_data['student_id']=$paymentDuesResult->student_id;
                                                $customer_log_data['franchisee_id']=Session::get('franchiseId');
                                                $customer_log_data['paymentfollowup_id']=$payment_followup_data->id;
                                                $customer_log_data['followup_type']='PAYMENT';
                                                $customer_log_data['reminderDate']=$followupdate;
                                                $customer_log_data['followup_status']='REMINDER_CALL';
                                                $customer_log_data['comment_type']='VERYINTERESTED';
                                                
                                                Comments::addOnebiPaymentComment($customer_log_data);
                                                }
                                            }
                                        }
				}
					
			}
                        // to create payment reminders
			/*
			$weeksDues = array("6", "12", "19", "24", "34");
			$date = date('Y/m/d', strtotime($reminderStartDate));
			foreach($weeksDues as $weeksDue){
					
				$weeks = $weeksDue;
				// Create and modify the date.
				$dateTime = DateTime::createFromFormat('Y/m/d', $date);
				$dateTime->add(DateInterval::createFromDateString($weeks . ' weeks'));
				//$dateTime->modify('next monday');
					
				// Output the new date.
				//echo $dateTime->format('Y-m-d')."<br>";
					
					
				$paymentReminderInput['customerId']    = $inputs['customerId'];
				$paymentReminderInput['studentId']     = $inputs['studentId'];
                                $paymentDuesInput['seasonId']             =$inputs['SeasonsCbx'];
				$paymentReminderInput['classId']       = $inputs['eligibleClassesCbx'];
				$paymentReminderInput['batchId']       = $inputs['batchCbx'];
                                $paymentReminderInput['seasonId']       =$inputs['SeasonsCbx'];
                                $paymentDuesInput['seasonId']          = $inputs['SeasonsCbx'];
				$paymentReminderInput['reminder_date'] = $dateTime->format('Y-m-d');
					
				PaymentReminders::addReminderDates($paymentReminderInput);
					
					
					
			}
                         * 
                         */
                        
                        
                        //payment reminders for followups.
                        if($i==1){
                        $order['payment_mode']=$inputs['paymentTypeRadio'];
                        $order['card_last_digit']=$inputs['card4digits'];
                        $order['cardType']=$inputs['card_type'];
                        $order['bank_name']=$inputs['cardBankName'];
                        $order['receipt_number']=$inputs['cardRecieptNumber'];
                        }
		}
		
		$orderCreated = Orders::createOrder($order);
		
		
                if(($inputs['CustomerType']=='OldCustomer')&& ($inputs['OrderDate2']!='') && ($inputs['paymentOptionsRadio']=='bipay')){
                    $payment_due=new PaymentDues();
                    $payment_due=PaymentDues::find($paydue_id2);
                    $payment_due->payment_status='paid';
                    $payment_due->save();
                    
                    $order['created_at']=$paymentDuesInput['created_at'];
                    $order['amount']=($paymentDuesResult->payment_due_amount-$paymentDuesResult->discount_amount);
                    $order['payment_dues_id']=$paydue_id2;
                    if($inputs['paymentTypeRadioOldCustomer2']=='cash'){
                        $order['payment_mode']='cash';
                        $orderCreated = Orders::createOrder($order);
                    }elseif($inputs['paymentTypeRadioOldCustomer2']=='cheque'){
                        $order['payment_mode']='cheque';
                        $order['bank_name']=$inputs['bankName2'];
                        $order['cheque_number']=$inputs['chequeNumber2'];
                        $orderCreated = Orders::createOrder($order);
                    }elseif($inputs['paymentTypeRadioOldCustomer2']=='card'){
                        $order['payment_mode']='card';
                        $order['card_type']=$inputs['cardType2'];
                        $order['card_last_digit']=$inputs['card4digits2'];
                        $order['bank_name']=$inputs['cardBankName2'];
                        $order['receipt_number']=$inputs['cardRecieptNumber2'];
                        $orderCreated = Orders::createOrder($order);
                    }
                }
                
                if(($inputs['CustomerType']=='OldCustomer')&& ($inputs['OrderDate2']!='')&& $inputs['paymentOptionsRadio'] == 'multipay'){
                    
                    $paydue_data=PaymentDues::where('id','=',$paydue_id[1])->get();
                    $paydue_data=$paydue_data[0];
                    $neworder=new Orders;
                    $neworder['customer_id']=$paydue_data['customer_id'];
                    $neworder['student_id']=$paydue_data['student_id'];
                    $neworder['season_id']=$paydue_data['season_id'];
                    $neworder['student_classes_id']=$paydue_data['student_class_id'];
                    $neworder['payment_for']="enrollment";
                    $neworder['payment_dues_id']=$paydue_id[1];
                    $neworder['amount']=$paydue_data['payment_due_amount'];
                    $neworder['order_status']="completed";
                    
                    if($inputs['paymentTypeRadioOldCustomer2']=='cash'){
                        $neworder['payment_mode']='cash';
                    }else if($inputs['paymentTypeRadioOldCustomer2']=='cheque'){
                        $neworder['payment_mode']='cheque';
                        $neworder['bank_name']=$inputs['bankName2'];
                        $neworder['cheque_number']=$inputs['chequeNumber2'];
                    }else if($inputs['paymentTypeRadioOldCustomer2']=='card'){
                        $neworder['payment_mode']='card';
                        $neworder['card_type']=$inputs['cardType2'];
                        $neworder['card_last_digit']=$inputs['card4digits2'];
                        $neworder['bank_name']=$inputs['cardBankName2'];
                        $neworder['receipt_number']=$inputs['cardRecieptNumber2'];
                    }
                    $neworder['created_at']=$paydue_data['created_at'];
                    $neworder->created_by = Session::get('userId');
                    $neworder->save();
                    
                
                    
                    
                    
                }
                if(($inputs['CustomerType']=='OldCustomer')&& ($inputs['OrderDate3']!='')&& $inputs['paymentOptionsRadio'] == 'multipay'){
                    
                    $paydue_data=PaymentDues::where('id','=',$paydue_id[2])->get();
                    $paydue_data=$paydue_data[0];
                    $neworder=new Orders;
                    $neworder['customer_id']=$paydue_data['customer_id'];
                    $neworder['student_id']=$paydue_data['student_id'];
                    $neworder['season_id']=$paydue_data['season_id'];
                    $neworder['student_classes_id']=$paydue_data['student_class_id'];
                    $neworder['payment_for']="enrollment";
                    $neworder['payment_dues_id']=$paydue_id[2];
                    $neworder['amount']=$paydue_data['payment_due_amount'];
                    $neworder['order_status']="completed";
                    if($inputs['paymentTypeRadioOldCustomer3']=='cash'){
                        $neworder['payment_mode']='cash';
                    }else if($inputs['paymentTypeRadioOldCustomer3']=='cheque'){
                        $neworder['payment_mode']='cheque';
                        $neworder['bank_name']=$inputs['bankName3'];
                        $neworder['cheque_number']=$inputs['chequeNumber3'];
                    }else if($inputs['paymentTypeRadioOldCustomer3']=='card'){
                        $neworder['payment_mode']='card';
                        $neworder['card_type']=$inputs['cardType3'];
                        $neworder['card_last_digit']=$inputs['card4digits3'];
                        $neworder['bank_name']=$inputs['cardBankName3'];
                        $neworder['receipt_number']=$inputs['cardRecieptNumber3'];
                    }
                    $neworder['created_at']=$paydue_data['created_at'];
                    $neworder->created_by = Session::get('userId');
                    $neworder->save();
                    
                }
                if(($inputs['CustomerType']=='OldCustomer')&& ($inputs['OrderDate4']!='')&& $inputs['paymentOptionsRadio'] == 'multipay'){
                    
                    $paydue_data=PaymentDues::where('id','=',$paydue_id[3])->get();
                    $paydue_data=$paydue_data[0];
                    $neworder=new Orders;
                    $neworder['customer_id']=$paydue_data['customer_id'];
                    $neworder['student_id']=$paydue_data['student_id'];
                    $neworder['season_id']=$paydue_data['season_id'];
                    $neworder['student_classes_id']=$paydue_data['student_class_id'];
                    $neworder['payment_for']="enrollment";
                    $neworder['payment_dues_id']=$paydue_id[3];
                    $neworder['amount']=$paydue_data['payment_due_amount'];
                    $neworder['order_status']="completed";
                    if($inputs['paymentTypeRadioOldCustomer4']=='cash'){
                        $neworder['payment_mode']='cash';
                    }else if($inputs['paymentTypeRadioOldCustomer4']=='cheque'){
                        $neworder['payment_mode']='cheque';
                        $neworder['bank_name']=$inputs['bankName4'];
                        $neworder['cheque_number']=$inputs['chequeNumber4'];
                    }else if($inputs['paymentTypeRadioOldCustomer4']=='card'){
                        $neworder['payment_mode']='card';
                        $neworder['card_type']=$inputs['cardType4'];
                        $neworder['card_last_digit']=$inputs['card4digits4'];
                        $neworder['bank_name']=$inputs['cardBankName4'];
                        $neworder['receipt_number']=$inputs['cardRecieptNumber4'];
                    }
                    $neworder['created_at']=$paydue_data['created_at'];
                    $neworder->created_by = Session::get('userId');
                    $neworder->save();
                    
                }
               
		
		if(isset($inputs['membershipType'])){
			$membershipInputs['customer_id'] = $inputs['customerId'];
			$membershipInputs['membership_type_id'] = $inputs['membershipType'];
			CustomerMembership::addMembership($membershipInputs);
		}
		
		
		
		
		$student = Students::with('Customers','StudentClasses')->where('id','=',$enrollment->student_id)->get();
		$class   = Classes::where('id','=',$enrollment->class_id)->get();
		
		
		$CustomerObject = Customers::find($inputs['customerId']);
		$CustomerObject->stage          = "ENROLLED";
		$CustomerObject->save();
			
		$customer = array();
		$customer['customerName']  = $student['0']->Customers->customer_name;
		$customer['customerEmail'] = $student['0']->Customers->customer_email;
		$customer['kidName']       = $student['0']->student_name;
		$customer['className']     = $class['0']->class_name;
		
		$commentsInput['customerId']     = $inputs['customerId'];
		$commentsInput['commentText']    = Config::get('constants.ENROLLED').' for '.$class['0']->class_name;
		$commentsInput['commentType']    = 'FOLLOW_UP';
		$commentsInput['reminderDate']   = null;
		Comments::addComments($commentsInput);
		
		if(isset($inputs['emailOption']) && $inputs['emailOption'] == 'yes'){
			
			
			
			$orders = Orders::with('Customers', 'Students', 'StudentClasses')->where('id', '=', $orderCreated->id)->get();
			$orders = $orders['0'];
                        $paymentDues = PaymentDues::where('id', '=', $orders->payment_dues_id)->get();
                        $batchDetails = Batches::where('id', '=', $orders->StudentClasses->batch_id)->get();
                        $class = Classes::where('id', '=', $orders->StudentClasses->class_id)
                                ->where('franchisee_id', '=', Session::get('franchiseId'))->first();
                        $customerMembership = CustomerMembership::getCustomerMembership($orders->customer_id);
			$class  = Classes::where('id', '=', $inputs['eligibleClassesCbx'])
							->where('franchisee_id', '=', Session::get('franchiseId'))->first(); 
			$batch  = Batches::where('id', '=', $inputs['batchCbx'])->first();
			
			
			
			$orderDetailsTomail['orders'] = $orders;
			$orderDetailsTomail['customers'] = $customer;
                        $orderDetailsTomail['paymentDues'] = $paymentDues;
                        $orderDetailsTomail['customerMembership'] = $customerMembership;
			$orderDetailsTomail['class'] = $class;
			$orderDetailsTomail['batchDetails'] = $batchDetails;
			
			$orderDetailsTomail['studentbatch']['start_date'] = date('Y-m-d', strtotime($inputs['enrollmentStartDate']));
			$orderDetailsTomail['studentbatch']['end_date']   = date('Y-m-d', strtotime($inputs['enrollmentEndDate']));
			$orderDetailsTomail['customers']['customerMembership'] = CustomerMembership::getCustomerMembership($orders->customer_id);
			
			
			Mail::send('emails.account.enrollment', $orderDetailsTomail, function($msg) use ($orderDetailsTomail){
					
				$msg->from(Config::get('constants.EMAIL_ID'), Config::get('constants.EMAIL_NAME'));
				$msg->to($orderDetailsTomail['customers']['customerEmail'], $orderDetailsTomail['customers']['customerName'])->subject('The Little Gym - Kids Enrollment Successful');
			
			});
		}
		
		if(isset($inputs['invoicePrintOption']) && $inputs['invoicePrintOption'] == 'yes'){
			$printUrl = url().'/orders/print/'.Crypt::encrypt($orderCreated->id);
		}else{
			$printUrl = "";
		}
		
		
		//header('Access-Control-Allow-Origin: *');
		if($enrollment){
			return Response::json(array("status"=>"success", "printUrl"=>$printUrl));
		}else{
			return Response::json(array("status"=>"failed"));
            	}
	}
	
	
	public function checkenrollmentExists(){
		
		$inputs = Input::all();
		$studentId = $inputs['studentId'];
		$classId   = $inputs['classId'];
		
		$enrolledClassForStudent = StudentClasses::where("student_id","=",$studentId)
		->where("class_id","=",$classId)->get();
		
		if(isset($enrolledClassForStudent['0'])){
			return Response::json(array("status"=>"exist"));
		}
		return Response::json(array("status"=>"clear"));
	}
	
	
	
	public function addbirthdayParty(){
		
		$inputs = Input::all();
                $taxAmtapplied=$inputs['taxAmount'];
                if($inputs['membershipType'] != "" && $inputs['membershipPriceBday'] !=0 ){
			$membershipInput['customer_id']        = $inputs['customerId'];
			$membershipInput['membership_type_id'] = $inputs['membershipType'];
			$customerMembershipData=CustomerMembership::addMembership($membershipInput);
                        if($customerMembershipData->membership_type_id==1){
                        $followupMembershipData=  Comments::addFollowupForMembership($customerMembershipData);
                        }
		}
                $addBirthday =  BirthdayParties::addbirthdayParty($inputs);
                
                if($inputs['remainingAmount']!=0){
                    
                    if(isset($customerMembershipData)){
                    $addBirthday['membership_id']=$customerMembershipData->id;
                    $membership_data=  MembershipTypes::find($customerMembershipData->membership_type_id);
                    $addBirthday['membership_amount']=$membership_data->fee_amount;
                    
                    
                    }
                $firstpayment=PaymentDues::createBirthdaypaymentFirstdues($addBirthday);
                $addPaymentDues= PaymentDues::createBirthdaypaymentdues($addBirthday);
                }
                if(isset($addPaymentDues)){
                $addBirthdayOrder = Orders::createBOrder($addBirthday,$firstpayment,$taxAmtapplied,$inputs);
                }else{
                    $addBirthdayOrder = Orders::createBOrderwithoutPaymentDue($addBirthday,$addPaymentDues,$taxAmtapplied);
                }
                $addPaymentremainder= PaymentReminders::addReminderDates($addBirthday);
                
                $input['customerId']=$addBirthday->customer_id;
                $input['birthday_id']=$addBirthday->id;
                $input['student_id']=$addBirthday->student_id;
                $input['commentType']='ACTION_LOG';
                $student_data=Students::find($addBirthday->student_id);
                $input['commentText']="Birthday celebration added for kid ".$student_data['student_name'];
                $input['commentStatus']='ACTIVE/SCHEDULED';
                 Comments::addComments($input);
                  
                $input['followupType']='PAYMENT';
                $input['commentStatus']='REMINDER_CALL';
                $input['commentType']='VERYINTERESTED';
                $input['commentText']="Call for Birthday celebration for kid ".$student_data['student_name'];
                $celebration_date=Carbon::createFromFormat('d M Y',$inputs['birthdayCelebrationDate']);
                if($celebration_date->eq(carbon::now())){
                 Comments::addComments($input);
                }else{
                  $celebration_date->subDay();
                  $input['reminderDate']=  $celebration_date->toDateString();
                 Comments::addComments($input);
                }
                
                if(isset($inputs['invoicePrintOption']) && $inputs['invoicePrintOption'] == 'yes'){
			$printUrl = url().'/orders/Bprint/'.Crypt::encrypt($addBirthdayOrder);
                        //$printUrl = url().'/orders/Bprint/'.$addBirthdayOrder;
                        
		}else{
			$printUrl = "";
		}
                    
                //header('Access-Control-Allow-Origin: *');
		if($addBirthdayOrder){
			return Response::json(array("status"=>"success","printUrl"=>$printUrl));
		}
		return Response::json(array("status"=>"failed"));
	}
	
        public function createPendingorder(){
            $inputs = Input::all();
            $taxamount=$inputs['taxamount'];
            $paymentPendingdata=  PaymentDues::getPaymentpendingdata($inputs['pending_id']);
            $createPendingorder=  Orders::createPendingorder($paymentPendingdata,$taxamount,$inputs);
            $changingpendingstatus=  PaymentDues::changeStatustopaid($inputs['pending_id'],0);
            
            $paymentpending=PaymentDues::where('id','=',$inputs['pending_id'])->get();
                $input['customerId']=$paymentpending[0]['customer_id'];
                $input['birthday_id']=$paymentpending[0]['birthday_id'];
                $input['student_id']=$paymentpending[0]['student_id'];
                $input['commentType']='ACTION_LOG';
                $student_data=Students::find($input['student_id']);
                $input['commentText']="Birthday celebration Completed  for kid ".$student_data['student_name'];
               
                Comments::addComments($input);
            $print_url=url().'/orders/Bprint/'.Crypt::encrypt($createPendingorder->id);
            //header('Access-Control-Allow-Origin: *');
            
            return Response::json(array("status"=>"success","printurl"=>$print_url));
            
        }
        public function createPendingOrderForEnrollment(){
             $inputs = Input::all();
             if($inputs['paymentType']=='cash'){
             $discountamount=$inputs['pendingamount'];
             $paymentPendingdata=  PaymentDues::getPaymentpendingdata($inputs['pending_id']);
             $createorder=  Orders::createPendingOrderForEnrollment($paymentPendingdata);
             $changingpendingstatus= PaymentDues::changeStatustopaid($inputs['pending_id'],$discountamount);
             $print_url=url().'/orders/print/'.Crypt::encrypt($createorder->id);
             //header('Access-Control-Allow-Origin: *');
             
             return Response::json(array("status"=>"success","printurl"=>$print_url));
             }
             elseif($inputs['paymentType']=='card'){
             $discountamount=$inputs['pendingamount'];
             $paymentPendingdata=  PaymentDues::getPaymentpendingdata($inputs['pending_id']);
             $createorder=  Orders::createPendingOrderForEnrollmentCardType($paymentPendingdata,$inputs);
             $changingpendingstatus= PaymentDues::changeStatustopaid($inputs['pending_id'],$discountamount);
             $print_url=url().'/orders/print/'.Crypt::encrypt($createorder->id);
             return Response::json(array("status"=>"success","printurl"=>$print_url));
             
             }elseif($inputs['paymentType']=='cheque'){
               $discountamount=$inputs['pendingamount'];
               $paymentPendingdata=  PaymentDues::getPaymentpendingdata($inputs['pending_id']);
               $createorder=  Orders::createPendingOrderForEnrollmentChequeType($paymentPendingdata,$inputs);
               $changingpendingstatus= PaymentDues::changeStatustopaid($inputs['pending_id'],$discountamount);
               $print_url=url().'/orders/print/'.Crypt::encrypt($createorder->id);
               return Response::json(array("status"=>"success","printurl"=>$print_url));
             }
        }
	public function checkExistingBirthdayParty(){
		
		$inputs = Input::all();
		$existingBirthdayParties = BirthdayParties::checkWhetherBirthdayPartyExists($inputs['kidsSelect']);
		if($existingBirthdayParties){
			return Response::json(array("status"=>"exist"));
		}
		return Response::json(array("status"=>"clear"));
		
	}
	
	
	public function getStudentsByBatch(){
		
		$inputs = Input::all();
		
		$batchId       = $inputs['batchId'];
		$selectedDate  = $inputs['selectedDate'];
		
		$studentsByBatchId = StudentClasses::getStudentByBatchId($batchId, $selectedDate);
		
		if($studentsByBatchId){
			
			$attendanceArray = array();
			$i = 0;
			foreach ($studentsByBatchId as $studentAttendance){
				
				$attendanceArray[$i]['studentName'] = $studentAttendance->Students->student_name;
				$attendanceArray[$i]['studentId']   = $studentAttendance->Students->id;
				$studentAttendanceRecord = Attendance::getDaysAttendanceForStudent($studentAttendance->Students->id, $batchId,  $selectedDate);
				
				if($studentAttendanceRecord){
					
					$attendanceArray[$i]['isAttendanceEntered'] = 'yes';
					$attendanceArray[$i]['attendanceStatus'] = $studentAttendanceRecord->status;
					
				}else{
					$attendanceArray[$i]['isAttendanceEntered'] = 'no';
					
				}
				
				$i++;
			}
			//print_r($attendanceArray);
			return Response::json(array("status"=>"success", 'result'=>$attendanceArray));
		}
		return Response::json(array("status"=>"failed"));
		
	}
	
	
	
	public function addStudentAttendance(){
		
		$inputs = Input::all();
			
		for($i =0; $i<$inputs['totalStudents'];$i++){
			
			$isAttendanceExists = Attendance::getDaysAttendanceForStudent($inputs['student_'.$i], $inputs['batch_'.$i], $inputs['attendanceDate_'.$i]);
			if($isAttendanceExists){

				$isAttendanceExists->status = $inputs['attendance_for_user'.$i];
				$isAttendanceExists->save();
				
			}else{
				
				$attendanceData = new Attendance();
				$attendanceData->attendance_date = $inputs['attendanceDate_'.$i];
				$attendanceData->batch_id        = $inputs['batch_'.$i];
				$attendanceData->student_id      = $inputs['student_'.$i];
				$attendanceData->status          = $inputs['attendance_for_user'.$i];
				$attendanceData->save();
				
			}
			
		}
		return Response::json(array("status"=>"success"));
	}
	
	
	public function addIntroVisit(){
		
		$inputs = Input::all();
		
		
		
		$result = IntroVisit::addSchedule($inputs);
		
		
		$commentsInput['customerId']     = $inputs['customerId'];
                $commentsInput['student_id']     = $inputs['studentIdIntroVisit'];
                $commentsInput['introvisit_id']  = $result->id;
		$commentsInput['commentText']    = Config::get('constants.IV_SCHEDULED_COMMENT').'  '.$inputs['customerCommentTxtarea'];
                $commentsInput['commentStatus']  = 'ACTIVE/SCHEDULED';
		$commentsInput['commentType']    = $inputs['commentType'];
                $commentsInput['followupType']    = $inputs['followupType'];
		//$commentsInput['reminderDate']   = date('Y-m-d', strtotime($inputs['reminderTxtBox']));
		$iv_date=Carbon::createFromFormat('m/d/Y',$inputs['introVisitTxtBox']);
                if($iv_date->eq(carbon::now())){
                Comments::addComments($commentsInput);
                }else{
                  $iv_date->subDay();
                  $commentsInput['reminderDate']=  $iv_date->toDateString();
                  Comments::addComments($commentsInput);
                }
                
		
		if($result){
			return Response::json(array("status"=>"success"));
		}
		return Response::json(array("status"=>"failed"));
		
		
	}
	
	public function editIntroVisit(){
	
		$inputs = Input::all();
	
                    if(($inputs['iveditAction']!=' ')){
                    $introvisit_data_make_reminder_null= Comments::where('introvisit_id','=',$inputs['iv_id'])
                                               ->update(array('reminder_date'=>Null,));
                    $introvisit=Comments::where('introvisit_id','=',$inputs['iv_id'])
                                               ->orderBy('id','DESC')
                                               ->first();
                    
                    //return Response::json(array("status",$introvisit)); 
                        
                     if($introvisit){
                      
                          if($inputs['ivstatus']=='ATTENDED/CELEBRATED'){
                              $commentText = Config::get('constants.IV_ATTENDED_COMMENT').'on '.date('Y-m-d').' '.$inputs['customerCommentTxtarea'];
				
                          }elseif($inputs['ivstatus']== 'REMINDER_CALL'){
				
				$commentText = "Reminder call  ".'on  '.date('Y-m-d', strtotime($inputs['reminder-date'])).' '.$inputs['customerCommentTxtarea'];
				
		 	  }elseif($inputs['ivstatus']== 'FOLLOW_CALL'){
				
				$commentText = 'FOLLOW CALL on '.date('Y-m-d', strtotime($inputs['reminder-date'])).$inputs['customerCommentTxtarea'];
                		
			  }elseif($inputs['ivstatus']== 'CALL_SPOUSE'){
				
				$commentText = "CALL SPOUSE Call  ".'on  '.date('Y-m-d', strtotime($inputs['reminder-date'])).' '.$inputs['customerCommentTxtarea'];
				
		 	  }elseif($inputs['ivstatus']== 'NOT_AVAILABLE'){
				
				$commentText = "NOT_AVAILABLE  ".'on  '.date('Y-m-d', strtotime($inputs['reminder-date'])).' '.$inputs['customerCommentTxtarea'];
				
		 	  }elseif($inputs['ivstatus']== 'NOT_INTERESTED'){
				
				$commentText = "Customer Not Interested  ".' -'.$inputs['customerCommentTxtarea'];
                                
				
		 	  }elseif($inputs['ivstatus']== 'ENROLLED'){
                                $commentText='Customer Enrolled '.$inputs['customerCommentTxtarea'];
                              
                                
                          } 
                        
                        
                        //insert new followuphere
                           
			$commentsInput['customerId']     = $introvisit['customer_id'];
                        $commentsInput['student_id']     = $introvisit['student_id'];
                        $commentsInput['introvisit_id']  = $introvisit['introvisit_id'];
                       

                     
                        $commentsInput['followupType']  = $introvisit['followup_type'];
                        $commentsInput['commentStatus']= $inputs['ivstatus'];
                        $commentsInput['commentType']   = $inputs['iveditAction']; 
                           
			$commentsInput['commentText']    = $commentText;
		      	
                        if($inputs['ivstatus']!= 'ENROLLED'){
                            if($inputs['ivstatus']!= 'NOT_INTERESTED'){
                               if(isset($inputs['reminder-date'])){
                                   $commentsInput['reminderDate']   = $inputs['reminder-date'];
                               }
                            }
                        }
                        //if($inputs['ivstatus']!= 'ENROLLED'){
                        //    $iv=IntroVisit::find($introvisit);
                        //    $iv->
                        //}
                       //return Response::json(array("status",$commentsInput));
	                
                       Comments::addComments($commentsInput);
                        
                       }
                       
                       
                    }
	return Response::json(array("status"=>"success"));
	}

        public function editBirthdayCelebrationFollowup(){
            $inputs=Input::all();
            
            if($inputs['birthdayAction']!=' '){
                $birthday_data_make_reminder_null= Comments::where('birthday_id','=',$inputs['birthday_id'])
                                               ->update(array('reminder_date'=>Null,));
                $bithday_celebration=Comments::where('birthday_id','=',$inputs['birthday_id'])
                                               ->orderBy('id','DESC')
                                               ->first();
                $student_data=  Students::find($bithday_celebration['student_id']);
                if($bithday_celebration){
                      
                          if($inputs['birthdaystatusSelect']=='ATTENDED/CELEBRATED'){
                              $commentText = "Birthday Celebrated ".'on '.date('Y-m-d').'for kid '.$student_data['student_name'].$inputs['birthdayCommentTxtarea'];
				
                          }elseif($inputs['birthdaystatusSelect']== 'REMINDER_CALL'){
				
				$commentText = "Reminder call  ".'on  '.date('Y-m-d', strtotime($inputs['birthdayReminderDate'])).' '.$inputs['birthdayCommentTxtarea'];
				
		 	  }elseif($inputs['birthdaystatusSelect']== 'FOLLOW_CALL'){
				
				$commentText = 'birthdayFollow CALL on '.date('Y-m-d', strtotime($inputs['birthdayReminderDate'])).$inputs['birthdayCommentTxtarea'];
                		
			  }elseif($inputs['birthdaystatusSelect']== 'CALL_SPOUSE'){
				
				$commentText = "CALL SPOUSE  ".'on  '.date('Y-m-d', strtotime($inputs['birthdayReminderDate'])).' '.$inputs['birthdayCommentTxtarea'];
				
		 	  }elseif($inputs['birthdaystatusSelect']== 'NOT_AVAILABLE'){
				
				$commentText = "NOT_AVAILABLE  ".'on  '.date('Y-m-d', strtotime($inputs['birthdayReminderDate'])).' '.$inputs['birthdayCommentTxtarea'];
				
		 	  }elseif($inputs['birthdaystatusSelect']== 'NOT_INTERESTED'){
				
				$commentText = "Customer Not Interested  ".' -'.$inputs['birthdayCommentTxtarea'];
                                
				
		 	  }
                        
                        
                        //insert new followuphere
                           
			$commentsInput['customerId']     = $bithday_celebration['customer_id'];
                        $commentsInput['student_id']     = $bithday_celebration['student_id'];
                        $commentsInput['birthday_id']  = $bithday_celebration['birthday_id'];
                        $commentsInput['followupType']  = $bithday_celebration['followup_type'];
                        $commentsInput['commentStatus']= $inputs['birthdaystatusSelect'];
                        $commentsInput['commentType']   = $inputs['birthdayAction']; 
                           
			$commentsInput['commentText']    = $commentText;
		      	
                        if($inputs['birthdaystatusSelect']!= 'ATTENDED/CELEBRATED'){
                            if($inputs['birthdaystatusSelect']!= 'NOT_INTERESTED'){
                               if(isset($inputs['birthdayReminderDate'])){
                                   $commentsInput['reminderDate']   = $inputs['birthdayReminderDate'];
                               }
                            }
                        }
                        
                       Comments::addComments($commentsInput);
                        
                       }
                       
                     
            }
            return Response::json(array('status'=>'success','data'=>$inputs));
        }
        public function getBirthdayHistoryDataByBirthdayId(){
            $inputs = Input::all();
            $birthdayHistory=  Comments::where('birthday_id','=',$inputs['birthday_id'])
                                           ->orderBy('id','desc')
                                           ->get();
            for($i=0;$i<count($birthdayHistory);$i++){
                $data=User::find($birthdayHistory[$i]['created_by']);
                $birthdayHistory[$i]['commentor_name']=$data->first_name.' '.$data->last_name; 
            }
            return Response::json(array('status'=>'success','data'=>$birthdayHistory));
        }
        
        public function getEnrollmetHistory(){
            $inputs=Input::all();
            $enrollment_history=Comments::where('paymentfollowup_id','=',$inputs['paymentfollowupId'])
                                          ->orderBy('id','DESC')
                                          ->get();
            for($i=0;$i<count($enrollment_history);$i++){
                $data=User::find($enrollment_history[$i]['created_by']);
                $enrollment_history[$i]['commentor_name']=$data->first_name.' '.$data->last_name; 
            }
            return Response::json(array('status'=>'success','data'=>$enrollment_history));
        }
        
        public function checkBiPayOrderDate(){
            $inputs=  Input::all();
            $batch_data=  Batches::find($inputs['batchid']);
            $eachClassCost=$batch_data->class_amount;
            $startdate=new carbon();
            $enddate=new carbon();
            $startdate=$startdate->createFromFormat('m/d/Y',$inputs['startdate']);
            $enddate=$enddate->createFromFormat('m/d/Y',$inputs['enddate']);
            
            $firstPayWeeksNo=($inputs['bipayamount1']/$eachClassCost);
            $secondPayWeeksNo=($inputs['bipayamount2']/$eachClassCost);
            $totalWeekNo=$firstPayWeeksNo+$secondPayWeeksNo;
            $batch_data=BatchSchedule::where('batch_id','=',$inputs['batchid'])
                           //->where('franchisee_id','=',Session::get('franchiseId'))
                           //->where('season_id','=',$inputs['seasonid'])
                           ->whereBetween('schedule_date',array($startdate->toDateString(),$enddate->toDateString()))
                           ->where('holiday','!=',1)
                           ->orderBy('id')
                           ->get();
            //return Response::json(array('status'=>$batch_data));
            $firstPayWeeksNo=$firstPayWeeksNo-1;
            $secondPaymentDate=new carbon();
            $presentdate=new carbon();
            $presentdate=$presentdate->now();
            $secondPaymentDate=$secondPaymentDate->createFromFormat('Y-m-d',$batch_data[$firstPayWeeksNo]['schedule_date']);
           
            if($presentdate->gt($secondPaymentDate)){
            return Response::json(array('status'=>'true'));
            }else{
            return Response::json(array('status'=>'false'));   
            }
        }
        
        
        public function checkmultiPayOrderDate(){
            $inputs=  Input::all();
            $batch_data=  Batches::find($inputs['batchid']);
            $eachClassCost=$batch_data->class_amount;
            $startdate=new carbon();
            $enddate=new carbon();
            $startdate=$startdate->createFromFormat('m/d/Y',$inputs['startdate']);
            $enddate=$enddate->createFromFormat('m/d/Y',$inputs['enddate']);
            if(isset($inputs['multipayAmount1'])){
              $firstPayWeeksNo=($inputs['multipayAmount1']/$eachClassCost);
            }
            if(isset($inputs['multipayAmount2'])){
                 $secondPayWeeksNo=($inputs['multipayAmount2']/$eachClassCost);
            }
            if(isset($inputs['multipayAmount3'])){
                 $thirdPayWeeksNo=($inputs['multipayAmount3']/$eachClassCost);
            }
            if(isset($inputs['multipayAmount4'])){
                 $fourthPayWeeksNo=($inputs['multipayAmount4']/$eachClassCost);
            }
            //$totalweekNo=$firstPayWeeksNo+$secondPayWeeksNo+$thirdPayWeeksNo+$fourthPayWeeksNo;
            $batch_data=BatchSchedule::where('batch_id','=',$inputs['batchid'])
                           ->where('franchisee_id','=',Session::get('franchiseId'))
                           ->where('season_id','=',$inputs['seasonid'])
                           ->whereBetween('schedule_date',array($startdate->toDateString(),$enddate->toDateString()))
                           ->where('holiday','!=',1)
                           ->orderBy('id')
                           ->get();
            $firstPayWeeksNo=$firstPayWeeksNo-1;
            if(isset($inputs['multipayAmount2'])){
                $secondPayWeeksNo=$secondPayWeeksNo+$firstPayWeeksNo;
            }
            if(isset($inputs['multipayAmount3'])){
                $thirdPayWeeksNo=$secondPayWeeksNo+$thirdPayWeeksNo;
            }
            if(isset($inputs['multipayAmount4'])){
             $fourthPayWeeksNo=$thirdPayWeeksNo+$fourthPayWeeksNo;
            }
            $presentdate=new carbon();
            $presentdate=$presentdate->now();
            
            $secondPaymentDate=new carbon();
            $thirdPaymentDate=new carbon();
            $fourthPaymentDate= new carbon();
            if(isset($inputs['multipayAmount2'])){ 
                $secondPaymentDate=$secondPaymentDate->createFromFormat('Y-m-d',$batch_data[$firstPayWeeksNo]['schedule_date']);
            }
            if(isset($inputs['multipayAmount3'])){
            $thirdPaymentDate=$thirdPaymentDate->createFromFormat('Y-m-d',$batch_data[$secondPayWeeksNo]['schedule_date']);
            }
            if(isset($inputs['multipayAmount4'])){
            $fourthPaymentDate=$fourthPaymentDate->createFromFormat('Y-m-d',$batch_data[$thirdPayWeeksNo]['schedule_date']);
            }
           
             
           if(isset($inputs['multipayAmount4'])){
             if($presentdate->gt($fourthPaymentDate)){
                return Response::json(array('status'=>'four'));
            }
           }
           if(isset($inputs['multipayAmount3'])){
            if($presentdate->gt($thirdPaymentDate)){
                return Response::json(array('status'=>'three'));
            }
           }
               
           
           if(isset($inputs['multipayAmount2'])){
            if($presentdate->gt($secondPaymentDate)){
                return Response::json(array('status'=>'two','date'=>$secondPaymentDate));
            }
           }
            //return Response::json(array('status'=>$batch_data));
        }
        
        
        
        
        
        public function getBirthdayOrderPendingDetails(){
            $inputs=Input::all();
            $paymentData=  PaymentDues::where('id','=',$inputs['pending_id'])->get();
            $paymentData=$paymentData[0];
            $birthdayPartyData=BirthdayParties::where('id','=',$paymentData['birthday_id'])->get();
            $birthdayPartyData=$birthdayPartyData[0];
            $studentData=  Students::where('id','=',$paymentData['student_id'])->get();
            $studentData=$studentData[0];
            return Response::json(array("status"=>"success","payment_due_data"=>$paymentData,
                                        "birthday_data"=>$birthdayPartyData,"student_data"=>$studentData));
        }
        
        
        
        
        public function modifyBirthdayPendingOrder(){
           $inputs=Input::all();
             //payment_due_table
           $payment_data=  PaymentDues::find($inputs['pending_id']);
           $payment_data->payment_due_amount=$inputs['amountpending'];
           $payment_data->updated_by=Session::get('userId');
           $payment_data->save();
           $payment_data=  PaymentDues::where('id','=',$inputs['pending_id'])->get();
           $payment_data=$payment_data[0];
             //birthday_table
           
           $birthday_data=BirthdayParties::find($payment_data['birthday_id']);
           $birthday_data->remaining_due_amount=$inputs["amountpending"];
           $birthday_data->additional_number_of_guests=$inputs['additionalguestNo'];
           $birthday_data->additional_half_hours=$inputs['additionalhalfhours'];
           $birthday_data->additional_guest_price=$inputs['additionalguesAmount'];
           $birthday_data->additional_halfhour_price=$inputs['additionalhalfhourscost'];
           $birthday_data->updated_by=Session::get('userId');
           $birthday_data->save();
           
           $birthday_data=BirthdayParties::where('id','=',$payment_data['birthday_id'])->get();
           $birthday_data=$birthday_data[0];
           $b=  BirthdayParties::find($birthday_data->id);
           $b->grand_total=($birthday_data['default_birthday_cost']+$birthday_data['additional_guest_price']+$birthday_data['additional_halfhour_price']);
           $b->save();
           
           
           // create order for payment order in payemnt due
           
           $payment_data=  PaymentDues::where('id','=',$inputs['pending_id'])->get();
           $taxamount=$inputs['taxamount'];
           $createPendingorder=  Orders::createPendingorder($payment_data,$taxamount,$inputs);
            $changingpendingstatus=  PaymentDues::changeStatustopaid($inputs['pending_id'],0);
            $print_url=url().'/orders/Bprint/'.Crypt::encrypt($createPendingorder->id);
            
            
            
            
            $paymentpending=PaymentDues::where('id','=',$inputs['pending_id'])->get();
                $input['customerId']=$paymentpending[0]['customer_id'];
                $input['birthday_id']=$paymentpending[0]['birthday_id'];
                $input['student_id']=$paymentpending[0]['student_id'];
                $input['commentType']='ACTION_LOG';
                $student_data=Students::find($input['student_id']);
                $input['commentText']="Birthday celebration Completed  for kid ".$student_data['student_name'];
               
                Comments::addComments($input);

            //header('Access-Control-Allow-Origin: *');
            
            return Response::json(array("status"=>"success","printurl"=>$print_url));
          
           
        }
        
        
        public static function getIntrovisitHistory(){
            $inputs=Input::all();
            $introVisitHistory=  Comments::where('introvisit_id','=',$inputs['introvisit_id'])
                                           ->orderBy('id','desc')
                                           ->get();
            for($i=0;$i<count($introVisitHistory);$i++){
                $data=User::find($introVisitHistory[$i]['created_by']);
                $introVisitHistory[$i]['commentor_name']=$data->first_name.' '.$data->last_name; 
            }
            return Response::json(array('status'=>$introVisitHistory));
        }
        
        public function getIvdataByCustomerId(){
            $inputs=Input::all();
            $data=IntroVisit::where('customer_id','=',$inputs['customer_id'])
                              ->get();
            for($i=0;$i<count($data);$i++){
                $student_data=  Students::find($data[$i]['student_id']);
                $data[$i]['student_name']=$student_data['student_name'];
                $data[$i]['latestcomment']=Comments::where('introvisit_id','=',$data[$i]['id'])->orderBy('id','desc')->first();
                
            }
            return Response::json(array('status'=>'success','data'=>$data));
        }
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
        
        
        public static function getStudentsByCustomerid(){
         $inputs=Input::all();
         $students_data=  Students::where('customer_id','=',$inputs['customer_id'])->get();
         if($students_data){
         return Response::json(array('status'=>'success','student_data'=>$students_data));
         }else{
             return Response::json(array('status'=>'failure'));
         }
         
        }
        
        public function createFollowup(){
            $inputs=Input::all();
            if($inputs['followupType']=='COMPLAINTS'){
               $createComplaint=Complaint::createComplaint($inputs);
               $input['complaint_id']=$createComplaint->id;
            }else if($inputs['followupType']=='RETENTION'){
               $createRetention=Retention::createRetention($inputs);
               $input['retention_id']=$createRetention->id;
            }else if($inputs['followupType']=='INQUIRY'){
                $createInquiry=Inquiry::createInquiry($inputs);
                $input['inquiry_id']=$createInquiry->id;
            }
            
            //create comment 
            if(isset($createComplaint)){
               $input['customerId']=$inputs['customer_id'];
               $input['student_id']=$inputs['student_id'];
               $input['followupType']=$inputs['followupType'];
               $input['commentStatus']=$inputs['followupstatus'];
               $input['commentText']=$inputs['otherCommentTxtarea'];
               $input['commentType']=$inputs['comment_type'];
               $input['reminderDate']=$inputs['remindDate'];
               $comments_data=Comments::addComments($input);
            }
            if(isset($createRetention)){
               $input['customerId']=$inputs['customer_id'];
               $input['student_id']=$inputs['student_id'];
               $input['followupType']=$inputs['followupType'];
               $input['commentStatus']=$inputs['followupstatus'];
               $input['commentText']=$inputs['otherCommentTxtarea'];
               $input['commentType']=$inputs['comment_type'];
               $input['reminderDate']=$inputs['remindDate'];
               $comments_data=Comments::addComments($input);    
            }
            if(isset($createInquiry)){
               $input['customerId']=$inputs['customer_id'];
               
               $input['followupType']=$inputs['followupType'];
               $input['commentStatus']=$inputs['followupstatus'];
               $input['commentText']=$inputs['otherCommentTxtarea'];
               $input['commentType']=$inputs['comment_type'];
               $input['reminderDate']=$inputs['remindDate'];
               $comments_data=Comments::addComments($input);
            }
            if($comments_data){
            return Response::json(array('status'=>'success'));
            }else{
            return Response::json(array('status'=>'failure'));
            }
        }
        
        
        
        public function getComplaintHistoryById(){
        $inputs=Input::all();
        $complaint_data=  Comments::where('complaint_id','=',$inputs['complaintId'])
                          ->orderBy('id','DESC')
                          ->get();
        for($i=0;$i<count($complaint_data);$i++){
                $data=User::find($complaint_data[$i]['created_by']);
                $complaint_data[$i]['commentor_name']=$data->first_name.' '.$data->last_name; 
            }
        return Response::json(array('status'=>'success','data'=>$complaint_data));
        }
        
        public function getRetentionHistoryById(){
          $inputs=Input::all();
          $retention_data=Comments::where('retention_id','=',$inputs['retentionId'])
                          ->orderBy('id','DESC')
                          ->get();
          for($i=0;$i<count($retention_data);$i++){
                $data=User::find($retention_data[$i]['created_by']);
                $retention_data[$i]['commentor_name']=$data->first_name.' '.$data->last_name; 
            }
          return Response::json(array('status'=>'success','data'=>$retention_data));
        }
        public function getInquiryHistoryById(){
          $inputs=Input::all();
          $Inquiry_data=Comments::where('inquiry_id','=',$inputs['inquiryId'])
                          ->orderBy('id','DESC')
                          ->get();
          for($i=0;$i<count($Inquiry_data);$i++){
                $data=User::find($Inquiry_data[$i]['created_by']);
                $Inquiry_data[$i]['commentor_name']=$data->first_name.' '.$data->last_name; 
            }
          return Response::json(array('status'=>'success','data'=>$Inquiry_data));
        }
        
        
        
        public function UpdateFollowup(){
            $inputs=Input::all();
            $complaint_data_make_reminder_null= Comments::where('complaint_id','=',$inputs['complaint_id'])
                                               ->update(array('reminder_date'=>Null,));
            $complaint_data=Comments::where('complaint_id','=',$inputs['complaint_id'])
                                               ->orderBy('id','DESC')
                                               ->first();
            $student_data=  Students::find($complaint_data['student_id']);

            if($inputs['followup_status']=='ACTIVE/SCHEDULED'){
                $commentText = "Active call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
				
            }elseif($inputs['followup_status']=='REMINDER_CALL'){
                $commentText = "Reminder call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
				
                
            }elseif($inputs['followup_status']=='FOLLOW_CALL'){
                $commentText = "Follow call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
		
            }elseif($inputs['followup_status']=='CALL_SPOUSE'){
                $commentText = "Call Spouse ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
		                
            }elseif($inputs['followup_status']=='NOT_AVAILABLE'){
                $commentText = "Not Available  ".'on  '.date('Y-m-d').' '.$inputs['customer_text_area'];
		        
            }elseif($inputs['followup_status']=='CLOSE_CALL'){
                $commentText="Followupcall closed on ".date('Y-m-d').' '.$inputs['customer_text_area'];
            }
                
                        $commentsInput['customerId']     = $complaint_data['customer_id'];
                        $commentsInput['student_id']     = $complaint_data['student_id'];
                        $commentsInput['complaint_id']  = $complaint_data['complaint_id'];
                        $commentsInput['followupType']  = $complaint_data['followup_type'];
                        $commentsInput['commentStatus']= $inputs['followup_status'];
                        $commentsInput['commentType']   = $inputs['comment_type']; 
                           
			$commentsInput['commentText']    = $commentText;
		      	
                        
                            if($inputs['followup_status']!= 'CLOSE_CALL'){
                               if(isset($inputs['rDate'])){
                                   $commentsInput['reminderDate']   = $inputs['rDate'];
                               }
                            }
                        
                        
                       Comments::addComments($commentsInput);
            
            return Response::json(array('status'=>'success'));
            
        }        
        
        public function UpdateRetentionFollowup(){
            $inputs=Input::all();
            $retention_data_make_reminder_null= Comments::where('retention_id','=',$inputs['retention_id'])
                                               ->update(array('reminder_date'=>Null,));
            $retention_data=Comments::where('retention_id','=',$inputs['retention_id'])
                                               ->orderBy('id','DESC')
                                               ->first();
            $student_data=  Students::find($retention_data['student_id']);

            if($inputs['followup_status']=='ACTIVE/SCHEDULED'){
                $commentText = "Active call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
				
            }elseif($inputs['followup_status']=='REMINDER_CALL'){
                $commentText = "Reminder call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
				
                
            }elseif($inputs['followup_status']=='FOLLOW_CALL'){
                $commentText = "Follow call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
		
            }elseif($inputs['followup_status']=='CALL_SPOUSE'){
                $commentText = "Call Spouse ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
		                
            }elseif($inputs['followup_status']=='NOT_AVAILABLE'){
                $commentText = "Not Available  ".'on  '.date('Y-m-d').' '.$inputs['customer_text_area'];
		        
            }elseif($inputs['followup_status']=='CLOSE_CALL'){
                $commentText="Followupcall closed on ".date('Y-m-d').' '.$inputs['customer_text_area'];
            }
                
                        $commentsInput['customerId']     = $retention_data['customer_id'];
                        $commentsInput['student_id']     = $retention_data['student_id'];
                        $commentsInput['retention_id']  = $retention_data['retention_id'];
                        $commentsInput['followupType']  = $retention_data['followup_type'];
                        $commentsInput['commentStatus']= $inputs['followup_status'];
                        $commentsInput['commentType']   = $inputs['comment_type']; 
                           
			$commentsInput['commentText']    = $commentText;
		      	
                        
                            if($inputs['followup_status']!= 'CLOSE_CALL'){
                               if(isset($inputs['rDate'])){
                                   $commentsInput['reminderDate']   = $inputs['rDate'];
                               }
                            }
                        
                        
                       Comments::addComments($commentsInput);
            
            return Response::json(array('status'=>'success'));
            
        }
        
        
        
        
        
        public function UpdateInquiryFollowup(){
            $inputs=Input::all();
            $inquiry_data_make_reminder_null= Comments::where('inquiry_id','=',$inputs['inquiry_id'])
                                               ->update(array('reminder_date'=>Null,));
            $inquiry_data=Comments::where('inquiry_id','=',$inputs['inquiry_id'])
                                               ->orderBy('id','DESC')
                                               ->first();
            //$student_data=  Students::find($inquiry_data['student_id']);

            if($inputs['followup_status']=='ACTIVE/SCHEDULED'){
                $commentText = "Active call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
				
            }elseif($inputs['followup_status']=='REMINDER_CALL'){
                $commentText = "Reminder call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
				
                
            }elseif($inputs['followup_status']=='FOLLOW_CALL'){
                $commentText = "Follow call  ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
		
            }elseif($inputs['followup_status']=='CALL_SPOUSE'){
                $commentText = "Call Spouse ".'on  '.date('Y-m-d', strtotime($inputs['rDate'])).' '.$inputs['customer_text_area'];
		                
            }elseif($inputs['followup_status']=='NOT_AVAILABLE'){
                $commentText = "Not Available  ".'on  '.date('Y-m-d').' '.$inputs['customer_text_area'];
		        
            }elseif($inputs['followup_status']=='CLOSE_CALL'){
                $commentText="Followupcall closed on ".date('Y-m-d').' '.$inputs['customer_text_area'];
            }
                
                        $commentsInput['customerId']     = $inquiry_data['customer_id'];
                       // $commentsInput['student_id']     = $inquiry_data['student_id'];
                        $commentsInput['inquiry_id']  = $inquiry_data['inquiry_id'];
                        $commentsInput['followupType']  = $inquiry_data['followup_type'];
                        $commentsInput['commentStatus']= $inputs['followup_status'];
                        $commentsInput['commentType']   = $inputs['comment_type']; 
                           
			$commentsInput['commentText']    = $commentText;
		      	
                        
                            if($inputs['followup_status']!= 'CLOSE_CALL'){
                               if(isset($inputs['rDate'])){
                                   $commentsInput['reminderDate']   = $inputs['rDate'];
                               }
                            }
                        
                        
                       Comments::addComments($commentsInput);
            
            return Response::json(array('status'=>'success'));
            
        }
        
        
        
        public function editEnrollment(){
            $inputs=Input::all();
            $paymentreminder_data_make_reminder_null= Comments::where('paymentfollowup_id','=',$inputs['paymentdue_id'])
                                               ->update(array('reminder_date'=>Null,));
            $paymentreminder_data=Comments::where('paymentfollowup_id','=',$inputs['paymentdue_id'])
                                               ->orderBy('id','DESC')
                                               ->first();
            
            
            
            if($inputs['enrollmentstatus']=='REMINDER_CALL'){
                $commentText = "Reminder call  ".'on  '.date('Y-m-d', strtotime($inputs['reminder-date'])).' '.$inputs['customerCommentTxtarea'];
				
                
            }elseif($inputs['enrollmentstatus']=='FOLLOW_CALL'){
                $commentText = "Follow call  ".'on  '.date('Y-m-d', strtotime($inputs['reminder-date'])).' '.$inputs['customerCommentTxtarea'];
		
            }elseif($inputs['enrollmentstatus']=='CALL_SPOUSE'){
                $commentText = "Call Spouse ".'on  '.date('Y-m-d', strtotime($inputs['reminder-date'])).' '.$inputs['customerCommentTxtarea'];
		                
            }elseif($inputs['enrollmentstatus']=='NOT_AVAILABLE'){
                $commentText = "Not Available  ".'on  '.date('Y-m-d').' '.$inputs['customerCommentTxtarea'];
            }elseif($inputs['enrollmentstatus']=='NOT_INTERESTED'){
                $commentText = "NOT_INTERESTED ".'on  '.date('Y-m-d').' '.$inputs['customerCommentTxtarea'];
            }elseif($inputs['enrollmentstatus']=='CLOSE_CALL'){
                $commentText="Followupcall closed on ".date('Y-m-d').' '.$inputs['customerCommentTxtarea'];
            }
             
            
                        $commentsInput['customerId']     = $paymentreminder_data['customer_id'];
                        $commentsInput['student_id']     = $paymentreminder_data['student_id'];
                        $commentsInput['paymentfollowup_id']     = $paymentreminder_data['paymentfollowup_id'];
                        $commentsInput['followupType']   = $paymentreminder_data['followup_type'];
                        $commentsInput['commentStatus']  = $inputs['enrollmentstatus'];
                        $commentsInput['commentType']    = $inputs['enrollmenteditAction']; 
                           
			$commentsInput['commentText']    = $commentText;
		      	
                       
                            if(($inputs['enrollmentstatus']!= 'CLOSE_CALL')){
                                if(($inputs['enrollmentstatus']!= 'NOT_INTERESTED')){
                                   if(isset($inputs['reminder-date'])){
                                     $commentsInput['reminderDate']   = $inputs['reminder-date'];
                                   }
                            
                                 }
                             }
                        
                         //return Response::json(array('status'=>$inputs)); 
                       Comments::addComments($commentsInput);
            
            
            return Response::json(array('status'=>'success'));
            
        }
        
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
