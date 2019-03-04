 	<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class UsersController extends \BaseController {
        
    
    
        public function changepassword(){
            if(Auth::check() && (Session::get('userType')=='ADMIN')){
                $currentPage  =  "ChangePassword_LI";
		$mainMenu     =  "SETTINGS_MENU_MAIN";
		
                $users_data= User::where('franchisee_id','=',Session::get('franchiseId'))->get();
                
                $viewData = array (
					'users_data',
					'currentPage',
					'mainMenu',
					 
			);
			return View::make ( 'pages.users.changepassword', compact ( $viewData ) );
            }else{
                return Redirect::action('VaultController@logout');
            }
        }

        public function kidsEndDates(){
             if(Auth::check() && (Session::get('userType')=='ADMIN')){
                $currentPage  =  "Madecorrections_LI";
		$mainMenu     =  "EASY_CORRECTIONS_MENU_MAIN";
		
                $users_data= User::where('franchisee_id','=',Session::get('franchiseId'))->get();
                
                $viewData = array (
					'users_data',
					'currentPage',
					'mainMenu',
					 
			);
			return View::make ( 'pages.users.madecorrections', compact ( $viewData ) );
            }else{
                return Redirect::action('VaultController@logout');
            }
        }

        
        public function updatepassword(){
            if(Auth::check() && (Session::get('userType')=='ADMIN')){
                $inputs=Input::all();
                $user=User::find($inputs['user_id']);
                $user->password=Hash::make($inputs['password']);
                $user->save();
                return Response::json(array('status'=>'success'));
            }else{
                return Response::json(array('status'=>'failure')); 
            }
        }

        public static function addAdminUser(){
			if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){
				$inputs=Input::all();
				$AdminUser=User::createNewAdminUser($inputs);
				if($AdminUser){
					return Response::json(array('status'=>'success'));
				}
				return Response::json(array('status'=>'failure'));
			}
			return Response::json(array('status'=>'failure'));
		}

		public static function updateAlltheTablesWithTheseRecords ($finalData) {
			  // return count($finalData);
			// update student classes
			// map with payments dues with that studengt class id ->$payment[0]['payment_no'];
			// checck order
			foreach ($finalData as $key => $value) {
				$updateToStudentClasses = StudentClasses::where('franchisee_id','=', Session::get('franchiseId'))
			                          ->where('student_id','=',$value['student_id'])
			                          ->where('id','=',$value['student_classes_id'])
			                          ->update(['enrollment_end_date'=>$value['enroll_last_date']]);
			    $updateToPaymentsDues = PaymentDues::where('franchisee_id','=', Session::get('franchiseId'))
			                                         ->where('student_id','=',$value['student_id'])
			                                         ->where('student_class_id','=',$value['student_classes_id'])
			                                         ->update(['end_order_date'=>$value['enroll_last_date']]);
			}
			// return $updateToStudentClasses;
			
		}

        public function updateEndDates() {
        	if(Auth::check() && (Session::get('userType')=='ADMIN')) {
        		$inputs=Input::all();
        		$getHolidays = Holidays::where('franchisee_id','=', Session::get('franchiseId'))
		                        ->get(); 
		        $toBeUpdateArray = [];            
              	$studentClasses = StudentClasses::where('franchisee_id', '=', Session::get('franchiseId'))
                                               ->where('enrollment_end_date','>=',date('Y-m-d'))
    		                                   /*->whereDate('enrollment_start_date','<=',date('Y-m-d',strtotime($getHolidays[$i]['startdate'])))
                                               ->whereDate('enrollment_end_date','>=',date('Y-m-d',strtotime($getHolidays[$i]['startdate'])))*/
    		                                    ->get();
	             
		         
		         $toBeUpdateArray = [];
	             $holidaysArray = [];
	             $count = 0;
	             for ($i=0; $i < count($getHolidays); $i++) { 
	                for ($j=0; $j < count($studentClasses); $j++) { 
	             	 $holiDay = date('l', strtotime($getHolidays[$i]['startdate']));
	             	 $studentEnrollDay = date('l', strtotime($studentClasses[$j]['enrollment_start_date']));
	             		if ($holiDay === $studentEnrollDay && $studentClasses[$j]['enrollment_start_date'] <= date('Y-m-d',strtotime($getHolidays[$i]['startdate'])) && $studentClasses[$j]['enrollment_end_date'] >= date('Y-m-d',strtotime($getHolidays[$i]['startdate']))) {
	             			$studentClasses[$j]['holiDay'] = 1;
	             			$toBeUpdateArray[] = $studentClasses[$j];
	             		} 
	             	}
	             }	
	            $updatedWeeksArray = [];
	            for ($i=0; $i < count($toBeUpdateArray); $i++) { 
	            	$addNoOfDays = $toBeUpdateArray[$i]['holiDay'] * 7;
	            	if(array_key_exists($toBeUpdateArray[$i]['student_id'], $updatedWeeksArray)) {
	            		$endDateUpdate = date('Y-m-d', strtotime($updatedWeeksArray[$toBeUpdateArray[$i]['student_id']]['enroll_last_date'].'+'.$addNoOfDays.' days'));
	            		$student['student_id'] = $toBeUpdateArray[$i]['student_id'];
	            		$student['enroll_last_date'] = $endDateUpdate;
	            		$student['student_classes_id'] = $toBeUpdateArray[$i]['id'];
	            		$updatedWeeksArray[$toBeUpdateArray[$i]['student_id']] = $student;
	            	} else {
	            		$endDateUpdate = date('Y-m-d', strtotime($toBeUpdateArray[$i]['enrollment_end_date'].'+'.$addNoOfDays.' days'));
	            		$student['student_id'] = $toBeUpdateArray[$i]['student_id'];
	            		$student['enroll_last_date'] = $endDateUpdate;
	            		$student['student_classes_id'] = $toBeUpdateArray[$i]['id'];
	            		$updatedWeeksArray[$toBeUpdateArray[$i]['student_id']] = $student;
	            	}
	            }  
	             // return $updatedWeeksArray;	
	            $update = self::updateAlltheTablesWithTheseRecords($updatedWeeksArray);
	           // return $update;
        		return Response::json(array('status'=>'success'));
        	}else{
                return Response::json(array('status'=>'failure')); 
            }
        }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
