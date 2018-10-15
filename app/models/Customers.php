<?php
use Carbon\Carbon;

class Customers extends \Eloquent {
	protected $fillable = [];
	
	
	public function Students(){
		return $this->hasMany('Students','customer_id');
	}
	
	public function Comments(){
		return $this->hasMany('Comments','customer_id');
	}
	
	public function BatchSchedule(){
		return $this->hasMany('BatchSchedule','customer_id');
	}
	
	public function Events(){
		return $this->belongsTo('Events','source_event');
	}
	
	public function CustomerMembership(){
		return $this->hasMany('CustomerMembership','customer_id')->where('status', '=','active');
	}
	
	
	public function Orders(){
		return $this->hasMany('Orders','customer_id');
	}
	
	
	
	static function addCustomers($inputs){
		if(($inputs['altMobileNo']!='') && (Customers::where('alt_mobile_no','=',$inputs['altMobileNo'])
                    ->where('franchisee_id','=',Session::get('franchiseId'))
                    ->exists()
		  )||(Customers::where('mobile_no','=',$inputs['altMobileNo'])
                    ->where('franchisee_id','=',Session::get('franchiseId'))
                    ->exists()
          )
		  ){

			return false;
		}
		
		if(! ((Customers::where('mobile_no','=',$inputs['customerMobile'])
                    ->where('franchisee_id','=',Session::get('franchiseId'))
                    ->exists()
          )||(Customers::where('alt_mobile_no','=',$inputs['customerMobile'])
                    ->where('franchisee_id','=',Session::get('franchiseId'))
                    ->exists()
          )

        )){
		
		$customer = new Customers();
		$customer->franchisee_id  = Session::get('franchiseId');
		$customer->customer_name  =  ucfirst($inputs['customerName']);
                $customer->customer_lastname=ucfirst($inputs['customerLastName']);
		$customer->customer_email = $inputs['customerEmail'];
		$customer->mobile_no      = $inputs['customerMobile'];
    $customer->alt_mobile_no  = $inputs['altMobileNo'];
    $customer->landline_no    = $inputs['landlineNo'];
		$customer->building       = $inputs['building'];		
		$customer->apartment_name = $inputs['apartment'];
		$customer->lane           = $inputs['lane'];
		$customer->locality       = $inputs['locality'];
		$customer->state          = $inputs['state'];
		$customer->city           = $inputs['city'];
		$customer->zipcode        = $inputs['zipcode'];
		$customer->source         = $inputs['source'];
		$customer->stage          = "INIITATED";
		
		if(isset($inputs['eventsId']) && $inputs['eventsId'] != ""){
			
			$customer->source_event     = $inputs['eventsId'];
			
		}
		
		
		
		$customer->referred_by    = $inputs['referredBy'];
		$customer->created_by     = Session::get('userId');
		$customer->created_at     = date("Y-m-d H:i:s");

		$customer->save();
		return $customer;
            }else{
                return false;
            }
	}
	
	static function getOpenLeads(){
	    $presentdate=  Carbon::now();
	    $customer_members=  CustomerMembership::
                                  where('membership_start_date','<=',$presentdate->toDateString())
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
            $customer_id;
            foreach($customers as $c){
                $customer_id[]=$c['id'];
            }
   
	    if(!empty($customer_id)){
             $iv = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                        ->whereIn('customer_id',$customer_id)
                                        ->where('lead_status','=','new')
                                         ->groupBy('customer_id')
                                         ->get();

             $iv_ids;
             $iv_ids = '';
              foreach($iv as $iv_id){
                $iv_ids[] = $iv_id['customer_id'];
              }
            
            } else {
               $iv_ids = '';
               $iv = '';
            }
	    
	    if(!empty($iv_ids)){
             $new = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                           ->whereNotIn('customer_id',$iv_ids)
                           ->whereIn('customer_id',$customer_id)
                           ->where('followup_status','=','ACTIVE/SCHEDULED')
                           ->where('followup_type','=','INQUIRY')
                           ->groupBy('customer_id')
                           ->get();
		return count($iv)+count($new);
            } else {
                return 0;
            }                           
            
	}

