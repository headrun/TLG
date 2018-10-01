@extends('layout.master')
@section('libraryCSS')
	<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all"> -->
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
    <link href='{{url()}}/assets/css/bootstrap.min.css' rel='stylesheet' />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- <link href="https://cdn.datatables.net/buttons/1.2.0/css/buttons.dataTables.min.css" rel="stylesheet"> -->
@stop

@section('libraryJS')
<script src="{{url()}}/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="{{url()}}/bower_components/datatables-colvis/js/dataTables.colVis.js"></script>
<script src="{{url()}}/bower_components/datatables-tabletools/js/dataTables.tableTools.js"></script>
<script src="{{url()}}/assets/js/custom/datatables_uikit.min.js"></script>
<script src="{{url()}}/assets/js/pages/plugins_datatables.min.js"></script>

 <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/dataTables.buttons.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.0/js/buttons.html5.min.js"></script>
 <script src="{{url()}}/assets/js/kendoui_custom.min.js"></script>
<script src="{{url()}}/assets/js/pages/kendoui.min.js"></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
<script type="text/javascript">
  $('#marBudMonth').kendoDatePicker( {format: "dd-MM-yyyy"});
  $('#UpdatemarBudMonth').kendoDatePicker( {format: "dd-MM-yyyy"});
  /*$('#marBudMonth').datepicker({
       changeMonth: true,
       changeYear: true,
       dateFormat: 'MM yy',
         
       onClose: function() {
          var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
          var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
          $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
       },
         
       beforeShow: function() {
         if ((selDate = $(this).val()).length > 0) 
         {
            iYear = selDate.substring(selDate.length - 4, selDate.length);
            iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
            $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
             $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
         }
      }
    });*/
  $(document).on('click', '#saveUpdatedMarBud', function(){
    var selectedMonth = $('#UpdatemarBudMonth').val();
    var budget = $('#updatedBudget').val();
    if (typeof selectedMonth !== 'undefined' && typeof budget !== 'undefined' ) {
        $.ajax({
            type: "POST",
            url: "{{URL::to('quick/addMarketingBudget')}}",
            data: {'budgetMonth': selectedMonth, 'budgetAmount': budget},
            dataType: 'json',
            success: function(response){
                if(response.status === "success"){
                  $('#saveUpdatedMarBud').addClass('disabled');
                  $("#MBupdateStatusMsg").html('<p class="uk-alert uk-alert-success">Marketing Budget has been Updated successfully.Please wait untill the page reloads.</p>');
                  $('#editModal').modal('hide');
                  $('#divLoadingUpdate').show();
                  setTimeout(function(){
                    window.location.reload(1);
                  }, 2000);
                } else {
                  $("#MBupdateStatusMsg").html('<p class="uk-alert uk-alert-danger">Marketing budget updation is failed.Please try again.</p>');
                  setTimeout(function(){
                    window.location.reload(1);
                  }, 2000);
                }
            }
        });
    }
  });

  $(document).on('click', '#saveMarBud', function(){
      var selectedMonth = $('#marBudMonth').val();
      var budget = $('#budgetAmount').val();
      $.ajax({
          type: "POST",
          url: "{{URL::to('quick/checkMbExist')}}",
          data: {'budgetMonth': selectedMonth, 'budgetAmount': budget},
          dataType: 'json',
          success: function(response){
            if(response.status === "success"){
              $("#MBstatusMsg").html('<p class="uk-alert uk-alert-warning">Already marketing budget is added for the following selected month & year.</p>');
            } else {
              if (typeof selectedMonth !== 'undefined' && typeof budget !== 'undefined' ) {
                  $.ajax({
                      type: "POST",
                      url: "{{URL::to('quick/addMarketingBudget')}}",
                      data: {'budgetMonth': selectedMonth, 'budgetAmount': budget},
                      dataType: 'json',
                      success: function(response){
                          if(response.status === "success"){
                            $('#saveMarBud').addClass('disabled');
                            $("#MBstatusMsg").html('<p class="uk-alert uk-alert-success">Marketing Budget has been added successfully.Please wait untill the page reloads.</p>');
                            $('#myModal').modal('hide');
                            $('#divLoadingAdding').show();
                            setTimeout(function(){
                              window.location.reload(1);
                            }, 2000);
                          } else {
                            $("#MBstatusMsg").html('<p class="uk-alert uk-alert-danger" style="padding-right:100px;">You are trying to add marketing budget is failed.Please try again.</p>');
                            setTimeout(function(){
                              window.location.reload(1);
                            }, 2000);
                          }
                      }
                  });
              }
            }
          }
      });
  });
    

