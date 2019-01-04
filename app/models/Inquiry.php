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
    static function getInquiryForActivityReport($inputs){
        $getInquiryForActivityReport['data'] = Inquiry::where('franchisee_id','=',Session::get('franchiseId'))
                         ->whereDate('created_at','>=',$inputs['reportStartDate'])
                         ->whereDate('created_at','<=',$inputs['reportEndDate'])
                         ->select('customer_id','created_at')
                         ->orderby('created_at','DESC')
                         ->limit(1)
                         ->get();
        for($i=0;$i<count($getInquiryForActivityReport['data']);$i++){

            $temp=  Customers::find($getInquiryForActivityReport['data'][$i]['customer_id']);

            $getInquiryForActivityReport['data'][$i]['customer_name'] = $temp->customer_name." ".$temp->customer_lastname;

            $temp2=  Students::where('customer_id','=',$getInquiryForActivityReport['data'][$i]['customer_id'])->get();

            $getInquiryForActivityReport['data'][$i]['student_name'] = isset($temp2) && !empty($temp2) ? $temp2[0]['student_name'] : "";

            $getInquiryForActivityReport['data'][$i]['payment_due_for'] = 'INQUIRY';

            $temp3=  Comments::where('student_id','=',$getInquiryForActivityReport['data'][$i]['student_id'])
                              ->where('reminder_date','!=','null')
                              ->orderby('created_at','DESC')
                              ->where('followup_type','=','INQUIRY')
                              ->limit(1)
                              ->get();
                              
            if(isset($temp3) && !empty($temp3)){
                $getInquiryForActivityReport['data'][$i]['created_at']=$temp3[0]['reminder_date']; }
        }
        return $getInquiryForActivityReport;
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

    static function lastTwoInqNotShed ($inputs) {
      $lastTwoDays = date('Y-m-d', strtotime('-2 day', strtotime($inputs['start_date'])));
      $inquiry = Inquiry::where('franchisee_id', '=', Session::get('franchiseId'))
                        ->whereDate('created_at', '>=',$lastTwoDays)
                        ->whereDate('created_at', '<=',$inputs['start_date'])
                        ->get();
                        

      $iv_ids = array();
      foreach ($inquiry as $key => $value) {
        $data = IntroVisit::where('franchisee_id', '=', Session::get('franchiseId'))
                          ->where('customer_id', '=', $value['customer_id'])
                          ->where('iv_date', '>=',$lastTwoDays)
                          ->count();
        $iv_ids[$key]['customer_id'] = $value['customer_id'];
        $iv_ids[$key]['status_count'] = $data;
      }
      for ($i=0; $i <count($iv_ids) ; $i++) {
        if($iv_ids[$i]['status_count'] == 0) {  
          $customers = Customers::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->where('id', '=', $iv_ids[$i]['customer_id'])
                                ->get();
          $iv_ids[$i]['customer_name'] = $customers[0]['customer_name'] . $customers[0]['customer_lastname'];
          $iv_ids[$i]['mobile_no'] = $customers[0]['mobile_no'];
          $iv_ids[$i]['email'] = $customers[0]['customer_email'];
        }
      }
      return $iv_ids;

    } 
}