	static function getHotLeads(){
	    $presentdate=  Carbon::now();
	    $customer_members=  CustomerMembership::
                                  where('membership_start_date','<=',$presentdate->toDateString())
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
            $customer_id;
            foreach($customers as $c){
		$customer_id[] = $c['id']; 
		 
	    }

	    if(!empty($customer_id)){
              $lead_types = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
				    // ->where('lead_status','!=','')
				     ->whereIn('customer_id',$customer_id)
				     ->where('lead_status','=','hot')	
                                     ->groupBy('customer_id')
                                     ->get();
              return count($lead_types);
	   }else{
              return 0;
	   }
	}
       
       /* static function getHotLeadsForProspects(){
            $presentdate=  Carbon::now();
            $customer_members=  CustomerMembership::
                                  where('membership_start_date','<=',$presentdate->toDateString())
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
           // print_r(count($customers)); die();
            $customer_id;
            foreach($customers as $c){
                $customer_id[] = $c['id'];

            }
            $student_classes = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                     ->whereIn('customer_id',$customer_id)
                                     ->where('followup_type', '=', 'INQUIRY')
                                     ->where('followup_status', '=', 'ATTENDED/CELEBRATED')
                                     ->groupBy('customer_id')
                                     ->get();
	    $hotLead_id;
            foreach($student_classes as $c){
		$hotLead_id[] = $c['customer_id'];
	    }           
	    return $hotLead_id;
        }*/
    
	static function getOpenLeadsForProspects(){
                $presentdate=  Carbon::now();
            $customer_members=  CustomerMembership::
                                  where('membership_start_date','<=',$presentdate->toDateString())
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
            $customer_id;
            foreach($customers as $c){
                $customer_id[]=$c['id'];
            }

            $iv = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                        ->whereIn('customer_id',$customer_id)
                                        ->where('followup_status', '=', 'FOLLOW_CALL')
                                        ->groupBy('customer_id')
                                        ->get();


            $hotLead_id;
            foreach($iv as $c){
                $hotLead_id[] = $c['customer_id'];
            }
            return $hotLead_id;

        }

	static function FollowupSForProspects(){
	    $presentdate=  Carbon::now();
            $customer_members=  CustomerMembership::
                                  where('membership_start_date','<=',$presentdate->toDateString())
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
            
	    $customer_id;
            foreach($customers as $c){
                $customer_id[]=$c['id'];
            }
	    if(!empty($customer_id)) {
              $iv = Comments::where('franchisee_id', '=', Session::get('franchiseId'))
                                        ->whereIn('customer_id',$customer_id)
					->selectRaw('reminder_date, followup_type, customer_id, lead_status')
					->orderBy('customer_id','Desc')
					->groupBy('customer_id')
                                        ->get();	

	    } else {
              $iv = '';
            }
            return $iv;

        }


	static function saveCustomers($inputs){
	
	
		$customer = Customers::find($inputs['customerId']);
		$customer->franchisee_id  = Session::get('franchiseId');
		$customer->customer_name  = $inputs['customerName'];
    $customer->customer_lastname  = $inputs['customerLastName'];
		$customer->alt_mobile_no  = $inputs['altMobileNo'];
    $customer->landline_no    = $inputs['landlineNo'];
		$customer->customer_email = $inputs['customerEmail'];
		$customer->mobile_no      = $inputs['customerMobile'];
		$customer->building       = $inputs['building'];
		$customer->apartment_name = $inputs['apartment'];
		$customer->lane           = $inputs['lane'];
		$customer->locality       = $inputs['locality'];
                if($inputs['state']==''){
		 $customer->state          = 0;
                }else{
                $customer->state          = $inputs['state'];
                }
                if(isset($inputs['city'])){
                if($inputs['city']==''){
		 $customer->city          = 0;
                }else{
                $customer->city          = $inputs['city'];
                }
                }
                
		$customer->zipcode        = $inputs['zipcode'];
		$customer->source         = $inputs['source'];
		//$customer->stage          = 'inquire';
		$customer->referred_by    = $inputs['referredBy'];
		$customer->created_by     = Session::get('userId');
		$customer->updated_at     = date("Y-m-d H:i:s");
		/* if(isset($inputs['eventsId']) && $inputs['eventsId'] != ""){
		
		$customer->source_event     = $inputs['eventsId'];
		
		} */
		
		$customer->save();
		return $customer;
	}
        static function getAllCustomersByFranchiseeId($franchiseeId){
            $customers = Customers::where('franchisee_id','=',$franchiseeId) 
                            ->get();
                return $customers;
        }
	
