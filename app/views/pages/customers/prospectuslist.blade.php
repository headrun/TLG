@extends('layout.master')

@section('libraryCSS')
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all">
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
    <!-- <link href='https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css' />
    <link href='https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css' />
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' /> -->
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

	$("#customersTable").DataTable({
		dom: 'Bfrtip',
        	buttons: [
            		'copyHtml5',
           		 'excelHtml5',
           		 'csvHtml5',
            		'pdfHtml5'
        		],
        	"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			$(document).find('#customersTable td').click(function(){
        		//	console.log($(this).attr('customer_id'));   
			});
        	
            		return nRow;
      		 },
       		"iDisplayLength": 50,
       		"lengthMenu": [ 10, 50, 100, 150, 200 ]
	 });


	$("#introVisitDateDiv").hide();
	$("#state").change(function (){
		 getCities($("#state").val(), 'city');
	});

	$("#customerEmail").blur(function (){
		isCustomerExists();
	});


	function isCustomerExists(){
		
		var ajaxUrl = "{{url()}}/quick/"+"customerexistence";
		console.log(ajaxUrl);
		var isExists = "no";
		$.ajax({
			  type: "POST",
			  url: ajaxUrl,
			  dataType: 'json',
			  async: true,
			  data:{'email':$("#customerEmail").val()},
			  success: function(response, textStatus, jqXHR)
			  {
				    if (response.status == "exists"){	
					    isExists = "yes";			    	
				    	$("#callbackMessage").html('<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a>Sorry, This Email address already exists.</div>');
				    }else{
				    	$("#callbackMessage").html("");
				    }			  
			  },
			  error: function (jqXHR, textStatus, errorThrown)
			  { }
		});

		console.log(isExists);
		return isExists;
	}


	function onDateChangeFunction(){
		//alert($("#introVisitDate").val());
		$("#availabilityCheckDiv").show();
		$("#introVisitModal").modal("show");


		var ajaxUrl = "{{url()}}/quick/"+"checkslots";
		console.log(ajaxUrl);

		$.ajax({
			  type: "POST",
			  url: ajaxUrl,
			  dataType: 'json',
			  async: true,
			  data:{'datetime':$("#introVisitDate").val()},
			  success: function(response, textStatus, jqXHR)
			  {

				    if (response.status == "success"){
				    	$("#availabilityCheckDiv").hide();
				    	$("#submitMsgDiv").html("");
				    	$("#messageDiv").html('<p class="uk-alert uk-alert-success">Great! The selected time slot is available</p>');
				    	$("#customerSubmit").show();
				    	
				    }else{
				    	$("#availabilityCheckDiv").hide();
				    	$("#submitMsgDiv").html('<p class="uk-alert uk-alert-danger">Sorry! The selected time slot is not available</p>');
				    	$("#messageDiv").html('<p class="uk-alert uk-alert-danger">Sorry! The selected time slot is not available</p>');
				    	$("#customerSubmit").hide();
				    }
			  
			  },
			  error: function (jqXHR, textStatus, errorThrown)
			  {
		 
			  }
		});
	}

	$("#introVisitDate").kendoDateTimePicker({
		change:onDateChangeFunction
	});

	$("#reminderTxtBox").kendoDatePicker();

	$("#introVisit").change(function (){

		//alert("changed");

		if ($(this).is(':checked')) {

			$("#introVisitDateDiv").show();
			$("#introVisitDate").attr("required", true);
			
		}else{
			$("#introVisitDateDiv").hide();
			$("#introVisitDate").attr("required", false);

		}

	});

	function getCities(regionCode, targetSelectorId){

		var ajaxUrl = "{{url()}}/quick/"+"getCities";
		console.log(ajaxUrl);

		$.ajax({
			  type: "POST",
			  url: ajaxUrl,
			  dataType: 'json',
			  async: true,
			  data:{'id':regionCode, 'countryCode':"IN"},
			  success: function(response, textStatus, jqXHR)
			  {
				    
				   
				    //$("#"+targetSelectorId).append('<option value="" selected>Select City</option>');

				   console.log(response);
				   $('#'+targetSelectorId).empty();
				   $('#'+targetSelectorId).append('<option value=""></option');
				   $.each(response, function (index, item) {
				         $('#'+targetSelectorId).append(
				              $('<option></option>').val(index).html(item)
				          );
				     });
			  
			  },
			  error: function (jqXHR, textStatus, errorThrown)
			  {
		 
			  }
		});
	}


