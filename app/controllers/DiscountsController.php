<?php

class DiscountsController extends \BaseController {



	public function add_or_view_discounts(){
		if (Auth::check ()) {
                
            $currentPage = "AddDiscounts_LI";
            $mainMenu = "DISCOUNTS_MENU_MAIN";
            $discount_data=  Discounts::where('franchisee_id','=',Session::get('franchiseId'))
                           ->orderBy('number_of_classes','=','DESC') 
                           ->get();
            for($i=0;$i<sizeof($discount_data);$i++){
                $user_data=User::where('id','=',$discount_data[$i]['created_by'])->get();
                $user_data=$user_data[0];
                $discount_data[$i]['created_by_name']=$user_data['first_name'].$user_data['last_name'];
            }

            $viewdata=array('currentPage','mainMenu', 'discount_data');
            return View::make('pages.Discounts.add_or_view_discounts',  compact($viewdata));
        }else{
            return Redirect::action('VaultController@logout');
        }
	}

	public function enable_or_disable(){
		if (Auth::check ()) {
                
            $currentPage = "EnableDiscounts_LI";
            $mainMenu = "DISCOUNTS_MENU_MAIN";
            $discount_data=  Discounts::where('franchisee_id','=',Session::get('franchiseId'))
                           ->orderBy('number_of_classes','=','DESC') 
                           ->get();
            $viewdata=array('currentPage','mainMenu', 'discount_data');
            //$viewdata=array('currentPage','mainMenu');
            return View::make('pages.Discounts.enable_or_disable',  compact($viewdata));
        }else{
            return Redirect::action('VaultController@logout');
        }
	}


	public function addMultipleDiscounts(){
		if (Auth::check ()) {    
            $inputs = Input::all();
            for($i=0;$i<count($inputs['discount_prcnt']);$i++){
            	$inputDiscount['discount_prcnt']=$inputs['discount_prcnt'][$i];
            	$inputDiscount['no_of_class']=$inputs['no_of_class'][$i];
            	$sendDetails = Discounts::InsertMultipleDiscounts($inputDiscount);
            }
            if($sendDetails){
                return Response::json(array("status"=>'success'));     
            }else{
            	return Response::json(array("status"=>'failure'));
            }
        }
	}


	public function approvingDiscounts(){
		if(Auth::check()){
			$inputs = Input::all();
			$send_details = Discounts::approveDiscounts($inputs);

			if($send_details){
                return Response::json(array("status"=>'success', $send_details));     
            }else{
            	return Response::json(array("status"=>'failure', $send_details));
            }
		}
	}


	public function updateDiscounts(){
		if(Auth::check()){
			$inputs = Input::all();
			$send_details = Discounts::updateDiscounts($inputs);

			if($send_details){
                return Response::json(array("status"=>'success', $send_details));     
            }else{
            	return Response::json(array("status"=>'failure', $send_details));
            }
		}
	}


	public function updateSecondChild_ClassDisc(){
		if(Auth::check()){
			$inputs = Input::all();
			$send_details = Discounts::updateSecondChild_ClassDisc($inputs);

			if($send_details){
                return Response::json(array("status"=>'success', $send_details));     
            }else{
            	return Response::json(array("status"=>'failure', $send_details));
            }
		}
	}


	public function insertSecondChild_ClassDisc(){
		if(Auth::check()){
			$inputs = Input::all();
			$send_details = Discounts::insertSecondChild_ClassDisc($inputs);

			if($send_details){
                return Response::json(array("status"=>'success', $send_details));     
            }else{
            	return Response::json(array("status"=>'failure', $send_details));
            }
		}
	}




	public function deleteDiscounts(){
		if(Auth::check()){
			$inputs = Input::all();
			$send_details = Discounts::deleteDiscounts($inputs);

			if($send_details){
                return Response::json(array("status"=>'success', $send_details));     
            }else{
            	return Response::json(array("status"=>'failure', $send_details));
            }
		}
	}











	/**
	 * Display a listing of the resource.
	 * GET /discounts
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /discounts/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /discounts
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /discounts/{id}
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
	 * GET /discounts/{id}/edit
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
	 * PUT /discounts/{id}
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
	 * DELETE /discounts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}