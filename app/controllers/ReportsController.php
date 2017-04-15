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
                    return Response::json(array(PaymentDues::getWeeklyEnrollmentReport($inputs),'Weekly'));
                }else if($inputs['reportType']=='BySchool'){
                    return Response::json(array(PaymentDues::getBySchoolEnrollmentReport($inputs),'BySchool'));
                }else if($inputs['reportType']=='ByLocality'){
                    return Response::json(array(PaymentDues::getByLocalityEnrollmentReport($inputs),'ByLocality'));
                }else if($inputs['reportType']=='ByApartment'){
                    return Response::json(array(PaymentDues::getByApartmentEnrollmentReport($inputs),'ByApartment'));
                }
                
                return Response::json(array($inputs));
            }
        }

        public static function salesAllocreport(){

        	if(Auth::check()){
        		$inputs=  Input::all();
        		$salesFile = PaymentDues::getSalesAllocReport($inputs);

        		$sheetheaders = ['ROLL NUMBER', 'INVOICE NUMBER', "Date of Billing\nMM/DD/YYYY", "Date of Birth\nMM/DD//YYYY", 'Child Name', 'Parent Name', 'Class', 'No. Of Weeks', '2nd Class', "Start Date\nMM/DD/YYYY", 'End Date', 'Membership', 'Classes', 'Discount', 'Tax', 'Total', "Mode Of\nPayment"];

        		//Concatinating shet headers and body
				$sheetData[0] = $sheetheaders;
				$sheetData = $sheetData + $salesFile;

        		Excel::create('Sales Allocation Report', function($excel) use($sheetData) {
		              $excel->sheet('Sheet 1', function($sheet) use($sheetData){
		                  
		                  //Styles in Row wise
		                  $sheet->mergeCells('A1:P1');
		                  $sheet->setAllBorders('thin');
		                  $heightArray = array(
		                      1     =>  50,
		                      2     =>  50,
		                  );
		                  for ($i=3; $i < count($sheetData); $i++) { 
		                  	$heightArray[$i] = 22; 
		                  }

		                  $sheet->setHeight($heightArray);
		                  $sheet->row(1, function ($row) {
		                      $row->setFontFamily('Calibri');
		                      $row->setFontSize(11);
		                      $row->setFontColor('#ffffff');
		                      $row->setAlignment('center');
		                      $row->setFontWeight('normal');
		                      $row->setValignment('center');
		                      $row->setBackground('#205867');
		                  });
		                  $sheet->row(2, function ($row) {
		                      $row->setFontFamily('Calibri');
		                      $row->setFontSize(9);
		                      $row->setFontColor('#ffffff');
		                      $row->setAlignment('center');
		                      $row->setFontWeight('normal');
		                      $row->setValignment('center');
		                      $row->setBackground('#205867');
		                  });

		                  //Set Headers in row wise
		                  $sheet->row(1, array('MASTER SALES ALLOCATION FOR THE MONTH OF '. date("F Y")));

		                  //Writing into file 
		                  $sheet->fromArray($sheetData);
		              });
		          })->export('xls');


        	}else{
        		return Redirect::action('VaultController@logout');
        	}
        }
        
        public static function deleted_customers(){
            if((Auth::check()) && (Session::get('userType'))=='ADMIN'){
                $currentPage  =  "ViewDeletedCustomer_LI";
                $mainMenu     =  "REPORTS_MENU_MAIN";
                $deletedCustomer_data=DeletedCustomers::where('franchisee_id','=',Session::get('franchiseId'))
                                                        ->orderBy('id','desc')
                                                        ->get();
                $viewData= compact('currentPage','mainMenu','deletedCustomer_data');
                return View::make('pages.reports.deletedcustomer_view',$viewData);
            }else{
                return Redirect::action('VaultController@logout');
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