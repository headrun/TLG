<?php
use Carbon\Carbon;

class Comments extends \Eloquent {
	protected $fillable = [];
	public $table = "customer_logs";
	
	
	public function Customers(){
		
		return $this->belongsTo("Customers", "customer_id");
	}
	
	public function Users(){
	
		return $this->belongsTo("User", "created_by");
	}

	
	
	static function addComments($input){		
		
		$comments = new Comments();
		$comments->customer_id   = $input['customerId'];
                if(isset($input['introvisit_id'])){
                $comments->introvisit_id=$input['introvisit_id'];
                }
                if(isset($input['paymentfollowup_id'])){
                $comments->paymentfollowup_id=$input['paymentfollowup_id'];
                }
                if(isset($input['followupType'])){
                    $comments->followup_type=$input['followupType'];
                }
                if(isset($input['birthday_id'])){
                    $comments['birthday_id']=$input['birthday_id'];
                }
                if(isset($input['student_id'])){
                    $comments['student_id']=$input['student_id'];
                }
                if(isset($input['inquiry_id'])){
                    $comments['inquiry_id']=$input['inquiry_id'];
                }
                
                if(isset($input['membership_followup_id'])){
                    $comments['membership_followup_id']=$input['membership_followup_id'];
                }
                
                if(isset($input['commentStatus'])){
                    $comments['followup_status']=$input['commentStatus'];
                }
		if(isset($input['LeadStatus'])){
                    $comments['lead_status']=$input['LeadStatus'];
                }
                if(isset($input['complaint_id'])){
                    $comments['complaint_id']=$input['complaint_id'];
                }
                if(isset($input['retention_id'])){
                    $comments['retention_id']=$input['retention_id'];
                }
                $comments->log_text      = $input['commentText'];
		$comments->comment_type = $input['commentType'];
		$comments->franchisee_id = Session::get('franchiseId');
                if(isset($input['reminder-Date'])){
			$comments->reminder_date = date('Y/m/d',strtotime($input['reminder-Date']));
		}
		if(isset($input['reminderDate'])){
			$comments->reminder_date = date('Y-m-d H:i:s',strtotime($input['reminderDate']));
		}else{
			$comments->reminder_date = null;
		}	
                //if(isset($input['reminder-Date'])){
		//	$comments->reminder_date = $input['reminder-Date'];
		//}
		$comments->created_by    = Session::get('userId');
		$comments->created_at    = date("Y-m-d H:i:s");
		$comments->save();
		
		return $comments;
	}
        
        
        
        static function addPaymentComments($inputs){
            
         for($i=0;$i<3;$i++){
            
              
            $comments=new Comments();
            $comments->student_id=$inputs['student_id'];
            $comments->customer_id=$inputs['customer_id'];
            $comments->franchisee_id=$inputs['franchisee_id'];
            $comments->followup_type=$inputs['followup_type'];
            $comments->followup_status=$inputs['followup_status'];
            $comments->comment_type=$inputs['comment_type'];
            $comments->created_by=Session::get('userId');
            $comments->created_at= date('Y-m-d H:i:s');
            if($i==0){  
                $comments->log_text="brush up call for the kid";
                $comments->paymentfollowup_id=$inputs['paymentfollowup_id'];
                $comments->reminder_date=$inputs['firstReminderDate'];
                $comments->save();
                }else  
                if($i==1){
                $comments->log_text="Initial payment call for the kid";
                $comments->paymentfollowup_id=$inputs['paymentfollowup_id2'];
                $comments->reminder_date=$inputs['secondReminderDate'];
                $comments->save();
                }else
                if($i==2){
                $comments->log_text="final payment call for the kid";
                $comments->paymentfollowup_id=$inputs['paymentfollowup_id3'];
                $comments->reminder_date=$inputs['thirdReminderDate'];
                $comments->save();
                }
            }
         
           return $comments;
            
        }
        
