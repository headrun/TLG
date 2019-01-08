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

	public static function createdNewFranchisee($inputs) {
		$fianancialYearDates = Franchisee::getFinancialStartDates();
		$franchisee= new Franchisee();
		$franchisee->franchisee_name = $inputs['firstName'].' '.$inputs['lastName'];
		$franchisee->franchisee_official_email=$inputs['franchiseeEmail'];
		$franchisee->franchisee_phone=$inputs['franchiseePhno'];
		$franchisee->franchisee_address=$inputs['franchiseeAddress'];
		$franchisee->invoice_code = $inputs['invoiceCode'];
		$franchisee->franchisee_legal_entity = $inputs['legalEntity'];
		$franchisee->financial_year_start_date = $fianancialYearDates['start_date'];
		$franchisee->financial_year_end_date = $fianancialYearDates['end_date'];
		$franchisee->max_invoice = 0;
		$franchisee->created_by=Session::get('userId');
		$franchisee->created_at=date("Y-m-d H:i:s");
		$franchisee->save();
		return $franchisee;
	}

	public static function updateExistingFranchisee($inputs) {
		$updateFranchisee = Franchisee::where('id', '=', $inputs['franchisee_id'])
		                              ->update([
		                              		'franchisee_name' => $inputs['franchisee_name'],
		                              		'franchisee_phone' => $inputs['franchiseePhno'],
		                              		'franchisee_address' => $inputs['franchiseeAddress'],
		                              		'invoice_code' => $inputs['invoiceCode'],
		                              		'franchisee_legal_entity' => $inputs['legalEntity'],
		                              		'updated_at' => date("Y-m-d H:i:s")
		                              	]);
		return $updateFranchisee;
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
                $dates['start_date'] = $dtt;
                $pt = date('Y', strtotime('+1 year'));
                $ptt=$pt."-03-31";
                $dates['end_date'] = $ptt;
             }
             else {
                $y=date('Y', strtotime('-1 year'));
                $dtt=$y."-04-01";
                $dates['start_date'] = $dtt;
                $pt =date('Y');
                $ptt=$pt."-03-31";
                $dates['end_date'] = $ptt;
             }
            return $dates;

        }
	
	public static function getDataForthisYear($dates){
        	$data =  Franchisee::where('id', '=', Session::get(franchiseId))
                                    ->where('financial_year_start_date', '=', $dates['start_date'])
                                    ->where('financial_year_end_date', '=', $dates['end_date'])
                                    ->get();
		return $data;
        }

        public static function updateInvoiceNumber($invoiceNo){
		$data = new Franchisee();
                $data->exists = true;
                $data->id = Session::get('franchiseId');
                $data->max_invoice = $invoiceNo;
                $data->save();

                return $data;
        }

        public static function updateFinancialYears($dates){
		$data = new Franchisee();
		$data->exists = true;
		$data->id = Session::get(franchiseId);
		$data->financial_year_start_date = $dates['start_date'];
		$data->financial_year_end_date = $dates['end_date'];
		$data->max_invoice = '1';
		$data->save();

                return $data;
        }

	public static function invoiceForMembership(){
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
		return $invoiceNo;
	}
	
	
}
