<?php
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
class CustomersController extends \BaseController {
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		if (Auth::check ()) {
			
			$currentPage = "MEMBERS_LIST";
			$mainMenu = "CUSTOMERS_MAIN";
			if(CustomerMembership::count()){
			$customers = Customers::getAllCustomerMembersByFranchiseeId ( Session::get ( 'franchiseId' ) );
                        }
			$provinces = Provinces::getProvinces ( "IN" );
			$viewData = array (
					'customers',
					'currentPage',
					'mainMenu',
					'provinces' 
			);
			return View::make ( 'pages.customers.memberslist', compact ( $viewData ) );
		} else {
			return Redirect::action('VaultController@logout');
		}
	}
	
        public function getNonMembersList(){
            	if (Auth::check ()) {
			
			$currentPage = "PROSPECTUS_LIST";
			$mainMenu = "CUSTOMERS_MAIN";
			$followUpsArray = array();
                        if(CustomerMembership::count()){
			    $customers = Customers::getAllCustomerNonMembersByFranchiseeId ( Session::get ( 'franchiseId' ) );
                        }else{
                            $customers=Customers::where('franchisee_id','=',Session::get ( 'franchiseId' ))->get();    
                        }
                        $hotLeads = Customers::getHotLeadsForProspects();
                        $openLeads =  Customers::getOpenLeadsForProspects();
			$follow_ups = Customers::FollowupSForProspects();  
			foreach($follow_ups as $key=> $value) {
			    $followUpsArray[$value['customer_id']] = array('reminder_date'=> $value['reminder_date'],
									'followup_type'=> $value['followup_type']	 
							             );
			}
	                
			foreach($customers as $key=> $value) {
			    $customers[$key]['reminder_date'] = '';
			    $customers[$key]['followup_type'] = '';
			    if(array_key_exists($value['id'], $followUpsArray)) {
				$customers[$key]['reminder_date'] = $followUpsArray[$value['id']]['reminder_date'];
				$customers[$key]['followup_type'] = $followUpsArray[$value['id']]['followup_type'];
			    }
			}

                        $provinces = Provinces::getProvinces ( "IN" );
			$viewData = array (
					'customers',
                                        'hotLeads',
			                'openLeads',
					'currentPage',
					'mainMenu',
					'provinces' 
			            );
			return View::make ( 'pages.customers.prospectuslist', compact ( $viewData ) );
		} else {
			return Redirect::to ( "/" );
		}
        }
	
	public function add() {
		if (Auth::check ()) {
				
			$currentPage = "CUSTOMERS_ADD";
			$mainMenu = "CUSTOMERS_MAIN";
			$inputs = Input::all ();
			if (isset ( $inputs ['customerName'] )) {
				
				
				$addCustomerResult = Customers::addCustomers ( $inputs );
	
				if ($addCustomerResult) {
						
					//if($inputs['customerCommentTxtarea'] != ""){
						$commentsInput['customerId']     = $addCustomerResult->id;
						$commentsInput['commentText']    = Config::get('constants.INITIATED_COMMENT').' '.$inputs['customerCommentTxtarea'];
						$commentsInput['commentType']    = 'FOLLOW_UP';
						if($inputs['reminderTxtBox'] == ''){
							$commentsInput['reminderDate']   = null;
						}else{
							$commentsInput['reminderDate']   = $inputs['reminderTxtBox'];
						}
						Comments::addComments($commentsInput);
					//}
					
					//Membership 
                                          if(isset($inputs['membershipType'])){
					if($inputs['membershipType'] != ""){
						
						$membershipInput['customer_id']           = $addCustomerResult->id;
						$membershipInput['membership_type_id']    = $inputs['membershipType'];						
							CustomerMembership::addMembership($membershipInput);
						
						$order['customer_id']     = $addCustomerResult->id;
						$order['payment_for']     = "membership";
						$order['payment_dues_id'] = '';
						$order['payment_mode']    = $inputs['paymentTypeRadio'];
						$order['card_last_digit'] = $inputs['card4digits'];
						$order['card_type']       = $inputs['cardType'];
						$order['bank_name']       = $inputs['bankName'];
						$order['cheque_number']   = $inputs['chequeNumber'];
						$order['amount']          = $inputs['membershipPrice'];
						$order['order_status']      = "completed";
						Orders::createOrder($order);
					}
                                          }
					//Upload Image
					if(Input::file('profileImage')){
						
						$file = Input::file('profileImage');
						$destinationPath = 'upload/profile/customer/';
						$filename = $file->getClientOriginalName();
						$fileExtension = '.'.$file->getClientOriginalExtension();
						$customerId = $addCustomerResult->id;
						$filename = 'customer_profile_'.$customerId.'_medium'.$fileExtension;
						$result = Input::file('profileImage')->move($destinationPath, $filename);
						
						if($result){
						
							$customer = Customers::find($customerId);
							$customer->profile_image = $filename;
							$customer->save();
						
						}
					}
					
					
					Session::flash ( 'msg', "Customer account created successfully." );
						
					return Redirect::to ( 'customers/view/' . $addCustomerResult->id );
				} else {
					Session::flash ( 'warning', "Sorry, Customer already exists in the system." );
				}
			}
			
			$provinces = Provinces::getProvinces ( "IN" );
			$membershipTypes = MembershipTypes::getMembershipTypes();
			$viewData = array (
					'currentPage',
					'mainMenu',
					'provinces',
					'membershipTypes'
			);
			return View::make ( 'pages.customers.customeradd', compact ( $viewData ) );
		} else {
			return Redirect::action('VaultController@logout');
		}
	}
	
	
	
	
	public function details($id) {
		
		if(Auth::check()){
			$currentPage = "CUSTOMERS_LIST";
			$mainMenu = "CUSTOMERS_MAIN";
			
			$inputs = Input::all ();
			if (isset ( $inputs ['customerName'] )) {
				if (Customers::addCustomers ( $inputs )) {
					Session::flash ( 'msg', "Customer added successfully." );
				} else {
					Session::flash ( 'warning', "Customer, Course Could not be added at the moment." );
				}
			}
			$customer = Customers::getCustomersById ( $id );
			$students = Students::getStudentByCustomer ( $id );
			$comments = Comments::getCommentByCustomerId ( $id );
			$provinces = Provinces::getProvinces ( "IN" );
			$kidsSelect = Students::getStudentsForSelectBox($id);
			$membershipTypes = MembershipTypes::getMembershipTypesForSelectBox();
			$birthdays = BirthdayParties::getBirthdaysByCustomer($id);
			
			
			//return $customer;
			
			//Membership
			if (isset ($inputs['membershipTypesMembersDiv'])){
				
				/* echo '<pre>';
				print_r($inputs);
				echo '</pre>';
				exit(); */
				if($inputs['membershipTypesMembersDiv'] != ""){
					
				
					$membershipInput['customer_id']           = $id;
					$membershipInput['membership_type_id']    = $inputs['membershipTypesMembersDiv'];
					CustomerMembership::addMembership($membershipInput);
				
					$order['customer_id']     = $id;
					$order['payment_for']     = "membership";
					$order['payment_dues_id'] = '';
					$order['payment_mode']    = $inputs['paymentTypeRadio'];
					$order['card_last_digit'] = $inputs['card4digits'];
					$order['card_type']       = $inputs['cardType'];
					$order['bank_name']       = $inputs['bankName'];
					$order['cheque_number']   = $inputs['chequeNumber'];
					$order['amount']          = $inputs['membershipPrice'];
					$order['order_status']      = "completed";
					Orders::createOrder($order);
				}
			}
			
			$presentDate=  Carbon::now();
                        $membershipStartDate=Carbon::now();
                        $membershipEndDate=Carbon::now();
			$customerMembershipId = '';
			if(isset($customer->CustomerMembership['0'])){
                                $select=(count($customer->CustomerMembership)-1);
                                $membershipStartDate=$membershipStartDate->createFromFormat('Y-m-d', $customer->CustomerMembership[$select]->membership_start_date);
                                $membershipEndDate=$membershipEndDate->createFromFormat('Y-m-d', $customer->CustomerMembership[$select]->membership_end_date);
                                if($membershipStartDate->lte($presentDate)  && $membershipEndDate->gte($presentDate)){
                                $customerMembershipId = $customer->CustomerMembership[$select]->membership_type_id;    
                                }
				
			}
                        if(isset($customerMembershipId)){
			$customerMembership = MembershipTypes::getMembershipTypeByID($customerMembershipId);
                        }
                        $membershipTypesAll = MembershipTypes::getMembershipTypes();
			
                        $birthdaypaiddata =Orders::getBirthdayfulldata($id); 
                       for($i=0;$i<count($birthdaypaiddata);$i++){
                           $studentData=  Students::getStudentById($birthdaypaiddata[$i]['student_id']);
                           $birthdaypaiddata[$i]['student_name']=$studentData[0]['student_name'];
                           $birthdaypaiddata[$i]['student_date_of_birth']=$studentData[0]['student_date_of_birth'];  
                           
                           $birthdayData=BirthdayParties::getBirthdaybyId($birthdaypaiddata[$i]['birthday_id']);
                           $birthdaypaiddata[$i]['birthday_party_date']=$birthdayData[0]['birthday_party_date'];
                           // $birthdaypaiddata[$i]['tax_amount']= $birthdaypaiddata[0]['tax_amount'];
                           $user_data=User::getUsersByUserId($birthdaypaiddata[$i]['created_by']);
                           $birthdaypaiddata[$i]['name']=$user_data[0]['first_name'].$user_data[0]['last_name'];
                           $birthdaypaiddata[$i]['encrypted_id']=Crypt::encrypt($birthdaypaiddata[$i]['id']);
                       } 
                       
                       $birthdayDuedata=  PaymentDues::getPaymentpendingfulldata($id);
                         for($i=0;$i<count($birthdayDuedata);$i++){
                             $studentData=Students::getStudentById($birthdayDuedata[$i]['student_id']);
                             $birthdayDuedata[$i]['student_name']=$studentData[0]['student_name'];
                             $user_data=User::getUsersByUserId($birthdayDuedata[$i]['created_by']);
                             $birthdayDuedata[$i]['name']=$user_data[0]['first_name'].$user_data[0]['last_name'];
                             $birthdayData=BirthdayParties::getBirthdaybyId($birthdayDuedata[$i]['birthday_id']);
                             $birthdayDuedata[$i]['birthday_party_date']=$birthdayData[0]['birthday_party_date'];
                             
                         }
                         //followup_data
                         
                         $iv_data=IntroVisit::where('customer_id','=',$id)
                                                  ->get();
                         for($i=0;$i<count($iv_data);$i++){
                             $comments_data=Comments::where('introvisit_id','=',$iv_data[$i]['id'])
                                                      ->orderBy('id','DESC')
                                                      ->first();
                             $iv_data[$i]['comment_data']=$comments_data;
                             $student=Students::find($iv_data[$i]['student_id']);
                             $iv_data[$i]['student_name']=$student['student_name'];
                             $iv_data[$i]['iv_date']=date("Y-m-d",strtotime($iv_data[$i]['iv_date']));

                         }
                         $birthday_data=BirthdayParties::where('customer_id','=',$id)
                                                         ->get();
                         for($i=0;$i<count($birthday_data);$i++){
                         $birthday_comments=Comments::where('birthday_id','=',$birthday_data[$i]['id'])
                                                      ->orderBy('id','DESC')
                                                      ->first();
                         $birthday_data[$i]['comment_data']=$birthday_comments;
                         $student_data=Students::find($birthday_data[$i]['student_id']);
                         $birthday_data[$i]['student_name']=$student_data['student_name'];
                         $birthday_data[$i]['birthday_party_date']=date("Y-m-d",strtotime($birthday_data[$i]['birthday_party_date']));

                         }
                         
                       //for complaints  
                         $complaint_data=Complaint::getComplaintByCustomerId($id);
                         //Comments::where('customer_id','=',$id)->get();
                         for($i=0;$i<count($complaint_data);$i++){
                             $complaint_data[$i]['comments']=Comments::where('complaint_id','=',$complaint_data[$i]['id'])
                                       ->orderBy('id','DESC')
                                       ->first();
                             $student_data=  Students::find($complaint_data[$i]['student_id']);
                             $complaint_data[$i]['student_name']=$student_data['student_name'];
                         }
                         
                      //for retention
                         $retention_data=Retention::getRetentionByCustomerId($id);
                         for($i=0;$i<count($retention_data);$i++){
                               $retention_data[$i]['comments']=  Comments::where('retention_id','=',$retention_data[$i]['id'])
                                                                 ->orderBy('id','DESC')
                                                                 ->first();
                               $student_data=  Students::find($retention_data[$i]['student_id']);
                               $retention_data[$i]['student_name']=$student_data['student_name'];
                         }
                    	 //return $end_date;
                      //for inquiry
                         $inuiry_data=Inquiry::getInquiryByCustomerId($id);
                         for($i=0;$i<count($inuiry_data);$i++){
                             $inuiry_data[$i]['comments']=Comments::where('inquiry_id','=',$inuiry_data[$i]['id'])
                                                          ->orderBy('id','DESC')
                                                          ->first();
                             
                         }
                           
                        // for customer kids enrollment.  
                          
                        $customer_student_data=  Students::where('customer_id','=',$id)
                                                 ->where('franchisee_id','=',  Session::get('franchiseId'))
                                                 ->select('id','student_name')
                                                 ->get();
                        for($i=0;$i<count($customer_student_data);$i++){
                            $student_classes=StudentClasses::getEnrolledStudentBatch($customer_student_data[$i]['id']);
                            //return $student_classes[0]['batch_id'];
                            $customer_student_data[$i]['student_classes_data']=$student_classes;
                        }
                        //return $customer_student_data;
                        for($i=0;$i<count($customer_student_data);$i++){
                            for($j=0;$j<count($customer_student_data[$i]['student_classes_data']);$j++){
                                $find=  Batches::find($customer_student_data[$i]['student_classes_data'][$j]['batch_id']);
                                $customer_student_data[$i]['student_classes_data'][$j]['batch_name']=$find['batch_name'];
                            }
                        }
                        
                        //return the customer membership follolwup
                        /*
                        $customer_membership_data= MembershipFollowup::where('customer_id','=',$id)
                                                                        ->get();
                        for($i=0;$i<count($customer_membership_data);$i++){
                           $membershipid[$i]= $customer_membership_data[$i]['id'];
                           
                        }
                        if(isset($membershipid)){
                       for($i=0;$i<count($membershipid);$i++){
                           $membership_followup_data[$i]=Comments::where('membership_followup_id','=',$membershipid[$i])
                                                          ->orderBy('id','DESC')
                                                          ->first();
                           $memfollowup_data= MembershipFollowup::find($membershipid[$i]);
                           $Customer_membership_data =  CustomerMembership::find($memfollowup_data->membership_id);
                          
                           $membership_followup_data[$i]['membership_end_date']=$Customer_membership_data->membership_end_date;
                        }
                        }
                        */
                        $taxPercentage=  PaymentTax::getTaxPercentageForPayment();
                        $tax_data=TaxParticulars::where('franchisee_id','=',Session::get('franchiseId'))->get();
                        $birthday_base_price = BirthdayBasePrice::getBirthdaybasePrice();
                        $membership_data=CustomerMembership::getCustomerMembershipDetails($customer->id);
                        
                        

                        
                        
			$viewData = array (
                                        'birthday_base_price',
                                        'birthdaypaiddata',
                                        'birthdayDuedata',
					'customer',
					'students',
					'currentPage',
					'mainMenu',
					'comments',
					'provinces',
					'customerMembership',
					'kidsSelect',
					'membershipTypes',
					'membershipTypesAll',
					'membership_data',
					'birthdays',
                                        'iv_data',
                                        'birthday_data',
                                        'complaint_data',
                                        'retention_data',
                                        'inuiry_data',
                                        'enrollmentFollowupData',
                                        'customer_student_data',
                                        'membership_followup_data',
                                        'taxPercentage',
                                        'tax_data',
			);
			return View::make ( 'pages.customers.details', compact ( $viewData ) );
		}else{
			return Redirect::action('VaultController@logout');
		}
	}


	public function purchaseMembership(){
		if(Auth::check()){
			$inputs=Input::all();
			DB::beginTransaction();
			try {
				
				// adding to membershiptable
    			$membership_data=CustomerMembership::addMembership($inputs);
    			$inputs['membership_id'] = $membership_data['id'];
    			$memdata=MembershipTypes::find($inputs['membership_type_id']);
    			$inputs['membership_id'] = $membership_data['id'];
    			$inputs['membership_name']=$memdata->name;
    			$inputs['amount']=$memdata->fee_amount;
    			$inputs['membership_amount']=$inputs['amount'];
    			$inputs['payment_due_amount']=$inputs['amount'];
    			$inputs['payment_due_amount_after_discount']=$inputs['amount'];
     			$tax=PaymentTax::where('franchisee_id','=',Session::get('franchiseId'))
    						->select('tax_percentage')->get();
    			$tax=$tax[0];
    			$inputs['tax']= $tax['tax_percentage'];
     			$payment_data=PaymentDues::createMembershipPaymentDues($inputs);
    			$inputs['payment_due_id']=$payment_data->id;
    			$inputs['taxamt']=($inputs['membership_amount']*$inputs['tax'])/100;
    			$order_data=Orders::CreateMembershipOrder($inputs);
    			
    			DB::commit();
    			return Response::json(array('status'=>'success','print_id'=>Crypt::encrypt($order_data->id)));
    			// all good
			} catch (\Exception $e) {
    			DB::rollback();
    			return Response::json(array('status'=>'failure'));
    			// something went wrong
			}


		}else{
			return Redirect::action('VaultController@logout');	
		}

	}

	static public function getmembershiptypedetails() {
		if(Auth::check()){
			$inputs=Input::all();
			$memdata=MembershipTypes::find($inputs['mem_type_id']);
			$tax_data=PaymentTax::where('franchisee_id','=',Session::get('franchiseId'))->get();
			$tax_data=$tax_data[0];
			$taxcal=($memdata->fee_amount*$tax_data->tax_percentage)/100;
			$totalcost=$memdata->fee_amount+$taxcal;
			return Response::json(array('status'=>'success','mem_data'=>$memdata,'tax_data'=>$tax_data,'taxcal'=>$taxcal,'totalcost'=>$totalcost));
		}else{
			return Redirect::action('VaultController@logout');	
		}
	} 
	
	public function uploadProfilePicture(){
	
		$file = Input::file('profileImage');
		$destinationPath = 'upload/profile/customer/';
		$filename = $file->getClientOriginalName();
		$fileExtension = '.'.$file->getClientOriginalExtension();
		$customerId = Input::get('customerId');
		$filename = 'customer_profile_'.$customerId.'_medium'.$fileExtension;
		$result = Input::file('profileImage')->move($destinationPath, $filename);
	
		if($result){
	
			$customer = Customers::find($customerId);
			$customer->profile_image = $filename;
			$customer->save();
	
		}
	
		Session::flash ( 'imageUploadMessage', "Profile picture updated successfully." );
		return Redirect::to("/customers/view/".$customerId);
	
	
	
	}
	
	public function checkCustomerExists(){
		$inputs = Input::all();		
		$customer = Customers::getCustomerByEmail($inputs['email']);		
		if(isset($customer['0'])){
			return Response::json(array("status"=>"exists"));
		}
		return Response::json(array("status"=>"clear"));
	}
	
	
	
	public function editCustomer() {
		
		$inputs = Input::all();
		
		if (isset ( $inputs ['customerName'] )) {
			$editCustomerResult = Customers::saveCustomers ( $inputs );
			if($editCustomerResult){
				return Response::json(array("status"=>"success"));
			}else{
			return Response::json(array("status"=>"failed"));
			}
		}
		
	}
        
        public function getScheduledIntrovisitByCustomerId(){
            $inputs = Input::all();
            $iv_data=IntroVisit::with('students','classes','batches')->where('customer_id','=',$inputs['customerId'])->get();
            return Response::json(array('status'=>'success','data'=>$iv_data));
        }
	
        
         public function getIntrovisitByCustomerStatus(){
            $inputs=Input::all();
            $iv_data=  IntroVisit::with('students','classes','batches')
                                   ->where('customer_id','=',$inputs['customerId'])
                                   ->whereIn('status',array('ACTIVE/SCHEDULED','RESCHEDULED'))
                                   ->get();
            return Response::json(array('status'=>'success','data'=>$iv_data));
        }
        
        public function changeIvStatustoAttendedByIVid(){
            $inputs=Input::all();
            $iv_data=  IntroVisit::find($inputs['iv_id']);
            $iv_data->status='ATTENDED';
            $iv_data->save();
            if($iv_data){
                return Response::json(array('status'=>'succes'));
            }
        }
        
        
        
        public function getMembershipHistory(){
            $inputs=Input::all();
            $membershipHistoryData=Comments::getMembershipHistoryById($inputs['membership_id']);
            for($i=0;$i<count($membershipHistoryData);$i++){
                $user_data=User::find($membershipHistoryData[$i]['created_by']);
                $membershipHistoryData[$i]['commentor_name']=$user_data->first_name.$user_data->last_name;
            }
            return Response::json(array('status'=>'success','historyData'=>$membershipHistoryData));
        }
        
        
        
        public function  updateMembershipFollowup(){
            $inputs=Input::all();
            $membership_data_make_reminder_null= Comments::where('membership_followup_id','=',$inputs['membership_followup_id'])
                                               ->update(array('reminder_date'=>Null,));
            $membership_data=Comments::where('membership_followup_id','=',$inputs['membership_followup_id'])
                                               ->orderBy('id','DESC')
                                               ->first();
           if($inputs['followup_status']=='REMINDER_CALL'){
                $commentText = "Reminder call  ".'on  '.date('Y-m-d', strtotime($inputs['remider_date'])).' '.$inputs['comment'];
				
                
            }elseif($inputs['followup_status']=='FOLLOW_CALL'){
                $commentText = "Follow call  ".'on  '.date('Y-m-d', strtotime($inputs['remider_date'])).' '.$inputs['comment'];
		
            }elseif($inputs['followup_status']=='CALL_SPOUSE'){
                $commentText = "Call Spouse ".'on  '.date('Y-m-d', strtotime($inputs['remider_date'])).' '.$inputs['comment'];
            }elseif($inputs['followup_status']=='NOT_AVAILABLE'){
                $commentText = "NOT AVAILABLE".'on '.date('Y-m-d', strtotime($inputs['remider_date'])).' '.$inputs['comment'];
            }elseif($inputs['followup_status']=='NOT_INTERESTED'){
                $commentText = "Not Available  ".'on  '.date('Y-m-d').' '.$inputs['comment'];
		        
            }elseif($inputs['followup_status']=='CLOSE_CALL'){
                $commentText="Followupcall closed on ".date('Y-m-d').' '.$inputs['comment'];
            }
            
            
                        $commentsInput['customerId']     = $membership_data['customer_id'];
                        //$commentsInput['student_id']     = $membership_data['student_id'];
                        $commentsInput['membership_followup_id']  = $membership_data['membership_followup_id'];
                        $commentsInput['followupType']  = $membership_data['followup_type'];
                        $commentsInput['commentStatus']= $inputs['followup_status'];
                        $commentsInput['commentType']   = $inputs['comment_type']; 
                           
			$commentsInput['commentText']    = $commentText;
		      	
                        if(($inputs['followup_status']== 'CLOSE_CALL')){
                            if(($inputs['followup_status']!= 'NOT_INTERESTED')){
                               if(isset($inputs['remider_date'])){
                                   $commentsInput['reminderDate']   = $inputs['remider_date'];
                               }
                            }
                        }
                        
                        
                       Comments::addComments($commentsInput);
            
            
            
            return Response::json(array('status'=>'success'));
        }
        
        
        
        public function getUniqueLocality(){
            if(Auth::check()){
                return Response::json(array('status'=>'success','data'=>Customers::where('locality','!=','')->where('franchisee_id','=',Session::get('franchiseId'))->distinct('locality')->select('locality')->get()));
            }
        }
        
        public function getUniqueApartmentNames(){
            if(Auth::check()){
                return Response::json(array('status'=>'success','data'=>Customers::where('apartment_name','!=','')->where('franchisee_id','=',Session::get('franchiseId'))->distinct('apartment_name')->select('apartment_name')->get()));
            }
        }
        
        
        
        public function deleteCustomer(){
            if((Auth::check()) && (Session::get('userType')=='ADMIN') ){
                 $inputs=Input::all();
                 $deleted=0;
                 //orders
                 if(Orders::where('customer_id','=',$inputs['customer_id'])
                           ->exists()
                   ){
                     Orders::where('customer_id','=',$inputs['customer_id'])
                             ->delete();
                     $deleted=1;
                   }
                 if(PaymentMaster::where('customer_id','=',$inputs['customer_id'])
                                  ->exists()){
                     PaymentMaster::where('student_id','=',$inputs['customer_id'])
                                  ->delete();
                     $deleted=1;
                   }
                 if(PaymentDues::where('customer_id','=',$inputs['customer_id'])
                                ->where('franchisee_id','=',Session::get('franchiseId'))
                                ->exists()){
                     PaymentDues::where('customer_id','=',$inputs['customer_id'])
                                ->where('franchisee_id','=',Session::get('franchiseId'))
                                ->delete();
                     $deleted=1;
                 }
                if(PaymentFollowups::where('customer_id','=',$inputs['customer_id'])
                                     ->exists()
                        ){
                    PaymentFollowups::where('customer_id','=',$inputs['customer_id'])
                                      ->delete();
                    $deleted=1;
                }
                
                if(BirthdayParties::where('customer_id','=',$inputs['customer_id'])
                                    ->exists()){
                        
                        BirthdayParties::where('customer_id','=',$inputs['customer_id'])
                                    ->delete();
                        $deleted=1;
                }
                if(IntroVisit::where('customer_id','=',$inputs['customer_id'])
                               ->where('franchisee_id','=',Session::get('franchiseId'))
                               ->exists()){
                        IntroVisit::where('customer_id','=',$inputs['customer_id'])
                               ->where('franchisee_id','=',Session::get('franchiseId'))
                               ->delete();
                        $deleted=1;
                }
                if(CustomerMembership::where('customer_id','=',$inputs['customer_id'])->exists()){
                    CustomerMembership::where('customer_id','=',$inputs['customer_id'])->delete();
                }
                if(MembershipFollowup::where('customer_id','=',$inputs['customer_id'])->exists()){
                   MembershipFollowup::where('customer_id','=',$inputs['customer_id'])->delete(); 
                }
                if(PaymentReminders::where('customer_id','=',$inputs['customer_id'])->exists()){
                    PaymentReminders::where('customer_id','=',$inputs['customer_id'])->delete();
                }
                if(Students::where('customer_id','=',$inputs['customer_id'])->exists()){
                    $student_ids=Students::where('customer_id','=',$inputs['customer_id'])
                                 ->select('id')->get();
                    foreach($student_ids as $student_id){
                        if(StudentClasses::where('student_id','=',$student_id->id)
                                   ->exists()
                        ){
                            StudentClasses::where('student_id','=',$student_id->id)->delete();
                            $deleted=1;
                        }
                        if(Attendance::where('student_id','=',$student_id->id)->exists()){
                            Attendance::where('student_id','=',$student_id->id)->delete();
                            $deleted=1;
                        }
                    }
                   Students::where('customer_id','=',$inputs['customer_id'])->delete();
                }
                if(Customers::where('id','=',$inputs['customer_id'])->exists()){
                
                
                $customer_data=Customers::find($inputs['customer_id']);
                $deletedcustomer= new DeletedCustomers();
                $deletedcustomer->customer_id=$inputs['customer_id'];
                $deletedcustomer->franchisee_id=$customer_data->franchisee_id;
                $deletedcustomer->customer_name=$customer_data->customer_name;
                $deletedcustomer->customer_lastname=$customer_data->customer_lastname;
                $deletedcustomer->mobile_no=$customer_data->mobile_no;
                $deletedcustomer->alt_mobile_no=$customer_data->alt_mobile_no;
                $deletedcustomer->landline_no=$customer_data->landline_no;
                $deletedcustomer->building=$customer_data->building;
                $deletedcustomer->apartment_name=$customer_data->apartment_name;
                $deletedcustomer->lane=$customer_data->lane;
                $deletedcustomer->locality=$customer_data->locality;
                $deletedcustomer->state=$customer_data->state;
                $deletedcustomer->city=$customer_data->city;
                $deletedcustomer->zipcode=$customer_data->zipcode;
                $deletedcustomer->address=$customer_data->address;
                $deletedcustomer->source=$customer_data->source;
                $deletedcustomer->source_event=$customer_data->source_event;
                $deletedcustomer->profile_image=$customer_data->profile_image;
                $deletedcustomer->stage=$customer_data->stage;
                $deletedcustomer->referred_by=$customer_data->referred_by;
                $deletedcustomer->created_by=Session::get('userId');
                $deletedcustomer->created_at=date("Y-m-d H:i:s");
                $deletedcustomer->save();
                
                if(Comments::where('customer_id','=',$inputs['customer_id'])->exists()){
                    Comments::where('customer_id','=',$inputs['customer_id'])->delete();
                }
                
                Customers::where('id','=',$inputs['customer_id'])->delete();
                $deleted=1;
                }
                
                
                
              
                   
                return Response::json(array('status'=>'success','deleted_data'=>$deleted)); 
            }else{
                return Response::json(array('status'=>'failure'));
            }
        }
		public static  function deleteMembership(){
        	if((Auth::check()) && (Session::get('userType')=='ADMIN')){
                 $inputs=Input::all();
                 $deleted=CustomerMembership::where('membership_start_date','<=',Date('Y-m-d'))
                  					 ->where('membership_end_date','>=',Date('Y-m-d'))
                  					 ->where('customer_id','=',$inputs['customer_id'])
                  					 ->delete();

				 return Response::json(array('status'=>'success','deleted'=>$deleted));
        	}	
        }
		/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {
		//
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function show($id) {
		//
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function edit($id) {
		//
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function update($id) {
		//
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function destroy($id) {
		//
	}
}
