@extends('layout.master')

@section('libraryCSS')
<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
<link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' />
@stop

@section('libraryJS')
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
<script src="{{url()}}/assets/js/pages/kendoui.min.js"></script>
<script>
    
   $("#deleteBatch").click(function (){
   	$.ajax({
			type: "POST",
			url: "{{URL::to('/quick/deleteBatch')}}",
            data: {'batch_id':$('#batchId').val()},
			dataType: 'json',
			success: function(response){
                            console.log(response);
                            if(response.status=='success'){
                              $('#DeleteMessageDiv').html('<p class="uk-alert uk-alert-success">Sucessfully deleted the batch.please wait till the page reloads </p>');
                              // $('#updateCustomerProfile').show();
                              setTimeout(function(){
							    window.location.reload(1);
							  }, 2000);
                            }else if(response.status=='classes'){
                            	$('#DeleteMessageDiv').html('<p class="uk-alert uk-alert-danger">Student classes contains this batch so cannot delete batch.</p>');
                            }else if(response.status=='failure'){
                            	$('#DeleteMessageDiv').html('<p class="uk-alert uk-alert-failure">cannot delete batch.Try again after some time</p>');
                            }
                        }
             });
})
   $("#deleteStudent").click(function (){
   	$.ajax({
			type: "POST",
			url: "{{URL::to('/quick/deleteStudent')}}",
            data: {'student_id':$('#studentId').val()},
			dataType: 'json',
			success: function(response){
                            console.log(response);
                            if(response.status=='success'){
                              $('#DeleteMessageDiv').html('<p class="uk-alert uk-alert-success">Sucessfully deleted the student.please wait till the page reloads </p>');
                              // $('#updateCustomerProfile').show();
                              setTimeout(function(){
							    window.location.reload(1);
							  }, 2000);
                            }else if(response.status=='failure'){
                            	$('#DeleteMessageDiv').html('<p class="uk-alert uk-alert-failure">cannot delete student.Try again after some time</p>');
                            }
                        }
             });
})
</script>
@stop


@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">Corrections</a></li>
		<li><a href="#" style="z-index:7;">Made Corrections</a></li>
	</ul>
</div>
<br clear="all"/>
     <!--      Batch Delete		 -->
<div id="DeleteMessageDiv"></div>
<div class="md-card">
	<div class="md-card-content">
		<div class="md-card-content">
			<div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-2">
					<div class="parsley-row form-group">
			         	<label for="batchId">Batch ID<span class="req">*</span></label>
			         	<input type="text" name="batch Id" id = "batchId">
			        </div>
                </div>
		        <div class="uk-width-medium-1-2">
		             <button type="button" class="btn btn-default" id = "deleteBatch">Delete</button>
		        </div>
            </div>	
		</div>		
	</div>
</div>

              <!-- Student Delete -->
<div class="md-card">
	<div class="md-card-content">
		<div class="md-card-content">
			<div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-2">
					<div class="parsley-row form-group">
			         	<label for="batchId">Student ID<span class="req">*</span></label>
			         	<input type="text" name="student Id" id = "studentId">
			        </div>
                </div>
		        <div class="uk-width-medium-1-2">
		             <button type="button" class="btn btn-default" id = "deleteStudent">Delete</button>
		        </div>
            </div>	
		</div>		
	</div>
</div>
@stop

