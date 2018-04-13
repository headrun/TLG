<?php

class Franchisee extends \Eloquent {
	protected $fillable = [];
	
	protected $table = 'franchisees';
	
	
	public function Users(){
		
		return $this->hasMany('User','franchisee_id');
	}

	public static function updateFranchisee($inputs){
		$franchisee=Franchisee::find($inputs['franchisee_id']);
		$franchisee->franchisee_name=$inputs['franchisee_name'];
		$franchisee->franchisee_address=$inputs['franchisee_address'];
		$franchisee->franchisee_phone=$inputs['ph_no'];
		$franchisee->financial_year_start_date=$inputs['start_date'];
		$franchisee->financial_year_end_date=$inputs['end_date'];
		$franchisee->franchisee_official_email=$inputs['email'];
		$franchisee->updated_by=Session::get('userId');
		$franchisee->updated_at=date("Y-m-d H:i:s");
		$franchisee->save();
		return $franchisee;
	}


	public static function addNewFranchisee($inputs){
		$franchisee= new Franchisee();
		$franchisee->franchisee_name=$inputs['franchiseeName'];
		$franchisee->franchisee_official_email=$inputs['franchiseeEmail'];
		$franchisee->franchisee_phone=$inputs['franchiseePhno'];
		$franchisee->franchisee_address=$inputs['franchiseeAddress'];
		$franchisee->created_by=Session::get('userId');
		$franchisee->created_at=date("Y-m-d H:i:s");
		$franchisee->save();
		return $franchisee;
	}


	public static function getFranchiseeList(){
		return Franchisee::paginate(10);
	}

	public static function getFList(){
		return Franchisee::select('id','franchisee_name')->get();
	}
	public static function getFinancialStartDates(){
	     $pst = date('m');
             $dates = array();
             if($pst>=4) {
                $y=date('Y');
                $dtt=$y."-04-01";
                $dates[start_date] = $dtt;
                $pt = date('Y', strtotime('+1 year'));
                $ptt=$pt."-03-31";
                $dates[end_date] = $ptt;
             }
             else {
                $y=date('Y', strtotime('-1 year'));
                $dtt=$y."-04-01";
                $dates[start_date] = $dtt;
                $pt =date('Y');
                $ptt=$pt."-03-31";
                $dates[end_date] = $ptt;
             }
            return $dates;

	}	
	
	public static function getDataForthisYear($startDate){
        return Franchisee::where('id', '=', Session::get(franchiseId))
                                    ->where('financial_year_start_date', '=', $startDate['start_date'])
                                    ->where('financial_year_end_date', '=', $startDate['end_date'])
                                    ->get();
    	}

    	public static function updateInvoiceNumber($invoiceNo){
        	$data = Franchisee::where('id', '=', Session::get(franchiseId))
                                    ->update(['max_invoice' => $invoiceNo]);
        	return $data;
    	}

	public static function updateFinancialYears($dates){
	//	return $dates;
		$data =	Franchisee::where('id', '=', Session::get(franchiseId))
                                  ->update(['financial_year_start_date' => $dates['start_date'],
				]);
		return $data;
	}
	
}
