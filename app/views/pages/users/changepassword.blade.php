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
    
    function changepassword(userid){
        $('.msg').empty();
        $('#password').val('');
        $('#myModal').modal('show');
        $('#update').click(function(){
            if($('#password').val() != ''){
                $('.msg').html('<p class="uk-alert uk-alert-warning uk-text-small"> Please wait processing ...</p>');
                $.ajax({
			type: "POST",
			url: "{{URL::to('/quick/updatepassword')}}",
                        data: {'password':$('#password').val(),'user_id':userid},
			dataType: 'json',
			success: function(response){
                            setTimeout(function(){
                                  $('.msg').html('<p class="uk-alert uk-alert-success uk-text-small">Success.</p>')
                            }, 1000);
                        }
                });
            }else{
                $('.msg').html('<p class="uk-alert uk-alert-warning uk-text-small"> Please enter password to update</p>');
            }
        });
    }
</script>
@stop


@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">Settings</a></li>
		<li><a href="#" style="z-index:7;">Change Password</a></li>
	</ul>
</div>
<br clear="all"/>
<div class="md-card">
    <div class="md-card-content">
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-1">
                <div class="parsley-row">
                    <table  class="uk-table">
                        <thead>
                            <tr>
                                <td>Name</td>
                                <td>e-mail</td>
                                <td>User Type</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                         @foreach($users_data as $user)
                         <tr>
                             <td>{{$user->first_name.$user->last_name}}</td>
                             <td>{{$user->email}}</td>
                             <td>{{$user->user_type}}</td>
                             <td><button class="btn btn-warning btn-xs" onclick="changepassword({{$user->id}})">Change Password</button></td>
                         </tr>
                         @endforeach
                        </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>

  <!-- Modal -->
  <div class="modal  fade" id="myModal" role="dialog" style="margin-top: 50px; z-index: 99999;">
    <div class="modal-dialog modal-sm">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Change Password</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="msg">
                    &nbsp;
                </div>
            <input type="password" class="center-block input-medium" placeholder="New Password Here" name="password" id="password" />
            
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary " id="update">Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
      
    </div>
  </div>				


@stop
