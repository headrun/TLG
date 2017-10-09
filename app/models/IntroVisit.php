<?php
use Carbon\Carbon;

class IntroVisit extends \Eloquent {
	protected $fillable = [];
	protected $table= 'introvisit';
	
	public function Customers(){
		return $this->belongsTo('Customers','customer_id');
	}
	
	public function Students(){
		return $this->belongsTo('Students','student_id');
	}
	
	public function Classes(){
	
		return $this->belongsTo('Classes', 'class_id');
	}
	
	public function Batches(){
		return $this->belongsTo('Batches', 'batch_id');
	}
	

	
	
	static function addSchedule($inputs){
		
		$introVisit = new IntroVisit();
		
		$introVisit['customer_id']      = $inputs['customerId'];
		$introVisit->student_id         = $inputs['studentIdIntroVisit'];
		
		$introVisit->class_id           = $inputs['eligibleClassesCbx'];
		$introVisit->batch_id           = $inputs['introbatchCbx'];	
		//$introVisit->status             = 'ACTIVE/SCHEDULED';
		
		$introVisit->franchisee_id      = Session::get('franchiseId');		
		if(isset($inputs['introVisitTxtBox']) && $inputs['introVisitTxtBox'] !=''){
			$introVisit->iv_date            = date('Y-m-d',strtotime($inputs['introVisitTxtBox']));
		}
		$introVisit->created_by         = Session::get('userId');
		$introVisit->created_at         = date("Y-m-d H:i:s");
		$introVisit->save();
		
		
		$customerObj = Customers::find($inputs['customerId']);
		if($customerObj){
			
			$customerObj->stage = "IV SCHEDULED";
			$customerObj->save();
		}
		
		return $introVisit;
		
	}
	
	static function getIntrovisitBytoday(){
	
		return IntroVisit::whereDate('iv_date', '=',  date("Y-m-d"))
						//	->whereIn('status',array('ACTIVE/SCHEDULED','RESCHEDULED'))
                                                       
							->where('franchisee_id', '=',  Session::get('franchiseId'))
							->count();
	}
	
	static function getAllActiveIntrovisit(){
	
		return IntroVisit::with('Customers', 'Classes', 'Batches', 'Students')

						//	->whereIn('status',array('ACTIVE/SCHEDULED','RESCHEDULED'))
							->where('franchisee_id', '=',  Session::get('franchiseId'))
							->orderBy('iv_date','DESC')
							->get();
	}
	
	static function getIntrovisitByStudentId($studentId){
		
		return IntroVisit::with('Classes','Batches')
							->where('student_id', '=', $studentId)
							->where('franchisee_id', '=',  Session::get('franchiseId'))
							->get();
	}
        static function getIntrovistCount(){
            return  IntroVisit:: where('franchisee_id', '=',  Session::get('franchiseId'))
                                ->count();
        }
	
       static function getAllIntrovisitforReport($inputs){
        	$present_date = Carbon::now();
            $introvisit['data']= Introvisit::where('franchisee_id','=',Session::get('franchiseId'))
                               ->whereDate('created_at','>=',$inputs['reportGenerateStartdate'])
                               ->whereDate('created_at','<=',$inputs['reportGenerateEnddate'])
                               ->get();
            //return $present_date;
            for($i=0;$i<count($introvisit['data']);$i++){
                $temp=  Customers::find($introvisit['data'][$i]['customer_id']);
                $introvisit['data'][$i]['customer_name']=$temp->customer_name." ".$temp->customer_lastname;
                $temp2=  Students::find($introvisit['data'][$i]['student_id']);
                $introvisit['data'][$i]['student_name']=$temp2->student_name;
                $temp4= StudentClasses::find($introvisit['data'][$i]['student_id']);
                $introvisit['data'][$i]['status']=$temp2->status;
                if($introvisit['data'][$i]['status']==''){
                	if($introvisit['data'][$i]['iv_date'] <= $present_date){
                	   $introvisit['data'][$i]['status'] ='Attended';
                	}
                	else{
                	   $introvisit['data'][$i]['status'] ='IV SCHEDULED';
                	}
                }
                if($introvisit['data'][$i]['batch_id']!=null){
                    $temp3= Batches::find($introvisit['data'][$i]['batch_id']);
                    $introvisit['data'][$i]['batch_name']=$temp3->batch_name;
                }
            }
            return $introvisit;
        }	
	
}
