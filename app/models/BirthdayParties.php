<?php

class BirthdayParties extends \Eloquent {
	protected $fillable = [];
	protected $table = 'birthday_parties';
	
	public function Customers(){
	
		return $this->belongsTo('Customers', 'customer_id');
	}
	
	public function Students(){
	
		return $this->belongsTo('Students', 'student_id');
	}
	
	
	static function addbirthdayParty($inputs){
	
		
	
		$birthday = new BirthdayParties();
		$birthday->customer_id                   = $inputs['customerId'];
		$birthday->student_id                    = $inputs['kidsSelect'];
                $birthday->franchisee_id                 =Session::get('franchiseId');
                if(isset($inputs['defaultBirthdayPrice'])){
                    $birthday->default_birthday_cost=$inputs['defaultBirthdayPrice'];
                }
		$birthday->additional_number_of_guests   = $inputs['additionalGuestCount'];
		$birthday->additional_half_hours         = $inputs['additionalHalfHourCount'];
		$birthday->additional_guest_price	 = $inputs['additionalGuestPrice'];
		$birthday->additional_halfhour_price     = $inputs['additionalHalfHourPrice'];
		$birthday->advance_amount_paid           = $inputs['advanceAmount'];
		$birthday->remaining_due_amount          = $inputs['remainingAmount'];
	        $birthday->discount_amount	         = $inputs['discountAmount'];
		$birthday->grand_total  		 = $inputs['grandTotal'];
		$birthday->birthday_party_date  	 = date('Y-m-d',strtotime($inputs['birthdayCelebrationDate']));
		$birthday->birthday_party_time           = date('H:i:s',strtotime($inputs['birthdayTime']));
		$birthday->created_at                    = date("Y-m-d H:i:s");
		$birthday->created_by                    = Session::get('userId');
		$birthday->save();
		return $birthday;
		
	}
	
	
	static function checkWhetherBirthdayPartyExists($student_id){
		
		$currentYear = date('Y');		
		$existingBirthdayParties = BirthdayParties::where('student_id', '=', $student_id)
													->whereYear('birthday_party_date','=', $currentYear)->get();		
		if(isset($existingBirthdayParties['0'])){
			return true;
				
		}
		return false;		
		
	}
	
	static function getBirthdaysByCustomer($customerId){
		return BirthdayParties::with('Customers', 'Students')->where('customer_id', '=', $customerId)->get();
	}
        static function getBpartyCount(){
             return DB::table('birthday_parties')
                                             ->where('franchisee_id', '=', Session::get('franchiseId'))
                                             ->count();
        }
        static function  getBpartyCountBytoday(){
             return DB::table('birthday_parties')
                                             ->whereDate('birthday_parties.birthday_party_date', '=',  date("Y-m-d"))
                                             ->where('franchisee_id', '=', Session::get('franchiseId'))
                                             ->count();
        } 
        static function getBirthdaybyId($id){
            $birthdaydetails= BirthdayParties::where('id','=',$id)->get();
            return ($birthdaydetails);
        }
        
        static function getallBirthdayParties() {
        $birthdayDetails = BirthdayParties::where('franchisee_id','=',Session::get('franchiseId'))->get();
        if ($birthdayDetails->count()) {
            $calenderData = array();
            $i = 0;
            $calenderData['gotoDate'] = $birthdayDetails[0]->birthday_party_date;
            foreach ($birthdayDetails as $birthdaydetails) {
                $students = Students::getStudentId($birthdayDetails[$i]->student_id);
                $calenderData['event'][$i]['title'] = $students[0]->student_name . " 's Birthday";
                $calenderData['event'][$i]['id'] = $birthdaydetails->id;
                $calenderData['event'][$i]['start'] = $birthdaydetails->birthday_party_date . ' ' . $birthdaydetails->birthday_party_time;
                $calenderData['event'][$i]['end'] = $birthdaydetails->birthday_party_date . ' ' . $birthdaydetails->birthday_party_time;
                $calenderData['event'][$i]['allDay'] = 'false';
                $calenderData['event'][$i]['backgroundColor'] = '#909090';
                $calenderData['event'][$i]['textColor'] = 'white';
                $i++;
            }
            return $calenderData;
        }
    }

    static function todayBdaysForDailyRepo ($inputs) {
    	$data = Students::where('franchisee_id', '=', Session::get('franchiseId'))
    					->whereRaw('MONTH(student_date_of_birth) = MONTH(NOW())')
    					->whereRaw('DAY(student_date_of_birth) = DAY(NOW())')
    					->get();
    	for ($i=0; $i < count($data); $i++) { 
    	    $customers = Customers::where('franchisee_id', '=', Session::get('franchiseId'))
    	                          ->where('id', '=', $data[$i]['customer_id'])
    	                          ->get();
    	    $data[$i]['customer_name'] = $customers[0]['customer_name'] . $customers[0]['customer_lastname'];
    	    $data[$i]['mobile_no'] = $customers[0]['mobile_no'];
    	    $data[$i]['email'] = $customers[0]['customer_email'];
    	    $data[$i]['age'] = date_diff(date_create(date('Y-m-d',strtotime($data[$i]['student_date_of_birth']))), date_create('today'))->y.'.'.date_diff(date_create(date('Y-m-d',strtotime($data[$i]['student_date_of_birth']))), date_create('today'))->m.'years';
    	}
    	return $data;
    }

    
}
