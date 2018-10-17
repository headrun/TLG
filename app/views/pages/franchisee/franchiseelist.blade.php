@extends('layout.master')

@section('libraryCSS')
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

	$(document).on('click', '.franchiseeEdit', function() {
        var franchisee_id = $(this).parent().parent().find('.id').text();
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/getDataForFranchisee')}}",
            data: {'franchisee_id':franchisee_id},
            dataType: 'json',
            success: function(response){
              if (response.status === 'success') {
                $('#franchisee_id').val(franchisee_id);
                $('#franchiseeName').val(response.franchisee_data[0]['franchisee_name']);
                $('#franchiseAddress').val(response.franchisee_data[0]['franchisee_address']);
                $('#franchiseEmailId').val(response.franchisee_data[0]['franchisee_official_email']);
                $('#franchiseePhno').val(response.franchisee_data[0]['franchisee_phone']);
                $('#invoiceCode').val(response.franchisee_data[0]['invoice_code']);
                $('#default_birthday_price').val(response.bday_data[0]['default_birthday_price']);
                $('#member_birthday_price').val(response.bday_data[0]['member_birthday_price']);
                $('#default_advance_amount').val(response.bday_data[0]['default_advance_amount']);
                $('#additional_guest').val(response.bday_data[0]['additional_guest']);
                $('#additional_half_hour').val(response.bday_data[0]['additional_half_hour']);
                $('#base_price').val(response.class_base_price[0]['base_price']);
                $('#pan_no').val(response.invoice_data[0]['pan_no']);
                $('#service_tax_no').val(response.invoice_data[0]['service_tax_no']);
                $('#tin_no').val(response.invoice_data[0]['tin_no']);
                $('#annaul_membership').val(response.annual[0]['fee_amount']);
                $('#lifetime_membership').val(response.lifetime[0]['fee_amount']);
                $('#cgst').val(response.cgst[0]['tax_percentage']);
                $('#sgst').val(response.sgst[0]['tax_percentage']);
                $('#legalEntity').val(response.invoice_data[0]['legal_entry_name']);
              }
            }
        });
        $('#NewFranchiseeMsgDiv').html('');
    });

    $('#updateFranchisee').on('click', function () {
        var franchisee_id = $('#franchisee_id').val();
        var franchisee_name = $('#franchiseeName').val();
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

        $.ajax({
          type: "POST",
          url: "{{URL::to('/quick/updateFranchiseeDetails')}}",
          data: {
             'franchisee_id': franchisee_id,
             'franchisee_name': franchisee_name, 
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
             'sgst': sgst
            },
          dataType: 'json',
          success: function(response){
            if(response.status === "success"){
              $('.updateFranchisee').addClass('disabled');  
              $('#NewFranchiseeMsgDiv').html("<p class='uk-alert uk-alert-success'>Franchisee has been updated successfully.Please wait untill the page reloads</p>");
                  $('#modaldata').modal('hide');
                  // $('#newFranchiseeLoading').show();
                  setTimeout(function(){
                  window.location.reload(1);
                  }, 4000);
            } else {
              $('#NewFranchiseeMsgDiv').html("<p class='uk-alert uk-alert-warning'>New franchisee not yet created.Please try again.</P>");
            }
          }
        });
    })

	$('.update').click(function(){
		$(this).addClass('disabled');
		$('.editmsg').html("<p class='uk-alert uk-alert-warning'>Please wait Updating....</p>");
		$.ajax({
			type: "POST",
			url: "{{URL::to('/quick/updateFranchisee')}}",
            data: {'franchisee_name':$('.fname').val(), 'franchisee_address':$('.faddress').val(),'ph_no':$('.fcontactno').val(),'email':$('.femail').val(),'franchisee_id':$('.f_id').val() },
			dataType: 'json',
			success: function(response){
				if(response.status==='success'){
					$('.update').removeClass('disabled');
					$('.editmsg').html("<p class='uk-alert uk-alert-success'>Updated Successfully...</p>");
					$('.uk-modal-close').click(function(){
						window.location.reload(1);
					});
					
				}
					$('.update').removeClass('disabled');
					$('.editmsg').html("<p class='uk-alert uk-alert-danger'>Try again Later...</p>");

			}
        });  
	});
</script>>
@stop

@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">Franchisee</a></li>
		<li><a href="#" style="z-index:7;">Franchisee List</a></li>
	</ul>
</div>
<div id="newFranchiseeLoading" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:18px;">
    <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:13%;">
     Franchisee updated successfully.Please wait . . .
    </p>
