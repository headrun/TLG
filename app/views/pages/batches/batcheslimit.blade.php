@extends('layout.master')
@section('libraryCSS')
<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all"> -->
<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
<link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
$("#batchLimitTable").DataTable();
function editbatcheslimit(batchLimitId, batchlimitrecep, batchlimitadmin){
        $('#editBatchesLimit_id').val(batchLimitId);
        $('#batch_limit_recep_modal').val(batchlimitrecep);
        $('#batch_limit_admin_modal').val(batchlimitadmin);
        $('#editBatchesLimitmodal').modal('show');
}

$('#savebatcheslimitedit').click(function(){
//    alert($('#editBatchesLimit_id').val());
    $.ajax({
			type: "POST",
			url: "{{URL::to('/quick/editBatchLimitByBatchId')}}",
                        data: {'batchlimit_id':$('#editBatchesLimit_id').val(),
                               'batchlimit_recep':$('#batch_limit_recep_modal').val(),
                               'batchlimit_admin':$('#batch_limit_admin_modal').val(),
                              },
			dataType: 'json',
			success: function(response){
                            if(response.status=='success'){
                                $('#batchEditMsg').html("<p class='uk-alert uk-alert-success'>updated successfully. please wait till page reloads.</p>");
                                $('#editBatchesLimitmodal').modal('hide');
                                $('#editBatchLimit').show();
                                setTimeout(function(){
                				   window.location.reload(1);
                				}, 3000);
                            }
                        }
          });  
});

function deletebatcheslimit(batchLimitId){
//    alert(batchLimitId);
    $('#deleteBatchesLimit_id').val(batchLimitId);
    $('#deletebatcheslimit').modal('show');
}

$('#batcheslimit_delete').click(function(){
//        alert($('#deleteBatchesLimit_id').val());
    $.ajax({
			type: "POST",
			url: "{{URL::to('/quick/deleteBatchLimitById')}}",
                        data: {'batchlimit_id':$('#deleteBatchesLimit_id').val()},
			dataType: 'json',
			success: function(response){
                            //console.log(response);
                 if(response.status=='success'){  
                   $('#deletebatcheslimit').modal('hide');
                   $('#deleteBatchLimit').show();
	               setTimeout(function(){
                       window.location.reload(1);
                    }, 3000);
                }
            }
        });  
});

</script>
@stop

@section('content')
<div id="breadcrumb">
    <ul class="crumbs">
        <li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
        <li><a href="#" style="z-index:8;">Batches</a></li>
        <li><a href="#" style="z-index:7;">Add/view BatchesLimit</a></li>
    </ul>
</div>
<div id="editBatchLimit" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:20px;">
    <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:25%;">
     Updating batch limit  . . .
    </p>
</div>
<div id="deleteBatchLimit" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:20px;">
    <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:25%;">
     Deleting batch limit  . . .
    </p>
