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
  $(document).on('change', '#franchiseEmailId', function () {
    var email = $('#franchiseEmailId').val();
    $('#user_mail_id').val(email);
  });
  $(document).on('click','.add-newFranchisee-btn', function(){
    $('.add-newFranchisee-btn').addClass('disabled');
    var firstName = $('#firstName').val();
    var lastName = $('#lastName').val();
    var franchiseAddress = $('#franchiseAddress').val();
    var franchiseEmailId = $('#franchiseEmailId').val();
    var legalEntity = $('#legalEntity').val();
    var invoiceCode = $('#invoiceCode').val();
    var franchiseePhno = $('#franchiseePhno').val();
    var default_birthday_price = $('#default_birthday_price').val();
    var member_birthday_price = $('#member_birthday_price').val();
    var default_advance_amount = $('#default_advance_amount').val();
    var additional_guest = $('#additional_guest').val();
    var additional_half_hour = $('#additional_half_hour').val();
    var base_price = $('#base_price').val();
    var pan_no = $('#pan_no').val();
    var service_tax_no = $('#service_tax_no').val();
    var tin_no = $('#tin_no').val();
    var annaul_membership = $('#annaul_membership').val();
    var lifetime_membership = $('#lifetime_membership').val();
    var cgst = $('#cgst').val();
    var sgst = $('#sgst').val();
    var user_mail_id = $('#user_mail_id').val();
    var password = $('#password').val();

      $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/createdNewFranchisee')}}",
        data: {
           'firstName': firstName, 
           'lastName': lastName, 
           'franchiseeAddress': franchiseAddress, 
           'franchiseeEmail': franchiseEmailId,
           'franchiseePhno': franchiseePhno,
           'legalEntity':legalEntity, 
           'invoiceCode':invoiceCode,
           'default_birthday_price': default_birthday_price,
           'member_birthday_price': member_birthday_price,
           'default_advance_amount': default_advance_amount,
           'additional_guest': additional_guest,
           'additional_half_hour': additional_half_hour,
           'base_price': base_price,
           'pan_no': pan_no,
           'service_tax_no': service_tax_no,
           'tin_no': tin_no,
           'annaul_membership': annaul_membership,
           'lifetime_membership': lifetime_membership,
           'cgst': cgst,
           'sgst': sgst,
           'user_mail_id': user_mail_id,
           'password': password
          },
        dataType: 'json',
        success: function(response){
          if(response.status === "success"){
            $('#NewFranchiseeMsgDiv').html("<p class='uk-alert uk-alert-success'>New franchisee has been created successfully.Please wait untill the page reloads</p>");
                $('#newFranchiseeLoading').show();
                setTimeout(function(){
                window.location.reload(1);
                }, 4000);
          } else {
            $('#NewFranchiseeMsgDiv').html("<p class='uk-alert uk-alert-warning'>New franchisee not yet created.Please try again.</P>");
          }
        }
      });
  });
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
<div id="newFranchiseeLoading" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:18px;">
    <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:13%;">
     New Franchisee added successfully.Please wait . . .
    </p>
</div>
<div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
    <div class="uk-width-medium-1-1">
        <div id="NewFranchiseeMsgDiv"></div>
        <h3>Add New Franchisee</h3>
        <div class="md-card">
            <div class="md-card-content">
              <div class="row">
                <div class="col-lg-6">
                  <h4 style="color:#d3d3de;float:right;">Franchisee Details</h4>
                </div>
              </div>
              <hr>
              {{ Form::open(array('url' => '/students/enrollYard', "class"=>"uk-form-stacked", 'method' => 'post')) }}
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">First Name * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('firstName',null,array('id'=>'firstName','class'=>'form-control','required'=>''))}}
                  </div>
                </div>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Last Name * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('lastName',null,array('id'=>'lastName','class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Franchisee Address :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('franchiseAddress',null,array('id'=>'franchiseAddress','class'=>'form-control'))}}
                  </div>
                </div>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Official Email Id * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('franchiseEmailId',null,array('id'=>'franchiseEmailId','class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Franchisee Mobile No :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('franchiseePhno',null,array('id'=>'franchiseePhno','class'=>'form-control'))}}
                  </div>
                </div>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Legal Entity Name :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('legalEntity',null,array('id'=>'legalEntity','class'=>'form-control'))}}
                  </div>
                </div>
              </div>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Invoice Code * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('invoiceCode',null,array('id'=>'invoiceCode','class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-lg-6">
                  <h4 style="color:#d3d3de;float:right;">Birthday Pricing</h4>
                </div>
              </div>
              <hr>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Default Birthday Price * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('default_birthday_price',0,array('id'=>'default_birthday_price','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Member Birthday Price * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('member_birthday_price',0,array('id'=>'member_birthday_price','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Default Advance Amount * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('default_advance_amount',0,array('id'=>'default_advance_amount','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Additional Guest Price * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('additional_guest',0,array('id'=>'additional_guest','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Additional Half An Hour Price * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('additional_half_hour',0,array('id'=>'additional_half_hour','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-lg-6">
                  <h4 style="color:#d3d3de;float:right;">Classes Base Pricing</h4>
                </div>
              </div>
              <hr>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Classes base price * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('base_price',0,array('id'=>'base_price','min'=> 0,'class'=>'form-control','required'=>''))}}(In Rs./-)
                  </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-lg-6">
                  <h4 style="color:#d3d3de;float:right;">Membership Pricing</h4>
                </div>
              </div>
              <hr>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Annual Membership * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('annaul_membership',0,array('id'=>'annaul_membership','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Lifetime Membership * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('lifetime_membership',0,array('id'=>'lifetime_membership','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-lg-6">
                  <h4 style="color:#d3d3de;float:right;">Invoice Data</h4>
                </div>
              </div>
              <hr>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">PAN Number :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('pan_no',null,array('id'=>'pan_no','class'=>'form-control','required'=>''))}}
                  </div>
                </div>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Service Tax Number :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('service_tax_no',null,array('id'=>'service_tax_no','class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Tin Number :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('tin_no',null,array('id'=>'tin_no','class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-lg-6">
                  <h4 style="color:#d3d3de;float:right;">Payment Tax</h4>
                </div>
              </div>
              <hr>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">CGST (%) * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('cgst',0,array('id'=>'cgst','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">SGST (%) * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::number('sgst',0,array('id'=>'sgst','min'=> 0,'class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <hr><hr>
              <div class="row">
                <div class="col-lg-6">
                  <h4 style="color:#d3d3de;float:right;">Admin Login</h4>
                </div>
              </div>
              <hr>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-3" style="text-align:right;padding-top:7px;">User Mail Id :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('user_mail_id',null,array('id'=>'user_mail_id','class'=>'form-control','required'=>'','readonly'))}}
                  </div>
                </div>
              </div>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-3" style="text-align:right;padding-top:7px;">Create Password * :</label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    {{Form::text('password',null,array('id'=>'password','class'=>'form-control','required'=>''))}}
                  </div>
                </div>
              </div>
              <div class="uk-grid" data-uk-grid-margin>
                <label class="uk-width-medium-1-3" style="text-align:right;padding-top:7px;"></label>
                <div class="uk-width-medium-1-4">
                  <div class="parsley-row form-group">
                    <button type="button" class="md-btn md-btn-primary add-newFranchisee-btn" style="border-radius:5px;">Add New Franchisee</button>
                  </div>
                </div>
              </div>
              {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@stop