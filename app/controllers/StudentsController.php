<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
//include 'mpdf60/mpdf.php';
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
      return Redirect::action('VaultController@logout');
    }
    
  }
        
  public function enrolledstudents(){
    if(Auth::check()){
        $currentPage  =  "ENROLLEDSTUDENTS";
        $mainMenu     =  "STUDENTS_MAIN";
        $students = StudentClasses::getAllEnrolledStudents(Session::get('franchiseId'));
        $status_array = array();
    		foreach($students as $key=> $value) {
    			$list = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
                             ->where('student_id', '=', $value['student_id'])
                             ->where('end_order_date', '>=', date('Y-m-d'))
                              ->where('payment_due_for', '=', 'enrollment')
                             ->count();
          if($list > 1){
                  $status = 'Multiple';
          }else{  
                  $status = 'Single';
          }
          $status_array[$value['student_id']] = array('status'=> $status
                                                                         );
        }
		    foreach($students as $key=> $value) {
            $students[$key]['status'] = '';
            if(array_key_exists($value['student_id'], $status_array)) {
                $students[$key]['status'] = $status_array[$value['student_id']]['status'];
            }
        }
		
        $dataToView = array('students','currentPage', 'mainMenu');
        return View::make('pages.students.enrolledstudentslist', compact($dataToView));
      }else{
        return Redirect::action('VaultController@logout');
      }
    
  }
  public function addstudent(){
            if(Auth::check()){
    $inputs = Input::all();
    $addStudentResult = Students::addStudent($inputs);
    
    header('Access-Control-Allow-Origin: *');
    if($addStudentResult){
      return Response::json(array("status"=>"success"));
    }else{
      return Response::json(array("status"=>"failed"));
    }
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
                            $studentclasssectiondata = Classes::getstudentclasses($order_due_data[0]['class_id']);
                            $order_due_data[$i]['class_name'] = $studentclasssectiondata[0]['class_name'];
                            $user_Data = User::getUsersByUserId($order_due_data[$i]['created_by']);
                            $order_due_data[$i]['receivedname'] = $user_Data[0]['first_name'].$user_Data[0]['last_name'];
                        }
                        //getting values for present Discount for enrollment
                        $discount_second_child = 0;
                        $discount_second_class = 0;
                        $discount_second_child_elligible = 0;
                        $discount_second_class_elligible = 0;
                        $count=0;
                        
                        $DiscountApprove = Discounts::where('franchisee_id', '=', Session::get('franchiseId'))->first();
                        $end = StudentClasses::where('student_id', '=', $id)
                                               ->where('student_id', '=', $id)
                                               ->max('enrollment_end_date'); 
                        if($DiscountApprove['discount_second_child_approve'] == 1){
                            $discount_second_child_elligible = 1;
                            $discount_second_child = $DiscountApprove['discount_second_child'];
                        }
                        if($DiscountApprove['discount_second_class_approve'] == 1){
                            $discount_second_class_elligible = 1;
                            $discount_second_class = $DiscountApprove['discount_second_class'];
                        }

                        if($discount_second_class_elligible){
                              $classes_count = StudentClasses::where('student_id', '=', $id)
                                               ->where('status', '=', 'enrolled')
                                               ->count();

                              if($classes_count >= 1){
                                  $discount_second_class_elligible = 1;  
                              }else{
                                  $discount_second_class_elligible = 0;
                              }
                        }
                        
                        if($discount_second_child_elligible){
                           $student_ids = Students::where('customer_id', '=', $student[0]['customer_id'])->select('id')->get()->toArray();
                           for($i=0; $i<count($student_ids); $i++){
                               if($student_ids[$i]['id'] != $id){
                                 if(StudentClasses::where('student_id', '=', $student_ids[$i]['id'])->where('status','=','enrolled')->exists()){
                                    $count++;   
                                  }
                               }
                           }
                         
                           if($count >= 1){
                                $discount_second_child_elligible = 1;
                            }else{
                                $discount_second_child_elligible = 0;
                            }
                        }
                    // Getting latest batches for showing in header of student tab
                        $latestEnrolledData = StudentClasses::where('student_id','=',$id)
							                                              ->where('enrollment_end_date', '>=', date('Y-m-d'))
                                                            ->orderBy('created_at','desc')
                                                            ->limit(2)
                                                            ->get();
                        //return $latestEnrolledData;
                        for($i=0;$i<count($latestEnrolledData);$i++){
                          $latest_batch_data = Batches::find($latestEnrolledData[$i]['batch_id']);
                          $latestEnrolledData[$i]['batch_name'] = $latest_batch_data['batch_name'];
                          $latestEnrolledData[$i]['batch_id'] = $latest_batch_data['id'];
                          $latestEnrolledData[$i]['preferred_time'] = $latest_batch_data['preferred_time'];
                          $latestEnrolledData[$i]['preferred_end_time'] = $latest_batch_data['preferred_end_time'];
                          $latestEnrolledData[$i]['enrollment_start_date'] = date('d-M-Y',strtotime($latestEnrolledData[$i]['enrollment_start_date']));
                          $latestEnrolledData[$i]['enrollment_end_date'] = date('d-M-Y',strtotime($latestEnrolledData[$i]['enrollment_end_date']));

                        }
                        $discountEnrollmentData = Discounts::getEnrollmentDiscontByFranchiseId();    
			//getting the date from payment_master for camps or yards

			$payment_made_data_summer = PaymentDues::where('student_id','=',$id)
								->where('payment_due_for','!=','enrollment')
								->where('payment_due_for','!=','birthday')
                                                                ->get();
			   for($i=0;$i<count($payment_made_data_summer);$i++){	
				$payment_made_data_summer[$i]['day'] = date('l', strtotime($payment_made_data_summer[$i]['start_order_date']));
			  	$payment_made_data_summer[$i]['encrypted_payment_no'] = url().'/orders/printSummerOrder/'.Crypt::encrypt($payment_made_data_summer[$i]['payment_no']);
				$userName = User::find($payment_made_data_summer[$i]['created_by']);
				$payment_made_data_summer[$i]['receivedBy'] = $userName['first_name'].''.$userName['last_name'];
			  }
			 
			
                    //getting the data from payment_master
                        $payments_master_details = PaymentDues::where('student_id','=',$id)
                                                                  ->distinct('payment_no')
                                                                  ->select('payment_no')
                                                                  ->get();

                        for($i=0;$i<count($payments_master_details);$i++){
                          $payment_made_data[$i] = PaymentDues::where('student_id','=',$id)
                                                                ->where('payment_no','=',$payments_master_details[$i]['payment_no'])
                                                                ->get();
                          
                          for($j=0;$j<count($payment_made_data[$i]);$j++){
			    if (isset($payment_made_data[$i][$j]['batch_id']) && !empty($payment_made_data[$i][$j]['batch_id'])){
                                $batch_name = Batches::where('id','=',$payment_made_data[$i][$j]['batch_id'])
                                              ->selectRaw('batch_name,preferred_end_time,preferred_time')
                                              ->get();
                            	if(isset($batch_name) && !empty($batch_name)){  
                              		$batch_user = User::find($payment_made_data[$i][$j]['created_by']);

                              		$payment_made_data[$i][$j]['day'] = date('l', strtotime($payment_made_data[$i][$j]['start_order_date']));
                              		$start_time = explode(':', $batch_name[0]['preferred_time']);
                              		$end_time = explode(':', $batch_name[0]['preferred_end_time']);

                              		$payment_made_data[$i][$j]['time'] = $start_time[0].':'.$start_time[1].'-'.$end_time[0].':'.$end_time[1];
                              
                              		$payment_made_data[$i][$j]['receivedname'] = $batch_user['first_name'].$batch_user['last_name'];
                              
                              		$payment_made_data[$i][$j]['class_name'] = $batch_name[0]['batch_name'];
                            	}
                            }  
                           }
                          $payments_master_details[$i]['encrypted_payment_no'] = url().'/orders/print/'.Crypt::encrypt($payments_master_details[$i]['payment_no']);
                        }
                        
                        $AttendanceYeardata = DB::select("SELECT EXTRACT(year from enrollment_start_date) as year FROM student_classes WHERE student_id = $id GROUP BY year");   
                        $taxPercentage = PaymentTax::getTaxPercentageForPayment();
                        $tax_data = TaxParticulars::where('franchisee_id','=',Session::get('franchiseId'))->get();
                        $membershipTypesAll = MembershipTypes::getMembershipTypes();
                        //2nd class applciable for remaning classes in 1st class
                        
                        $last = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                        ->where('student_id', '=', $id)
                                        ->where('selected_sessions', '!=', 1)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(1)
                                        ->get(); 
                        $last_Enrollment_EndDate = count($last) > 0 ? $last[0]['enrollment_end_date'] : '';
                                                 
                        $base_price = ClassBasePrice::where('franchise_id', '=', Session::get('franchiseId'))
                                                    ->select('base_price')
                                                    ->get();
                        
                        $present = carbon::now();
                        $iv = IntroVisit::where('franchisee_id', '=', Session::get('franchiseId'))
                                        ->where('student_id', '=', $id)
                                        ->select('iv_date')
                                        ->get();
                        if(sizeof($iv))
                        {
                          $iv_date = date('Y-m-d', strtotime($iv[0]['iv_date']));
                          $present_date = date('Y-m-d', strtotime($present));

                          if(isset($iv_date) != ''){
                            if(($iv_date) >= $present_date){
                                $stage = 'IV SCHEDULED';
                              }else{
                                $stage = '';
                              }
                            }
                        }else{
                          $stage = '';
                        }
                        $file = glob("assets/discovery_images/discovery_".$id."*.{jpg,gif,png,csv,pdf,tif,xls,odt}", GLOB_BRACE);
                        if(isset($file) && !empty($file)){
                          $file_extention = explode(".", $file[0]);
                          $url = "assets/discovery_images/discovery_".$id.".".$file_extention[1];
                          $attachment_location  = url().'/'.$url;
                        }else{  
                          $attachment_location = "";
                        }
                        
                        
                        
                        $batchDetails = [];
                        $batch_id = StudentClasses::where('student_id', '=', $id)
                                                    ->select('id', 'batch_id', 'enrollment_start_date', 'enrollment_end_date', 'selected_sessions', 'status')
                                                    ->get();

                        for($i=0;$i<count($batch_id);$i++){
                              $batch = Batches::where('id', '=', $batch_id[$i]['batch_id'])
                                              ->select('batch_name','preferred_end_time', 'preferred_time','start_date')
                                              ->get();
                              $bacth_name = explode('-', $batch[0 ]['batch_name']); 
                              $batchDetails[$i]['batch_name'] = $bacth_name[0].' '.$bacth_name[1].' '.$bacth_name[2];
                              $batchDetails[$i]['Day'] = date('l', strtotime($batch_id[$i]['enrollment_start_date']));
                              $timeStart = explode(':',$batch[0]['preferred_time']);
                              $timeEnd = explode(':',$batch[0]['preferred_end_time']);
                              $batchDetails[$i]['time'] = $timeStart[0].':'.$timeStart[1].'-'.$timeEnd[0].':'.$timeEnd[1]; 
                              
                              $batchDetails[$i]['enrollment_start_date'] = date('d-M-Y',strtotime($batch_id[$i]['enrollment_start_date']));

                              $batchDetails[$i]['enrollment_end_date'] = date('d-M-Y',strtotime($batch_id[$i]['enrollment_end_date']));
                              $batchDetails[$i]['selected_sessions'] = $batch_id[$i]['selected_sessions'];
                              $batchDetails[$i]['id'] = $batch_id[$i]['id'];

                              $transfer = StudentClasses::where('student_id', '=', $id)
                                                    ->select('status')
                                                    ->get();                    
                              if($transfer[$i]['status'] == 'transferred_class'){
                                $batchDetails[$i]['stage'] = 'Transfered';
                              }else{
                                $batchDetails[$i]['stage'] = '';
                              }

                              ////// To get present dates count ////////

                              $present_count = Attendance::where('student_id', '=', $id)
                                                ->where('student_classes_id', '=', $batch_id[$i]['id'])
                                                ->select('status', 'attendance_date')
                                                ->where('status', '=', 'P')
                                                ->count();
                              
                              $batchDetails[$i]['present'] = $present_count > 0 ? $present_count : 0;

                              ////// To get absent date count ////////

                              $absent_count = Attendance::where('student_id', '=', $id)
                                                ->where('student_classes_id', '=', $batch_id[$i]['id'])
                                                ->select('status')
                                                ->where('status', '=', 'A')
                                                ->count();
                              $batchDetails[$i]['Absent'] = $absent_count > 0 ? $absent_count : 0;

                              ////// To get EA count ////////

                              $EA_count = Attendance::where('student_id', '=', $id)
                                                ->where('student_classes_id', '=', $batch_id[$i]['id'])
                                                ->where('makeup_class_given','=',null)
                                                ->where('status', '=', 'EA')
                                                ->count(); 
                              $batchDetails[$i]['EA'] = $EA_count > 0 ? $EA_count : 0; 

                              ////// To get makeup count ////////

                              $makeup_count = Attendance::where('student_id', '=', $id)
                                                ->where('student_classes_id', '=', $batch_id[$i]['id'])
                                                ->where('makeup_class_given', '=', '1')
                                                ->count();
                              $batchDetails[$i]['makeup'] = $makeup_count > 0 ? $makeup_count : 0;

                              $total = $batchDetails[$i]['present'] + $batchDetails[$i]['Absent'] + $batchDetails[$i]['EA'] + $batchDetails[$i]['makeup'];
                              
                              if(!empty($batch_id[$i]['selected_sessions']) && count($batch_id[$i]['selected_sessions']) > 0){
                                  if($total <= $batch_id[$i]['selected_sessions']){
                                     $attendance = Attendance::where('student_id', '=', $id)
                                                      ->where('student_classes_id', '=', $batch_id[$i]['id'])
                                                      ->count();

                                     $batchDetails[$i]['remaining_classes'] = $batchDetails[$i]['selected_sessions'] - $attendance;
                                  }else{
                                     $batchDetails[$i]['remaining_classes'] = 0;
                                  }
                              }else{
                                  $batchDetails[$i]['remaining_classes'] = 0;
                              }
                        }

                      //Introvisit Tab

                      $introvisit_list = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                               ->where('student_id', '=', $id)
                                               ->where('status', '=', 'introvisit')
                                               ->get();
                      for($i = 0; $i < count($introvisit_list); $i++){
                         $batches = Batches::where('id', '=', $introvisit_list[0]['batch_id'])
                                           ->get();
                         $introvisit_list[$i]['batch_name'] = $batches[0]['batch_name'];
                         $introvisit_list[$i]['preferred_time'] = $batches[0]['preferred_time'];
                         $introvisit_list[$i]['Day'] = date('l', strtotime($batches[0]['start_date']));
                        
                      }

                      $makeup_list = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                               ->where('student_id', '=', $id)
                                               ->where('status', '=', 'makeup')
                                               ->get();
                      for($i = 0; $i < count($makeup_list); $i++){
                         $batches = Batches::where('id', '=', $makeup_list[0]['batch_id'])
                                           ->get();
                         $makeup_list[$i]['batch_name'] = $batches[0]['batch_name'];
                         $makeup_list[$i]['preferred_time'] = $batches[0]['preferred_time'];
                         $makeup_list[$i]['Day'] = date('l', strtotime($batches[0]['start_date']));
                        
                      }
                      $name = "discovery_".$student[0]['id']."_medium.jpg";
                      $attachment_location = '';  
                      $file = glob("assets/discovery_images/discovery_".$student[0]['id']."*.{jpg,gif,png,csv,pdf,tif,xls,odt}", GLOB_BRACE);
                      if(isset($file) && !empty($file)){
                        $file_extention = explode(".", $file[0]);
                        $discovery_sheet = "assets/discovery_images/discovery_".$student[0]['id'].".".$file_extention[1];
                      } else {
                        $discovery_sheet = '';
                      }

      $dataToView = array("student",'currentPage', 'mainMenu','franchiseeCourses', 'membershipTypesAll','end', 'last_Enrollment_EndDate','attachment_location','discountEnrollmentData','latestEnrolledData','taxPercentage','tax_data','discount_second_class_elligible','discount_second_child_elligible','discount_second_child','discount_second_class','studentEnrollments','customermembership','paymentDues','scheduledIntroVisits', 'introvisit', 'discountEligibility','paidAmountdata','order_due_data','payment_made_data','payments_master_details', 'AttendanceYeardata','base_price','stage','batchDetails','payment_made_data_summer','introvisit_list','makeup_list','discovery_sheet');
      return View::make('pages.students.details',compact($dataToView));
    }else{
      return Redirect::action('VaultController@logout');
    }
  }
  
  public function deleteBatchesEnrollForId () {
      if((Auth::check()) && (Session::get('userType')=='ADMIN') ){
        $inputs=Input::all();
        $payment_no = PaymentDues::where('student_class_id', '=', $inputs['student_class_id'])
                                  ->where('franchisee_id', '=', Session::get('franchiseId'))
                                  ->get();
        if (isset($payment_no) && !empty($payment_no) && count($payment_no)) {
          $orderDelete = Orders::where('student_id', '=', $inputs['studentId'])
                               ->where('franchisee_id', '=', Session::get('franchiseId'))
                               ->where('payment_no', '=', $payment_no[0]['payment_no'])
                               ->delete();
          $paymentsDelete = PaymentDues::where('student_class_id', '=', $inputs['student_class_id'])
                                    ->where('franchisee_id', '=', Session::get('franchiseId'))
                                    ->delete();
          $paymentMasterDelete = PaymentMaster::where('student_id', '=', $inputs['studentId'])
                               ->where('payment_no', '=', $payment_no[0]['payment_no'])
                               ->delete();
          $paymentFollowupsDelete = PaymentFollowups::where('student_id', '=', $inputs['studentId'])
                               ->where('payment_no', '=', $payment_no[0]['payment_no'])
                               ->delete();
          $studentClassesDelete = StudentClasses::where('id', '=', $payment_no[0]['student_class_id'])
                               ->where('student_id', '=', $inputs['studentId'])
                               ->delete();
        } else {
          $studentClassesDelete = StudentClasses::where('id', '=', $inputs['student_class_id'])
                               ->where('student_id', '=', $inputs['studentId'])
                               ->delete();
        }
        if ($studentClassesDelete) {
          return Response::json(array('status'=>'success', 'data' => $inputs));
        } else {
          return Response::json(array('status'=>'failure'));
        }                           
      } else {
        return Response::json(array('status'=>'failure'));
      }
    }

  public function deleteAllBatchesEnrollForId () {
    if((Auth::check()) && (Session::get('userType')=='ADMIN') ){
      $inputs=Input::all();
      $orderDelete = Orders::where('student_id', '=', $inputs['studentId'])
                           ->where('franchisee_id', '=', Session::get('franchiseId'))
                           ->delete();
      $paymentMasterDelete = PaymentMaster::where('student_id', '=', $inputs['studentId'])
                           ->delete();
      $paymentFollowupsDelete = PaymentFollowups::where('student_id', '=', $inputs['studentId'])
                           ->delete();
      $studentClassesDelete = StudentClasses::where('student_id', '=', $inputs['studentId'])
                           ->delete();
      $paymentsDelete = PaymentDues::where('student_id', '=', $inputs['studentId'])
                                ->where('franchisee_id', '=', Session::get('franchiseId'))
                                ->delete();
      $paymentsDelete = Attendance::where('student_id', '=', $inputs['studentId'])
                                ->delete();                        

      if ($paymentsDelete) {
        return Response::json(array('status'=>'success', 'data' => $inputs));
      } else {
        return Response::json(array('status'=>'failure'));
      }
    } else {
      return Response::json(array('status'=>'failure'));
    }
  }

  public function getAttendanceForStudent(){
    $inputs = Input::all();
    $sendDetails = Attendance::getAttendanceForStudent($inputs);
    $totalsession =  StudentClasses::getAllClassCountByBatchId($inputs);
    $getIntrovisit = IntroVisit::getIntrovisitForThisStuId($inputs);
    $getMakeUpClasses = StudentClasses::getmakeupClassesForThisStuId($inputs);
    $transferToOtherCls = StudentClasses::getTransferToOtherCls($inputs);
    if($sendDetails){
      return Response::json(array('status'=> "success", 'data'=> $sendDetails,'totalSession'=>$totalsession, 'introvisit'=>$getIntrovisit, 'makeupClass' => $getMakeUpClasses, 'transferredToOtherClass' => $transferToOtherCls));
    }else{
      return Response::json(array('status'=> "failure",));
    }
  }
  public function getBatchNameByYear(){
    $inputs = Input::all();
    $sendDetails = StudentClasses::select('batch_id')
                                  ->where('enrollment_start_date', 'like', '%'.$inputs["year"].'%')
                                  ->where('student_id', '=', $inputs['studentId'])
                                  ->distinct()
                                  ->get(); 
      // return $sendDetails;
    $name = array();
    for ($i=0; $i < count($sendDetails); $i++) {
      $temp = Batches::where('id', '=', $sendDetails[$i]['batch_id'])
                       ->where('status','=','active')
                       ->get();
   
         if(count($temp) > 0) {
           $timestamp = strtotime($temp[0]['start_date']);
        $temp[0]['day']=date('l', $timestamp);
            if($temp[0]['lead_instructor']!=0){
                $temp2=User::find($temp[0]['lead_instructor']);
                $temp[0]['Leadinstructor']=$temp2->first_name.$temp2->last_name;
            }
          array_push($name,$temp);
         }  
                                            
    }  /*print_r($name);   
    die();*/
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
  
   public function uploadDiscoveryPicture(){
    
    $file = Input::file('discoveryPicture');
    $studentId = Input::get('studentId');
    $destinationPath = 'assets/discovery_images/';
    $filename = $file->getClientOriginalName();   
    $fileExtension = '.'.$file->getClientOriginalExtension();
    $filename = 'discovery_'.$studentId.$fileExtension;
    $discovery_image = Input::file('discoveryPicture')->move($destinationPath, $filename);

    Session::flash('imageUploadMessage', "Discovery Sheet is uploaded successfully." );    
    return Redirect::to("/students/view/".$studentId); 
    
  }

  public function downloadDiscoveryPicture(){
    $studentId = Input::get('studentId');
    $name = "discovery_".$studentId."_medium.jpg";
    $attachment_location = '';  
    $file = glob("assets/discovery_images/discovery_".$studentId."*.{jpg,gif,png,csv,pdf,tif,xls,odt}", GLOB_BRACE);
      if(isset($file) && !empty($file)){
      $file_extention = explode(".", $file[0]);
      $attachment_location = "assets/discovery_images/discovery_".$studentId.".".$file_extention[1];
    }
    return $attachment_location;
    /*if (file_exists($attachment_location)) {
      define('DIRECTORY', '');
       $content = file_get_contents('assets/discovery_images/discovery_'.$studentId.'.jpg','D');
      file_put_contents(  DIRECTORY . 'discovery_'.$studentId.'.jpg', $content);
    }else{
      Session::flash('imageDownloadError', "Respective file is not found.Please upload it." );
      return Redirect::to("/students/view/".$studentId);
    }*/
    Session::flash('imageDownloadMessage', "Discovery Sheet has been downloaded." );
    return Redirect::to("/students/view/".$studentId);
 
  }
        
        
        public function enrollOldCustomer(){
          $inputs = Input::all();  
                
                $tax=PaymentTax::getTaxPercentageForPayment();
                $tax=$tax->tax_percentage;
                
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
                    $customerMembershipInput['customer_id']   = $inputs['oldCustomerId'];
                    $customerMembershipInput['membership_type_id']= $inputs['MembershipTypeForOld'];
                    $customerMembershipDetails=CustomerMembership::addMembership($customerMembershipInput);
                    $paymentDuesInput['membership_id']    = $customerMembershipDetails->id;
                    $paymentDuesInput['membership_type_id']         = $customerMembershipDetails->membership_type_id;
                    $paymentDuesInput['membership_amount']          = $inputs['MembershipAmountForOld'];
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
            $paymentDuesInput['tax']                                    = $tax;
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
                    $franchisee_name=Franchisee::find(Session::get('franchiseId'));
                    $data = compact('totalSelectedClasses', 'getBatchNname',
                        'getSeasonName', 'selectedSessionsInEachBatch', 'classStartDate','franchisee_name',
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
  if(Auth::check()){
    DB::beginTransaction();
    try {
      $inputs = Input::all();
      $final_payment_master_no;
      $getEstimateDetails= Estimate::where('estimate_master_no', '=', $inputs['estimate_master_no'])
                                     ->where('is_cancelled', '!=', '1')
                                     ->where('franchise_id', '=', Session::get('franchiseId'))
                                     ->get();
                // getting the tax for particular franchisee
      if ($inputs['taxAmount'] === '0') {
        $tax = 0;
      } else {
        $tax=PaymentTax::getTaxPercentageForPayment();
        $tax=$tax->tax_percentage;
      }
                //** checking if it is a one batch **//
      if(count($getEstimateDetails) == 1){
	      $fianancialYearDates = Franchisee::getFinancialStartDates();
        $dataForThisYear = Franchisee::where('id', '=', Session::get('franchiseId'))
                                    ->where('financial_year_start_date', '=', $fianancialYearDates['start_date'])
                                    ->where('financial_year_end_date', '=', $fianancialYearDates['end_date'])
                                    ->get();

        if( count($dataForThisYear) > 0){
                $invoiceNo =  $dataForThisYear[0]['max_invoice'] + 1;
                $data = Franchisee::updateInvoiceNumber($invoiceNo);
        }else{  
		$invoiceNo = '1';
                $data = Franchisee::updateFinancialYears($fianancialYearDates);
        }

        $batch_data=  BatchSchedule::
                                where('batch_id','=',$getEstimateDetails[0]['batch_id'])
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
        $singleBatchstartDate=
                        Carbon::createFromFormat('Y-m-d', $getEstimateDetails[0]['enroll_start_date']);
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
        if(isset($inputs['membershipType']) && array_key_exists('membershipType',$inputs) 
           && $inputs['membershipType']!=''){
                  //** create membership for customer **//
          $customerMembershipInput['customer_id']   = $getEstimateDetails[0]['customer_id'];
          $customerMembershipInput['membership_type_id']= $inputs['membershipType'];
          $customerMembershipDetails=CustomerMembership::addMembership($customerMembershipInput);
          $paymentDuesInput['membership_id'] = $customerMembershipDetails->id;
          $paymentDuesInput['membership_type_id']=$customerMembershipDetails->membership_type_id;
          $temp=MembershipTypes::find($customerMembershipDetails->membership_type_id);
          $paymentDuesInput['membership_name']            =       $temp->description;
          $paymentDuesInput['membership_amount']          = $inputs['membershipAmount'];
        }
                
                //** checking for discounts **//
        if(isset($inputs['discountTextBox']) && array_key_exists('discountTextBox',$inputs) 
           && $inputs['discountTextBox']!=''){

          $discount_amount = explode("-",$inputs['discountTextBox']);
          $paymentDuesInput['discount_applied']       = $inputs['discountPercentage'];
          $paymentDuesInput['discount_amount']        = $discount_amount[1];
        }

        if(isset($inputs['second_child_discount_to_form']) && 
            array_key_exists('second_child_discount_to_form',$inputs) && 
            $inputs['second_child_discount_to_form']!=''){

          $explodedDiscountAmount=explode("-",$inputs['second_child_amount']);
          $paymentDuesInput['discount_sibling_applied'] = $inputs['second_child_discount_to_form']; 
          $paymentDuesInput['discount_sibling_amount']  = $explodedDiscountAmount[1];
                
        }

        if(isset($inputs['second_class_discount_to_form'])  &&  
           array_key_exists('second_class_discount_to_form',$inputs) && 
           $inputs['second_class_discount_to_form']!=''){

          $explodedDiscountAmount=explode("-",$inputs['second_class_amount']);
          $paymentDuesInput['discount_multipleclasses_applied'] = $inputs['second_class_discount_to_form']; 
          $paymentDuesInput['discount_multipleclasses_amount']  = $explodedDiscountAmount[1];
        }

        if(isset($inputs['admin_discount_amount']) &&
           array_key_exists('admin_discount_amount',$inputs) && 
           $inputs['admin_discount_amount']!=''){

          $paymentDuesInput['discount_admin_amount']=$inputs['admin_discount_amount'];
        }
                
        $paymentDuesInput['payment_due_amount']                     = $inputs['singlePayAmount'];
        $paymentDuesInput['payment_due_amount_after_discount']      = $inputs['grandTotal'];
        $paymentDuesInput['payment_status']                         = "paid";
        $paymentDuesInput['selected_order_sessions']     = $getEstimateDetails[0]['no_of_opted_classes'];
        $paymentDuesInput['start_order_date']  = $insertDataToStudentClassTable['enrollment_start_date'];
        $paymentDuesInput['end_order_date']    = $insertDataToStudentClassTable['enrollment_end_date'];
        $paymentDuesInput['payment_batch_amount'] = $paymentDuesInput['selected_order_sessions']*$paymentDuesInput['each_class_cost'];
        $paymentDuesInput['tax']  = $tax;
                
        $sendPaymentDetailsToInsert = PaymentDues::createPaymentDues($paymentDuesInput, $invoiceNo);
                
                /* inserting into payment Due table is completed for single pay */
                    /* Working on preparing to payment master table for single pay */
        $sendPaymentMasterDetailsToInsert = PaymentMaster::createPaymentMaster($sendPaymentDetailsToInsert);

        

                    /* inserting into payment Master table is completed for single pay */
                    /* Working on preparing to Orders table for single pay */

        $order['customer_id']     = $inputs['customerId'];
        $order['student_id']      = $insertDataToStudentClassTable['student_id'];
        $order['student_classes_id'] = $insertDataToStudentClassTable['id'];
        if(isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs))
           && ($inputs['paymentTypeRadio'] == "card")){

          $order['payment_mode']    = $inputs['paymentTypeRadio'];  
          $order['card_last_digit'] = $inputs['card4digits'];
          $order['card_type']       = $inputs['cardType'];
          $order['bank_name']       = $inputs['cardBankName'];
                      $order['receipt_number']       = $inputs['cardRecieptNumber'];
        }elseif(isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs)) && 
          ($inputs['paymentTypeRadio'] == "cheque")){
          $order['payment_mode']    = $inputs['paymentTypeRadio'];  
          $order['bank_name']       = $inputs['bankName'];
          $order['cheque_number']       = $inputs['chequeNumber'];

        }elseif(isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs)) && ($inputs['paymentTypeRadio'] == "cash")){
          $order['payment_mode']    = $inputs['paymentTypeRadio'];  
        }
                    
        $order['payment_for']     = "enrollment";
        $order['payment_no']   = $sendPaymentMasterDetailsToInsert['payment_no'];
        $order['payment_dues_id']   = $sendPaymentDetailsToInsert['id'];
        $order['amount'] = $inputs['singlePayAmount'];
        $order['order_status'] = "completed";
                                      
        $sendOrderDetailsToInsert = Orders::createOrder($order, $invoiceNo);

                    
        $update_payment_due = PaymentDues::find($sendPaymentDetailsToInsert->id);
        $update_payment_due->payment_no=$sendPaymentMasterDetailsToInsert->payment_no;
        $final_payment_master_no=$sendPaymentMasterDetailsToInsert->payment_no;
        $update_payment_due->save();

        $updatePaymentMasterTable = PaymentMaster::find($sendPaymentMasterDetailsToInsert->id);
        $updatePaymentMasterTable->order_id = $sendOrderDetailsToInsert->id;
        $updatePaymentMasterTable->save();
                
                    //** working on the payment_followups **//
                    
       // if(count($batch_data) >= 15){
          // $retention['customer_id']     = $inputs['customerId'];
          // $retention['student_id'] = $insertDataToStudentClassTable['student_id'];
          $dataToretention = Retention::createRetention($sendPaymentDetailsToInsert);
                        //creating logs/followup for first payment
          $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
          $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
          $customer_log_data['franchisee_id']=Session::get('franchiseId');
          $customer_log_data['retention_id']=$dataToretention->id;
          $customer_log_data['followup_type']='RETENTION';
          $customer_log_data['followup_status']='REMINDER_CALL';
          $customer_log_data['comment_type']='VERYINTERESTED';

          $PaymentreminderDate=new carbon();
          $PaymentreminderDate=$PaymentreminderDate->createFromFormat('Y-m-d',$insertDataToStudentClassTable['enrollment_end_date']);
          $customer_log_data['reminderDate']=$PaymentreminderDate->toDateString();
          
          Comments::addSinglePayComment($customer_log_data);

          $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($sendPaymentDetailsToInsert,$final_payment_master_no);

          if(isset($payment_followup_data1)){
              $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
              $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
              $customer_log_data['franchisee_id']=Session::get('franchiseId');
              $customer_log_data['paymentfollowup_id'] = $payment_followup_data1->id;
              $customer_log_data['retention_id']='NULL';
              $customer_log_data['followup_type']='PAYMENT';
              $customer_log_data['followup_status']='REMINDER_CALL';
              $customer_log_data['comment_type']='VERYINTERESTED';

              $PaymentreminderDate=new carbon();
              $PaymentreminderDate=$PaymentreminderDate->createFromFormat('Y-m-d',$insertDataToStudentClassTable['enrollment_end_date']);
              $customer_log_data['reminderDate']=$PaymentreminderDate->toDateString();
              
              Comments::addSinglePayComment($customer_log_data);
          }
       // }
    

    
                              
    //** checking if it is a 2 batch **//
    }elseif (count($getEstimateDetails) == 2) {
        $fianancialYearDates = Franchisee::getFinancialStartDates();
        $dataForThisYear = Franchisee::where('id', '=', Session::get('franchiseId'))
                                    ->where('financial_year_start_date', '=', $fianancialYearDates['start_date'])
                                    ->where('financial_year_end_date', '=', $fianancialYearDates['end_date'])
                                    ->get();

        if( count($dataForThisYear) > 0){
                $invoiceNo =  $dataForThisYear[0]['max_invoice'] + 1;
                $updateInvoice = Franchisee::updateInvoiceNumber($invoiceNo);
        }else{ 
		$invoiceNo = '1'; 
                $data = Franchisee::updateFinancialYears($fianancialYearDates);
        }      

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
        $singleBatchstartDate=
                        Carbon::createFromFormat('Y-m-d', $getEstimateDetails[$i]['enroll_start_date']);
        $studentClasses[$i]['enrollment_start_date'] = $getEstimateDetails[$i]['enroll_start_date'];
        $studentClasses[$i]['enrollment_end_date']   = $getEstimateDetails[$i]['enroll_end_date'];
        $singleBatchendDate=
                        Carbon::createFromFormat('Y-m-d', $getEstimateDetails[$i]['enroll_end_date']);
        $insertDataToStudentClassTable           =  StudentClasses::addStudentClass($studentClasses[$i]);
                    
        $paymentDuesInput[$i]['student_id']          = $insertDataToStudentClassTable['student_id'];
        $paymentDuesInput[$i]['customer_id']         = $inputs['customerId'];
        $paymentDuesInput[$i]['batch_id']            = $insertDataToStudentClassTable['batch_id'];
        $paymentDuesInput[$i]['class_id']            = $insertDataToStudentClassTable['class_id'];
        $paymentDuesInput[$i]['selected_sessions'] = $insertDataToStudentClassTable['selected_sessions'];
        $paymentDuesInput[$i]['seasonId']            = $insertDataToStudentClassTable['season_id'];
        $paymentDuesInput[$i]['student_class_id']    = $insertDataToStudentClassTable['id'];
        $paymentDuesInput[$i]['each_class_cost']     = $getEstimateDetails[$i]['base_price'];
                                
                                //** working on the customer_membership **//
        if($i==0){
          if(isset($inputs['membershipType']) && array_key_exists('membershipType',$inputs) && 
            $inputs['membershipType']!=''){
                                    //** create membership for customer **//
            $customerMembershipInput['customer_id']   = $getEstimateDetails[$i]['customer_id'];
            $customerMembershipInput['membership_type_id']      = $inputs['membershipType'];
            $customerMembershipDetails=CustomerMembership::addMembership($customerMembershipInput);

            $paymentDuesInput[$i]['membership_id']    = $customerMembershipDetails->id;
            $paymentDuesInput[$i]['membership_type_id'] = $customerMembershipDetails->membership_type_id;
            $paymentDuesInput[$i]['membership_amount']          = $inputs['membershipAmount'];
             $temp=MembershipTypes::find($customerMembershipDetails->membership_type_id);
            $paymentDuesInput['membership_name']       =       $temp->description;
          }
        }
                                //** checking for discounts **//
        if(isset($inputs['discountPercentage']) && array_key_exists('discountPercentage',$inputs) && $inputs['discountPercentage']!=''){
            
            $discount_amount                               = explode("-",$inputs['discountTextBox']);
            $paymentDuesInput[$i]['discount_amount']       = $discount_amount[1];
            $paymentDuesInput[$i]['discount_applied']      = $inputs['discountPercentage'];
        }

        if(isset($inputs['second_class_discount_to_form']) && 
           array_key_exists('second_class_discount_to_form', $inputs) && 
           $inputs['second_class_discount_to_form']!=''){

          $discount_multipleclasses_amount      = explode("-",$inputs['second_class_amount']);
          $paymentDuesInput[$i]['discount_multipleclasses_amount'] = $discount_multipleclasses_amount[1];
          $paymentDuesInput[$i]['discount_multipleclasses_applied']= 
                                                            $inputs['second_class_discount_to_form'];
        }
        if(isset($inputs['second_child_discount_to_form']) && 
           array_key_exists('second_child_discount_to_form', $inputs) && 
           $inputs['second_child_discount_to_form']!=''){

          $discount_sibling_amount   = explode("-",$inputs['second_child_amount']);
          $paymentDuesInput[$i]['discount_sibling_amount']    = $discount_sibling_amount[1];
          $paymentDuesInput[$i]['discount_sibling_applied']   = $inputs['second_child_discount_to_form'];
        }
        
        $paymentDuesInput[$i]['payment_due_amount']   = $inputs['singlePayAmount'];
        $paymentDuesInput[$i]['payment_due_amount_after_discount']  = $inputs['grandTotal'];
        $paymentDuesInput[$i]['payment_status']                     = "paid";
        $paymentDuesInput[$i]['selected_order_sessions']= $getEstimateDetails[$i]['no_of_opted_classes'];
        $paymentDuesInput[$i]['start_order_date']=
                                                $insertDataToStudentClassTable['enrollment_start_date'];
        $paymentDuesInput[$i]['end_order_date']= $insertDataToStudentClassTable['enrollment_end_date'];
        $paymentDuesInput[$i]['payment_batch_amount']= $paymentDuesInput[$i]['selected_order_sessions']*$paymentDuesInput[$i]['each_class_cost'];
        $paymentDuesInput[$i]['tax']= $tax;
                                
        $sendPaymentDetailsToInsert = PaymentDues::createPaymentDues($paymentDuesInput[$i]);
                                
        if($i == 0){
          $sendPaymentMasterDetailsToInsert1 = 
                                 PaymentMaster::createPaymentMaster($sendPaymentDetailsToInsert);
                                        //** updating back to paymentDues **//
          $update_payment_due = PaymentDues::find($sendPaymentDetailsToInsert->id);
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

          if( isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs))
           && $inputs['paymentTypeRadio'] == "card"){
            $order['payment_mode']    = $inputs['paymentTypeRadio'];  
            $order['card_last_digit'] = $inputs['card4digits'];
            $order['card_type']       = $inputs['cardType'];
            $order['bank_name']       = $inputs['cardBankName'];
            $order['receipt_number']  = $inputs['cardRecieptNumber'];
          }elseif( isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs))
           && $inputs['paymentTypeRadio'] == "cheque"){
            $order['payment_mode']    = $inputs['paymentTypeRadio'];  
            $order['bank_name']       = $inputs['bankName'];
            $order['cheque_number']   = $inputs['chequeNumber'];
          }elseif( isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs))
           && $inputs['paymentTypeRadio'] == "cash"){
            $order['payment_mode']    = $inputs['paymentTypeRadio'];  
          }

            $order['payment_for']     = "enrollment";
            $order['payment_no']   = $sendPaymentMasterDetailsToInsert1['payment_no'];
            $order['payment_dues_id']   = $sendPaymentDetailsToInsert['id'];
            $order['amount'] = $inputs['singlePayAmount'];
            $order['order_status'] = "completed";
            $sendOrderDetailsToInsert = Orders::createOrder($order, $invoiceNo);
        }
      }
                            
                            
                    //** working on the payment_followups **//
                    
      if((count($batch_data[0]) + count($batch_data[1])) >= 15){
        // $retention['customer_id']     = $inputs['customerId'];
        // $retention['student_id'] = $insertDataToStudentClassTable['student_id'];
        $dataToretention = Retention::createRetention($sendPaymentDetailsToInsert);
                      //creating logs/followup for first payment
        $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
        $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
        $customer_log_data['franchisee_id']=Session::get('franchiseId');
        $customer_log_data['retention_id']=$dataToretention->id;
        $customer_log_data['followup_type']='RETENTION';
        $customer_log_data['followup_status']='REMINDER_CALL';
        $customer_log_data['comment_type']='VERYINTERESTED';

        $PaymentreminderDate=new carbon();
        $PaymentreminderDate=$PaymentreminderDate->createFromFormat('Y-m-d',$insertDataToStudentClassTable['enrollment_end_date']);
        $customer_log_data['reminderDate']=$PaymentreminderDate->toDateString();
        
        Comments::addSinglePayComment($customer_log_data);

        $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($sendPaymentDetailsToInsert,$final_payment_master_no);
                      //creating logs/followup for first payment
        //if(isset($payment_followup_data1)){
            $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
            $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
            $customer_log_data['franchisee_id']=Session::get('franchiseId');
            $customer_log_data['paymentfollowup_id'] = $payment_followup_data1->id;
            $customer_log_data['retention_id']='NULL';
            $customer_log_data['followup_type']='PAYMENT';
            $customer_log_data['followup_status']='REMINDER_CALL';
            $customer_log_data['comment_type']='VERYINTERESTED';

            $PaymentreminderDate=new carbon();
            $PaymentreminderDate=$PaymentreminderDate->createFromFormat('Y-m-d',$insertDataToStudentClassTable['enrollment_end_date']);
            $PaymentreminderDate->subDays(14);
            $customer_log_data['reminderDate']=$PaymentreminderDate->toDateString();
            
            Comments::addSinglePayComment($customer_log_data);
        }
    //  }
    

                
    //** checking if it is a 3 batch **//
    }elseif (count($getEstimateDetails) == 3) {
        $fianancialYearDates = Franchisee::getFinancialStartDates();
        $dataForThisYear = Franchisee::where('id', '=', Session::get('franchiseId'))
                                    ->where('financial_year_start_date', '=', $fianancialYearDates['start_date'])
                                    ->where('financial_year_end_date', '=', $fianancialYearDates['end_date'])
                                    ->get();

        if( count($dataForThisYear) > 0){
                $invoiceNo =  $dataForThisYear[0]['max_invoice'] + 1;
                $updateInvoice = Franchisee::updateInvoiceNumber($invoiceNo);
        }else{  
		$invoiceNo = '1';
                $data = Franchisee::updateFinancialYears($fianancialYearDates);
        }
                         
      for($i=0;$i<3;$i++){
        $batch_data[$i]=BatchSchedule::
                                where('batch_id','=',$getEstimateDetails[$i]['batch_id'])
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
        $singleBatchstartDate=
                      Carbon::createFromFormat('Y-m-d', $getEstimateDetails[$i]['enroll_start_date']);
        $studentClasses[$i]['enrollment_start_date'] = $getEstimateDetails[$i]['enroll_start_date'];
        $studentClasses[$i]['enrollment_end_date']   = $getEstimateDetails[$i]['enroll_end_date'];
        $singleBatchendDate=
                      Carbon::createFromFormat('Y-m-d', $getEstimateDetails[$i]['enroll_end_date']);
        $insertDataToStudentClassTable         = StudentClasses::addStudentClass($studentClasses[$i]);
                    
        $paymentDuesInput[$i]['student_id']        = $insertDataToStudentClassTable['student_id'];
                                //return Response::json(array('status'=>'success','inputs'=>$paymentDuesInput));
        $paymentDuesInput[$i]['customer_id']       = $inputs['customerId'];
        $paymentDuesInput[$i]['batch_id']          = $insertDataToStudentClassTable['batch_id'];
        $paymentDuesInput[$i]['class_id']          = $insertDataToStudentClassTable['class_id'];
        $paymentDuesInput[$i]['selected_sessions'] = $insertDataToStudentClassTable['selected_sessions'];
        $paymentDuesInput[$i]['seasonId']          = $insertDataToStudentClassTable['season_id'];
                                              
                                
        $paymentDuesInput[$i]['student_class_id']  = $insertDataToStudentClassTable['id'];
        $paymentDuesInput[$i]['each_class_cost']   = $getEstimateDetails[$i]['base_price'];
                                
        if($i==0){ 
        if(isset($inputs['membershipType']) && array_key_exists('membershipType',$inputs) && 
            $inputs['membershipType']!=''){

                                    //** create membership for customer **//
          $customerMembershipInput['customer_id']   = $getEstimateDetails[$i]['customer_id'];
          $customerMembershipInput['membership_type_id']      = $inputs['membershipType'];
          $customerMembershipDetails=CustomerMembership::addMembership($customerMembershipInput);

          $paymentDuesInput[$i]['membership_id']    = $customerMembershipDetails->id;
          $paymentDuesInput[$i]['membership_type_id']= $customerMembershipDetails->membership_type_id;
          $paymentDuesInput[$i]['membership_amount']          = $inputs['membershipAmount'];
          $temp=MembershipTypes::find($customerMembershipDetails->membership_type_id);
          $paymentDuesInput['membership_name']            =       $temp->description;
                                    
        }
        }
                                //** checking for discounts **//
        if($inputs['discountPercentage']!=''){
          $discount_amount                     =   explode("-",$inputs['discountTextBox']);
          $paymentDuesInput[$i]['discount_amount']            =   $discount_amount[1];
          $paymentDuesInput[$i]['discount_applied']           =   $inputs['discountPercentage'];
        }
        if( isset($inputs['second_class_discount_to_form']) && array_key_exists('second_class_discount_to_form', $inputs) &&  $inputs['second_class_discount_to_form']!=''){
                                    
          $discount_multipleclasses_amount         = explode("-",$inputs['second_class_amount']);
          $paymentDuesInput[$i]['discount_multipleclasses_amount'] = $discount_multipleclasses_amount[1];
          $paymentDuesInput[$i]['discount_multipleclasses_applied']= 
                                                        $inputs['second_class_discount_to_form'];
        }
        if(isset($inputs['second_class_discount_to_form']) && array_key_exists('second_child_discount_to_form', $inputs) && $inputs['second_child_discount_to_form']!=''){
                                    
          $discount_sibling_amount  = explode("-",$inputs['second_child_amount']);
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
        $paymentDuesInput[$i]['tax']                                = $tax;
                                
        $sendPaymentDetailsToInsert = PaymentDues::createPaymentDues($paymentDuesInput[$i]);
                                
                                /* inserting into payment Due table is completed for single pay[2batch] */
                                /* Working on preparing to payment master table for single pay [2 batch]*/
        if($i == 0){
          $sendPaymentMasterDetailsToInsert1   = PaymentMaster::createPaymentMaster($sendPaymentDetailsToInsert);
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

          if( isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs))
           && $inputs['paymentTypeRadio'] == "card"){
                                        $order['payment_mode']    = $inputs['paymentTypeRadio'];  
                                        $order['card_last_digit'] = $inputs['card4digits'];
                                        $order['card_type']       = $inputs['cardType'];
                                        $order['bank_name']       = $inputs['cardBankName'];
                                        $order['receipt_number']  = $inputs['cardRecieptNumber'];

          }elseif( isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs))
             && $inputs['paymentTypeRadio'] == "cheque"){
                                        $order['payment_mode']    = $inputs['paymentTypeRadio'];  
                                        $order['bank_name']       = $inputs['bankName'];
                                        $order['cheque_number']   = $inputs['chequeNumber'];

          }elseif(isset($inputs['paymentTypeRadio']) && (array_key_exists('paymentTypeRadio',$inputs))
           && $inputs['paymentTypeRadio'] == "cash"){
                                        $order['payment_mode']    = $inputs['paymentTypeRadio'];  
          }


          $order['payment_for']     = "enrollment";
          $order['payment_no']   = $sendPaymentMasterDetailsToInsert1['payment_no'];
          $order['payment_dues_id']   = $sendPaymentDetailsToInsert['id'];
          $order['amount'] = $inputs['singlePayAmount'];
          $order['order_status'] = "completed";
          $sendOrderDetailsToInsert = Orders::createOrder($order, $invoiceNo);

        }
                            
                                                            
      }
      //** working on the payment_followups **//
                    
      //if((count($batch_data[0]) + count($batch_data[1])+ count($batch_data[2])) >= 15){
        // $retention['customer_id']     = $inputs['customerId'];
        // $retention['student_id'] = $insertDataToStudentClassTable['student_id'];
        $dataToretention = Retention::createRetention($sendPaymentDetailsToInsert);
                      //creating logs/followup for first payment
        $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
        $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
        $customer_log_data['franchisee_id']=Session::get('franchiseId');
        $customer_log_data['retention_id']=$dataToretention->id;
        $customer_log_data['followup_type']='RETENTION';
        $customer_log_data['followup_status']='REMINDER_CALL';
        $customer_log_data['comment_type']='VERYINTERESTED';

        $PaymentreminderDate=new carbon();
        $PaymentreminderDate=$PaymentreminderDate->createFromFormat('Y-m-d',$insertDataToStudentClassTable['enrollment_end_date']);
        $PaymentreminderDate->subDays(14);
        $customer_log_data['reminderDate']=$PaymentreminderDate->toDateString();
        
        Comments::addSinglePayComment($customer_log_data);

        $payment_followup_data1=  PaymentFollowups::createPaymentFollowup($sendPaymentDetailsToInsert,$final_payment_master_no);
                      //creating logs/followup for first payment
        if(isset($payment_followup_data1)){
            $customer_log_data['customer_id']=$sendPaymentDetailsToInsert->customer_id;
            $customer_log_data['student_id']=$sendPaymentDetailsToInsert->student_id;
            $customer_log_data['franchisee_id']=Session::get('franchiseId');
            $customer_log_data['paymentfollowup_id'] = $payment_followup_data1->id;
            $customer_log_data['retention_id']='NULL';
            $customer_log_data['followup_type']='PAYMENT';
            $customer_log_data['followup_status']='REMINDER_CALL';
            $customer_log_data['comment_type']='VERYINTERESTED';

            $PaymentreminderDate=new carbon();
            $PaymentreminderDate=$PaymentreminderDate->createFromFormat('Y-m-d',$insertDataToStudentClassTable['enrollment_end_date']);
            $PaymentreminderDate->subDays(14);
            $customer_log_data['reminderDate']=$PaymentreminderDate->toDateString();
            
            Comments::addSinglePayComment($customer_log_data);
        }
     // }
    }
    DB::commit();
    
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
      $getTermsAndConditions = TermsAndConditions::where('franchisee_id', '=', Session::get('franchiseId'))->get();
      $franchisee_name=Franchisee::find(Session::get('franchiseId'));               
      $getCustomerName = Customers::select('customer_name','customer_lastname','customer_email')->where('id', '=', $paymentDueDetails[0]['customer_id'])->get();
                    //return Response::json(array($getCustomerName));
      $getStudentName = Students::select('student_name')->where('id', '=', $paymentDueDetails[0]['student_id'])->get();
      $paymentMode = Orders::where('payment_no', '=', $final_payment_master_no)->get();
      if($paymentDueDetails[0]['membership_type_id']!=0){
        $membership_data= MembershipTypes::find($paymentDueDetails[0]['membership_type_id']);
        $paymentDueDetails[0]['membership_type']=$membership_data->description;
      }
        $franchisee_name=Franchisee::find(Session::get('franchiseId'));

        if ($paymentDueDetails[0]['tax_percentage'] <= 0) {
          $tax_data[0]['tax_percentage'] = 0;
        } else {
          $tax_data=TaxParticulars::where('franchisee_id','=',Session::get('franchiseId'))->get();
        }
        if (Session::get('franchiseId') == 11) {
          $tax_data[0]['tax_particular'] = 'VAT';
        } 
        $data = compact('totalSelectedClasses', 'getBatchNname','tax_data','franchisee_name',
                        'getSeasonName', 'selectedSessionsInEachBatch', 'classStartDate','franchisee_name',
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
    }
    }catch (\Exception $e) {
      DB::rollback();
      return Response::json(array("status"=>"failed"));
    }
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
		$fianancialYearDates = Franchisee::getFinancialStartDates();
    $dataForThisYear = Franchisee::where('id', '=', Session::get('franchiseId'))
                                ->where('financial_year_start_date', '=', $fianancialYearDates['start_date'])
                                ->where('financial_year_end_date', '=', $fianancialYearDates['end_date'])
                                ->get();

  	if( count($dataForThisYear) > 0){
          	$invoiceNo =  $dataForThisYear[0]['max_invoice'] + 1;
          	$data = Franchisee::updateInvoiceNumber($invoiceNo);
  	}else{
          	$invoiceNo = '1';
          	$data = Franchisee::updateFinancialYears($fianancialYearDates);
  	}
    if($inputs['taxAmount'] === '0') {
      $inputs['taxPercentage'] = 0;
    }

    if($inputs['remainingAmount'] >=0){
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
        if(isset($customerMembershipData)){
          $addBirthday['membership_id']=$customerMembershipData->id;
          $membership_data=  MembershipTypes::find($customerMembershipData->membership_type_id);
          $addBirthday['membership_amount']=$membership_data->fee_amount;
        }
        $addBirthday['taxpercent']=$inputs['taxPercentage'];  
        if($inputs['remainingAmount']!='0'){
            $addBirthday['payment_type']="bipay";
        }else{
            $addBirthday['payment_type']="singlepay";
        }
        $firstpayment=PaymentDues::createBirthdaypaymentFirstdues($addBirthday);
        if($inputs['remainingAmount']!='0'){
        $addPaymentDues= PaymentDues::createBirthdaypaymentdues($addBirthday);
        }
        $addBirthdayOrder = Orders::createBOrder($addBirthday,$firstpayment,$taxAmtapplied,$inputs,$invoiceNo); 
    }
    
    //$addPaymentremainder= PaymentReminders::addReminderDates($addBirthday);
    
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
    
    $inputs=Input::all();
    $batchId       = $inputs['batchId'];
    $selectedDate  = $inputs['selectedDate'];
    $studentsByBatchId = StudentClasses::getStudentByBatchId($batchId, $selectedDate);
    if($studentsByBatchId){
      $attendanceArray = array();
      $i = 0;
      foreach ($studentsByBatchId as $studentAttendance){
        $attendanceArray[$i]['studentName'] = $studentAttendance->Students->student_name;
                                $attendanceArray[$i]['student_classes_id']= $studentAttendance->id;
                                $attendanceArray[$i]['introvisit_id']= $studentAttendance->introvisit_id;
                                if($studentAttendance->status==='makeup'){
                                    $attendanceArray[$i]['studentName']=$attendanceArray[$i]['studentName'].' [Makeup]';
                                }elseif($studentAttendance->status==='introvisit'){
                                    $attendanceArray[$i]['studentName']=$attendanceArray[$i]['studentName'].' [Introvisit]';
                                }
                                    
        $attendanceArray[$i]['studentId']   = $studentAttendance->Students->id;
        $studentAttendanceRecord = Attendance::getDaysAttendanceForStudent($studentAttendance->Students->id, $batchId,  $selectedDate);
        $enrollmentDates = StudentClasses::where('id','=',$studentAttendance->id)
                                        ->select('enrollment_start_date','enrollment_end_date','selected_sessions')
                                        ->get();
        $attendanceArray[$i]['enrollment_start_date'] = $enrollmentDates[0]['enrollment_start_date'];
        $attendanceArray[$i]['enrollment_end_date'] = $enrollmentDates[0]['enrollment_end_date']; 
        $attended_classes = Attendance::where('student_classes_id','=',$studentAttendance->id)
                                      ->where('batch_id','=',$inputs['batchId'])
                                      ->count();


        $attendanceArray[$i]['attended_classes'] = $attended_classes;
        $remaining = $enrollmentDates[0]['selected_sessions'] - $attended_classes;
        $attendanceArray[$i]['remaining_classes'] = $remaining > 0 ? $remaining : 0;  
       
        $end = new Carbon();
        $end = $end->createFromFormat('Y-m-d',$attendanceArray[$i]['enrollment_end_date']);
        $end = $end->subDays(7);
        $attendanceArray[$i]['end'] = $end->toDateString();

        if($studentAttendanceRecord){
          $attendanceArray[$i]['isAttendanceEntered'] = 'yes';
          $attendanceArray[$i]['attendanceStatus'] = $studentAttendanceRecord->status;
        }else{
          $attendanceArray[$i]['isAttendanceEntered'] = 'no';
        }
        $i++;
      }
      return Response::json(array("status"=>"success", 'result'=>$attendanceArray));
    }
    return Response::json(array("status"=>"failed"));
    
  }
  
  
  
  public function addStudentAttendance(){
    
    $inputs = Input::all();
               // return Response::json(array("status"=>$inputs));
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
                                $attendanceData->introvisit_id   = $inputs['introvisit_id'.$i];
        $attendanceData->status          = $inputs['attendance_for_user'.$i];
                                $attendanceData->student_classes_id = $inputs['student_class_id'.$i];
                                if($inputs['attendance_for_user'.$i]==='EA'){
                                    //** Add description for Excused Absent **// 
                                    $attendanceData->description_absent =$inputs['description_user_'.$i];
                                    // creating the retention id
                                    $getcustomerdetails=Students::find($inputs['student_'.$i]);
                                    $retentionData['customer_id']=$getcustomerdetails->customer_id;
                                    $retentionData['student_id']=$inputs['student_'.$i];
                                    $rdata=Retention::createRetention($retentionData);
                                    //to create followup
                                    
                                    $customer_logdata['customerId']=$rdata->customer_id;
                                    $customer_logdata['student_id']=$rdata->student_id;
                                    $customer_logdata['followupType']='RETENTION';
                                    $customer_logdata['commentStatus']='REMINDER_CALL';
                                    $customer_logdata['retention_id']=$rdata->id;
                                    $customer_logdata['commentText']=$inputs['description_user_'.$i];
                                    $customer_logdata['commentType']='INTERESTED';       
                                    if($inputs['reminderdate_user_'.$i]!=""){
                                        //create followup
                                        $customer_logdata['reminderDate']=$inputs['reminderdate_user_'.$i];
                                    }
                                    $customer_log_data=  Comments::addComments($customer_logdata);
                                    
                                }else if($inputs['attendance_for_user'.$i]==='A'){
                                    //** Add description for Excused Absent **// 
                                //    $attendanceData->description_absent =$inputs['description_user_absent_'.$i];
                                    // creating the retention id
                                    $getcustomerdetails=Students::find($inputs['student_'.$i]);
                                    $retentionData['customer_id']=$getcustomerdetails->customer_id;
                                    $retentionData['student_id']=$inputs['student_'.$i];
                                    $rdata=Retention::createRetention($retentionData);
                                    //to create followup
                                    
                                    $customer_logdata['customerId']=$rdata->customer_id;
                                    $customer_logdata['student_id']=$rdata->student_id;
                                    $customer_logdata['followupType']='RETENTION';
                                    $customer_logdata['commentStatus']='REMINDER_CALL';
                                    $customer_logdata['retention_id']=$rdata->id;
                                    $customer_logdata['commentText']='ABSENT';
                                    $customer_logdata['commentType']='INTERESTED';       
                                    if($inputs['attendanceDate_'.$i]!=""){
                                        //create followup
                                  //      $customer_logdata['reminderDate']=$inputs['reminderdate_user_absent_'.$i];
                                    }
                                    $customer_log_data=  Comments::addComments($customer_logdata);
                                    
                                }else{
                                    $attendanceData->description_absent ='';
                                }
                                
        $attendanceData->save();
        
      }
      
    }
    return Response::json(array("status"=>"success"));
  }
  
  
  public function addIntroVisit(){
    
    $inputs = Input::all();
                
    
            if(
                   StudentClasses::where('student_id','=',$inputs['studentIdIntroVisit'])
                                   ->where('season_id','=',$inputs['seasonId'])
                                   ->where('class_id','=',$inputs['eligibleClassesCbx'])
                                   ->where('batch_id','=',$inputs['introbatchCbx'])
                                   ->whereDate('enrollment_start_date','<=',date('Y-m-d',strtotime($inputs['introVisitTxtBox'])))
                                   ->whereDate('enrollment_end_date','>=',date('Y-m-d',strtotime($inputs['introVisitTxtBox'])))
                                   ->exists()
               ){
                      
                      return Response::json(array('status'=>'exists'));
                }else{
    
  
    $result = IntroVisit::addSchedule($inputs);
    $student_class_input['studentId']=$inputs['studentIdIntroVisit'];
                $student_class_input['seasonId']=$inputs['seasonId'];
                $student_class_input['introvisit_id']=$result->id;
                $student_class_input['classId']=$inputs['eligibleClassesCbx'];
                $student_class_input['enrollment_start_date']=date('Y-m-d',strtotime($inputs['introVisitTxtBox']));
                $student_class_input['enrollment_end_date']=date('Y-m-d',strtotime($inputs['introVisitTxtBox']));
                $student_class_input['selected_sessions']=1;
                $student_class_input['status']='introvisit';
                $student_class_input['batchId']=$inputs['introbatchCbx'];
                $student_class_data=  StudentClasses::addStudentClass($student_class_input);
    
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
  }
  
  public function editIntroVisit(){
  
    $inputs = Input::all();
  
                    if(($inputs['iveditAction']!=' ')){
                    $introvisit_data_make_reminder_null= Comments::where('introvisit_id','=',$inputs['iv_id'])
                                               ->update(array('reminder_date'=>Null,));
                    $introvisit=Comments::where('introvisit_id','=',$inputs['iv_id'])
                                               ->orderBy('id','DESC')
                                               ->first();
                    if($inputs['reschedule_date'] !=''){
                      $re_date = $inputs['reschedule_date']; 
                      $reschedule_date = date("Y-m-d H:i:s", strtotime($re_date));
                      //return $resc_date;
                      $update  = IntroVisit::where('id', '=', $inputs['iv_id'])
                                             ->update(['iv_date'=>$reschedule_date]);
                      $update_att = StudentClasses::where('introvisit_id','=',$inputs['iv_id'])
                                                  ->update(['enrollment_start_date'=>$reschedule_date,'enrollment_end_date'=>$reschedule_date]);

                      
                    }
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
                              
                                
              }else{
                $commentText='';
              } 
                        
                        
                        //insert new followuphere
                           
      $commentsInput['customerId']     = $introvisit['customer_id'];
                        $commentsInput['student_id']     = $introvisit['student_id'];
                        $commentsInput['introvisit_id']  = $introvisit['introvisit_id'];
                       

                     
                        $commentsInput['followupType']  = $introvisit['followup_type'];
                        $commentsInput['commentStatus']= $inputs['ivstatus'];
                        $commentsInput['commentType']   = $inputs['iveditAction'];
			/*  if($inputs['lead_status'] == 'Yes'){  
                        	$commentsInput['LeadStatus'] = 'very_interested';
			}else if($inputs['lead_status'] == 'May be'){
				$commentsInput['LeadStatus'] = 'interested';
			}else {
				$commentsInput['LeadStatus'] = 'not_interested';
			}  */
		//	return $commentsInput['leadStatus'];  
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
            
                        
                            if($inputs['followup_status'] != 'CLOSE_CALL'){
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
        
        public function getExcusedabsentStudentsByBatchId(){
            if(Auth::check()){
            $inputs=Input::all();
            $season_data= Seasons::where('franchisee_id','=',Session::get('franchiseId'))->Orderby('id','desc')->get();
            $classes_data= Classes::where('franchisee_id','=',Session::get('franchiseId'))->get();
            return Response::json(array('status'=>'success','data'=>Attendance::getEAbybatchandStudentId($inputs['batch_id'],$inputs['student_id']),
                                        'season_data'=>$season_data,'classes_data'=>$classes_data));
            }
        }
        
        public function transferkid(){
            if(Auth::check()){
                $inputs=Input::all();
                // getting the batch_data
                $batch_data=BatchSchedule::where('season_id','=',$inputs['season_id'])
                                            ->where('batch_id','=',$inputs['batch_id'])
                                            ->where('schedule_date','>=',$inputs['start_date'])
                                            ->where('franchisee_id','=',Session::get('franchiseId'))
                                            ->where('holiday','!=',1)  
                                            ->take($inputs['no_of_class'])
                                            ->get();
                if(
                    StudentClasses::where('student_id','=',$inputs['student_id'])
                                            ->where('batch_id','=',$inputs['batch_id']) 
                                            ->where('enrollment_start_date','>=',$batch_data[0]['schedule_date'])
                                            ->where('enrollment_end_date','<=',$batch_data[count($batch_data)-1]['schedule_date'])
                                            ->exists()
                   ){
                    return Response::json(array('status'=>'exists'));
                    
                }
                
                //creating new class 
                   $newClassInput['studentId']=$inputs['student_id'];
                   $newClassInput['seasonId']=$inputs['season_id'];
                   $newClassInput['classId']=$inputs['class_id'];
                   $newClassInput['batchId']=$inputs['batch_id'];
                   $newClassInput['enrollment_start_date']=$batch_data[0]['schedule_date'];
                   $newClassInput['enrollment_end_date']=$batch_data[count($batch_data)-1]['schedule_date'];
                   $newClassInput['selected_sessions']=count($batch_data);
                   $newClassInput['status']='transferred_class';
                   $newstudent_class=  StudentClasses::addStudentClass($newClassInput);
                //updating changes to student_class table   
                
                //getting attendance data
                $attendance_date=Attendance::where('batch_id','=',$inputs['oldbatch_id'])
                                                ->where('student_id','=',$inputs['student_id'])
                                                ->max('attendance_date');
                $success=0;
                if(($attendance_date!='') && ($attendance_date!=null) ){
                   // getting the latet attendance
                   $attendance_class_data=Attendance::where('batch_id','=',$inputs['oldbatch_id'])
                                                ->where('student_id','=',$inputs['student_id'])
                                                ->where('attendance_date','=',$attendance_date)
                                                ->first();
                   $student_future_class =StudentClasses::where('batch_id','=',$inputs['oldbatch_id'])
                                                 ->where('student_id','=',$inputs['student_id'])
                                                 ->where('enrollment_start_date','>',$attendance_date)
                                                 ->where('enrollment_end_date','>',$attendance_date)
                                                 ->whereIn('status',array('enrolled','transferred_class','makeup'))
                                                 ->update(array('status'=>'transferred_to_other_class',
                                                                'transferred_student_class_id'=>$newstudent_class->id));
                   $student_active_class =StudentClasses::where('id','=',$attendance_class_data['student_classes_id'])
                                                  ->whereIn('status',array('enrolled','transferred_class','makeup'))
                                                  ->update(array('status'=>'transferred_to_other_class',
                                                                'transferred_student_class_id'=>$newstudent_class->id));
                   //updating enrollemnt_end_date to delete rest of the classes
                   $student_class_detail=StudentClasses::find($attendance_class_data['student_classes_id']);
                   $student_class_detail->expected_enrollment_end_date=$student_class_detail->enrollment_end_date;
                   $student_class_detail->enrollment_end_date=$attendance_date;
                   $student_class_detail->save();
                   $success=1;
                }else{
                    // no attendance found update all rows of batch
                    
                $student_future_class=StudentClasses::where('batch_id','=',$inputs['oldbatch_id'])
                                                ->where('student_id','=',$inputs['student_id'])
                                                //->where('enrollment_start_date','>',$inputs['start_date'])
                                                //->where('enrollment_end_date','>',$inputs['start_date'])
                                                ->whereIn('status',array('enrolled','transferred_class','makeup'))
                                                ->update(array('status'=>'transferred_to_other_class',
                                                        'transferred_student_class_id'=>$newstudent_class->id));
                $success=1;            
                }
                
                if($success){
                    //creating followup
                    $followup_input['customerId']=$inputs['customer_id'];
                    $followup_input['followupType']='ENROLLMENT';
                    $followup_input['student_id']=$inputs['student_id'];
                    $followup_input['commentStatus']='ACTIVE/SCHEDULED';
                    $followup_input['commentText']=$inputs['description'];
                    $followup_input['commentType']='VERYINTERESTED';
                    $create_followup=  Comments::addComments($followup_input);
                    
                    
                    
                }
                
                return Response::json(array('status'=>'success','data'=>$inputs));
            }
            
        }
        
        
        public function getUniqueSchoolNames(){
            if(Auth::check()){
                return Response::json(array('status'=>'success','data'=>Students::where('school','!=','')->where('franchisee_id','=',Session::get('franchiseId'))->distinct('school')->select('school')->get()));
            }
        }
        
        
        public function deleteIVdata(){
            if((Auth::check()) && (Session::get('userType')=='ADMIN') ){
                $inputs=Input::all();
                $deleted_data=0;
                if(StudentClasses::where('student_id','=',$inputs['student_id'])
                                   ->where('introvisit_id','<>',0)
                                   ->exists()
                        ){
                    StudentClasses::where('student_id','=',$inputs['student_id'])
                                   ->where('introvisit_id','<>',0)
                                   ->delete();
                    $deleted_data=1;
                }
                if(IntroVisit::where('student_id','=',$inputs['student_id'])
                               ->where('franchisee_id','=',Session::get('franchiseId'))
                               ->exists()
                        ){
                        $introvisit_data=Introvisit::where('student_id','=',$inputs['student_id'])
                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                    ->get();
                        IntroVisit::where('student_id','=',$inputs['student_id'])
                               ->where('franchisee_id','=',Session::get('franchiseId'))
                               ->delete();
                        $deleted_data=1;
                        for($i=0;$i<1;$i++){
                            $intro_data=$introvisit_data[$i];
                            if(Comments::where('introvisit_id','=',$intro_data->id)
                                     ->exists()
                               ){
                                Comments::where('introvisit_id','=',$intro_data->id)
                                         ->update(array('reminder_date'=>null));
                               }
                            $comment=  new Comments();
                            $comment->customer_id=   $intro_data->customer_id;
                            $comment->student_id= $intro_data->student_id;
                            $comment->franchisee_id= $intro_data->franchisee_id;
                            $comment->log_text= "Introvisits are deleted";
                            $comment->comment_type="ACTION_LOG";
                            $comment->followup_type="INQUIRY";
                            $comment->created_by    = Session::get('userId');
                            $comment->created_at    = date("Y-m-d H:i:s");
                            $comment->save();
                                
                        }
               }
               return Response::json(array('status'=>'success','deleted_data'=>$deleted_data));
            }else{
               return Response::json(array('status'=>'failure')); 
            }
            
            
        }
        
        public function deletebirthdaydata(){
            if((Auth::check()) && (Session::get('userType')=='ADMIN') ){
                $inputs=Input::all();
                $deleted=0;
                    
                if(Orders::where('student_id','=',$inputs['student_id'])
                           ->where('payment_for','=','birthday')
                           ->exists()
                   ){
                Orders::where('student_id','=',$inputs['student_id'])
                            ->where('payment_for','=','birthday')
                           ->delete();
                    $deleted=1;
                }
               
                if(PaymentDues::where('student_id','=',$inputs['student_id'])
                                ->where('franchisee_id','=',Session::get('franchiseId'))
                                ->where('payment_due_for','=','birthday')
                                ->exists()
                   ){
                    PaymentDues::where('student_id','=',$inputs['student_id'])
                                ->where('franchisee_id','=',Session::get('franchiseId'))
                                ->where('payment_due_for','=','birthday')
                                ->delete();
                    $deleted=1;
                   }
                if(BirthdayParties::where('student_id','=',$inputs['student_id'])
                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                    ->exists()        
                        ){
                    $deleted=1;
                    $birthday_data=BirthdayParties::where('student_id','=',$inputs['student_id'])
                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                    ->get();
                    BirthdayParties::where('student_id','=',$inputs['student_id'])
                                    ->where('franchisee_id','=',Session::get('franchiseId'))
                                    ->delete();
                    
                    for($i=0;$i<1;$i++){
                            $birth_data=$birthday_data[$i];
                            if(Comments::where('birthday_id','=',$birth_data->id)
                                     ->exists()
                               ){
                                Comments::where('introvisit_id','=',$birth_data->id)
                                         ->update(array('reminder_date'=>null));
                               }
                            $comment=  new Comments();
                            $comment->customer_id=   $birth_data->customer_id;
                            $comment->student_id= $birth_data->student_id;
                            $comment->franchisee_id= $birth_data->franchisee_id;
                            $comment->log_text= "Birthday data are deleted";
                            $comment->comment_type="ACTION_LOG";
                            $comment->followup_type="INQUIRY";
                            $comment->created_by    = Session::get('userId');
                            $comment->created_at    = date("Y-m-d H:i:s");
                            $comment->save();
                                
                        }
                }
                
                return Response::json(array('status'=>'success','deleted_data'=>$deleted));
                   
            }else{
                return Response::json(array('status'=>'failure'));
            }
        }
        
        
        public function deleteenrollmentdata(){
            if((Auth::check()) && (Session::get('userType')=='ADMIN') ){
                $inputs=Input::all();
                $deleted=0;
                if(Orders::where('student_id','=',$inputs['student_id'])
                           ->where('payment_for','=','enrollment')
                           ->exists()
                   ){
                Orders::where('student_id','=',$inputs['student_id'])
                            ->where('payment_for','=','enrollment')
                           ->delete();
                    $deleted=1;
                }
                
                if(PaymentMaster::where('student_id','=',$inputs['student_id'])
                                  ->exists()
                        ){
                    PaymentMaster::where('student_id','=',$inputs['student_id'])
                                  ->delete();
                }
                
                if(PaymentDues::where('student_id','=',$inputs['student_id'])
                                ->where('franchisee_id','=',Session::get('franchiseId'))
                                ->where('payment_due_for','=','enrollment')
                                ->exists()
                   ){
                    PaymentDues::where('student_id','=',$inputs['student_id'])
                                ->where('franchisee_id','=',Session::get('franchiseId'))
                                ->where('payment_due_for','=','enrollment')
                                ->delete();
                    $deleted=1;
                   }
                if(PaymentFollowups::where('student_id','=',$inputs['student_id'])
                                     ->exists()
                        ){
                    PaymentFollowups::where('student_id','=',$inputs['student_id'])
                                      ->delete();
                    $deleted=1;
                }
                if(PaymentReminders::where('student_id','=',$inputs['student_id'])->exists()){
                    PaymentReminders::where('student_id','=',$inputs['student_id'])->delete();
                    $deleted=1;
                }
                if(StudentClasses::where('student_id','=',$inputs['student_id'])
                                   ->where('introvisit_id','=',0)
                                   ->exists()
                        ){
                    
                    StudentClasses::where('student_id','=',$inputs['student_id'])
                                   ->where('introvisit_id','=',0)
                                   ->delete();
                    
                    
                    if(Comments::whereNotNull('paymentfollowup_id')
                                 ->where('student_id','=',$inputs['student_id'])
                                 ->exists()
                      ){
                       Comments::whereNotNull('paymentfollowup_id')
                                 ->where('student_id','=',$inputs['student_id'])
                                 ->update(array('reminder_date'=>NULL));
                    
                            
                      }
                    $deleted=1;
                }
                if($deleted){
                    $comment= new comments();
                    $comment->student_id=$inputs['student_id'];
                    $comment->customer_id=$inputs['customer_id'];
                    $comment->franchisee_id= Session::get('franchiseId');
                    $comment->log_text= "Enrollment data are deleted";
                    $comment->comment_type="ACTION_LOG";
                    $comment->followup_type="INQUIRY";
                    $comment->created_by    = Session::get('userId');
                    $comment->created_at    = date("Y-m-d H:i:s");
                    $comment->save();
                }
                   
                
                return Response::json(array('status'=>'success','deleted_data'=>$deleted));
            }else{
                return Response::json(array('status'=>'failure'));
            }
        }
        
  
  public function getAttendanceDetails() {
        $inputs=  Input::all();

        $class_dates = StudentClasses::where('id', '=', $inputs['class_id'])
                                     ->select('id','student_id','enrollment_start_date','enrollment_end_date','batch_id','selected_sessions')
                                     ->get();
              $batch_id = $class_dates[0]->batch_id;
              $student_id = $class_dates[0]->student_id;
            $student_classes_id = $class_dates[0]->id;
        $attendance_date = [];               
        $dates = $class_dates[0]['enrollment_start_date'];
        $increment['class_dates'] = date('Y-m-d', strtotime($dates));
        for($i=0; $i < ($class_dates[0]['selected_sessions']); $i++) {
          $attendance_date[] = $increment;
          $increment['class_dates'] = date('Y-m-d', strtotime('+1 week', strtotime($dates)));
          
          $dates = $increment['class_dates'];
        }
        // return $attendance_date;
        $present_dates=[];
            $absent_dates=[];
            $ea_dates=[];
            $makeup=[];

        foreach ($attendance_date as $key => $value) {

            $attendance = Attendance::where('student_classes_id', '=', $inputs['class_id'])
                                  ->where('attendance_date', $value)
                                  ->select('student_classes_id','student_id','batch_id','status', 'makeup_class_given','attendance_date')
                                  ->get();
            
          if(isset($attendance[0]))
          {
             
            
           switch ($attendance[0]->status) {
             case 'P':
               $present_dates1 = $attendance[0]->attendance_date;
               $present_dates[] = date('d-M-y',strtotime($present_dates1));
               $attendance_date[$key]['status'] = 'P'; 

               break;
             case 'A':
               $present_dates1 = $attendance[0]->attendance_date;
               $absent_dates[] = date('d-M-y',strtotime($present_dates1));
               $attendance_date[$key]['status'] = 'A';
               break;

              case 'EA':
                $makeup1 = $attendance[0]->attendance_date;
                if($attendance[0]->makeup_class_given == 1){
                  $makeup[] = date('d-M-y',strtotime($makeup1));
                
                $attendance_date[$key]['status'] = 'MK';
              }
                else{ 
                  $ea_dates[] = date('d-M-y',strtotime($makeup1));
                  $attendance_date[$key]['status'] = 'EA';
                }
               break;
              
                

           }
          }
          else
          {
            if($attendance_date[$key]['class_dates'] < date('Y-m-d'))
              $attendance_date[$key]['status'] = 'NMP';
            else
              $attendance_date[$key]['status'] = 'NMF';
          }
          $attendance['present_dates'] = $present_dates;
          $attendance['absent_dates'] = $absent_dates;
          $attendance['ea_dates'] = $ea_dates;
          $attendance['makeup'] = $makeup;
        }
        //return $attendance;
        
        return Response::json(array('status'=>'success', 'data'=> $attendance_date , 'all_dates' => $attendance_date, 'batch_id' => $batch_id, 'student_id' => $student_id, 'student_classes_id' => $student_classes_id ) );

  }  
  public function insertPastAttendance() {
    $inputs=  Input::all();
    $absent_dates = json_decode($inputs['absent']);
    $present_dates = json_decode($inputs['present']);
    $ea_dates = json_decode($inputs['ea']);
    $batch_id = $inputs['batch_id'];
    $student_id = $inputs['student_id'];
    $student_classes_id = $inputs['student_classes_id'];
 

      foreach ($absent_dates as $key => $value) {
        $insert_absent_data = Attendance::where('franchise_id', '=', Session::get('franchiseId'))
                                        ->insert(
                                         array(['batch_id' => $batch_id, 
                                              'student_id' => $student_id,
                                              'student_classes_id' => $student_classes_id,
                                              'attendance_date' => date('Y-m-d', strtotime($value)),
                                              'status' => 'A',
                                              'description_absent' => 'not informed',
                                              'makeup_class_given' => null])

                                            );
      }
    
      foreach ($present_dates as $key => $value) { 
        $insert_absent_data = Attendance::where('franchise_id', '=', Session::get('franchiseId'))
                                        ->insert(
                                         array(['batch_id' => $batch_id, 
                                              'student_id' => $student_id,
                                              'student_classes_id' => $student_classes_id,
                                              'attendance_date' => date('Y-m-d', strtotime($value)),
                                              'status' => 'P',
                                              'makeup_class_given' => null])

                                            );
      }
    
    
      foreach ($ea_dates as $key => $value) { 
        $insert_absent_data = Attendance::where('franchise_id', '=', Session::get('franchiseId'))
                                        ->insert(
                                         array(['batch_id' => $batch_id, 
                                              'student_id' => $student_id,
                                              'student_classes_id' => $student_classes_id,
                                              'attendance_date' => date('Y-m-d', strtotime($value)),
                                              'status' => 'EA',
                                              'description_absent' => 'not informed',
                                              'makeup_class_given' => null])

                                            );
      }
      
      $batch_name = Batches::where('id', '=', $inputs['batch_id'])
                          ->select('batch_name')
                          ->get();
      $enrollment = StudentClasses::where('student_id','=',$inputs['student_id'])
                                  ->where('id','=',$inputs['student_classes_id'])
                                  ->selectRaw('id,enrollment_start_date as startDate,enrollment_end_date as endDate')
                                  ->get();
      
      $data = new stdClass();
      $data->batch_name = $batch_name;
      $data->class_id = $enrollment[0]['id'];
      $data->start_date = $enrollment[0]['startDate'];
      $data->end_date = $enrollment[0]['endDate'];

      return Response::json(array('status'=>'success','data'=>$data));

  }

  public static function checkSecondSibling () {
    $inputs = Input::all();
    $secondChild = Students::where('id', '=', $inputs['student_id'])->get();
    $customer = PaymentDues::where('customer_id', '=', $secondChild[0]['customer_id'])
                        ->orderBy('created_at', 'ASC')
                        ->get();
    if (count($customer) > 1) {
      $data = $customer[0]['id'];
    } else {
      $data = 0;
    }
    return Response::json(array('status'=>'success','data'=>$data));
  }

  public function enrollYard(){
	$inputs = Input::all();
	$presentDate = Carbon::now();
	$typeOfClass = $inputs['typeOfClass'];
	$no_of_classes = $inputs['NoOfWeeksForSummer'] - 1;
	$totalAmount = $inputs['amountForSummer'] * $inputs['NoOfWeeksForSummer'];
	$end_date = date('Y-m-d', strtotime('+'.$no_of_classes.' week', strtotime($inputs['startDateForSummer'])));
	$discount_amount = ($inputs['discountPercentageForSummer']/100)*$totalAmount;
	$customerId = Students::where('franchisee_id', '=', Session::get('franchiseId'))
				->where('id','=',$inputs['studentId'])
				->select('customer_id')
				->get();
	 $getPayment_no = PaymentMaster::selectRaw('max(payment_no) as payment_no')	
						 ->get();
	 $payment_no = $getPayment_no[0]->payment_no + 1;

	 $userId =  Session::get('userId');

	 $data = new PaymentDues();
         $data->franchisee_id = Session::get('franchiseId');
         $data->customer_id = $customerId[0]->customer_id;
         $data->student_id = $inputs['studentId'];
         $data->payment_due_for = $inputs['typeOfClass'];
         $data->each_class_amount = $inputs['amountForSummer'];
         $data->payment_due_amount = $totalAmount;
         $data->payment_due_amount_after_discount =  $inputs['totalAmountForSummer'];
         $data->discount_applied = $inputs['discountPercentageForSummer'];
         $data->tax_percentage = $inputs['taxPercentageForSummer'];
         $data->payment_type = 'singlepay';
         $data->payment_status = 'paid';
         $data->payment_no = $payment_no;
         $data->selected_sessions = $inputs['NoOfWeeksForSummer'];
	 $data->selected_order_sessions = $inputs['NoOfWeeksForSummer'];
         $data->start_order_date = $inputs['startDateForSummer'];
         $data->end_order_date = $end_date;
         $data->created_by = $userId;
         $data->created_at = $presentDate;
         $data->updated_at = $presentDate;
         $data->save();

	$getInvoiceId = Orders::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->max('invoice_id');
         $invoice_id = $getInvoiceId + 1;
	$invoiceNo = Franchisee::invoiceForMembership();
        $invoiceFormat = Orders::invoiceFormat($invoiceNo);
	$getPaymentDuesId = PaymentDues::where('franchisee_id', '=', Session::get('franchiseId'))
				       ->where('student_id','=',$inputs['studentId'])
				       ->where('payment_due_for','=',$typeOfClass)
				       ->where('start_order_date','=',$inputs['startDateForSummer'])
				       ->get();
	$insertIntoOrdersTable = Orders::where('franchisee_id', '=', Session::get('franchiseId'))
				       ->insert(
						array(
						      ['franchisee_id' => Session::get('franchiseId'),
							'invoice_id' => $invoice_id,
							'invoice_format' => $invoiceFormat,
							'customer_id' => $customerId[0]->customer_id,
							'student_id' => $inputs['studentId'],
							'payment_for' => $typeOfClass,
							'payment_dues_id' => $data['id'],
							'payment_mode' => 'cash',
							'amount' => $inputs['totalAmountForSummer'],
							'payment_no' => $payment_no,
							'tax_percentage' => $inputs['taxPercentageForSummer'],
							'created_by' => $userId,
						        'created_at' => $presentDate,
							'updated_at' => $presentDate
						      ]) 

						); 
	$getOrdersId = Orders::where('franchisee_id','=', Session::get('franchiseId'))
			     ->where('student_id','=',$inputs['studentId'])
			     ->where('payment_for','=',$typeOfClass)
			     ->where('payment_no','=',$payment_no)
			     ->get();

	$insertIntoPaymentMaster = PaymentMaster::insert(
						         array([
								'customer_id' => $customerId[0]->customer_id,
								'student_id' => $inputs['studentId'],
								'payment_no' => $payment_no,
								'payment_due_id' => $data['id'],
								'order_id' => $getOrdersId[0]->id,
								'created_by' => $userId,
								'created_at' => $presentDate,
								'updated_at' => $presentDate
							])

				);

	    if($data){
		return Response::json(array('status'=>'success'));

            }else{
                return Response::json(array('status'=>'failure'));
            } 
		
	
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
