<?php

class Retention extends \Eloquent {
	protected $fillable = [];
        protected  $table='retention';
        
     static function createRetention($input){
           $retention=new Retention();
           $retention->customer_id=$input['customer_id'];
           $retention->student_id=$input['student_id'];
           $retention->franchisee_id=Session::get('franchiseId');
           $retention->created_at= date("Y-m-d H:i:s");
           $retention->created_by    = Session::get('userId');
           $retention->save();
           return $retention;
     }
     static function getRetentionByCustomerId($customerId){
           return Retention::where('customer_id','=', $customerId)
                             ->where('franchisee_id','=',Session::get('franchiseId'))
                             ->orderby('id','DESC')
                             ->selectRaw('max(id) as id, student_id')
                             ->groupBy('student_id')
                             ->get();
        }
     static function getRetentionForActivityReport($inputs){
           $getRetentionForActivityReport['data'] = IntroVisit::where('franchisee_id','=',Session::get('franchiseId'))
             ->whereDate('created_at','>=',$inputs['reportStartDate'])
                 ->whereDate('created_at','<=',$inputs['reportEndDate'])
                 ->select('student_id','customer_id','created_at')
                 ->get();
           for($i=0;$i<count($getRetentionForActivityReport['data']);$i++){

              $temp=  Customers::find($getRetentionForActivityReport['data'][$i]['customer_id']);
              
              $getRetentionForActivityReport['data'][$i]['customer_name']=$temp->customer_name." ".$temp->customer_lastname;
              
              $temp2=  Students::find($getRetentionForActivityReport['data'][$i]['student_id']);
              
              $getRetentionForActivityReport['data'][$i]['student_name'] = isset($temp2) && !empty($temp2) ? $temp2[0]['student_name'] : "";
              
              $getRetentionForActivityReport['data'][$i]['payment_due_for']= 'RETENTION';
              
              $temp3=  Comments::where('student_id','=',$getRetentionForActivityReport['data'][$i]['student_id'])
                                ->where('reminder_date','!=','null')
                                ->orderby('created_at','DESC')
                                ->limit(1)
                                ->get();

              if(isset($temp3) && count($temp3)>0){
                  $getRetentionForActivityReport['data'][$i]['reminder_date'] = isset($temp3) && !empty($temp2) ? $temp3[0]['reminder_date'] : ""; 
              }


        }
        return $getRetentionForActivityReport;
    }
}