        static function addOnebiPaymentComment($inputs){
            $comments=new Comments();
            $comments->student_id=$inputs['student_id'];
            $comments->customer_id=$inputs['customer_id'];
            $comments->franchisee_id=$inputs['franchisee_id'];
            $comments->followup_type=$inputs['followup_type'];
            $comments->followup_status=$inputs['followup_status'];
            $comments->comment_type=$inputs['comment_type'];
            $comments->created_by=Session::get('userId');
            $comments->created_at= date('Y-m-d H:i:s');
            $comments->log_text="final payment call for the kid";
            $comments->paymentfollowup_id=$inputs['paymentfollowup_id'];
            $comments->reminder_date=$inputs['reminderDate'];
            $comments->save();
            return $comments;
        }
        static function addSinglePayComment($inputs){
            $comments=new Comments();
            $comments->student_id=$inputs['student_id'];
            $comments->customer_id=$inputs['customer_id'];
            $comments->franchisee_id=$inputs['franchisee_id'];
            if(isset($inputs['retention_id'])){
                $comments->retention_id=$inputs['retention_id']; 
            }
            $comments->followup_type=$inputs['followup_type'];
            $comments->followup_status=$inputs['followup_status'];
            $comments->comment_type=$inputs['comment_type'];
            $comments->created_by=Session::get('userId');
            $comments->created_at= date('Y-m-d H:i:s');
            $comments->log_text="Payment call for the kid";
            if(isset($inputs['paymentfollowup_id'])){
                $comments->paymentfollowup_id=$inputs['paymentfollowup_id'];
            }
            if(isset($inputs['reminderDate']) && array_key_exists('reminderDate',$inputs) ){
                $comments->reminder_date=$inputs['reminderDate'];
            }
            $comments->save();
            return $comments;
        }
	
	static function getCommentByCustomerId($customerId){
		
		return Comments::with("Customers","Users")
						->where('customer_id', "=", $customerId)
						->orderBy('created_at', 'DESC')
						->get();
		
	}
	
	static function getReminderCountByFranchiseeId(){
		
            return Comments::where("franchisee_id", "=", Session::get('franchiseId'))
					->where("reminder_date", "!=", "NULL")
					->whereDate('reminder_date','=',date("Y-m-d"))
					->count();
					
	}
	
	static function getTodaysFollowup(){
	
		$today = date('Y-m-d');
		return Comments::with('Customers')->where("franchisee_id", "=", Session::get('franchiseId'))
		->where("reminder_date", "!=", "NULL")
		//->where("comment_type", "=", "FOLLOW_UP")
		->whereDate("reminder_date", "=", $today)
		->get();
	
	}
	
	
	static function getAllFollowup(){
	
		$today = date('Y-m-d');
		return Comments::with('Customers')->where("franchisee_id", "=", Session::get('franchiseId'))
		                 ->whereDate("reminder_date", "=", $today)
		                 ->orderBy('reminder_date', 'desc')
		                 ->get();
		
	
	}
        
        static function getFutureFollowup(){
            $today = date('Y-m-d');
            return Comments::join('customers', 'customers.id', '=', 'customer_logs.customer_id')
                    ->where("customer_logs.franchisee_id", "=", Session::get('franchiseId'))
                    ->where("customer_logs.followup_type", "!=", "PAYMENT")
                    ->selectRaw('customers.customer_name, customers.customer_lastname, customers.id, customer_logs.followup_type, max(customer_logs.reminder_date) as reminder_date, customers.mobile_no')
                    ->groupBy('customer_logs.student_id')
                    ->havingRaw('max(customer_logs.reminder_date) > "'.$today.'"')
                    ->orderBy('customer_logs.reminder_date','DESC')
                    ->get();
        }
    
