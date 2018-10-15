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
;(function($) {
    $(document).ready(function() {
    $('#reportGenerateStartdate, #reportGenerateStartdate1').kendoDatePicker( {format: "yyyy-MM-dd"});
    $('#reportGenerateenddate, #reportGenerateenddate1').kendoDatePicker({format: "yyyy-MM-dd"});
    $('#reportGenerateStartdate, #reportGenerateStartdate1').val('{{$presentdate}}');
    $('#reportGenerateenddate, #reportGenerateenddate1').val('{{$presentdate}}');
    $('#reportStartDate').kendoDatePicker( {format: "yyyy-MM-dd"});
    $('#reportEndDate').kendoDatePicker( {format: "yyyy-MM-dd"});
    $('#reportStartDate').val('{{$presentdate}}');
    $('#reportEndDate').val('{{$presentdate}}');
    $('#reportType').val('Birthday');
    
    
    $.ajax({
			type: "POST",
			url: "{{URL::to('/quick/generatereport')}}",
                        data: $('#generatereportform').serialize(),
			dataType: 'json',
			success: function(response){
                            console.log(response);
                            var data='';
                             if(response[1]==='both'){
                                var header_data="<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Batch Name</th>'+
                                            '<th>Membership Amount</th>'+
                                            '<th>Birthday Amount</th>'+
                                            '<th>Enrollment Amount</th>'+
                                            '<th>Transaction Date</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                  var membership_amt = response[0]['data'][i]['membership_amount'] == null ? 0 : response[0]['data'][i]['membership_amount'];
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>";
                                          if(typeof(response[0]['data'][i]['batch_name']) == 'undefined'){
                                            header_data+="birthday"+"</td><td>";  
                                          }else{
                                            header_data+=response[0]['data'][i]['batch_name']+"</td><td>";
                                          }
                                    header_data+= membership_amt +"</td><td>";
                                          if(typeof(response[0]['data'][i]['batch_name']) == 'undefined'){
                                            header_data+=response[0]['data'][i]['payment_due_amount']+"</td><td>";
                                          }else{
                                            header_data+="0"+"</td><td>";
                                          }
                                          
                                          header_data+=response[0]['data'][i]['payment_due_amount_after_discount']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
                                   
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
                                                /*$(nRow).click(function() {
                                                  window.location = $(this).find('a').attr('href');
                                                });

                                                return nRow;*/
                                           },
                                           "iDisplayLength": 50,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                            }
                        }
             }); 
    
    });

    $('#generatereportform').submit(function(event){
        event.preventDefault();
       $.ajax({
			type: "POST",
			url: "{{URL::to('/quick/generatereport')}}",
                        data: $('#generatereportform').serialize(),
			dataType: 'json',
			success: function(response){
                            console.log(response);
                            var data='';
                            if(response[1]==='Birthday'){
                            var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Birthday Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Membership Amount</th>'+
                                            '<th>Birthday Amount</th>'+
                                            '<th>Transaction Date</th>'+
                                            '</tr></thead>';                                
                            
                            for(var i=0;i<response[0]['data'].length;i++){
                              var membership_amt = response[0]['data'][i]['membership_amount'] == null ? 0 : response[0]['data'][i]['membership_amount'];
                              var payment_due_amt = response[0]['data'][i]['payment_due_amount'] == null ? 0 : response[0]['data'][i]['payment_due_amount'];
                             header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          membership_amt+"</td><td>"+
                                          payment_due_amt+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
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
                                                /*$(nRow).click(function() {
                                                    window.location = $(this).find('a').attr('href');
                                                      
                                                });
                                                return nRow;*/
                                           },
                                           "iDisplayLength": 10,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                                    
                            }else if(response[1]==='Enrollment'){

                                var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Enrollment Report</h3>"+
                                                "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Batch Name</th>'+
                                            '<th>Slected Sessions</th>'+
                                            '<th>Membership Amount</th>'+
                                            '<th>Enrollment Amount</th>'+
                                            '<th>Transaction Date</th>'+
					    '<th>Source of Enrollment</th>'+
                                            '</tr></thead>';
                                    
                                    for(var i=0;i<response[0]['data'].length;i++){
                                      var membership_amt = response[0]['data'][i]['membership_amount'] == null ? 0 : response[0]['data'][i]['membership_amount'];
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          response[0]['data'][i]['batch_name']+"</td><td>"+
                                          response[0]['data'][i]['selected_sessions']+"</td><td>"+
                                          membership_amt +"</td><td>"+
                                          response[0]['data'][i]['payment_due_amount_after_discount']+"</td><td>"+
					  response[0]['data'][i]['created_at']+"</td><td>"+
                                          response[0]['data'][i]['source']+"</td></tr>";
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
                                                /*$(nRow).click(function() {
                                                    window.location = $(this).find('a').attr('href');
                                                });
                                                return nRow;*/
                                           },
                                           "iDisplayLength": 10,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                            
                            }else if(response[1]==='both'){
                                var header_data="<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Batch Name</th>'+
                                            '<th>Membership Amount</th>'+
                                            '<th>Birthday Amount</th>'+
                                            '<th>Enrollment Amount</th>'+
                                            '<th>Transaction Date</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                  var membership_amt = response[0]['data'][i]['membership_amount'] == null ? 0 : response[0]['data'][i]['membership_amount'];
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>";
                                          if(typeof(response[0]['data'][i]['batch_name']) == 'undefined'){
                                            header_data+="birthday"+"</td><td>";  
                                          }else{
                                            header_data+=response[0]['data'][i]['batch_name']+"</td><td>";
                                          }
                                          header_data+= membership_amt+"</td><td>";
                                          if(typeof(response[0]['data'][i]['batch_name']) == 'undefined'){
                                            header_data+=response[0]['data'][i]['payment_due_amount']+"</td><td>";
                                          }else{
                                            header_data+="0"+"</td><td>";
                                          }
                                          
                                          header_data+=response[0]['data'][i]['payment_due_amount_after_discount']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
                                   
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
                                                /*  $(nRow).click(function() {
                                            window.location = $(this).find('a').attr('href');
                                                });
                                                return nRow;  */
                                           },
                                           "iDisplayLength": 10,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                                
                            }else if(response[1]==='Membership'){
                                var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Membership Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Membership Amount</th>'+
                                            '<th>Transaction Date</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          response[0]['data'][i]['membership_amount']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
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
                                                /*  $(nRow).click(function() {
                                                window.location = $(this).find('a').attr('href');

                                                });
                                                return nRow;  */
                                           },
                                           "iDisplayLength": 10,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                                
                            }else if(response[1]==='Introvisit'){
                                var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Introvisit Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Batch Name</th>'+
                                            '<th>IV Date</th>'+
                                            '<th>IV Status</th>'+
                                            '<th>Registered Date</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          response[0]['data'][i]['batch_name']+"</td><td>"+
                                          response[0]['data'][i]['iv_date']+"</td><td>"+
                                          response[0]['data'][i]['status']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
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
                                                /*$(nRow).click(function() {
                                                  window.location = $(this).find('a').attr('href');
                                                });
                                                return nRow;*/
                                           },
                                           "iDisplayLength": 10,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                                
                            }else if(response[1]==='Inquiry'){
                            var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Inquiry Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Inquiry Date</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0].data.length;i++){
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
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
                                                /*$(nRow).click(function() {
                                                  window.location = $(this).find('a').attr('href');
                                                });

                                                return nRow;*/
                                           },
                                           "iDisplayLength": 10,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                            }else if(response[1]==='Customer_mails'){
                            var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Customer mails Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Mobile No</th>'+
                                            '<th>Email Address</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0].length;i++){
                                      header_data+="<tr><td>"+response[0][i]['customer_name']+"</td><td>"+
                                      response[0][i]['mobile_no']+"</td><td>"+
                                      response[0][i]['customer_email']+"</td></tr>";
                                }
                                header_data+="</table></div></div>";
                                $('#reportdata').html(header_data);
                                $("#reportTable").DataTable({
                                    dom: 'Bfrtip',
                                        buttons: [
                                            'excelHtml5',
                                            'csvHtml5',
                                            'pdfHtml5'
                                        ],
                                        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                                            /*$(nRow).click(function() {
                                              window.location = $(this).find('a').attr('href');
                                            });

                                            return nRow;*/
                                       },
                                       "iDisplayLength": 10,
                                       "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                   });
                            }else if(response[1]==='Renewal_due'){
                            var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Total Renewal's Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Kid Name</th>'+
                                            '<th>Last enrollment end date</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                      header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                      response[0]['data'][i]['student_name']+"</td><td>"+
                                      response[0]['data'][i]['end_order_date']+"</td></tr>";
                                }
                                header_data+="</table></div></div>";
                                $('#reportdata').html(header_data);
                                $("#reportTable").DataTable({
                                    dom: 'Bfrtip',
                                        buttons: [
                                            'excelHtml5',
                                            'csvHtml5',
                                            'pdfHtml5'
                                        ],
                                        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                                            /*$(nRow).click(function() {
                                              window.location = $(this).find('a').attr('href');
                                            });

                                            return nRow;*/
                                       },
                                       "iDisplayLength": 10,
                                       "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                   });
                            }else if(response[1]==='Renewal_done'){
                            var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Renewal's done Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Kid Name</th>'+
                                            '<th>Transaction Date</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                      header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                      response[0]['data'][i]['student_name']+"</td><td>"+
                                      response[0]['data'][i]['created_at']+"</td></tr>";
                                }
                                header_data+="</table></div></div>";
                                $('#reportdata').html(header_data);
                                $("#reportTable").DataTable({
                                    dom: 'Bfrtip',
                                        buttons: [
                                            'excelHtml5',
                                            'csvHtml5',
                                            'pdfHtml5'
                                        ],
                                        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                                            /*$(nRow).click(function() {
                                              window.location = $(this).find('a').attr('href');
                                            });

                                            return nRow;*/
                                       },
                                       "iDisplayLength": 10,
                                       "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                   });
                            }else if(response[1]==='Renewal_pending'){
                            var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Renewal's pending Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Kid Name</th>'+
                                            '<th>Enrollment end date</th>'+
                                            '</tr></thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                      header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                      response[0]['data'][i]['student_name']+"</td><td>"+
                                      response[0]['data'][i]['end_order_date']+"</td></tr>";
                                }
                                header_data+="</table></div></div>";
                                $('#reportdata').html(header_data);
                                $("#reportTable").DataTable({
                                    dom: 'Bfrtip',
                                        buttons: [
                                            'excelHtml5',
                                            'csvHtml5',
                                            'pdfHtml5'
                                        ],
                                        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                                            /*$(nRow).click(function() {
                                              window.location = $(this).find('a').attr('href');
                                            });

                                            return nRow;*/
                                       },
                                       "iDisplayLength": 10,
                                       "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                   });
                            }else if(response[1]==='Calls'){
                                var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Calls Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Kid Name</th>'+
                                            '<th>Type of Call</th>'+
                                            '<th>Comment</th>'+
                                            '<th>Reminder Date</th>'+
                                            '<th>Status</th>'+
                                            '</tr>'+
                                            '</thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          response[0]['data'][i]['followup_type']+"</td><td>"+
                                          response[0]['data'][i]['log_text']+"</td><td>"+
                                          response[0]['data'][i]['reminder_date']+"</td><td>"+
                                          response[0]['data'][i]['followup_status']+"</td></tr>";
                                    }
                                    header_data+="</table></div></div>";
                                    console.log(header_data);
                                    $('#reportdata').html(header_data);
                                    $("#reportTable").DataTable({
                                        "ordering": false,
                                        dom: 'Bfrtip',
                                            buttons: [
                                                'excelHtml5',
                                                'csvHtml5',
                                                'pdfHtml5'
                                            ],
                                            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                                                /*$(nRow).click(function() {
                                                  window.location = $(this).find('a').attr('href');
                                                });

                                                return nRow;*/
                                           },
                                           "iDisplayLength": 10,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                            }else if(response[1]==='Calls_Made'){
                                var header_data="<h3 style='padding-top:20px;padding-left:20px;'>Calls Made Report</h3>"+
                                            "<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Kid Name</th>'+
                                            '<th>Type of Call</th>'+
                                            '<th>Schedule Date</th>'+
                                            '<th>Comment</th>'+
                                            '<th>Status</th>'+
					    '<th>Created At</th>'+
                                            '</tr>'+
                                            '</thead>';
                                for(var i=0;i<response[0]['data'].length;i++){
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          response[0]['data'][i]['followup_type']+"</td><td>"+
                                          response[0]['data'][i]['reminder_date']+"</td><td>"+
                                          response[0]['data'][i]['log_text']+"</td><td>"+
					  response[0]['data'][i]['followup_status']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
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
                                                /*$(nRow).click(function() {
                                                  window.location = $(this).find('a').attr('href');
                                                });

                                                return nRow;*/
                                           },
                                           "iDisplayLength": 10,
                                           "lengthMenu": [ 10, 50, 100, 150, 200 ]
                                       });
                            }else if(response[1]==='BySchool'){
                            
                                var header_data="<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Batch Name</th>'+
                                            '<th>Membership Amount</th>'+
                                            '<th>Enrollment Amount</th>'+
                                            '<th>Transaction Date</th>'+
                                            '</tr></thead>';
                                    
                                    for(var i=0;i<response[0]['data'].length;i++){
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          response[0]['data'][i]['batch_name']+"</td><td>"+
                                          response[0]['data'][i]['membership_amount']+"</td><td>"+
                                          response[0]['data'][i]['payment_due_amount_after_discount']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
                                    }
                                    header_data+="</table></div></div>";
                                    //console.log(header_data);
                                    $('#reportdata').html(header_data);
                                    $('#reportTable').DataTable();
                            }else if(response[1]==='ByLocality'){
                                                           
                                var header_data="<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Batch Name</th>'+
                                            '<th>Membership Amount</th>'+
                                            '<th>Enrollment Amount</th>'+
                                            '<th>Transaction Date</th>'+
                                            '</tr></thead>';
                                    
                                    for(var i=0;i<response[0]['data'].length;i++){
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          response[0]['data'][i]['batch_name']+"</td><td>"+
                                          response[0]['data'][i]['membership_amount']+"</td><td>"+
                                          response[0]['data'][i]['payment_due_amount_after_discount']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
                                    }
                                    header_data+="</table></div></div>";
                                    //console.log(header_data);
                                    $('#reportdata').html(header_data);
                                    $('#reportTable').DataTable();
 
                            }else if(response[1]==='ByApartment'){
                                                           
                                var header_data="<div class='md-card-content'>"+
                                                "<div class='uk-overflow-container'>"+
                                            "<table id='reportTable' class='uk-table'>"+
                                            "<thead>"+
                                            '<tr>'+
                                            '<th>Customer Name</th>'+
                                            '<th>Student Name</th>'+
                                            '<th>Batch Name</th>'+
                                            '<th>Membership Amount</th>'+
                                            '<th>Enrollment Amount</th>'+
                                            '<th>Transaction Date</th>'+
                                            '</tr></thead>';
                                    
                                    for(var i=0;i<response[0]['data'].length;i++){
                                    header_data+="<tr><td>"+response[0]['data'][i]['customer_name']+"</td><td>"+
                                          response[0]['data'][i]['student_name']+"</td><td>"+
                                          response[0]['data'][i]['batch_name']+"</td><td>"+
                                          response[0]['data'][i]['membership_amount']+"</td><td>"+
                                          response[0]['data'][i]['payment_due_amount_after_discount']+"</td><td>"+
                                          response[0]['data'][i]['created_at']+"</td></tr>";
                                    }
                                    header_data+="</table></div></div>";
                                    console.log(header_data);
                                    $('#reportdata').html(header_data);
                                    $('#reportTable').DataTable();
 
                            }
                        }
             });  
              //$("#ReportTable").DataTable();
    });
    $('#reportType').change(function(){
        if(($('#reportType').val()==='BySchool')||($('#reportType').val()==='ByLocality')||($('#reportType').val()==='ByApartment')){
            
            if($('#reportType').val()==='BySchool'){
                $('#reportoption').html('Select School');
                $('#reportOptionSelect').empty();
                 $.ajax({
			type: "POST",
			url: "{{URL::to('/quick/getUniqueSchoolNames')}}",
                        data: {},
			dataType: 'json',
			success: function(response){
                            if(response.status==='success'){
                                var data='';
                                for(var i=0;i<response.data.length;i++){
                                    data+="<option value='"+response.data[i]['school']+"'>"+response.data[i]['school']+"</option>";
                                }
                                $('#reportOptionSelect').html(data);
                            }
                        }
                 });
                
            }
            if($('#reportType').val()==='ByLocality'){
                $('#reportoption').html('Select Locality');
                $('#reportOptionSelect').empty();
                 $.ajax({
			type: "POST",
			url: "{{URL::to('/quick/getUniqueLocalityNames')}}",
                        data: {},
			dataType: 'json',
			success: function(response){
                            if(response.status==='success'){
                                var data='';
                                for(var i=0;i<response.data.length;i++){
                                    data+="<option value='"+response.data[i]['locality']+"'>"+response.data[i]['locality']+"</option>";
                                }
                                $('#reportOptionSelect').html(data);
                            }
                        }
                 });
            }
            if($('#reportType').val()==='ByApartment'){
                $('#reportoption').html('Select Apartment');
                $('#reportOptionSelect').empty();
                 $.ajax({
			type: "POST",
			url: "{{URL::to('/quick/getUniqueApartmentNames')}}",
                        data: {},
			dataType: 'json',
			success: function(response){
                            if(response.status==='success'){
                                var data='';
                                for(var i=0;i<response.data.length;i++){
                                    data+="<option value='"+response.data[i]['apartment_name']+"'>"+response.data[i]['apartment_name']+"</option>";
                                }
                                $('#reportOptionSelect').html(data);
                            }
                        }
                 });
            }
            $('.reportdynamic').css('display','block');
            
            
        }else{
            $('#reportoption').empty();
            $('.reportdynamic').css('display','none');
        }
    });
})(jQuery);