</div>
<br clear="all"/>


	<table class="uk-table">
	<thead>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Contact No</th>
            <th>E-mail</th>
            <!-- <th>Created-at</th> -->
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    	@foreach ($franchiseeList as $franchisee)
        <tr>
        	<td class="id" style="display:none">{{$franchisee->id}}</td>
            <td class="name">{{$franchisee->franchisee_name}}</td>
            <td class="address">{{$franchisee->franchisee_address}}</td>
            <td class="phone">{{$franchisee->franchisee_phone}}</td>
            <td class="email">{{$franchisee->franchisee_official_email}}</td>
            <td class="created_at" style="display:none">{{$franchisee->created_at}}</td>
            <td>
            	<button class="btn btn-warning btn-xs franchiseeEdit" type="button" data-uk-modal="{target:'#my-id'}" style=" color:black" ><i class="Small material-icons">mode_edit</i></button>
            </td>
		</tr>
        @endforeach
    </tbody>
    </table>

   <div class="pagination center-block text-center"> {{ $franchiseeList->links() }} </div>

   <div id="my-id" class="uk-modal">
        <div class="uk-modal-dialog" style="width:70%;">
            <a class="uk-modal-close uk-close" id="deletecustomerclose"></a>
            <div class="modaldata">
            	<div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                    <div class="uk-width-medium-1-1">
                        <div id="NewFranchiseeMsgDiv"></div>
                        <h3>Edit Franchisee</h3>
                        <div id="franchisee_id" style="dispaly:none;"></div>
                        <div class="md-card">
                            <div class="md-card-content">
                              <div class="row">
                                <center>
                                  <h4 style="color:#d3d3de;">Franchisee Details</h4>
                                </center>
                              </div>
                              <hr>
                              {{ Form::open(array('url' => '/students/enrollYard', "class"=>"uk-form-stacked", 'method' => 'post')) }}
                              <div class="uk-grid" data-uk-grid-margin>
                                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Franchisee Name * :</label>
                                <div class="uk-width-medium-1-4">
                                  <div class="parsley-row form-group">
                                    {{Form::text('franchiseeName',null,array('id'=>'franchiseeName','class'=>'form-control','required'=>''))}}
                                  </div>
                                </div>
                                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Franchisee Address :</label>
                                <div class="uk-width-medium-1-4">
                                  <div class="parsley-row form-group">
                                    {{Form::text('franchiseAddress',null,array('id'=>'franchiseAddress','class'=>'form-control'))}}
                                  </div>
                                </div>
                              </div>
                              <div class="uk-grid" data-uk-grid-margin>                                
                                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Official Email Id * :</label>
                                <div class="uk-width-medium-1-4">
                                  <div class="parsley-row form-group">
                                    {{Form::text('franchiseEmailId',null,array('id'=>'franchiseEmailId','class'=>'form-control','required'=>'', 'readonly'))}}
                                  </div>
                                </div>
                                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Franchisee Mobile No :</label>
                                <div class="uk-width-medium-1-4">
                                  <div class="parsley-row form-group">
                                    {{Form::number('franchiseePhno',null,array('id'=>'franchiseePhno','class'=>'form-control'))}}
                                  </div>
                                </div>
                              </div>
                              <div class="uk-grid" data-uk-grid-margin>
                                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Legal Entity Name :</label>
                                <div class="uk-width-medium-1-4">
                                  <div class="parsley-row form-group">
                                    {{Form::text('legalEntity',null,array('id'=>'legalEntity','class'=>'form-control'))}}
                                  </div>
                                </div>
                                <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Invoice Code * :</label>
                                <div class="uk-width-medium-1-4">
                                  <div class="parsley-row form-group">
                                    {{Form::text('invoiceCode',null,array('id'=>'invoiceCode','class'=>'form-control','required'=>''))}}
                                  </div>
                                </div>
                              </div>
                              <hr>
                              <div class="row">
                                <center>
                                  <h4 style="color:#d3d3de;">Birthday Pricing</h4>
                                </center>
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
                                <center>
                                  <h4 style="color:#d3d3de;">Classes Base Pricing</h4>
                                </center>
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
                                <center>
                                  <h4 style="color:#d3d3de;">Membership Pricing</h4>
                                </center>
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
                                <center>
                                  <h4 style="color:#d3d3de;">Invoice Data</h4>
                                </center>
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
                                <center>
                                  <h4 style="color:#d3d3de;">Payment Tax</h4>
                                </center>
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
                              <div class="row" style="padding-top:20px;">
                               <div class="col-lg-6" style="text-align:right;">
                                <button type="button" class="md-btn md-btn-flat btn-warning updateFranchisee" id="updateFranchisee" style="border-radius:5px;">Update</button>
                               </div>
                               <div class="col-lg-6">
                                <button type="button" class=" btn md-btn md-btn-flat uk-modal-close btn-primary" style="border-radius:5px;">Close</button>
                               </div>
                              </div>
                              <!-- <div class="uk-grid" data-uk-grid-margin>
                                <div
                                <label class="uk-width-medium-1-3" style="text-align:right;padding-top:7px;"></label>
                                <div class="uk-width-medium-1-4">
                                  <div class="parsley-row form-group">
                                    <button type="button" class="md-btn md-btn-flat btn-warning update" style="border-radius:5px;">Update</button>
                                  </div>
                                </div>
                                <label class="uk-width-medium-1-3" style="text-align:right;padding-top:7px;"></label>
                                <div class="uk-width-medium-1-4">
                                  <div class="parsley-row form-group">
                                    <button type="button" class=" btn md-btn md-btn-flat uk-modal-close btn-primary" style="border-radius:5px;">Close</button>
                                  </div>
                                </div>
                              </div> -->
                              {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>	                                    



@stop