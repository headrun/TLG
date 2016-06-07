@extends('layout.master')

@section('libraryCSS')
	<link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' />
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
        "lengthMenu": [ 10, 50, 100, 150, 200 ]
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
        "lengthMenu": [ 10, 50, 100, 150, 200 ]
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
        "lengthMenu": [ 10, 50, 100, 150, 200 ]
    });
    
     $("#birthdayCelebrationTable").DataTable({
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
                    });
                }
            });
    });

    function navigateToCustomer(url){
        window.location = url;
    }
   

    </script>
@stop
@section('content')
             

            <a href="{{url()}}/customers/add" class="md-fab md-fab-accent" id="addEnrollment" title="Add customers" data-uk-tooltip="{pos:'left'}" style="float:right; margin-top: 11em;">
				<i class="material-icons">&#xE03B;</i>
			</a>
            <!-- statistics (small charts) -->
            <div class="uk-grid uk-grid-width-large-1-4 uk-grid-width-medium-1-2 uk-grid-medium uk-sortable sortable-handler hierarchical_show" data-uk-sortable data-uk-grid-margin>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Inquiries</span></center>
                            <div class = "row" style = "">
                                <div class = "col-md-6">
                                    <center><span class="uk-text-muted uk-text-small">Today</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$todaysCustomerReg}}<noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-6" align= "center">
                                    <center><span class="uk-text-muted uk-text-small">Till now</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$customerCount}}<noscript>12456</noscript></span></h2></center>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Family Memberships</span></center>
                             <!--<table style="width: 100% " >
                                <thead>
                                        <tr>
                                            <th align="left"><center><span class="uk-text-muted uk-text-small">Today</span></center> </th>
                                            <th align="right" valign="right"><center><span class="uk-text-muted uk-text-small">Till now</span></center> </th>
                                        </tr>
                                </thead>
                                <tbody align = "center">
                                <td><h2 class="uk-margin-remove"><span class="countUpMe">{{$todaysMemberReg}}<noscript>12456</noscript></span></h2></td>
                                <td valign="left"><h2 class="uk-margin-remove"><span class="countUpMe">{{$membersCount}}<noscript>12456</noscript></span></h2></td>
                                </tbody>
                            </table>-->

                            <div class = "row" style = "">
                                <div class = "col-md-6">
                                    <center><span class="uk-text-muted uk-text-small">Today</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$todaysMemberReg}}<noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-6" align= "center">
                                    <center><span class="uk-text-muted uk-text-small">Till now</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$membersCount}}<noscript>12456</noscript></span></h2></center>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles"> Prospects</span></center>
                             <!--<table style="width: 100% " >
                                <thead>
                                        <tr>
                                            <th align="left"><center><span class="uk-text-muted uk-text-small">Today</span></center></th>
                                            <th align="right" valign="right"><center><span class="uk-text-muted uk-text-small">Till now</span></center></th>
                                        </tr>
                                </thead>
                                <tbody align = "center">
                                <td><h2 class="uk-margin-remove"><span class="countUpMe">{{$todaysNonmemberReg}}<noscript>12456</noscript></span></h2></td>
                                <td valign="left"><h2 class="uk-margin-remove"><span class="countUpMe">{{$NonmembersCount}}<noscript>12456</noscript></span></h2></td>
                                </tbody>
                            </table>-->

                            <div class = "row" style = "">
                                <div class = "col-md-6">
                                    <center><span class="uk-text-muted uk-text-small">Today</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$todaysNonmemberReg}}<noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-6" align= "center">
                                    <center><span class="uk-text-muted uk-text-small">Till now</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$NonmembersCount}}<noscript>12456</noscript></span></h2></center>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Enrolled Kids</span></center>
                             <!--<table style="width: 100% " >
                                <thead>
                                        <tr>
                                            <th align="left"><center><span class="uk-text-muted uk-text-small">Today</span></center></th>
                                            <th align="right" valign="right"><center><span class="uk-text-muted uk-text-small">Till now</span></center></th>
                                        </tr>
                                </thead>
                                <tbody>
                                <td ><center><h2 class="uk-margin-remove"><span class="countUpMe">@if($todaysEnrolledCustomers){{$todaysEnrolledCustomers}}@else 0 @endif<noscript>12456</noscript></span></h2></center></td>
                                <td valign="left" ><center><h2 class="uk-margin-remove"><span class="countUpMe">
                                                 @if($enrolledCustomers)
                            
                                                 {{$enrolledCustomers}}
                                                  @else
							    0
						  @endif
                                            <noscript>12456</noscript></span></h2></center></td>
                                </tbody>
                            </table>-->

                            <div class = "row" style = "">
                                <div class = "col-md-6">
                                    <center><span class="uk-text-muted uk-text-small">Today</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">@if($todaysEnrolledCustomers){{$todaysEnrolledCustomers}}@else 0 @endif<noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-6" align= "center">
                                    <center><span class="uk-text-muted uk-text-small">Till now</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">
                                                 @if($enrolledCustomers)
                            
                                                 {{$enrolledCustomers}}
                                                  @else
                                0
                          @endif
                                            <noscript>12456</noscript></span></h2></center>
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
                                <div class = "col-md-12">  
                                    <center><span class="uk-text-muted uk-text-small">Till now</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$reminderCount}}<noscript>64</noscript></span></h2></center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Introductory Visit</span></center>
                             <!--<table style="width: 100% " >
                                <thead>
                                        <tr>
                                            <th align="left"><center><span class="uk-text-muted uk-text-small">Today</span></center></th>
                                            <th align="right" valign="right"><center><span class="uk-text-muted uk-text-small">Till now</span></center></th>
                                        </tr>
                                </thead>
                                <tbody align = "center">
                                <td><h2 class="uk-margin-remove"><span class="countUpMe">{{$introVisitCount}}<noscript>12456</noscript></span></h2></td>
                                <td valign="left"><h2 class="uk-margin-remove"><span class="countUpMe">{{$totalIntrovisitCount}}<noscript>12456</noscript></span></h2></td>
                                </tbody>
                            </table>-->

                            <div class = "row" style = "">
                                <div class = "col-md-6">
                                    <center><span class="uk-text-muted uk-text-small">Today</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$introVisitCount}}<noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-6" align= "center">
                                    <center><span class="uk-text-muted uk-text-small">Till now</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$totalIntrovisitCount}}<noscript>12456</noscript></span></h2></center>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <center><span class="uk-text-muted uk-text-small" id = "Titles">Birthday Party</span></center>
                             <!--<table style="width: 100% " >
                                <thead>
                                        <tr>
                                            <th align="left"><center><span class="uk-text-muted uk-text-small">Today</span></center></th>
                                            <th align="right" valign="right"><center><span class="uk-text-muted uk-text-small">Till now</span></center></th>
                                        </tr>
                                </thead>
                                <tbody align = "center"> 
                                <td><h2 class="uk-margin-remove"><span class="countUpMe">{{$todaysbpartycount}}<noscript>12456</noscript></span></h2></td>
                                <td valign="left"><h2 class="uk-margin-remove"><span class="countUpMe">{{$totalbpartyCount}}<noscript>12456</noscript></span></h2></td>
                                </tbody>
                            </table>-->
                            <div class = "row" style = "">
                                <div class = "col-md-6">
                                    <center><span class="uk-text-muted uk-text-small">Today</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$todaysbpartycount}}<noscript>12456</noscript></span></h2></center>
                                </div>
                                <div class = "col-md-6" align= "center">
                                    <center><span class="uk-text-muted uk-text-small">Till now</span></center>
                                    <center><h2 class="uk-margin-remove"><span class="countUpMe">{{$totalbpartyCount}}<noscript>12456</noscript></span></h2></center>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
                            <span class="uk-text-muted uk-text-small" id = "Titles">Current Enrollment</span>
                             <!--<table style="width: 100% " >
                                <thead>
                                        <tr>
                                            <th align="left"><span class="uk-text-muted uk-text-small">ParentChild:{{$totalParentchildCourse}}</span></th>
                                        </tr><tr>
                                            <th align="right" valign="right"><span class="uk-text-muted uk-text-small">Pre/K: {{$totalPrekgKindergarten}}</span></th>
                                        </tr><tr>
                                            <th align="right" valign="right"><span class="uk-text-muted uk-text-small">Gradeschool: {{$totalGradeschool}}</span></th>
                                        </tr><tr>   
                                            <th align="right" valign="right"><span class="uk-text-muted uk-text-small">Total: {{$totalCourses}}</span></th>
                                        
                                        </tr>
                                </thead>
                            </table>-->

                            <div class = "row" style = "">
                                <div class = "col-md-12">  
                                        <h5><span class="uk-text-muted uk-text-small_"><span>Parent & Child :</span><b> &nbsp;&nbsp;{{$totalParentchildCourse}}</b></span></h5>
                                        <h5><span class="uk-text-muted uk-text-small_"><span>Pre / K:</span><b> &nbsp;&nbsp; {{$totalPrekgKindergarten}}</b></span></h5>
                                        <h5><span class="uk-text-muted uk-text-small_"><span>Grade School:</span><b> &nbsp;&nbsp; {{$totalGradeschool}}</b></span></h5>
                                        <h5><span class="uk-text-muted uk-text-small_"><span>Total:</span><b> &nbsp;&nbsp; {{$totalCourses}}</b></span></h5>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>

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
                                           	<!--<td class="uk-width-3-10 uk-text-nowrap">{{--$items->Customers['customer_email']--}}</td>-->
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
                                            <td class="uk-width-3-10 uk-text-nowrap">{{$items->Customers->customer_name}}{{$items->Customers->customer_lastname}}<a href="{{url()}}/customers/view/{{$items->Customers->id}}?tab=ivfollowup"></a></td>
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
                                <h3>Upcoming Birthdays</h3>
                                
                                <table class="uk-table" id="birthdayLog">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-nowrap">Customer</th>
                                            <th class="uk-text-nowrap">Kid</th>
                                            <th class="uk-text-nowrap">Mobile No</th>                                            
                                            <th class="uk-text-nowrap">DOB</th>
                                            <th class="uk-text-nowrap">Type</th>
                                         
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <?php if(isset($birthday_data)){for($i=0;$i<count($birthday_data);$i++){?>
                                        <tr>
                                            <td>
                                                {{$birthday_data[$i]['customer_name']}}
                                                <a href="{{url()}}/customers/view/{{$birthday_data[$i]['customer_id']}}?tab=birthdayparty"></a>
                                            </td>
                                            <td> 
                                                {{$birthday_data[$i]['student_name']}}
                                            </td>
                                            <td> 
                                                {{$birthday_data[$i]['mobile_no']}}
                                            </td>
                                            <td> {{$birthday_data[$i]['student_date_of_birth']}}
                                            
                                            </td>
                                            <td> <?php if($birthday_data[$i]['membership']==0){
                                                 echo "Non member";
                                                }else{
                                                    echo "Member";
                                                }
                                                 ?>
                                            </td>
                                        </tr>
                                        
                                       <?php }} ?>
                                      
                                        
           <?php 
            for($i=0;$i<count($birthday_data_month);$i++){
              if(isset($birthday_data_month[$i][0])){
                for($j=0;$j<count($birthday_data_month[$i]);$j++){
                ?>   
                
                    <tr>
                                            <td>
                                                {{$birthday_data_month[$i][$j]['customer_name']}}
                                                <a href="{{url()}}/customers/view/{{$birthday_data_month[$i][$j]['customer_id']}}?tab=birthdayparty"></a>
                                            </td>
                                            <td> {{$birthday_data_month[$i][$j]['student_name']}}
                                            
                                            </td>
                                            <td> {{$birthday_data_month[$i][$j]['mobile_no']}}
                                            
                                            </td>
                                            <td> {{$birthday_data_month[$i][$j]['student_date_of_birth']}}
                                            
                                            </td>
                                            <td> <?php if($birthday_data_month[$i][$j]['membership']==0){
                                                 echo "Non member";
                                                }else{
                                                    echo "Member";
                                                }
                                                 ?>
                                            </td>
                      </tr>
      <?php    }
              }
            }
      ?>
                      
                      
                      <?php if(isset($birthday_month_startdays[0])){ for($i=0;$i<count($birthday_month_startdays);$i++){ ?>
                            <tr>
                                            <td>
                                                {{$birthday_month_startdays[$i]['customer_name']}}{{$birthday_month_startdays[$i]['customer_lastname']}}
                                                <a href="{{url()}}/customers/view/{{$birthday_month_startdays[$i]['customer_id']}}?tab=birthdayparty"></a>
                                            </td>
                                            <td> {{$birthday_month_startdays[$i]['student_name']}}
                                            
                                            </td>
                                            <td> {{$birthday_month_startdays[$i]['mobile_no']}}
                                            
                                            </td>
                                            <td> {{$birthday_month_startdays[$i]['student_date_of_birth']}}
                                            
                                            </td>
                                            <td> <?php if($birthday_month_startdays[$i]['membership']==0){
                                                 echo "Non member";
                                                }else{
                                                    echo "Member";
                                                }
                                                 ?>
                                            </td>
                            </tr>
                      <?php }}?>

                                        
                                    </tbody>
                                </table>
                               
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
                                <div width = "100%">
                                    <div style = "width: 60%; position: absolute">
                                        <p style  ="font-size: 24px;">Birthdays Celebration </p>        
                                    </div>
                                    <div style = "width: 40%; margin-left: 60%">
                                        <select id="BdayPatiesFilterByDate" required="required" class="form-control input-sm md-input" style="padding: 0px;font-weight:bold;color: #727272;">
                                            <option value="Week">By Week</option>
                                            <option value="Month">By Month</option>
                                            <option value="Year">By Year</option>
                                        </select>

                                    </div>
                                </div>
                                <br clear="all"/>
                            	<div id = "allBdayData"></div>
                                <table class="uk-table" id="birthdayCelebrationTable">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-nowrap">Customer</th>
                                            <th class="uk-text-nowrap">Kid</th>
                                            <th class="uk-text-nowrap">Mobile No</th>                                            
                                            <th class="uk-text-nowrap">DOB</th>
                                            <th class="uk-text-nowrap">Time</th>
                              
                                        </tr>
                                    </thead>
                                    <tbody id = "BirthdayTableBody">
                                        <?php for($i=0;$i<count($birthdayPresentWeek);$i++){
                                            if($birthdayPresentWeek[$i]['franchisee_id']==$f_id){?>
                                        <tr>
                                            <td>{{$birthdayPresentWeek[$i]['customer_name']}}{{$birthdayPresentWeek[$i]['customer_lastname']}}
                                            <a href="{{url()}}/customers/view/{{$birthdayPresentWeek[$i]['customer_id']}}?tab=birthdayparty"></a>
                                            </td>
                                            <td>{{$birthdayPresentWeek[$i]['student_name']}}</td>
                                            <td>{{$birthdayPresentWeek[$i]['mobile_no']}}</td>
                                            <td>{{$birthdayPresentWeek[$i]['birthday_party_date']}}</td>
                                            <td>{{$birthdayPresentWeek[$i]['birthday_party_time']}}</td>
                                        </tr>
                                            <?php }} ?>
                                    </tbody>
                                </table>
                               
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              

@stop