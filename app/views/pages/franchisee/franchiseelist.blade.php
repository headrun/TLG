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
	$('.franchiseeEdit').click(function(){
		$('.fname').val($(this).parent().parent().children('td.name').html());
		$('.faddress').val($(this).parent().parent().children('td.address').html());
		$('.fcontactno').val($(this).parent().parent().children('td.phone').html());
		$('.femail').val($(this).parent().parent().children('td.email').html());
		$('.f_id').val($(this).parent().parent().children('td.id').html());
		$('.editmsg').html('');
	});
	$('.update').click(function(){
		$(this).addClass('disabled');
		$('.editmsg').html("<p class='uk-alert uk-alert-warning'>Please wait Updating....</p>");
		$.ajax({
			type: "POST",
			url: "{{URL::to('/quick/updateFranchisee')}}",
            data: {'franchisee_name':$('.fname').val(), 'franchisee_address':$('.faddress').val(),'ph_no':$('.fcontactno').val(),'email':$('.femail').val(),'franchisee_id':$('.f_id').val() },
			dataType: 'json',
			success: function(response){
				console.log(response);
				if(response.status==='success'){
					$('.update').removeClass('disabled');
					$('.editmsg').html("<p class='uk-alert uk-alert-success'>Updated Successfully...</p>");
					$('.uk-modal-close').click(function(){
						window.location.reload(1);
					});
					
				}else{
					$('.update').removeClass('disabled');
					$('.editmsg').html("<p class='uk-alert uk-alert-danger'>Try again Later...</p>");

				}
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
<br clear="all"/>


	<table class="uk-table">
	<thead>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Contact No</th>
            <th>E-mail</th>
            <th>Created-at</th>
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
            <td class="created_at">{{$franchisee->created_at}}</td>
            <td>
            	<button class="btn btn-warning btn-xs franchiseeEdit" type="button" data-uk-modal="{target:'#my-id'}" >Edit</button>
            </td>
		</tr>
        @endforeach
    </tbody>
    </table>

   <div class="pagination center-block text-center"> {{ $franchiseeList->links() }} </div>

   <div id="my-id" class="uk-modal">
        <div class="uk-modal-dialog ">
            <a class="uk-modal-close uk-close" id="deletecustomerclose"></a>
            <div class="uk-modal-header" style="/*border-bottom:solid 1px rgba(0,0,0,.12)*/">
                <h3 class="uk-modal-title">Edit</h3>
            </div>
            <div class="modaldata">
            	<form class="uk-form formfranchisee">
                <div class="editmsg"></div>
                <div class="uk-grid" data-uk-grid-margin="">
                <div class="uk-width-medium-1-2">
                	<label class="uk-form-label"> Name</label>
                	</br>
                	<input class="fname" type="text" placeholder="">
                	<input class="f_id" type="hidden">
                </div>
                <div class="uk-width-medium-1-2">
                	<label> Address</label>
                	</br>
                	<input class="faddress" type="text" placeholder="">
                </div>
                <div class="uk-width-medium-1-2">
                	<label > Contact No</label>
                	</br>
                	<input class="fcontactno" type="text" placeholder="">
                </div>
                <div class="uk-width-medium-1-2">
                	<label> Email</label>
                	</br>
                	<input class="femail" type="text" placeholder="">
                </div>
                </div>
                </form>
            </div>
            <div class="uk-modal-footer uk-text-right" style="/*border-top:solid 1px rgba(0,0,0,.12)*/">
            	<button type="button" class="md-btn md-btn-flat btn-warning update">Update</button>
            	<button type="button" class=" btn md-btn md-btn-flat uk-modal-close btn-primary">Close</button>
            </div>
        </div>
    </div>	                                    



@stop