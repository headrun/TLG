@extends('layout.master')

@section('libraryCSS')
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' />
    <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet' />
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

    $('#AddDiscounts').click(function(){
        $('#mainPercentageDiv').css('display','block');
        $('#mainSecondDiscDiv').css('display','none');
    });

    $('#AddSecondChldCls').click(function(){
        $('#mainPercentageDiv').css('display','none');
        $('#mainSecondDiscDiv').css('display','block');
    });



	$("#seasonTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            $(nRow).click(function() {
            });
            return nRow;
        },
       "ordering": false,
       "iDisplayLength": 20,
       "lengthMenu": [ 10, 50, 100, 150, 200 ]
	 });

    $("#seasonTable1").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            $(nRow).click(function() {
            });
            return nRow;
        },
       "ordering": false,
       "iDisplayLength": 20,
       "lengthMenu": [ 10, 50, 100, 150, 200 ]
     });

	$(document).on("click", "a.remove" , function() {
            $(this).parent().parent(".uk-grid").remove();
    });

	function addDiscount(){
		var markups ='<div class="uk-grid" data-uk-grid-margin>'+
					  	'<div class="uk-width-medium-1-3">'+
        					'<div class="parsley-row form-group">'+
        						'<label for="title[]">Number of Classes<span class="req">*</span></label>'+
        						'<input id="NoClasses[]" required class="form-control input-sm md-input" name="NoClasses[]" type="text"/>'+
        					'</div>'+
    					'</div>'+
    					'<div class="uk-width-medium-1-3">'+
        					'<div class="parsley-row form-group">'+
        						'<label for="title[]">Discount Percentage<span class="req">*</span></label>'+
        						'<input id="DiscountPrcnt[]" required class="form-control input-sm md-input" name="DiscountPrcnt[]" type="text"/>'+
        					'</div>'+
    					'</div>'+
    					'<div class="uk-width-medium-1-3">'+	
    						'<a href="javascript:void(0);" class="remove badge" style = "background: red; color: #fff"> &times; </a>'+
    					'</div>'+
					'</div>';
		$('#addDiscount').append(markups);
	}

	function saveDiscount(){
            $('#discountsAdd').show();
			var no_of_class = $('input[name="NoClasses[]"]').map(function() {
                        return this.value
                   }).get();
			var discount_prcnt = $('input[name="DiscountPrcnt[]"]').map(function() {
                        return this.value
                   }).get();
            console.log(no_of_class[0]);
            console.log(discount_prcnt);
            if(no_of_class != '' && discount_prcnt != ''){
			$.ajax({
        		type: "POST",
        		url: "{{URL::to('/quick/addMultipleDiscounts')}}",
        		data: {'no_of_class': no_of_class, 'discount_prcnt': discount_prcnt},
        		dataType:"json",
        		success: function (response)
        		{
        			if (response.status == "success") {
                        setTimeout(function () {
                            $('#discountsAdd').hide();
                        }, 3000);
        				$('#msgDiv').html('<h5 class = "uk-alert-success" style = "color: #fff; width: 85%; padding: 8px; text-align: center">Discount added Successfully. Please wait untill this page reload</h5>');			
                        setTimeout(function(){
 							window.location.reload(1);
           				}, 3500);
        			}
        			else{
        				setTimeout(function () {
                            $('#discountsAdd').hide();
                        }, 3000);
        			}
        		}
        	});  
        }else{
            setTimeout(function () {
                $('#discountsAdd').hide();
            }, 3000);
            $('#msgDiv').html('<h5 class = "uk-alert-danger" style = "color: #fff; width: 95%; padding: 8px; text-align: center">Please fill the all required fields.</h5>');          
        }
	}



function deleteDiscounts(id){
    $('#deleteDiscounts').modal('show');
    $('#discounts_delete').click(function(){
        $('#discountsDelete').show();
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/deleteDiscounts')}}",
            data: {'id': id},
            dataType: 'json',
            success: function(response){
                if(response.status=='success'){
                    $('#deleteDiscounts').modal('hide');
                    setTimeout(function () {
                        $('#deleteDiscounts').hide();
                    }, 4000);
                    $('#successDiv').html('<h5 alert class = "uk-alert-success" style = "color: #fff; width: 100%; padding: 10px; text-align: center">Discount deleted Successfully.</h5>');
                    setTimeout(function(){
                        window.location.reload(1);
                    }, 2000);
                    
                }
            }
        });
    });
} 

