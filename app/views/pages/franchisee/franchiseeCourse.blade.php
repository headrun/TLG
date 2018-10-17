@extends('layout.master')

@section('libraryCSS')
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>

    <style type="text/css">

    </style>
@stop


@section('libraryJS')
<script src="{{url()}}/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="{{url()}}/bower_components/datatables-colvis/js/dataTables.colVis.js"></script>
<script src="{{url()}}/bower_components/datatables-tabletools/js/dataTables.tableTools.js"></script>
<script src="{{url()}}/assets/js/custom/datatables_uikit.min.js"></script>
<script src="{{url()}}/assets/js/pages/plugins_datatables.min.js"></script>
<script src="{{url()}}/assets/js/kendoui_custom.min.js"></script>
<script src="{{url()}}/assets/js/pages/kendoui.min.js"></script>

<script type="text/javascript">
  var franchiseeIdForAddCourse = '';
  $(document).on('click', '#addCourse', function (e) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/updateCoursesForFranchisee')}}",
      data: {
         'franchiseeId': franchiseeIdForAddCourse,
         'coursId': $('#val_select').val()
      },
      dataType: 'json',
      success: function(response){
        if(response.status === "success"){
          if(response.status === "success"){
            $('#NewFranchiseeMsgDiv').html("<p class='uk-alert uk-alert-success'>New Course has been added successfully.Please wait untill the page reloads</p>");
                // $('#newFranchiseeLoading').show();
                setTimeout(function(){
                window.location.reload(1);
                }, 4000);
          } else {
            $('#NewFranchiseeMsgDiv').html("<p class='uk-alert uk-alert-warning'>New course not yet created.Please try again.</P>");
          }
        }
      }
    });
  });
  $(document).on('click', '#courseFNameForAddCourse', function () {
    var franchiseeId = parseInt($(this).val());
    franchiseeIdForAddCourse = franchiseeId;
  })

  $(document).on('change', '#courseFName', function () {
    var franchiseeId = parseInt($(this).val());
    $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/getCoursesFranchiseeWise')}}",
      data: {
         'franchiseeId': franchiseeId,
      },
      dataType: 'json',
      success: function(response){
        if(response.status === "success"){
           var header_data="<div class='uk-overflow-container'>"+
                       "<table id='reportTable' class='uk-table'>"+
                       "<thead>"+
                       '<tr>'+
                       '<th>Course Name</th>'+
                       '<th>Slug</th>'+
                       '</tr></thead>';
           for(var i=0;i<response.courseForSelectedFranchisee.length;i++){
                header_data+="<tr><td>"+response.courseForSelectedFranchisee[i]['course_name']+"</td><td>"+
                response.courseForSelectedFranchisee[i]['slug']+"</td></tr>";
            }
           header_data+="</table></div>";
           $('#reportdata').html(header_data);
        }
      }
    });
  });
</script>
@stop

@section('content')
    <div id="newFranchiseeLoading" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 42%; left: 41%;font-size:18px;">
        <img src="{{url()}}/assets/img/spinners/load3.gif" style="width:13%;">
         Course is added successfully.Please wait . . .
        </p>
    </div>
    <div class="row">
      <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
          <div class="uk-width-medium-1-1">
              <div id="NewFranchiseeMsgDiv"></div>
              <div class="md-card">
                  <div class="md-card-content">
                    <div class="row">
                      <div class="col-lg-6">
                        <h4 style="color:#d3d3de;float:right;">Add Courses</h4>
                      </div>
                    </div>
                    <hr>
                    {{ Form::open(array('url' => '', 'id'=>"form_course_name_list", "class"=>"uk-form-stacked", 'method' => 'post')) }}
                    <div class="uk-grid" data-uk-grid-margin>
                      <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Select Franchisee * :</label>
                      <div class="uk-width-medium-1-4">
                        <div class="parsley-row form-group">
                          <select  class="form-group courseFName  input-sm md-input" id="courseFNameForAddCourse" style="padding:0px;width:100%">
                          @foreach($franchiseelist as $franchisee) 
                              <option value="{{$franchisee->id}}">{{$franchisee->franchisee_name}}</option>
                          @endforeach
                          </select>
                        </div>
                      </div>
                      <label class="uk-width-medium-1-5" style="text-align:right;padding-top:7px;">Select Course * :</label>
                      <div class="uk-width-medium-1-4">
                        <div class="parsley-row form-group">
                          {{ Form::select('masterCourseList', array('' => 'Please Select Master Course')+ $courseList,null ,array('id'=>'val_select', 'required', 'data-md-selectize', 'style'=>'width:250px;')) }}
                        </div>
                      </div>
                    </div>
                    <div class="row" align="right" style="padding-top:30px;">
                        <div class="col-lg-6">
                          <button type="submit" class="md-btn md-btn-primary" id="addCourse">Add Course</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                  </div>
                </div>
          </div>
        </div>
    </div>
    <div class="row">
        <div class="md-card">
            <div class="md-card-content large-padding">
                <div class="row">
                  <div class="col-lg-6">
                    <h4 style="color:#d3d3de;float:right;">View Courses</h4>
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

@stop