@extends('layout.master')

@section('libraryCSS')
	<link href='{{url()}}/assets/css/bootstrap.min.css' rel='stylesheet' />
	<style>
		.smallText td a, .smallText td {
			font-size:12px !important;
		
		}
		
		.smallText td a{
			text-decoration:none !important;
		}

        #Titles{
            font-weight: bold !important;
            font-size: 17px !important;
        }
        table tbody thead {
            cursor: default !important;
        }
	
	</style>
@stop

@section('libraryJS')

    
	
 <!-- datatables -->
    <script src="{{url()}}/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <!-- datatables colVis-->
    <script src="{{url()}}/bower_components/datatables-colvis/js/dataTables.colVis.js"></script>
    <!-- datatables tableTools-->
    <script src="{{url()}}/bower_components/datatables-tabletools/js/dataTables.tableTools.js"></script>
    <!-- datatables custom integration -->
    <script src="{{url()}}/assets/js/custom/datatables_uikit.min.js"></script>

    <!--  datatables functions -->
    <script src="{{url()}}/assets/js/pages/plugins_datatables.min.js"></script>
    <script>
        
    $("#futurefollowupTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
				window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 10,
        "lengthMenu": [ 10, 50, 100, 150, 200 ],
        "order": [[ 3, "asc" ]],
    });    
    $("#followupTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
				window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 10,
        "lengthMenu": [ 10, 50, 100, 150, 200 ]
    });
    $("#introvisitTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
				window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 10,
        "lengthMenu": [ 10, 50, 100, 150, 200 ],
        //"order": [[ 3, "desc" ]]
    });
    $("#followupPending").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
				window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 10,
        "lengthMenu": [ 10, 50, 100, 150, 200 ],
        "order": [[ 3, "desc" ]]
    });
        $("#birthdayLog").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
				window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 10,
        "lengthMenu": [ 10, 50, 100, 150, 200 ],
    });
    
     $("#birthdayDataTable").DataTable({
        "ordering": false,
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
				window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 10,
        "lengthMenu": [ 10, 50, 100, 150, 200 ],
        "order": [[ 3, "desc" ]]
    });

     /*  $("#birthdayDataTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
                window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 10,
        "lengthMenu": [ 10, 50, 100, 150, 200 ],
        "order": [[ 3, "desc" ]]
    });  */
    
    $("#classesExpiringTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
				//window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 10,
        "lengthMenu": [ 10, 50, 100, 150, 200 ],
        "order": [[ 1, "desc" ]],
    });

    
    /* $("#followupTable tr").click(function (){

		window.location = $(this).find('a').attr('href');
	})
	
	$("#introvisitTable tbody tr").click(function (){

		window.location = $(this).find('a').attr('href');
	})
	
	$("#followupPending tr").click(function (){

		window.location = $(this).find('a').attr('href');
	}) */
    
    
    $('#BdayPatiesFilterByDate').change(function(){
        //alert($(this).val());
        
            $.ajax({
                type: "POST",
                url: "{{URL::to('/quick/BdayPartiesFiltering')}}",
                dataType: 'json',
                data:{"value": $('#BdayPatiesFilterByDate').val()},
                success: function(response)
                {
                   //var BdayRows = '';
                   
                   //console.log(response.data[0]);
                   var tableHeader = '';
                   tableHeader = '<table class="uk-table" id="birthdayCelebrationTable1">'+
                                    '<thead>'+
                                        '<tr>'+
                                            '<th class="uk-text-nowrap">Customer</th>'+
                                            '<th class="uk-text-nowrap">Kid</th>'+
                                            '<th class="uk-text-nowrap">Mobile No</th>'+                                            
                                            '<th class="uk-text-nowrap">DOB</th>'+
                                            '<th class="uk-text-nowrap">Time</th>'+                              
                                        '</tr>'+
                                    '</thead>'+
                                    '<tbody>';
                   for(i = 0; i < response.data.length; i++){
                        tableHeader  +=  '<tr onclick = navigateToCustomer("{{url()}}/customers/view/'+response.data[i]['customer_id']+'?tab=birthdayparty")>'+
                                        '<td>'+response.data[i]['customer_name']+'</td>'+
                                        '<td>'+response.data[i]['student_name']+'</td>'+
                                        '<td>'+response.data[i]['mobile_no']+'</td>'+
                                        '<td>'+response.data[i]['birthday_party_date']+'</td>'+
                                        '<td>'+response.data[i]['birthday_party_time']+'</td>'+
                                    '</tr>';
                   } 

                   tableHeader += '</tbody></table>';

                   //console.log(tableHeader);
                   $('#allBdayData').html(tableHeader);

                   $("#birthdayCelebrationTable").DataTable({
                        "destroy" : true,
                        "paging":   false,
                        "ordering": false,
                        "info":     false,
                        "searching" : false
                   });

                   $("#birthdayCelebrationTable").html('');

                   $("#birthdayCelebrationTable1").DataTable({
                            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                                return nRow;
                            },
                            "iDisplayLength": 10,
                            "lengthMenu": [ 10, 50, 100, 150, 200 ]
                           // "order": [[ 3, "asc" ]],
                    });
                }
            });
    });

    /*$('#BdayDataFilterByDate').change(function(){
            $.ajax({
                type: "POST",
                url: "{{URL::to('/quick/BdayDataFiltering')}}",
                dataType: 'json',
                data:{"value": $('#BdayDataFilterByDate').val()},
                success: function(response)
                {
                   var tableHeader = '';
                   tableHeader = '<table class="uk-table" id="birthdayCelebrationTable1">'+
                                    '<thead>'+
                                        '<tr>'+
                                            '<th class="uk-text-nowrap">Customer</th>'+
                                            '<th class="uk-text-nowrap">Kid</th>'+
                                            '<th class="uk-text-nowrap">Mobile No</th>'+                                            
                                            '<th class="uk-text-nowrap">DOB</th>'+                             
                                        '</tr>'+
                                    '</thead>'+
                                    '<tbody>';
                   for(i = 0; i < response.data.length; i++){
                        tableHeader  +=  '<tr onclick = navigateToCustomer("{{url()}}/customers/view/'+response.data[i]['customer_id']+'?tab=birthdayparty")>'+
                                        '<td>'+response.data[i]['customer_name']+'</td>'+
                                        '<td>'+response.data[i]['student_name']+'</td>'+
                                        '<td>'+response.data[i]['mobile_no']+'</td>'+
                                        '<td>'+response.data[i]['student_date_of_birth']+'</td>'+
                                    '</tr>';
                   } 

                   tableHeader += '</tbody></table>';

                   //console.log(tableHeader);
                   $('#allBdayData').html(tableHeader);

                   $("#birthdayDataTable").DataTable({
                        "destroy" : true,
                        "paging":   false,
                        "ordering": false,
                        "info":     false,
                        "searching" : false
                   });

                   $("#birthdayDataTable").html('');

                   $("#birthdayCelebrationTable1").DataTable({
                            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                                return nRow;
                            },
                            "iDisplayLength": 10,
                            "lengthMenu": [ 10, 50, 100, 150, 200 ]
                           // "order": [[ 3, "asc" ]],
                    });
                }
            });
    });*/

    function navigateToCustomer(url){
        window.location = url;
    }
   

    </script>
