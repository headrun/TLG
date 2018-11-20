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
            url: "{{URL::to('/quick/getTermsAndCondForFranchisee')}}",
            data: {'franchisee_id':franchisee_id},
            dataType: 'json',
            success: function(response){
              if (response.status === 'success') {
                $('#franchisee_id').val(franchisee_id);
                $('#editor').val(response.franchisee_data[0]['terms_conditions']);
              }
            }
        });
        $('#NewFranchiseeMsgDiv').html('');
    });

    $('#updateFranchiseeTerms').on('click', function () {
        var franchisee_id = $('#franchisee_id').val();
        var terms_conditions = $('#editor').val();

        $.ajax({
          type: "POST",
          url: "{{URL::to('/quick/updateTermsAndCondtions')}}",
          data: {
             'franchisee_id': franchisee_id,
             'terms_conditions': terms_conditions
            },
          dataType: 'json',
          success: function(response){
            if(response.status === "success"){
              $('#updateFranchiseeTerms').addClass('disabled');  
              $('#NewFranchiseeMsgDiv').html("<p class='uk-alert uk-alert-success'>Terms & Conditions has been updated successfully.Please wait untill the page reloads</p>");
                  $('#modaldata').modal('hide');
                  // $('#newFranchiseeLoading').show();
                  setTimeout(function(){
                  window.location.reload(1);
                  }, 4000);
            } else {
              $('#NewFranchiseeMsgDiv').html("<p class='uk-alert uk-alert-warning'>Please try again.</P>");
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
                        <h3>Update Terms & Conditions</h3>
                        <div id="franchisee_id" style="dispaly:none;"></div>
                        <div class="row">
                          <div class="md-card">
                            <div class="md-card-content">
                              <textarea id="editor" name="editor" value="" rows="20" cols="105"></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="row" style="padding-top:20px;">
                         <div class="col-lg-6" style="text-align:right;">
                          <button type="button" class="md-btn md-btn-flat btn-warning updateFranchisee" id="updateFranchiseeTerms" style="border-radius:5px;">Update</button>
                         </div>
                         <div class="col-lg-6">
                          <button type="button" class=" btn md-btn md-btn-flat uk-modal-close btn-primary" style="border-radius:5px;">Close</button>
                         </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>	                                    



@stop