$(document).on('click', '#activityReport', function(){

    var start_date = $('#reportStartDate').val();
    var end_date = $('#reportEndDate').val();

    if (typeof start_date !== 'undefined' && typeof end_date !== 'undefined' ) {
        $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/activityReport')}}",
            data: {'reportStartDate': start_date, 'reportEndDate': end_date},
            dataType: 'json',
            success: function(response){
                    var data = '';
                    var header_data="<div class='md-card-content'>"+
                                        "<div class='uk-overflow-container'>"+
                                    "<table id='reportTable' class='uk-table'>"+
                                    "<thead>"+
                                    '<tr>'+
                                    '<th>Customer Name</th>'+
                                    '<th>Student Name</th>'+
                                    '<th>Type of Activity</th>'+
                                    '<th>Schedule Date</th>'+
                                    '</tr></thead>';
                            for(var i=0;i<response.data.length;i++){

                                header_data+="<tr><td>"+response.data[i]['customer_name']+"</td><td>"+
                                          response.data[i]['student_name']+"</td><td>"+
                                          response.data[i]['payment_due_for']+"</td><td>"+
                                          response.data[i]['created_at']+"</td></tr>";
                                    
                            }
                            
                            header_data+="</table></div></div>";
                            console.log(header_data);
                            $('#reportdata').html(header_data);
                            $('#reportTable').DataTable();
                    
                
            }
        });
    }
});

