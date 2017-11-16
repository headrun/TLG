<?php

class Complaint extends \Eloquent {
	protected $fillable = [];
        protected $table='complaints';
        
        static function createComplaint($input){
           $complaint=new Complaint();
           $complaint->customer_id=$input['customer_id'];
           $complaint->student_id=$input['student_id'];
           $complaint->franchisee_id=Session::get('franchiseId');
           $complaint->created_at= date("Y-m-d H:i:s");
           $complaint->created_by    = Session::get('userId');
           $complaint->save();
           return $complaint;
           
        }
        static function getComplaintByCustomerId($customerId){
            return Complaint::where('customer_id','=',$customerId)
                             ->where('franchisee_id','=',Session::get('franchiseId'))
                             ->get();
        }
        static function getComplaintsForActivityReport($inputs){
            $ComplaintsActivityReportDetails['data'] = Complaint::where('franchisee_id','=',Session::get('franchiseId'))
                             ->whereDate('created_at','>=',$inputs['reportStartDate'])
                             ->whereDate('created_at','<=',$inputs['reportEndDate'])
                             ->select('student_id','customer_id','created_at')
                             ->groupby('student_id')
                             ->get();

            for($i=0;$i<count($ComplaintsActivityReportDetails['data']);$i++){

                $temp=Customers::find($ComplaintsActivityReportDetails['data'][$i]['customer_id']);

                $ComplaintsActivityReportDetails['data'][$i]['customer_name']=$temp->customer_name." ".$temp->customer_lastname;

                $temp2= Students::find($ComplaintsActivityReportDetails['data'][$i]['student_id']);

                $ComplaintsActivityReportDetails['data'][$i]['student_name']=$temp2->student_name;

                $ComplaintsActivityReportDetails['data'][$i]['payment_due_for']= 'COMPLAINTS';

                $temp3=  Comments::where('student_id','=',$ComplaintsActivityReportDetails['data'][$i]['student_id'])
                                  ->where('reminder_date','!=','null')
                                  ->orderby('created_at','DESC')
                                  ->where('followup_type','=','COMPLAINTS')
                                  ->limit(1)
                                  ->get();
                                  
                if(isset($temp3) && !empty($temp3)){
                    $ComplaintsActivityReportDetails['data'][$i]['created_at']=$temp3[0]['reminder_date'];  
                }
               
            }
        return $ComplaintsActivityReportDetails;
      }
}