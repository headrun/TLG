<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

Route::any('/', "VaultController@login");
Route::any('/try', "TryController@index");


Route::group(array('prefix' => 'vault'), function() {
	Route::any('login', "VaultController@login");
	Route::get('logout', "VaultController@logout");
	
});

    

Route::any('/courses', 'CoursesController@viewCourses');
//Route::any('/classes', 'ClassesController@index');


Route::any('/terms_conditions', 'DashboardController@terms_conditions');
Route::any('dashboard/toDeleteMultiple','DashboardController@toDeleteMultiple');
Route::any('/dashboard/UpdateBatchSchedule','DashboardController@UpdateBatchSchedule');
Route::any('/dashboard/toEditTheEnrollmentEndDates','DashboardController@toEditTheEnrollmentEndDates');

Route::any('/classes', 'ClassesController@add_new_classes');
Route::any('/add_new_class_franchise', 'ClassesController@add_new_class_franchise');
Route::any('/calendar', 'CalenderController@index');


Route::group(array('prefix' => 'courses'), function() {
	Route::any('/add', "CoursesController@addCourses");
    Route::any('/name_list', "CoursesController@courseNameList");
    Route::any('/addCourses', 'CoursesController@viewCoursesAdmin');
    Route::any('/addCoursesForFranchisee', 'CoursesController@addCoursesForFranchisee');
});


Route::group(array('prefix' => 'admin'), function() {
	Route::any('/users/add', "FranchiseeAdministration@adduser");
	Route::get('/users', "FranchiseeAdministration@users");
	Route::get('/users/updatebatches', "FranchiseeAdministration@updatebatches");
	Route::any('/users/view/{id}', ['uses' =>"FranchiseeAdministration@viewUser"]);
});

Route::group(array('prefix' => 'super_admin'), function() {

});

Route::group(array('prefix'=>'settings'), function(){
        Route::any('/changepassword','UsersController@changepassword');
});



Route::group(array('prefix' => 'dashboard'), function() {
	Route::any('/', "DashboardController@index");
	Route::get('logout', "VaultController@logout");

});



Route::group(array('prefix' => 'students'), function() {
	Route::any('/nonenrolled', "StudentsController@index");
    Route::any('/enrolled', "StudentsController@enrolledstudents");
	Route::any('/view/{id}', ['uses' =>"StudentsController@view"]);
	Route::any('/profile/picture', "StudentsController@uploadProfilePicture");
	Route::any('/discovery/picture', "StudentsController@uploadDiscoveryPicture");
	Route::any('/discovery/download', "StudentsController@downloadDiscoveryPicture");


});


Route::group(array('prefix' => 'customers'), function() {
	Route::any('/memberslist', "CustomersController@index");
	Route::any('/currentCustomerList', "CustomersController@currentCustomersList");
    Route::any('/prospectslist', "CustomersController@getNonMembersList");
	Route::any('/add', "CustomersController@add");
	Route::any('/view/{id}', "CustomersController@details");
	Route::any('/profile/picture', "CustomersController@uploadProfilePicture");

});


    
Route::group(array('prefix' => 'batches'), function() {
	Route::any('/', "BatchesController@index");
	Route::any('/view/{id}', "BatchesController@view");
	Route::any('/attendance/{id}', "BatchesController@attendance");
    Route::any('/batcheslimit',"BatchesController@batcheslimit");
    Route::any('/addbatchlimit',"BatchesController@addBatchLimit");
});



Route::group(array('prefix' => 'events'), function() {
	Route::any('/', "EventsController@index");
	Route::any('/types', "EventsController@eventTypes");
	

});


Route::group(array('prefix' => 'orders'), function() {
	Route::any('/print/{id}', "PaymentsController@printOrder");
    	Route::any('/Bprint/{id}',"PaymentsController@printBdayOrder");
    	Route::any('/Membershipprint/{id}',"PaymentsController@printMembershipOrder");
	Route::any('/types', "EventsController@eventTypes");
	Route::any('/printSummerOrder/{id}', "PaymentsController@printSummerOrder");


});


    
Route::group(array('prefix'=>'season'),function(){
        Route::any('/add','SeasonsController@add');
        Route::any('/viewseasons','SeasonsController@index');
        
});