    static function toDeleteMultile(){
        $inputs = Input::all();   
        $today = date('Y-m-d');
        $franchis_id = Session::get('franchiseId'); 
       // print_r($today); die();
        return Comments::join('customers', 'customers.id', '=', 'customer_logs.customer_id')
                    ->where("customer_logs.franchisee_id", "=", Session::get('franchiseId'))
                    ->where("customer_logs.reminder_date", "!=", "NULL")
                    ->where("customer_logs.followup_status", "!=", "NOT_INTERESTED")
                    ->where("customer_logs.followup_type", "=", "RETENTION")
                    ->selectRaw('max(customer_logs.id) as id')
                    ->groupBy('customer_logs.student_id')
                    /*->havingRaw('max(customer_logs.reminder_date) < "'.$today.'"')*/
                    ->orderBy('customer_logs.reminder_date','DESC')
                    ->lists('id');

    }
    static function toGetMultileRecords(){
        $inputs = Input::all();   
        $today = date('Y-m-d');
        $franchis_id = Session::get('franchiseId'); 
       // print_r($today); die();
        return Comments::join('customers', 'customers.id', '=', 'customer_logs.customer_id')
                    ->where("customer_logs.franchisee_id", "=", Session::get('franchiseId'))
                    ->where("customer_logs.reminder_date", "!=", "NULL")
                    ->where("customer_logs.followup_type", "=", "RETENTION")
                    ->selectRaw('customer_logs.id')
                   // ->havingRaw('max(customer_logs.reminder_date) < "'.$today.'"')
                    ->orderBy('customer_logs.reminder_date','DESC')
                    ->lists('customer_logs.id');

    }
	
	static function getAllFollowupActive(){
	    $inputs = Input::all();   
		$today = date('Y-m-d');
        $franchis_id = Session::get('franchiseId'); 
       // print_r($today); die();
		return Comments::join('customers', 'customers.id', '=', 'customer_logs.customer_id')
                    ->where("customer_logs.franchisee_id", "=", Session::get('franchiseId'))
            		->where("customer_logs.reminder_date", "!=", "NULL")
                    ->where("customer_logs.reminder_date", ">=", '2000-01-01')
                    ->where("customer_logs.followup_status", "!=", "NOT_INTERESTED")
                    ->where("customer_logs.followup_type", "!=", "PAYMENT")
                    ->selectRaw('customers.customer_name, customers.customer_lastname, customers.id, customer_logs.followup_type, max(customer_logs.reminder_date) as reminder_date, customers.mobile_no')
                    ->groupBy('customer_logs.student_id')
                    ->havingRaw('max(customer_logs.reminder_date) < "'.$today.'"')
                    ->orderBy('customer_logs.reminder_date','DESC')
                    ->get();

	}
	
    static function addFollowupForMembership($customerMembershipData){
            
            $brushupReminderdate=  Carbon::now();
            $finalReminderdate=Carbon::now();
            for($i=0;$i<2;$i++){
                if($i==0){
                    $membership_data=MembershipFollowup::createMembershipfollowup($customerMembershipData);
                    
                    $followup=new Comments();
                    $brushupReminderdate=$brushupReminderdate->createFromFormat('Y-m-d',$customerMembershipData['membership_end_date']);
                    $finalReminderdate=$finalReminderdate->createFromFormat('Y-m-d',$customerMembershipData['membership_end_date']);
                    $followup->customer_id=$customerMembershipData['customer_id'];
                    $followup->franchisee_id=Session::get('franchiseId');
                    $followup->membership_followup_id=$membership_data['id'];
                    $followup->followup_type='MEMBERSHIP';
                    $followup->followup_status='FOLLOW_CALL';
                    $followup->comment_type='INTERESTED';
                    $followup->reminder_date='';
                    $followup->created_by=Session::get('userId');
                    $followup->created_at=date('Y-m-d H:i:s');
            
                    $followup->log_text='Brush Up Call for Membership (Memebership End date:'.$customerMembershipData['membership_end_date'].')';
                    $brushupReminderdate=$brushupReminderdate->subWeek();
                    $followup->reminder_date=$brushupReminderdate->toDateString();
                    $followup->save();
                    
                }
                if($i==1){
                    $membership_data=MembershipFollowup::createMembershipfollowup($customerMembershipData);
                    
                   $followup=new Comments();
                   $brushupReminderdate=$brushupReminderdate->createFromFormat('Y-m-d',$customerMembershipData['membership_end_date']);
                   $finalReminderdate=$finalReminderdate->createFromFormat('Y-m-d',$customerMembershipData['membership_end_date']);
                   $followup->customer_id=$customerMembershipData['customer_id'];
                   $followup->franchisee_id=Session::get('franchiseId');
                   $followup->membership_followup_id=$membership_data['id'];
                   $followup->followup_type='MEMBERSHIP';
                   $followup->followup_status='FOLLOW_CALL';
                   $followup->comment_type='INTERESTED';
                   $followup->reminder_date='';
                   $followup->created_by=Session::get('userId');
                   $followup->created_at=date('Y-m-d H:i:s');
            
                    $followup->log_text='Final Call for Membership (Memebership End date:'.$customerMembershipData['membership_end_date'].')';
                    $finalReminderdate=$finalReminderdate->subWeeks(3);
                    $followup->reminder_date=$finalReminderdate->toDateString();
                    $followup->save();
                }
                
                
            }
        }
        
