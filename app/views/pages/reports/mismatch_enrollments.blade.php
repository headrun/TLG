@extends('layout.master')
@section('libraryCSS')
	<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all"> -->
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
    <link href='{{url()}}/assets/css/bootstrap.min.css' rel='stylesheet' />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- <link href="https://cdn.datatables.net/buttons/1.2.0/css/buttons.dataTables.min.css" rel="stylesheet"> -->
@stop

@section('libraryJS')
<script src="{{url()}}/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="{{url()}}/bower_components/datatables-colvis/js/dataTables.colVis.js"></script>
<script src="{{url()}}/bower_components/datatables-tabletools/js/dataTables.tableTools.js"></script>
<script src="{{url()}}/assets/js/custom/datatables_uikit.min.js"></script>
<script src="{{url()}}/assets/js/pages/plugins_datatables.min.js"></script>

 <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/dataTables.buttons.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.0/js/buttons.html5.min.js"></script>
 <script src="{{url()}}/assets/js/kendoui_custom.min.js"></script>
<script src="{{url()}}/assets/js/pages/kendoui.min.js"></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
<script type="text/javascript">

/*  $(document).ready(function(){
    $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/getMisMatchReports')}}",
        data: {},
        dataType: 'json',
        success: function(response){
          if (response.status == 'success') {
            var data = '';
            var header_data="<div class='md-card-content'>"+
                                "<div class='uk-overflow-container'>"+
                            "<table id='reportTable' class='uk-table'>"+
                            "<thead>"+
                            '<tr>'+
                            '<th>Student Id</th>'+
                            '<th>Enrollment start date</th>'+
                            '<th>Enrollment end date</th>'+
                            '<th>Selected Sessions</th>'+
                            '</tr></thead>';
            for(var i=0;i<response.data.length;i++){

                header_data+="<tr><td>"+response.data[i]['student_id']+"</td><td>"+
                          response.data[i]['enrollment_start_date']+"</td><td>"+
                          response.data[i]['enrollment_end_date']+"</td><td>"+
                          response.data[i]['selected_sessions']+"</td></tr>";
            }
            
            header_data+="</table></div></div>";
            console.log(header_data);
            $('#reportdata').html(header_data);
            $('#reportTable').DataTable();
          } else {
            $('.runScript').addClass('disabled');
          }
        }
    });
});  */

$(document).ready(function(){
    $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/getMisMatchReports')}}",
        data: {},
        dataType: 'json',
        success: function(response){
          if (response.status == 'success') {
            var data = '';
            var data_loop = response.data;
            var finalData = [];
            var dataForStudent = [];
            for (var key in data_loop) {
               var arr = data_loop[key];
               var dataForStudent = [];
               for( var i = 0; i < arr.length; i++ ) {
                   var obj = arr[ i ];
                   dataForStudent['id' + '_' + i] = obj.id;
                   dataForStudent['customer_name' + '_' + i] = obj.customer_name;
                   dataForStudent['customer_email' + '_' + i] = obj.customer_email;
                   dataForStudent['mobile_no' + '_' + i] = obj.mobile_no;
                   dataForStudent['student_name' + '_' + i] = obj.student_name;
                   dataForStudent['age' + '_' + i] = obj.age;
                   dataForStudent['class_name' + '_' + i] = obj.class_name;
                   dataForStudent['start_date' + '_' + i] = obj.enrollment_start_date;
                   dataForStudent['end_date' + '_' + i] = obj.enrollment_end_date;
                   dataForStudent['course_name' + '_' + i] = obj.course_name;
                   dataForStudent['selected_sessions' + '_' + i] = obj.selected_sessions;
               }
               finalData[key] = dataForStudent;
            }
            var header_data="<div class='md-card-content'>"+
                                "<div class='uk-overflow-container'>"+
                            "<table id='reportTable' class='uk-table'>"+
                            "<thead>"+
                            '<tr>'+
                            '<th>Customer Name</th>'+
                            '<th>Customer Email</th>'+
                            '<th>Mobile no</th>'+
                            '<th>Student Name</th>'+
                            '<th>Age</th>'+
                            '<th>current start date</th>'+
                            '<th>current end date</th>'+
                            '<th>selected sessions</th>'+
                            '<th>current class name</th>'+
                            '<th>course name</th>'+
                            '<th>start date 4</th>'+
                            '<th>start date 3</th>'+
                            '<th>start date 2</th>'+
                            '<th>start date 1</th>'+
                            '</tr></thead>';
            for (var key in finalData) {
              header_data+="<tr><td>"+finalData[key]['customer_name_0']+"</td><td>"+
                        finalData[key]['customer_email_0']+"</td><td>"+
                        finalData[key]['mobile_no_0']+"</td><td>"+
                        finalData[key]['student_name_0']+"</td><td>"+
                        finalData[key]['age_0']+"</td><td>"+
                        finalData[key]['start_date_0']+"</td><td>"+
                        finalData[key]['end_date_0']+"</td><td>"+
                        finalData[key]['selected_sessions_0']+"</td><td>"+
                        finalData[key]['class_name_0']+"</td><td>"+
                        finalData[key]['course_name_0']+"</td><td>"+
                        finalData[key]['start_date_1']+"</td><td>"+
                        finalData[key]['start_date_2']+"</td><td>"+
                        finalData[key]['start_date_3']+"</td><td>"+
                        finalData[key]['start_date_4']+"</td></tr>";
            }
            header_data+="</table></div></div>";
            console.log(header_data);
            $('#reportdata').html(header_data);
            $("#reportTable").DataTable({
                dom: 'Bfrtip',
                    buttons: [
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5'
                    ],
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                   },
                   "iDisplayLength": 50,
                   "lengthMenu": [ 10, 50, 100, 150, 200 ]
               });
          } else {
            $('.runScript').addClass('disabled');
          }
        }
    });
});

$('.runScript').click(function () {
  $('.runScript').addClass('disabled');
  $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/updateEnrollmentEndDate')}}",
      data: {},
      dataType: 'json',
      success: function(response){
        if (response.status == 'success') {
          alert('Script exicuted successfully.Click Ok to reload');
          window.location.reload(1);
        }
      }
  });
})

</script>
@stop

@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">mismatch</a></li>
		<li><a href="#" style="z-index:7;">enrollments</a></li>
	</ul>
    
</div>
<div class="row">
  <div class="parsley-row" style="padding: 25px 30px;" align="right">
     <button type="button" class="md-btn md-btn-primary runScript">RUN SCRIPT</button>
  </div>
</div>
<br clear="all"/>
<br clear="all"/>
<div class="">
  <div class="row"> 
     <br clear="all">
      <div class="md-card uk-margin-medium-bottom" id="reportdata">
          <div class="md-card-content" >
              <div class="uk-overflow-container">
                  <table id="reportTable" class="uk-table">
                      <!-- Table Data -->         
                  </table>
              </div>
                 
          </div>
      </div>
     </div>
  </div>
<!--    </div>
</div>-->
        
@stop
