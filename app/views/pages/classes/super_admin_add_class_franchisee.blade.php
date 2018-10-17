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
    var franchisee_id = '';
    var courseId = '';
    var classId = '';
    $(document).on('change','#franchiseeChange', function () {
        franchisee_id = $(this).val();
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/getAllCoursesForFranchisee')}}",
            data: {'franchisee_id': franchisee_id},
            dataType:"json",
            success: function (response)
            {
                $("#CoursesForFranchisee").val("");
                $("#CoursesForFranchisee").empty();
                string='<option></option>';
                for(var i=0;i<response.courses.length;i++) {
                    string += '<option value='+response.courses[i]['master_course_id'] + '-' + response.courses[i]['id'] +'>'+response.courses[i]['course_name']+'</option>';
                 }
                $("#CoursesForFranchisee").append(string);
                $("#BasePrice").val("");
                $("#BasePrice").empty();
                price='';
                for(var i=0;i<response.baseBrice.length;i++) {
                    price += '<option value='+response.baseBrice[i]['id']+'>'+response.baseBrice[i]['base_price']+'</option>';
                 }
                $("#BasePrice").append(price);
            }
        });
    });

    $(document).on('change','#CoursesForFranchisee', function () {
        courseId = $(this).val();
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/getAllClassesForFranchisee')}}",
            data: {'franchisee_id': franchisee_id, 'courseId': courseId},
            dataType:"json",
            success: function (response)
            {
                $("#ClassesForFranchisee").val("");
                $("#ClassesForFranchisee").empty();
                string='';
                for(var i=0;i<response.classesForCourse.length;i++) {
                    string += '<option value='+response.classesForCourse[i]['base_price_no']+'>'+response.classesForCourse[i]['class_name']+'</option>';
                 }
                $("#ClassesForFranchisee").append(string);
            }
        });
    });

	$("#seasonTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            $(nRow).click(function() {
            });
            return nRow;
        },
       "iDisplayLength": 10,
       "lengthMenu": [ 10, 50, 100, 150, 200 ]
	 });




	$(document).on('click', '#saveClasses', function(e){
      e.preventDefault();
      var id = $('#CoursesForFranchisee').val();
      var Course_master_id = id.split("-")[0];
      var Course_id = id.split("-")[1];
      if ($('#franchiseeChange').val() !== null && $('#CoursesForFranchisee').val() !== null && $('#ClassesForFranchisee').val() !== null && $('#BasePrice').val()) {
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/addNewClassToFrnachisee')}}",
            data: {'franchisee_id': franchisee_id, 'courseId': courseId, 'BasePrice': $('#BasePrice').val(), 'class_name': $("#ClassesForFranchisee option:selected" ).text(), 'Course_id': Course_id},
            dataType:"json",
            success: function (response)
            {
                if(response.status ==="success"){
                    if(response.test == "exist"){
                        $('#msgDiv').html("<h5 class = 'uk-alert uk-alert-danger' style = 'color: #fff; width: 90%; padding: 8px; text-align: center'>Class is already Exist.</h5>");
                    } else {
                        $('#msgDiv').html("<h5 class = 'uk-alert uk-alert-success' style = 'color: #fff; width: 90%; padding: 8px; text-align: center'>Class inserted Successfully.</h5>");   
                        $('#newClassLoading').show();
                        setTimeout(function(){
                          window.location.reload(1);
                        }, 2500);
                    }
                }else{
                   $('#msgDiv').html("<h5 class = 'uk-alert uk-alert-success' style = 'color: #fff; width: 90%; padding: 8px; text-align: center'>Status failed please try agiain.</h5>");
                    /*setTimeout(function(){
                      window.location.reload(1);
                    }, 2500);*/
                }
           }
        });
      } else {
        $('#msgDiv').html("<h5 class = 'uk-alert uk-alert-danger' style = 'color: #fff; width: 90%; padding: 8px; text-align: center'>Please Fill all required fields and save.</h5>");
      }
    });


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
        			var markup;
        			if(response[0] != ''){
        				for(var i = 0; i < response[0].length; i++){
        					markup += '<option value = "'+response[0][i]["class_name"]+'">'+response[0][i]["class_name"]+'</option>';
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

    var selectedfIdForClasses = ''
    var selectedClassId = ''
    $(document).on('change', '#courseFName', function () {
      selectedfIdForClasses = parseInt($(this).val());
      $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/getAllClassesForFranchiseeWise')}}",
        data: {
           'franchisee_id': selectedfIdForClasses,
        },
        dataType: 'json',
        success: function(response){
          if(response.status === "success"){
             var header_data="<div class='uk-overflow-container'>"+
                         "<table id='reportTable' class='uk-table'>"+
                         "<thead>"+
                         '<tr>'+
                         '<th>Name Of Course</th>'+
                         '<th>Name Of Class</th>'+
                         '<th>Slug</th>'+
                         '<th>Base Price</th>'+
                         '<th>Actions</th>'+
                         '</tr></thead>';
             for(var i=0;i<response.classesForFranchisee.length;i++){
                  header_data+="<tr><td>"+response.classesForFranchisee[i]['course_name']+"</td><td>"+
                  response.classesForFranchisee[i]['class_name']+"</td><td>"+
                  response.classesForFranchisee[i]['slug']+"</td><td>"+
                  response.classesForFranchisee[i]['base_price']+"</td><td>"+
                  '<span class="btn btn-warning btn-xs" title="Modify" onclick = "editBasePrice(' + response.classesForFranchisee[i]['base_price'] + ', '+ response.classesForFranchisee[i]['base_price_no'] +', '+ response.classesForFranchisee[i]['id'] +')"><i class="Small material-icons" style="font-size:20px;">mode_edit</i></span>'+"</td></tr>";
              }
             header_data+="</table></div>";
             $('#reportdata').html(header_data);
          }
        }
      });
    });

    function editBasePrice(B_price, B_Price_No, class_id){
        selectedClassId = class_id
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/getBasePricesForFranchisee')}}",
            data: {'franchisee_id': selectedfIdForClasses, 'class_id': selectedClassId},
            dataType:"json",
            success: function (response)
            {
                $("#EditBasePrice").val("");
                $("#EditBasePrice").empty();
                string='<option></option>';
                for(var i=0;i<response.basePrice.length;i++) {
                    string += '<option value='+response.basePrice[i]['base_price_no']+'>'+response.basePrice[i]['base_price']+'</option>';
                 }
                $("#EditBasePrice").append(string);
            }
        });
        $('#editModal').modal('show');
    }
            
    $('#saveBtn').click(function(){ 
        if($('#EditBasePrice').val() != ''){
            $.ajax({
                type: "POST",
                url: "{{URL::to('/quick/updateClassesBasePriceForFranchisee')}}",
                data: {'BasePriceNo': $('#EditBasePrice').val(),'class_id':selectedClassId, 'franchisee_id': selectedfIdForClasses},
                dataType:"json",
                success: function (response)
                {
                    if(response.status == "success"){
                        $('#modalMsgDiv').html("<h5 class = 'uk-alert uk-alert-success' style = 'color: #fff; width: 100%; padding: 8px; text-align: center'>Class is updated Successfully. Please wait untill this page reloads.</h5>");
                        $('#updateClassLoading').show(); 
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

</script>

@stop

@section('content')
<div class="uk-width-medium-1-1">
	<div class="parsley-row form-group">
    	<div id = "msgDiv"></div>
    </div>
</div>
<div id="newClassLoading" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:18px;">
    <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:13%;">
     New class added successfully.Please wait . . .
    </p>
</div>
<div id="updateClassLoading" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:18px;">
    <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:13%;">
     Class updated successfully.Please wait . . .
    </p>
</div>
<div class="md-card">
	<div class="md-card-content large-padding">
			<h3 class="heading_b uk-margin-bottom">Add Classes</h3>

		<br clear="all"/>

		<div id = "addCourses">
			<div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-4">    
                    <div class="parsley-row">
                        <label for="Courses">Select Franchisee<span class="req">*</span></label><br>
                        <select  class="form-group courseFName  input-sm md-input" id="franchiseeChange" style="padding:0px;width:100%">
                        @foreach($franchiseelist as $franchisee) 
                            <option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
                        @endforeach
                        </select>                                   
                    </div>
                </div>
				<div class="uk-width-medium-1-4">    
				    <div class="parsley-row">
				    	<label for="Courses">Select Courses<span class="req">*</span></label><br>
				        <select id="CoursesForFranchisee" name="Courses" class="form-control input-sm md-input" required style='padding:0px; font-weight:bold;color: #727272;'></select>				                 	
				    </div>
			    </div>

			    <div class="uk-width-medium-1-4">    
				    <div class="parsley-row">
				    	<label for="Courses">Select Classes<span class="req">*</span></label><br>
				        <select id="ClassesForFranchisee" name="Courses" class="form-control input-sm md-input" required style='padding:0px; font-weight:bold;color: #727272; ' ></select>
				    </div>
			    </div>

			    <div class="uk-width-medium-1-4">    
				    <div class="parsley-row">
				    	<label for="Courses">Select Base Price<span class="req">*</span></label><br>
				        <select id="BasePrice" name="Courses" class="form-control input-sm md-input" required style='padding:0px; font-weight:bold;color: #727272;'></select>				                 	
				    </div>
			    </div>
    		</div>
		</div>

		<br clear="all"/>
		<br clear="all"/>
		<div class="row">
        <div class="col-md-11">
			<button type="button" id="saveClasses" class="md-btn md-btn-primary pull-right">Save CLasses</button>
        </div>
    </div>
	</div>
</div>

<div class="uk-width-medium-1-1">
	<div class="parsley-row form-group">
    	<div id = "msgDiv1"></div>
    </div>
    <div class="row">
        <div class="md-card">
            <div class="md-card-content large-padding">
                <div class="row">
                  <div class="col-lg-6">
                    <h4 style="color:#d3d3de;float:right;">View Classes</h4>
                  </div>
                </div>
                <hr>
                <div class="row" align="right" style="padding-top:30px;">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                      <select  class="form-group courseFName  input-sm md-input" id="courseFName" style="padding:0px;width:100%">
                      @foreach($franchiseelist as $franchisee) 
                          <option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
                      @endforeach
                      </select>
                    </div>
                    <div class="col-lg-4"></div>
                </div>
            </div>
            <div class="md-card uk-margin-medium-bottom" id="reportdata">
                <div class="md-card-content" >
                        <table id="reportTable" class="uk-table">     
                        </table> 
                </div>
            </div>
        </div>
    </div><!-- row -->

</div>

<!-- <div class="md-card">
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
</div> -->
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