$("#customerSubmit").click(function (event){
	
	event.preventDefault();
	var ajaxUrl = "{{url()}}/quick/"+"customerexistence";
	console.log(ajaxUrl);
	var isExists = "no";$.ajax({
		  type: "POST",
		  url: ajaxUrl,
		  dataType: 'json',
		  async: true,
		  data:{'email':$("#customerEmail").val()},
		  success: function(response, textStatus, jqXHR)
		  {
			    if (response.status == "exists"){	
				    		    	
			    	$("#callbackMessage").html('<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a>Sorry, This Email address already exists.</div>');
			    }else if(response.status == "clear"){
			    	$("#callbackMessage").html("");
			    	$("#addCustomerForm").submit();
			    }			  
		  },
		  error: function (jqXHR, textStatus, errorThrown)
		  { }
	});
	
});

$(document).on('change', 'tbody td .leadTypeDropdown', function() { 
    var lead_type = $(this).val();
    var customer_id = $(this).attr('data');
  //  var customer_id = '1253';
    $.ajax({
    	type: "POST",
    	url: "{{URL::to('/quick/UpdateCustomerLogs')}}",
        data: {'customer_id':customer_id, 'lead_type':lead_type},
    	dataType:"json", 
	success: function (response) {
		if(response.status == "success"){
			$('#updateLeadType').show();
			setTimeout(function(){
               window.location.reload(1);
            }, 3000);
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
		<li><a href="{{url()}}/customers/prospectslist" style="z-index:8;">Customers</a></li>
		<li><a href="#" style="z-index:7;">Prospects List</a></li>
	</ul>
</div>
<br clear="all"/>
<div class="">
	<div id="updateLeadType" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
	    <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:20px;">
	    <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:25%;">
	     Updated lead type.Please wait . . .
	    </p>
	</div>
	<div class="row">
	
		
		
		
			<h4>Prospects Customers List</h4>
		
            
            
		            
		            <?php 
		            	/*  echo "<pre>";
		            	print_r($customers);
		            	echo "</pre>";  */
		            
		            ?>
		
		           
		            <div class="md-card uk-margin-medium-bottom">
		                <div class="md-card-content">
		                    <div class="uk-overflow-container">
		                        <table class="uk-table table-striped" id="customersTable">
		                            <!-- <caption>Table caption</caption> -->
		                            <thead>
		                            <tr>
		                                <th>Customer</th>
		                                <th>Email</th>
		                                <th>Mobile No</th>
                                                <th>Followup Type</th>
		                                <th>Followup Date</th>
                                                <th>Lead Type</th>
						<th>Created at</th>
		                                <!-- <th>Action</th> -->
		                            </tr>
		                            </thead>
		                            <tbody>
                                                <?php if(isset($customers)){ ?>
		                            @foreach($customers as $customer)
		                            <tr id="{{ $customer->id }}">
		                                <td>
                                                  <a href="{{url()}}/customers/view/{{$customer->id}}">
                                                    {{$customer->customer_name.' '}}&nbsp;{{$customer->customer_lastname}}
                                                  </a>
                                                </td>
						<td>{{$customer->customer_email}}</td>
		                                <td>{{$customer->mobile_no}}</td>
						<td>{{$customer->followup_type}}</td>
						<td>{{$customer->reminder_date}}</td>
							<td>
                         			     		<select class="leadTypeDropdown form-control" data="{{ $customer->id }}">
                            						<option value=""></option>
                            						<option value="new" {{ $customer->lead_status == 'new' ? 'selected' : '' }}>New</option>
                            						<option value="hot" {{ $customer->lead_status == 'hot' ? 'selected' : '' }}>Hot</option>
                            						<option value="not_interested" {{ $customer->lead_status == 'not_interested' ? 'selected' : '' }}>Archived - No</option>
									<option value="interested" {{  $customer->lead_status == 'interested' ? 'selected' : '' }}>Archived - Future</option>
                        			     		</select>
                    					</td>		
		                                <td>{{ date('d-M-Y',strtotime($customer->created_at)); }}<a style="display: none;" href="{{url()}}/customers/view/{{$customer->id}}"></a></td>
		                            </tr>
		                            @endforeach
                                            <?php } ?>
		                          </tbody>
		                        </table>
		                    </div>
		                </div>
		            </div>
				
			
		
		
		
		
		
	</div><!-- row -->
</div><!-- Container -->


<!-- Modal -->
<div id="introVisitModal" class="modal fade" role="dialog" style="z-index: 99999;
    margin-top: 50px;">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Checking availability...</h4>
      </div>
      <div class="modal-body">
      
      		<div id="availabilityCheckDiv">      		
				<p>Please wait while we check availability of selected date and time</p>      		
      		</div>
      		<div id="messageDiv">
      		
      		
      		</div>
      
      
        	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
 
<!--- <div class="md-fab-wrapper">
<a class="md-fab md-fab-accent" href="{{url()}}/customers/add" title="Add customers">
<i class="material-icons">&#xE03B;</i>
</a>
</div>  ----->
@stop