        static function getMembershipHistoryById($membership_Followup_id){
            return Comments::where('membership_followup_id','=',$membership_Followup_id)
                             ->where('franchisee_id','=',Session::get('franchiseId'))
                             ->orderBy('id','DESC')
                             ->get();
        }

        static function getAllFollowupReports($inputs){
           $comment_data['data'] = Comments::where('franchisee_id','=',Session::get('franchiseId'))
                                    ->where('created_at','>=',$inputs['reportGenerateStartdate'])
                                    ->where('created_at','<=',$inputs['reportGenerateEnddate'])
                                    //->selectRaw('max(reminder_date)')
                                    ->orderBy('reminder_date','DESC')
                                    ->groupBy('student_id')
                                    ->get();

           for($i=0;$i<count($comment_data['data']);$i++){

              $temp=  Customers::find($comment_data['data'][$i]['customer_id']);
              
              $comment_data['data'][$i]['customer_name']=$temp['customer_name']." ".$temp['customer_lastname'];
          
              $temp2 =  Students::find($comment_data['data'][$i]['student_id']);
              $comment_data['data'][$i]['student_name'] = $temp2['student_name'];



            }
           return $comment_data;   
        }   
	
	static function getThisWeekNewLeads($customer_id, $presentdate, $endOfWeek) {
		$end = date('Y-m-d', $endOfWeek);
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                                    ->whereIn('customer_id',$customer_id)
                                                    ->whereDate('updated_at', '<=', $presentdate)
                                                    ->whereDate('updated_at', '>=', $end)
                                                    ->where('lead_status', '=', 'new')
                                                    ->groupBy('customer_id')
                                                    ->get();

		return count($leads);
	}	
	
	static function getNewLeadsForWeekWise($customer_id, $dates_start, $dates_end ){

		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                                    ->whereIn('customer_id',$customer_id)
                                                    ->whereDate('updated_at', '>=', $dates_start)
                                                    ->whereDate('updated_at', '<=', $dates_end)
                                                    ->where('lead_status', '=', 'new')
                                                    ->groupBy('customer_id')
                                                    ->get();
		return count($leads);

	}
	static function getNewLeadsForThisMonth($customer_id, $presentdate, $currentMonthStartDate){
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                                    ->whereIn('customer_id',$customer_id)
                                                    ->whereDate('updated_at', '>=', $currentMonthStartDate)
                                                    ->whereDate('updated_at', '<=', $presentdate)
                                                    ->where('lead_status', '=', 'new')
                                                    ->groupBy('customer_id')
                                                    ->get();
                return count($leads);

	}

	static function getCurrentWeekIvAttended($customer_id, $presentdate, $endOfWeek){
		$end_date = date('Y-m-d', $endOfWeek);
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                                         ->whereIn('customer_id',$customer_id)
                                                         ->whereDate('created_at', '<=', $presentdate)
                                                         ->whereDate('created_at', '>=', $end_date)
                                                         ->where('followup_type','=','INQUIRY')
                                                         ->where('followup_status', '=', 'ATTENDED/CELEBRATED')
                                                         ->groupBy('customer_id')
                                                         ->get();
		return count($leads);
	
	}

