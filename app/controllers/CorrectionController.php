<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class CorrectionController extends \BaseController {

	public function madecorrections(){
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
        

        public function deleteBatch() {
        	if(Auth::check() && (Session::get('userType')=='ADMIN')) {
        		$inputs=Input::all();
        		$studentClasses = StudentClasses::where('franchisee_id','=',Session::get('franchiseId'))
        		                                 ->where('batch_id','=',$inputs['batch_id'])
        		                                 ->count();
        		if($studentClasses == 0) {
        			$batch = Batches::where('franchisee_id','=',Session::get('franchiseId'))
        		                 ->where('id','=',$inputs['batch_id'])
        		                 ->delete();
        		return Response::json(array('status'=>'success'));
        	}else{
        		return Response::json(array('status'=>'classes'));
        	}
        		
        	}else{
                return Response::json(array('status'=>'failure')); 
            }
        }


        public function deleteStudent() {
        	if(Auth::check() && (Session::get('userType')=='ADMIN')) {
        		$inputs=Input::all();
        		$studentClasses = StudentClasses::where('franchisee_id','=',Session::get('franchiseId'))
        		                                 ->where('student_id','=',$inputs['student_id'])
        		                                 ->count();
        		$paymentDues = PaymentDues::where('franchisee_id','=',Session::get('franchiseId'))
        		                           ->where('student_id','=',$inputs['student_id'])
        		                           ->count(); 
        		$paymentDues = PaymentDues::where('franchisee_id','=',Session::get('franchiseId'))
        		                           ->where('student_id','=',$inputs['student_id'])
        		                           ->count();
        			$studentClasses = StudentClasses::where('franchisee_id','=',Session::get('franchiseId'))
        		                                     ->where('student_id','=',$inputs['student_id'])
        		                                     ->delete();
	                $paymentDues = PaymentDues::where('franchisee_id','=',Session::get('franchiseId'))
	                                            ->where('student_id','=',$inputs['student_id'])
	                                            ->delete();
	                $orders = Orders::where('franchisee_id','=',Session::get('franchiseId'))
	        		                           ->where('student_id','=',$inputs['student_id'])
	        		                           ->delete();
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