</script>
@stop

@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">Reports</a></li>
		<li><a href="#" style="z-index:7;">KBI</a></li>
	</ul>
</div>
<br clear="all"/>
<br clear="all"/>
<div id="divLoadingAdding" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
  <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:18px;">
  <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:11%;">
   Budget Added succussfully.Please wait . . .
  </p>
</div>
<div id="divLoadingUpdate" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
  <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:18px;">
  <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:20%;">
   Budget updated succussfully.Please wait . . .
  </p>
</div>
<div>
  <div class="uk-width-medium">
      <div class="row">
        <div align="right" style="padding-right:50px;">
          <button class="md-btn md-btn-primary" data-toggle="modal" data-target="#myModal">Add Marketing Budget</button>
          <div class="modal fade" id="myModal" role="dialog" style="margin-top:100px;">
              <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                  <!-- <form> -->
                  {{ Form::open(array('url' => '/reports/addMarketingBudget','id'=>"marketingBudgetForm","class"=>"uk-form-stacked")) }}
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><center>Add Marketing Budget</center></h4>
                  </div>
                  <div id="MBstatusMsg"></div>
                  <div class="modal-body" align="left">
                      <div class="row">
                        <div class="col-md-6">
                          <center>
                            <label for="startDate" style="padding-left:150px;padding-top:10px;">Select Month </label>
                          </center>
                        </div>
                        <div class="col-md-4">
                          <center>
                            <!-- <input type="text" class="form-control" id="marBudMonth" required /> -->
                            {{ Form::text('marBudMonth',null,array('id'=>'marBudMonth', 'class' => 'form-control', 'required'))}}<br>
                          </center>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <center>
                            <label for="ForAmount" style="padding-top:25px;padding-left:150px;">Enter Budget( ₹ )</label>
                          </center>
                        </div>
                        <div class="col-md-4" style="padding-top:14px;">
                          <center>
                            <!-- <input type="number" class="form-control" id="budgetAmount" min="0" required />(In Rs/-) -->
                            {{Form::number('budgetAmount',null,array('id'=>'budgetAmount','min' => 0, 'class' => 'form-control', 'required'))}}(In Rs/-)
                          </center>
                        </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="md-btn md-btn-primary saveMarBud" id="saveMarBud">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                  {{ Form::close() }}
                </form>
                </div>
                
              </div>
            </div>
        </div>
        <center><span class="uk-text-muted uk-text-small" id = "Titles"><h3>Leads Info</h3></span></center>
      </div>
      <div class="md-card">
          <div class="md-card-content">
            <table class="table table-bordered">
              <thead>
                  <tr>
                  <th></th>
                <th><center>Current Month<br><span class='uk-text-muted uk-text-small_'>({{ $currentMonthStartDate }} - Today)</span></center></th>
                <th><center>Current Week<br><span class='uk-text-muted uk-text-small_'>({{ $endOfWeekDate }} - Today)</span></center></th>
                @foreach($weeks as $date)
                      <th><center>{{ $date['start']}} - {{ $date['end']}}</center></th>
                    @endforeach
                  </tr>
              </thead>
              <tbody>
                  <tr>
                    <th>New Leads</th>
                    <td><center>{{ $currentMonthNewLeads + $IvScheduledInThisMonth }}</center></td>
                    <td><center>{{ $newLeadsForcurrentWeek + $currentWeekIvScheduled }}</center></td>
                    <td><center>{{ $newLeadsForWeek1 + $IvScheduledInWeek1}}</center></td>
                    <td><center>{{ $newLeadsForWeek2 + $IvScheduledInWeek2}}</center></td>
                    <td><center>{{ $newLeadsForWeek3 + $IvScheduledInWeek3}}</center></td>
                    <td><center>{{ $newLeadsForWeek4 + $IvScheduledInWeek4}}</center></td>
                  </tr>  
                  <tr> 
                    <th>IV Attended</th>
                    <td><center>{{ $IvAttendedInThisMonth}}</center></td>
                    <td><center>{{ $currentWeekIvAttended }}</center></td>
                    <td><center>{{ $IvAttendedInWeek1 }}</center></td>
                    <td><center>{{ $IvAttendedInWeek2 }}</center></td>
                    <td><center>{{ $IvAttendedInWeek3 }}</center></td>
                    <td><center>{{ $IvAttendedInWeek4 }}</center></td>
                </tr>
                <tr>
                    <th>Outstanding Leads</th>
                    <td><center>{{ $currentMonthNewLeads + $IvScheduledInThisMonth + $thisMonthOutStandLeads }}</center></td>
                    <td><center>{{ $newLeadsForcurrentWeek + $currentWeekIvScheduled + $currentWeekOutStandLeads }}</center></td>
                    <td><center>{{ $newLeadsForWeek1 + $IvScheduledInWeek1 + $outStandLeadsWeek1 }}</center></td>
                    <td><center>{{ $newLeadsForWeek2 + $IvScheduledInWeek2 + $outStandLeadsWeek2 }}</center></td>
                    <td><center>{{ $newLeadsForWeek3 + $IvScheduledInWeek3 + $outStandLeadsWeek3 }}</center></td>
                    <td><center>{{ $newLeadsForWeek4 + $IvScheduledInWeek4 + $outStandLeadsWeek4 }}</center></td>
                </tr> 
                <tr>
                    <th>IV Scheduled</th>
                    <td><center>{{ $IvScheduledInThisMonth}}</center></td>
                    <td><center>{{ $currentWeekIvScheduled }}</center></td>
                    <td><center>{{ $IvScheduledInWeek1 }}</center></td>
                    <td><center>{{ $IvScheduledInWeek2 }}</center></td>
                    <td><center>{{ $IvScheduledInWeek3 }}</center></td>
                <td><center>{{ $IvScheduledInWeek4 }}</center></td>
                  </tr>
                  <tr>
                    <th>Hot Leads</th>
                    <td><center>{{ $currentMonthHotLeads }}</center></td>
                    <td><center>{{ $currentWeekHotLeadsYes }}</center></td>
                    <td><center>{{ $hotLeadsYesWeek1 }}</center></td>
                    <td><center>{{ $hotLeadsYesWeek2 }}</center></td>
                    <td><center>{{ $hotLeadsYesWeek3 }}</center></td>
                    <td><center>{{ $hotLeadsYesWeek4 }}</center></td>
                </tr>
                  <tr>
                    <th>Archived - No</th>
                    <td><center>{{ $currentMonthNoLeads }}</center></td>
                    <td><center>{{ $currentWeekHotLeadsNo }}</center></td>
                    <td><center>{{ $hotLeadsNoWeek1 }}</center></td>
                    <td><center>{{ $hotLeadsNoWeek2 }}</center></td>
                    <td><center>{{ $hotLeadsNoWeek3 }}</center></td>
                    <td><center>{{ $hotLeadsNoWeek4 }}</center></td>
                  </tr>
                  <tr>
                    <th>Archived - Future</th>
                    <td><center>{{ $currentMonthMaybeLeads }}</center></td>
                    <td><center>{{ $currentWeekHotLeadsMaybe }}</center></td>
                    <td><center>{{ $hotLeadsMaybeWeek1 }}</center></td>
                    <td><center>{{ $hotLeadsMaybeWeek2 }}</center></td>
                    <td><center>{{ $hotLeadsMaybeWeek3 }}</center></td>
                    <td><center>{{ $hotLeadsMaybeWeek4 }}</center></td>
                  </tr>
                <tr>
                  <th>Renewals Due</th>
                  <td><center>{{ $currentMonthRenewalDue }}</center></td>
                  <td><center>{{ $currentWeekRenewalDue }}</center></td>      
                  <td><center>{{ $renewalDueWeek1 }}</center></td>
                  <td><center>{{ $renewalDueWeek2 }}</center></td>
                  <td><center>{{ $renewalDueWeek3}}</center></td>
                  <td><center>{{ $renewalDueWeek4}}</center></td>
                </tr>
              </tbody>
            </table>
          </div>
      </div> 
   </div> 
