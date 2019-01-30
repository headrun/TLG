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
			
			$franchiseelist = Franchisee::getFList();
			$courseList = CoursesMaster::getAllCourses();
			
      		$viewData = array('currentPage','mainMenu','franchiseelist','courseList');
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

		public static function terms_conditions() {
			if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){

				
				$mainMenu     =  "FRANCHISEE_MAIN";

				$currentPage  =  "TERMSANDCONDTIONS";

				$franchiseeList = Franchisee::getFranchiseeList();
	            

	      		$viewData = array('currentPage','mainMenu','franchiseeList');
	      		return View::make('pages.franchisee.termsAndConditions',compact($viewData)); 
	      
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

			return Response::json(array('status'=>'failure'));
		}
	}

	public static function addFranchisee(){
		if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){
			
			$inputs=Input::all();
			$newFranchisee=Franchisee::addNewFranchisee($inputs);
			
			if($newFranchisee){
				return Response::json(array('status'=>'success'));
			}

			return Response::json(array('status'=>'failure'));
		}

		return Response::json(array('status'=>'failure'));
	}

    public static function createdNewFranchisee() {
    	if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){
          $inputs=Input::all();
          $newFranchisee=Franchisee::createdNewFranchisee($inputs);
          $newFranchiseeBdayPrice = BirthdayBasePrice::createBdayPriceForNew($inputs, $newFranchisee['id']);
          $newFranchiseeClassBasePrice = ClassBasePrice::insertNewBasePrice($inputs, $newFranchisee['id']);
          $newFranchiseeInvoiceData = InvoiceData::insertNewInvoiceData($inputs, $newFranchisee['id']);
          $newFranchiseeAnnaulMembership = MembershipTypes::insertNewAnnaulMembershipFranchisee($inputs, $newFranchisee['id']);
          $newFranchiseeLifetimeMembership = MembershipTypes::insertNewLifeTimeMembershipFranchisee($inputs, $newFranchisee['id']);
          $newFranchiseePaymentTax = PaymentTax::insertPaymentTaxForNewFranchisee($inputs, $newFranchisee['id']);
          $newFranchiseeCgstTaxParticular = TaxParticulars::insertCgstTaxParicularNewFranchisee($inputs, $newFranchisee['id']);
          $newFranchiseeSgstTaxParticular = TaxParticulars::insertSgstTaxParicularNewFranchisee($inputs, $newFranchisee['id']);
          $newFranchiseeTermsAndCond = TermsAndConditions::newFranchiseeTermsAndCon($inputs, $newFranchisee['id']);
          $newFranchiseeAdminUser = User::insertNewAdminUser($inputs, $newFranchisee['id']);
          if($newFranchiseeAdminUser){			
          	return Response::json(array('status'=>'success'));
          }else{
          	return Response::json(array('status'=>'failure'));
          }
    	}
    }

    public static function updateFranchiseeDetails() {
		if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){
	      $inputs=Input::all();
          $updateFranchisee = Franchisee::updateExistingFranchisee($inputs);
          $updateBdayPrice = BirthdayBasePrice::updateBdayPricing($inputs);
          $updateClassPrice = ClassBasePrice::updateClassBasePrice($inputs);
          $updateInvoiceData = InvoiceData::updateInvoiceDetails($inputs);
          $updateFranchiseeAnnaulMembership = MembershipTypes::updateAnnaulMembershipFranchisee($inputs);
          $updateFranchiseeLifetimeMembership = MembershipTypes::updateLifeTimeMembershipFranchisee($inputs);
          $updateFranchiseePaymentTax = PaymentTax::updatePaymentTaxForNewFranchisee($inputs);
          if($inputs['franchisee_id'] != 11){
	          $updateFranchiseeCgstTaxParticular = TaxParticulars::updateCgstTaxParicularNewFranchisee($inputs);
	          $updateFranchiseeSgstTaxParticular = TaxParticulars::updateSgstTaxParicularNewFranchisee($inputs);
          }else{									
          	$updateFranchiseeVatTaxParticular = TaxParticulars::updateVatTaxParicularNewFranchisee($inputs);
          }
          if($updateFranchisee){			
          	return Response::json(array('status'=>'success'));
          }else{
          	return Response::json(array('status'=>'failure'));
          }
	    }
    }
	
	public static function getDataForFranchisee () {
	  	if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){
	        $inputs=Input::all();
	        $franchiseDetails = Franchisee::where('id', '=', $inputs['franchisee_id'])->get();
	        $bdayDetails = BirthdayBasePrice::where('franchisee_id','=',$inputs['franchisee_id'])->get();
	        $classBasePrice = ClassBasePrice::where('franchise_id','=',$inputs['franchisee_id'])->get();
            $invoice_data = InvoiceData::where('franchise_id','=',$inputs['franchisee_id'])->get();
            $annual = MembershipTypes::where('franchisee_id','=',$inputs['franchisee_id'])
            								   ->where('name','=','Annual')
                                               ->get();
            $lifetime = MembershipTypes::where('franchisee_id','=',$inputs['franchisee_id'])
            								   ->where('name','=','Lifetime')
                                               ->get();

            $cgst = TaxParticulars::where('franchisee_id','=',$inputs['franchisee_id'])
            					               ->where('tax_particular', '=', 'CGST')
            					               ->get();
            $sgst = TaxParticulars::where('franchisee_id','=',$inputs['franchisee_id'])
            					               ->where('tax_particular', '=', 'SGST')
            					               ->get();
            $vat = TaxParticulars::where('franchisee_id','=',$inputs['franchisee_id'])
            					               ->where('tax_particular', '=', 'VAT')
            					               ->get(); 
            $tax_config = TaxParticulars::where('franchisee_id','=',$inputs['franchisee_id'])
            					               ->get();
                if(count($tax_config) > 1){
                	for ($i=0; $i < count($tax_config); $i++) { 
                		$tax_config_field[$i] = $tax_config[$i]['tax_particular'];
                		$tax_config_fields = $tax_config_field[$i];
                	}
                } else{
                	$tax_config_field =  $tax_config[0]['tax_particular'];
                }

	        if($franchiseDetails){
	          return Response::json(array('status'=> "success", 'franchisee_data' => $franchiseDetails,
	          	                          'bday_data'=>$bdayDetails,
	          							  'class_base_price' => $classBasePrice,
	          							  'invoice_data' => $invoice_data,
	          							  'annual' => $annual,
	          							  'lifetime' => $lifetime,
	          							  'cgst' => $cgst,
	          							  'sgst' => $sgst,
	          							  'vat' => $vat,
	          							  'tax_config_field' => $tax_config_field
	          							  )
	                                );
	        }else{
	          return Response::json(array('status'=> "failure",));
	        }
	    }
	}

	public static function getTermsAndCondForFranchisee () {
	  	if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){
	        $inputs=Input::all();          					
	        $franchiseDetails = TermsAndConditions::where('franchisee_id', '=', $inputs['franchisee_id'])->get();
	        if($franchiseDetails){
	          return Response::json(array('status'=> "success", 'franchisee_data' => $franchiseDetails));
	        }else{
	          return Response::json(array('status'=> "failure"));
	        }
	    }
	}

	public static function updateTermsAndCondtions () {
		if(Auth::check() && Session::get('userType')==='SUPER_ADMIN'){
			$inputs = Input::all();          					
			$updateConditions = TermsAndConditions::updateTermsAndCondtions($inputs);
			if ($updateConditions) {
				return Response::json(array('status' => "success"));
			} else {
				return Response::json(array('status' => "failure"));
			}
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