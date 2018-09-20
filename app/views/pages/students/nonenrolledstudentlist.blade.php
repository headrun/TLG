@extends('layout.master')

@section('libraryCSS')
	<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all"> -->
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all">
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
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
	$("#studentsTable").DataTable({
		dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
				window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 50,
        "lengthMenu": [ 10, 50, 100, 150, 200 ]
    });

	/* $("#studentsTable tr").click(function (){

		window.location = $(this).find('a').attr('href');
	}) */
		

</script>  -->
<script type="text/javascript">
        $("#studentsTable").DataTable({
                dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5', 
            'pdfHtml5'
        ],
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
    
            // Bind click event 
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
                                window.location = $(this).find('a').attr('href');
                  //OR

                // window.open(aData.url);

            });

            return nRow;
        },
        "iDisplayLength": 50,
        "lengthMenu": [ 10, 50, 100, 150, 200 ]
    });

        /* $("#studentsTable tr").click(function (){

                window.location = $(this).find('a').attr('href');
        }) */
                

</script>
@stop


@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="{{url()}}/students/nonenrolled" style="z-index:8;">NonEnrolled Students</a></li>
		
	</ul>
</div>
<br clear="all"/>
<div class="">
	<div class="row">
	
		
	
            
            
		            <h3 class="heading_b uk-margin-bottom">NonEnrolled Students</h3>
		            
		            <?php 
		            	/*  echo "<pre>";
		            	print_r($customers);
		            	echo "</pre>";  */
		            
		            ?>
		
		           
		            <div class="md-card uk-margin-medium-bottom">
		                <div class="md-card-content">
		                    <div class="uk-overflow-container">
		                        <table class="uk-table table-striped" id="studentsTable">
		                            <!-- <caption>Table caption</caption> -->
		                            <thead>
		                            <tr>
		                                <th>Name</th>
		                                <th>Gender</th>
		                                <th>Age</th>
		                                <th>Date Of Birth</th>
						<th>Last Enrollment End Date</th>
		                                <!-- <th>Action</th> -->
		                            </tr>
		                            </thead>
		                            <tbody>
                                            <?php if(isset($students)){ ?>
                                            
                                           @foreach($students as $student)
		                           <tr>
		                                <td>{{$student->student_name}}</td>
		                                <td>{{$student->student_gender}}</td>
		                                <td><?php echo date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->y.'.'.date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->m.'years';?> </td>
		                                <td>
                                                    {{$student->student_date_of_birth}}
		                                </td>
						<td>{{ $student->end_date }}
						   <a  style="display: none" href="{{url()}}/students/view/{{$student->id}}"></a>
		                                </td>	
		                                
		                            </tr>
		                            @endforeach 
                                            <?php }?>
		                            </tbody>
		                        </table>
		                    </div>
		                </div>
		            </div>
			
		
		
		
		
		
	</div><!-- row -->
</div><!-- Container -->
<!--- <div class="md-fab-wrapper">
<a class="md-fab md-fab-accent" href="{{url()}}/customers/add" title="Add customers">
<i class="material-icons">&#xE03B;</i>
</a>
</div>  --->
@stop