	static function getMembershipDates(){
		$presentdate=  Carbon::now();
		$customer_members=  CustomerMembership::where('franchisee_id', '=', Session::get('franchiseId'))
                                      ->where('membership_start_date','<=',$presentdate->toDateString())
                                      ->where('membership_end_date','>=',$presentdate->toDateString())
                                      ->select('customer_id')
                                      ->get();

                $id;
                foreach($customer_members as $c){
                    $id[]=$c['customer_id'];
                }
		$customers = Customers::where('franchisee_id','=',Session::get('franchiseId'))
                            ->whereIn('id',$id)
                            ->orderBy('id','Desc')
                            ->get();
		$membership;
		foreach($customers as $customer){
			$membership[] = $customer['id'];
		}
		
		$membership_dates = CustomerMembership::where('franchisee_id', '=', Session::get('franchiseId'))
						      ->whereIn('customer_id',$membership)
						      ->selectRaw('customer_id, max(membership_end_date) as membership_end_date')
						      ->orderBy('id','DESC')
						      ->groupBy('customer_id')
						      ->get();
		return $membership_dates;
	}	

        
	static function getAllCustomerMembersByFranchiseeId($franchiseeId){
    		$presentdate=  Carbon::now();
		    $customer_members=  CustomerMembership::
                                      where('membership_start_date','<=',$presentdate->toDateString())
                                      ->where('membership_end_date','>=',$presentdate->toDateString())
                                      ->select('customer_id')
                                      ->get();
               
                $id;
                foreach($customer_members as $c){
                    $id[]=$c['customer_id'];
                }
               
               
                $customers = Customers::where('franchisee_id','=',$franchiseeId)
                            ->whereIn('id',$id)
                            ->orderBy('id','Desc')
                            ->get();
                 return $customers;
                
                 
                
            
                /*
		$customers = Customers:: join('customer_membership','customers.id','=','customer_membership.customer_id')
                                        ->where('status','=','active')
                                        ->where('franchisee_id', '=', $franchiseeId)
                                        ->get();
		return $customers;
		*/
	}

  static function getAllCuurentCustomerMembersByFranchiseeId($franchiseeId){
    $presentdate=  Carbon::now();
    $customer_members = StudentClasses::getAllEnrolledStudents($franchiseeId);   
    $id;
    foreach($customer_members as $c){
        $id[]=$c['customer_id'];
    }
    
    $customers = Customers::where('franchisee_id','=',$franchiseeId)
                ->whereIn('id',$id)
                ->get();
    return $customers;
  }
        static function getAllCustomerNonMembersByFranchiseeId($franchiseeId){
		$presentdate=  Carbon::now();
		$customer_members=  CustomerMembership::where('membership_start_date','<=',$presentdate->toDateString())
                                                        ->where('membership_end_date','>=',$presentdate->toDateString())
                                                        ->select('customer_id')
                                                        ->get();
               
                $id;
                foreach($customer_members as $c){
                    $id[]=$c['customer_id'];
                }
                 
                $customers = Customers::where('franchisee_id','=',$franchiseeId)
                            ->whereNotIn('id',$id)
                            ->orderBy('created_at','Desc')
                            ->get();
 		$comment_id;

		foreach($customers as $c){
		    $commit_id[] = $c['id'];
		}
   

                return $customers;        
	}
	
	static function getAllCustomersForDropdown($franchiseeId){
		
		$customers = Customers::where('franchisee_id', '=', $franchiseeId)->lists('customer_name', 'id');
		return $customers;
		
	}
	
	static function getCustomerByEmail($email){
		
		if($email != ""){
			return Customers::where("customer_email","=",$email)->where('franchisee_id', '=', Session::get('franchiseId'))->take(1)->get();
		}
	}
	
	
	static function getCustomersById($customerId){
		
		$customer = Customers::with('Events','CustomerMembership')->where('id','=',$customerId)->take(1)->get();
		return $customer['0'];
	}
	
	static function getCustomerCount(){
		
		return Customers::where('franchisee_id', '=',  Session::get('franchiseId'))
						          ->count();
	}
	
	static function getEnrolledCustomerCount(){
	
		return Customers::count();
	}
  static function getCustomertodaysRegCount(){
    return Customers::whereDate('created_at', '=',  date("Y-m-d"))
                      ->where('franchisee_id', '=',  Session::get('franchiseId'))
						          ->count();
  }
  
  static function getCustomersReport($inputs) {
    $data['data'] = Customers::where('franchisee_id', '=', Session::get('franchiseId'))
                      ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                      ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                      ->get();
    return $data['data'];
  }        
}