	static function getWeekWiseIvAtteded($customer_id, $dates_start, $dates_end){
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
					->whereIn('customer_id',$customer_id)
					->whereDate('created_at', '>=', $dates_start)
                                	->whereDate('created_at', '<=', $dates_end)
					->where('followup_type','=','INQUIRY')
                                        ->where('followup_status', '=', 'ATTENDED/CELEBRATED')
                                        ->groupBy('customer_id')
                                        ->get();
		return count($leads);

	}

	static function getThisMonthIvAttended($customer_id, $presentdate, $currentMonthStartDate){
	
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                        ->whereIn('customer_id',$customer_id)
                                        ->whereDate('created_at', '>=', $currentMonthStartDate)
                                        ->whereDate('created_at', '<=', $presentdate)
                                        ->where('followup_type','=','INQUIRY')
                                        ->where('followup_status', '=', 'ATTENDED/CELEBRATED')
                                        ->groupBy('customer_id')
                                        ->get();
                return count($leads);
	


	}
	
	static function getCurrentWeekIvScheduled($customer_id, $presentdate, $endOfWeek){
		$end_date = date('Y-m-d', $endOfWeek);
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
				  ->whereIn('customer_id',$customer_id)
				  ->where('followup_type','=','INQUIRY')
				  ->whereDate('created_at', '<=', $presentdate)
				  ->whereDate('created_at', '>=', $end_date)
				  ->where('followup_status', '=', 'ACTIVE/SCHEDULED')
				  ->groupBy('customer_id')
			          ->get();	

		return count($leads);
	}	
	
	static function getWeekWiseIvScheduled($customer_id, $dates_start, $dates_end){
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
 	 			 ->whereIn('customer_id',$customer_id)
				 ->whereDate('created_at', '>=', $dates_start)
      				 ->whereDate('created_at', '<=', $dates_end)
				 ->where('followup_type','=','INQUIRY')
				 ->where('followup_status', '=', 'ACTIVE/SCHEDULED')
				 ->groupBy('customer_id')
				 ->get();
				 
		return count($leads);

	}

	static function getThisMonthIvScheduled($customer_id, $presentdate, $currentMonthStartDate){
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                 ->whereIn('customer_id',$customer_id)
                                 ->whereDate('created_at', '>=', $currentMonthStartDate)
                                 ->whereDate('created_at', '<=', $presentdate)
                                 ->where('followup_type','=','INQUIRY')
                                 ->where('followup_status', '=', 'ACTIVE/SCHEDULED')
                                 ->groupBy('customer_id')
                                 ->get();

                return count($leads);

	}
		
	static function getCurrentWeekHotLeadsYes($customer_id, $presentdate, $endOfWeek){
		$end_date = date('Y-m-d', $endOfWeek);
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
				->whereIn('customer_id',$customer_id)	
				->whereDate('updated_at', '<=', $presentdate)
				->whereDate('updated_at', '>=', $end_date)
				->where('lead_status', '=','hot')
				->groupBy('customer_id')
				->get();
		return count($leads);
	}	

	static function getWeekWiseHotLeadsYes($customer_id, $dates_start, $dates_end){
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->whereIn('customer_id',$customer_id)
                                ->whereDate('updated_at', '>=', $dates_start)
                                ->whereDate('updated_at', '<=', $dates_end)
                                ->where('lead_status', '=', 'hot')
                                ->groupBy('customer_id')
                                ->get();
                return count($leads);


	}
	
	static function getHotLeadsForThisMonth($customer_id, $presentdate, $currentMonthStartDate){
                $leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                                    ->whereIn('customer_id',$customer_id)
                                                    ->whereDate('updated_at', '>=', $currentMonthStartDate)
                                                    ->whereDate('updated_at', '<=', $presentdate)
                                                    ->where('lead_status', '=', 'hot')
                                                    ->groupBy('customer_id')
                                                    ->get();
                return count($leads);

        }