</div><br><br>
    <div class="row">
      <div class="col-md-6">
        <div class="md-card">
          <div class="md-card-content">
              <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
             <span style="float:right;cursor:pointer;" class="btn btn-warning btn-xs">
              <i class="material-icons" data-toggle="modal" data-target="#editModal">
                edit
              </i>
             </span>
             <center><span class="uk-text-muted uk-text-small" id = "Titles"><h4>Marketing budget <strong class="uk-text-muted uk-text-small">( For {{date('M')}} Month )</strong></h4></span></center>
               
             <table style="width:100%;text-align: center;">
                <tr>
                  <center>
                  <td><h2>₹.{{$marketingBudget}}/-<h2></td>
                </tr>
              </table>
           </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="md-card">
          <div class="md-card-content">
              <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
             <center><span class="uk-text-muted uk-text-small" id = "Titles"><h4>Marketing efficiency</h4></span></center>
               
             <table style="width:100%;text-align: center;">
                <tr>
                  <td><h2><!-- {{$MarketingEff}} -->0 %<h2></td>
                </tr>
              </table>
           </div>
        </div>
      </div>
    </div><br clear="all"><br clear="all">
    <div class="row">
      <div class="col-md-6">
        <div class="md-card">
          <div class="md-card-content">
              <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
             <center><span class="uk-text-muted uk-text-small" id = "Titles"><h4>New Leads</h4></span></center>
               
             <table style="width:100%;text-align: center;">
                <tr>
                  <td><h2>{{ $currentMonthNewLeads + $IvScheduledInThisMonth }}<h2></td>
                </tr>
              </table>
           </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="md-card">
          <div class="md-card-content">
              <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
             <center><span class="uk-text-muted uk-text-small" id = "Titles"><h4>IV Scheduled</h4></span></center>
               
             <table style="width:100%;text-align: center;">
                <tr>
                  <td><h2>{{$IvScheduledInThisMonth}}<h2></td>
                </tr>
              </table>
           </div>
        </div>
      </div>
    </div><br clear="all"><br clear="all">
    <div class="row">
      <div class="col-md-6">
        <div class="md-card">
          <div class="md-card-content">
              <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
             <center><span class="uk-text-muted uk-text-small" id = "Titles"><h4>Intro Attendance efficiency</h4></span></center>
               
             <table style="width:100%;text-align: center;">
                <tr>
                  <td><h2>{{$introAttendaceEff}} %<h2></td>
                </tr>   
             </table>
           </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="md-card">
          <div class="md-card-content">
              <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
             <center><span class="uk-text-muted uk-text-small" id = "Titles"><h4>Intro Conversion</h4></span></center>
               
             <table style="width:100%;text-align: center;">
                <tr>
                  <td><h2>{{$introConversation}} %<h2></td>
                </tr>   
             </table>
           </div>
        </div>
      </div>
    </div><br clear="all"><br clear="all">
    <div class="row">
      <div class="col-md-6">
        <div class="md-card">
          <div class="md-card-content">
              <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
             <center><span class="uk-text-muted uk-text-small" id = "Titles"><h4>Enrollment</h4></span></center>
             <div class = "row" style="padding-top:10px;">
                <div class="col-md-6">
                   <center><span class="uk-text-muted uk-text-small">New Enrollments</span></center>
                   <center>
                     <h2 class="uk-margin-remove" style="padding-bottom:8px;padding-top:4px;">
                       <span class="countUpMe">{{$NoOfNewEnrollments}}
                         <noscript>12456</noscript>
                       </span>
                     </h2>
                   </center>
                </div>
                <div class="col-md-6">
                   <center><span class="uk-text-muted uk-text-small">Total Enrollments</span></center>
                   <center>
                     <h2 class="uk-margin-remove" style="padding-bottom:8px;padding-top:4px;">
                       <span class="countUpMe">{{$totalEnrollmetns}}
                         <noscript>12456</noscript>
                       </span>
                     </h2>
                   </center>
                </div>
             </div>
           </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="md-card">
          <div class="md-card-content">
              <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
             <center><span class="uk-text-muted uk-text-small" id = "Titles"><h4>Renewals</h4></span></center>
             <div class = "row" style="padding-top:10px;">
                <div class="col-md-4">
                   <center><span class="uk-text-muted uk-text-small">Done</span></center>
                   <center>
                     <h2 class="uk-margin-remove" style="padding-bottom:8px;padding-top:4px;">
                       <span class="countUpMe">{{$noOfRenewalsDoneInthisMonth}}
                         <noscript>12456</noscript>
                       </span>
                     </h2>
                   </center>
                </div>
                <div class="col-md-4">
                   <center><span class="uk-text-muted uk-text-small">Due</span></center>
                   <center>
                     <h2 class="uk-margin-remove" style="padding-bottom:8px;padding-top:4px;">
                       <span class="countUpMe">{{$currentMonthRenewalDue - $noOfRenewalsDoneInthisMonth}}
                         <noscript>12456</noscript>
                       </span>
                     </h2>
                   </center>
                </div>
                <div class="col-md-4">
                   <center><span class="uk-text-muted uk-text-small">Total Renewals</span></center>
                   <center>
                     <h2 class="uk-margin-remove" style="padding-bottom:8px;padding-top:4px;">
                       <span class="countUpMe">{{$currentMonthRenewalDue}}
                         <noscript>12456</noscript>
                       </span>
                     </h2>
                   </center>
                </div>
             </div>  
             <!-- <table style="width:100%;text-align: center;">
                <tr>
                  <td><h2>{{$currentMonthRenewalDue}} <h2></td>
                </tr>
              </table> -->
           </div>
        </div>
      </div>
    </div> 
    <!-- EDIT MARKETING BUDGET --> 
    <div class="modal fade" id="editModal" role="dialog" style="margin-top:100px;">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <!-- <form> -->
            {{ Form::open(array('url' => '/reports/addMarketingBudget','id'=>"marketingBudgetForm","class"=>"uk-form-stacked")) }}
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><center>Update Marketing Budget</center></h4>
            </div>
            <div id="MBupdateStatusMsg"></div>
            <div class="modal-body" align="left">
                <div class="row">
                  <div class="col-md-6">
                    <center>
                      <label for="startDate" style="padding-left:150px;padding-top:10px;">Select Month </label>
                    </center>
                  </div>
                  <div class="col-md-4">
                    <center>
                      <!-- <input type="text" class="form-control" id="marBudMonth" required /> -->
                      {{ Form::text('UpdatemarBudMonth',null,array('id'=>'UpdatemarBudMonth', 'class' => 'form-control', 'required'))}}<br>
                    </center>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <center>
                      <label for="ForAmount" style="padding-top:25px;padding-left:150px;">Enter Budget( ₹ )</label>
                    </center>
                  </div>
                  <div class="col-md-4" style="padding-top:14px;">
                    <center>
                      <!-- <input type="number" class="form-control" id="budgetAmount" min="0" required />(In Rs/-) -->
                      {{Form::number('updatedBudget',null,array('id'=>'updatedBudget','min' => 0, 'class' => 'form-control', 'required'))}}(In Rs/-)
                    </center>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="md-btn md-btn-primary saveUpdatedMarBud" id="saveUpdatedMarBud">Save</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            {{ Form::close() }}
          </form>
          </div>
          
        </div>
      </div>   
@stop
