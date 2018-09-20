@extends('layout.master')

@section('libraryCSS')
	<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all"> -->
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' />
@stop

@section('libraryJS')
<script src="{{url()}}/assets/js/pages/validator.js"></script>
<script src="{{url()}}/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="{{url()}}/bower_components/datatables-colvis/js/dataTables.colVis.js"></script>
<script src="{{url()}}/bower_components/datatables-tabletools/js/dataTables.tableTools.js"></script>
<script src="{{url()}}/assets/js/custom/datatables_uikit.min.js"></script>
<script src="{{url()}}/assets/js/pages/plugins_datatables.min.js"></script>
<script src="{{url()}}/assets/js/kendoui_custom.min.js"></script>
<script src="{{url()}}/assets/js/pages/kendoui.min.js"></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
<script type="text/javascript">

$(document).ready(function (){
	$('#divLoading').hide();
	$("#email").on('keyup change input',function (){
	 if ($(this).val() != '') {
	   $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/checkUserExistance')}}",
            data: {'email':$(this).val()},
            dataType:"json",
            success: function (response)
            {
          	  	console.log(response);      	 	
    			if(response.existence == "exists"){
        			
    				$("#callbackMessage").html('<p class="uk-alert uk-alert-danger">The email '+$("#email").val()+'  has been used for another account. Please use another email.</p>');
    				$("#email").val("");
    			}else{
    				$("#callbackMessage").html("");
    			}
    			
    	     	
            }
          });
	 }
	});
});	
$('#customerSubmit').click(function(){
	$('#customerSubmit').disabled();
	// $('#divLoading').show();
});

</script>

@stop

@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="{{url()}}/admin/users" style="z-index:8;">Users</a></li>
		<li><a href="#" style="z-index:7;">Add</a></li>
	</ul>
</div>
<br clear="all"/>
<div class="">
	<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
	    <p style="position: absolute; color: White; top: 50%; left: 45%;font-size:20px;">
	    <img src="{{url()}}/assets/img/spinners/spinner.gif">
	     Loading, please wait...
	    </p>
    </div>
	<div class="row">
		<h4>New User</h4>
		 <div class="md-card">
                <div class="md-card-content large-padding">
                
                	@if(!$errors->isEmpty())
                	
                	<div class="uk-alert uk-alert-danger" data-uk-alert>
                    	<a href="#" class="uk-alert-close uk-close"></a>
                                {{$errors->first('courseName')}}
								{{$errors->first('masterCourse')}}
                    </div>
				    @endif	
			
				    @if (Session::has('msg'))
					  <div class="uk-alert uk-alert-success" data-uk-alert>
                      		 <a href="#" class="uk-alert-close uk-close"></a>
                             {{ Session::get('msg') }}
                      </div>
                      <br clear="all"/>
					@endif
					
					 @if (Session::has('error'))
					  <div class="uk-alert uk-alert-danger" data-uk-alert>
                      		 <a href="#" class="uk-alert-close uk-close"></a>
                             {{ Session::get('error') }}
                      </div>
                      <br clear="all"/>
					@endif
					
					<div id="callbackMessage"></div>
					<br clear="all"/>
					
					<?php $url = url().'/admin/users/add';?>
                    
                       {{ Form::open(array('files'=> true,'url' => $url, 'id'=>"addCustomerForm", "class"=>"uk-form-stacked", 'method' => 'post')) }} 
                        <div class="uk-grid" data-uk-grid-margin>
			             	<div class="uk-width-medium-1-2">
				                 <div class="parsley-row form-group">
				                 	<label for="firstName">First Name<span class="req">*</span></label>
				                 	{{Form::text('firstName', null,array('id'=>'firstName', 'required'=>'', 'class' => 'form-control input-sm md-input'))}}
				                 </div>
				            </div>
				            <div class="uk-width-medium-1-2">
				                 <div class="parsley-row form-group">
				                 	<label for="lastName">Last Name<span class="req">*</span></label>
				                 	{{Form::text('lastName', null,array('id'=>'lastName', 'required'=>'', 'class' => 'form-control input-sm md-input'))}}
				                 </div>
				            </div>
				            
				         </div>
				         <br clear="all"/><br clear="all"/>
				         <div class="uk-grid" data-uk-grid-margin>
				            <div class="uk-width-medium-1-2"> 
				                  <div class="parsley-row form-group">
				                 	<label for="email">User Email<span class="req">*</span></label>
				                 	{{Form::email('email', null,array('id'=>'email', 'required'=>'', 'class' => 'form-control input-sm md-input'))}}
				                 </div>
				            </div>    
				            <div class="uk-width-medium-1-2">    
				                  <div class="parsley-row form-group">
				                 	<label for="mobileNo">User Mobile number<span class="req">*</span></label>
				                 	
				                 	{{Form::text('mobileNo', null,array('id'=>'mobileNo', 'required'=>'', "onkeypress"=>"return isNumberKey(event);", 'maxlength'=>'10',  'minlength'=>'10', 'pattern'=>'\d*',   'class' => 'form-control input-sm md-input','style'=>'padding:0px'))}}
				                 </div>
				            </div>  
				            <br clear="all"/><br clear="all"/><br clear="all"/>
				        </div>  
				        <br clear="all"/><br clear="all"/>
				         <div class="uk-grid" data-uk-grid-margin>
				            <div class="uk-width-medium-1-2">    
				                  <div class="parsley-row form-group">				                 	
				                 	{{ Form::select('userType', array('' => 'Please select a User type','INSTRUCTOR'=>'Instructor','RECEPTIONIST'=>'Receptionist'), null ,array('id'=>'userType', 'class' => 'input-sm md-input',  'required'=>'', "placeholder"=>"User type", "style"=>'padding:0px; font-weight:bold;color: #727272;')) }}
				                 </div>
				            </div>  
				            <br clear="all"/><br clear="all"/><br clear="all"/>
				        </div>    
				        
				          
			            
                        <div class="uk-grid">
                            <div class="uk-width-1-1">
                            	
                                <button type="submit" id="customerSubmit"  onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" onSubmit="window.location.reload();" class="md-btn md-btn-primary">Save User Details</button>
                            </div>
                        </div>
                    {{ Form::close() }}	
                </div>
            </div>
            
            
            
		
		
		
		
		
	</div><!-- row -->
</div><!-- Container -->


 
@stop
