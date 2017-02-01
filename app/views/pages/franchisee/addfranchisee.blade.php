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
<script>
	// for adding new franchisee
	$('.addFranchisee').click(function(){
		
		$('.addFranchisee').addClass('disabled');
		$('.addFranchiseeMsg').html("<p class='uk-alert uk-alert-warning'> please wait adding Franchisee...</p>");
		$.ajax({
			type: "POST",
			url: "{{URL::to('/quick/addFranchisee')}}",
            data: $('.addFranchiseeForm').serialize(),
			dataType: 'json',
			success: function(response){
                console.log(response);
                if(response.status==="success"){
                	$('.addFranchisee').removeClass('disabled');
                	$('.addFranchiseeMsg').html("<p class='uk-alert uk-alert-success'>New Franchisee Added... Please Wait till Page Reloads.</p>");

                	setTimeout(function(){
					   window.location.reload(1);
					}, 3200);


                }else{
                	$('.addFranchisee').removeClass('disabled');
                	$('.addFranchiseeMsg').html("<p class='uk-alert uk-alert-success'>Error... Please Try again later.</p>");
            	}
                
            }
         });
	});

	$('.addAdmin').click(function(){
		$('.addAdmin').addClass('disabled');
		$('.adminUsermsg').html('<p class="uk-alert uk-alert-warning">Please wait adding Admin User....</p>');
		$.ajax({
			type: "POST",
			url: "{{URL::to('/quick/addAdminUser')}}",
            data: $('.addadminForm').serialize(),
			dataType: 'json',
			success: function(response){
				if(response.status==='success'){
					$('.addAdmin').removeClass('disabled');
					$('.adminUsermsg').html('<p class="uk-alert uk-alert-success">Admin User Added Successfully.</p>');	
					$('.addadminForm')[0].reset();

                	setTimeout(function(){
					   $('.adminUsermsg').html('');
					}, 3200);			

				}else{
					$('.addAdmin').removeClass('disabled');
					$('.adminUsermsg').html("<p class='uk-alert uk-alert-danger'> Unable to Add Admin User... Try again later.</p>");

                	setTimeout(function(){
					   $('.adminUsermsg').html('');
					}, 3200);
                }
           	}
        });
		console.log($('.addadminForm').serialize());
	});

</script>

@stop

@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">Franchisee</a></li>
		<li><a href="#" style="z-index:7;">Add Franchisee</a></li>
	</ul>
</div>
<br clear="all"/>
<br clear="all"/>