Route::group(array('prefix'=>'prices'),function(){
      Route::any('/add_or_view_prices','PaymentsController@addorviewprices');
});

Route::group(array('prefix'=>'Discounts'),function(){
        Route::any('/add_or_view_discounts','DiscountsController@add_or_view_discounts');
        Route::any('/enable_or_disable','DiscountsController@enable_or_disable');
        
});

Route::group(array('prefix'=>'reports'),function(){
        Route::any('/view_reports','ReportsController@view_reports');
        Route::any('/mismatch_enrollments','ReportsController@mismatch_enrollments');
        Route::any('/kids_deleted_batch','ReportsController@kids_deleted_batch');
        Route::any('/kbi_reports','ReportsController@kbi_reports');
        Route::any('/deleted_customers','ReportsController@deleted_customers');
});


Route::group(array('prefix'=>'franchisee'),function(){

	Route::any('/addfranchisee','FranchiseeController@addNewFranchisee');
	Route::get('/franchiseelist','FranchiseeController@franchiseeList');
	Route::any('/addNewClass', "ClassesController@addNewClass");
	Route::any('/addNewClassFranchisee', "ClassesController@addNewClassFranchisee");
	// Route::any('/addCoursesForEachFranchise', 'CoursesController@addCoursesForEachFranchise');
});




