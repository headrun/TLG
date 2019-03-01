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
    
   $("#updateEndDates").click(function (){
   	$.ajax({
			type: "POST",
			url: "{{URL::to('/quick/updateEndDates')}}",
            data: {},
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
		<li><a href="#" style="z-index:7;">EndDates Corrections</a></li>
	</ul>
</div>
<br>
<br>
<br>
<br clear="all"/>
     <!--      Batch Delete		 -->
<div id="DeleteMessageDiv"></div>

              <!-- Student Delete -->
<!-- <div class="md-card"> -->
	<!-- <div class="md-card-content"> -->
		<!-- <div class="md-card-content"> -->
		<div class="col-md-12" >
			<div class="uk-grid" data-uk-grid-margin>
		        <div>
		             <button type="button" class="md-btn md-btn-primary pull-right"  style = "margin-right: 3em" id = "updateEndDates">Update Enrolled Students - End Dates</button>
		        </div>
            </div>
            </div>	
		<!-- </div>		 -->
	<!-- </div> -->
<!-- </div> -->
@stop

