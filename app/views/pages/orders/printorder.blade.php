<?php 
/* echo "<pre>";
print_r($orders); */
?>
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
						<img src="{{url()}}//assets/img/logo.png"/>
					
					</div>
				
				</div>

				<p style="text-align: center;">Thank You and welcome to The Little Gym family</p>
				<br clear="all"/>
				<div class="col-md-7" style="margin:0px auto !important; float:left; border-bottom:2px dashed #EEEEEE;">
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
					  <div class="col-md-4" style="float:left !important">{{$getCustomerName[0]['customer_name']}}</div>
					</div>
					<div>
						<div class="" style="width:50%; float:left">
						  <div class="title">Kid Name</div>
                                                  <div class="" >{{$getStudentName[0]['student_name']}}</div>
						</div>
						
						<div class="" style="width:50%; float:right">
						  <div class="title">Total Enrolled Classes</div>
						  <div class="">{{$totalSelectedClasses}}</div>
						</div>
					</div>
					<br clear = "all" />
					<br clear="all"/>
					<h4>Payment details:</h4>
					
					<table class = "table">
						<thead>
							<th>Season Name</th>
							<th>Batch Name</th>
							<th>Selected Classes</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Amount</th>
						</thead>
						<tbody>
							@for($i = 0; $i < count($paymentDueDetails); $i++)
								<tr>
									<td>{{$getSeasonName[$i][0]['season_name']}}</td>
									<td>{{$getBatchNname[$i][0]['batch_name']}}</td>
									<td>{{$selectedSessionsInEachBatch[$i]}}</td>
									<td>{{$classStartDate[$i]}}</td>
									<td>{{$classEndDate[$i]}}</td>
									<td>{{$totalAmountForEachBach[$i]}}</td>
								</tr>
							@endfor
						</tbody>
					</table>
					
					
					<table class="paymentsTable">
						<thead>
							<tr style="font-weight:bold">
								<td style="text-align:right; ">Classes Amount</td>
								<td style="text-align:right">{{$paymentDueDetails[0]['payment_due_amount']}}</td>
							</tr>
						
						</thead>
						<?php $membershipAmount = ''; 
						if($paymentDueDetails[0]['membership_type_id'] == 1){
							$membership = "Annual Membership Amount";
							$membershipAmount = 2000;
						}elseif($paymentDueDetails[0]['membership_type_id'] == 2){
							$membership = "Life-Time Membership Amount";
							$membershipAmount = 5000;
						}
						?>
						
						<tr>
							<td style="text-align:right"><strong>By Choosing {{$paymentDueDetails[0]['selected_sessions']}} Classes You are Saving ({{$paymentDueDetails[0]['discount_applied']}}%: [-{{$paymentDueDetails[0]['discount_amount']}}Rs])</strong></td>
							<td  style="text-align:right">
								<strong><?php $amount=$paymentDueDetails[0]['payment_due_amount']-$paymentDueDetails[0]['discount_amount']; echo number_format((float)$amount, 2, '.', ''); ?></strong>
							</td>
						</tr>
                                                <?php if($paymentDueDetails[0]['discount_sibling_applied']!=0){ ?>
						<tr>
							<td style="text-align:right"><strong>By Enrolling Sibling You are Saving({{$paymentDueDetails[0]['discount_sibling_applied']}}%:[-{{$paymentDueDetails[0]['discount_sibling_amount']}}Rs])</strong></td>
							<td  style="text-align:right">
								<strong><?php $amount=$amount-$paymentDueDetails[0]['discount_sibling_amount'];echo number_format((float)$amount, 2, '.', '');?> </strong>
							</td>
						</tr>
                                                <?php  } ?>
                                                <?php if($paymentDueDetails[0]['discount_multipleclasses_applied']!=0){ ?>
						<tr>
							<td style="text-align:right"><strong>By Enrolling Multiple Classes You are Saving {{$paymentDueDetails[0]['discount_multipleclasses_applied']}} %:[-{{$paymentDueDetails[0]['discount_multipleclasses_amount']}}Rs])</strong></td>
							<td  style="text-align:right">
								<strong><?php $amount=$amount-$paymentDueDetails[0]['discount_multipleclasses_amount']; echo number_format((float)$amount, 2, '.', '');?></strong>
							</td>
						</tr>
                                                <?php }?>
                                                <?php if($paymentDueDetails[0]['discount_admin_amount']!=0){ ?>
                                                <tr>
							<td style="text-align:right"><strong>Special Discount For You(-{{$paymentDueDetails[0]['discount_admin_amount']}})</strong></td>
							<td  style="text-align:right">
								
								<strong><?php $amount=$amount-$paymentDueDetails[0]['discount_admin_amount']; echo number_format((float)$amount, 2, '.', '');?></strong>
							</td>
						</tr>
                                                <?php } ?>
                                                <?php if(isset($membership)){?>
						<tr>
							<td style="text-align:right"><strong>{{$membership}} {{$paymentMode[0]['membership_type']}}</strong></td>
							<td  style="text-align:right">
								<strong><?php echo number_format((float)$membershipAmount, 2, '.', ''); ?></strong>
							</td>
						</tr>
						<?php }?>
						
						<tr>
							<td style="text-align:right"><strong>Subtotal</strong></td>
							<td  style="text-align:right">
								
								<strong>{{number_format((float)(((float)$membershipAmount)+$paymentDueDetails[0]['payment_due_amount']-$paymentDueDetails[0]['discount_amount']-$paymentDueDetails[0]['discount_sibling_amount']-$paymentDueDetails[0]['discount_multipleclasses_amount']-$paymentDueDetails[0]['discount_admin_amount']), 2, '.', '')}}</strong>
							</td>
						</tr>
						
						<tr>
							<td style="text-align:right"><strong>Service Tax</strong></td>
							<td  style="text-align:right">
								
								<strong>{{number_format((float)( (((float)$membershipAmount)+$paymentDueDetails[0]['payment_due_amount']-$paymentDueDetails[0]['discount_amount']-$paymentDueDetails[0]['discount_sibling_amount']-$paymentDueDetails[0]['discount_multipleclasses_amount']) * 14.5/100), 2, '.', '') }}</strong>
							</td>
						</tr>
						
						<tr>
							<td style="text-align:right"><strong>Grand Total</strong></td>
							<td  style="text-align:right">
								
								<strong>{{$paymentDueDetails[0]['payment_due_amount_after_discount'] }}</strong>
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
					  	<spanstyle="font-weight: bold">Card Type: </span><span>{{$paymentMode[0]['card_type']}}</span><br>
					  	<span style="font-weight: bold">Card Last Digits: </span><span>{{$paymentMode[0]['card_last_digit']}}</span><br>
					  	<span style="font-weight: bold">Reciept Number: </span><span>{{$paymentMode[0]['receipt_number']}}</span><br>
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
					
					<p>Welcome. Thanks for Joining The Little Gym.  Regards, Team TLG</p>
					<hr/>
					<p style = "font-weight: bold">Terms & Conditions:</p>
					<pre>{{ $getTermsAndConditions[0]['terms_conditions']}}</pre>
					<br/>					
				</div>
			
			
			</div>
		
		</div>
	
	
	</div>


</body>
</html>