	static function getCurrentWeekHotLeadsNo($customer_id, $presentdate, $endOfWeek){
                $end_date = date('Y-m-d', $endOfWeek);
                $leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->whereIn('customer_id',$customer_id)
                                ->whereDate('updated_at', '<=', $presentdate)
                                ->whereDate('updated_at', '>=', $end_date)
                                ->where('lead_status', '=', 'not_interested')
                                ->groupBy('customer_id')
                                ->get();
                return count($leads);
        }

        static function getWeekWiseHotLeadsNo($customer_id, $dates_start, $dates_end){

                $leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->whereIn('customer_id',$customer_id)
                                ->whereDate('updated_at', '>=', $dates_start)
                                ->whereDate('updated_at', '<=', $dates_end)
                                ->where('lead_status', '=', 'not_interested')
                                ->groupBy('customer_id')
                                ->get();

                return count($leads);


        }
	
	static function getNoLeadsForThisMonth($customer_id, $presentdate, $currentMonthStartDate){
                $leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                                    ->whereIn('customer_id',$customer_id)
                                                    ->whereDate('updated_at', '>=', $currentMonthStartDate)
                                                    ->whereDate('updated_at', '<=', $presentdate)
                                                    ->where('lead_status', '=', 'not_interested')
                                                    ->groupBy('customer_id')
                                                    ->get();
                return count($leads);

        }

	static function getCurrentWeekHotLeadsMaybe($customer_id, $presentdate, $endOfWeek){
                $end_date = date('Y-m-d', $endOfWeek);
                $leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->whereIn('customer_id',$customer_id)
                                ->whereDate('updated_at', '<=', $presentdate)
                                ->whereDate('updated_at', '>=', $end_date)
                                ->where('lead_status', '=', 'interested')
                                ->groupBy('customer_id')
                                ->get();
                return count($leads);
        }

        static function getWeekWiseHotLeadsMaybe($customer_id, $dates_start, $dates_end){

                $leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->whereIn('customer_id',$customer_id)
                                ->whereDate('updated_at', '<=', $dates_end)
                                ->whereDate('updated_at', '>=', $dates_start)
                                ->where('lead_status', '=', 'interested')
                                ->groupBy('customer_id')
                                ->get();

                return count($leads);


        }
	
	static function getMaybeLeadsForThisMonth($customer_id, $presentdate, $currentMonthStartDate){
                $leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                                    ->whereIn('customer_id',$customer_id)
                                                    ->whereDate('updated_at', '>=', $currentMonthStartDate)
                                                    ->whereDate('updated_at', '<=', $presentdate)
                                                    ->where('lead_status', '=', 'interested')
                                                    ->groupBy('customer_id')
                                                    ->get();
                return count($leads);

        }
		
	static function getThisWeekOsLeads($customer_id, $presentdate, $endOfWeek){
		$end_date = date('Y-m-d', $endOfWeek);
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
				 ->whereDate('updated_at', '=',$presentdate)
                                 ->whereDate('updated_at', '>=', $end_date)
				 ->whereIn('customer_id',$customer_id)
				 ->where('lead_status','=','')
				 ->groupBy('customer_id')
				 ->get();
		return count($leads);
	}

	static function getWeekWiseOsLeads($customer_id, $dates_start, $dates_end){
		$leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
				->whereDate('created_at','>=',$dates_start)
				->whereDate('created_at', '<=',$dates_end)
				->whereIn('customer_id',$customer_id)
				->where('lead_status','=','')
				->groupBy('customer_id')
				->get();
		return count($leads);
	}
	
	static function getThisMonthOutStands($customer_id, $presentdate, $currentMonthStartDate){
                $leads = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->whereDate('updated_at','>=',$currentMonthStartDate)
                                ->whereDate('updated_at', '<=',$presentdate)
                                ->whereIn('customer_id',$customer_id)
                                ->where('lead_status','=','')
				->groupBy('customer_id')
				->get();
                return count($leads);
        }
}
