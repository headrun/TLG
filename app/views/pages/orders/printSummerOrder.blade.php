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
                    word-break:keep-all;
                    overflow:hidden; 
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
<div class="col-md-12" style="background-color: #EEEEEE; padding:5px;" >
	<div class="container">
		<div class="row">		
			<div class="col-md-9"  style="margin:0px auto !important; float:none; border: 1px dashed #ededed;">
				<button id="printBtn" style="float:right;" class="btn btn-sm  btn-primary">Print</button>
			</div>
		</div>
	</div>
</div>
	<div class="container" id="printable">

		<div class="row">
		
			<div class="col-md-9"  style="margin:0px auto !important; float:none; border: 1px dashed #ededed; padding:20px;">
				<div class="col-md-11" style="margin:0px auto !important; float:none; border-bottom:2px dashed #EEEEEE;">
					<div style="margin:0px auto; width:154px; display:block;  background-repeat:no-repeat;  background-image: url('{{url()}}//assets/img/logo.png'); height:90px;">
						<img src="{{url()}}/assets/img/logo.png"/>
					
					</div>
				
				</div>
                                <p style="text-align: center;padding-top:25px;">{{$franchisee_name['franchisee_legal_entity']}}</p>
				<p style="text-align: center;">Thank You and welcome to The Little Gym family</p>
				<br clear="all"/>
				<div class="col-md-7" style="margin:0px auto !important; float:left; border-bottom:2px dashed #EEEEEE;">
                                    <h4>Invoice Number :
					@if($paymentDueDetails[0]['created_at'] > '2018-04-16')
                                         {{$paymentMode[0]['invoice_format']}}
                                        @else
                                         <?php
                                                  $yrdata= strtotime($paymentDueDetails[0]['created_at']);
                                                  switch (strlen($paymentMode[0]['id'])){

                                                    case 1:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|00000'.$paymentMode[0]['id'];
                                                        break;
                                                    case 2:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|0000'.$paymentMode[0]['id'];
                                                        break;
                                                    case 3:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|000'.$paymentMode[0]['id'];
                                                        break;
                                                    case 4:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|00'.$paymentMode[0]['id'];
                                                        break;
                                                    case 5:
                                                        echo 'TLG|'.$franchisee_name['franchisee_name'].'|'.date('M', $yrdata).'|0'.$paymentMode[0]['id'];
                                                        break;
                                                    default:
                                                        echo $paymentMode[0]['id'];
                                                        break;
                                                    }
                                            ?>
                                        @endif				
                                    </h4>
                                    <h4>Payment Reciept and Enrollment  Details</h4>
				</div>
				<div class = "col-md-4" style = "float: right">
					<b><?php
				
						echo $paymentDueDetails[0]['created_at'];
					?></b>
				</div>

				<br clear="all"/>
				<div class="col-md-11" style="margin:0px auto !important; float:none;">
					<div class="row datarow">
					  <div class="col-md-3 title">Customer Name</div>
					  <div class="col-md-4" style="float:left !important">{{$getCustomerName[0]['customer_name'].' '}}{{$getCustomerName[0]['customer_lastname']}}</div>
					</div>
					<div>
					<div class="row datarow">
					  <div class="col-md-3 title">Kid Name</div>
					  <div class="col-md-4" style="float:left !important">{{$getStudentName[0]['student_name']}}</div>
					</div>
					<br clear = "all" />
					<br clear="all"/>
					<h4>Payment details:</h4>
					
					<table class = "table">
						<thead>
							<th>Type Of Class</th>
							<th>Selected Classes</th>
							<th>Start Date</th>
							<th>Amount per Class</th>
							<th>Total Amount</th>
						</thead>
						<tbody>
							@foreach($paymentDueDetails as $key => $value)
								<tr>
									<td>{{$value['payment_due_for']}}</td>
									<td>{{$value['selected_order_sessions']}}</td>
									<td>{{date('d-M-Y',strtotime($value['start_order_date']))}}</td>
									<td>{{ $value['each_class_amount'] }}</td>
									<td>{{$value['payment_due_amount']}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					
					
					<table class="paymentsTable">
						<thead>
							<tr style="font-weight:bold">
								<td style="text-align:right; ">Classes Amount</td>
								<td style="text-align:right">{{$paymentDueDetails[0]['payment_due_amount']}}</td>
							</tr>
						
						</thead>
					
						<tr>
							<td style="text-align:right"><strong>Discount({{$paymentDueDetails[0]['discount_applied']}}%:[-{{$paymentDueDetails[0]['discount_amount']}}Rs])</strong></td>
							<td  style="text-align:right">
								<strong><?php $amount=$paymentDueDetails[0]['payment_due_amount']-$paymentDueDetails[0]['discount_amount'];echo number_format((float)$amount, 2, '.', '');?> </strong>
							</td>
						</tr>
                            
						<tr>
							<td style="text-align:right"><strong>Subtotal</strong></td>
							<td  style="text-align:right">
								<strong>
								{{ number_format((float)($paymentDueDetails[0]['payment_due_amount'] - 
								$paymentDueDetails[0]['discount_amount']), 2, '.', '')}}</strong>
							</td>
						</tr>

						@if(isset($tax_data))
                        @for($i=0;$i<count($tax_data);$i++)
						<tr>
							<td style="text-align:right">
                            	<strong>
                            		{{$tax_data[$i]['tax_particular'].'('.$tax_data[$i]['tax_percentage'].'%)'}}
                            	</strong>
                            </td>
                            <td style="text-align:right">
                            	<strong>
                            		{{number_format((float)( (+$paymentDueDetails[0]['payment_due_amount']-$paymentDueDetails[0]['discount_amount']) * $tax_data[$i]['tax_percentage'] /100), 2, '.', '') }}
                            	</strong>
                            </td>
						</tr>
						@endfor
						@endif
						
						<tr>
							<td style="text-align:right"><strong>Grand Total</strong></td>
							<td  style="text-align:right">
								
								<strong>{{(int)$paymentDueDetails[0]['payment_due_amount_after_discount'] }}</strong>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
						</tr>
					
					
					</table>
					
					
					
					<div class="row datarow">
					  <div class="col-md-3 title">Payment Mode</div>
					  <div class="col-md-4">{{$paymentMode[0]['payment_mode']}}</div>
					</div>
					@if($paymentMode[0]['payment_mode'] == 'cash')
					<div class="row datarow">
					  <div class="col-md-3 title">Payment type details</div>
					  <div class="col-md-4"></div>
					</div>
					@elseif($paymentMode[0]['payment_mode'] == 'card')
					 <div class="row datarow">
					  <div class="col-md-3 title">Payment type details</div>
					  <div class="col-md-4">
					  	<span style="font-weight: bold">Bank Name: </span><span>{{$paymentMode[0]['bank_name']}}</span><br>
					  	<span style="font-weight: bold">Card Type: </span><span>{{$paymentMode[0]['card_type']}}</span><br>
                                          </div>
					</div>
					@elseif($paymentMode[0]['payment_mode'] == 'cheque')
					 <div class="row datarow">
					  <div class="col-md-3 title">Payment type details</div>
					  <div class="col-md-4">
					  	<span style="font-weight: bold">Bank Name: </span><span>{{$paymentMode[0]['bank_name']}}</span><br>
					  	<span style="font-weight: bold">Cheque Number: </span><span>{{$paymentMode[0]['cheque_number']}}</span><br>
					  </div>
					</div>
					@endif
					<br clear="all"/>
					@if($invoice_data[0]['franchise_id'] != 11)
						<table style="width: 100%;">
							<tr>
								<td><strong>Legal Entity Name :</strong></td>
								<td>{{$invoice_data[0]['legal_entry_name']}}</td>
								<td><strong>PAN No :</strong></td>
								<td>{{$invoice_data[0]['pan_no']}}</td>
							</tr>
							<tr>
								<td><strong>Service Tax No :</strong></td>
								<td>{{$invoice_data[0]['service_tax_no']}}</td>
								<td><strong>TIN No :</strong></td>
								<td>{{$invoice_data[0]['tin_no']}}</td>
							</tr>
							<tr>
								<td><strong>TAN NO :</strong></td>
								<td>{{$invoice_data[0]['tan_no']}}</td>
								@if(isset($invoice_data[0]['gst_no']) && !empty($invoice_data[0]['gst_no'])){

										<td><strong>GST No :</strong></td>
										<td>{{$invoice_data[0]['gst_no']}}</td>

							         }
							    @endif
							</tr>
						</table>
					@else
						<table style="width: 100%;">
							<tr>
								<td><strong>Legal Entity Name :</strong></td>
								<td>{{$invoice_data[0]['legal_entry_name']}}</td>
								<td><strong>VAT No :</strong></td>
								<td>{{$invoice_data[0]['service_tax_no']}}</td>
							</tr>
							<tr>
								<td><strong>TIN No :</strong></td>
								<td>{{$invoice_data[0]['tin_no']}}</td>
							</tr>
						</table>
					@endif
					<br>
					<p>Welcome. Thanks for Joining The Little Gym.  Regards, Team TLG</p>
					<hr/>
                                        <br>
                                        <div>
					<pre class="text-justify"><p style = "font-weight: bold">Terms & Conditions:</p>{{ $getTermsAndConditions[0]['terms_conditions']}}</pre>
                                        </div>
					<br/>					
				</div>
			
			
			</div>
		
		</div>
	
	
	</div>


</body>
</html>