$(document).on('click', '.salse_alloc_btn', function(){

    var start_date = $('#reportGenerateStartdate1').val();
    var end_date = $('#reportGenerateenddate1').val();

    if (typeof start_date !== 'undefined' && typeof end_date !== 'undefined' ) {

        $.ajax({

            type: "POST",
            url: "{{URL::to('/quick/salesAllocreport')}}",
            data: {'reportGenerateStartdate1': start_date, 'reportGenerateEnddate1': end_date},
            dataType: 'json',
            success: function(response){
                if(response.status === "success"){

                    window.open(response.data, '_blank');
                } 
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
		<li><a href="#" style="z-index:8;">Reports</a></li>
		<li><a href="#" style="z-index:7;">Enrollment</a></li>
	</ul>
    
</div>
<br clear="all"/>
<br clear="all"/>
<div class="">
    <div class="row">
        
    

<!-- <div class="md-card">
	    <div class="md-card-content large-padding">-->
<!--               <center>
		<h3 class="heading_b uk-margin-bottom">Enrollment and Birthday Report</h3>
               </center>-->
                <div class="md-card uk-margin-medium-bottom">
		    <div class="md-card-content">
                        <br>
                        <!-- <h3 class="heading_b uk-margin-bottom">Activity Report</h3> -->
                        {{------ {{ Form::open(array('url' => '/reports/activityReport', 'id'=>"activityReportform", "class"=>"uk-form-stacked", 'method' => 'post')) }} 
                          <div class="uk-grid" data-uk-grid-margin>
                              <div class="uk-width-medium-1-4">
                                <div class="parsley-row form-group">
                                  <label for="selectDate">Start Date</label><br>
                                    {{Form::text('reportStartDate',
            null,array('id'=>'reportStartDate', 'class' => '','required'))}} 
                                </div>
                              </div>
                              <div class="uk-width-medium-1-4">
                               <div class="parsley-row form-group">
                                        <label for="endDate">End Date</label><br>
                                            {{Form::text('reportEndDate',
            null,array('id'=>'reportEndDate', 'class' => '','required'))}} 
                               </div>
                            </div>

                              <div class="uk-width-1-4">
                                <div class="parsley-row" style="padding: 25px 30px;">
                                  <button type="button" class="md-btn md-btn-primary" id="activityReport">Generate</button>
                                </div>
                              </div>
                            </div>
                        {{ Form::close() }} -------}}
                        <h3 class="heading_b uk-margin-bottom">General Report</h3>
                        {{ Form::open(array('url' => '/reports/generatereport', 'id'=>"generatereportform", "class"=>"uk-form-stacked", 'method' => 'post')) }}    
                           <div class="uk-grid" data-uk-grid-margin>
                               <div class="uk-width-medium-1-4">
                                   <div class="parsley-row form-group">
                                        <label for="startDate">Start Date</label><br>
                                            {{Form::text('reportGenerateStartdate',
						null,array('id'=>'reportGenerateStartdate', 'class' => '','required'))}} 
                                    </div>
                                </div>
                            <div class="uk-width-medium-1-4">
                               <div class="parsley-row form-group">
                                        <label for="endDate">End Date</label><br>
                                            {{Form::text('reportGenerateEnddate',
						null,array('id'=>'reportGenerateenddate', 'class' => '','required'))}} 
                               </div>
                            </div>
                               <div class="uk-width-1-4">
                                   <div class="parsley-row form-group">
                                        <strong><label>Report Type</label></strong>
                                            <select name="reportType" id="reportType" class="input-sm md-input"
                                                    style='padding: 0px; font-weight: bold; color: #727272; width:100%'>
                                                    <option value="Birthday">Birthday</option>       
                                                    <option value="Enrollment">Enrollment</option>
                                                    <!--<option value="both">Enrollment & Birthday</option>-->
                                                    <option value="Membership">Membership</option>
                                                    <option value="Introvisit">Introvisit</option>
                                                    <option value="Inquiry">Inquiry</option>
                                                    <option value="Calls">Calls Report</option>
                                                    <option value="Calls_Made">Calls Made</option>
                                                    <option value="Customer_mails">Customer Emails</option>
                                                    <option value="Renewal_due">Total renewals</option>
                                                    <option value="Renewal_done">Renewal Done</option>
                                                    <option value="Renewal_pending">Pending Renewals</option>
                                                    <!-- <option value="BySchool">By School</option>
                                                    <option value="ByLocality">By Locality</option>
                                                    <option value="ByApartment">By Apartmnet</option> -->
                                            </select>
                                                 
                                   </div>
                               </div>
                            <div class="uk-width-1-4">
                                <div class="parsley-row form-group reportdynamic" style="display:none;">
                                    <strong><label id="reportoption"></label></strong>
                                    <select name="reportOptionSelect" id="reportOptionSelect" class="input-sm md-input"
                                        style='padding: 0px; font-weight: bold; color: #727272; width:100%'>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-4">
                                <div class="parsley-row">
                                <button type="submit" class="md-btn md-btn-primary">Generate</button>
                                </div>
                            </div>
                            <div class="uk-width-1-4"></div>
                            <div class="uk-width-1-4"></div>
                            <div class="uk-width-1-4"></div>
                            </div>
                        {{ Form::close() }}


                        <br><br>
                    
                      <h3 class="heading_b uk-margin-bottom">Sales Allocation Report</h3>
                      {{ Form::open(array('url' => '/reports/salesAllocreport', "class"=>"uk-form-stacked", 'method' => 'post')) }}    
                        <div class="uk-grid" data-uk-grid-margin>
                           <div class="uk-width-medium-1-4">
                             <div class="parsley-row form-group">
                                <label for="startDate">Start Date</label><br>
                                {{Form::text('reportGenerateStartdate1',
                                null,array('id'=>'reportGenerateStartdate1', 'class' => '','required'))}} 
                             </div>
                           </div>
                           <div class="uk-width-medium-1-4">
                             <div class="parsley-row form-group">
                                <label for="endDate">End Date</label><br>
                                {{Form::text('reportGenerateEnddate1',
                                null,array('id'=>'reportGenerateenddate1', 'class' => '','required'))}} 
                             </div>
                           </div>
                           <div class="uk-width-1-4">
                             <div class="parsley-row" style="padding: 25px 30px;">
                                <button type="button" class="md-btn md-btn-primary salse_alloc_btn">Generate</button>
                             </div>
                           </div>
                         </div>
                      {{ Form::close() }}<br>
                
               <br clear="all">
                <div class="md-card uk-margin-medium-bottom" id="reportdata">
                    <div class="md-card-content" >
                        <div class="uk-overflow-container">
                            <table id="reportTable" class="uk-table">
                                            
                            </table>
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