if(Auth::check()){


/*****************************************************  AJAX ROUTES ********************************************************/

Route::group(array('prefix' => 'quick'), function() {
    
	/* Super Admin related Ajax calls*/

		Route::any('addFranchisee',"FranchiseeController@addFranchisee");
        Route::any('updateFranchisee','FranchiseeController@updateFranchisee');
        Route:: any('addAdminUser','UsersController@addAdminUser');
        Route::any('/salesAllocreport', "ReportsController@salesAllocreport");
        Route::any('UpdateDataBatch', "ReportsController@UpdateDataBatch");
        Route::any('createdNewFranchisee',"FranchiseeController@createdNewFranchisee");
        Route::any('getDataForFranchisee', 'FranchiseeController@getDataForFranchisee');
        Route::any('updateFranchiseeDetails', 'FranchiseeController@updateFranchiseeDetails');
        Route::any('getCoursesFranchiseeWise', 'CoursesController@getCoursesFranchiseeWise');
        Route::any('updateCoursesForFranchisee', 'CoursesController@updateCoursesForFranchisee');
        Route::any('getAllClassesForFranchiseeWise', 'CoursesController@getAllClassesForFranchiseeWise');
        Route::any('getBasePricesForFranchisee', 'CoursesController@getBasePricesForFranchisee');
        Route::any('updateClassesBasePriceForFranchisee', 'CoursesController@updateClassesBasePriceForFranchisee');


        /**
*  --------------------------------------------------------------------------------------------------------------------------------------
* Courses related Ajax calls
*  --------------------------------------------------------------------------------------------------------------------------------------
*/		
		Route::any('checkSecondSibling', "StudentsController@checkSecondSibling");
        Route::any('deleteCoursesMaster', "CoursesController@deleteCoursesMaster");
        Route::any('updateCoursesMaster', "CoursesController@updateCoursesMaster");	
        Route::any('InsertNewCoursesMaster', "CoursesController@InsertNewCoursesMaster"); 
        Route::any('updatepassword','UsersController@updatepassword');
    	Route::any('purchaseMembership','CustomersController@purchaseMembership');    
    	Route::any('getmembershiptypedetails','CustomersController@getmembershiptypedetails');


        Route::group(array('prefix'=>'baseprice'),function(){
            Route::any('/deletebaseprice','PaymentsController@deletebaseprice');
            Route::any('/updatebaseprice','PaymentsController@updatebaseprice');
        });
        
        
    	Route::any('/addMultipleDiscounts','DiscountsController@addMultipleDiscounts');
    	Route::any('/approvingDiscounts','DiscountsController@approvingDiscounts');
        
        
        Route::group(array('prefix'=>'season'),function(){
                     Route::any('/getWeekstartenddayseason','SeasonsController@getstartenddays');
                     Route::any('/addSeason','SeasonsController@addSeason');
                     Route::any('/getSeasonsForBatches','SeasonsController@getSeasonsForBatches');
                     Route::any('/getSeasonsForEnrollment','SeasonsController@getSeasonsForEnrollment');
                     Route::any('/getLocationBySeasonId','SeasonsController@getLocationBySeasonId');
                     Route::any('/getSeasonDataBySeasonId','SeasonsController@getSeasonDataBySeasonId');
        });
        
        Route::group(array('prefix'=>'discount'),function(){
        Route::any('/getdiscount','ClassesController@getDiscount');
        });
        Route::any('/getAllCoursesForFranchisee', 'CoursesController@getAllCoursesForFranchisee');
        Route::any('/getAllClassesForFranchisee', 'CoursesController@getAllClassesForFranchisee');
        Route::any('/getBasePriceForAllClasses', 'CoursesController@getBasePriceForAllClasses');
        Route::any('/addNewClassToFrnachisee', 'CoursesController@addNewClassToFrnachisee');

	/**
	 * --------------------------------------------------------------------------------------------------------------------------------------
	 * Classes related Ajax calls
	 * --------------------------------------------------------------------------------------------------------------------------------------
	 */
        Route::any('getClassesByCourseId', "ClassesController@getClassesByCourseId");
	Route::any('classesbymaster', "ClassesController@classesbymaster");	
	Route::any('classesbyCourse', "ClassesController@classesbyCourse");
        Route::any('eligibleClassessForIv', "ClassesController@eligibleClassessForIV");
	Route::any('eligibleClassess', "ClassesController@eligibleClassess");
        Route::any('eligibleClassessForOtherBatches', "ClassesController@eligibleClassessForOtherBatches");
        Route::any('batchesByClassSeasonId', "ClassesController@batchesByClassSeasonId");
	Route::any('getMembershipTypesDetails', "CustomersController@getMembershipTypesDetails");
	Route::any('getScheduledIntrovisitByCustomerId','CustomersController@getScheduledIntrovisitByCustomerId');
        Route::any('getIntrovisitByCustomerStatus','CustomersController@getIntrovisitByCustomerStatus');
        Route::any('changeIvStatustoAttendedByIVid','CustomersController@changeIvStatustoAttendedByIVid');
	Route::any('checkUserExistance', "FranchiseeAdministration@checkUser");
        Route::any('InsertNewClass', "ClassesController@InsertNewClass");
        Route::any('updateClassesMaster', "ClassesController@updateClassesMaster");
        Route::any('InsertNewClassFromFranchise', "ClassesController@InsertNewClassFromFranchise");
        Route::any('updateClassesBasePrice', "ClassesController@updateClassesBasePrice");
        
	/**
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 * Customer related Ajax calls
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 */
	Route::any('customerexistence', "CustomersController@checkCustomerExists");
	Route::any('editCustomer', "CustomersController@editCustomer");
        Route::any('getUniqueLocalityNames',"CustomersController@getUniqueLocality");
        Route::any('getUniqueApartmentNames',"CustomersController@getUniqueApartmentNames");
	Route::any('deleteCustomer',"CustomersController@deleteCustomer");
	Route::any('deleteMembership',"CustomersController@deleteMembership");
	Route::any('UpdateCustomerLogs', "CustomersController@UpdateCustomerLogs");
	/**
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 * Students related Ajax calls
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 */
	Route::any('addstudent', "StudentsController@addstudent");	
	Route::any('getStudentById', "StudentsController@getStudentById");
        Route::any('getStudentDetailsByIdForBatches','StudentsController@getStudentDetailsByIdForBatches');
	Route::any('saveKids', "StudentsController@saveKids");
	Route::any('enrollkid', "StudentsController@enrollKid2");
        Route::any('enrollYard', "StudentsController@enrollYard");
        Route::any('checkBiPayOrderDate',"StudentsController@checkBiPayOrderDate");
        Route::any('checkmultiPayOrderDate',"StudentsController@checkmultiPayOrderDate");
	Route::any('checkenrollmentExists', "StudentsController@checkenrollmentExists");
	Route::any('addbirthdayParty', "StudentsController@addbirthdayParty");
	Route::any('checkExistingBirthdayParty', "StudentsController@checkExistingBirthdayParty");
        Route::any('getBirthdayOrderPendingDetails',"StudentsController@getBirthdayOrderPendingDetails");
        Route::any('modifyBirthdayPendingOrder',"StudentsController@modifyBirthdayPendingOrder");
	Route::any('getStudentsByBatch', "StudentsController@getStudentsByBatch");
	Route::any('addStudentAttendance', "StudentsController@addStudentAttendance");
	Route::any('createorder',"StudentsController@createPendingorder");
	Route::any('creatependingorder',"StudentsController@createPendingOrderForEnrollment");
	Route::any('insertPastAttendance','StudentsController@insertPastAttendance');
	Route::any('getStudentsByCustomerid','StudentsController@getStudentsByCustomerid');
        Route::any('transferkid','StudentsController@transferkid');
        Route::any('getUniqueSchoolNames',"StudentsController@getUniqueSchoolNames");
        Route::any('deleteIVdata',"StudentsController@deleteIVdata");
        Route::any('deletebirthdaydata',"StudentsController@deletebirthdaydata");
        Route::any('deleteenrollmentdata',"StudentsController@deleteenrollmentdata");
        Route::any('deleteUserFromUsers',"FranchiseeAdministration@deleteUserFromUsers");
	/**
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 * Estimate related Ajax calls
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 */
        
        Route::any('deleteBatchInestimateTable','EstimateController@deleteBatchInestimateTable');
        Route::any('insertEstimateDetails','EstimateController@insertEstimateDetails');
        Route::any('insertEstimateMasterDetails','EstimateController@insertEstimateMasterDetails');
        Route::any('cancelBatchEstimate','EstimateController@cancelBatchEstimate');
        
	/**
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 * Batches related Ajax calls
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 */
	Route::any('checkslots', "BatchesController@checkslots");
	Route::any('getBatcheSchedules', "BatchesController@getBatchesSchedules");	
        Route::any('getBatchData','BatchesController@getBatchData');
        Route::any('getBatchRemainingClassesByBatchId','BatchesController@getBatchRemainingClassesByBatchId');
        Route::any('getBatchRemainingClassesCountByBatchId','BatchesController@getBatchRemainingClassesCountByBatchId');
        Route::any('/checkbatchesslot','BatchesController@checkBatchExistBySeasonIdLocationId');
        Route::any('getBatchDetailsById','BatchesController@getBatchDetailsById');
        Route::any('editbatchByBatchId','BatchesController@editbatchByBatchId');
	Route::any('deleteBatchById','BatchesController@deleteBatchById');
	Route::any('CheckNoofStudentsinBatch','BatchesController@CheckNoofStudentsinBatch');	
	Route::any('editBatchLimitByBatchId','BatchesController@editBatchLimitByBatchId');
        Route::any('deleteBatchLimitById','BatchesController@deleteBatchLimitById');
        Route::any('getBatchesForOldCustomer','BatchesController@getBatchesForOldCustomer');
        Route::any('getbatchesbybatchidanddate','BatchesController@getbatchesbybatchidanddate');
        Route::any('getBatchDatesByBatchId','BatchesController@getBatchDatesByBatchId');
        Route::any('getTotalBatchesForSelectedDate','BatchesController@getTotalBatchesForSelectedDate'); 
        /**
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 * Reports related Ajax calls
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 */
        Route::any('generatereport', "ReportsController@generatereport");
        Route::any('activityReport',"ReportsController@activityReport");
        Route::any('getMisMatchReports',"ReportsController@getMisMatchReports");
        Route::any('getDeletedBatchIdReports',"ReportsController@getDeletedBatchIdReports");
        Route::any('updateEnrollmentEndDate',"ReportsController@updateEnrollmentEndDate");
        Route::any('UpdateDataBatch',"ReportsController@UpdateDataBatch");
        Route::any('getAttendanceDetails',"StudentsController@getAttendanceDetails");
        
        
        
	/**
	 *  --------------------------------------------------------------------------------------------------------------------------------------
	 * Other Ajax Calls
	 *  -------------------------------------------------------------------
	 */
        
	Route::any('/addTermsAndConditions','DashboardController@addTermsAndConditionscont');
	Route::any('/updateSecondChild_ClassDisc','DiscountsController@updateSecondChild_ClassDisc');
        Route::any('/insertSecondChild_ClassDisc','DiscountsController@insertSecondChild_ClassDisc');
	Route::any('/deleteDiscounts','DiscountsController@deleteDiscounts');
	Route::any('/updateDiscounts','DiscountsController@updateDiscounts');
	Route::any('checkSlotAvailableForIntrovisit', "EventsController@checkSlotAvailableForIntrovisit");
	Route::any('addIntroVisit', "StudentsController@addIntroVisit");	
	Route::any('editIntrovisit', "StudentsController@editIntroVisit");
        Route::any('editEnrollment','StudentsController@editEnrollment');
        Route::any('getIntrovisitHistory',"StudentsController@getIntrovisitHistory");
        Route::any('getInquiryHistoryById','StudentsController@getInquiryHistoryById');
        Route::any('UpdateInquiryFollowup','StudentsController@UpdateInquiryFollowup');
        Route::any('getIvdataByCustomerId',"StudentsController@getIvdataByCustomerId"); 
        Route::any('getBirthdayHistory',"StudentsController@getBirthdayHistoryDataByBirthdayId"); 
        Route::any('editBirthdayCelebrationFollowup',"StudentsController@editBirthdayCelebrationFollowup");
        Route::any('createFollowup',"StudentsController@createFollowup");
        Route::any('getComplaintHistoryById',"StudentsController@getComplaintHistoryById");
        Route::any('getRetentionHistoryById',"StudentsController@getRetentionHistoryById");
        Route::any('getEnrollmetHistory','StudentsController@getEnrollmetHistory');
        Route::any('getFollowupByMembershipId','CustomersController@getMembershipHistory');
        Route::any('updateMembershipFollowup','CustomersController@updateMembershipFollowup');
        Route::any('UpdateRetentionFollowup','StudentsController@UpdateRetentionFollowup');
        Route::any('UpdateFollowup','StudentsController@UpdateFollowup');
	Route::any('getEvents', "EventsController@getEvents");		
	Route::any('getPaymentTypes', "PaymentsController@index");
	Route::any('navigateToProfile', "VaultController@navigateToProfile");	
		
	Route::any('eventTypeById', "EventsController@getEventTypeById");
	Route::any('saveEventType', "EventsController@saveEventType");
	
	Route::any('getEventById', "EventsController@getEventById");	
	Route::any('saveEvent', "EventsController@saveEvent");
	Route::any('/getAttendanceForStudent','StudentsController@getAttendanceForStudent');
        Route::any('/getBatchNameByYear','StudentsController@getBatchNameByYear');
        Route::any('/enrollOldCustomer','StudentsController@enrollOldCustomer');
        Route::any('/getExcusedabsentStudentsByBatchId','StudentsController@getExcusedabsentStudentsByBatchId');
        Route::any('createMakeupClass','ClassesController@createMakeupClass');
        Route::any('getMakeupdataByBatchId','ClassesController@getMakeupdatabyBatchId');
        Route::any('getTransferkiddata','ClassesController@getTransferkiddatabyBatchId');
        Route::any('BdayPartiesFiltering','DashboardController@BdayPartiesFiltering');
        Route::any('BdayDataFiltering','DashboardController@BdayDataFiltering');
        Route::any('UpdateEaDate','ClassesController@UpdateEaDate');
        Route::any('UpdateLeadStatus','ClassesController@UpdateLeadStatus');
        Route::any('UpdateBatchSchedule','DashboardController@UpdateBatchSchedule');
        Route::any('addMarketingBudget', 'ReportsController@addMarketingBudget');
        Route::any('checkMbExist', 'ReportsController@checkMbExist');
        
	Route::any('saveSchedule', function(){
	
		$input = Input::all();
		if($input['studentId'] != ""){
			$studentSchedule = StudentSchedule::addSchedule($input);
				
			if($studentSchedule){
				$status = array("status"=>"success");
			}
		}
		header('Access-Control-Allow-Origin: *');
		return Response::json($status);
	
	});
	
	
	
	
	
	Route::any('savecomment', function(){

		$inputs = Input::all();

		$comments = Comments::addComments($inputs);

		header('Access-Control-Allow-Origin: *');
		if($comments){
			return Response::json(array("status"=> "success"));
		}else{
			return Response::json(array("status"=> "failed"));
		}

	});
	
	Route::any('customerStudentSearch', function(){
	
		$inputs       = Input::all();
		$term         = $inputs['term'];
		$franchiseeId = Session::get('franchiseId');
		
		$customers = Customers::where('franchisee_id', '=', $franchiseeId)
		                    ->where('customer_name', 'LIKE', '%' . $term . '%')
		                    ->selectRaw('CONCAT(customer_name,customer_lastname, " (Parent)") as label, CONCAT(id, "####CST") as id')
		                    ->get()->toArray();
		$students = Students::where('franchisee_id', '=', $franchiseeId)
							->where('student_name', 'LIKE', '%' . $term . '%')
		                    ->selectRaw('CONCAT(student_name, " (Kid)") as label, CONCAT(id, "####STD") as id')
		                    ->get()->toArray();
			                   
		$result = array_merge($customers, $students);
			
		if(isset($result)){
			return Response::json($result);
		}
		return Response::json(array("status"=>"clear"));
	
	});
	
	
	
	Route::any('getWeekendsForBday', function(){
	
		$inputs = Input::all();
		$startDate = "01 ".$inputs['dateSelected'];
		$lastDateofMonth = date('t', strtotime($inputs['dateSelected']));
		$endDate   = $lastDateofMonth." ".$inputs['dateSelected'];		
		$saturdays = getWeekends($startDate, $endDate, "Sat");
		$sundays   = getWeekends($startDate, $endDate, "Sun");
		$result['saturdays'] = $saturdays;
		$result['sundays']   = $sundays;
	
		if(isset($result)){
			return Response::json($result);
		}
		return Response::json(array("status"=>"clear"));
	
	});
	
	Route::any('getCities', function(){
	
		$inputs = Input::all();
		$id     = $inputs['id'];
		$countryCode = $inputs['countryCode'];
		$cities = Cities::getCities($id,$countryCode);
	
		return Response::json($cities);
	
	});
	
	Route::get('logout', "VaultController@logout");
        
});
Route::any('/getfullfranchiseedata','FranchiseeAdministration@getFullFranchiseeData');
}



