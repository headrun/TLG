@extends('layout.master')

@section('libraryCSS')
	<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all"> -->
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' />
@stop

@section('libraryJS')
<script src="{{url()}}/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="{{url()}}/bower_components/datatables-colvis/js/dataTables.colVis.js"></script>
<script src="{{url()}}/bower_components/datatables-tabletools/js/dataTables.tableTools.js"></script>
<script src="{{url()}}/assets/js/custom/datatables_uikit.min.js"></script>
<script src="{{url()}}/assets/js/pages/plugins_datatables.min.js"></script>
<script src="{{url()}}/assets/js/kendoui_custom.min.js"></script>
<script src="{{url()}}/assets/js/pages/kendoui.min.js"></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
<script type="text/javascript">

$("#customersTable").DataTable();
$("#customersTable tr").click(function (){
	//window.location = $(this).find('a').attr('href');
});

	
	
</script>

<script type="text/javascript">

function deleteUser(user_id) {
    $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/deleteUserFromUsers')}}",
        dataType: 'json',
        async: true,
        data:{'user_id': user_id},
        success: function(response)
        {
          if(response.status == "success"){
          		$("#messageForUserDelete").html('<p class="uk-alert uk-alert-success">User has been deleted successfully. Please wait till this page reloads</p>');
	            setTimeout(function(){
	             window.location.reload(1);
	          }, 3000);    
          }else{
   				$("#messageForUserDelete").html('<p class="uk-alert uk-alert-success">User could not be deleted. Please try again later</p>');	
          }
        }
    });   
}
$(document).on('click', '#updateBatchId', function(){

    var update_id = $('#updateId').val();
    var batch_id = $('#batchId').val();
    if (typeof update_id !== 'undefined' && typeof batch_id !== 'undefined' ) {
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/UpdateDataBatch')}}",
            data: {'update_id': update_id, 'batch_id': batch_id},
            dataType: 'json',
            success: function(response){
            	
            }
            });
    }

 
});
</script>
@stop

@section('content')

<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="{{url()}}/admin/users" style="z-index:8;">Users</a></li>
		<li><a href="#" style="z-index:7;">All Users</a></li>
	</ul>
</div>
<br clear="all"/>
<div class="">
	<div class="row">
	
		
		
		    <!-- <h3 class="heading_b uk-margin-bottom">Update Batch Details</h3>
                        {{ Form::open(array('url' => '/reports/updateDataBatch', 'id'=>"updateDataBatchform", "class"=>"uk-form-stacked", 'method' => 'post')) }} 
                          <div class="uk-grid" data-uk-grid-margin>
                              <div class="uk-width-medium-1-4">
                                <div class="parsley-row form-group">
                                  <label for="updateId">Update batch ID</label><br>
                                    {{Form::text('updateId', null,array('id'=>'updateId'))}} 
                                </div>
                              </div>
                              <div class="uk-width-medium-1-4">
                               <div class="parsley-row form-group">
                                  <label for="batchId">Batch ID</label><br>
                                    {{Form::text('batchId', null,array('id'=>'batchId'))}} 
                               </div>
                            </div>

                              <div class="uk-width-1-4">
                                <div class="parsley-row" style="padding: 25px 30px;">
                                  <button type="button" class="md-btn md-btn-primary" id="updateBatchId">Update</button>
                                </div>
                              </div>
                            </div>
                        {{ Form::close() }} -->
			<h4>List of Users <span style="font-size:12px;">(Other than admins)</span></h4>
		
            <div id="messageForUserDelete"></div>
              <div class="md-card">
	            <div class="md-card-content large-padding">
		            <h3 class="heading_b uk-margin-bottom">Users</h3>
		            
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
		                                <th>User Name</th>
		                                <th>Email</th>
		                                <th>User Type</th>
		                                <th>Mobile number</th>
		                                <th>Action</th>
		                            </tr>
		                            </thead>
		                            <tbody>
		                            @foreach($Users as $user)
		                            <tr>
		                                <td>
		                                	{{$user->first_name}} {{$user->last_name}}
		                                	<a style="display: none;" href="{{url()}}/admin/users/view/{{$user->id}}">View</a>		                                
		                                </td>
		                                <td>{{ $user['email'] }}</td>
		                                <td>{{ $user['user_type'] }}</td>
		                                <td>{{ $user['mobile_no'] }}</td>
		                                <td>
		                                	<a id='deleteUserbutton' class="btn btn-danger btn-xs" onclick="deleteUser({{ $user['id'] }})"> <i class="Small material-icons" style="font-size:20px;" title="Delete">delete</i></a>
		                                </td>
		                                
		                            </tr>
		                            @endforeach 
		                            </tbody>
		                        </table>
		                    </div>
		                </div>
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
 
@stop