@stop
@section('content')
             

     <!--       <a href="{{url()}}/customers/add" class="md-fab md-fab-accent" id="addEnrollment" title="Add customers" data-uk-tooltip="{pos:'left'}" style="float:right; margin-top: 11em;">
				<i class="material-icons">&#xE03B;</i>
			</a> -->
            <!-- statistics (small charts) -->
            <div class="uk-grid uk-grid-width-large-1-4 uk-grid-width-medium-1-2 uk-grid-medium uk-sortable sortable-handler hierarchical_show" data-uk-sortable data-uk-grid-margin>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Enrolled Information</span></center>
                            <div class = "row" style = "">
                                <div class = "col-md-4" align="center">
                                    <span class="uk-text-muted uk-text-small">Today</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{$todayEnrolledList}}<noscript>12456</noscript></span></h2>
                                </div>
                                <div class = "col-md-4" align= "center">
                                    <span class="uk-text-muted uk-text-small">Week</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{$thisWeekEnrollment}}<noscript>12456</noscript></span></h2>
                                </div>
                                <div class = "col-md-4" align= "center">
                                    <span class="uk-text-muted uk-text-small">Month</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{$thisMonthEnrollment}}<noscript>12456</noscript></span></h2>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                           <center><span class="uk-text-muted uk-text-small" id = "Titles">Revenue Details</span></center>
                             
                           <table style="width:100%;text-align: center;">
                              <tr>
                                <td>Today</td>
                                <td>Rs. {{$todayRevenueDetails}}/-</td>
                              </tr>
                              <tr>
                                <td>Week</td>
                                <td>Rs. {{$thisWeekRevenueDetails}}/-</td>
                              </tr>
                              <tr>
                                <td>MTD</td>
                                <td>Rs. {{$thisMonthRevenueDetails}}/-</td>
                              </tr>
                            </table>
                         </div>
			</div>
		      </div>
		    
                           <!--  <div class = "row" style = "">
                                <div class = "col-md-4">
                                    <span class="uk-text-muted uk-text-small">Today</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{ 5555555}}<noscript>12456</noscript></span></h2>
                                </div>
                                <div class = "col-md-4" align= "center">
                                    <span class="uk-text-muted uk-text-small">Week</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{$membersCount}}<noscript>12456</noscript></span></h2>
                                </div>
                                <div class = "col-md-4" align= "center">
                                    <span class="uk-text-muted uk-text-small">MTD</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{$membersCount}}<noscript>12456</noscript></span></h2>
                                </div>
                            </div> 
                            
                        </div>
                    </div>
                </div> -->
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Enrolled Kids</span></center>
                            <div class = "row" style = "">
                                <div class = "col-md-3">
                                    <center><span class="uk-text-muted uk-text-small">Total</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">    
                                    {{ $singleEnrollments + (($multipleEnrollments) * 2) + (($threeEnrollemnts) * 3) + (($fourEnrollemnts) * 4)}}
				    <noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-2">
                                    <span class="uk-text-muted uk-text-small">Single</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">@if($singleEnrollments)
                                        {{$singleEnrollments}}
                                    @else 
                                        0
                                    @endif
                                    <noscript>12456</noscript></span></h2>
                                </div>

                                <div class = "col-md-2" align= "center">
                                    <span class="uk-text-muted uk-text-small">Two</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">
                                     @if($multipleEnrollments)
                                        {{$multipleEnrollments}}
                                     @else 
                                        0 
                                     @endif
                                    <noscript>12456</noscript></span></h2>
                                </div>

                                <div class = "col-md-2" align= "center">
                                    <span class="uk-text-muted uk-text-small">Three</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">
                                     @if($threeEnrollemnts)
                                        {{$threeEnrollemnts}}
                                     @else 
                                        0 
                                     @endif
                                    <noscript>12456</noscript></span></h2>
                                </div>
                                <div class = "col-md-2" align= "center">
                                    <span class="uk-text-muted uk-text-small">Four</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">
                                     @if($fourEnrollemnts)
                                        {{$fourEnrollemnts}}
                                     @else 
                                        0 
                                     @endif
                                    <noscript>12456</noscript></span></h2>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Total Enrollments</span></center>
                            <div class = "row" style = "padding-top:18px;">
                                <center><h2 class="uk-margin-remove"><span class="countUpMe">    
                                {{ $totalEnrollments}}
                                <noscript>12456</noscript></span></h2></center>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Leads Information</span></center>
                            <div class = "row" style="text-align: center;">
                                <div class = "col-md-6" >
                                    <span class="uk-text-muted uk-text-small">Open</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{ $openLeads }}<noscript>12456</noscript></span></h2>
                                </div>
                                <div class = "col-md-6">
                                    <span class="uk-text-muted uk-text-small">Hot</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{ $hotLeads }}<noscript>12456</noscript></span></h2>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_orders peity_data">64/100</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Follow-up Customers</span></center>
                            <!--<center><h2 class="uk-margin-remove"><span class="countUpMe">{{$reminderCount}}<noscript>64</noscript></span></h2></center>-->
                            <div class = "row" style = "">
                                <div class = "col-md-4">  
                                    <span class="uk-text-muted uk-text-small">Today</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{count($todaysFollowup)}}<noscript>64</noscript></span></h2>
                                </div>
                                <div class = "col-md-4">  
                                    <span class="uk-text-muted uk-text-small">Pending</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{count($activeRemindersCount)}}<noscript>64</noscript></span></h2>
                                </div>
                                <div class = "col-md-4">  
                                    <span class="uk-text-muted uk-text-small">Future</span>
                                    <h2 class="uk-margin-remove"><span class="countUpMe">{{count($futurefollowups)}}<noscript>64</noscript></span></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               <!--  <div>
                    <div class="md-card-4">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Introductory Visit</span></center>
                            <table style="width:100%; text-align: center;" >
                              <tr class="uk-text-muted uk-text-small">
                                <th></th>
                                <th>Scheduled</th> 
                                <th>Attended</th>
                              </tr>
                              <tr>
                                <td>Today</td>
                                <td>{{$todayScheduledIvs}}</td>
                                <td>{{$todayAttendedIvs}}</td>
                              </tr>
                              <tr>
                                <td>Week</td>
                                <td>{{$thisWeekScheduledIvs}}</td>
                                <td>{{$thisWeekAttendedIvs}}</td>
                              </tr>
                              <tr>
                                <td>Month</td>
                                <td>{{$thisMonthIvScheduled}}</td>
                                <td>{{$thisMonthAttendedIvs}}</td>
                              </tr>
                            </table>
                             <table>
                                <div class = "row" style = "">
                                    <div class = "col-md-4">
                                        <span class="uk-text-muted uk-text-small">Today</span>
                                        <span class="uk-text-muted uk-text-small">Today</span>
                                        <span class="uk-text-muted uk-text-small">Week</span>
                                        <span class="uk-text-muted uk-text-small">Month</span> -->
                                        <!-- <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$introVisitCount}}<noscript>12456</noscript></span></h2></center> -->
                                   <!--  </div>
                                    <div class = "col-md-4">
                                        <span class="uk-text-muted uk-text-small">Scheduled</span>
                                        <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$introVisitCount}}<noscript>12456</noscript></span></h2></center>
                                    </div>
                                    <div class = "col-md-4" align= "center">
                                        <span class="uk-text-muted uk-text-small">Attended</span>
                                        <h2 class="uk-margin-remove"><span class="countUpMe">{{$totalIntrovisitCount}}<noscript>12456</noscript></span></h2>
                                    </div>
                                </div>
                            </table> -->
                                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Birthday Party</span></center>
                            <div class = "row" style = "">
                                <div class = "col-md-4">
                                    <center><span class="uk-text-muted uk-text-small">Today</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$todaysbpartycount}}<noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-4">
                                    <center><span class="uk-text-muted uk-text-small">Week</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$bdayPartyInThisWeek}}<noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-4" align= "center">
                                    <center><span class="uk-text-muted uk-text-small">Month</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$bdayPartyInThisMonth}}<noscript>12456</noscript></span></h2></center>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                 <!--   <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <span class="uk-text-muted uk-text-small" id = "Titles">Current Enrollment</span>
                            
                            <div class = "row" style = "">
                                <div class = "col-md-12">  
                                        @foreach($courses as $course)
                                        <h5><span class="uk-text-muted uk-text-small_"><span>{{$course->course_name}} :</span><b>{{$course->totalno}} &nbsp;&nbsp;</b></span></h5>
                                        @endforeach
                                        <h5><span class="uk-text-muted uk-text-small_"><span>Total :</span><b>{{$totalclasses}} &nbsp;&nbsp;</b></span></h5>
                                </div>
                            </div>
       			  </div>                     
                        </div>
                    </div> -->
                </div> 
            </div>
             <div>
      	    
	    </div><br><br>
            <!-- large chart -->
            

            <!-- circular charts -->
            
            

            <!-- tasks -->
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                            	<h3>Follow Ups (Today)</h3>
                            	<?php if(isset($todaysFollowup)){?>
                                <table class="uk-table dashboardTable" id="followupTable" >
                                    <thead>
                                        <tr>
                                            <th class="uk-text-nowrap">Customer</th>
                                            <th class="uk-text-nowrap">Followup Type</th>
                                            <th class="uk-text-nowrap">Mobile No</th>
                                            <th class="uk-text-nowrap">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<?php foreach($todaysFollowup as $items){?>
                                        <tr class="uk-table-middle smallText">
                                            <td class="uk-width-3-10 uk-text-nowrap">{{$items->Customers->customer_name}} {{$items->Customers->customer_lastname}}<a href="{{url()}}/customers/view/{{$items->Customers->id}}?tab=ivfollowup"></a></td>
                                            <td class="uk-width-3-10 uk-text-nowrap">{{$items->followup_type}}</td>
                                            <td class="uk-width-3-10 uk-text-nowrap">{{$items->Customers->mobile_no}}</td>
                                            <td class="uk-width-3-10 uk-text-nowrap">{{date('d M Y', strtotime($items->reminder_date))}}</td>
                                        </tr>   
                                        <?php }?>                                     
                                    </tbody>
                                </table>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                            	<h3>Intro Visit</h3>
                            	<?php 
                            	/* echo '<pre>';
                            	print_r($todaysIntrovisit);
                            	echo '</pre>'; */
                            	?>
                            	<?php if(isset($allIntrovisits)){?>
                                <table class="uk-table" id="introvisitTable">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-nowrap">Customer</th>
                                           <!-- <th class="uk-text-nowrap">Email</th> -->
                                            <th class="uk-text-nowrap">Mobile No</th>
                                            <th class="uk-text-nowrap">Status</th>
                                            <th class="uk-text-nowrap">Visit Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<?php 
                                    	/* echo '<pre>';
                                    	print_r($todaysIntrovisit);
                                    	echo '</pre>'; */ 
                                    	foreach($allIntrovisits as $items){
                                                if($items->followup_status!='ENROLLED'){
                                                    if($items->followup_status!='NOT_INTERESTED'){
                                    		/* echo '<pre>';
                                    		print_r($items->Students);
                                    		echo '</pre>'; */
                                    	?>
                                        <tr class="uk-table-middle smallText">
                                           	<td class="uk-width-3-10 uk-text-nowrap">{{$items->Customers['customer_name']}}{{$items->Customers['customer_lastname']}} </td>
                                           	<!--<td class="uk-width-3-10 uk-text-nowrap">{{-$items->Customers['customer_email']}}</td>-->
                                           	<td class="uk-width-3-10 uk-text-nowrap">{{$items->Customers['mobile_no']}}</td>
                                           	<td class="uk-width-3-10 uk-text-nowrap">{{$items->followup_status}}</td>
                                           	
                                            <td class="uk-width-3-10 uk-text-nowrap"> {{date('M d Y',strtotime($items->iv_date))}}
                                            <a href="{{url()}}/customers/view/{{$items->Customers->id}}?tab=ivfollowup"></a>
                                            
                                            </td>
                                        </tr>   
                                                    <?php }}}?>                                     
                                    </tbody>
                                </table>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
            <br clear="all"/>
            
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
            <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                            	<h3>Follow Ups(Pending)</h3>
                            	<?php if(isset($activeRemindersCount)){?>
                                <table class="uk-table dashboardTable" id="followupPending" >
                                    <thead>
                                        <tr>
                                            <th class="uk-text-nowrap">Customer</th>
                                            <th class="uk-text-nowrap">Followup Type</th>
                                            <th class="uk-text-nowrap">Mobile No</th>
                                            <th class="uk-text-nowrap">Date</th>
                                        </tr>
                                    </thead>
				                <tbody>
                                        <?php foreach($activeRemindersCount as $items){?>
                                        <tr class="uk-table-middle smallText">
                                            <td class="uk-width-3-10 uk-text-nowrap">{{$items['customer_name']}}{{$items['customer_lastname']}}<a href="{{url()}}/customers/view/{{$items['id']}}?tab=ivfollowup"></a></td>
                                            <td class="uk-width-3-10 uk-text-nowrap">{{$items['followup_type']}}</td>
                                            <td class="uk-width-3-10 uk-text-nowrap">{{$items['mobile_no']}}</td>
                                            <td class="uk-width-3-10 uk-text-nowrap">{{date('d M Y', strtotime($items['reminder_date']))}}</td>
                                        </tr>   
                                        <?php }?>                                     
                                    </tbody> 
                                </table>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="uk-width-medium-1-2">
                     <div class="md-card">
                         <div class="md-card-content">
                             <div class="uk-overflow-container">
                             <h3>Follow Ups (Future)</h3>
                               <?php if(isset($futurefollowups)){?>
                                 <table class="uk-table dashboardTable" id="futurefollowupTable" >
                                     <thead>
                                         <tr>
                                             <th class="uk-text-nowrap">Customer</th>
                                             <th class="uk-text-nowrap">Followup Type</th>
                                             <th class="uk-text-nowrap">Mobile No</th>
                                             <th class="uk-text-nowrap">Date</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                       <?php foreach($futurefollowups as $items){?>
                                         <tr class="uk-table-middle smallText">
                                           <td class="uk-width-3-10 uk-text-nowrap">{{$items['customer_name']}}{{$items['customer_lastname']}}<a href="{{url()}}/customers/view/{{$items['id']}}?tab=ivfollowup"></a></td>
                                           <td class="uk-width-3-10 uk-text-nowrap">{{$items['followup_type']}}</td>
                                           <td class="uk-width-3-10 uk-text-nowrap">{{$items['mobile_no']}}</td>
                                           <td class="uk-width-3-10 uk-text-nowrap">{{date('d M Y', strtotime($items['reminder_date']))}}</td>
                                          </tr>   
                                         <?php }?>                                     
                                     </tbody>
                                 </table>
                                 <?php }?>
                             </div>
                         </div>
                     </div>
                </div>
               </div>  
                
			
            <!-- info cards -->
            
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
            
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <h3 style  ="font-size: 24px;">Upcoming Birthdays</h3>        
                                <br clear="all"/>
                            	<div id = "allBdayData"></div>
                                <table class="uk-table" id="birthdayDataTable">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-nowrap">Customer</th>
                                            <th class="uk-text-nowrap">Kid</th>
                                            <th class="uk-text-nowrap">Mobile No</th>                                            
                                            <th class="uk-text-nowrap">DOB</th>
                                            <th class="uk-text-nowrap">Status</th>
                              
                                        </tr>
                                    </thead>
                                    <tbody id = "BirthdayTableBody">
                                        @foreach($upcomingBdays as $value)
                                        <tr>
                                            <td>{{$value->customer_name}}
                                            <a href="{{url()}}/customers/view/{{$value->customer_id}}?tab=birthdayparty"></a>
                                            </td>
                                            <td>{{$value->student_name}}</td>
                                            <td>{{$value->mobile_no}}</td>
                                            <td>{{$value->student_date_of_birth}}</td>
                                            <td>{{$value->status}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                               
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            
            
            
              

@stop
