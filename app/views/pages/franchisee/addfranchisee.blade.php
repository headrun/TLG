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
<script src="{{url()}}/assets/js/kendoui_custom.min.js"></script>
<script src="{{url()}}/assets/js/pages/kendoui.min.js"></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>

@stop

@section('content')
<div id="breadcrumb">
	<ul class="crumbs">
		<li class="first"><a href="{{url()}}" style="z-index:9;"><span></span>Home</a></li>
		<li><a href="#" style="z-index:8;">Franchisee</a></li>
		<li><a href="#" style="z-index:7;">Add Franchisee</a></li>
	</ul>
</div>
<br clear="all"/>
<br clear="all"/>

<div class="md-card-content large-padding">
	<ul class="uk-tab " data-uk-tab={connect:'#tab-content'}>
    	<li class="uk-active"><a href=""><i class="uk-icon-plus"></i> &nbsp; Franchisee</a></li>
    	<li><a href=""><i class="uk-icon-user-plus"></i> &nbsp; Admin</a></li>
    	<li><a href=""><i class="uk-icon-plus-square"></i>&nbsp; Courses</a></li>
    	<li><a href=""><i class="uk-icon-plus-circle"></i>&nbsp; Base Price</a></li>
    	<li><a href=""><i class="uk-icon-plus-square-o"></i>&nbsp; Classes</a></li>
    	<li><a href=""><i class="uk-icon-plus"></i>&nbsp; Tax Details</a></li>
    </ul>
	<ul id="tab-content" class="uk-switcher uk-margin">
        <li class="uk-active" aria-hidden="false">
        	<!-- Add Franchisee-->
        	Add Franchisee
        </li>
        <li aria-hidden="true" class="">
        	<!-- Add Admin User-->
        	Admin User
        </li>
        <li aria-hidden="true" class="">
        	<!-- Add Courses-->
        	Add Courses
        </li>
        <li aria-hidden="true" class="">
        	<!-- Base price for classes and Birthday -->
        	Base price for classes and Birthday
        </li>
        <li aria-hidden="true" class="">
        	<!-- classes -->
        	Add classes
        </li>
        <li aria-hidden="true" class="">
        	<!-- Tax Details -->
        	Add Tax details
        </li>
    </ul>

</div>


<div class="uk-grid" data-uk-grid-margin>
	<div class="uk-width-medium-1-3">
	</div>
</div>


@stop