function editDiscounts(Dprcntge,NoClses,id){
    var id = id;
    var Dprcntge = Dprcntge;
    var NoClses = NoClses;
    $('#editNoOfClsses').val(NoClses);
    $('#editDiscountPrcnt').val(Dprcntge);

    $('#editDiscounts').modal('show');
    $('#discounts_update').click(function(){
        $('#discountsUpdate').show();
        if($('#editNoOfClsses').val() != '' && $('#editDiscountPrcnt').val() != ''){
            $.ajax({
                type: "POST",
                url: "{{URL::to('/quick/updateDiscounts')}}",
                data: {'id': id, 'no_of_classes': $('#editNoOfClsses').val(), 'Discount_percentage': $('#editDiscountPrcnt').val()},
                dataType: 'json',
                success: function(response){
                    //console.log(response);
                    if(response.status=='success'){
                        $('#editDiscounts').modal('hide');
                        setTimeout(function () {
                            $('#discountsUpdate').hide();
                        }, 2000);
                        $('#successDiv').html('<h5 alert class = "uk-alert-success" style = "color: #fff; width: 100%; padding: 10px; text-align: center">Discount updated Successfully. Please wait untill this page reload</h5>');
                        setTimeout(function(){
                            window.location.reload(1);
                        }, 2000);  
                     
                    }
                }
            });
        }
        else{
            setTimeout(function () {
                $('#discountsUpdate').hide();
            }, 3000);
            $('#ModelMsgDiv').html('<h5 alert class = "uk-alert-success" style = "color: #fff; width: 100%; padding: 10px; text-align: center">Please fill all required fields</h5>');
        }  
    });    
}


function editChild_Class_disc(Class, Child){

    $('#editMulClass').val(Class);
    $('#editMulChild').val(Child);

    $('#editChild_Class_disc').modal('show');
    $('#Class_child_disc_update').click(function(){
            $.ajax({
                type: "POST",
                url: "{{URL::to('/quick/updateSecondChild_ClassDisc')}}",
                data: {'editClass': $('#editMulClass').val(), 'editChild': $('#editMulChild').val()},
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    $('#editChild_Class_disc').modal('hide');
                    if(response.status=='success'){
                        $('#successDiv1').html('<h5 alert class = "uk-alert-success" style = "color: #fff; width: 100%; padding: 10px; text-align: center">Discounts are updated Successfully. Please wait untill this page reload</h5>');
                        $('#editDiscounts').modal('hide');
                        $('#discountsUpdate').show();
                        setTimeout(function(){
                            window.location.reload(1);
                        }, 2000);  
                     
                    }
                }
            }); 
    });    
}


function insertChild_Class_disc(Class, Child){

    $('#editMulClass').val(Class);
    $('#editMulChild').val(Child);

    $('#editChild_Class_disc').modal('show');
    $('#Class_child_disc_update').click(function(){
            $.ajax({
                type: "POST",
                url: "{{URL::to('/quick/insertSecondChild_ClassDisc')}}",
                data: {'editClass': $('#editMulClass').val(), 'editChild': $('#editMulChild').val()},
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    $('#editChild_Class_disc').modal('hide');
                    if(response.status=='success'){
                        $('#successDiv1').html('<h5 alert class = "uk-alert-success" style = "color: #fff; width: 100%; padding: 10px; text-align: center">Discounts are updated Successfully. Please wait untill this page reload</h5>');
                        setTimeout(function(){
                            window.location.reload(1);
                        }, 2000);  
                     
                    }
                }
            }); 
    });    
}


/*function saveChildClsDisc(){

    var editChild = $('#DiscountForSecondChaild').val();
    var editClass = $('#DiscountForSecondClass').val();

    if(editChild != '' && editClass != ''){
            $.ajax({
                type: "POST",
                url: "{{URL::to('/quick/updateSecondChild_ClassDisc')}}",
                data: {'editClass': editClass, 'editChild': editChild},
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if(response.status=='success'){
                        $('#successDiv2').html('<h5 alert class = "uk-alert-success" style = "color: #fff; width: 100%; padding: 10px; text-align: center">Discounts are updated Successfully. Please wait untill this page reload</h5>');
                        setTimeout(function(){
                            window.location.reload(1);
                        }, 2000);  
                     
                    }
                }
            });     
    }else{
        $('#successDiv2').html('<h5 alert class = "uk-alert-danger" style = "color: #fff; width: 100%; padding: 10px; text-align: center">The fields should not empty. Please fill and save.</h5>');
        setTimeout(function(){
            window.location.reload(1);
        }, 2000); 
    }
}*/



</script>
@stop

@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">Discounts</a></li>
		<li><a href="#" style="z-index:7;">Add/View Discounts</a></li>
	</ul>
</div>

<div class="row" style = "margin-top: 1em;">
    <div class="col-md-12">
        <button type="button" id="AddDiscounts" class="md-btn md-btn-primary pull-right" style = "margin-right: 3em">Add Discounts For Classes</button>
        <button type="button" id="AddSecondChldCls" class="md-btn md-btn-primary pull-right" style = "margin-right: 3em">Add Second Child & Multi Classes Discounts</button>
    </div>
