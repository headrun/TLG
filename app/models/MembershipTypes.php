<?php

class MembershipTypes extends \Eloquent {
	protected $fillable = [];
	protected $table= 'membership_types';
	
	public function CustomerMembership(){
	
		return $this->has('CustomerMembership', 'membership_type_id');
	}
	
	
	static function getMembershipTypesForSelectBox(){
		
		return MembershipTypes::where('franchisee_id','=',Session::get('franchiseId'))
                                        ->lists('name', 'id');
	}
	
	static function getMembershipTypes(){
	
            return MembershipTypes::where('franchisee_id','=',Session::get('franchiseId'))->get();
	}
	
	
	static function getMembershipTypeByID($membershipId){
		return MembershipTypes::find($membershipId);
	}

	public static function insertNewAnnaulMembershipFranchisee($inputs, $franchiseId) {
		$membership = new MembershipTypes();
		$membership->name = 'Annual';
		$membership->description = 'Annual Membership';
		$membership->year_interval = 1;
		$membership->fee_amount = $inputs['annaul_membership'];
		$membership->created_at = date("Y-m-d H:i:s");
		$membership->created_by = Session::get('userId');
		$membership->franchisee_id = $franchiseId;
		$membership->save();
		return $membership;
	}

	public static function insertNewLifetimeMembershipFranchisee($inputs, $franchiseId) {
		$membership = new MembershipTypes();
		$membership->name = 'Lifetime';
		$membership->description = 'Lifetime Membership';
		$membership->year_interval = 25;
		$membership->fee_amount = $inputs['lifetime_membership'];
		$membership->created_at = date("Y-m-d H:i:s");
		$membership->created_by = Session::get('userId');
		$membership->franchisee_id = $franchiseId;
		$membership->save();
		return $membership;
	}

	public static function updateAnnaulMembershipFranchisee($inputs) {
		$membership = MembershipTypes::where('franchisee_id', '=', $inputs['franchisee_id'])
		                             ->where('name','=','Annual')
									 ->update([
							 			'name' => 'Annual',
							 			'description' => 'Annual Membership',
							 			'fee_amount' => $inputs['annaul_membership'],
							 			'updated_by' => Session::get('userId')
									 ]);
		return $membership;
	}

	public static function updateLifetimeMembershipFranchisee($inputs) {
		$membership = MembershipTypes::where('franchisee_id', '=', $inputs['franchisee_id'])
									 ->where('name','=','Lifetime')
									 ->update([
							 			'name' => 'Lifetime',
							 			'description' => 'Lifetime Membership',
							 			'fee_amount' => $inputs['lifetime_membership'],
							 			'updated_by' => Session::get('userId')
									 ]);
		return $membership;
	}
}