</div>
<br clear="all"/>
<br clear="all"/>
<div class="content">
    <div class="row">
        <h4>Add/view BatchesLimit</h4>

        @if (Session::has('msg'))
        <div class="uk-alert uk-alert-success" data-uk-alert>
            <a href="#" class="uk-alert-close uk-close"></a>
            {{ Session::get('msg') }}
        </div>
        @endif
        <div class="md-card">
            <div class="md-card-content large-padding">
                <h3 class="heading_b uk-margin-bottom">Add Batches Limit</h3>

                <div class="md-card uk-margin-medium-bottom">
                    <div class="md-card-content">
                        <br>
                        {{ Form::open(array('url' => '/batches/addbatchlimit', 'id'=>"addbatchlimit", "class"=>"uk-form-stacked", 'method' => 'post')) }}    
                        <div class="uk-grid" data-uk-grid-margin>
                            <div class="uk-width-medium-1-3">
                                <div class="parsley-row">
                                    <label for="batch_limit">BatchLimit(receptionist)<span class="req">*</span></label>
                                    {{Form::text('batch_limit_recep', null,array('id'=>'batch_limit_recep', 'required', 'class' => 'form-control input-sm md-input'))}}
                                </div>
                            </div>
                            <div class="uk-width-medium-1-3">
                                <div class="parsley-row">
                                    <label for="batch_limit">BatchLimit(Admin)<span class="req">*</span></label>
                                    {{Form::text('batch_limit_admin', null,array('id'=>'batch_limit_admin', 'required', 'class' => 'form-control input-sm md-input'))}}
                                </div>
                            </div>
                            <div class="uk-width-1-3"></div>
                            <div class="uk-width-1-3">
                                <div class="parsley-row">
                                    <button type="submit" class="md-btn md-btn-primary" style="float:left" >Add Batch Limit</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="md-card">
            <div class="md-card-content large-padding">
                <h3 class="heading_b uk-margin-bottom">Batches Limit</h3>
                <div class="md-card uk-margin-medium-bottom" id='batchData'>
                    <div class="md-card-content">
                        <div class="uk-overflow-container">
                            <table id="batchLimitTable" class="uk-table">
                                <!-- <caption>Table caption</caption> -->
                                <thead>
                                    <tr>
                                        <th>Batches Limit Number</th> 
                                        <th>Batch Limit(Receptionist)</th>
                                        <th>Batch Limit(Admin)</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($batchLimit)) { ?>    
                                        @foreach($batchLimit as $batchLmt)
                                        <tr>
                                            <td>{{$batchLmt->batches_limit_no}}</td>
                                            <td>{{$batchLmt->batch_limit_receptionist}}</td>
                                            <td>{{$batchLmt->batch_limit_admin}}</td>
                                            <td>{{$batchLmt->created_at}}</td>
                                            <td>
                                                <a id='editBatchbutton' class="btn btn-warning btn-xs" onclick="editbatcheslimit({{$batchLmt->id}},{{$batchLmt->batch_limit_receptionist}},{{$batchLmt->batch_limit_admin}})" title="Edit"> <i class="Small material-icons" style="font-size:20px;">mode_edit</i></a>
                                                <a id='deleteBatchbutton' class="btn btn-danger btn-xs" onclick="deletebatcheslimit({{$batchLmt->id}})"> <i class="Small material-icons" style="font-size:20px;" title="Delete">delete</i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit  Batches Limit Modal -->
    <div id='editBatchesLimitmodal' class="modal fade" role="dialog" style="margin-top: 50px; z-index: 99999;"> 
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="editBatchheader">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Batches Limit</h4>
                </div>
                <div class="modal-body" id="editBatchbody">
                    <div class="batchEditMsg" id="batchEditMsg"></div>
                    <div><input type="hidden" value="" id='editBatchesLimit_id'/></div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-3">
                            <div class="parsley-row">
                                <label for="batch_limit_recep">Batch Limit(Receptionist)<span
                                        accesskey=""class="req">*</span></label><br>
                                <input type="text" value='' id='batch_limit_recep_modal' class='form-control input-sm md-input' name='batch_limit_recep' style="padding:0px;"/>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-3">
                            <div class="parsley-row">
                                <label for="batch_limit_admin">Batch Limit(Admin)<span
                                        accesskey=""class="req">*</span></label><br>
                                <input type="text" value='' id='batch_limit_admin_modal' class='form-control input-sm md-input' name='batch_limit_admin' style="padding:0px;"/>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
                <div class="modal-footer" id="editBatchfooter">
                    <button id="savebatcheslimitedit" type="button" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="deletebatcheslimit" role="dialog" style="margin-top: 50px; z-index: 99999;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div class="deletepriceheader" id='deletepriceheader'>
                        <h4 class="modal-title">Confirm Delete</h4>
                    </div>
                </div>
                <div class="modal-body deletepricebody" id='deletepricebody'>
                    <div><input type="hidden" value="" id='deleteBatchesLimit_id'/></div>
                    <p>Do you really want to delete this Batches Limit ?</p>
                </div>
                <div class="modal-footer deletepricefooter" id='deletepricefooter'>
                    <center>
                        <button type="button" class="btn btn-primary" id='batcheslimit_delete' >Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    </center>
                </div>
            </div>
        </div>
    </div>

</div>
@stop