<div class="md-card-content large-padding">
	<ul class="uk-tab " data-uk-tab={connect:'#tab-content'}>
    	<li class="uk-active"><a href=""><i class="uk-icon-plus"></i> &nbsp; Franchisee</a></li>
    	<li><a href=""><i class="uk-icon-user-plus"></i> &nbsp; Admin</a></li>
    	<li><a href=""><i class="uk-icon-plus-square"></i>&nbsp; Courses</a></li>
    	<li><a href=""><i class="uk-icon-plus-circle"></i>&nbsp; Base Price</a></li>
    	<li><a href=""><i class="uk-icon-plus-square-o"></i>&nbsp; Classes</a></li>
    	<li><a href=""><i class="uk-icon-plus"></i>&nbsp; Tax Details</a></li>
    </ul>
	<ul id="tab-content" class="uk-switcher uk-margin">
        <li class="uk-active" aria-hidden="false">
        	<!-- Add Franchisee-->
        	<br clear="all">
        	<div class="addFranchiseeMsg"></div>
        	<form class="uk-form addFranchiseeForm" name="addFranchiseeForm" method="post" action="">
        	<div class="uk-grid" data-uk-grid-margin >
        		
   				<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="franchiseeName">Name<span class="req">*</span></label>
   						<input type="text" name="franchiseeName" class="form-control input-sm md-input franchiseeName"  id="franchiseeName" style='padding:0px' required>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="franchiseeEmail">e-mail <span class="req">*</span></label>
   						<input type="text" name="franchiseeEmail" class="form-control input-sm md-input franchiseeEmail"  id="franchiseeEmail" style='padding:0px' required>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="franchiseePhno">Contact No <span class="req">*</span></label>
   						<input type="text" name="franchiseePhno" class="form-control input-sm md-input franchiseePhno"  id="franchiseePhno" style='padding:0px' required>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-1"> 
   					<div class="parsley-row form-group">
   						<label for="franchiseeAddress">Address <span class="req">*</span></label>
   						<input type="text" name="franchiseeAddress" class="form-control input-sm md-input franchiseeAddress"  id="franchiseeAddress" style='padding:0px' required>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-1"> 
   					<div class="parsley-row form-group">
   						<button type="button" class="btn btn-sm btn-primary addFranchisee" name="addFranchisee" style="float:left">Add Franchisee</button>
   					</div>	
    			</div>
								
			</div>
			</form>
        </li>
        <li aria-hidden="true" class="">
        	<!-- Add Admin User-->
        	<div class="adminUsermsg"></div>
        	<br clear="all">
			<form class="uk-form addadminForm" name="addadminForm" method="post" action="">
        	<div class="uk-grid" data-uk-grid-margin >
        		<div class="uk-width-medium-1-3"> 
        			<div class="parsley-row form-group"></div>
   				</div>
   				<div class="uk-width-medium-1-3">
   					<div class="parsley-row form-group"></div> 
   				</div>
   				<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="FName">Select Franchisee <span class="req">*</span></label>
   						<select  class="form-group FName  input-sm md-input" id="FName" name="FName" style="padding:0px;width:100%">
   							@foreach($franchiseelist as $franchisee) 
   								<option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
   							@endforeach
   						</select>
   					</div>

   				</div>
   				<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="AdminFirstName">First Name <span class="req">*</span></label>
   						<input type="text" name="AdminFirstName" class="form-control input-sm md-input AdminFirstName"  id="AdminFirstName" style='padding:0px' required>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="AdminLastName">Last Name <span class="req">*</span></label>
   						<input type="text" class="form-control input-sm md-input AdminLastName" name="AdminLastName" id="AdminLastName" style='padding:0px' required>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group" >
   						<label for="AdminEmail">E-mail<span class="req">*</span></label>
   						<input type="text" name="AdminEmail" class="form-control input-sm md-input AdminEmail"  id="AdminEmail" style='padding:0px' required>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="AdminMobileNo">Mobile No <span class="req">*</span></label>
   						<input type="text" class="form-control input-sm md-input AdminMobileNo" name="AdminMobileNo"  id="AdminMobileNo" style='padding:0px' required>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="AdminAltMobileNo">Alt Mobile No</label>
   						<input type="text" class="form-control input-sm md-input AdminAltMobileNo" name="AdminAltMobileNo" id="AdminAltMobileNo" style='padding:0px'>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group">
   						<label for="AdminLandlineNo">Landline No</label>
   						<input type="text" class="form-control input-sm md-input AdminLandlineNo"  id="AdminLandlineMobileNo" name="AdminLandlineMobileNo" style='padding:0px'>
   					</div>	
    			</div>
    			<div class="uk-width-medium-1-1"> 
   					<div class="parsley-row form-group">
   						<button type="button" class="btn btn-sm btn-primary addAdmin" id="addAdmin" style="float:left">Add Admin</button>
   					</div>	
    			</div>


    		</div>
    		</form>
        </li>
        <li aria-hidden="true" class="">
        	<!-- Add Courses-->
        	<br clear="all" />
        	<form class="uk-form" method="post" action="">
        	<div class="uk-grid" data-uk-grid-margin >
        		<div class="uk-width-medium-1-3"> 
        			<div class="parsley-row form-group">
        				<label for="courseFName">Select Franchisee</label>
   						<select  class="form-group courseFName  input-sm md-input" id="courseFName" style="padding:0px;width:100%">
   							@foreach($franchiseelist as $franchisee) 
   								<option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
   							@endforeach
   						</select>
        			</div>
   				</div>
   				<div class="uk-width-medium-1-3">
   					<div class="parsley-row form-group"></div> 
   				</div>
   				<div class="uk-width-medium-1-3"> 
   					<div class="parsley-row form-group"></div>
				</div>
				@foreach($courseList  as $course)
				<div class="uk-width-medium-1-3">
   					<div class="parsley-row form-group">
   						<input id="{{$course->id}}" name="{{$course->id}}" value="yes" type="checkbox" class="checkbox-custom">
        			<label for="{{$course->id}}" class="checkbox-custom-label">{{$course->course_name}}</label>
   					</div> 
   				</div>
   				@endforeach
   				<div class="uk-width-medium-1-1">
   					<div class="parsley-row form-group">
   						<button type="submit" class="btn btn-sm btn-primary addCourse" style="float:left">Add Courses</button>
   						
   					</div> 
   				</div>

   			</div>
   			</form>
        	
        </li>
        <li aria-hidden="true" class="">
        	<!-- Base price for classes and Birthday -->
        	<ul class="uk-subnav uk-subnav-pill" data-uk-switcher="{connect:'#baseprice-id'}">
        		<li class="uk-active" aria-hidden="false"><a href="">Classes</a></li>
				<li class="" aria-hidden="true"><a href="">Birthday</a></li>
        	</ul>	
        	<ul id="baseprice-id" class="uk-switcher uk-margin">
        		<li class="uk-active" aria-hidden="false">
        			<!-- Base price for Classes -->
        			<br clear="all" />
        			<form class="uk-form" method="post" action="">
        			<div class="uk-grid" data-uk-grid-margin >
        				<div class="uk-width-medium-1-3">
        					<div class="parsley-row form-group">
        						<label for="courseFName">Select Franchisee</label>
   								<select  class="form-group courseFName  input-sm md-input" id="courseFName" style="padding:0px;width:100%">
   								@foreach($franchiseelist as $franchisee) 
   									<option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
   								@endforeach
   								</select>
        					</div>
        				</div>
        				<div class="uk-width-medium-1-3">
        					<div class="parsley-row form-group">
        						<label for="classBasePrice">Class Base Price</label>
   								<input type="text" class="form-control input-sm md-input classBasePrice"  id="classBasePrice" required style='padding:0px'>
        					</div>
        				</div> 
        				<div class="uk-width-medium-1-3"></div>
        				<div class="uk-width-medium-1-1">
        					<button type="submit" class="btn btn-sm btn-primary classBasepriceSubmit" id="classBasepriceSubmit" style="float:left">Add Base Price</button>
   						</div>
   					</div>
   					</form>
        		</li>
        		<li class="" aria-hidden="true">
        			<!-- Base price for Birthday -->
        			<br clear="all" />
        			<form class="uk-form" method="post" action="">
        			<div class="uk-grid" data-uk-grid-margin >
        				<div class="uk-width-medium-1-3">
        					<div class="parsley-row form-group">
        						<label for="birthdayBasePricefranchisee">Select Franchisee</label>
   								<select  class="form-group  input-sm md-input birthdayBasePricefranchisee" id="birthdayBasePricefranchisee" style="padding:0px;width:100%">
   								@foreach($franchiseelist as $franchisee) 
   									<option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
   								@endforeach
   								</select>
        					</div>
        				</div>
        				<div class="uk-width-medium-1-3">
        					<div class="parsley-row form-group">
        						<label for="birthdayBasePrice">Birthday Base Price</label>
   								<input type="text" class="form-control input-sm md-input birthdayBasePrice"  id="birthdayBasePrice" required style='padding:0px'>
        					</div>
        				</div>
        				<div class="uk-width-medium-1-3"></div>
        				<div class="uk-width-medium-1-1">
        					<button type="submit" class="btn btn-sm btn-primary birthdayBasepriceSubmit" id="birthdayBasepriceSubmit" style="float:left">Add Base Price</button>
   						</div>
   					</div>
   					</form>
        		</li>

        	</ul>
        </li>
        <li aria-hidden="true" class="">
        	<!-- classes -->
        	<br clear="all" />
        		<form class="uk-form" method="post" action="">
        			<div class="uk-grid" data-uk-grid-margin >
        				<div class="uk-width-medium-1-3">
        					<div class="parsley-row form-group">
        						<label for="addClassFranchisee">Select Franchisee</label>
   								<select  class="form-group  input-sm md-input addClassFranchisee" id="addClassFranchisee" style="padding:0px;width:100%">
   								@foreach($franchiseelist as $franchisee) 
   									<option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
   								@endforeach
   								</select>
        					</div>
        				</div>
        				<div class="uk-width-medium-1-3">
        					<div class="parsley-row form-group">
        					<label for="addClassCourses">Select Course</label>
        					<select  class="form-group  input-sm md-input addClassCourses" id="addClassCourses" style="padding:0px;width:100%">
        						@foreach($courseList  as $course)
        						  <option id="{{$course->id}}">{{$course->course_name}}</option>
        						@endforeach
        					</select>
        					</div>
        				</div>
        				<div class="uk-width-medium-1-3">
        					<div class="parsley-row form-group">
        					
        					</div>
        				</div>
        				<div class="uk-width-medium-1-1">
        					<div class="parsley-row form-group">
        						<button type="submit" class="btn btn-sm btn-primary addClassesSubmit" id="addClassesSubmit" style="float:left">Add Classes</button>	
        					</div>
        				</div>
					</div>
        		</form>
        			
        	
        </li>
        <li aria-hidden="true" class="">
        	<!-- Tax Details -->
        	<ul class="uk-subnav uk-subnav-pill" data-uk-switcher="{connect:'#tax-id'}">
        		<li class="uk-active" aria-hidden="false"><a href="">Tax Percentage</a></li>
				<li class="" aria-hidden="true"><a href="">Tax Particular</a></li>
        	</ul>	
        	<ul id="tax-id" class="uk-switcher uk-margin">
        		<li class="uk-active" aria-hidden="false">
        			<!--tax percentage -->
        			<br clear="all" />
        			<form class="uk-form" method="post" action="">
        				<div class="uk-grid" data-uk-grid-margin >
        					<div class="uk-width-medium-1-3">
        						<div class="parsley-row form-group">
        							<label for="addtaxpercentageFranchisee">Select Franchisee</label>
   									<select  class="form-group  input-sm md-input addtaxpercentageFranchisee" id="addtaxpercentageFranchisee" style="padding:0px;width:100%">
   										@foreach($franchiseelist as $franchisee) 
   										<option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
   									@endforeach
   									</select>
        						</div>
        					</div>
        					<div class="uk-width-medium-1-3">
        						<div class="parsley-row form-group">
        							<label for="taxpercentage">Tax Percentage</label>
        							<input type="text" class="form-control input-sm md-input taxpercentage"  id="taxpercentage" required style='padding:0px'>
        						</div>
        					</div>
        					<div class="uk-width-medium-1-3">
        						<div class="parsley-row form-group">
        					
        						</div>
        					</div>
        					<div class="uk-width-medium-1-1">
        						<div class="parsley-row form-group">
        							<button type="submit" class="btn btn-sm btn-primary addTaxSubmit" id="addTaxSubmit" style="float:left">Add Tax</button>	
        						</div>
        					</div>
						</div>
        			</form>
        		</li>
        		<li class="" aria-hidden="true">
        			<!-- tax particular -->
        			<br clear="all" />
        			<form class="uk-form" method="post" action="">
        				<div class="uk-grid" data-uk-grid-margin >
        					<div class="uk-width-medium-1-3">
        						<div class="parsley-row form-group">
        							<label for="addtaxparticularFranchisee">Select Franchisee</label>
   									<select  class="form-group  input-sm md-input addtaxparticularFranchisee" id="addtaxparticularFranchisee" style="padding:0px;width:100%">
   										@foreach($franchiseelist as $franchisee) 
   										<option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
   									@endforeach
   									</select>
        						</div>
        					</div>
        					<div class="uk-width-medium-1-3">
        						<div class="parsley-row form-group">
        							<label for="taxParticularName">Particular</label>
        							<input type="text" class="form-control input-sm md-input taxParticularName"  id="taxParticularName" required style='padding:0px'>
        						</div>
        					</div>
        					<div class="uk-width-medium-1-3">
        						<div class="parsley-row form-group">
        							<label for="taxParticularPercentage">Percentage</label>
        							<input type="text" class="form-control input-sm md-input taxParticularPercentage"  id="taxParticularPercentage" required style='padding:0px'>
        						</div>
        					</div>
        					<div class="uk-width-medium-1-1">
        						<div class="parsley-row form-group">
        							<button type="submit" class="btn btn-sm btn-primary addTaxParticularSubmit" id="addTaxParticularSubmit" style="float:left">Add Tax Particular</button>	
        						</div>
        					</div>
						</div>
        			</form>
        		</li>
        	</ul>
        	
        </li>
    </ul>

</div>


@stop