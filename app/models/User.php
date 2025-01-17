<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	
	
	
	
	protected $hidden = array('password', 'remember_token');
	
	public function Franchisee(){
		return $this->belongsTo('Franchisee','franchisee_id');
	}
	
	public function Courses(){
		return $this->hasMany('Courses','created_by');
	}
        public function ClassBasePrice(){
		return $this->hasMany('Courses','created_by');
	}
	
	public function Comments(){
		return $this->hasMany('Comments','created_by');
	}
	
	public function LeadInstructors(){
		return $this->hasMany('batches','lead_instructor');
	}
	
	public function AlternateInstructors(){
		return $this->hasMany('batches','alternate_instructor');
	}
	
	
	static function getInstructors(){
		
		return User::select('id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))
					->where("user_type", "=", "INSTRUCTOR")
					->where("franchisee_id", "=", Session::get('franchiseId') )
					->lists("full_name", 'id');
	}
	
	
	static function getUsersByFranchisee(){
		
		return User::where("franchisee_id", "=", Session::get('franchiseId') )
					->where("user_type", "<>", "ADMIN")
					->where("status","=",'active')
					->get();
	}
	
	
	static function addUser($inputs){
		
		$user = new User();
		$user->first_name    = $inputs['firstName'];
		$user->last_name     = $inputs['lastName'];
		$user->email         = $inputs['email'];
		$user->mobile_no     = $inputs['mobileNo'];
		$user->user_type     = $inputs['userType'];
		$user->franchisee_id = $inputs['franchiseeId'];
		$user->password = $inputs['password'];
		$user->status   = 'active';
        $user->save();
		
		return $user;
		
	}
	
	static function getUsersByUserId($id){
	
		/* echo $id;
		exit(); */
		return User::where('id','=',$id)->take(1)->get();
		//return User::find($id)->get();
	}
	
	static function editUser($id, $inputs){
		
		$User = User::find($id);
		
		/* echo '<pre>';
		print_r($User);
		echo '</pre>';
		exit(); */
		
		$User->first_name = $inputs['firstName'];
		$User->last_name  = $inputs['lastName'];
		$User->mobile_no  = $inputs['mobileNo'];
		$User->email      = $inputs['email'];
		$User->user_type  = $inputs['userType'];
		$User->save();
		
		return $User;
		
		
	}
        
        static function getTeachersByFranchiseeId($franchisee_id){
            return User::where('franchisee_id','=',$franchisee_id)
                         ->where('user_type','=','INSTRUCTOR')
                         ->get();
        }

    static public function createNewAdminUser($inputs){
    	$newAdminUser= new User();
    	$newAdminUser->first_name = $inputs['AdminFirstName'];
    	$newAdminUser->last_name =  $inputs['AdminLastName'];
    	$newAdminUser->user_type = 'ADMIN';
    	$newAdminUser->franchisee_id = $inputs['FName'];
    	$newAdminUser->mobile_no = $inputs['AdminMobileNo'];
    	$newAdminUser->email =$inputs['AdminEmail'];
    	$newAdminUser->password=Hash::make('secret');
    	$newAdminUser->created_by=Session::get('userId');
    	$newAdminUser->created_at=date("Y-m-d H:i:s");
		$newAdminUser->save();
		return $newAdminUser;
    }

    static public function insertNewAdminUser($inputs, $franchiseeId){
    	$newAdminUser= new User();
    	$newAdminUser->first_name = $inputs['firstName'];
    	$newAdminUser->last_name =  $inputs['lastName'];
    	$newAdminUser->user_type = 'ADMIN';
    	$newAdminUser->franchisee_id = $franchiseeId;
    	$newAdminUser->mobile_no = $inputs['franchiseePhno'];
    	$newAdminUser->email = $inputs['user_mail_id'];
    	$newAdminUser->password=Hash::make($inputs['password']);
    	$newAdminUser->created_by=Session::get('userId');
    	$newAdminUser->created_at=date("Y-m-d H:i:s");
		$newAdminUser->save();

		return $newAdminUser;
    }

}