</div>
<div id="discountsAdd" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 28%; left: 35%;font-size:18px;">
      <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:60%;">
    </p>
</div>
<div id="discountsUpdate" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 28%; left: 35%;font-size:18px;">
      <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:60%;">
    </p>
</div>
<div id="discountsDelete" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 28%; left: 35%;font-size:18px;">
      <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:60%;">
    </p>
</div>
<div id = "mainPercentageDiv" style = "display: none">
    <br clear="all"/>
    <div class="uk-width-medium-1-1">
	   <div class="parsley-row form-group">
    	   <div id = "msgDiv"></div>
        </div>
    </div>
    <div class="md-card">
        <div class="md-card-content large-padding">
            <div class="uk-width-medium-1-1">
	           <div class="parsley-row form-group">
    	           <strong><h4>Add Discounts<i class=" btn fa fa-plus fa-1x" onclick="addDiscount()"></i></h4></strong>
                </div>
            </div>

            <div id = "addDiscount">
	           <div class="uk-grid" data-uk-grid-margin>
		          <div class="uk-width-medium-1-3">
        	           <div class="parsley-row form-group">
        		          <label for="title[]">Number of Classes<span class="req">*</span></label>
            	               {{Form::text('NoClasses[]', null,array('id'=>'NoClasses[]',
				                'required', 'class' => 'form-control input-sm md-input'))}}
        	            </div>
    	           </div>
    	           <div class="uk-width-medium-1-3">
        	           <div class="parsley-row form-group">
        		          <label for="title[]">Discount Percentage<span class="req">*</span></label>
            	               {{Form::text('DiscountPrcnt[]', null,array('id'=>'DiscountPrcnt[]',
				                'required', 'class' => 'form-control input-sm md-input'))}}
        	           </div>
    	           </div>
	           </div>
            </div>

	       <div class="row" style = "margin-top: 3em;">
                <div class="col-md-12">
			         <button type="button" id="saveDiscount" onclick = "saveDiscount()" class="md-btn md-btn-primary pull-right" style = "margin-right: 7em">Save Discounts</button>
                </div>
            </div>

        </div>
    </div>

    <div class="md-card">
	   <div class="md-card-content large-padding">
            <div id = "successDiv"></div>
		        <h3 class="heading_b uk-margin-bottom">Discounts</h3>

		        <div class="md-card uk-margin-medium-bottom" >
		            <div class="md-card-content">
                        <div class="uk-overflow-container">
                            <table  class="uk-table" id="seasonTable">
                                <thead>
		                            <tr>
		                                <th>Number Of Classes</th>
		                                <th>Discount Percentage</th>
                                        <th>Created BY</th>
                                        <th>Actions</th>
		                            </tr>
		                            </thead>
                                    <tbody>
                                        <?php if($discount_data){for($i=0;$i<sizeof($discount_data);$i++){ ?>
                                        <tr>
                                            <td>{{$discount_data[$i]['number_of_classes']}}</td>
                                            <td>{{$discount_data[$i]['discount_percentage']}}</td>
                                            <td>{{$discount_data[$i]['created_by_name']}}</td>
                                            <td><button class='btn btn-warning btn-xs' ><i class="Small material-icons" onclick='editDiscounts({{$discount_data[$i]['discount_percentage']}}, {{$discount_data[$i]['number_of_classes']}}, {{$discount_data[$i]['id']}})'>mode_edit</i></button>
                                                 <button class='btn btn-danger btn-xs' ><i class="Small material-icons" onclick="deleteDiscounts({{$discount_data[$i]['id']}})">delete</i></button>
                                             </td>
                                        </tr>
                                         <?php }}else{ ?>
                                        <tr>
                                             No seasons Added
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
	           </div>
            </div>
        </div>



<div id = "mainSecondDiscDiv" style = "display: none">
    <div class="md-card">
        <div class="md-card-content large-padding">
            <!--<div id = "successDiv2"></div>
                <div class="uk-width-medium-1-1">
                    <div class="parsley-row form-group">
                        <strong><h4>Add Second Child/Multiple Class Discounts</h4></strong>
                    </div>
                </div>
                <br>

                <div class="uk-grid" data-uk-grid-margin style = "margin-top: 1em">
                    <div class="uk-width-medium-1-3">
                        <div class="parsley-row form-group">
                            <label for="title[]">Discount For Second Class<span class="req">*</span></label>
                                {{Form::number('DiscountForSecondClass', null,array('id'=>'DiscountForSecondClass',
                                'required', 'class' => 'form-control input-sm md-input'))}}
                        </div>
                    </div>
                    <div class="uk-width-medium-1-3">
                        <div class="parsley-row form-group">
                            <label for="title[]">Discount For Second Child<span class="req">*</span></label>
                                {{Form::number('DiscountForSecondChaild', null,array('id'=>'DiscountForSecondChaild',
                                'required', 'class' => 'form-control input-sm md-input'))}}
                        </div>
                    </div>
                </div>
                <br></br>

                <div class="row" style = "margin-top: 3em;">
                    <div class="col-md-12">
                        <button type="button" id="saveDiscount" onclick = "saveChildClsDisc()" class="md-btn md-btn-primary pull-right" style = "margin-right: 7em">Save Discounts</button>
                    </div>
                </div>
                <br></br>-->


                <div id = "successDiv1"></div>
                <h3 class="heading_b uk-margin-bottom">Second Child & multiple Classes Discounts</h3>

                <div class="md-card uk-margin-medium-bottom" >
                    <div class="md-card-content">
                        <div class="uk-overflow-container">
                            <table  class="uk-table" id="seasonTable1">
                                <thead>
                                    <tr>
                                        <th>Discounts for Second Class</th>
                                        <th>Discounts for Second Child</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($discount_data[0])){ ?>
                                    <tr>
                                        <td>{{$discount_data[0]['discount_second_class']}}</td>
                                        <td>{{$discount_data[0]['discount_second_child']}}</td>
                                        <td><button class='btn btn-warning btn-xs' ><i class="Small material-icons" onclick='editChild_Class_disc({{$discount_data[0]['discount_second_class']}}, {{$discount_data[0]['discount_second_child']}})'>mode_edit</i></button>
                                        </td>
                                    </tr>
                                    <?php }else{ ?>
                                    <tr>
                                        <td>0</td>
                                        <td>0</td>
                                        <td><button class='btn btn-warning btn-xs' ><i class="Small material-icons" onclick='insertChild_Class_disc(0,0)'>mode_edit</i></button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->
  <div class="modal fade" id="deleteDiscounts" role="dialog" style="margin-top: 50px; z-index: 99999;">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Confirm Delete</h4>
        </div>
        <div class="modal-body">
            <div><input type='hidden' id='deleteBatch_id' value=''/></div>
          <p>Do you want to delete this Discount ?</p>
        </div>
        <div class="modal-footer ">
          <center>
          <button type="button" class="btn btn-primary" id='discounts_delete' >Yes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
          </center>
        </div>
      </div>
    </div>
  </div>



  <!-- Modal -->
  <div class="modal fade" id="editDiscounts" role="dialog" style="margin-top: 50px; z-index: 99999;">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Update</h4>
        </div>
        <div class="modal-body">
            <br>
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-2">
                    <div class="parsley-row form-group">
                        <label_ for="title[]" style = "font-weight: bold">Number of Classes<span class="req">*</span></label>
                        {{Form::text('editNoOfClsses', null,array('id'=>'editNoOfClsses',
                        'required', 'class' => 'form-control input-sm md-input'))}}
                    </div>
                </div>
                <div class="uk-width-medium-1-2">
                    <div class="parsley-row form-group">
                        <label_ for="title[]" style = "font-weight: bold">Discount Percentage<span class="req">*</span></label>
                        {{Form::text('editDiscountPrcnt', null,array('id'=>'editDiscountPrcnt',
                        'required', 'class' => 'form-control input-sm md-input'))}}
                    </div>
                </div>
                <br><br>
            </div>
        </div>
        <div class="modal-footer ">

          <button type="button" class="btn btn-primary" id='discounts_update' >Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

        </div>
    </div>
</div>
</div>


<!-- Modal -->
  <div class="modal fade" id="editChild_Class_disc" role="dialog" style="margin-top: 50px; z-index: 99999;">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Update Second Child & Multiple Class Discounts</h4>
        </div>
        <div class="modal-body">
            <br>
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-2">
                    <div class="parsley-row form-group">
                        <label_ for="title[]" style = "font-weight: bold">Multiple Classes Discount<span class="req">*</span></label>
                        {{Form::text('editMulClass', null,array('id'=>'editMulClass',
                        'required', 'class' => 'form-control input-sm md-input'))}}
                    </div>
                </div>
                <div class="uk-width-medium-1-2">
                    <div class="parsley-row form-group">
                        <label_ for="title[]" style = "font-weight: bold">Second Child Discount<span class="req">*</span></label>
                        {{Form::text('editMulChild', null,array('id'=>'editMulChild',
                        'required', 'class' => 'form-control input-sm md-input'))}}
                    </div>
                </div>
                <br><br>
            </div>
        </div>
        <div class="modal-footer ">

          <button type="button" class="btn btn-primary" id='Class_child_disc_update' >Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

        </div>
    </div>
</div>
</div>


@stop