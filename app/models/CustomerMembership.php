<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
class CustomerMembership extends \Eloquent {
	protected $fillable = [];
	protected $table = 'customer_membership';
	
	public function Customers(){
	
		return $this->hasMany('Customers', 'customer_id');
	}
	
	public function MembershipTypes(){
	
		return $this->belongsTo('MembershipTypes', 'membership_type_id');
	}
        
	static function addMembership($inputs){
        $present_date=Carbon::now();
		$customerMembership = new CustomerMembership();
		$customerMembership->customer_id        = $inputs['customer_id'];
		$customerMembership->membership_type_id = $inputs['membership_type_id'];
		$customerMembership->status             = "active";
		$customerMembership->action             = "default";
        $customerMembership->membership_start_date=$present_date->toDateString();
            if(isset($inputs['membership_type_id'])){
                $interval=  MembershipTypes::find($inputs['membership_type_id']);
                $present_date=$present_date->addYears($interval->year_interval);
                $customerMembership->membership_end_date=$present_date->toDateString();    
            }
		$customerMembership->created_by         = Session::get('userId');
		$customerMembership->created_at         = date("Y-m-d H:i:s");
		$customerMembership->save();
		
		return $customerMembership;
		
	}
	
	static function getCustomerMembership($customerId){
		return CustomerMembership::where("customer_id", "=", $customerId)
					   ->whereDate('membership_start_date','<=',date("Y-m-d"))    
                                           ->whereDate('membership_end_date','>=',date("Y-m-d"))
                                           ->count();
						
	}
         static function getMembertodaysRegCount(){
            return CustomerMembership::join('customers', 'customer_membership.customer_id', '=', 'customers.id')
                                            ->whereDate('customer_membership.created_at', '=',  date("Y-m-d"))                                         
					    ->where('customers.franchisee_id', '=',  Session::get('franchiseId'))
					    ->count();
        }
        static function getMemberCount(){
            return CustomerMembership::join('customers', 'customer_membership.customer_id', '=', 'customers.id')                                       
                                            ->where('customers.franchisee_id', '=',  Session::get('franchiseId'))
                                            ->count();
        
        }
        static function getNonMembertodaysRegCount(){
//            
                            
                           $s=DB::table('customers')
                                ->leftJoin('customer_membership', 'customer_id', '=', 'customers.id')        
                                ->whereDate('customers.created_at', '=',  date("Y-m-d"))     
                                ->where('customers.franchisee_id','=',Session::get('franchiseId'))
                                ->where('membership_type_id','=',null)
                                ->count('customers.id');    
                       return $s; 
                    }
        static function getNonMemberCount(){
                            $s=DB::table('customers')
                                ->leftJoin('customer_membership', 'customer_id', '=', 'customers.id')
                                ->where('customers.franchisee_id', '=',  Session::get('franchiseId'))
                                ->where('membership_type_id','=',null)
                                ->count('customers.id');    
                       return $s; 
        }

        static function getCustomerMembershipDetails($customerId){
            $customer_mem_data=  CustomerMembership::
                                        join('membership_types','membership_types.id','=','membership_type_id')
                                        ->join('orders','orders.membership_id','=','customer_membership.id')
                                        ->where('customer_membership.customer_id','=',$customerId)
                                        ->select('description','membership_start_date','membership_end_date','customer_membership.id', 'orders.id as order_id')
                                        ->get();
            foreach($customer_mem_data as $mem) {

                $mem->enc_order_id=crypt::encrypt($mem->order_id);               

            }
            return $customer_mem_data;

        }
}