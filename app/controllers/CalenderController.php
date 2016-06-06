<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class CalenderController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //

        if (Auth::check()) {

            $currentPage = "CALENDER";
            $mainMenu = "CALENDER_MAIN";

            $batchSchedules1 = BatchSchedule::getAllBatches();
            $batchSchedules = json_encode($batchSchedules1['event']);

            $birthdaySchedules1 = BirthdayParties::getallBirthdayParties();
            $birthdaySchedules = json_encode($birthdaySchedules1['event']);
            $allEvents = 'null';
            if (isset($batchSchedules1) && isset($birthdaySchedules1)) {
                $allEvents = array_merge_recursive($batchSchedules1, $birthdaySchedules1);
                $allEvents = json_encode($allEvents['event']);
            }
            $dataToView = ['currentPage', 'mainMenu', 'batchSchedules', 'birthdaySchedules', 'allEvents'];
            return View::make('pages.calender.calenderindex', compact($dataToView));
        } else {
            return Redirect::to("/");
        }
    }

    public function view() {

        return "Calender View";

//		if(Auth::check()){
//			
//			$currentPage  =  "BATCHES";
//			$mainMenu     =  "COURSES_MAIN";
//			$batchSchedules = BatchSchedule::getBatcheSchedulesbyCourseandClassID($id);
//			$batchSchedules = json_encode($batchSchedules['event']);
//                        
//			$dataToView = array('currentPage', 'mainMenu', 'batchSchedules');
//			return View::make('pages.batches.batchview', compact($dataToView));
//		
//		}else{
//			return Redirect::to("/");
//		}		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

}
