<?php

class Inquiry extends \Eloquent {
	protected $fillable = [];
        protected $table='inquiry';
        
    static function createInquiry($input){
           $inquiry=new Inquiry();
           $inquiry->customer_id=$input['customer_id'];
          // $inquiry->student_id=$input['student_id'];
           $inquiry->franchisee_id=Session::get('franchiseId');
           $inquiry->created_at= date("Y-m-d H:i:s");
           $inquiry->created_by    = Session::get('userId');
           $inquiry->save();
           return $inquiry;
    }
    static function getInquiryByCustomerId($customerId){
            return Inquiry::where('customer_id','=',$customerId)
                             ->where('franchisee_id','=',Session::get('franchiseId'))
                             ->get();
    }
    static function getAllInquiryforReport($inputs){
         $inquiry['data']= Inquiry::where('franchisee_id','=',Session::get('franchiseId'))
                               ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                               ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                               ->get();
          //$finalArray = array();
         for($i=0;$i<count($inquiry['data']);$i++){
              $temp=  Customers::find($inquiry['data'][$i]['customer_id']);
                //$inquiry['data'][$i]['customer_name'] = "";
              if (!empty($temp)) {
                  if (gettype($temp) == "array") {
                      $inquiry['data'][$i]['customer_name']= $temp['customer_name']." ".$temp['customer_lastname'];
                  }elseif(gettype($temp) == "object"){
                      $inquiry['data'][$i]['customer_name']= $temp->customer_name." ".$temp->customer_lastname;
                  } 
                  //array_push($finalArray, $inquiry['data'][$i]);
              }
                
         }
         return $inquiry;
            
    }
        
}