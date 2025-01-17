@extends('layout.master')

@section('libraryCSS')
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' />
    <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet' />

	<style type="text/css">

	</style>
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

	$("#seasonTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            $(nRow).click(function() {
            });
            return nRow;
        },
       "iDisplayLength": 10,
       "lengthMenu": [ 10, 50, 100, 150, 200 ]
	 });




	function saveClasses(){
	if($('#Courses').val() != '' && $('#Classes').val() != '' && $('#BasePrice').val() != ''){
        var id = $('#Courses').val();
        var Course_master_id = id.split("-")[0];
        var Course_id = id.split("-")[1];
		$.ajax({
			type: "POST",
        	url: "{{URL::to('/quick/InsertNewClassFromFranchise')}}",
        	data: {'Course_master_id': Course_master_id, 'className': $('#Classes').val(), 'basePriceNo': $('#BasePrice').val(), 'Course_id': Course_id},
        	dataType:"json",
        	success: function (response)
        	{
        		console.log(response);
                if(response.test == "exist"){
                    $('#msgDiv').html("<h5 class = 'uk-alert uk-alert-danger' style = 'color: #fff; width: 90%; padding: 8px; text-align: center'>Class is already Exist.</h5>");   
                }else{
	               $('#msgDiv').html("<h5 class = 'uk-alert uk-alert-success' style = 'color: #fff; width: 90%; padding: 8px; text-align: center'>Row was Inserted Successfully. Please wait untill this page reloads.</h5>");
                    setTimeout(function(){
	            	  window.location.reload(1);
                    }, 2500);
        	    }
           }
    	});
	}else{
		$('#msgDiv').html("<h5 class = 'uk-alert uk-alert-danger' style = 'color: #fff; width: 90%; padding: 8px; text-align: center'>Please Fill all required fields and save.</h5>");
	}
}



function editBasePrice(B_price, B_price_no,class_id){
        
//	
	$('#EditBasePrice').val(B_price_no);
//	$('#EditBasePriceNo').val(B_Price_No);
//        $('#class_id').val(class_id);

	$('#editModal').modal('show');
        
        
$('#saveBtn').click(function(){

		
	if(B_price != '' && B_price_no != ''){
		$.ajax({
        	type: "POST",
        	url: "{{URL::to('/quick/updateClassesBasePrice')}}",
        	data: {'BasePriceNo': $('#EditBasePrice').val(),'class_id':class_id},
        	dataType:"json",
        	success: function (response)
        	{
        		if(response.status == "success"){
        			console.log(response);
            		$('#modalMsgDiv').html("<h5 class = 'uk-alert uk-alert-success' style = 'color: #fff; width: 100%; padding: 8px; text-align: center'>Row was updated Successfully. Please wait untill this page reloads.</h5>");
                	setTimeout(function(){
                	    window.location.reload(1);
                	}, 2500);
        		}else{
        			console.log(response);
        		}
        	}	
                });
	}else{
		$('#modalMsgDiv').html("<h5 class = 'uk-alert uk-alert-danger' style = 'color: #fff; width: 100%; padding: 8px; text-align: center'>Please fill all required fields and Update.</h5>");		
	}	
	});

        
}



	$('#Courses').change(function(){
		var CoursemasterId = $(this).val();

		$.ajax({
        	type: "POST",
        	url: "{{URL::to('/quick/getClassesByCourseId')}}",
        	data: {'CoursemasterId': CoursemasterId},
        	dataType:"json",
        	success: function (response)
        	{
        		if(response.status == "success"){
        			console.log(response[0]);
        			var markup;
        			if(response[0] != ''){
        				for(var i = 0; i < response[0].length; i++){
        					markup += '<option value = "'+response[0][i]["class_name"]+'">'+response[0][i]["class_name"]+'</option>';
        					//console.log(response[0][i]);
        				}
        				$('#Classes').html(markup);
        			}
        			else{
        				$('#Classes').html('<option></option>');
        			}
            		
        		}else{
        			console.log(response);
        		}
        	}	
    	});

	});

</script>

@stop

@section('content')
<div class="uk-width-medium-1-1">
	<div class="parsley-row form-group">
    	<div id = "msgDiv"></div>
    </div>
</div>

