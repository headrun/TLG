<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
class FranchiseeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /franchisee
	 *
	 * @return Response
	 */

	public static function addNewFranchisee() {

		if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){

			
			$mainMenu     =  "FRANCHISEE_MAIN";

			$currentPage  =  "NEWFRANCHISEE";
			
      		$viewData = array('currentPage','mainMenu');
      		return View::make('pages.franchisee.addfranchisee',compact($viewData)); 
      
		}else{

			return Redirect::action('VaultController@logout');

		}
	} 

	public static function franchiseeList() {
		if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){

			
			$mainMenu     =  "FRANCHISEE_MAIN";

			$currentPage  =  "LISTOFFRANCHISEE";

			$franchiseeList = Franchisee::getFranchiseeList();


      		$viewData = array('currentPage','mainMenu','franchiseeList');
      		return View::make('pages.franchisee.franchiseelist',compact($viewData)); 
      
		}else{

			return Redirect::action('VaultController@logout');

		}	
	}

	public static function updateFranchisee(){
		if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){

			$inputs=Input::all();
			$status=Franchisee::updateFranchisee($inputs);
			if($status){			
				return Response::json(array('status'=>'success'));
			}else{
				return Response::json(array('status'=>'failure'));
			}
		}else{

		}
	}

	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /franchisee/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /franchisee
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /franchisee/{id}
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
	 * GET /franchisee/{id}/edit
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
	 * PUT /franchisee/{id}
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
	 * DELETE /franchisee/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}