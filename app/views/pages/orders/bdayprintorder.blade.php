

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <link rel="icon" type="image/png" href="{{url()}}/assets/img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="{{url()}}/assets/img/favicon-32x32.png" sizes="32x32">
    <title>TLG - Administration</title> 	
	<link rel="stylesheet" href="{{url()}}/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{url()}}/assets/css/bootstrap-theme.min.css">
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
                        //window.close();
			$("#printBtn").click(function (){
            			printme();
                               // window.close();
		
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
		
		
		<br clear="all"/>
		<div class="row">
		
			<div class="col-md-9"  style="margin:0px auto !important; float:none; border: 1px dashed #ededed; padding:20px;">
				<div class="col-md-11" style="margin:0px auto !important; float:none; border-bottom:2px dashed #EEEEEE;">
					<div style="margin:0px auto; width:174px; display:block;  background-repeat:no-repeat;  background-image: url('{{url()}}//assets/img/logo.png'); height:90px;">
						<img src="{{url()}}//assets/img/logo.png"/>
					
					</div>
				
				</div>
				<br clear="all"/>
                                <p style="text-align: center;padding-top:25px;">{{$franchisee_name['franchisee_legal_entity']}}</p>
				<p style="text-align: center;">Thank You and welcome to celebrate B'day party in The Little Gym family</p>
				<br clear="all"/>
				<div class="col-md-11" style="margin:0px auto !important; float:none; border-bottom:2px dashed #EEEEEE;">
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
				 <h4>Payment Reciept and Birthday  Details</h4>
				</div>
				<br clear="all"/>
				<div class="col-md-11" style="margin:0px auto !important; float:none;">
					<div class="row datarow">
					  <div class="col-md-3 title">Customer Name</div><br>
					  <div class="col-md-4" style="float:left !important">{{$customer_data->customer_name.' '}}{{$customer_data->customer_lastname}}</div>
					</div>
					
					<div>
						<div class="" style="width:50%; float:left">
						  <div class="title">Kid Name</div>
						  <div class="col-md-4" style="float:left !important; padding-left:0!important;">{{$student_data->student_name}}</div>
						</div>
						
						<div class="" style="width:50%; float:right">
						  <div class="title">Birthday party date and time</div>
						  <div class="col-md-4" style="float:left !important; padding-left:0!important;">{{$birthday_data->birthday_party_date}} : {{$birthday_data->birthday_party_time }}</div>
						</div>
					</div>
					
					<br clear="all"/>
					<h4>Payment details:</h4>
					<div class="row datarow">
					  <div class="col-md-3 title">Payment date</div>
					  <div class="col-md-4">{{date('d M Y  H:i A',strtotime($order_data->created_at))}}</div>
					</div>
					<?php if($birthday_data->additional_no_of_guests){ ?>
                                        <div class="row datarow">
					  <div class="col-md-3 title">Additional guest (more than 15)</div>
					  <div class="col-md-4">{{$order_data->additional_no_of_guests}}* 300={{$order_data->additional_guest_price}}</div>
					</div>
                                        <?php } ?>
					
                                        <?php if($birthday_data->additional_halfhours){?>
					<div>
						<div class="" style="width:50%; float:left">
						  <div class="title">additional Hours</div>
						  <div class="col-md-4">{{$order_data->additional_halfhours}}</div>
						</div>
						
						<div class="" style="width:50%; float:right">
						  <div class="title">additional hours price</div>
						  <div class="col-md-4">{{$order_data->additional_halfhour_price}}</div>
						</div>
					</div>
                                        <?php } ?>
					
					<div class="row datarow">
					  <div class="col-md-3 title">Payment amount</div>
					  <div class="col-md-4">{{$order_data->amount}}</div>
					</div>
					
					
					<table class="paymentsTable">
						<thead>
							<tr style="font-weight:bold">
								<td style="text-align:left; width:70%;">Particulars</td>
								<td style="text-align:right">Amount(without Tax)</td>
							</tr>
						
						</thead>						
		
						
						
												
						
						<tr>
                                                        <td>Advance Paid : </td>
                                                        <td style="text-align:right" >{{$birthday_data->advance_amount_paid}}</td>
							
						</tr>
						
						
										
						<tr>
							<td >Amount Pending <?php if(isset($payment_due_data->membership_id)){?>
                                                                                    (includes {{$payment_due_data->description}} cost of RS {{$payment_due_data->membership_amount}}):  
                                                                            <?php } ?>
									    <?php if($payment_due_data->discount_amount > 0){?>
										    (includes discount of RS {{$payment_due_data->discount_amount}}):
									    <?php } ?>
                                                        </td>
                                                        <td style="text-align:right">{{$birthday_data->remaining_due_amount}}  </td>
							
						</tr>
                                                						<tr>
							<td> Total Birthday celebration  Amount</td>
							<td style="text-align:right">
								
								

								  {{$birthday_data->grand_total}}
							</td>
						</tr>
						
					
					</table>
						@if(isset($tax_data))
                        @for($i=0;$i<count($tax_data);$i++)
							<h6>
							@if($tax_data[$i]['tax_percentage'] === 0 && $tax_data[$i]['tax_particular'] === 'VAT')
                        	    <input id="diplomatOption" name="diplomatOption" type="checkbox"  checked="checked" class="checkbox-custom" onclick="return false;"/>
                        	    <label for="diplomatOption" class="checkbox-custom-label">Diplomat <span
                        	      class="req"> </span></label> /
                        		{{$tax_data[$i]['tax_particular'].'('.$tax_data[$i]['tax_percentage'].'%)'}} : {{$order_data->tax_amount}}
                        	@else
                        		{{$tax_data[$i]['tax_particular'].'('.$tax_data[$i]['tax_percentage'].'%)'}} : {{($order_data->tax_amount)/2}}
                        	@endif
                        	</h6>
						@endfor
						@endif
					<h5>Total  Amount paid with Tax is {{$order_data->amount+$order_data->tax_amount}}</h5>
					
					<div class="row datarow">
					  <div class="col-md-3 title">Payment Mode</div>
					  <div class="col-md-4">{{$order_data->payment_mode}}</div>
					</div>
					
					<div class="row datarow">
					  <div class="col-md-3 title">Payment type details</div>
					  <div class="col-md-4">
					  <?php if($order_data->payment_mode == 'card'){ ?>
					  	Card type: {{$order_data->card_type}}
					  
					  <?php }else if($order_data->payment_mode == 'cheque'){ ?>
					  		Cheque number: {{$order_data->cheque_number}}
					  <?php }else{?>
					  cash payment
					  <?php }?>
					  
					  
					  </div>
					</div>
					
					<br clear="all"/>
					
                                        <p class="text-center">Welcome. Thanks for Celebrating B'day in  The Little Gym.  Regards, Team TLG</p>
				    <hr/>
                                        <pre class="text-justify"><p style = "font-weight: bold">Terms & Conditions:</p>{{ $getTermsAndConditions[0]['terms_conditions']}}</pre>

					<br/>
				</div>
			
			
			</div>
		
		</div>
	
	
	</div>


</body>
</html>
