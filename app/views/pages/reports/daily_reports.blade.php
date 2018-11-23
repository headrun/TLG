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
<script src="{{url()}}/assets/js/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.5.2/bluebird.core.min.js"></script>
<style type="text/css">
  .daily_rpo_img {
    margin:0px auto;
    display:block;
    background-repeat:no-repeat;
    background-image: url('{{url()}}//assets/img/logo.png');
    height:83px;
  }
</style>
<script type="text/javascript">
  $(document).ready(function() {
    $('#reportGenerateStartdate, #reportGenerateStartdate1').kendoDatePicker( {format: "yyyy-MM-dd"});
    $('#reportGenerateenddate, #reportGenerateenddate1').kendoDatePicker({format: "yyyy-MM-dd"});
    $('#reportGenerateStartdate, #reportGenerateStartdate1').val('{{$presentdate}}');
    $('#reportGenerateenddate, #reportGenerateenddate1').val('{{$presentdate}}');
    $('#reportStartDate').kendoDatePicker( {format: "yyyy-MM-dd"});
    $('#reportEndDate').kendoDatePicker( {format: "yyyy-MM-dd"});
    $('#reportStartDate').val('{{$presentdate}}');
    $('#reportEndDate').val('{{$presentdate}}');
    $('#reportType').val('dailyPhoneCalls');
  });

  var form = $('#DivIdToPrint'), 
  cache_width = form.width(),  
  a4 = [550.28, 841.89];

  function createPDF() {
      getCanvas().then(function (canvas) {  
            var img = canvas.toDataURL("image/png"),  
             doc = new jsPDF({  
                 unit: 'px',  
                 format: 'a4'  
             });  
            doc.addImage(img, 'JPEG', 20, 20);                                
            doc.save('DailyPhoneCallsReports.pdf');  
            form.width(cache_width);  
        });
  }

  function getCanvas() {  
      form.width((a4[0] * 1.33333) - 10).css('max-width', 'none');  
      return html2canvas(form, {  
          imageTimeout: 2000,  
          removeContainer: true  
      });  
  }
  $(document).on('click', '#download', function() {
    createPDF();
    setTimeout(function () {
      window.location.reload(1);
    },2000)
  });

