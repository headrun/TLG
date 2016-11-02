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

	public static function getFranchiseeList(){
		return Franchisee::paginate(10);
	}
	
	
}