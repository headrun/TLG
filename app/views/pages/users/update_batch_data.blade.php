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

$("#customersTable").DataTable();
$("#customersTable tr").click(function (){
    //window.location = $(this).find('a').attr('href');
});

    
    
</script>

<script type="text/javascript">
    $(document).on('click', '#updateBatchId', function(){

    var update_id = $('#updateId').val();
    var batch_id = $('#batchId').val();
    if (typeof update_id !== 'undefined' && typeof batch_id !== 'undefined' ) {
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/UpdateDataBatch')}}",
            data: {'update_id': update_id, 'batch_id': batch_id},
            dataType: 'json',
            success: function(response){
                
            }
            });
    }

 
});
</script>
@stop

@section('content')

<div id="breadcrumb">
    <ul class="crumbs">
        <li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
        <li><a href="{{url()}}/admin/users" style="z-index:8;">Users</a></li>
        <li><a href="#" style="z-index:7;">update</a></li>
    </ul>
</div>
<br clear="all"/>
<div class="">
    <div class="row">
    
        
        
            <h3 class="heading_b uk-margin-bottom">Update Batch Details</h3>
                        {{ Form::open(array('url' => '/reports/updateDataBatch', 'id'=>"updateDataBatchform", "class"=>"uk-form-stacked", 'method' => 'post')) }} 
                          <div class="uk-grid" data-uk-grid-margin>
                              <div class="uk-width-medium-1-4">
                                <div class="parsley-row form-group">
                                  <label for="updateId">Update batch ID</label><br>
                                    {{Form::text('updateId', null,array('id'=>'updateId'))}} 
                                </div>
                              </div>
                              <div class="uk-width-medium-1-4">
                               <div class="parsley-row form-group">
                                  <label for="batchId">Batch ID</label><br>
                                    {{Form::text('batchId', null,array('id'=>'batchId'))}} 
                               </div>
                            </div>

                              <div class="uk-width-1-4">
                                <div class="parsley-row" style="padding: 25px 30px;">
                                  <button type="button" class="md-btn md-btn-primary" id="updateBatchId">Update</button>
                                </div>
                              </div>
                            </div>
                        {{ Form::close() }}
        
           
        
        
        
    </div><!-- row -->
</div><!-- Container -->

 
@stop