$(document).on('click', '.daily_reportsBtn', function(){
    var start_date = $('#reportGenerateStartdate1').val();
    var reportType = $('#reportType').val();
    if (typeof start_date !== 'undefined' && start_date !== '' && reportType !== '') {
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/generateDailyReport')}}",
            data: {'start_date': start_date, 'reportType': reportType},
            dataType: 'json',
            success: function(response){
              if (response[5] == 'dailyPhoneCalls') {
                data = '';
                data = '<div style="float:right;">'+
                        '<button type="button" class="md-btn md-btn-primary" id="download"style="border-radius:5px;">Print</button>'+
                        '</div>';
                data += '<div class="row" style="margin-top:10px;margin-left:0px;margin-right:0px;">'+
                         '<div class="col-lg-4 col-md-4">'+
                           '<center><div class="daily_rpo_img"></div></center>'+
                         '</div>'+
                         '<div class="col-lg-4 col-md-4">'+
                           '<center><h2>Daily Phone Calls</h2><div>'+start_date+'</div></center>'+
                         '</div>'+
                       '</div>';
                data += '<hr>';
                data += '<center><h4>Missed Yesterdays Class</h4></center>';
                if (response[0].length > 0) {
                  data += "<div class='uk-overflow-container'>"+
                            "<table class='uk-table'>"+
                              "<thead>"+
                                '<tr>'+
                                  '<th>Class Description</th>'+
                                  '<th>Student Name</th>'+
                                  '<th>Instructor Name</th>'+
                                  '<th>Parent Name</th>'+
                                  '<th>Phone Number</th>'+
                                  '<th>Email</th>'+
                                '</tr>'+
                              '</thead>';
                  for(var i=0;i<response[0].length;i++){
                    data+="<tr><td>"+response[0][i]['batch_name']+"</td><td>"+
                          response[0][i]['student_name']+"</td><td>"+
                          response[0][i]['instructor_name']+"</td><td>"+
                          response[0][i]['customer_name']+"</td><td>"+
                          response[0][i]['mobile_no']+"</td><td>"+
                          response[0][i]['email']+"</td></tr>";
                  }
                } else {
                  data += "<center><p>******* No records founds *******</p></center>"
                }
                data += "</table></div>";
                /*data += '<hr>';
                data += '<center><h3>Inquired Online But Didnt Schedule Anything</h3></center>';*/
                data += '<center><h4>Inquired Last Days But Didnt Schedule Anything</h4></center>';
                if (response[4].length > 0) {
                  data += "<div class='uk-overflow-container'>"+
                            "<table class='uk-table'>"+
                              "<thead>"+
                                '<tr>'+
                                  '<th>Parent Name</th>'+
                                  '<th>Phone Number</th>'+
                                  '<th>Email</th>'+
                                '</tr>'+
                              '</thead>';
                  for(var i=0;i<response[4].length;i++){
                    data+="<tr><td>"+response[4][i]['customer_name']+"</td><td>"+
                          response[4][i]['mobile_no']+"</td><td>"+
                          response[4][i]['email']+"</td></tr>";
                  }
                } else {
                  data += "<center><p>******* No records founds *******</p></center>"
                }
                data += "</table></div>";
                data += '<center><h4>Attended An Intro 2 Days Ago But Havent Enrolled</h4></center>';
                data += '<hr>';
                data += '<center><h4>People Who No-Showed To An Intro</h4></center>';
                if (response[3].length > 0) {
                  data += "<div class='uk-overflow-container'>"+
                            "<table class='uk-table'>"+
                              "<thead>"+
                                '<tr>'+
                                  '<th>Class Description</th>'+
                                  '<th>Student Name</th>'+
                                  '<th>Instructor Name</th>'+
                                  '<th>Parent Name</th>'+
                                  '<th>Phone Number</th>'+
                                  '<th>Email</th>'+
                                '</tr>'+
                              '</thead>';
                  for(var i=0;i<response[3].length;i++){
                    data+="<tr><td>"+response[3][i]['batch_name']+"</td><td>"+
                          response[3][i]['student_name']+"</td><td>"+
                          response[3][i]['instructor_name']+"</td><td>"+
                          response[3][i]['customer_name']+"</td><td>"+
                          response[3][i]['mobile_no']+"</td><td>"+
                          response[3][i]['email']+"</td></tr>";
                  }
                } else {
                  data += "<center><p>******* No records founds *******</p></center>"
                }
                data += "</table></div>";
                data += '<center><h4>People Who Have An Intro Tomorrow</h4></center>';
                if (response[1].length > 0) {
                  data += "<div class='uk-overflow-container'>"+
                            "<table class='uk-table'>"+
                              "<thead>"+
                                '<tr>'+
                                  '<th>Class Description</th>'+
                                  '<th>Student Name</th>'+
                                  '<th>Instructor Name</th>'+
                                  '<th>Parent Name</th>'+
                                  '<th>Phone Number</th>'+
                                  '<th>Email</th>'+
                                '</tr>'+
                              '</thead>';
                  for(var i=0;i<response[1].length;i++){
                    data+="<tr><td>"+response[1][i]['batch_name']+"</td><td>"+
                          response[1][i]['student_name']+"</td><td>"+
                          response[1][i]['instructor_name']+"</td><td>"+
                          response[1][i]['customer_name']+"</td><td>"+
                          response[1][i]['mobile_no']+"</td><td>"+
                          response[1][i]['email']+"</td></tr>";
                  }
                } else {
                  data += "<center><p>******* No records founds *******</p></center>"
                }
                data += "</table></div>";
                /*data += '<center><h3>Other Calls</h3></center>';
                data += '<hr>';*/
                data += '<center><h4>Upcoming Birthdays</h4></center>';
                if (response[2].length > 0) {
                  data += "<div class='uk-overflow-container'>"+
                            "<table class='uk-table'>"+
                              "<thead>"+
                                '<tr>'+
                                  '<th>Customer Name</th>'+
                                  '<th>Student Name</th>'+
                                  '<th>Age</th>'+
                                  '<th>Mobile No</th>'+
                                  '<th>Date of Birth</th>'+
                                  '<th>Email</th>'+
                                '</tr>'+
                              '</thead>';
                  for(var i=0;i<response[2].length;i++){
                    data+="<tr><td>"+response[2][i]['customer_name']+"</td><td>"+
                          response[2][i]['student_name']+"</td><td>"+
                          response[2][i]['age']+"</td><td>"+
                          response[2][i]['mobile_no']+"</td><td>"+
                          response[2][i]['student_date_of_birth']+"</td><td>"+
                          response[2][i]['email']+"</td></tr>";
                  }
                } else {
                  data += "<center><p>******* No records founds *******</p></center>"
                }
                data += "</table></div>";
                $('#pdfData').html(data);
              }
            }
        });
    } else {
      alert('ok');
    }
});

</script>
@stop

@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">Reports</a></li>
		<li><a href="#" style="z-index:7;">Daily Reports</a></li>
	</ul>
    
</div>
<br clear="all"/>
<br clear="all"/>
<div class="">
  <div class="row">
    <div class="md-card uk-margin-medium-bottom">
	    <div class="md-card-content">
        <br>
          <h3 class="heading_b uk-margin-bottom">Daily Reports</h3>
          {{ Form::open(array('url' => '/reports/daily_reports_data', "class"=>"uk-form-stacked", 'method' => 'post')) }}    
            <div class="uk-grid" data-uk-grid-margin>
               <div class="uk-width-1-3">
                   <div class="parsley-row form-group">
                    <strong><label>Report Type</label></strong>
                    <select name="reportType" id="reportType" class="input-sm md-input"
                      style='padding: 0px; font-weight: bold; color: #727272; width:100%'>
                      <option value="Registration_MediaReleaseWaiver">Registration/Media Release Waiver</option>       
                      <option value="dailyPhoneCalls">Daily Phone Calls</option>
                      <option value="dailyClassAvailability">Daily Class Availability</option>
                    </select>   
                   </div>
               </div>
               <div class="uk-width-medium-1-3">
                 <center>
                   <div class="parsley-row form-group">
                    <label for="startDate">Select Date</label><br>
                    {{Form::text('reportGenerateStartdate1',
                    null,array('id'=>'reportGenerateStartdate1', 'class' => '','required'))}} 
                 </div>
                 </center>
               </div>
               <div class="uk-width-1-3">
                 <center>
                   <div class="parsley-row" style="padding: 25px 30px;">
                    <button type="button" class="md-btn md-btn-primary daily_reportsBtn">Generate</button>
                 </div>
                 </center>
               </div>
             </div>
          {{ Form::close() }}<br>
          <br clear="all">
          <div class="md-card uk-margin-medium-bottom" id="reportdata">
              <div class="md-card-content" >
                  <div class="uk-overflow-container">
                      <div id="pdfData">              
                      </div>
                  </div>
              </div>
          </div>
        </div>         
      </div>
    </div>
  </div>
<!--    </div>
</div>-->
@stop