Route::get('/test', function(){
   // return View::make( 'pages.error404');
    //echo Hash::make('uday');
/*
$student_classes=StudentClasses::get();
// for franchisee_id
for($i=0;$i<count($student_classes);$i++){
	$customer=Students::find($student_classes[$i]['student_id']);
	$mem=StudentClasses::find($student_classes[$i]['id']);
	$mem->franchisee_id=$customer->franchisee_id;
	$mem->save();
} 
*/
echo "done";
/*
//for adding franchisee_id in customer_membership

$customer_mem=CustomerMembership::get();
for($i=0;$i<count($customer_mem);$i++){
	$customer=Customers::find($customer_mem[$i]['customer_id']);
	$mem=CustomerMembership::find($customer_mem[$i]['id']);
	$mem->franchisee_id=$customer->franchisee_id;
	$mem->save();
} 
echo "done";

*/

/*

// first step for adding franchisee_id in ordertable
    $orders= Orders::get();
    for($i=0;$i<count($orders);$i++){
    	$customer=Customers::find($orders[$i]['customer_id']);
    	$order=Orders::find($orders[$i]['id']);
    	$order->franchisee_id=$customer->franchisee_id;
    	$order->save();
    }
    echo "done";
 */

///second step for adding franchisee_wise invoice id
/* 
	$franchisees = Franchisee::get();

	for($i=0;$i<count($franchisees);$i++){
		$orders=Orders::where('franchisee_id','=',$franchisees[$i]['id'])->get();
		for($j=0;$j<count($orders);$j++){
			$order=Orders::find($orders[$j]['id']);
			$order->invoice_id=$j+1;
			$order->save();
		}
	}
	echo "done";
*/

//	return (Orders::where('franchisee_id','=',Session::get('franchiseId'))->max('invoice_id'))+1;

die();
    
 
});
