<?php

class ReportsController extends \BaseController {
        
   
        public static function view_reports(){
            if(Auth::check()){
                if(Session::get('userType') == 'ADMIN'){
                    $currentPage  =  "ViewReoprt_LI";
                    $mainMenu     =  "REPORTS_MENU_MAIN";
                    $presentdate  =  date("Y-m-d");
                    $viewData= compact('currentPage','mainMenu','presentdate');
                    return View::make('pages.reports.report_view',$viewData);
                }else{
                    return Redirect::action('DashboardController@index');
                }    
            }else{
                return Redirect::action('VaultController@logout');
            }
        }
    
        public static function generatereport(){
            if(Auth::check()){
                $inputs=  Input::all();
                if($inputs['reportType']=='Birthday'){
                    return Response::json(array(PaymentDues::getAllBirthdayPaymentsforReport($inputs),'Birthday'));
                }else if($inputs['reportType']=='Enrollment'){
                    return Response::json(array(PaymentDues::getAllEnrollmentPaymentsforReport($inputs),'Enrollment'));
                }else if($inputs['reportType']=='both'){
                    return Response::json(array(PaymentDues::getAllEnrollmentBirthdayPaymentsforReport($inputs),'both'));
                }else if($inputs['reportType']=='Membership'){
                    return Response::json(array(PaymentDues::getAllMembershipPaymentsforReport($inputs),'Membership'));
                }else if($inputs['reportType']=='Introvisit'){
                    return Response::json(array(IntroVisit::getAllIntrovisitforReport($inputs),'Introvisit'));
                }else if($inputs['reportType']=='Inquiry'){
                    return Response::json(array(Inquiry::getAllInquiryforReport($inputs),'Inquiry'));
                }else if($inputs['reportType']=='Weekly'){
                    return Response::json(array(PaymentDues::getWeeklyEnrollmentReport(),'Weekly'));
                }
                
                return Response::json(array($inputs));
            }
        }
    
	/**
	 * Display a listing of the resource.
	 * GET /reports
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /reports/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /reports
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /reports/{id}
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
	 * GET /reports/{id}/edit
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
	 * PUT /reports/{id}
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
	 * DELETE /reports/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}