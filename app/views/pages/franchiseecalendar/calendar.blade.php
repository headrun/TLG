@extends('layout.master')
@section('libraryCSS')
	<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.print.css" media="all"> -->
	<link href='{{url()}}/assets/fullcalender/fullcalendar.css' rel='stylesheet' />
	<link href='{{url()}}/assets/fullcalender/fullcalendar.print.css' rel='stylesheet' media='print' />
	<link rel="stylesheet" media="all" type="text/css" href="http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css" />
	<link type="text/css" href="{{url()}}/assets/timepicker/jquery-ui-timepicker-addon.css" />

	<style>
	.fc-unthemed .fc-button:after{
		display:none;
	}
	
	.fc-button-group, .fc button{
		display:block !important;
	}
	
	.fc-view-container{
		background-color:#FFFFFF;
	}
	.modal-dialog{
		margin-top:100px;
		left:50px;
	}
	
	td.has-error label{
		color:red !important;
	}
	
	#saveAttendanceBtn:disabled{
		background: #E4E4E4;
    	color: #C3C3C3;
	}
	</style>
	<link href='{{url()}}/assets//xcalender/fullcalendar.css' rel='stylesheet' />
	<link href='{{url()}}/assets//xcalender/fullcalendar.print.css' rel='stylesheet' media='print' />
@stop



@section('libraryJS')
@section('libraryJS')

<script src="{{url()}}/assets/js/pages/validator.js"></script>
<script src='{{url()}}/assets/fullcalender/lib/moment.min.js'></script>

	
<!-- <script src='{{url()}}/assets//xcalender/jquery/jquery-1.10.2.js'></script> -->
<script src='{{url()}}/assets//xcalender/jquery/jquery-ui.custom.min.js'></script>

<script src='{{url()}}/assets//xcalender/jquery/fullcalendar.js'></script>
<script
	src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
<script type="text/javascript">
    
</script>
@stop
@section('content')

<div id='calendar'></div>
@stop