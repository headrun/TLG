
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <link rel="icon" type="image/png" href="{{url()}}/assets/img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="{{url()}}/assets/img/favicon-32x32.png" sizes="32x32">
    <title>TLG - Administration</title> 	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	<style>
		.datarow{
			margin-bottom:10px;
		}
		
		.title{
			font-weight:bold;
		}
		
		.paymentsTable{
		
			width:100%;
		
		
		}
		
		.paymentsTable tr td{
		
			border-bottom:1px #e5e5e5 dashed;
			padding:10px;
			height:30px;
		
		
		}
                pre {
                    white-space: pre-wrap;  
                }
                @media print {
                    #Header, #Footer { display: none !important; }
                }
	
	</style>
	<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script>
		$(document).ready(function (){
			
			printme();
			$("#printBtn").click(function (){
				printme();
		
			});
		
		});
		function printme() {
		   // window.print();

			  //Get the HTML of div
            var divElements = document.getElementById('printable').innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = 
              "<html><head><title></title></head><body>" + 
              divElements + "</body>";

            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
			   

			 
		}
	</script>
</head>
<body>
<!--
<div class="col-md-12" style="background-color: #EEEEEE; padding:5px;" >
	<div class="container">
		<div class="row">		
			<div class="col-md-9"  style="margin:0px auto !important; float:none; border: 1px dashed #ededed;">
				<button id="printBtn" style="float:right;" class="btn btn-sm  btn-primary">Print</button>
			</div>
		</div>
	</div>
</div>
-->
	<div class="container" id="printable">

		<div class="row">
		
			<div class="col-md-9"  style="margin:0px auto !important; float:none; border: 1px dashed #ededed; padding:20px;">
				<div class="col-md-11" style="margin:0px auto !important; float:none; border-bottom:2px dashed #EEEEEE;">
					<div style="margin:0px auto; width:154px; display:block;  background-repeat:no-repeat;   height:90px;">
						<img src="{{url()}}/assets/img/logo.png"/>
					
					</div>
				
				</div>

				<p style="text-align: center;">Thank You and welcome to The Little Gym family</p>
				<br clear="all"/>
				<div class="col-md-7" style="margin:0px auto !important; float:left; border-bottom:2px dashed #EEEEEE;">
                                    <h4>Invoice Number :
					@if(strtotime($order_data->created_at) > '2018-04-16')
                                         {{$order_data['invoice_format']}}
                                        @else
                                         <?php
                                                  $yrdata= strtotime($order_data->created_at);
                                                  switch (strlen($paymentMode[0]['id'])){
                                                    case 1:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|00000'.$order_data['id'];
                                                        break;
                                                    case 2:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|0000'.$order_data['id'];
                                                        break;
                                                    case 3:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|000'.$order_data['id'];
                                                        break;
                                                    case 4:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|00'.$order_data['id'];
                                                        break;
                                                    case 5:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|0'.$order_data['id'];
                                                        break;
                                                    default:
                                                        echo $order_data['id'];
                                                        break;
                                                    }
                                            ?>
                                        @endif
				    </h4>
                                    <h4>Payment Reciept and Enrollment  Details</h4>
				</div>
				<div class = "col-md-4" style = "float: right">
					<b><?php
				
						echo $paymentDueDetails['created_at'];
					?></b>
				</div>

				<br clear="all"/>
				<br clear="all"/>
				
				<div class="col-md-11" style="margin:0px auto !important; float:none;">
					<div class="row datarow">
					  <div class="col-md-3 title">Customer Name</div>
					  <div class="col-md-4" style="float:left !important">{{$customer_data['customer_name'].' '}}{{$customer_data['customer_lastname']}}</div>
					</div>
					
					<br clear = "all" />
					<br clear="all"/>
					<h4>Payment details:</h4>
					
					<table class = "table">
						<thead>
							<th>Membership Type</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Membership Amount</th>
							<th>Tax({{$order_data->tax_percentage}}%)</th>
							<th>Total</th>
						</thead>
						<tbody>
							<tr>
								<td>{{$membership_type->name}}</td>
								<td>{{$membership_data->membership_start_date}}</td>
								<td>{{$membership_data->membership_end_date}}</td>
								<td>{{$membership_type->fee_amount}}</td>
								<td>{{$order_data->tax_amount}}</td>
								<td>{{$membership_type->fee_amount+$order_data->tax_amount}}</td>
							</tr>
						</tbody>
					</table>
					
					
					<div class="row datarow">
					  <div class="col-md-3 title">Payment Mode</div>
					  <div class="col-md-4">{{$order_data->payment_mode}}</div>
					</div>
					@if($order_data->payment_mode == 'cash')
					<div class="row datarow">
					  <div class="col-md-3 title">Payment type details</div>
					  <div class="col-md-4">Cash</div>
					</div>
					@elseif($order_data->payment_mode == 'card')
					 <div class="row datarow">
					  <div class="col-md-3 title">Payment type details</div>
					  <div class="col-md-4">
					  	<span style="font-weight: bold">Bank Name: </span><span>{{$order_data -> bank_name}}</span><br>
					  	<span style="font-weight: bold">Card Type: </span><span>{{$order_data -> card_type}}</span><br>
                      </div>
					</div>
					@elseif($order_data->payment_mode == 'cheque')
					 <div class="row datarow">
					  <div class="col-md-3 title">Payment type details</div>
					  <div class="col-md-4">
					  	<span style="font-weight: bold">Bank Name: </span><span>{{$order_data -> bank_name}}</span><br>
					  	<span style="font-weight: bold">Cheque Number: </span><span>{{$order_data -> cheque_number}}</span><br>
					  </div>
					</div>
					@endif
					<br clear="all"/>
					
					<p>Welcome. Thanks for Joining The Little Gym.  Regards, Team TLG</p>
					<hr/>
					<p style = "font-weight: bold">Terms & Conditions:</p>
					<pre>{{ $getTermsAndConditions['terms_conditions']}}</pre>
					<br/>					
				</div>
			
			
			</div>
		
		</div>
	
	
	</div>


</body>
</html>
