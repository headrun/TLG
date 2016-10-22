@extends('layout.master')

@section('libraryCSS')
	<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all"> -->
	<link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
    <link rel="stylesheet" href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css"/>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' />
@stop

@section('libraryJS')
<script src="{{url()}}/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="{{url()}}/bower_components/datatables-colvis/js/dataTables.colVis.js"></script>
<script src="{{url()}}/bower_components/datatables-tabletools/js/dataTables.tableTools.js"></script>
<script src="{{url()}}/assets/js/custom/datatables_uikit.min.js"></script>
<script src="{{url()}}/assets/js/pages/plugins_datatables.min.js"></script>

<script type="text/javascript">

	$("#studentsTable").DataTable({
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {

            // Bind click event
            $(nRow).click(function() {
                  //window.open($(this).find('a').attr('href'));
	//			window.location = $(this).find('a').attr('href');
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
		<li><a href="#" style="z-index:8;">Reports</a></li>
                <li><a href="#" style="z-index:7;">Deleted Customers</a></li>
		
	</ul>
</div>
    <br clear="all"/>
    
	<div class="row">
            <h3 class="heading_b uk-margin-bottom">Deleted Customers</h3>
            
            <div class="md-card uk-margin-medium-bottom">
		<div class="md-card-content">
		    <div class="uk-overflow-container">
                        
		        <table class="uk-table table-striped" id="studentsTable" width="100%">
		                            <!-- <caption>Table caption</caption> -->
		                            <thead>
		                            <tr>
		                                <th>Customer</th>
		                                <th>Email</th>
		                                <th>Mobile No</th>
                                                <th>Alt Mobile No</th>
                                                <th>Landline No</th>
		                                <th>Address</th>
                                                <th>Deleted at</th>
		                                <!-- <th>Action</th> -->
		                            </tr>
		                            </thead>
		                            <tbody>
		                            @foreach($deletedCustomer_data as $customer)
                                            
		                            <tr>
		                                <td>{{$customer->customer_name.' '}}{{$customer->customer_lastname}}</td>
		                                <td>{{$customer->customer_email}}</td>
		                                <td>{{$customer->mobile_no}}</td>
                                                <td>{{$customer->alt_mobile_no}}</td>
                                                <td>{{$customer->landline_no}}</td>
		                                <td>{{$customer->building}} {{$customer->apartment_name}} {{$customer->lane}}
                                                <td>{{$customer->created_at}}</td>
                                                </td>
		                                
		                                
		                            </tr>
                                            
		                            @endforeach 
		                            </tbody>
		                        </table>
		                    </div>
		</div>
            </div>

        </div>
    
</div>

@stop