<div class="md-card">
	<div class="md-card-content large-padding">
			<h3 class="heading_b uk-margin-bottom">Add Classes</h3>

		<br clear="all"/>

		<div id = "addCourses">
			<div class="uk-grid" data-uk-grid-margin>
				<div class="uk-width-medium-1-3">    
				    <div class="parsley-row">
				    	<label for="Courses">Select Courses<span class="req">*</span></label><br>
				        <select id="Courses" name="Courses" class="form-control input-sm md-input" required style='padding:0px; font-weight:bold;color: #727272;'>
				        	<option></option>
				        	@for($i=0; $i < count($franchiseeCourses); $i++)
        					<option value = "{{$franchiseeCourses[$i]['master_course_id']}}-{{$franchiseeCourses[$i]['id']}}">{{$franchiseeCourses[$i]['course_name']}}</option>
        					@endfor
        				</select>				                 	
				    </div>
			    </div>

			    <div class="uk-width-medium-1-3">    
				    <div class="parsley-row">
				    	<label for="Courses">Select Classes<span class="req">*</span></label><br>
				        <select id="Classes" name="Courses" class="form-control input-sm md-input" required style='padding:0px; font-weight:bold;color: #727272; ' >
				        	
        				</select>
				    </div>
			    </div>

			    <div class="uk-width-medium-1-3">    
				    <div class="parsley-row">
				    	<label for="Courses">Select Base Price<span class="req">*</span></label><br>
				        <select id="BasePrice" name="Courses" class="form-control input-sm md-input" required style='padding:0px; font-weight:bold;color: #727272;'>
				        	<option></option>
				        	@for($i=0; $i < count($franchiseeBaseprice); $i++)
        					<option value = "{{$franchiseeBaseprice[$i]['base_price_no']}}">{{$franchiseeBaseprice[$i]['base_price']}}</option>
        					@endfor
        				</select>				                 	
				    </div>
			    </div>
				
    		</div>
		</div>

		<br clear="all"/>
		<br clear="all"/>
		<div class="row">
        <div class="col-md-11">
			<button type="button" id="saveCourses" onclick = saveClasses() class="md-btn md-btn-primary pull-right">Save CLasses</button>
        </div>
    </div>
	</div>
</div>

<div class="uk-width-medium-1-1">
	<div class="parsley-row form-group">
    	<div id = "msgDiv1"></div>
    </div>
</div>

<div class="md-card">
	<div class="md-card-content large-padding">
		<h3 class="heading_b uk-margin-bottom">View Classes</h3>

		<div class="md-card-content">
        	<div class="uk-overflow-container">
            	<table  class="uk-table" id="seasonTable">
                	<thead>
		            	<tr>
		            		<th>Name Of Course</th>
		                	<th>Name Of Class</th>
		                    <th>Slug</th>
		                    <th>Base Price</th>
                            <th>Actions</th>
		                </tr>
		            </thead>
                  	<tbody>
                         @for($i = 0; $i < count($getAllClassesForFranchise); $i++)       
                         	<tr>
                         		<td>{{$getAllClassesForFranchise[$i]['course_name']}}</td>
                         		<td>{{$getAllClassesForFranchise[$i]['class_name']}}</td>
                         		<td>{{$getAllClassesForFranchise[$i]['slug']}}</td>
                         		<td>{{$getAllClassesForFranchise[$i]['base_price']}}</td>
                         		<td>
                         			<span class='btn btn-warning btn-xs' title='Modify' onclick = 'editBasePrice("{{$getAllClassesForFranchise[$i]['base_price']}}", "{{$getAllClassesForFranchise[$i]['base_price_no']}}","{{$getAllClassesForFranchise[$i]['id']}}")'><i class="Small material-icons" style="font-size:20px;">mode_edit</i></span>
                         		</td>
                         	</tr>
                         @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Edit-->
<div id="editModal" class="modal fade" role="dialog" style = "margin-top: 3em">
  <div class="modal-dialog modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Base Price</h4>
      </div>
      <div class="modal-body">
      		<div id = "modalMsgDiv"></div>
      		<br clear="all"/>
        	<div class="uk-grid" data-uk-grid-margin>
				
				<div class="uk-width-medium-1-3">
        			<div class="parsley-row form-group">
        				<label_ for="EditBasePrice" style = "font-weight:bold;">Base Price<span class_="req">*</span></label>
                                <select name="EditBasePrice" id="EditBasePrice" class="input-sm md-input"
                                                                 style='padding: 0px; font-weight: bold; color: #727272; width:100%'>
                                                                                        <option></option>
                                                                                        @for($i=0; $i < count($franchiseeBaseprice); $i++)
                                                                                        <option value = "{{$franchiseeBaseprice[$i]['base_price_no']}}">{{$franchiseeBaseprice[$i]['base_price']}}</option>
                                                                                        @endfor
                                </select>
                                </div>
    			</div>
    			<input type = "hidden" id = "EditBasePriceNo">
                        <input type = "hidden" id = "class_id">
                        
    		</div>
    	</div>
      <div class="modal-footer">
        <button type = "button" class = "btn btn-primary" id = "saveBtn">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>
@stop