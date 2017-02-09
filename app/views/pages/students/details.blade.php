@extends('layout.master') @section('libraryCSS')
<link
  href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'
  rel='stylesheet' />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css">
<link rel="stylesheet"
  href="{{url()}}/bower_components/kendo-ui/styles/kendo.common-material.min.css" />
<link rel="stylesheet"
  href="{{url()}}/bower_components/kendo-ui/styles/kendo.material.min.css" />
@stop

<?php 
  /* echo '<pre>';
  print_r($student[]);
  echo '</pre>';
  exit(); */

  
  
  
  $student = $student['0'];
  
  /* echo $student->student_date_of_birth."<br>";
  $dob = date('Y-m-d', strtotime($student->student_date_of_birth));
  
  $datetime1  = new DateTime($dob);
  $datetime2  = new DateTime(date('Y-m-d'));
  
  
  $interval = $datetime1->diff($datetime2);
  echo $interval->format('%y years %m months and %d days');
   */
  //exit();
?>

@section('libraryJS')

<!-- <script src='https://code.jquery.com/jquery-1.11.3.js'></script>
<script src='http://momentjs.com/downloads/moment.min.js'></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.4.0/fullcalendar.min.js'></script> -->
<script src="{{url()}}/assets/js/pages/validator.js"></script>
<script src="{{url()}}/assets/js/kendoui_custom.min.js"></script>
<script src="{{url()}}/assets/js/pages/kendoui.min.js"></script>

<script
  src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>

<script type="text/javascript">

var studentName   = "{{$student->student_name}}";
var studentId     = "{{$student->id}}";
var customerId    = "{{$student->customer_id}}";
var studentGender = "{{$student->student_gender}}";
var studentAge    = "{{date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->y;}}";

var ageYear  = '<?php echo date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->y;?>';
var ageMonth = '<?php echo date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->m;?>';
var tax_Percentage= "{{$taxPercentage->tax_percentage}}";
//getEligibleClasses()
var selectedNoOfClass=0;
var DiscountPercentage=0;
var DiscountAmount=0;
var selectedNoOfClass1=0;
var selectedNoOfClass2=0;
var selectedNoOfClass3=0;
var firstselectedNoOfClass=0;
var secondselectedNoOfClass=0;
var thirdselectedNoOfClass=0;

var estimate_master_no=0;
var totalCostForpay;
var Adminamountcal;
var batch1ClassCost=0;
var batch2ClassCost=0;
var batch3ClassCost=0;

var enddate1='';
var enddate2='';
var enddate3='';

var estimate_id1=0;
var estimate_id2=0;
var estimate_id3=0;

// for payments

var bipay=[];
var multipay=[];

$('#studentDob').kendoDatePicker();

$("#editKidBtn").click(function (){
  $("#KidsformBody").show();
  $("#messageStudentAddDiv").html("");
  getStudentDetails();
  $("#addKidsModal").modal('show');
});

$("#kidsAddForm").submit(function (event){
  event.preventDefault();
  saveKids(event);
});

$("#OrderDate").kendoDateTimePicker();
$("#OrderDate2").kendoDateTimePicker();
$("#OrderDate3").kendoDateTimePicker();
$("#OrderDate4").kendoDateTimePicker();
$("#reminderTxtBox").kendoDatePicker();
$("#introVisitTxtBox").kendoDatePicker({  
    change: function() {
      var postData = {"batchId":$("#introbatchCbx").val(), "scheduleDate":$("#introVisitTxtBox").val()};
      $.ajax({
            type: "POST",
            url: "{{URL::to('/quick/checkSlotAvailableForIntrovisit')}}",
            data: postData,
            dataType:"json",
            success: function (response)
            {
                //console.log(response);          
          if(response.status == "clear"){
            $("#introVisitAddMessage").html('<p class="uk-alert uk-alert-danger">Please select another day. Batch chosen does not have schedule on selected date.</p>');
            //$("#KidsformBody").hide();
          }else{
            $("#introVisitAddMessage").html("");
          }
            
            }
        });
    }
});







function saveKids(event){

  var postData = {'studentName'       : $('#studentName').val(),
            'nickname'           : $('#nickname').val(),
            'studentDob'         : $('#studentDob').val(),
            'studentGender'      : $('#studentGender').val(),
            'school'             : $('#school').val(),
            'location'           : $('#location').val(),
            'hobbies'            : $('#hobbies').val(),
            'emergencyContact'   : $('#emergencyContact').val(),        
            'remarks'            : $('#remarks').val(),
            'healthIssue'        : $('#healthIssue').val(),
            'studentId'          : studentId
            };

  $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/saveKids')}}",
        data: postData,
        dataType:"json",
        success: function (response)
        {
          //  console.log(response);          
      if(response.status == "success"){
        $("#messageStudentAddDiv").html('<p class="uk-alert uk-alert-success">Kid details has been updated successfully. Please wait till this page reloads</p>');
        $("#KidsformBody").hide();

        setTimeout(function(){
           window.location.reload(1);
        }, 5000);
      }else{
        $("#messageStudentAddDiv").html('<p class="uk-alert uk-alert-danger">Sorry! Kid details could not be updated.</p>');
        $("#KidsformBody").hide();
      }
        
        
        }
    });
}

function getStudentDetails(){

  $("#kidsAddForm .md-input-wrapper").addClass('md-input-filled');

   $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/getStudentById')}}",
        data: {'studentId':studentId},
        dataType:"json",
        success: function (response)
        {
            //console.log(response);          
          $('#studentName').val(response.student_name);
        $('#nickname').val(response.nickname);
        $('#studentDob').val(response.student_date_of_birth);
        $('#studentGender').val(response.student_gender);
        $('#school').val(response.school);
        $('#location').val(response.location);
        $('#hobbies').val(response.hobbies);
        $('#emergencyContact').val(response.emergency_contact);       
        $('#remarks').val(response.remarks);
        $('#healthIssue').val(response.health_issue);

        
        //$("#kidsAddForm select").addClass('md-input-filled');

        
        }
    });
  
}

function fullEnrollmentReset(){
    $("#sessionsTable").hide();
    $("#finalPaymentDiv").hide();
    $("#SeasonsCbx").removeAttr('disabled');
    $("#eligibleClassesCbx").removeAttr('disabled');
    $('#batchCbx').removeAttr('disabled');
    $("#SeasonsCbx2").removeAttr('disabled');
    $("#eligibleClassesCbx2").removeAttr('disabled');
    $('#batchCbx2').removeAttr('disabled');
    $("#SeasonsCbx3").removeAttr('disabled');
    $("#eligibleClassesCbx3").removeAttr('disabled');
    $('#batchCbx3').removeAttr('disabled');
    $('#enrollNow').hide();
    $('#enrollmentcontinue2').hide();
    $('#enrollmentcontinue3').hide();
    
    
    //clearing the msgs
    $('#batch1Msg').html('');
    $('#batch2Msg').html('');
    $('#batch3Msg').html('');
    
    //resetting the variables used
    //selectedNoOfClass=0;
    //DiscountPercentage=0;
    //DiscountAmount=0;
    
    selectedNoOfClass1=0;
    selectedNoOfClass2=0;
    selectedNoOfClass3=0;
    
    firstselectedNoOfClass=0;
    secondselectedNoOfClass=0;
    thirdselectedNoOfClass=0;
    
    estimate_master_no=0;
    totalCostForpay;

    batch1ClassCost=0;
    batch2ClassCost=0;
    batch3ClassCost=0;

    enddate1='';
    enddate2='';
    enddate3='';

    estimate_id1=0;
    estimate_id2=0;
    estimate_id3=0;

    
    
}

$("#addEnrollment").click(function(){
    fullEnrollmentReset();
    if($('input[name="enrollmentClassesSelect"]').is(':checked')){
        if($("input[name='enrollmentClassesSelect']:checked").val()!='custom'){
            selectedNoOfClass=parseInt($("input[name='enrollmentClassesSelect']:checked").val());
            DiscountPercentage=parseFloat($("input[name='enrollmentClassesSelect']:checked").attr('discountpercentage'));
        }else{
            selectedNoOfClass=parseInt($('#customEnrollmemtNoofClass').val());
            DiscountPercentage=parseFloat($('#customEnrollmemtDiscountPercentage').val());
        }
        $('#enrollmentcontinue2').hide();
        $('#enrollmentcontinue3').hide();
        $('#enrollmentMsg').html("");
        $("#enrollmentModal").modal({
          backdrop: 'static',
          keyboard: false 
        });
        $('#invoicePrintOption').attr('checked','checked');
        $('#emailOption').attr('checked','checked');
        $("#formBody").show();
        $("#messageStudentEnrollmentDiv").html("");
        getSeasons();
        getEligibleClasses();
    }else{
        $('#enrollmentMsg').html("<p class='uk-alert uk-alert-danger'>please select the Number of Classes </p>");
    }
      
});

//$("#paymentOptions").hide();

$("#enrollmentOptions").click(function (){
        <?php if(!$customermembership){?>
     var membershipAmt={{json_encode($membershipTypesAll)}};  
    $("#membershipAmount").val(membershipAmt[0]['fee_amount']);
    $("#membershipAmounttotals").val(membershipAmt[0]['fee_amount']);
                $('#membershipAmounttotalslabel').html(membershipAmt[0]['fee_amount']);
  <?php }?>
                
                $('#enrollNow').addClass('disabled');
                totalCostForpay=(firstselectedNoOfClass*batch1ClassCost)+(secondselectedNoOfClass*batch2ClassCost)+(thirdselectedNoOfClass*batch3ClassCost);
                //$('#selectedPaymentMethod').html('Amount:');
                $("#finalPaymentDiv").show();
    $("#singlePayAmountDiv").show();
    $("#singlePayAmount").val(totalCostForpay);
    $("#totalAmountToPay").val(totalCostForpay);
    $("#totalAmountToPaytotals").val(totalCostForpay);
                $('#totalAmountToPaytotalslabel').html(totalCostForpay);
                calculateFinalAmount();
                
                $("#paymentType").show();
                
});

$("#cardDetailsDiv").hide();
$("#chequeDetailsDiv").hide();
$('#cardDetailsDiv2').hide();
$('#chequeDetailsDiv2').hide();
$('#cardDetailsDiv3').hide();
$('#chequeDetailsDiv3').hide();
$('#cardDetailsDiv4').hide();
$('#chequeDetailsDiv4').hide();







$("#finalPaymentDiv").hide();


function calculateFinalAmount(){
        
        var second_child_discount_amt=0;
  var second_class_discount_amt=0;
        var finalAmount = (parseFloat($("#totalAmountToPay").val()));
  
                          
                                    var percentAmount = parseFloat($("#totalAmountToPaytotals").val()*DiscountPercentage/100);
                                    $('#discount').html('<p>By Choosing '+selectedNoOfClass+' Classes You are Saving ('+DiscountPercentage+'%:[-'+(percentAmount).toFixed(2)+'Rs])</p>');
                            
                        
                                $("#discountTextBox").val("-"+(percentAmount).toFixed(2));
                                
                          finalAmount = parseFloat(finalAmount-percentAmount);
                                $("#discountTextBoxlabel").html((finalAmount).toFixed(2));
                                <?php if($discount_second_child_elligible){ ?>
                                    
                                    
                                    $('#second_child_discount_to_form').val({{$discount_second_child}});
                                    second_child_discount_amt=parseFloat(finalAmount*{{$discount_second_child}}/100);
                                    $('#second_child_amount').val('-'+(second_child_discount_amt).toFixed(2));
                                    
                                    finalAmount=parseFloat(finalAmount-second_child_discount_amt);
                                    $('#second_child_discount').html('<p>By Enrolling Sibling You are Saving('+{{$discount_second_child}}+'%:[-'+(second_child_discount_amt).toFixed(2)+'Rs])</p>');
                                    $('#second_child_amountlabel').html((finalAmount).toFixed(2));
                                <?php } ?>
                                  
                                <?php if($discount_second_class_elligible){ ?>
                                    
                                    
                                    $('#second_class_discount_to_form').val({{$discount_second_class}});
                                    second_class_discount_amt=parseFloat(finalAmount*{{$discount_second_class}}/100);
                                    $('#second_class_amount').val('-'+(second_class_discount_amt).toFixed(2));
                                    $('#second_class_amountlabel').html('-'+(second_class_discount_amt).toFixed(2));
                                    finalAmount=parseFloat(finalAmount-second_class_discount_amt);
                                    $('#second_class_discount').html('<p>By Enrolling Multiple Classes You are Saving('+{{$discount_second_class}}+'%[-'+(second_class_discount_amt).toFixed(2)+'Rs])</p>');
                                    $('#second_class_amountlabel').html((finalAmount).toFixed(2));
                                <?php } ?>
                                
                                <?php if(!$customermembership){?>
                                     finalAmount = finalAmount+parseFloat($("#membershipAmount").val());
                                <?php }?>
                                 Adminamountcal=finalAmount;
                                 if(typeof $('#admin_discount_amount').val()!='undefined'){
                                    var adminamt=parseFloat($('#admin_discount_amount').val());
                                    $("#subtotal").val(((Math.round((finalAmount-adminamt)*100)/100)).toFixed(2));
                                    $('#subtotallabel').html(((Math.round((finalAmount-adminamt)*100)/100)).toFixed(2));
                                 }else{
                                    $("#subtotal").val(((Math.round((finalAmount)*100)/100)).toFixed(0));
                                    $('#subtotallabel').html(((Math.round((finalAmount)*100)/100)).toFixed(0));
                                 }
                                 
                          var tax =(finalAmount*tax_Percentage/100);
                                tax=Math.round(tax*100)/100;
                                
                                $("#taxAmount").val((tax).toFixed(2));
                                $('#taxAmountlabel').html((tax).toFixed(2));
                                finalAmount=finalAmount+tax;
                                finalAmount=Math.round(finalAmount*100)/100;
                          $("#grandTotal").val((finalAmount).toFixed(0));
                                $('#grandTotallabel').html((finalAmount).toFixed(0));
                                $('#discountPercentage').val((DiscountPercentage).toFixed(2));
        $('#paymentTable').show();
        $('#paymentType').show();
                                $('#emailEnrollPrintDiv').show();
                                
                                $('#admin_discount_amount').change(function(){
                                    if(($('#admin_discount_amount').val()=='')||($('#admin_discount_amount').val()<0)){
                                       $('#admin_discount_amount').val('0'); 
                                    }
                                    var adminamt=parseFloat($('#admin_discount_amount').val());
                                    var subtotal=Adminamountcal;
                                    
                                    $("#subtotal").val((subtotal-adminamt).toFixed(2));
                                    $('#subtotallabel').html((subtotal-adminamt).toFixed(2));
                                    var tax =((subtotal-adminamt)*tax_Percentage/100);
                                    tax=Math.round(tax*100)/100;
                                    
                                    $("#taxAmount").val((tax).toFixed(2));
                                    $('#taxAmountlabel').html((tax).toFixed(2));
                                    Amount=Math.round(((subtotal-adminamt)+tax)*100)/100;
                                    $("#grandTotal").val((Amount).toFixed(0));
                                    $('#grandTotallabel').html((Amount).toFixed(0));
                                });
                                $('#admin_discount_amount').keyup(function(){
                                    if(($('#admin_discount_amount').val()=='')||($('#admin_discount_amount').val()<0)){
                                       $('#admin_discount_amount').val('0'); 
                                    }
                                    var adminamt=parseFloat($('#admin_discount_amount').val());
                                    var subtotal=Adminamountcal;
                                    
                                    $("#subtotal").val((subtotal-adminamt).toFixed(2));
                                    $('#subtotallabel').html((subtotal-adminamt).toFixed(2));
                                    var tax =(((subtotal-adminamt)*tax_Percentage/100).toFixed(2));
                                    tax=Math.round(tax*100)/100;
                                    
                                    $("#taxAmount").val((tax).toFixed(2));
                                    $('#taxAmountlabel').html((tax).toFixed(2));
                                    Amount=Math.round(((subtotal-adminamt)+tax)*100)/100;
                                    $("#grandTotal").val((Amount).toFixed(0));
                                    $('#grandTotallabel').html((Amount).toFixed(0));
                                });
        
}

$("input[name='paymentTypeRadio']").change(function (){
        $("#enrollNow").show();
  var selectedPaymentType = $("input[type='radio'][name='paymentTypeRadio']:checked").val();
  if(selectedPaymentType == "card"){
    $("#chequeDetailsDiv").hide();
    $("#cardDetailsDiv").show();


    $("#cardType").attr("required",true);
    $("#card4digits").attr("required",true);
    $("#cardBankName").attr("required",false);
    $("#cardRecieptNumber").attr("required",true);


    $("#chequeBankName").attr("required",false);
    $("#chequeNumber").attr("required",false);

  }else if(selectedPaymentType == "cheque"){
    $("#chequeDetailsDiv").show();
    $("#cardDetailsDiv").hide();

    $("#cardType").attr("required",false);
    $("#card4digits").attr("required",false);
    $("#cardBankName").attr("required",false);
    $("#cardRecieptNumber").attr("required",false);


    $("#chequeBankName").attr("required",true);
    $("#chequeNumber").attr("required",true);
  }
  else if(selectedPaymentType == "cash"){
    $("#chequeDetailsDiv").hide();
    $("#cardDetailsDiv").hide();

    $("#cardType").attr("required",false);
    $("#card4digits").attr("required",false);
    $("#cardBankName").attr("required",false);
    $("#cardRecieptNumber").attr("required",false);


    $("#chequeBankName").attr("required",false);
    $("#chequeNumber").attr("required",false);
  }
  
});
function applyDiscountOnLastPayment(){

  var selectedOption = $("input[type='radio'][name='paymentOptionsRadio']:checked").val();
}



$(document).on('change', "#membershipType", function() {
  //console.log($(this).val());
        var membershipTypesAll={{json_encode($membershipTypesAll)}};
        for(var z=0;z<membershipTypesAll.length;z++){
            if(membershipTypesAll[z]['id'] == $(this).val()){
                $("#membershipAmount").val(membershipTypesAll[z]['fee_amount']);
    $("#membershipAmounttotals").val(membershipTypesAll[z]['fee_amount']);
                $('#membershipAmounttotalslabel').html(membershipTypesAll[z]['fee_amount']);
            }
        }
        
        /*
  if($(this).val() == '1'){
    
    $("#membershipAmount").val("2000");
    $("#membershipAmounttotals").val("2000");
                $('#membershipAmounttotalslabel').html('2000');
    
  }else if($(this).val() == '2'){
    $("#membershipAmount").val("5000");
    $("#membershipAmounttotals").val("5000");
                $('#membershipAmounttotalslabel').html('5000');
  }
        */
  calculateFinalAmount();
})

$(document).on('change', "#TotalAmountForOld", function() {
   calculateSubTotalForOld();
})


$(document).on('change', "#MembershipTypeForOld", function() {
  //console.log($(this).val());
        if($(this).val()!=""){
        var membershipTypesAll={{json_encode($membershipTypesAll)}};
        for(var z=0;z<membershipTypesAll.length;z++){
            if(membershipTypesAll[z]['id'] == $(this).val()){
                $("#MembershipAmountForOld").val(membershipTypesAll[z]['fee_amount']);
            }
        }
        calculateSubTotalForOld();
        }else{   
               $("#MembershipAmountForOld").val('0');
               $('#SubTotalForOld').val('0');
               $('#TaxForOld').val('0');
               $('#GrandTotalForOld').val('0');
        }
        
        
})

$(document).on('change', "#DiscountPercentageForOld", function() {

   var DiscountAmountForOld = parseFloat(($("#TotalAmountForOld").val() * $("#DiscountPercentageForOld").val())); 
   $("#DiscountAmountForOld").val(Math.round(DiscountAmountForOld)/100); 
   calculateSubTotalForOld();
})

$(document).on('change', "#SiblingPercentageForOld", function() {

   var currentTotal = parseFloat($("#TotalAmountForOld").val() - $("#DiscountAmountForOld").val());
   var SiblingAmountForOld = parseFloat((currentTotal * $("#SiblingPercentageForOld").val())); 
   $("#SiblingAmountForOld").val(Math.round(SiblingAmountForOld)/100);
   calculateSubTotalForOld();
})

$(document).on('change', "#MultiClassesPercentageForOld", function() {

   var currentTotal = parseFloat($("#TotalAmountForOld").val() - $("#DiscountAmountForOld").val() - $("#SiblingAmountForOld").val());
   var MultiClassesAmountForOld = parseFloat((currentTotal * $("#MultiClassesPercentageForOld").val())); 
   $("#MultiClassesAmountForOld").val(Math.round(MultiClassesAmountForOld)/100);
   calculateSubTotalForOld();
})

$(document).on('change', "#AdminRupeeForOld", function() {
   calculateSubTotalForOld();
})

function calculateSubTotalForOld() {
    
    var SubTotalForOld =    parseFloat(
                               $("#TotalAmountForOld").val() - 
                               $("#DiscountAmountForOld").val() - 
                               $("#SiblingAmountForOld").val() - 
                               $("#MultiClassesAmountForOld").val() - 
                               $("#AdminRupeeForOld").val() 
                              ) +
                            parseFloat($("#MembershipAmountForOld").val()); 
                    
    $("#SubTotalForOld").val(SubTotalForOld); 
    
    calculateGrandTotalForOld(); 
                              
}

function calculateGrandTotalForOld() {
      
   var TaxForOld = parseInt(Math.round($("#SubTotalForOld").val() * tax_Percentage) / 100);
   $("#TaxForOld").val(TaxForOld);
   
   var GrandTotalForOld = parseInt($("#SubTotalForOld").val()) + 
                          parseInt($("#TaxForOld").val());
   $("#GrandTotalForOld").val(GrandTotalForOld);               
   
}


$("#closeEnrollmentModal").click(function (){

  $("#batchCbx").val("");
  $("#eligibleClassesCbx").val("");
        $('#enrollmentStartDate').val('');
        $('#enrollmentEndDate').val('');
  $("#paymentOptions").hide();
  $("#sessionsTable").hide();
  $("#enrollmentOptions").val("enroll");
        fullEnrollmentReset();
  if($(this).data('closemode') == 'print'){
         window.location.reload(1);
  }
  
});

$('#enrollmentStartDate').change(function(){
   $('#batch1Msg').html('');
   $('#batchCbx').val('');
   $('input[name=paymentTypeRadio]').prop('checked', false);
   if(estimate_id1!=0){
    var data=$('#enrollmentStartDate').val();
    fullEnrollmentReset();
    $('#enrollmentStartDate').val(data);
   }
});


$("#enrollNow").hide();
$('#getSchedulesSessions').click(function (){

  if($('#batchCbx').val() != "" && $('#enrollmentStartDate').val() != "" && $("#eligibleClassesCbx").val() != ""){

    $("#messageStudentEnrollmentDiv").html('');
    getSessionsForClasses()
    $("#enrollNow").show();

  }else{
    $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-danger">Please fill up required Fields.</p>');
  }
});


var todaysDate = new Date();
$("#enrollmentStartDate").kendoDatePicker({
            format: "yyyy-MM-dd",
  // change:function (){
//    prepareGetClasses()
//  } 
  //min: todaysDate
});


$("#enrollmentEndDate").kendoDatePicker({
  change:function (){
    prepareGetClasses();
  }
  
});


function prepareGetClasses(){
  $("#paymentOptions").hide();
  $("#finalPaymentDiv").hide();
  $("input[name='paymentOptionsRadio']").attr('checked', false);
       
  //checking for first case 1 season only
       // console.log('prepareclasses');
        if(firstselectedNoOfClass===selectedNoOfClass){
           // console.log('==')
            if($('#SeasonsCbx').val()!='' && $('#batchCbx').val() != "" && $('#enrollmentStartDate').val() != "" && $("#eligibleClassesCbx").val() != ""){
                    console.log('valid 1 season only');
                    $("#messageStudentEnrollmentDiv").html('');
                    $("#availableSessions").html(firstselectedNoOfClass);
                    $("#totalAmount").html((firstselectedNoOfClass*batch1ClassCost));
                    
                    $("#amountPerSesssion").html(batch1ClassCost);
              $("#totalEnrollmentAmount").val(firstselectedNoOfClass*batch1ClassCost);
              $("#sessionsTable").show();
                    //$("#enrollNow").show();
                    $('#enrollmentcontinue2').hide();
                    $('#enrollmentcontinue3').hide();
                    
            }
        }else if((firstselectedNoOfClass+secondselectedNoOfClass)===selectedNoOfClass){
            if($('#SeasonsCbx').val()!='' && $('#batchCbx').val() != "" && $('#enrollmentStartDate').val() != "" && $("#eligibleClassesCbx").val() != "" &&
               $('#SeasonsCbx2').val()!='' && $('#batchCbx2').val() != "" && $("#eligibleClassesCbx2").val() != "" ){
                    console.log('valid 2 seasons only');
                    $("#messageStudentEnrollmentDiv").html('');
                    $("#availableSessions").html(firstselectedNoOfClass+'+'+secondselectedNoOfClass+'='+(firstselectedNoOfClass+secondselectedNoOfClass));
                    $("#totalAmount").html((firstselectedNoOfClass*batch1ClassCost)+(secondselectedNoOfClass*batch2ClassCost));
                    $("#amountPerSesssion").html('('+batch1ClassCost+')('+batch2ClassCost+')');
              $("#totalEnrollmentAmount").val((firstselectedNoOfClass*batch1ClassCost)+(secondselectedNoOfClass*batch2ClassCost));
              $("#sessionsTable").show();
                    $('#enrollNow').addClass('disabled');
                    //$("#enrollNow").show();
                    $('#enrollmentcontinue3').hide();
                    
            }
        }else if((firstselectedNoOfClass+secondselectedNoOfClass+thirdselectedNoOfClass)===selectedNoOfClass){
                    console.log('valid 3 seasons only');
                    $("#messageStudentEnrollmentDiv").html('');
                    $("#availableSessions").html(firstselectedNoOfClass+'+'+secondselectedNoOfClass+'+'+thirdselectedNoOfClass+'='+(firstselectedNoOfClass+secondselectedNoOfClass+thirdselectedNoOfClass));
                    $("#totalAmount").html((firstselectedNoOfClass*batch1ClassCost)+(secondselectedNoOfClass*batch2ClassCost)+(thirdselectedNoOfClass*batch3ClassCost));
                    $("#amountPerSesssion").html('('+batch1ClassCost+')('+batch2ClassCost+')('+batch3ClassCost+')');
              $("#totalEnrollmentAmount").val((firstselectedNoOfClass*batch1ClassCost)+(secondselectedNoOfClass*batch2ClassCost)+(thirdselectedNoOfClass*batch3ClassCost));
              $("#sessionsTable").show();
                    //$("#enrollNow").show();
                    $('#enrollNow').addClass('disabled');
        }

}

$("#enrollKidForm").validator().on('submit', function (e) {
    if (e.isDefaultPrevented()) {
        // handle the invalid form...
    } else {
      // everything looks good!
      //alert("introvisit");
      e.preventDefault();
      enrollnow(event); 

    }
});



$(".eligibleClassesCbx").change(function (){
          if($('#SeasonsCbx').val()==''){
        $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-danger"> please select the season.</p>');
    }else{
  //console.log(this.id);
  var selector = "";
  if(this.id == "eligibleClassIntro"){
    selector = "introbatchCbx";
    from = this;
  }else{
    var classId = $(this).val();
    /*$.ajax({
          type: "POST",
          url: "{{URL::to('/quick/checkenrollmentExists')}}",
          data: {'classId': classId, "studentId":studentId},
          dataType:"json",
          success: function (response)
          {
             if(response.status == "exist"){
             $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-danger"> Course already enrolled.</p>');
             }else{
             $("#messageStudentEnrollmentDiv").html('');
             }
          }
      });*/
    selector = "batchCbx";
    from = this;
  }
  getBatchesBasedOnClasses(selector,from,$('#SeasonsCbx').val());
        $('#enrollmentcontinue2').hide();
        $('#enrollmentcontinue3').hide();
        $('#batch1Msg').html('');
        }
});
$(".eligibleClassesCbx2").change(function (){
          if($('#SeasonsCbx2').val()==''){
        $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-danger"> please select the season.</p>');
    }else{
  //console.log(this.id);
  var selector = "";
  if(this.id == "eligibleClassIntro"){
    selector = "introbatchCbx";
    from = this;
  }else{
    var classId = $(this).val();
    /*$.ajax({
          type: "POST",
          url: "{{URL::to('/quick/checkenrollmentExists')}}",
          data: {'classId': classId, "studentId":studentId},
          dataType:"json",
          success: function (response)
          {
             if(response.status == "exist"){
             $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-danger"> Course already enrolled.</p>');
             }else{
             $("#messageStudentEnrollmentDiv").html('');
             }
          }
      });*/
    selector = "batchCbx2";
    from = this;
  }
  getBatchesBasedOnClasses(selector,from,$('#SeasonsCbx2').val());
        $('#enrollmentcontinue3').hide();
        $('#batch2Msg').html('');
        }
});
$(".eligibleClassesCbx3").change(function (){
          if($('#SeasonsCbx3').val()==''){
        $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-danger"> please select the season.</p>');
    }else{
  //console.log(this.id);
  var selector = "";
  if(this.id == "eligibleClassIntro"){
    selector = "introbatchCbx";
    from = this;
  }else{
    var classId = $(this).val();
    /*$.ajax({
          type: "POST",
          url: "{{URL::to('/quick/checkenrollmentExists')}}",
          data: {'classId': classId, "studentId":studentId},
          dataType:"json",
          success: function (response)
          {
             if(response.status == "exist"){
             $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-danger"> Course already enrolled.</p>');
             }else{
             $("#messageStudentEnrollmentDiv").html('');
             }
          }
      });*/
    selector = "batchCbx3";
    from = this;
  }
  getBatchesBasedOnClasses(selector,from,$('#SeasonsCbx3').val());
        $('#batch3Msg').html('');
        }
});

function getSeasons(){
$.ajax({
      type: "POST",
      url: "{{URL::to('/quick/season/getSeasonsForEnrollment')}}",
                        data: {},
      dataType: 'json',
      success: function(response){
                            console.log(response.season_data);
                            $('#seasonMsgDiv').html("<p class='uk-alert uk-alert-warning'> Season StartDate: "+response.season_data[0]['start_date']+" End Date: "+response.season_data[0]['end_date']+"</p>");
                            $(".SeasonsCbx").empty(""); 
                            $(".SeasonsCbx2").empty(""); 
                            $(".SeasonsCbx3").empty(""); 
                            string = '';
                            for(var i=0;i<response.season_data.length;i++){
                                string += '<option value='+response.season_data[i]['id']+'>'+response.season_data[i]['season_name']+'</option>';
                            }
                            //$('#enrollmentEndDate').val(response.season_data[0]['end_date']);
                            $('.SeasonsCbx').append(string); 
                            $('.SeasonsCbx2').append(string);
                            $('.SeasonsCbx3').append(string);
                           // console.log(string);
                        }
             })  ;
}



function getEligibleClasses(){
    var yearAndMonth= (parseInt(ageYear*12)+parseInt(ageMonth));
          
  $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/eligibleClassess')}}",
        data: {'ageYear': ageYear, 'ageMonth': ageMonth, 'gender':studentGender,'yearAndMonth':yearAndMonth},
        dataType:"json",
        success: function (response)
        {
            
         // console.log(response);
          if(response.status=='success'){
            $(".eligibleClassesCbx").empty("");
            $string = '<option value=""></option>';
            $.each(response.data, function (index, item) {
              $string += '<option value='+item.id+'>'+item.class_name+'</option>';               
            });
            $('.eligibleClassesCbx').append($string);
          }
        }
    });
}

function getEligibleClassesForBatch2WithAgeChange(){
    $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/eligibleClassessForOtherBatches')}}",
        data: {'FutureAgeDate':enddate1,'studentDob':"{{$student->student_date_of_birth}}",},
        dataType:"json",
        success: function (response)
        {
          $(".eligibleClassesCbx2").empty("");  
          //$(".eligibleClassesCbx2").empty(""); 
          //$(".eligibleClassesCbx3").empty(""); 
          $string = '<option value=""></option>';
          $.each(response, function (index, item) {
            //console.log(index+" = "+item);
            $string += '<option value='+item.id+'>'+item.class_name+'</option>';               
            });
          $('.eligibleClassesCbx2').append($string);
          $('#enrollmentbtnsdisplay1').css('display','none');
          $('#batch2Msg').html('');
          //$('.eligibleClassesCbx2').append($string);
          //$('.eligibleClassesCbx3').append($string);
          $('#enrollmentcontinue2').show();
        
        }
    });
}



function getEligibleClassesForBatch3WithAgeChange(){
    $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/eligibleClassessForOtherBatches')}}",
        data: {'FutureAgeDate':enddate2,'studentDob':"{{$student->student_date_of_birth}}",},
        dataType:"json",
        success: function (response)
        {
          $(".eligibleClassesCbx3").empty("");  
          //$(".eligibleClassesCbx2").empty(""); 
          //$(".eligibleClassesCbx3").empty(""); 
          $string = '<option value=""></option>';
          $.each(response, function (index, item) {
            //console.log(index+" = "+item);
            $string += '<option value='+item.id+'>'+item.class_name+'</option>';               
            });
          $('.eligibleClassesCbx3').append($string);
          $('#enrollmentbtnsdisplay2').css('display','none');
          $('#batch3Msg').html('');
          $('#enrollmentcontinue3').show();
          //$('.eligibleClassesCbx2').append($string);
          //$('.eligibleClassesCbx3').append($string);
        
        }
    });
}


function getBatchesBasedOnClasses(selector, from,seasonId){
  
//console.log($(from).val());
//console.log($('#SeasonsCbx').val());
  $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/batchesByClassSeasonId')}}",
        data: {'classId': $(from).val(),'seasonId':seasonId},
        dataType:"json",
        success: function (response)
        {          
          //  console.log(response[0]['day']);
            
          $('#'+selector).empty();          
          $string = '<option value=""></option>';
          $.each(response, function (index, item) {
            $string += '<option value='+item.id+'>'+item.batch_name+' '+item.day+'  '+item.start_time+' - '+item.end_time+' '+item.instructor+'</option>';               
            });
          $('#'+selector).append($string);
          $('#'+selector).val();
          
          //$('#introbatchCbx').append($string);
        
        }
    });
    
}




var availableSessionCount = 0;
var eachClassCost=0;
function getSessionsForClasses(){
            $.ajax({
          type: "POST",
          url: "{{URL::to('/quick/getBatcheSchedules')}}",
          data: {'batchId': $('#batchCbx').val(), "enrollmentStartDate":$('#enrollmentStartDate').val(), "enrollmentEndDate":$('#enrollmentEndDate').val(),'seasonId':$('#SeasonsCbx').val()},
          dataType:"json",
          success: function (response)
          {   
                    //console.log(response);
                    
              $("#availableSessions").html(response.availableSession);
              $("#totalAmount").html(response.amountTotal);
                    eachClassCost = response.eachClassAmount;
                    $("#amountPerSesssion").html(eachClassCost);
              $("#totalEnrollmentAmount").val(response.amountTotal);
              availableSessionCount = response.availableSession;

        
        

              
            $("#sessionsTable").show();
                        
          }
      }); 

  
}

function enrollnow(){
  //console.log($("#enrollKidForm").serialize());
  event.preventDefault();
  $("#enrollNow").hide();
        $('#MsgDiv').hide();
        $('#seasonMsgDiv').hide();
  $("#KidsformBody").hide();
  $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-info">Enrolling student.Please wait till process is completed.</p>');
  $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/enrollkid')}}",
        data:$("#enrollKidForm").serialize(),
        //data: {'classId': $("#eligibleClassesCbx").val(), 'batchId':$("#batchCbx").val(), "studentId":studentId},
        dataType:"json",
        success: function (response)
        {
            console.log(response);
                
          if(response.status == "success"){
                        
                
              if(response.printUrl == ""){
          $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-success">Student has been successfully enrolled. Please wait till this page reloads</p>');
          setTimeout(function(){
             window.location.reload(1);
          }, 5000);
                    }else{

          var printvars = '<a target="_blank" href="'+response.printUrl+'" class="btn btn-primary">Print</a>';
                $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-success">Student has been successfully enrolled. Please click the print button below.</p>'+printvars);

                $("#closeEnrollmentModal").data("closemode",'print');
                    }
          }else{ 
                        //$("#messageStudentEnrollmentDiv").hide();
            $("#messageStudentEnrollmentDiv").html('<p class="uk-alert uk-alert-danger">Sorry! Student could not be enrolled.</p>');
          }        
        }
    }); 
  
}


function ReceivePendingDue(pendingId,pendingAmount,discount){
  $('#pending_id').val(pendingId);
  $('#pending_amt').val(pendingAmount);
  $('#pending_discount').val(discount);
  $('#receivepayment').addClass('disabled');
  $('#receiveCardDetailsDiv').css('display','none');
  $('#receiveChequeDetailsDiv').css('display','none');
  $('#receiveemailEnrollPrintDiv').css('display','none');
$('#receivedue').modal('show');

}



$('#receivepayment').click(function(){
 
    var amountAfterDiscount=(($('#pending_discount').val()/100)*parseInt($('#pending_amt').val()));
    if($("input[name=paymentReceiveTypeRadio]:checked").val()=='card'){
        if(($('#receivecard4digits').val()!='') && ($('#receivecardBankName').val()!='' && $('#receivecardRecieptNumber').val()!='')){
            //console.log('validation passed');
            var print=$('#receiveinvoicePrintOption').is(":checked");
            $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/creatependingorder')}}",
                        data: {'pending_id':$('#pending_id').val(),'pendingamount':amountAfterDiscount,
                               'cardType':$('#receivecardType').val(),'carddigits':$('#receivecard4digits').val(),
                               'bankName':$('#receivecardBankName').val(),'cardRecieptNumber':$('#receivecardRecieptNumber').val(),
                               'invoicePrint':print,'invoiceEmail':$('#receiveemailOption').is(":checked"),
                               'paymentType':'card',},
      dataType: 'json',
      success: function(response){
                            //console.log(response);
                            //console.log(print);
                            $('#receiveduebody').empty();
                            var printvars = '<a target="_blank" href="'+response.printurl+'" class="btn btn-primary">Print</a>';
                            if(print){                         
                              $('#receiveduebody').empty();
                              $('#receiveduebody').html("<p class='uk-alert uk-alert-success'>pending Due is succesfully received.</p><br>"+printvars);
                             $('#receivepayment').addClass('disabled');
                            }else{
                                $('#receiveduebody').empty();
                                $('#receiveduebody').html("<p class='uk-alert uk-alert-success'>pending Due is succesfully received.</p>");
                                $('#receivepayment').addClass('disabled');
                            }
                            
                            //$('#pendingamount').modal('show'); 
                            $('#receivedueclose').click(function(){
                                window.location.reload(1);        
                            });
                        }
             });
        }else{
            $('#receiveDueMsg').html('<p class="uk-alert uk-alert-warning"> please select all required details</p>');
        }
        
        
    }
    if($("input[name=paymentReceiveTypeRadio]:checked").val()=='cash'){
        //console.log('cash');
        var print=$('#receiveinvoicePrintOption').is(":checked");
        $('#receivepayment').addClass('disabled');
        $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/creatependingorder')}}",
                        data: {'pending_id':$('#pending_id').val(),'pendingamount':amountAfterDiscount,'paymentType':'cash','invoiceEmail':$('#receiveemailOption').is(":checked")},
      dataType: 'json',
      success: function(response){
                           // console.log(response.status);
                            
                            var printvars = '<a target="_blank" href="'+response.printurl+'" class="btn btn-primary">Print</a>';
                            if(print){                         
                              $('#receiveduebody').empty();
                              $('#receiveduebody').html("<p class='uk-alert uk-alert-success'>pending Due is succesfully received.</p><br>"+printvars);
                             $('#receivepayment').addClass('disabled');
                            }else{
                                $('#receiveduebody').empty();
                                $('#receiveduebody').html("<p class='uk-alert uk-alert-success'>pending Due is succesfully received.</p>");
                                $('#receivepayment').addClass('disabled');
                            }
                            
                            //$('#pendingamount').modal('show'); 
                           $('#receivedueclose').click(function(){
                            window.location.reload(1);        
                            });
                        }
             });
    }
    if($("input[name=paymentReceiveTypeRadio]:checked").val()=='cheque'){
        if(($('#receivechequeBankName').val()!='') && ($('#receivechequeNumber').val()!='')){
            //console.log('validation passed');
            
            var print=$('#receiveinvoicePrintOption').is(":checked");
            $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/creatependingorder')}}",
                        data: {'pending_id':$('#pending_id').val(),'pendingamount':amountAfterDiscount,
                               'bankChequeName':$('#receivechequeBankName').val(),'chqueNo':$('#receivechequeNumber').val(),
                               'invoicePrint':print,'invoiceEmail':$('#receiveemailOption').is(":checked"),
                               'paymentType':'cheque',},
      dataType: 'json',
      success: function(response){
                            //console.log(response);
                            
                            
                            var printvars = '<a target="_blank" href="'+response.printurl+'" class="btn btn-primary">Print</a>';
                            if(print){                         
                            $('#receiveduebody').empty();
                             $('#receiveduebody').html("<p class='uk-alert uk-alert-success'>pending Due is succesfully received.</p><br>"+printvars);
                             $('#receivepayment').addClass('disabled');
                            }else{
                                $('#receiveduebody').empty();
                                $('#receiveduebody').html("<p class='uk-alert uk-alert-success'>pending Due is succesfully received.</p>");
                                $('#receivepayment').addClass('disabled');
                            
                            }
                            
                            //$('#pendingamount').modal('show'); 
                            $('#receivedueclose').click(function(){
                            window.location.reload(1);        
                            });
                        }
             });
            
        }else{
            $('#receiveDueMsg').html('<p class="uk-alert uk-alert-warning"> please select all required details</p>');
        }
    }
    
    
       
});

$('#singlePayRadio').change(function(){
   // console.log('changed');
   //$('#CustomerType').val("NewCustomer"); 
//   $('#Order-date').css('display','none');
//   $('#Order-date1').css('display','none');
//   $('#Order-date2').css('display','none');
//   $('#Order-date3').css('display','none');
});

$('#SeasonsCbx').change(function(){
    $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/season/getSeasonDataBySeasonId')}}",
                        data: {'seasonId':$('#SeasonsCbx').val(),},
      dataType: 'json',
      success: function(response){
                            $('#seasonMsgDiv').html("<p class='uk-alert uk-alert-warning'> Season StartDate: "+response.data['start_date']+" End Date: "+response.data['end_date']+"</p>");
                          //  $('#enrollmentEndDate').val(response.end_date);
                            //getEligibleClasses();
                            $('#eligibleClassesCbx').val('');
                            $('#batch1Msg').html('');
                            $('#batchCbx').val('');
                            $('#enrollmentcontinue2').hide();
                            $('#enrollmentcontinue3').hide();
                        }
             }); 

});
$('#SeasonsCbx2').change(function(){
    $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/season/getSeasonDataBySeasonId')}}",
                        data: {'seasonId':$('#SeasonsCbx2').val(),},
      dataType: 'json',
      success: function(response){
                            $('#seasonMsgDiv').html("<p class='uk-alert uk-alert-warning'> Season StartDate: "+response.data['start_date']+" End Date: "+response.data['end_date']+"</p>");
                          //  $('#enrollmentEndDate').val(response.end_date);
                           $('#eligibleClassesCbx2').val('');
                           $('#batch2Msg').html('');
                           $('#enrollmentcontinue3').hide();
                        }
             }); 

});
$('#SeasonsCbx3').change(function(){
    $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/season/getSeasonDataBySeasonId')}}",
                        data: {'seasonId':$('#SeasonsCbx3').val(),},
      dataType: 'json',
      success: function(response){
                            $('#seasonMsgDiv').html("<p class='uk-alert uk-alert-warning'> Season StartDate: "+response.data['start_date']+" End Date: "+response.data['end_date']+"</p>");
                          //  $('#enrollmentEndDate').val(response.end_date);
                           $('#eligibleClassesCbx3').val('');
                           $('#batchCbx3').html('');
                           $('#batch3Msg').html('');
                        }
             }); 

});
$('#batchCbx').change(function(){

if($('#enrollmentStartDate').val()!=''){
$.ajax({
        type: "POST",
        url: "{{URL::to('/quick/getBatchRemainingClassesByBatchId')}}",
        data: {'batchId':$('#batchCbx').val(),'preferredStartDate':$('#enrollmentStartDate').val(),},
        dataType:"json",
        success: function (response)
        {
            console.log(response);
            if(response.status=='success'){
                
                if(selectedNoOfClass <= response.classCount){
                    $('#batch1Msg').html('<span class="uk-alert uk-alert-success">No Of Classes:'+selectedNoOfClass+'&nbsp;<i class="fa fa-trash" style = "background: #e53935; padding: 3px"  aria-hidden="true" id = "deleteBtn1" ></i></span>');
                    batch1ClassCost=response.classAmount;
                    $('#enrollmentcontinue2').hide();
                    $('#enrollmentcontinue3').hide();
                    firstselectedNoOfClass=selectedNoOfClass;
                     $.ajax({
                            type: "POST",
                            url: "{{URL::to('/quick/insertEstimateDetails')}}",
                            data: {'customer_id':{{$student->customer_id}},'student_id':studentId,'season_id':$('#SeasonsCbx').val(),
                                   'batch_id':$('#batchCbx').val(),'class_id':$('#eligibleClassesCbx').val(),
                                   'enroll_start_date':response.enrollment_start_date,'enroll_end_date':response.batch_Schedule_data[selectedNoOfClass-1]['schedule_date'],
                                    'total_selected_classes':selectedNoOfClass,'no_of_available_classes':response.classCount,
                                    'no_of_opted_classes':selectedNoOfClass,'batch_amount':response.classAmount,'estimate_master_no':estimate_master_no},
                            dataType: 'json',
                            success: function(response){
                              //console.log(response.data['id']);
                              $('#deleteBtn1').attr('onclick','deleteInEstimateTable('+response.data["id"]+')');
                                $('#SeasonsCbx').attr('disabled',true);
                                $('#eligibleClassesCbx').attr('disabled',true);
                                $('#batchCbx').attr('disabled',true);
                                
                                estimate_master_no=response.data['estimate_master_no'];
                                $('#estimate_master_no').val(response.data['estimate_master_no']);
                                estimate_id1=response.data['id'];
                                prepareGetClasses();
                                
                            }
                        });     
                                
                     
                    //alert('completed');
                }else{
                    $('#batch1Msg').html('<span class="uk-alert uk-alert-success" >No Of Classes:'+response.classCount+'&nbsp;<i class="fa fa-trash" style = "background: #e53935; padding: 3px"  id = "deleteBtn1"  aria-hidden="true"></i></span>');
                    enddate1=response.lastdate;
                    firstselectedNoOfClass=response.classCount;
                    batch1ClassCost=response.classAmount;
                    // **** age changing after enrollment of batch 1 ****//
                    
                    
                   // $('#enrollmentbtnsdisplay1').css('display','block');
                  //  $('#GoChangeAge').click(function(){
                        $.ajax({
                            type: "POST",
                            url: "{{URL::to('/quick/insertEstimateDetails')}}",
                            data: {'customer_id':{{$student->customer_id}},'student_id':studentId,'season_id':$('#SeasonsCbx').val(),
                                   'batch_id':$('#batchCbx').val(),'class_id':$('#eligibleClassesCbx').val(),
                                   'enroll_start_date':response.enrollment_start_date,'enroll_end_date':response.enrollment_end_date,
                                    'total_selected_classes':selectedNoOfClass,'no_of_available_classes':response.classCount,
                                    'no_of_opted_classes':response.classCount,'batch_amount':response.classAmount,'estimate_master_no':estimate_master_no},
                            dataType: 'json',
                            success: function(response){
                              //console.log(response);
                              
                                getEligibleClassesForBatch2WithAgeChange();
                                $('#SeasonsCbx').attr('disabled',true);
                                $('#eligibleClassesCbx').attr('disabled',true);
                                $('#batchCbx').attr('disabled',true);
                                estimate_master_no=response.data['estimate_master_no'];
                                $('#estimate_master_no').val(response.data['estimate_master_no']);
                                estimate_id1=response.data['id'];
                                $('#deleteBtn1').attr('onclick','deleteInEstimateTable('+response.data["id"]+')');
                            }
                        });     
                 //   });
                    
                    
                    //$('#enrollmentcontinue2').show();
                }
                $('#eligibleClassesCbx2').val('');
                $('#batchCbx2').val('');
                $('batch2Msg').html('');
                
                //$('#batch1Msg').html('');
                
                 //$('#enrollmentcontinue2').hide();
                 //$('#enrollmentcontinue3').hide();
                //$('#MsgDiv').html('<p class="uk-alert uk-alert-success">selected class day:'+response.day+'.</p>');   
            }else{
                $('#batch1Msg').html('<p class="uk-alert uk-alert-danger">No Of Classes:0</p>');
            }
        }
    });
}else{
$('#batch1Msg').html('<p class="uk-alert uk-alert-danger">select date</p>');
}
});


//for delete in estimate table
function deleteInEstimateTable(id){
        $("#enrollNow").hide();
  if(id == estimate_id1){
    if(estimate_id2 == 0){
      ajaxCallToDelete(id, "b1");
    }else{
      $('#MsgDiv').html('<p class="uk-alert uk-alert-danger">Please first delete 2nd batch, after delete 1st batch</p>');
    }
  }
  if(id == estimate_id2){
    if(estimate_id3 == 0){
      ajaxCallToDelete(id, "b2");
    }else{
      $('#MsgDiv').html('<p class="uk-alert uk-alert-danger">Please first delete 3rd batch, after delete 2nd batch</p>');
    }
  }
  if(id == estimate_id3){   
    ajaxCallToDelete(id, "b3");
  }
}


function ajaxCallToDelete(id, Bname){
  $.ajax({
    url : "{{URL::to('/quick/deleteBatchInestimateTable')}}",
    type: "POST",
    data: {'id': id},
    dataType: "json",
    success:function(response){
      if(response.status == "success"){
        console.log(response);
        if(Bname == "b1"){
          $('#SeasonsCbx').attr('disabled',false);
          $('#eligibleClassesCbx').attr('disabled',false);
          $('#batchCbx').attr('disabled',false);
          $('#sessionsTable').css('display', 'none'); 
          $('#paymentTable').hide();
          $('#finalPaymentDiv').hide(); 
          $('#paymentType').hide();
          $('input[name="paymentTypeRadio"]').attr('checked', false);
          $('#chequeDetailsDiv').hide();
          $('#chequeBankName').val('');
          $('#chequeNumber').val('');
          $('#card4digits').val('');
          $('#cardBankName').val('');
          $('#cardRecieptNumber').val('');
          $('#cardDetailsDiv').hide();
          $('#emailEnrollPrintDiv').hide();
          $('#SeasonsCbx2').parent().parent().parent().parent().css('display','none');  
          estimate_id1 = 0;
                                        $('#eligibleClassesCbx').val('');
                                        
                                        $('#batchCbx').val('');
                                        $('#batch1Msg').html('');
          
                                        $('#MsgDiv').html('<p class="uk-alert uk-alert-success">Deleted successfully</p>');
          setTimeout(function(){
            $('#MsgDiv').html('');
          }, 2500);
        }
        else if(Bname == "b2"){
          $('#SeasonsCbx2').attr('disabled',false);
          $('#eligibleClassesCbx2').attr('disabled',false);
          $('#batchCbx2').attr('disabled',false); 
          $('#sessionsTable').css('display', 'none');
          $('#paymentTable').hide();  
          $('#finalPaymentDiv').hide();
          $('input[name="paymentTypeRadio"]').attr('checked', false);
          $('#paymentType').hide();
          $('#chequeBankName').val('');
          $('#chequeNumber').val('');
          $('#card4digits').val('');
          $('#cardBankName').val('');
          $('#cardRecieptNumber').val('');
          $('#chequeDetailsDiv').hide();
          $('#cardDetailsDiv').hide();
          $('#emailEnrollPrintDiv').hide();
          $('#SeasonsCbx3').parent().parent().parent().parent().css('display','none');
          estimate_id2 = 0;
                                        
                                        $('#batchCbx2').val('');
                                        $('#batch2Msg').html('');
          
          $('#MsgDiv').html('<p class="uk-alert uk-alert-success">Deleted successfully</p>');
          setTimeout(function(){
            $('#MsgDiv').html('');
          }, 2500);
        }
        else if(Bname == "b3"){
          $('#SeasonsCbx3').attr('disabled',false);
          $('#eligibleClassesCbx3').attr('disabled',false);
          $('#batchCbx3').attr('disabled',false); 
          $('#sessionsTable').css('display', 'none');
          $('#paymentTable').hide();  
          $('#finalPaymentDiv').hide();
          $('input[name="paymentTypeRadio"]').attr('checked', false);
          $('#paymentType').hide();
          $('#chequeBankName').val('');
          $('#chequeNumber').val('');
          $('#card4digits').val('');
          $('#cardBankName').val('');
          $('#cardRecieptNumber').val('');
          $('#chequeDetailsDiv').hide();
          $('#cardDetailsDiv').hide();
          $('#emailEnrollPrintDiv').hide();
                                        
                                        
          estimate_id3 = 0;
                                        $('#batchCbx3').val('');
                                        $('#batch3Msg').html('');
          
          $('#MsgDiv').html('<p class="uk-alert uk-alert-success">Deleted successfully</p>');
          setTimeout(function(){
            $('#MsgDiv').html('');
          }, 2500);
        }
        
      }else{
        console.log(response);
      }
    }
  });
}


$('#batchCbx2').change(function(){
console.log(enddate1);
$.ajax({
        type: "POST",
        url: "{{URL::to('/quick/getBatchRemainingClassesByBatchId')}}",
        data: {'batchId':$('#batchCbx2').val(),'preferredStartDate':enddate1,},
        dataType:"json",
        success: function (response)
        {
            console.log(response);
            if(response.status=='success'){
                
                if((selectedNoOfClass-firstselectedNoOfClass)<= response.classCount){
                    $('#batch2Msg').html('<span class="uk-alert  uk-alert-success">No Of Classes:'+(selectedNoOfClass-firstselectedNoOfClass)+'&nbsp;<i class="fa fa-trash" style = "background: #e53935; padding: 3px" id = "deleteBtn2"  aria-hidden="true"></i></span>');
                    $('#enrollmentcontinue3').hide();
                    batch2ClassCost=response.classAmount;
                    secondselectedNoOfClass=(selectedNoOfClass-firstselectedNoOfClass);
                    $.ajax({
                            type: "POST",
                            url: "{{URL::to('/quick/insertEstimateDetails')}}",
                            data: {'customer_id':{{$student->customer_id}},'student_id':studentId,'season_id':$('#SeasonsCbx2').val(),
                                   'batch_id':$('#batchCbx2').val(),'class_id':$('#eligibleClassesCbx2').val(),
                                   'enroll_start_date':response.enrollment_start_date,'enroll_end_date':response.enrollment_end_date,
                                    'total_selected_classes':selectedNoOfClass,'no_of_available_classes':response.classCount,
                                    'no_of_opted_classes':(selectedNoOfClass-firstselectedNoOfClass),'batch_amount':response.classAmount,'estimate_master_no':estimate_master_no},
                            dataType: 'json',
                            success: function(response){
                              //console.log(response);
                              $('#deleteBtn2').attr('onclick','deleteInEstimateTable('+response.data["id"]+')');
                                $('#SeasonsCbx2').attr('disabled',true);
                                $('#eligibleClassesCbx2').attr('disabled',true);
                                $('#batchCbx2').attr('disabled',true);
                                estimate_master_no=response.data['estimate_master_no'];
                                $('#estimate_master_no').val(response.data['estimate_master_no']);
                                estimate_id2=response.data['id'];
                                prepareGetClasses();
                            }
                        }); 
                    //alert('completed');
                    
                }else{
                    $('#batch2Msg').html('<span class="uk-alert uk-alert-success" >No Of Classes:'+response.classCount+'&nbsp;<i class="fa fa-trash" style = "background: #e53935; padding: 3px" id = "deleteBtn2" aria-hidden="true"></i></span>');
                    //firstselectedNoOfClass=firstselectedNoOfClass+response.classCount
                    enddate2=response.lastdate;
                    secondselectedNoOfClass=response.classCount;
                    batch2ClassCost=response.classAmount;
                    //selectedNoOfClass=selectedNoOfClass-response.classCount;
                    if(selectedNoOfClass==0){
                        alert('completed');
                    }
                    //$('#enrollmentbtnsdisplay2').css('display','block');
                    
                    
                    //$('#GoChangeAge2').click(function(){
                        $.ajax({
                            type: "POST",
                            url: "{{URL::to('/quick/insertEstimateDetails')}}",
                            data: {'customer_id':{{$student->customer_id}},'student_id':studentId,'season_id':$('#SeasonsCbx2').val(),
                                   'batch_id':$('#batchCbx2').val(),'class_id':$('#eligibleClassesCbx2').val(),
                                   'enroll_start_date':response.enrollment_start_date,'enroll_end_date':response.enrollment_end_date,
                                    'total_selected_classes':selectedNoOfClass,'no_of_available_classes':response.classCount,
                                    'no_of_opted_classes':response.classCount,'batch_amount':response.classAmount,'estimate_master_no':estimate_master_no},
                            dataType: 'json',
                            success: function(response){
                            //console.log(response);
                                $('#deleteBtn2').attr('onclick','deleteInEstimateTable('+response.data["id"]+')');
                                getEligibleClassesForBatch3WithAgeChange();
                                $('#SeasonsCbx2').attr('disabled',true);
                                $('#eligibleClassesCbx2').attr('disabled',true);
                                $('#batchCbx2').attr('disabled',true);
                                estimate_master_no=response.data['estimate_master_no'];
                                $('#estimate_master_no').val(response.data['estimate_master_no']);
                                estimate_id2=response.data['id'];
                            }
                        });
                        
                    //});
                   // $('#enrollmentcontinue3').show();
                }
                $('#eligibleClassesCbx3').val('');
                $('#batchCbx3').val('');
                $('#batch3Msg').html('');
                //$('#batch2Msg').html('');
                 //$('#enrollmentcontinue3').hide();
                //$('#MsgDiv').html('<p class="uk-alert uk-alert-success">selected class day:'+response.day+'.</p>');   
            }else{
                $('#batch2Msg').html('<p class="uk-alert uk-alert-danger">No Of Classes:0</p>');
            }
        }
    });
});

$('#batchCbx3').change(function(){
console.log(enddate2);
$.ajax({
        type: "POST",
        url: "{{URL::to('/quick/getBatchRemainingClassesByBatchId')}}",
        data: {'batchId':$('#batchCbx3').val(),'preferredStartDate':enddate2},
        dataType:"json",
        success: function (response)
        {
            console.log(response);
            if(response.status=='success'){
                
                if((selectedNoOfClass-(firstselectedNoOfClass+secondselectedNoOfClass)) <= response.classCount){
                    $('#batch3Msg').html('<span class="uk-alert uk-alert-success">No Of Classes:'+(selectedNoOfClass-(firstselectedNoOfClass+secondselectedNoOfClass))+'&nbsp;<i class="fa fa-trash" style = "background: #e53935; padding: 3px" id = "deleteBtn3" aria-hidden="true"></i></span>');
                    batch3ClassCost=response.classAmount;
                    thirdselectedNoOfClass=(selectedNoOfClass-(firstselectedNoOfClass+secondselectedNoOfClass));
                    //$('#enrollmentbtnsdisplay3').css('display','block');
                    //$('#GoEnrollmentConfirm').click(function(){
                       $.ajax({
                            type: "POST",
                            url: "{{URL::to('/quick/insertEstimateDetails')}}",
                            data: {'customer_id':{{$student->customer_id}},'student_id':studentId,'season_id':$('#SeasonsCbx3').val(),
                                   'batch_id':$('#batchCbx3').val(),'class_id':$('#eligibleClassesCbx3').val(),
                                   'enroll_start_date':response.enrollment_start_date,'enroll_end_date':response.enrollment_end_date,
                                    'total_selected_classes':selectedNoOfClass,'no_of_available_classes':response.classCount,
                                    'no_of_opted_classes':thirdselectedNoOfClass,'batch_amount':response.classAmount,'estimate_master_no':estimate_master_no},
                            dataType: 'json',
                            success: function(response){
                            console.log(response);
                            $('#deleteBtn3').attr('onclick','deleteInEstimateTable('+response.data["id"]+')');
                                $('#SeasonsCbx3').attr('disabled',true);
                                $('#eligibleClassesCbx3').attr('disabled',true);
                                $('#batchCbx3').attr('disabled',true);
                                $('#enrollmentbtnsdisplay3').css('display','none');
                                
                                estimate_master_no=response.data['estimate_master_no'];
                                $('#estimate_master_no').val(response.data['estimate_master_no']);
                                estimate_id3=response.data['id'];
                                prepareGetClasses();
                            }
                        }); 
                        
                    //});
        
                        
                    
                    //prepareGetClasses();
                    //alert('completed');
                    
                }else{
                    $('#batch3Msg').html('<span class="uk-alert uk-alert-success">No Of Classes:'+response.classCount+'&nbsp;<i class="fa fa-trash" style = "background: #e53935; padding: 3px" id = "deleteBtn3" aria-hidden="true"></i></span>');
                    enddate3=response.lastdate;
                    thirdselectedNoOfClass=response.classCount;
                    batch3ClassCost=response.classAmount;
                    //selectedNoOfClass=selectedNoOfClass-response.classCount;
                   // $('#enrollmentcontinue3').show();
                }
               // $('#batch3Msg').html('');
                //$('#MsgDiv').html('<p class="uk-alert uk-alert-success">selected class day:'+response.day+'.</p>');   
            }else{
                $('#batch3Msg').html('<p class="uk-alert uk-alert-danger">No Of Classes:0</p>');
            }
        }
    });
});
function getivdata(ivid){
    //console.log(ivid);
    
    
    $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/getIntrovisitHistory')}}",
                        data: {'introvisit_id':ivid},
      dataType: 'json',
      success: function(response){
                            //console.log(response);
                            var data="<table class='uk-table'>"+
                                    "<thead>"+
                                    "<tr>"+
                                    "<th class='uk-text-nowrap'>History Details</th>"+
                                    "<th class='uk-text-nowrap'>Created Date</th>"+
                                    "</tr>"+
                                    "</thead>";
                           for(var i=0;i<response.status.length;i++){
                                data+="<tr role='row'>"+"<td>"+response.status[i]['log_text']+"</td><td>"+response.status[i]['created_at']+"</td></tr>";
                            }
                            data+="</table>";
                            //console.log(data);
                            $('#ivhistorybody').html(data);
                            $('#ivhistory').modal('show');
                        }
             });  

    
}
$('#reschedule-date').kendoDatePicker();

$('#iveditSelect').change(function(){
if ($('#iveditSelect').val()=='RE_SCHEDULE'){    
    $('#reschedule').css('display','block');
}else{
    $('#reschedule').css('display','none');
}
});


$.urlParam = function(name){
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
  if(results){
        return results[1];
        }else{
        return 0;
        }
}

 //for selecting proper div receive payment tab 
$(document).ready(function(){
    var data='';
    var membershipTypesAll={{json_encode($membershipTypesAll)}};
    for(var z=0;z<membershipTypesAll.length;z++){
        console.log(membershipTypesAll[z]);
         data+= "<option value="+membershipTypesAll[z]['id']+">"+membershipTypesAll[z]['description']+"</option>";
    }
    $('#MembershipTypeForOld').append(data);
    $('#membershipType').append(data);
    
   $('#paymentOptionsReceive_1').change(function(){
      if($('#paymentOptionsReceive_1').is(":checked")){
          //console.log('card');
          $('#receivepayment').removeClass('disabled');
          $('#receiveCardDetailsDiv').css('display','block');
          $('#receiveemailEnrollPrintDiv').css('display','block');
          $('#receiveChequeDetailsDiv').css('display','none');
          $('#receivecard4digits').attr('required','true');
          
      } 
   });
   $('#paymentOptionsReceive_2').change(function(){
      if($('#paymentOptionsReceive_2').is(":checked")){
         // console.log('cash');
          $('#receivepayment').removeClass('disabled');
          $('#receiveemailEnrollPrintDiv').css('display','block');
          $('#receiveCardDetailsDiv').css('display','none');
          $('#receiveChequeDetailsDiv').css('display','none');
      }
   });
   $('#paymentOptionsReceive_3').change(function(){
      if($('#paymentOptionsReceive_3').is(":checked")){
         // console.log('cheque');
          $('#receivepayment').removeClass('disabled');
          $('#receiveCardDetailsDiv').css('display','none');
          $('#receiveChequeDetailsDiv').css('display','block');
          $('#receiveemailEnrollPrintDiv').css('display','block');
      } 
   });
});


$('#receivedueclose').click(function(){
   $('#receivedue').modal('hide');
});
/*
$('#receivecard4digits').change(function(){
    console.log('working');
   if(($('#receivecardBankName').val()!='') && ($('#receivecardRecieptNumber').val()!='') ){
      $('#receivepayment').removeClass('disabled'); 
   } 
});

$('#receivecardBankName').change(function(){
   if(($('#receivecard4digits').val()!='') && ($('#receivecardRecieptNumber').val()!='') ){
      $('#receivepayment').removeClass('disabled'); 
   } 
});
$('#receivecardRecieptNumber').change(function(){
   if(($('#receivecardBankName').val()!='') && ($('#receivecard4digits').val()!='') ){
      $('#receivepayment').removeClass('disabled'); 
   } 
});
*/

$('#year').change(function(){
  //console.log($('#year').val());
  var year = $('#year').val();
  if($('#year').val() != ''){
    $.ajax({
      type: "POST",
            url: "{{URL::to('/quick/getBatchNameByYear')}}",
            data: {'year': year, 'studentId': studentId},
            dataType: 'json',
            success: function(response){
              //console.log(response[0][0][0]['batch_name']);
              if (response.status == "success") {
                var batchNames = '';
                $('#batchName').html('<option></option>');
                for(i = 0; i< response[0].length; i++){
                  if(typeof(response[0][i][0]['Leadinstructor'])!=='undefined'){
                    batchNames += "<option value = '"+response[0][i][0]['id']+"'>"+response[0][i][0]['batch_name']+' '+response[0][i][0]['day']+' '+response[0][i][0]['preferred_time']+' '+response[0][i][0]['preferred_end_time'] +' ('+response[0][i][0]['Leadinstructor'] +")</option>" ;
                  }else{
                    batchNames += "<option value = '"+response[0][i][0]['id']+"'>"+response[0][i][0]['batch_name']+' '+response[0][i][0]['day']+' '+response[0][i][0]['preferred_time']+' '+response[0][i][0]['preferred_end_time']+"</option>" ;
                  }
                  
                }
                $('#batchName').html('<option></option>'+batchNames);
              }
            }
        });
  }
});




$('#batchName').change(function(){
  if($('#year').val() != '' && $('#batchName').val() != ''){
    var year = $('#year').val();
    var batchId = $('#batchName').val();

    $.ajax({
      type: "POST",
            url: "{{URL::to('/quick/getAttendanceForStudent')}}",
            data: {'year': year, 'batchId': batchId, 'studentId': studentId},
            dataType: 'json',
            success: function(response){
              console.log(response);
              if(response.data.length == 0){
                $('#AttendanceDiv').html('<center><h4>No data was found</h4></center>');
                $('#Pcount').text('0');
                $('#Acount').text('0');
                $('#EAcount').text('0');
                        $('#makeup-session').text('0');
                        $('#total-session').text(response.totalSession);
                        
              }else{
                var markup = '';
                var Pcount = 0;
                var Acount = 0;
                var EAcount = 0;
                        var makeup =0;
                        var total=response.data.length;
                for(i = 0; i < response.data.length; i++){
                  if(response.data[i]["status"] == 'P'){
                    Pcount = Pcount+1;
                  }else if(response.data[i]["status"] == 'A'){
                    Acount = Acount+1;
                  }else if(response.data[i]["status"] == 'EA'){
                    EAcount = EAcount+1;
                                        if(response.data[i]['makeup_class_given']==1){
                                            makeup=makeup+1;
                                        }
                  }
                  var stringDate = response.data[i]["attendance_date"].toString();
                  markup += '<span class = "badge" >'+stringDate+'</span>'+
                       '&nbsp;&nbsp;<span style=" padding-top: 0px;">'+response.data[i]["status"]+'</span>';
                }
                $('#AttendanceDiv').html(markup);
                $('#Pcount').text(Pcount);
                $('#Acount').text(Acount);
                $('#EAcount').text(EAcount);
                        $('#makeup-session').text(makeup);
                        $('#total-session').text(response.totalSession);
              } 
            }
    });
  }else{
    $('#errorMsgDiv').html('<h5 class="uk-alert uk-alert-danger" data-uk-alert>Please select year and Batch Name<h5>');
                setTimeout(function(){
                                    $('#errorMsgDiv').slideUp();
                                    $('#errorMsgDiv').html('');
                                    $('#errorMsgDiv').show();
                },4000);
  }
});

$("#enrollmentStartDateForOld").kendoDatePicker({
            format: "yyyy-MM-dd",
});

$("#enrollmentEndDateForOld").kendoDatePicker({
            format: "yyyy-MM-dd",
});




$('#addOldCustomerEnrollment').click(function(){
  $.ajax({
    type: "POST",
    url: "{{URL::to('/quick/season/getSeasonsForEnrollment')}}",
        data: {},
    dataType: 'json',
    success: function(response){
            console.log(response.season_data);
            string = '';
            classes = '';
            for(var i=0;i<response.season_data.length;i++){
              string += '<option value='+response.season_data[i]['id']+'>'+response.season_data[i]['season_name']+'</option>';
            }

            for(var i=0;i<response.Class_data.length;i++){
              classes += '<option value='+response.Class_data[i]['id']+'>'+response.Class_data[i]['class_name']+'</option>';
            }
            $('#emailOptionforoldcustomer').attr('checked','checked');
            $('.SeasonsCbxForOld').append(string);
            $('.ClassesCbxForOld').append(classes); 
       }
    });
    
        /**
        *  Populate values for Enroll Old customer 
        */
  $('#EnrollOldCustomerModal').modal('show', true);
});

function calculateToatlsForOld(){  
   var totalAmountOld = parseFloat($("#NoOfClassesForOld1").val()*$("#EachClassAmountForOld").val());
   return totalAmountOld;
}


$('#ClassesCbxForOld').change(function(){
  var seasonId = $('#SeasonsCbxForOld').val();
  var classId = $('#ClassesCbxForOld').val();
  if(seasonId != '' && classId != ''){
    $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/getBatchesForOldCustomer')}}",
          data: {'seasonId': seasonId, 'classId': classId},
      dataType: 'json',
      success: function(response){
              console.log(response.batch_data);
              string = '<option value=""></options>';
              if(response.batch_data.length == 0){
                $('#OldCustomerMsgDiv').html('<h5 class="uk-alert uk-alert-warning" data-uk-alert>No Batches Found</h5>')
                setTimeout(function(){
                  $('#OldCustomerMsgDiv').html('');
                }, 3500)
              }else{
                for(var i=0;i<response.batch_data.length;i++){
                  if(typeof(response.batch_data[i]['Leadinstructor'])!=='undefined'){
                  string += '<option value='+response.batch_data[i]['id']+'>'+response.batch_data[i]['batch_name']+' '+response.batch_data[i]['day']+' '+response.batch_data[i]['preferred_time']+' '+response.batch_data[i]['preferred_end_time'] +' ('+response.batch_data[i]['Leadinstructor'] +')</option>';
                  }else{
                    string += '<option value='+response.batch_data[i]['id']+'>'+response.batch_data[i]['batch_name']+' '+response.batch_data[i]['day']+' '+response.batch_data[i]['preferred_time']+' '+response.batch_data[i]['preferred_end_time']+'</option>';
                  }
                }
              }
              $('#BatchesCbx').html(string); 
          }
      });
  }else{
    alert('Please select seasons and Classes');
  }
});



$('#enrollmentEndDateForOld').change(function(){
//  if($('#enrollmentEndDateForOld').val() != '' && $('#enrollmentStartDateForOld').val() != ''){
//      var startDate = $('#enrollmentStartDateForOld').val();
//      var endDate = $('#enrollmentEndDateForOld').val();
//      var parseSdate = parseDate(startDate);
//      var parseEdate = parseDate(endDate);
//      var ONE_WEEK = 1000 * 60 * 60 * 24 * 7;
//      var date1_ms = parseSdate.getTime();
//      var date2_ms = parseEdate.getTime();
//      var difference_ms = Math.abs(date2_ms - date1_ms);
//      var noOfClasses =  Math.round(difference_ms / ONE_WEEK);
//      //console.log(noOfClasses);
//      $('#NoOfClassesForOld').val(noOfClasses);
//  }else{
//    $('#OldCustomerMsgDiv').html('<h5 class="uk-alert uk-alert-warning" data-uk-alert>Please Select Start and End dates</h5>')
//        setTimeout(function(){
//          $('#OldCustomerMsgDiv').html('');
//        }, 3500)
//  }
});


function parseDate(input) {
  var parts = input.match(/(\d+)/g);
  // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
  return new Date(parts[0], parts[1]-1, parts[2]); // months are 0-based
}


$("input[name='paymentTypeRadioForOld']").change(function (){

  var selectedPaymentType = $("input[type='radio'][name='paymentTypeRadioForOld']:checked").val();
  if(selectedPaymentType == "card"){
    $('#paymentTypeForOld').css('display', 'block');
    $("#chequeDetailsDivForOld").hide();
    $("#cardDetailsDivForOld").show();


    $("#cardType").attr("required",true);
    $("#card4digits").attr("required",true);
    $("#cardBankName").attr("required",true);
    $("#cardRecieptNumber").attr("required",true);


    $("#chequeBankName").attr("required",false);
    $("#chequeNumber").attr("required",false);

  }else if(selectedPaymentType == "cheque"){
    $('#paymentTypeForOld').css('display', 'block');
    $("#chequeDetailsDivForOld").show();
    $("#cardDetailsDivForOld").hide();

    $("#cardType").attr("required",false);
    $("#card4digits").attr("required",false);
    $("#cardBankName").attr("required",false);
    $("#cardRecieptNumber").attr("required",false);


    $("#chequeBankName").attr("required",true);
    $("#chequeNumber").attr("required",true);
  }
  else if(selectedPaymentType == "cash"){
    $('#paymentTypeForOld').css('display', 'none');
    $("#chequeDetailsDivForOld").hide();
    $("#cardDetailsDivForOld").hide();

    $("#cardType").attr("required",false);
    $("#card4digits").attr("required",false);
    $("#cardBankName").attr("required",false);
    $("#cardRecieptNumber").attr("required",false);


    $("#chequeBankName").attr("required",false);
    $("#chequeNumber").attr("required",false);
  }
  
});

$('#BatchesCbx').change(function(){
    if($('#enrollmentStartDateForOld').val()!='' && $('#enrollmentEndDateForOld').val()!=''){
   $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/getbatchesbybatchidanddate')}}",
                        data: {'startdate':$('#enrollmentStartDateForOld').val(),'enddate':$('#enrollmentEndDateForOld').val(),'batch_id':$('#BatchesCbx').val()},
      dataType: 'json',
      success: function(response){
                            console.log(response);
                            if(response.status=='success'){
                            $('#classno').html('No Of Classes:'+response.class_count);
                            $('#NoOfClassesForOld1').val(response.class_count);
                            $('#classno').css('display','block');
                            $("#TotalAmountForOld").val(calculateToatlsForOld());
                            calculateSubTotalForOld();
                            
                            }else{
                                console.log('error');
                            }
                        }
             });
             
    }else{
        $('batchesCbx').val('');
        $('#OldCustomerMsgDiv').html("<p class='uk-alert uk-alert-success'>Please select Enrollment startdate and End date</p>")
    }
});


$('#excusedabsent').click(function(){
    if($('#year').val()!='' && $('#batchName').val()!=''){
    $('#eamodalheader').html('Make-up');
    $.ajax({
  type: "POST",
        url: "{{URL::to('/quick/getExcusedabsentStudentsByBatchId')}}",
                        data: {'student_id':studentId,'batch_id':$('#batchName').val()},
      dataType: 'json',
      success: function(response){
                            console.log(response);
                            var data='';
                            
                            var data2="";
                            for(var j=0;j<response.season_data.length;j++){
                                data2+="<option value='"+response.season_data[j]['id']+"'>"+response.season_data[j]['season_name']+"</option>";
                            }
                            var class_data="";
                            for(var z=0;z<response.classes_data.length;z++){
                                class_data+="<option value='"+response.classes_data[z]['id']+"'>"+response.classes_data[z]['class_name']+"</option>";
                            }
                            for(var i=0;i<response.data.length;i++){
                                 data+="<div class='uk-grid' data-uk-grid-margin>"+
                                            "<div class='uk-width-medium-1-1'>"+
                                                "<div class='parsley-row md-btn md-btn-warning' style='border-radius: 15px; font-size:12px;'>"+
                                                  "Excused Absent Date: "+response.data[i]['attendance_date']+
                                                  "<input type='hidden' id='eadate"+i+"' data='eadate"+i+"' data2="+i+" value='"+response.data[i]['attendance_date']+"'></input>"+
                                                  "<input type='hidden' id='attendance_id"+i+"' data='attendance_id"+i+"' data2="+i+" value='"+response.data[i]['id']+"'></input>"+
                                                  "<input type='hidden' id='eabatch_id"+i+"' data='eabatch_id"+i+"' data2="+i+" value='"+response.data[i]['batch_id']+"'></input>"+
                                                "</div>"+
                                                "<div class='parsley-row md-btn md-btn-primary' style='border-radius: 15px; font-size:12px;'>"+
                                                  "Description:"+response.data[i]['description_absent']+
                                                "</div>"+
                                            "</div><br clear='all'><br clear='all'>"+
                                            "<div id='makeupmsg"+i+"' class='uk-width-medium-1-1'></div>"+
                                            "<div class='uk-width-medium-1-3'>"+
                                                "<div class='parsley-row'>"+
                                                "<label for='season"+i+"'>Season<span class='req'>*</span></label>"+
                                                 "<select id='season"+i+"' data='season' data2="+i+" class='form-control input-sm md-input' class='form-control input-sm md-input' style='padding:0px; font-weight:bold;color: #727272;'>"+data2+"</select>"+
                                                "</div>"+
                                            "</div>"+
                                            "<div class='uk-width-medium-1-3'>"+
                                                "<div class='parsley-row'>"+
                                                    "<label for='classes"+i+"'>Classes<span class='req'>*</span></label>"+
                                                     "<select id='classes"+i+"' data='classes' data2="+i+" class='form-control input-sm md-input' class='form-control input-sm md-input' style='padding:0px; font-weight:bold;color: #727272;'>"+"<option></option>"+class_data+"</select>"+
                                                "</div>"+
                                            "</div>"+
                                            "<div class='uk-width-medium-1-3'>"+
                                                "<div class='parsley-row'>"+
                                                    "<label for='batches"+i+"'>Batch<span class='req'>*</span></label>"+
                                                     "<select id='batches"+i+"' data='batch' data2="+i+" class='form-control input-sm md-input' class='form-control input-sm md-input' style='padding:0px; font-weight:bold;color: #727272;'>"+"<option></option>"+"</select>"+
                                                "</div>"+
                                            "</div><br clear='all'><br clear='all'>"+
                                            "<div class='uk-width-medium-1-3'>"+
                                                "<div class='parsley-row'>"+
                                                    "<label for='date"+i+"'>Dates<span class='req'>*</span></label>"+
                                                     "<select id='date"+i+"' data='date' data2="+i+" class='form-control input-sm md-input' class='form-control input-sm md-input' style='padding:0px; font-weight:bold;color: #727272;'>"+"<option></option>"+"</select>"+
                                                "</div>"+
                                            "</div>"+
                                            "<div class='uk-width-medium-1-3'>"+
                                                "<div class='parsley-row'>"+
                                                    "<br><i class=' btn btn-success uk-icon-save  uk-icon-large' id='makeupsave"+i+"' data='makeupsave' data2="+i+"></i>"+

                                                "</div>"+
                                            "</div><br clear='all'><br clear='all'>"+
                                          "</div>";
                            }
                            
                            
                            //console.log(data);
                            $('#eaabsentbody').html(data);
                            if(response.data.length > 0){
                                $('#ea').modal('show');
                            }else{
                                $('#errorMsgDiv').hide();
                                $('#errorMsgDiv').html("<p class='uk-alert uk-alert-danger'>No Excused Absent or All Excused Absent makeup are given for this Batch</p>");
                                $('#errorMsgDiv').show('slow');
                                setTimeout(function(){
                                    $('#errorMsgDiv').slideUp();
                                    $('#errorMsgDiv').html('');
                                    $('#errorMsgDiv').show();
                                },4000);
                            }
                            $('select[data="season"]').change(function(){
                               //resetting 
                               var i=$(this).attr('data2');
                               $('#classes'+i).val('');
                               $('#batches'+i).html('');
                               $('#batches'+i).val('');
                               $('#date'+i).html('');
                               $('#date'+i).val('');
                            });
                            $('select[data="classes"]').change(function(){
                                var i=$(this).attr('data2');
                                
                               // call ajax and get batches data
                               if($('#season'+i).val()!='' && $('#classes'+i).val()!=''){
                                   //console.log('correct');
                                   
                                   $.ajax({
                                           type: "POST",
                                           url: "{{URL::to('/quick/getBatchesForOldCustomer')}}",
                                           data: {'seasonId': $('#season'+i).val(), 'classId': $('#classes'+i).val()},
                                           dataType: 'json',
                                          success: function(response){
                                           //console.log(response.batch_data);
                                            string = '<option value=""></options>';
                                            if(response.batch_data.length == 0){
                                                $('#OldCustomerMsgDiv').html('<h5 class="uk-alert uk-alert-warning" data-uk-alert>No Batches Found</h5>')
                                                    setTimeout(function(){
                                                        $('#OldCustomerMsgDiv').html('');
                                                    }, 3500)
                                            }else{
                                                for(var x=0;x<response.batch_data.length;x++){
                                                  if(typeof(response.batch_data[i]['Leadinstructor'])!=='undefined'){
                                                    string += '<option value="'+response.batch_data[x]['id']+'">'+response.batch_data[x]['batch_name']+' '+response.batch_data[x]['day']+' '+response.batch_data[x]['preferred_time']+' '+response.batch_data[x]['preferred_end_time'] +' ('+response.batch_data[x]['Leadinstructor'] +')</option>';
                                                  }else{
                                                    string += '<option value="'+response.batch_data[x]['id']+'">'+response.batch_data[x]['batch_name']+' '+response.batch_data[x]['day']+' '+response.batch_data[x]['preferred_time']+' '+response.batch_data[x]['preferred_end_time']+'</option>';
                                                  }
                                                }
                                            }
                                            //console.log(string);
                                            
                                            $('#batches'+i).html(string);
                                            $('#date'+i).html('');
                                            $('#date'+i).val('');
                                          }
                                   });
                               }else{
                                    $('#batches'+i).html('');
                                    $('#batches'+i).val('');
                                    $('#date'+i).html('');
                                    $('#date'+i).val('');
                                   //display error
                               }
                            });
                            
                            //checking for batches to get dates
                            $('select[data="batch"]').change(function(){
                                var i=$(this).attr('data2');
                                if($('#season'+i).val()!='' && $('#classes'+i).val()!='' && $('#batches'+i).val()!=''){
                                $.ajax({
                                    type: "POST",
                                    url: "{{URL::to('/quick/getBatchDatesByBatchId')}}",
                                    data: {'batch_id':$('#batches'+i).val()},
                                    dataType: 'json',
                                    success: function(response){
                                        if(response.status=='success'){
                                            console.log(response.dates);
                                            var data='';
                                            for(var x=0;x<response.dates.length;x++){
                                                data+="<option value='"+response.dates[x]['schedule_date']+"'>"+response.dates[x]['schedule_date']+"</option>";
                                            }
                                           // console.log(data);
                                           $('#date'+i).html(data);
                                           $('#date'+i).val('');
                                        }else{
                                           
                                        }
                                    }
                                });
                                }else{
                                $('#date'+i).html('');
                                $('#date'+i).val('');
                                }
                            });
                            
                            
                            $('i[data="makeupsave"]').click(function(){
                                var i=$(this).attr('data2');
                                if($('#season'+i).val()!='' && $('#classes'+i).val()!='' && $('#batches'+i).val()!='' && $('#date'+i).val()!=''){
                                    
                                    $(this).parent().append('<i class="uk-icon-spinner uk-icon-large uk-icon-spin"id="makeupsavespin'+i+'" data="makeupsavespin'+i+'" data2='+i+'></i>');
                                    $('#makeupsave'+i).hide();
                                    $.ajax({
                                        type: "POST",
                                        url: "{{URL::to('/quick/createMakeupClass')}}",
                                        data: {'ea_batch_id':$('#eabatch_id'+i).val(),'eadate':$('#eadate'+i).val(),
                                               'student_id':studentId,'mu_season_id':$('#season'+i).val(),
                                               'mu_class_id':$('#classes'+i).val(),'mu_batches_id':$('#batches'+i).val(),
                                               'mu_date':$('#date'+i).val(),'attendance_id':$('#attendance_id'+i).val()},
                                        dataType: 'json',
                                        success: function(response){
                                            console.log(response);
                                            if(response.status==='success'){
                                                $('#makeupsavespin'+i).parent().append("<i class=' btn btn-success uk-icon-check  uk-icon-large' id='makeupcheck"+i+"' data='makeupcheck' data2="+i+"></i>");
                                                $('#makeupsavespin'+i).remove();
                                                $('#makeupmsg'+i).html("<p class='uk-alert uk-alert-success'>Makeup Class Created successfully</p>")
                                                setTimeout(function(){
                                                        $('#makeupcheck'+i).parent().parent().parent().slideUp();
                                                        $('#makeupcheck'+i).parent().parent().parent().remove();
                                                }, 3000)
                                            }else if(response.status==='exists'){
                                                //$('#makeupsavespin'+i).parent().append("<i class=' btn btn-success uk-icon-save  uk-icon-large' id='makeupsave"+i+"' data='makeupsave' data2="+i+"></i>");
                                                $('#makeupsavespin'+i).remove();
                                                $('#makeupsave'+i).show();
                                                $('#makeupmsg'+i).html("<p class='uk-alert uk-alert-danger'>student is already enrolled to this class</p>");
                                                setTimeout(function(){
                                                        $('#makeupcheck'+i).parent().parent().parent().slideUp();
                                                        $('#makeupmsg'+i).html('');
                                                        $('#makeupcheck'+i).parent().parent().parent().remove();
                                                }, 3000)
                                            }
                                        }
                                    });
                                }else{
                                    $('#makeupmsg'+i).hide();
                                    $('#makeupmsg'+i).html("<p class='uk-alert uk-alert-danger'>Please select all required Fields</p>");
                                    $('#makeupmsg'+i).show('slow');
                                    setTimeout(function(){
                                           $('#makeupmsg'+i).slideUp();
                                           $('#makeupmsg'+i).html('');
                                           $('#makeupmsg'+i).show();
                                    }, 2000)
                                }    
                            });

                        }
    });
    
}else{
    $('#errorMsgDiv').hide();
    $('#errorMsgDiv').html("<p class='uk-alert uk-alert-warning'> please select the year and batch</p>");
    $('#errorMsgDiv').show('slow');
    setTimeout(function(){
       $('#errorMsgDiv').slideUp();
       $('#errorMsgDiv').html('');
       $('#errorMsgDiv').show(); 
    },2000);
}  

});

$('#makeupsession').click(function(){

    if($('#year').val()!='' && $('#batchName').val()!=''){
        $('#eamodalheader').text('Makeup-Given Details');
        $('#makeupMsgDiv').html('');
        $('#eaabsentbody').html('');
        // make ajax call to get data
        $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/getMakeupdataByBatchId')}}",
                        data: {'batch_id':$('#batchName').val(),'student_id':studentId},
      dataType: 'json',
      success: function(response){
                            console.log(response);
                            //return;
                            if(response.status==='success'){
                            var data="<table class='uk-table table-striped'>"+
                                     "<thead>"+
                                     "<tr><th>EA Date</th><th>Description</th><th>Makeup Batch</th><th>Makeup-date</th><th>Make-up Given By</th></tr>"+
                                     "</thead>"+
                                     "<tbody>";
                             for(var i=0;i<response.attendancedata.length;i++){
                                 data+="<tr><td>"+response.attendancedata[i]['attendance_date']+"</td>"+
                                       "<td>"+response.attendancedata[i]['description_absent']+"</td>"+
                                       "<td>"+response.student_class_data[i][0]['batch_name']+"</td>"+
                                       "<td>"+response.student_class_data[i][0]['enrollment_end_date']+"</td>"+
                                       "<td>"+response.student_class_data[i][0]['receivedby']+"</td>"+
                                       "</tr>";
                             }
                             data+="</tbody>";
                                     
                                     
                            $('#eaabsentbody').html(data);
                            $('#ea').modal('show');
                            }else{
                                $('#errorMsgDiv').hide();
                                $('#errorMsgDiv').html("<p class='uk-alert uk-alert-danger'>No Makeups Given for this Batch</p>");
                                $('#errorMsgDiv').show('slow');
                            setTimeout(function(){
                                $('#errorMsgDiv').slideUp();
                                $('#errorMsgDiv').html();
                                $('#errorMsgDiv').show();
                            },4000);
                            }
                        }
        });
        
        
        
        
    }else{
        $('#errorMsgDiv').hide();
        $('#errorMsgDiv').html("<p class='uk-alert uk-alert-warning'> please select the year and batch</p>");
        $('#errorMsgDiv').show('slow');
        setTimeout(function(){
            $('#errorMsgDiv').slideUp();
            $('#errorMsgDiv').html(''); 
            $('#errorMsgDiv').show();
        },4000);
    }
});

$('#Transfers').click(function(){
$('#eaabsentbody').empty();
$('#Transferbtn').remove();
$('#eaabsentbody').append("<div class='makeupMsgDiv' id='makeupMsgDiv'></div>");
$('#eafooter').prepend('<button type="submit" class="btn btn-primary" id="Transferbtn">Transfer</button>')
$('#Transferbtn').hide();
if($('#year').val()!='' && $('#batchName').val()!=''){
  $.ajax({
        type: "POST",
  url: "{{URL::to('/quick/getTransferkiddata')}}",
        data: {'student_id':studentId,'batch_id':$('#batchName').val()},
        dataType: 'json',
            success: function(response){
              
              if(response.status=='success'){
                if(response.remainingclass_count>0){
                    $('#eamodalheader').html('Transfer Kid');
                    var data="<div class='uk-grid' data-uk-grid-margin id='transferbody'>"+
                                "<div class='uk-width-medium-1-1'>"+
                                   "<div class='parsley-row md-btn md-btn-primary' style='border-radius: 15px; font-size:12px;'>"+
                                                  "Remaining Classes:"+response.remainingclass_count+
                                   "</div>"+
                                   "<input type='hidden' id='remainingclass' value='"+response.remainingclass_count+"' />"+
                                   "<input type='hidden' id='availableclass' value='' />"+
                                "</div>"+
                             "</div>"+
                             "<div class='uk-grid' data-uk-grid-margin>"+
                                "<div class='uk-width-medium-1-2'>"+
                                   "<input type='text' id='transfer_enrollment_start_date' placeholder='StartDate' required class='uk-form-width-medium'/>" +
                                "</div>"+
                                "<div class='uk-width-medium-1-2'>"+
                                    "<div id='transfermsg'></div>"+
                                "</div>"+
                             "</div>"+
                             "<div class='uk-grid' data-uk-grid-margin>"+
                                 "<div class='uk-width-medium-1-3'>"+
                                    "<label for='TransferSeasonsCbx'>Seasons<span class='req'>*</span></label>"+
                                    "<select id='TransferSeasonsCbx' name='TransferSeasonsCbx' required class='TransferSeasonsCbx form-control input-sm md-input' style='padding: 0px; font-weight: bold; color: #727272;'>"+
                                    "</select>"+
                                 "</div>"+
                                 "<div class='uk-width-medium-1-3'>"+
                                    "<label for='TransferClassesCbx'>Classes<span class='req'>*</span></label>"+
                                    "<select id='TransferClassesCbx' name='TransferClassesCbx' required class='TransferClassesCbx form-control input-sm md-input' style='padding: 0px; font-weight: bold; color: #727272;'>"+
                                    "</select>"+
                                 "</div>"+
                                 "<div class='uk-width-medium-1-3'>"+
                                    "<label for='TransferbatchCbx'>Batches<span class='req'>*</span></label>"+
                                    "<select id='TransferbatchCbx' name='TransferbatchCbx' required class='TransferbatchCbx form-control input-sm md-input' style='padding: 0px; font-weight: bold; color: #727272;'>"+
                                    "</select>"+
                                 "</div>"+
                             "</div>"+
                             "<div class='uk-grid' data-uk-grid-margin>"+
                                "<div class='uk-width-medium-1-2'>"+
                                    "<label for='TransferDescription'>Description<span class='req'>*</span></label>"+
                                       "<input type='text' id='TransferDescription'  required class='form-control input-sm md-input'/>" +
                                "</div>"+
                             "</div>";
                    
                    $('#eaabsentbody').append(data);
                    season_data='';
                    for(var i=0;i<response.season_data.length;i++){
                        season_data+="<option value="+response.season_data[i]['id']+">"+response.season_data[i]['season_name']+"</option>";
                    }
                    $('#TransferSeasonsCbx').append(season_data);
                    classes_data='<option></option>';
                    for(var i=0;i<response.class_data.length;i++){
                        classes_data+="<option value="+response.class_data[i]['id']+">"+response.class_data[i]['class_name']+"</option>";
                    }
                    $('#TransferClassesCbx').append(classes_data);
                    
                    $('#transfer_enrollment_start_date').kendoDatePicker({ format: "yyyy-MM-dd",});
                    $('#ea').modal('show');
                    
                    $('#TransferSeasonsCbx').change(function(){
                        $('#TransferClassesCbx').val('');
                        $('#TransferbatchCbx').html('');
                        $('#TransferbatchCbx').val('');
                        $('#transfermsg').html('');
                        
                    });
                    
                    $('#TransferClassesCbx').change(function(){
                       if($('#TransferSeasonsCbx').val()!='' && $('#TransferClassesCbx').val()!=''){
                        $.ajax({
                            type: "POST",
                            url: "{{URL::to('/quick/batchesByClassSeasonId')}}",
                            data: {'seasonId':$('#TransferSeasonsCbx').val(),'classId':$('#TransferClassesCbx').val()},
                            dataType: 'json',
                            success: function(response){
                                if(response.length>0){
                                    var batch_data='<option></option>';
                                    for(var i=0; i<response.length;i++){
                                        batch_data+="<option value='"+response[i]['id']+"'>"+response[i]['batch_name']+' '+response[i]['day']+' '+response[i]['start_time']+' '+response[i]['end_time']+response[i]['instructor']+"</option>";
                                    }
                                $('#TransferbatchCbx').html('');
                                $('#TransferbatchCbx').append(batch_data);
                                $('#transfermsg').html('');
                                }else{
                                    $('#TransferbatchCbx').html('');
                                    $('#transfermsg').html('');
                                    $('#makeupMsgDiv').hide();
                                    $('#makeupMsgDiv').html("<p class='uk-alert uk-alert-danger'>No Batches Available</p>");
                                    $('#TransferbatchCbx').val('');
                                    $('#makeupMsgDiv').show('slow');
                                    setTimeout(function(){
                                        $('#makeupMsgDiv').slideUp();
                                        $('#makeupMsgDiv').html(''); 
                                        $('#makeupMsgDiv').show();
                                    },3000);
                                }
                            }
                       });
                       }else{
                       $('#makeupMsgDiv').hide();
                       $('#makeupMsgDiv').html('please select the season and class');
                       $('#makeupMsgDiv').show('slow');
                       setTimeout(function(){
                            $('#makeupMsgDiv').slideUp();
                            $('#makeupMsgDiv').html(''); 
                            $('#makeupMsgDiv').show();
                       },3000);
                       }
                    });
                    
                    $('#TransferbatchCbx').change(function(){
                      if($('#TransferSeasonsCbx').val()!='' && $('#TransferClassesCbx').val()!='' && $('#transfer_enrollment_start_date').val()!='' && $('#TransferbatchCbx').val()!=''){
                        $.ajax({
                            type: "POST",
                            url: "{{URL::to('/quick/getBatchRemainingClassesCountByBatchId')}}",
                            data: {'start_date':$('#transfer_enrollment_start_date').val(),'batch_id':$('#TransferbatchCbx').val()},
                            dataType: 'json',
                            success: function(response){
                                console.log(response);
                                if(response.status=='success'){
                                    var remclass=$('#remainingclass').val();
                                    if(response.class_count >= remclass){
                                    $('#transfermsg').html("<p class='uk-alert uk-alert-success'>Remaining Classes selected:"+remclass+"</p>");
                                    $('#availableclass').val(response.class_count);
                                    }else{
                                    $('#transfermsg').html("<p class='uk-alert uk-alert-danger'>No of classes Available:"+response.class_count+"</p>");
                                    $('#availableclass').val(response.class_count);
                                    }
                                }else{
                                    console.log('error');
                                }
                            }
                        });
                    }else{
                        $('#makeupMsgDiv').hide();
                        $('#transfermsg').html('');
                        $('#makeupMsgDiv').html("<p class='uk-alert uk-alert-danger'>please select all the required fields</p>");
                        $('#Transferbtn').hide();
                        $('#makeupMsgDiv').show('slow');
                        setTimeout(function(){
                            $('#makeupMsgDiv').slideUp();
                            $('#makeupMsgDiv').html(''); 
                            $('#makeupMsgDiv').show();
                        },3000);
                    }
                    if($('#TransferbatchCbx').val()==null){
                        $('#Transferbtn').hide();
                    }
                    if( ($('#transfer_enrollment_start_date').val()!='') && 
                        ($('#TransferSeasonsCbx').val()!='') && 
                        ($('#TransferClassesCbx').val()!='')&& 
                        ($('#TransferbatchCbx').val()!='') &&
                        ($('#TransferDescription').val()!='')){
                         $('#Transferbtn').show();
                    }else{
                        $('#Transferbtn').hide();
                    }
                    });
                    
                    $('#TransferDescription').keyup(function(){
                     if($('#transfer_enrollment_start_date').val()!='' && 
                        $('#TransferSeasonsCbx').val()!=null && 
                        $('#TransferClassesCbx').val()!=null && 
                        $('#TransferbatchCbx').val()!=null) {
                         $('#Transferbtn').show();
                     }else{
                         $('#Transferbtn').hide();
                        $('#makeupMsgDiv').hide();
                        $('#makeupMsgDiv').html("<p class='uk-alert uk-alert-danger'>please select all the required fields</p>");
                        $('#makeupMsgDiv').show('slow');
                        setTimeout(function(){
                            $('#makeupMsgDiv').slideUp();
                            $('#makeupMsgDiv').html(''); 
                            $('#makeupMsgDiv').show();
                        },3000); 
                     }
                     if($('#TransferDescription').val()==''){
                         $('#Transferbtn').hide();
                     }
                    });
                    
                    $('#transfer_enrollment_start_date').change(function(){
                       $('#TransferbatchCbx').val('');
                       $('#transfermsg').html('');
                    });
                    $('#transfer_enrollment_start_date').keyup(function(){
                        if($('#transfer_enrollment_start_date').val()==''){
                             $('#TransferbatchCbx').val('');
                             $('#transfermsg').html('');
                             $('#Transferbtn').hide();
                        }
                    })
                    
                }else{
                    $('#errorMsgDiv').hide();
                    $('#errorMsgDiv').html("<p class='uk-alert uk-alert-warning'> Selected batch have no classes left</p>");
                    $('#errorMsgDiv').show('slow');
                    setTimeout(function(){
                     $('#errorMsgDiv').slideUp();
                     $('#errorMsgDiv').html(''); 
                     $('#errorMsgDiv').show();
                    },4000);
                }
              }
            }
    });    
    
    
    
    $('#Transferbtn').click(function(){
        if($('#transfer_enrollment_start_date').val()!='' && 
           $('#TransferSeasonsCbx').val()!='' &&
           $('#TransferClassesCbx').val()!='' &&     
           $('#TransferbatchCbx').val()!=''
           ){
        console.log('working');
            $.ajax({
      type: "POST",
      url: "{{URL::to('/quick/transferkid')}}",
                        data: {'student_id':studentId,'oldbatch_id':$('#batchName').val(),
                               'customer_id':customerId,'description':$('#TransferDescription').val(),
                               'start_date':$('#transfer_enrollment_start_date').val(),
                               'season_id':$('#TransferSeasonsCbx').val(),
                               'class_id':$('#TransferClassesCbx').val(),
                               'batch_id':$('#TransferbatchCbx').val(),
                               'no_of_class':$('#remainingclass').val(),
                              },
      dataType: 'json',
      success: function(response){
                            console.log(response);
                            if(response.status==='success'){
                                $('#makeupMsgDiv').hide();
                                $('#makeupMsgDiv').html("<p class='uk-alert uk-alert-success'>successfully transferred kid. please wait till page reloads</p>");
                                $('#makeupMsgDiv').show('slow');
                                setTimeout(function(){
                                    window.location.reload(1);
                                },3000);
                            }else if(response.status==='exists'){
                                $('#makeupMsgDiv').hide();
                                $('#makeupMsgDiv').html("<p class='uk-alert uk-alert-danger'>kid enrollment already exist please select some other batch.</p>");
                                $('#makeupMsgDiv').show('slow');
                                setTimeout(function(){
                                    $('#makeupMsgDiv').slideUp();
                                    $('#makeupMsgDiv').html(''); 
                                    $('#makeupMsgDiv').show();
                                },3000);
                            }else{
                                $('#makeupMsgDiv').hide();
                                $('#makeupMsgDiv').html("<p class='uk-alert uk-alert-danger'>kid transfer cannot complete this time.</p>");
                                $('#makeupMsgDiv').show('slow');
                            }
                        }
             });
        }else{
            $('#errorMsgDiv').hide();
            $('#errorMsgDiv').html("<p class='uk-alert uk-alert-warning'>please select all the required fields.</p>");
            $('#errorMsgDiv').show('slow');
            setTimeout(function(){
                $('#errorMsgDiv').slideUp();
                $('#errorMsgDiv').html(''); 
                $('#errorMsgDiv').show();
            },4000);
        }
    });
    
  
}else{
    $('#errorMsgDiv').hide();
        $('#errorMsgDiv').html("<p class='uk-alert uk-alert-warning'> please select the year and batch</p>");
        $('#errorMsgDiv').show('slow');
        setTimeout(function(){
            $('#errorMsgDiv').slideUp();
            $('#errorMsgDiv').html(''); 
            $('#errorMsgDiv').show();
        },4000);
}
});

$('.deleteivdata').click(function(){
    $('.deletemsg').html("<p class='uk-alert uk-alert-warning'>Please wait... Introvisit Data Deleting...</p>");
    $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/deleteIVdata')}}",
        data: {'student_id':studentId},
        dataType: 'json',
  success: function(response){
            if(response.status==='success'){
                if(response.deleted_data==1){
                    $('.deletemsg').html("<p class='uk-alert uk-alert-success'>Introvisit Data Deleted...</p>");
                }else{
                    $('.deletemsg').html("<p class='uk-alert uk-alert-success'>No Introvisit Data Found...</p>");
                }
                    $('deletemsg').show('slow');
                    setTimeout(function(){
                        $('.deletemsg').slideUp();
                        $('.deletemsg').html(''); 
                        $('.deletemsg').show('slow');
                    },2000);
                
            }else{
                
                $('.deletemsg').html("<p class='uk-alert uk-alert-danger'>Sorry.. Error in Deleting Introvisit Data</p>");
                setTimeout(function(){
                        $('.deletemsg').slideUp();
                        $('.deletemsg').html(''); 
                        $('.deletemsg').show('slow');
                    },2000);
            }
        }
    });
});

$('.deletbirthdaydata').click(function(){
    $('.deletemsg').html("<p class='uk-alert uk-alert-warning'>Please wait... Birthday Data Deleting...</p>");
    $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/deletebirthdaydata')}}",
        data: {'student_id':studentId},
        dataType: 'json',
  success: function(response){
            if(response.status==='success'){
                console.log(response);
                if(response.deleted_data==1){
                    $('.deletemsg').html("<p class='uk-alert uk-alert-success'>Birthday Data Deleted...</p>");
                }else{
                    $('.deletemsg').html("<p class='uk-alert uk-alert-success'>No Birthday Data Found...</p>");
                }
                    $('deletemsg').show('slow');
                    setTimeout(function(){
                        $('.deletemsg').slideUp();
                        $('.deletemsg').html(''); 
                        $('.deletemsg').show('slow');
                    },2000);
                
            }else{
                
                $('.deletemsg').html("<p class='uk-alert uk-alert-danger'>Sorry.. Error in Deleting Birthday Data</p>");
                setTimeout(function(){
                        $('.deletemsg').slideUp();
                        $('.deletemsg').html(''); 
                        $('.deletemsg').show('slow');
                    },2000);
            }
        }
    });
});

$('.deleteenrollmentdata').click(function(){
    $('.deletemsg').html("<p class='uk-alert uk-alert-warning'>Please wait... Enrollment Data Deleting...</p>");
    $.ajax({
        type: "POST",
        url: "{{URL::to('/quick/deleteenrollmentdata')}}",
        data: {'student_id':studentId, 'customer_id':customerId},
        dataType: 'json',
  success: function(response){
            if(response.status==='success'){
                console.log(response);
                if(response.deleted_data==1){
                    $('.deletemsg').html("<p class='uk-alert uk-alert-success'>Enrollment Data Deleted...</p>");
                }else{
                    $('.deletemsg').html("<p class='uk-alert uk-alert-success'>No Enrollment Data Found...</p>");
                }
                    $('deletemsg').show('slow');
                    setTimeout(function(){
                        $('.deletemsg').slideUp();
                        $('.deletemsg').html(''); 
                        $('.deletemsg').show('slow');
                    },2000);
                
            }else{
                
                $('.deletemsg').html("<p class='uk-alert uk-alert-danger'>Sorry.. Error in Deleting Enrollment Data</p>");
                setTimeout(function(){
                        $('.deletemsg').slideUp();
                        $('.deletemsg').html(''); 
                        $('.deletemsg').show('slow');
                    },2000);
            }
        }
    });
});

</script>
@stop 
@section('content')

<div id="breadcrumb">
  <ul class="crumbs">
    <li class="first"><a href="{{url()}}" style="z-index: 9;"><span></span>Home</a></li>
    <li><a href="{{url()}}/students" style="z-index: 8;">Students</a></li>
    <li><a href="#" style="z-index: 7;">{{$student->student_name}}</a></li>
  </ul>
</div>
<br clear="all" />
 <!-- Excused Absent Modal -->
  <div id='ea' class="modal fade" role="dialog" style="margin-top: 50px; z-index: 99999;"> 
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="eaheader">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id='eamodalheader'>Make-up</h4>
        </div>
        <div class="modal-body" id="eaabsentbody">
            <div id = "makeupMsgDiv"></div>
            
            
        </div>
          
        <div class="modal-footer" id="eafooter">
          <!--<button type="submit" class="btn btn-primary" id="">Receive Payment</button>-->
          <button type="button" class="btn btn-default"  data-dismiss="modal" id='eaclose'>Close</button>
        </div>
      
      </div>
    </div>
  </div>
 

 
<!-- Modal For Old Customer Enrollment -->
<div id="EnrollOldCustomerModal" class="modal fade" role="dialog"
  style="margin-top: 50px; z-index: 99999;">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">
          Enroll Old Customer
        </h4>
      </div>
      <div class="modal-body">
        <div id = "OldCustomerMsgDiv"></div>
        <br clear = "all"/>
        <br clear = "all"/>
        <form id = "enrollOldCustomerForm" method="post" action="{{url()}}/quick/enrollOldCustomer">
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
                        <div class="parsley-row" style="margin-top: -23px;">
                                                        <input name="oldCustomerStudentId" type="hidden"
              value="{{$student->id}}" /> 
                                                        
                                                        <input name="oldCustomerId"
              type="hidden" value="{{$student->customers->id}}" />
                                                        
              <label for="enrollmentStartDateForOld">Enroll Start Date<span class="req">*</span></label>
                {{Form::text('enrollmentStartDateForOld', 
                  null,array('id'=>'enrollmentStartDateForOld', 'required'=>'','class' =>
                  'uk-form-width-medium'))}}
                        </div>
                    </div>
                    <div class="uk-width-medium-1-3">
                        <div class="parsley-row" style="margin-top: -23px;">
              <label for="enrollmentEndDateForOld">Enroll End Date<span class="req">*</span></label>
                {{Form::text('enrollmentEndDateForOld', 
                  null,array('id'=>'enrollmentEndDateForOld', 'required'=>'','class' =>
                  'uk-form-width-medium'))}}
                        </div>
                    </div>
                    <div class="uk-width-medium-1-3">
                      <div class="parsley-row" id='noofclass' style="margin-top: -23px;">
                            <span class="uk-alert uk-alert-success" id='classno' style="display:none">class No: </span>
                            <input type = "hidden" id="NoOfClassesForOld1"name="NoOfClassesForOld1"  
                class='NoOfClassesForOld1 form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;">                                            
      </div>
                    </div>
        </div>
        </br class="all"/>
        </br class="all"/>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="SeasonsCbxForOld">Seasons<span class="req">*</span></label>
                            <select id="SeasonsCbxForOld"name="SeasonsCbxForOld" required
                class='SeasonsCbxForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;">   
              </select>                                            
            </div>
          </div>

          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="ClassesCbxForOld">Classes<span class="req">*</span></label>
                            <select id="ClassesCbxForOld"name="ClassesCbxForOld" required
                class='ClassesCbxForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;">   
              </select>                                            
            </div>
          </div>

          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="BatchesCbxForOld">Batches<span class="req">*</span></label>
                            <select id="BatchesCbx" name="BatchesCbx" required
                class='BatchesCbxForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;">   
              </select>                                            
            </div>
          </div>
        </div>
        </br class="all"/>
        </br class="all"/>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="EachClassAmountForOld">Amount For Each Class<span class="req">*</span></label>
                            <input type = "number" id="EachClassAmountForOld"name="EachClassAmountForOld" required
                class='EachClassAmountForOld form-control input-sm md-input'
                                                                style="padding: 0px; font-weight: bold; color: #727272;" value="500">                                              
            </div>
          </div>
                                      
                                    <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="TotalAmountForOld">Total Amount<span class="req">*</span></label>
                                                        <input type = "number" id="TotalAmountForOld"name="TotalAmountForOld" required
                class='TotalAmountForOld form-control input-sm md-input'
                                                                style="padding: 0px; font-weight: bold; color: #727272;" value="0">                                               
            </div>
          </div>
        </div>
        
        </br class="all"/>
        </br class="all"/>
        <h4>Discounts Fields</h4>
                                <br clear="all">
                                <br clear="all">
                                <br clear="all">
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="DiscountPercentageForOld">Discounts %<span class="req">*</span></label>
                                                        <input type = "number" id="DiscountPercentageForOld"name="DiscountPercentageForOld" required value="0"
                class='DiscountPercentageForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;"> 
            </div>
          </div>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="DiscountAmountForOld">Discounts Amount<span class="req">*</span></label>
              <input type = "number" id="DiscountAmountForOld"name="DiscountAmountForOld" required value="0"
                class='DiscountAmountForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;">                                              
            </div>
          </div>
        </div>
        </br class="all"/>
        </br class="all"/>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="SiblingPercentageForOld">Sibling Consideration Discount %<span class="req">*</span></label>
                            <input type = "number" id="SiblingPercentageForOld"name="SiblingPercentageForOld" required value="0"
                class='SiblingPercentageForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;"> 
            </div>
          </div>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="SiblingAmountForOld">Sibling Consideration Amount<span class="req">*</span></label>
              <input type = "number" id="SiblingAmountForOld"name="SiblingAmountForOld" required value="0"
                class='SiblingAmountForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;">                                              
            </div>
          </div>
        </div>
        </br class="all"/>
        </br class="all"/>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="MultiClassesPercentageForOld">Multi Classes Consideration %<span class="req">*</span></label>
                            <input type = "number" id="MultiClassesPercentageForOld"name="MultiClassesPercentageForOld" required value="0"
                class='MultiClassesPercentageForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;"> 
            </div>
          </div>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="MultiClassesAmountForOld">Multi Class Consideration Amount<span class="req">*</span></label>
              <input type = "number" id="MultiClassesAmountForOld"name="MultiClassesAmountForOld" required value="0"
                class='MultiClassesAmountForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;">                                              
            </div>
          </div>
        </div>
        </br class="all"/>
        </br class="all"/>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="AdminRupeeForOld">Admin Rupee <span class="req">*</span></label>
                            <input type = "number" id="AdminRupeeForOld"name="AdminRupeeForOld" required
                class='AdminRupeeForOld form-control input-sm md-input' value="0"
                style="padding: 0px; font-weight: bold; color: #727272;"> 
            </div>
          </div>
        </div>
                                </br class="all"/>
        </br class="all"/>
                                <div class="uk-grid" data-uk-grid-margin>
                                    <div class="uk-width-medium-1-3">
                                        <div class="parsley-row">
                                            <label for="MembershipTypeForOld">Membership Type<span class="req">*</span></label>
                                            <select id="MembershipTypeForOld" name="MembershipTypeForOld" class="input-sm md-input"
                                                    style='padding: 0px; font-weight: bold; color: #727272;'>
                                                <option value=""></option>
                                            </select>                                                 
                                        </div>
                                    </div>
                                    
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="MembershipAmountForOld">Membership Amount<span class="req">*</span></label>
                                                        <input type = "number" id="MembershipAmountForOld"name="MembershipAmountForOld" value="0"
                class='MembershipAmountForOld form-control input-sm md-input'
                style="padding: 0px; font-weight: bold; color: #727272;">                                            
            </div>
          </div>
                                </div>
        </br class="all"/>
        </br class="all"/>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="SubTotalForOld">Sub Total<span class="req">*</span></label>
                            <input type = "number" id="SubTotalForOld"name="SubTotalForOld" required
                class='SubTotalForOld form-control input-sm md-input'
                                                                style="padding: 0px; font-weight: bold; color: #727272;" value="0"> 
            </div>
          </div>
        </div>
        </br class="all"/>
        </br class="all"/>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="TaxForOld">Tax<span class="req">*</span></label>
                            <input type = "number" id="TaxForOld"name="TaxForOld" required
                class='TaxForOld form-control input-sm md-input'
                                                                style="padding: 0px; font-weight: bold; color: #727272;" value="0"> 
            </div>
          </div>
        </div>
        </br class="all"/>
        </br class="all"/>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <label for="GrandTotalForOld">Grand Total<span class="req">*</span></label>
                            <input type = "number" id="GrandTotalForOld"name="GrandTotalForOld" required
                class='GrandTotalForOld form-control input-sm md-input'
                                                                style="padding: 0px; font-weight: bold; color: #727272;" value="0"> 
            </div>
          </div>
                                        <div class="uk-width-medium-1-3">
                                            <div class="parsley-row">
                                                <input id="emailOptionforoldcustomer" name="emailOptionforoldcustomer" type="checkbox"  value="yes" class="checkbox-custom"/>
            <label for="emailOptionforoldcustomer" class="checkbox-custom-label">Email Invoice<span
                        class="req">*</span></label> 
                                            </div>
                                        </div>
        </div>
        </br class="all"/>
        </br class="all"/>
        <h4>Payment Options</h4>
        <div class="uk-grid" data-uk-grid-margin>
          <div class="uk-width-medium-1-3">
            <div class="parsley-row">
              <input type="radio" name="paymentTypeRadioForOld" required
                id="paymentOptionsold_1" value="card" /> <label
                for="paymentOptionsold_1" class="inline-label">Card</label> 
              <input type="radio" name="paymentTypeRadioForOld" id="paymentOptionsold_2"
                value="cash" /> <label for="paymentOptionsold_2"
                class="inline-label">Cash</label>
              <input type="radio" name="paymentTypeRadioForOld" id="paymentOptionsold_3" value="cheque" />
                <label for="paymentOptionsold_3" class="inline-label">Cheque</label>
            </div>
          </div>
        </div>
        </br class="all"/>

        <div  id="paymentTypeForOld" style="width: 100%; display: none">
                                                            
                    <div  class="uk-grid" data-uk-grid-margin id="cardDetailsDivForOld" style = "">
                      <div class="uk-width-medium-1-1" >
              <h4>Card details</h4>
            </div>
            <div class="uk-width-medium-1-2">
              <div class="parsley-row">
                <select name="cardType3" id="cardType3"
                  class="input-sm md-input"
                  class="form-control input-sm md-input"
                  style='padding: 0px; font-weight: bold; color: #727272;'>
                    <option value="master">Master card</option>
                    <option value="maestro">Maestro</option>
                    <option value="visa">Visa</option>
                    <option value="Rupay">Rupay</option>
                </select>
              </div>
            </div>
            <div class="uk-width-medium-1-2">
              <div class="parsley-row">
                                                                <label for="cardBankName3" class="inline-label">Bank Name of your card<span class="req">*</span>
                </label> <input id="cardBankName3"  name="cardBankName3"
                  type="text"
                  class="form-control input-sm md-input"  />
                                                                
                <input id="card4digits3" number name="card4digits3"
                                                                       maxlength="4" type="hidden"  value="1234"
                  class="form-control input-sm md-input" />
              </div>
            </div>
            <br clear="all">
                                                <br clear="all">
                                                <br clear="all">      
            <div class="uk-width-medium-1-2">
              <div class="parsley-row">
                
              </div>
            </div>
                  
            <div class="uk-width-medium-1-2">
              <div class="parsley-row">
                <input id="cardRecieptNumber3" number name="cardRecieptNumber3"
                  maxlength="4" type="hidden" value='1234' 
                  class="form-control input-sm md-input" />
              </div>
            </div>

          </div>
                                                                
        </br class="all"/>                                          
          <div id="chequeDetailsDivForOld" class="uk-grid" data-uk-grid-margin style ="">
                                                                        
            <div class="uk-width-medium-1-1">
              <h4>Cheque details</h4>
              <br clear="all"/>
            </div>
                                                                       
            <br clear="all"/>
            <div class="uk-width-medium-1-2">
              <div class="parsley-row">
                <label for="chequeBankName3" class="inline-label">Bank name<span
                  class="req">*</span></label> <input id="chequeBankName3"
                  name="bankName3" type="text"
                  class="form-control input-sm md-input" />
              </div>
            </div>
            <div class="uk-width-medium-1-2">
              <div class="parsley-row">
                <label for="chequeNumber3" class="inline-label">Cheque number<span
                  class="req">*</span></label> <input id="chequeNumber3"
                  name="chequeNumber3" type="text"
                  class="form-control input-sm md-input" />
              </div>
            </div>                                               
          </div>
        </div>


      
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" >Enroll Old Customer</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>

  </div>
</div>



<div class="uk-grid" data-uk-grid-margin data-uk-grid-match
  id="user_profile">
  <div class="uk-width-large-10-10">
    <div class="md-card">
      <div class="user_heading">
        <!--
                                <div class="user_heading_menu" data-uk-dropdown="{pos:'left-top'}">
                                    <i class="md-icon material-icons md-icon-light">&#xE5D4;</i>
                                    <div class="uk-dropdown uk-dropdown-small ">
                                        <ul class="uk-nav">
                                            <li><a href="#"><i class="material-icons">delete</i> IV Data</a></li>
                                            <li><a href="#"><i class="material-icons">delete</i> Birthday Data <br> <em class="text-center" style="color:black">(with payments)</em></a></li>
                                            <li><a href="#"><i class="material-icons">delete</i> Enrollment Data<br> <em class="text-center" style="color:black">(with payments)</em></a></li>
                                        </ul>
                                    </div>
                                </div>
                                -->
        <div class="user_heading_avatar">
                                    <?php if($student->profile_image!=''){ ?>
          <img src="{{url()}}/upload/profile/student/{{$student->profile_image}}" />
                                    <?php }else{ ?>
                                        <img src="" />
                                    <?php } ?>
        </div>
        <div class="user_heading_content">
          <div class="row">
                                        <div class="col-md-5">
          <h2 class="heading_b uk-margin-bottom">
            <span class="uk-text-truncate"> {{$student->student_name}}</span><span
              class="sub-heading"><a
              href="{{url()}}/customers/view/{{$student->customers->id}}"
              style="color: #FFF;">({{$student->customers->customer_name}})</a></span>
          </h2>
          <ul class="user_stats">
            <li>
              <h4 class="heading_a">
                                        <?php echo date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->y;?> years
                                                        <?php echo date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->m;?> months 
                                                         
                                        
                                        <span class="sub-heading">Age</span>
              </h4>
            </li>
            <li>
              <h4 class="heading_a">
                {{$student->student_gender}} <span class="sub-heading">Gender</span>
              </h4>
            </li>
            <li>
              <h4 class="heading_a">
                {{date('d M Y',strtotime($student->student_date_of_birth))}} <span
                  class="sub-heading">Date of birth</span>
              </h4>
            </li>
          </ul>
                                       </div>
                                       <div class="col-md-5">
                                           <?php if(count($latestEnrolledData) > 0){?>
                                            <table class='uk-table dataTable no-footer' id='enrolledtable'>
                                             <tbody>
                                             <tr>
                                                 <th>Batch&nbsp;Name&nbsp; </th>
                                                 <th>Start&nbsp;Date&nbsp; </th>
                                                 <th>End&nbsp;Date&nbsp; </th>
                                                 <th>No&nbsp;Of&nbsp;Sessions&nbsp; </th>
                                             </tr>
                                             <?php for($i=0;$i<count($latestEnrolledData);$i++){?>
                                            <tr>
                                                <td>{{$latestEnrolledData[$i]['batch_name']}}&nbsp;</td>
                                                <td>{{$latestEnrolledData[$i]['enrollment_start_date']}}&nbsp;</td>
                                                <td>{{$latestEnrolledData[$i]['enrollment_end_date']}}&nbsp;</td>
                                                <td>{{$latestEnrolledData[$i]['selected_sessions']}}&nbsp;</td>
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                             </tbody>
                                            </table>
                                           <?php } ?>
                                       </div>
                                    </div>
                                    
        </div>
                                <a class="md-fab md-fab-small md-fab-accent" id="editKidBtn" style="right:83px"> <i
          class="material-icons">&#xE150;</i>
        </a>
                                <?php if(Session::get('userType')=='ADMIN'){ ?>
        <a class="md-fab md-fab-small md-fab-accent uk-alert-danger"  data-uk-modal="{target:'#my-id'}" id="deleteBtn"> <i
          class="material-icons">delete</i>
        </a>
                                <?php } ?>

                                <!-- This is the modal -->
                                <div id="my-id" class="uk-modal">
                                    <div class="uk-modal-dialog">
                                        <a class="uk-modal-close uk-close"></a>
                                            <div class="uk-modal-header" style="color:red;">
                                                <h3 class="uk-modal-title">Delete Data</h3>
                                            </div>
                                            <div class="modaldata">
                                                <div class="deletemsg"></div>
                                                <div class="uk-grid" data-uk-grid-margin="">
                                                    <div class="uk-width-medium-1-3 " >
                                                        <button class=" center-block text-center uk-button uk-button-danger uk-button-large deleteivdata" style="font-size:12px;">
                                                            <i class="material-icons" style="color:white">delete</i> Introvisit Data
                                                        </button>
                                                    </div>
                                                    <div class="uk-width-medium-1-3">
                                                        <button class="  center-block text-center uk-button uk-button-danger uk-button-large deletbirthdaydata" style="font-size:12px;">
                                                            <i class="material-icons" style="color:white">delete</i> Birthday Party Data
                                                        </button>
                                                        <em class="uk-text-center-small center-block text-center" style="color:black;font-size:12px;"> (with payments)</em>
                                                    </div>
                                                    <div class="uk-width-medium-1-3">
                                                        <button class="center-block text-center  uk-button uk-button-danger uk-button-large  deleteenrollmentdata" style="font-size:12px;">
                                                            <i class="material-icons" style="color:white">delete</i> Enrollment Data
                                                        </button>
                                                        <em class="uk-text-center-small text-center center-block " style="color:black;font-size:12px;">(with payments)</em>
                                                    </div>
                                                </div>
                                            </div>
         
         
                                            <div class="uk-modal-footer uk-text-right">
                                                <button type="button" class="md-btn md-btn-flat uk-modal-close ">Close</button>
                                            </div>
                                    </div>
                                </div>
                    </div>
      <div class="user_content">
        <ul id="user_profile_tabs" class="uk-tab"
          data-uk-tab="{connect:'#user_profile_tabs_content', animation:'slide-horizontal'}"
          data-uk-sticky="{ top: 48, media: 960 }">
          <li id="aboutTabheading"class="uk-active"><a href="#about">About</a></li>
          
          <li id="enrollmentsTabheading"class=""><a href="#enrollments">Enrollments</a></li>
          <li id="paymentsTabheading"class=""><a href="#payments">Payments</a></li>
          <li id="attendanceTabheading"class=""><a href="#attendace">Attendance</a></li>
                                        <!--<li id="introvisitTabheading"class=""><a href="#introvisit">Intro Visit</a></li>-->
        </ul>
        <ul id="user_profile_tabs_content" class="uk-switcher uk-margin">
          <li id="introvisitpage">
            <div class="uk-grid uk-margin-medium-top uk-margin-large-bottom"
              data-uk-grid-margin>
              <div class="uk-width-large-1-2">
                <h4 class="heading_c uk-margin-small-bottom">Kid's Information</h4>
                <ul class="md-list md-list-addon">
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->student_name}} </span>
                      <span class="uk-text-small uk-text-muted">Name</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->nickname}}</span> <span
                        class="uk-text-small uk-text-muted">Nick Name</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->student_gender}}</span>
                      <span class="uk-text-small uk-text-muted">Gender</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{date('d/m/Y',
                        strtotime($student->student_date_of_birth))}}</span> <span
                        class="uk-text-small uk-text-muted">Date of birth</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">
                                                        <?php echo date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->y;?> years
                                                        <?php echo date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->m;?> months 
                                                         
                                                        
                                                       </span> <span
                        class="uk-text-small uk-text-muted">Age</span>
                    </div>
                  </li>

                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->school}}</span> <span
                        class="uk-text-small uk-text-muted">School</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->location}}</span> <span
                        class="uk-text-small uk-text-muted">Location</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->hobbies}}</span> <span
                        class="uk-text-small uk-text-muted">Hobbies</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->emergency_contact}}</span>
                      <span class="uk-text-small uk-text-muted">Emergency Contact</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->remarks}}</span> <span
                        class="uk-text-small uk-text-muted">Remarks</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->health_issue}}</span>
                      <span class="uk-text-small uk-text-muted">Health Issue</span>
                    </div>
                  </li>




                </ul>
                <h3>Profile Picture</h3>
                <ul>

                  <li>@if (Session::has('imageUploadMessage'))
                    <div class="uk-alert uk-alert-success" data-uk-alert>
                      <a href="#" class="uk-alert-close uk-close"></a> {{
                      Session::get('imageUploadMessage') }}
                    </div> <br clear="all" /> @endif
                    <div class="md-list-content">
                      {{Form::open(array('files'=> true,
                      'url'=>'students/profile/picture'))}} <span
                        class="md-list-heading">{{Form::file('profileImage',array('required', 'class'=>'form-control'))}}</span>
                      <span class="uk-text-small uk-text-muted">Change Profile
                        Picture</span> <input name="studentId"
                        value="{{$student->id}}" type="hidden" /> <input
                        type="submit" class="btn btn-sm btn-primary"
                        value="Upload Profile Picture" /> {{Form::close()}}
                    </div> <br clear="all" />
                  </li>

                </ul>


                <h4 class="heading_c uk-margin-small-bottom">Parent's
                  Information</h4>
                <ul class="md-list md-list-addon">
                  <li>
                    <div class="md-list-addon-element">
                      <i class="md-list-addon-icon material-icons">&#xE158;</i>
                    </div>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->customers->customer_email}}
                      </span> <span class="uk-text-small uk-text-muted">Email</span>
                    </div>
                  </li>
                  <li>
                    <div class="md-list-addon-element">
                      <i class="md-list-addon-icon material-icons">&#xE0CD;</i>
                    </div>
                    <div class="md-list-content">
                      <span class="md-list-heading">{{$student->customers->mobile_no}}</span>
                      <span class="uk-text-small uk-text-muted">Phone</span>
                    </div>
                  </li>
                </ul>
              </div>
          
          </li>
                                       
          <li id="enrollments">
                                                <h4 class="heading_c uk-margin-small-bottom">Enroll Class</h4>
                                                <br clear="all" />
                                                <div class='enrollmentMsg' id='enrollmentMsg'></div>
                                                    <div class="uk-grid" data-uk-grid-margin>
                                                        <div class="uk-width-medium-1-4 ">
                                                            <label>No of Classes</label>
                                                        </div>
                                                        <div class="uk-width-medium-1-4 text-center">
                                                            <label>Discount Percentage</label>
                                                        </div>
                                                        <div class="uk-width-medium-1-4 text-center">
                                                         
                                                        </div>
                                                        <div class="uk-width-medium-1-4 text-center">
                                                            <button  class="md-btn md-btn-primary" type = "button" title = "Enroll Old Customers"  id="addOldCustomerEnrollment" style="" >Enroll Old Customer</button>   
                                                        </div>
                                                        <br clear="all"/>
                                                        
                                                        <!-- for discount enrollment -->
                                                        <?php if(isset($discountEnrollmentData)){?>
                                                        @for($i = 0; $i < count($discountEnrollmentData); $i++)
                                                        <?php if($discountEnrollmentData[$i]['number_of_classes']!=0){ ?>
                                                        <div class="uk-width-medium-1-4 ">
                                                            <input type="radio" name="enrollmentClassesSelect" value="{{$discountEnrollmentData[$i]['number_of_classes']}}" discountPercentage="{{$discountEnrollmentData[$i]['discount_percentage']}}" position="{{$i}}" class="radio-custom" id="radio_demo_inline{{$i}}"/><label for="radio_demo_inline{{$i}}" class="radio-custom-label" >{{$discountEnrollmentData[$i]['number_of_classes']}} </label>
                                                        </div>
                                                        <div class="uk-width-medium-1-4 text-center">
                                                           <label id='discount{{$i}}' value='{{$discountEnrollmentData[$i]['discount_percentage']}}'>{{$discountEnrollmentData[$i]['discount_percentage']}}</label>%
                                                        </div>
                                                        <div class="uk-width-medium-1-4 ">
                                                           <!-- <input type="number" id="discountAmount{{$i}}" class='form-control input-sm md-input text-center' style='padding:0px' value='0'/> 
                                                           !-->
                                                        </div>
                                                        <div class="uk-width-medium-1-4">
                                                            
                                                        </div>
                                                        <?php } ?>
                                                        @endfor
                                                        <?php } ?>
                                                        <?php  if(Session::get('userType') == 'ADMIN') { ?>
                                                        <div class="uk-width-medium-1-4 ">
                                                            <input type="radio" name="enrollmentClassesSelect" value="custom" class="radio-custom" id="radio_demo_inline_custom"/><label for="radio_demo_inline_custom" class="radio-custom-label" >Custom Class No</label>
                                                            <br>
                                                            <input type='number' name='customEnrollmemtNoofClass' id='customEnrollmemtNoofClass' class='form-control input-sm md-input text-center' style='padding:0px;' value='0' />
                                                        </div>
                                                        <div class="uk-width-medium-1-4 text-center">
                                                            <br>
                                                                    <input type='number' name='customEnrollmemtDiscountPercentage' id='customEnrollmemtDiscountPercentage' class='form-control input-sm md-input text-center' style='padding:0px;margin-top:10px;' value='0' />
                                                        </div>
                                                        <div class="uk-width-medium-1-4 text-center">
                                                            <br>
                                                            <!--
                                                                    <input type='number' name='customEnrollmemt' id='customEnrollmemtDiscount' class='form-control input-sm md-input text-center' style='padding:0px;margin-top:10px;' value='0' />
                                                            -->
                                                        </div>
                                                 <?php }else{ ?>
                                                        <div class="uk-width-medium-1-4 "></div>
                                                        <div class="uk-width-medium-1-4 "></div>
                                                        <div class="uk-width-medium-1-4 "></div>
                                                 <?php } ?>
                                                        
                                                        <div class="uk-width-medium-1-4 text-center">
                                                        
                                                <?php 
                                                    $studentAgeCheck = date_diff(date_create(date('Y-m-d',strtotime($student->student_date_of_birth))), date_create('today'))->y;
                                                        if($studentAgeCheck <= 12){
                                                ?>
                                    
                                                <a class="md-fab md-fab-accent" id="addEnrollment"
                                                   style="float: right;" > <i class="material-icons" title="Enroll New Customer"><!--&#xE03B;--> trending_flat</i>
                                                </a>
            <?php }?>
                                                </div>
                                                       
                                            </div>
                                                
                                                <hr>
                                               
            <h4 class="heading_c uk-margin-small-bottom">Enrolled Classes</h4>
                                            <ul class="md-list">
                                          <?php foreach ($studentEnrollments as $enrollment){?>
                                           <li>
                <div class="md-list-content">
                  <span class="md-list-heading"><a href="#">{{$enrollment->Classes->class_name}}</a></span>
                  <span class="uk-text-small uk-text-muted">{{date('d M
                    Y',strtotime($enrollment->created_at))}}</span>
                </div>
              </li>
                                           <?php }?>
                                            </ul>
                                          
                                         
                                        
                                </li>
          <li id="payments">

            <h4 class="heading_c uk-margin-small-bottom">Payments made</h4>
                <ul class="md-list">
                      
                                                                                        <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">

                                                                                         <div class="uk-width-medium-1-1">

                                                                                                       <div class="md-card uk-margin-medium-bottom">
                                                                                                                   <div class="md-card-content">
                                                                                                                         <div class="uk-overflow-container">

                                                                                                                            
                                                                                                                            <table class="uk-table table-striped" id="paymentsMadeTable" >
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                    <th class="uk-text-nowrap">Enrolled class</th>
                                                                                                                                    <th class="uk-text-nowrap">class start date</th>
                                                                                                                                    <th class="uk-text-nowrap">class end date</th>
                                                                                                                                    <th class="uk-text-nowrap">sessions</th>
                                                                                                                                    <th class="uk-text-nowrap">Amount</th>
                                                                                                                                    
                                                                                                                                    <th class="uk-text-nowrap">Received by</th>
                                                                                                                                    <th class="uk-text-nowrap">option</th>
                                                                                                                                    
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>
                                                                                                                                    
                                                                                                                                    <?php if(isset($payment_made_data[0])){ 
                                                                                                                                        for($j=0;$j<count($payment_made_data);$j++){
                                                                                                                                            for($i=0;$i<sizeof($payment_made_data[$j]);$i++){ 
                                                                                                                                        
                                                                                                                                        ?>
                                                                                                                                        <tr>
                                                                                                                                            <td>{{$payment_made_data[$j][$i]['class_name']}}</td>
                                                                                                                                            <td>{{$payment_made_data[$j][$i]['start_order_date']}}</td>
                                                                                                                                            <td>{{$payment_made_data[$j][$i]['end_order_date']}}</td>
                                                                                                                                            <td>{{$payment_made_data[$j][$i]['selected_order_sessions']}}</td>
                                                                                                                                            <td>{{$payment_made_data[$j][$i]['payment_due_amount']}}</td>
                                                                                                                                            <td>{{$payment_made_data[$j][$i]['receivedname']}}</td>
                                                                                                                                            <?php if((count($payment_made_data[$j])>1) && $i==0 ) {?>
                                                                                                                                            <td style="text-align:justify;vertical-align:middle;"  rowspan=<?php echo count($payment_made_data[$j])?> ><a id='Print' target="_blank" class="btn btn-primary btn-xs" href="{{$payments_master_details[$j]['encrypted_payment_no']}}">Print</a></td>
                                                                                                                                            <?php }else if(count($payment_made_data[$j])==1){ ?>
                                                                                                                                            <td><a id='Print'  style="text-align:justify" target="_blank" class="btn btn-primary btn-xs" href="{{$payments_master_details[$j]['encrypted_payment_no']}}">Print</a></td>
                                                                                                                                            <?php } ?>
                                                                                                                                        </tr>
                                                                                                                                     <?php }
                                                                                                                                        }
                                                                                                                                        } ?>
                                                                                                                                          
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                         </div>
                                                                                                                       </div>

                                                                                                              
                                                                                                        </div>
                                                                                             </div>
                                                                                             
                                                
                                                                                             
                                                                                            </div>
                                                                    </ul>  
          </li>


          <li id= "attendance">
            <div class="md-card">
              <div id = "errorMsgDiv"></div>
              <br clear = "all"/>
              <br clear = "all"/>
                    <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">    
                    <div class="parsley-row">
                      <label for="year">Select Year <span class="req">*</span></label><br>
                        <select id="year" name="year" class="form-control input-sm md-input" required style='padding:0px; font-weight:bold;color: #727272;'>
                          <option></option>
                      @for($i = 0; $i< count($AttendanceYeardata); $i++)
                        <option value = "{{$AttendanceYeardata[$i]->year}}">{{$AttendanceYeardata[$i]->year}}</option>
                      @endfor
                        </select>                         
                    </div>
                  </div>
                  <div class="uk-width-medium-1-3">    
                    <div class="parsley-row">
                      <label for="batchName">Select Batch Name <span class="req">*</span></label><br>
                        <select id="batchName" name="batchName" class="form-control input-sm md-input" required style='padding:0px; font-weight:bold;color: #727272;'>
                          <option></option>
                      
                        </select>
                        <input type = "hidden" value = "{{$student[0]['id']}}" id = "studentIdForAttendance">                         
                    </div>
                  </div>
                  <div class="uk-width-medium-1-3"></div>    
                </div>
                <br clear = "all"/>
                <br clear = "all"/>
                <div class="uk-grid data-uk-grid-margin">
                  <div class="uk-width-medium-1-3">
                                                            <div class="parsley-row">
                                                                    <span class="md-btn md-btn-success" style="border-radius: 15px; font-size:12px;">
                                                                        Present days - <span class = "badge" id = "Pcount" style = "background: #000"></span> 
                                                                    </span>
                                                             </div>
                                                        </div>
                                                        <div class="uk-width-medium-1-3">
                                                            <div class="parsley-row">
                                                                <span class="md-btn md-btn-warning" style="border-radius: 15px; font-size:12px;" id="excusedabsent">
                                                                    Excused absent - <span class = "badge" id = "EAcount" style = "background: #000"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <div class="uk-width-medium-1-3">
                                                        <div class="parsley-row">
                                                            <span class="md-btn md-btn-danger" style="border-radius: 15px; font-size:12px;">
                                                                 Absent days - <span class = "badge" id = "Acount" style = "background: #000"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="uk-grid data-uk-grid-margin">
                                                    <div class="uk-width-medium-1-3">
                                                        <div class="parsley-row">
                                                            <span class="md-btn md-btn-warning" id='makeupsession' style="border-radius: 15px; font-size:12px;">
                                                Make-up given <span class = "badge" id = "makeup-session" style = "background: #000"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-3">
                                                        <div class="parsley-row">
                                                            <span class="md-btn md-btn-primary" style="border-radius: 15px; font-size:12px;">
                                                                Total-Session - <span class = "badge" id = "total-session" style = "background: #000"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-3">
                                                        <div class="parsley-row">
                                                            <span class="md-btn md-btn-success" id="Transfers" style="border-radius: 15px; font-size:12px;">
                                                            Transfer</span>
                                                            </span>
                                                        </div>
                                                    </div>
                  <br clear="all"/>
                  <br clear="all"/>
                  <div class="uk-width-medium-1-1"  id = "AttendanceDiv"> 
                                  
                  </div>
                </div>
                        </div>
          </li>


                                        
          <li id="birthdays">

            <h3 class="heading_c uk-margin-small-bottom">Birthdays</h3> <br
            clear="all" />
            <div class="md-card-content large-padding">

              <div class="uk-grid" data-uk-grid-margin id="addbirthday">
                <div class="uk-width-medium-1-1">
                  <div class="parsley-row" style="margin-top: -23px;">
                    <label for="birthdayMonth">Select Birthday Month<span
                      class="req">*</span></label> <br clear="all" />
                    {{Form::text('birthdayMonth',
                    null,array('id'=>'birthdayMonth','required', 'class' => ''))}}
                  </div>
                </div>
              </div>

            </div>


            <div class="uk-grid" data-uk-grid-margin id="weekendData">              
              <div class="uk-width-medium-1-3" id="saturdaysDiv">
                <h4>Saturdays</h4>
                <div></div>
              </div>          
              <div class="uk-width-medium-1-3" id="sundaysDiv">
                <h4>Sundays</h4>
                <div></div>             
              </div>                      
              <div class="uk-width-medium-1-3">
                <h4>Timings</h4>
                <br clear="all"/>
                <div class="parsley-row" style="margin-top: -23px;">
                  <label for="birthdayTime">Select Birthday Party Start Time<span class="req">*</span></label> <br clear="all" />
                  {{Form::text('birthdayTime', null,array('id'=>'birthdayTime','required', 'class' => ''))}}
                </div>
              </div>
            </div>
            
          </li>


        </ul>
      </div>
    </div>
  </div>

</div>

<!-- 
15
2 half 
1000  
-->


<div id="enrollmentModal" class="modal fade" role="dialog"
  style="margin-top: 50px; z-index: 99999;">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Enroll Kids</h4>
      </div>
      <form id="enrollKidForm" method="post"
        action="{{url()}}/quick/enrollkid">
        <div class="modal-body">
                                        <div id="seasonMsgDiv"></div>
                                        <div id='MsgDiv'></div>
          <div id="messageStudentEnrollmentDiv"></div>
          <div id="KidsformBody">
            <input type="hidden" name="discountOnLastInstallment" id="discountOnLastInstallment" value="no"/>
            <br clear="all" /> <input name="studentId" type="hidden"
              value="{{$student->id}}" /> <input name="customerId"
              type="hidden" value="{{$student->customers->id}}" /> <input
              id="selectedSessions" name="selectedSessions" type="hidden"
              value="" />
                                                        <input type="hidden" name="noOfBatchesUsed" id="noOfBatchesUsed" value=""/>
                                                        <input type="hidden" name="bipaybatch1availablesession" value=""/>
                                                        <input type="hidden" name="bipaybatch2availablesession" value=""/>
                                                        <input type="hidden" name="bipaybatch3availablesession" value=""/>
                                                        <input type="hidden" id="estimate_master_no" name="estimate_master_no" value=""/>
                                                        <input type="hidden" id="singlePayAmount" name="singlePayAmount" value=""/>
                                                        
                                                <div class="uk-grid" data-uk-grid-margin>
                                                    <!--<div class="uk-width-medium-1-3">
                <div class="parsley-row">
                  <label for="SeasonsCbx">Seasons<span
                    class="req">*</span></label>
                                                                        <select id="SeasonsCbx"
                    name="SeasonsCbx" required
                    class='SeasonsCbx form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    
                  </select>
                                                                        
                </div>
              </div>
                                                    -->
                                                    <div class="uk-width-medium-1-3">
                                                            <div class="parsley-row" style="margin-top: -23px;">
                  <label for="enrollmentStartDate">Preferred Start date<span
                                                                                class="req">*</span></label>
                  {{Form::text('enrollmentStartDate',
                  null,array('id'=>'enrollmentStartDate', 'required'=>'','class' =>
                  'uk-form-width-medium'))}}

                                                        
                                                            </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-3">
                                                            <div class="parsley-row">
                                                            </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-3">
                                                            <div class="parsley-row">
                
                                                            </div>
                                                    </div>
                                                    
                                                    
                                                </div>
            <div class="uk-grid" data-uk-grid-margin>
              
                                                        
                                                        <div class="uk-width-medium-1-4">
                <div class="parsley-row">
                  <label for="SeasonsCbx">Seasons<span
                    class="req">*</span></label>
                                                                        <select id="SeasonsCbx"
                    name="SeasonsCbx" required
                    class='SeasonsCbx form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    
                  </select>
                                                                        
                </div>
              </div>
                                                        <div class="uk-width-medium-1-4">
                <div class="parsley-row">
                  <label for="eligibleClassesCbx">Eligible Classes<span
                    class="req">*</span></label> <select id="eligibleClassesCbx"
                    name="eligibleClassesCbx" required
                    class='eligibleClassesCbx form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    <option value=""></option>
                  </select>
                </div>
              </div>
              <div class="uk-width-medium-1-4">
                <div class="parsley-row">
                  <label for="hobbies">Batch<span class="req">*</span></label> <select
                    id="batchCbx" name="batchCbx" required
                    class='form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    <option value=""></option>
                  </select>
                </div>
              </div>
                                                    <div class="uk-width-medium-1-4">
                                                        <div class="parsley-row" id='batch1Msg'>
                                                            
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                    

            </div>
                                                <div class="uk-grid" data-uk-grid-margin id='enrollmentcontinue2'>
                                                    <div class="uk-width-medium-1-4">
                <div class="parsley-row">
                  <label for="SeasonsCbx2">Seasons<span
                    class="req">*</span></label>
                                                                        <select id="SeasonsCbx2"
                    name="SeasonsCbx2" required
                    class='SeasonsCbx2 form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    
                  </select>
                                                                        
                </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-4">
                <div class="parsley-row">
                  <label for="eligibleClassesCbx2">Eligible Classes<span
                    class="req">*</span></label> <select id="eligibleClassesCbx2"
                    name="eligibleClassesCbx2" required
                    class='eligibleClassesCbx2 form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    <option value=""></option>
                  </select>
                </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-4">
                                                        <div class="parsley-row">
                                                            <label for="hobbies">Batch<span class="req">*</span></label> <select
                    id="batchCbx2" name="batchCbx2" required
                    class='form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    <option value=""></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-4">
                                                        <div class="parsley-row" id='batch2Msg'>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="uk-grid" data-uk-grid-margin id='enrollmentcontinue3'>
                                                    <div class="uk-width-medium-1-4">
                <div class="parsley-row">
                  <label for="SeasonsCbx3">Seasons<span
                    class="req">*</span></label>
                                                                        <select id="SeasonsCbx3"
                    name="SeasonsCbx3" required
                    class='SeasonsCbx3 form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    
                  </select>
                                                                        
                </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-4">
                <div class="parsley-row">
                  <label for="eligibleClassesCbx3">Eligible Classes<span
                    class="req">*</span></label> <select id="eligibleClassesCbx3"
                    name="eligibleClassesCbx3" required
                    class='eligibleClassesCbx3 form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    <option value=""></option>
                  </select>
                </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-4">
                                                        <div class="parsley-row">
                                                            <label for="hobbies">Batch<span class="req">*</span></label> <select
                    id="batchCbx3" name="batchCbx3" required
                    class='form-control input-sm md-input'
                    style="padding: 0px; font-weight: bold; color: #727272;">
                    <option value=""></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-medium-1-4">
                                                        <div class="parsley-row" id='batch3Msg'>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <table id="sessionsTable" class="uk-table">
              <!-- <caption>Table caption</caption> -->
              <thead>
                <tr>
                  <th>Sessions Available</th>
                  <th>Amount per session</th>
                  <th>Total Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><span id="availableSessions"></span></td>
                  <td><span id="amountPerSesssion">500</span></td>
                  <td><span id="totalAmount"></span></td>
                  <td>
                    <button type="button" class="btn btn-sm btn-primary"
                      id="enrollmentOptions">View payment options</button> 
                  </td>
                </tr>
              </tbody>
            </table>

<!--            <div id="paymentOptions" class="uk-grid" data-uk-grid-margin>


              <div class="uk-width-medium-1-3">
                <div class="parsley-row">
                  <div id="singlePayDiv">
                    <input type="radio" name="paymentOptionsRadio" class="radio-custom" id="singlePayRadio" value="singlepay" /> 
                    <label id="singlePayRadiolabel" for="singlePayRadio" class="radio-custom-label">Single Pay</label>
                    <div id="singlePayAmountDiv" class="uk-grid"
                      data-uk-grid-margin>
                      <div class="uk-width-medium-1-2">
                        <div class="parsley-row" id="singlepayDivInputs"
                          style="margin-left: 20px;">

                          <input type="text" name="singlePayAmount" required
                            id="singlePayAmount" value=""
                            class="form-control input-sm md-input" />

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="uk-width-medium-1-3">
                <div class="parsley-row">

                  <div id="bipayDiv">
                    <input type="radio" name="paymentOptionsRadio" id="biPayRadio" class="radio-custom" value="bipay" /> 
                    <label id="biPayRadiolabel" for="biPayRadio" class="radio-custom-label">Bipay</label>
                    <div id="biPayAmountDiv" class="uk-grid" data-uk-grid-margin>
                      <div class="uk-width-medium-1-3">
                        <div class="parsley-row" id="bipayDivInputs"
                          style="margin-left: 20px;"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="uk-width-medium-1-3">
                <div class="parsley-row">
                  <div id="multipayDiv">
                    <input type="radio" name="paymentOptionsRadio" class="radio-custom"  id="multiPayRadio" value="multipay" /> 
                    <label id="multiPayRadiolabel" for="multiPayRadio" class="radio-custom-label">Multipay</label>
                    <div class="uk-width-medium-1-3">
                      <div class="parsley-row" id="multipayDivInputs"
                        style="margin-left: 20px;"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>-->


            <div id="finalPaymentDiv">
                                                    <br clear="all">
                                                        <div class="uk-grid" data-uk-grid-margin>
                                                            <div class="uk-width-medium-1-3">
                                                                
                                                            </div>
                                                             <div class="uk-width-medium-1-3">
                                                                
                                                            </div>
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row">
                  
                                                                    </div>
                                                                </div>
                                                        </div>
              <table id="paymentTable"
                class="uk-table table-striped table-condensed">
                <!-- <caption>Table caption</caption> -->
                <thead>
                  <tr>
                    <th></th>
                    <th  style="text-align: right; font-weight: bold">Particulars</th>
                                                                                <th style="font-weight: bold">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td></td>
                                                                                <td style="text-align:right;font-weight: bold">Classes Amount:<input type="hidden" name="totalAmountToPay"
                      id="totalAmountToPay" readonly value=""
                      class="form-control input-sm md-input" /></td>
                                                                                <td><label style="font-weight: bold" id="totalAmountToPaytotalslabel" name="totalAmountToPaytotalslabel"> </label>
                                                                                    <input type="hidden" name="totalAmountToPaytotals"
                      id="totalAmountToPaytotals" readonly value=""
                      class="" style="font-weight: bold" /></td>
                  </tr>
                  
                  <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold">
                                                                                <!--
                    <select type="hidden" name="discountPercentage" id="discountPercentage" class="input-sm md-input"
                        style='padding: 0px; font-weight: bold; color: #727272; width:50%; float:right'>
                      <option value="0">Select discount percentage</option>
                      <option value="10">10%  discount</option>
                      <option value="20">20%  discount</option>
                      <option value="30">30%  discount</option>
                      <option value="40">40%  discount</option>
                      <option value="50">50%  discount</option>
                    </select>
                    <span id="discountText">
                    
                    </span>
                                                                                -->
                                                                                <div id="discount"></div>
                                                                                
                                                                                </td>
                    <td><label style="font-weight: bold" id="discountTextBoxlabel" name="discountTextBoxlabel">-0</label>
                                                                                    
                                                                                    <input style="font-weight: bold" type="hidden"
                      name="discountTextBox" id="discountTextBox" readonly value=""
                      class="" />
                      <input type="hidden" name="discountPercentage" id="discountPercentage" value=""/> 
                                                                                        
                      
                    </td>
                  </tr>
                                                                        <?php if($discount_second_child_elligible){ ?>
                                                                        <tr>
                                                                            <td colspan="2" style="text-align: right; font-weight: bold"><div id="second_child_discount"><p>Sibling Consideration:0%</p></div></td>
                                                                            <td><label style="font-weight: bold" id="second_child_amountlabel" name="second_child_amountlabel">-0</label>
                                                                                <input style="font-weight: bold" type="hidden"
                      name="second_child_amount" id="second_child_amount" readonly value="0"
                      class="" />
                                                                                <input type = "hidden" id = "second_child_discount_to_form" name = "second_child_discount_to_form">

                      
                                                                            </td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if($discount_second_class_elligible){ ?>
                                                                        <tr>
                                                                            <td colspan="2" style="text-align: right; font-weight: bold"><div id="second_class_discount"><p>Multi Classes:0%</p></div></td>
                                                                            <td><label style="font-weight: bold" id="second_class_amountlabel" name="second_class_amountlabel">-0</label>
                                                                                
                                                                                <input style="font-weight: bold" type="hidden"
                      name="second_class_amount" id="second_class_amount" readonly value="0"
                      class="" />
                                                                                <input type = "hidden" id = "second_class_discount_to_form" name = "second_class_discount_to_form">
                      
                                                                            </td>
                                                                        </tr>
                                                                        <?php }?>
                                                                        <?php if(Session::get('userType') == 'ADMIN'){?>
                                                                            <tr>
                                                                            <td colspan="2" style="text-align: right; font-weight: bold"><div><p>Special Discount For you</p></div></td>
                                                                            <td><input style="font-weight: bold; width:50%" type="number"
                      name="admin_discount_amount" id="admin_discount_amount"  value="0"
                      class="form-control" />
                                                                            </td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(!$customermembership){?>
                  <tr>

                    <td>
                      
                    </td>
                    <td>
                                                                                    <select id="membershipType" name="membershipType" class="input-sm md-input-width-small"
                        style='padding: 0px; font-weight: bold; color: #727272;width:50%; float:right'>
                                                                                                       
                          
                                                                                    </select>
                      <input type="hidden" name="membershipAmount"
                        id="membershipAmount" readonly value=""
                        class="" />
                                                                                </td>
                    <td>
                                                                                    <label style="font-weight: bold;" id='membershipAmounttotalslabel'>0</label>
                      <input type="hidden" name="membershipAmounttotals"
                        id="membershipAmounttotals" readonly value=""
                        class="" />
                                                                                </td>
                  </tr>
                  <?php }?>
                  <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold">Subtotal</td>
                                                                                <td><label id="subtotallabel" style="font-weight: bold"></label>
                                                                                    <input style="font-weight: bold" type="hidden"
                      name="subtotal" id="subtotal" readonly value=""
                      class="" />
                      <input type="hidden" name="totalEnrollmentAmount" id="totalEnrollmentAmount"/>  
                      
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold">Tax {{$taxPercentage->tax_percentage}}% 
                                                                                    <?php 
                                                                                        if(isset($tax_data)){
                                                                                            echo "[";
                                                                                            for($i=0;$i<count($tax_data);$i++){
                                                                                                echo $tax_data[$i]['tax_particular'].':'.$tax_data[$i]['tax_percentage'].'%';
                                                                                                if($i != count($tax_data) -1){
                                                                                                    echo ", &nbsp;";
                                                                                                }
                                                                                            }
                                                                                            echo "]";
                                                                                        } 
                                                                                    ?> 
                                                                                </td>
                                                                                <td><label style="font-weight:bold" id="taxAmountlabel"></label>
                                                                                    <input style="font-weight: bold" type="hidden"
                      name="taxAmount" id="taxAmount" value="" readonly
                      class="" /></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold">Grand
                      Total</td>
                                                                                <td><label style="font-weight:bold" id='grandTotallabel' name='grandTotallabel'></label>
                                                                                    <input style="font-weight: bold" type="hidden"
                      name="grandTotal" id="grandTotal" value="" readonly
                      class=""
                      style="font-weight:bold" /></td>
                  </tr>
                </tbody>
              </table>


              <div id="paymentType" class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                  <div class="parsley-row">
                    <input type="radio" name="paymentTypeRadio" required
                      id="paymentOptions_1" value="card" /> <label
                      for="paymentOptions_1" class="inline-label">Card</label> <input
                      type="radio" name="paymentTypeRadio" id="paymentOptions_2"
                      value="cash" /> <label for="paymentOptions_2"
                      class="inline-label">Cash</label> <input type="radio"
                      name="paymentTypeRadio" id="paymentOptions_3" value="cheque" />
                    <label for="paymentOptions_3" class="inline-label">Cheque</label>

                  </div>
                </div>
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row" id="Order-date" style="display: none;">
                                                                        <label for="OrderDate">Order date<span
                    class="req">*</span></label><br>
                  {{Form::text('OrderDate',
                  null,array('id'=>'OrderDate', 'required'=>'','class' =>
                  'uk-form-width-medium'))}}
                                                                    </div>
                                                                </div>
                                                            
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row Order-Date"  style="display: none;">
                                                                       
                                                                    </div>
                                                                </div>
              </div>
                                                        
                                                    
              <div id="paymentType" style="width: 100%">
                <div id="cardDetailsDiv" class="uk-grid" data-uk-grid-margin>
                  <div class="uk-width-medium-1-1">
                    <h4>Card details</h4>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <select name="cardType" id="cardType"
                        class="input-sm md-input"
                        class="form-control input-sm md-input"
                        style='padding: 0px; font-weight: bold; color: #727272;'>
                        <option value="master">Master card</option>
                        <option value="maestro">Maestro</option>
                        <option value="visa">Visa</option>
                        <option value="Rupay">Rupay</option>
                      </select>
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                                                                                    
                      <label for="cardBankName" class="inline-label">Bank Name of your card<span class="req">*</span>
                      </label> <input id="cardBankName" number name="cardBankName"
                         type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  <br clear="all">
                                                                        <br clear="all">
                                                                        <br clear="all">
                                                                        
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                                                                                        <label for="card4digits" class="inline-label" style="display:none">Last 4 digits
                        of your card<span class="req">*</span>
                      </label> <input id="card4digits" number name="card4digits"
                                                                                               maxlength="4" type="hidden" value="1234"
                        class="form-control input-sm md-input" />
                                                                                        
                    </div>
                  </div>
                  
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="cardRecieptNumber" class="inline-label" style="display:none">Reciept number<span class="req">*</span>
                      </label> <input id="cardRecieptNumber" number name="cardRecieptNumber"
                        maxlength="4" type="hidden"  value="1234" 
                        class="form-control input-sm md-input" />
                    </div>
                  </div>

                </div>
                <div id="chequeDetailsDiv" class="uk-grid" data-uk-grid-margin>

                  <div class="uk-width-medium-1-1">
                    <h4>Cheque details</h4>
                    <br clear="all"/>
                  </div>
                  <br clear="all"/><br clear="all"/>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="chequeBankName" class="inline-label">Bank name<span
                        class="req">*</span></label> <input id="chequeBankName"
                        name="bankName" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="chequeNumber" class="inline-label">Cheque number<span
                        class="req">*</span></label> <input id="chequeNumber"
                        name="chequeNumber" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                </div>
                                                            </div>
              
                                                           <!-- id="paymentType2" for old customer 2nd order box -->
                                                            
                                                            <div  id="Order-date2" class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3" >
                  <div class="parsley-row Order-Date2" style="display:none">
                    <input type="radio" name="paymentTypeRadioOldCustomer2" required
                      id="paymentOptionsOldCustomer_1" value="card" /> <label
                      for="paymentOptionsOldCustomer_1" class="inline-label">Card</label> <input
                      type="radio" name="paymentTypeRadioOldCustomer2" id="paymentOptionsOldCustomer_2"
                      value="cash" /> <label for="paymentOptionsOldCustomer_2"
                      class="inline-label">Cash</label> <input type="radio"
                      name="paymentTypeRadioOldCustomer2" id="paymentOptionsOldCustomer_3" value="cheque" />
                    <label for="paymentOptionsOldCustomer_3" class="inline-label">Cheque</label>

                  </div>
                </div>
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row Order-Date2"  style="display: none;">
                                                                       <label for="OrderDate2">Order date<span
                    class="req">*</span></label><br>
                  {{Form::text('OrderDate2',
                  null,array('id'=>'OrderDate2', 'required'=>'','class' =>
                  'uk-form-width-medium'))}}
                                                                    </div>
                                                                  </div>
                                                            
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row Order-Date2"  style="display: none;">
                                                                       
                                                                    </div>
                                                                </div>
              </div>
                                                        
                                                    
              <div  id="paymentType2" style="width: 100%; display:none">
                                                                <div  class="uk-grid" data-uk-grid-margin id="cardDetailsDiv2">
                                                                    <div class="uk-width-medium-1-1" >
                    <h4>Card details</h4>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <select name="cardType2" id="cardType2"
                        class="input-sm md-input"
                        class="form-control input-sm md-input"
                        style='padding: 0px; font-weight: bold; color: #727272;'>
                        <option value="master">Master card</option>
                        <option value="maestro">Maestro</option>
                        <option value="visa">Visa</option>
                        <option value="Rupay">Rupay</option>
                      </select>
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="card4digits2" class="inline-label">Last 4 digits
                        of your card<span class="req">*</span>
                      </label> <input id="card4digits2" number name="card4digits2"
                        maxlength="4" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="cardBankName2" class="inline-label">Bank Name of your card<span class="req">*</span>
                      </label> <input id="cardBankName2" number name="cardBankName2"
                         type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="cardRecieptNumber2" class="inline-label">Reciept number<span class="req">*</span>
                      </label> <input id="cardRecieptNumber2" number name="cardRecieptNumber2"
                        maxlength="4" type="text" 
                        class="form-control input-sm md-input" />
                    </div>
                  </div>

                </div>
                <div id="chequeDetailsDiv2" class="uk-grid" data-uk-grid-margin>

                  <div class="uk-width-medium-1-1">
                    <h4>Cheque details</h4>
                    <br clear="all"/>
                  </div>
                  <br clear="all"/><br clear="all"/>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="chequeBankName2" class="inline-label">Bank name<span
                        class="req">*</span></label> <input id="chequeBankName2"
                        name="bankName2" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="chequeNumber2" class="inline-label">Cheque number<span
                        class="req">*</span></label> <input id="chequeNumber2"
                        name="chequeNumber2" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                </div>
                                                            
                                                            </div>
                                                            
                                                            <!-- id="paymentType3" for old customer 3rd order box -->
                                                            
                                                            <div  id="Order-date3" class="uk-grid" data-uk-grid-margin>
                                                                
                <div class="uk-width-medium-1-3" >
                  <div class="parsley-row Order-Date3" style="display:none">
                    <input type="radio" name="paymentTypeRadioOldCustomer3" required
                      id="paymentOptionsOldCustomer3_1" value="card" /> <label
                      for="paymentOptionsOldCustomer3_1" class="inline-label">Card</label> <input
                      type="radio" name="paymentTypeRadioOldCustomer3" id="paymentOptionsOldCustomer3_2"
                      value="cash" /> <label for="paymentOptionsOldCustomer3_2"
                      class="inline-label">Cash</label> <input type="radio"
                      name="paymentTypeRadioOldCustomer3" id="paymentOptionsOldCustomer3_3" value="cheque" />
                    <label for="paymentOptionsOldCustomer3_3" class="inline-label">Cheque</label>

                  </div>
                </div> 
                                                                
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row Order-Date3"  style="display: none;">
                                                                       <label for="OrderDate3">Order date<span
                    class="req">*</span></label><br>
                  {{Form::text('OrderDate3',
                  null,array('id'=>'OrderDate3', 'required'=>'','class' =>
                  'uk-form-width-medium'))}}
                                                                    </div>
                                                                  </div>
                                                                
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row Order-Date3"  style="display: none;">
                                                                       
                                                                    </div>
                                                                </div>
                                                                
              </div>
                                                        
                                                       
              <div  id="paymentType3" style="width: 100%; display:none">
                                                            
                                                                <div  class="uk-grid" data-uk-grid-margin id="cardDetailsDiv3">
                                                                    <div class="uk-width-medium-1-1" >
                    <h4>Card details</h4>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <select name="cardType3" id="cardType3"
                        class="input-sm md-input"
                        class="form-control input-sm md-input"
                        style='padding: 0px; font-weight: bold; color: #727272;'>
                        <option value="master">Master card</option>
                        <option value="maestro">Maestro</option>
                        <option value="visa">Visa</option>
                        <option value="Rupay">Rupay</option>
                      </select>
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="card4digits3" class="inline-label">Last 4 digits
                        of your card<span class="req">*</span>
                      </label> <input id="card4digits3" number name="card4digits3"
                        maxlength="4" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="cardBankName3" class="inline-label">Bank Name of your card<span class="req">*</span>
                      </label> <input id="cardBankName3" number name="cardBankName3"
                         type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="cardRecieptNumber3" class="inline-label">Reciept number<span class="req">*</span>
                      </label> <input id="cardRecieptNumber3" number name="cardRecieptNumber3"
                        maxlength="4" type="text" 
                        class="form-control input-sm md-input" />
                    </div>
                  </div>

                </div>
                                                                
                                                                
                <div id="chequeDetailsDiv3" class="uk-grid" data-uk-grid-margin>
                                                                        
                  <div class="uk-width-medium-1-1">
                    <h4>Cheque details</h4>
                    <br clear="all"/>
                  </div>
                                                                       
                  <br clear="all"/><br clear="all"/>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="chequeBankName3" class="inline-label">Bank name<span
                        class="req">*</span></label> <input id="chequeBankName3"
                        name="bankName3" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="chequeNumber3" class="inline-label">Cheque number<span
                        class="req">*</span></label> <input id="chequeNumber3"
                        name="chequeNumber3" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                                                                       
                </div>
                                                                
                                                            </div>
                                                            
                                                            
                                                             
                                                        <!--  for old customer 4th order box -->
                                                            
                                                            <div  id="Order-date4" class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3" >
                  <div class="parsley-row Order-Date4" style="display:none">
                    <input type="radio" name="paymentTypeRadioOldCustomer4" required
                      id="paymentOptionsOldCustomer4_1" value="card" /> <label
                      for="paymentOptionsOldCustomer4_1" class="inline-label">Card</label> <input
                      type="radio" name="paymentTypeRadioOldCustomer4" id="paymentOptionsOldCustomer4_2"
                      value="cash" /> <label for="paymentOptionsOldCustomer4_2"
                      class="inline-label">Cash</label> <input type="radio"
                      name="paymentTypeRadioOldCustomer4" id="paymentOptionsOldCustomer4_3" value="cheque" />
                    <label for="paymentOptionsOldCustomer4_3" class="inline-label">Cheque</label>

                  </div>
                </div>
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row Order-Date4"  style="display: none;">
                                                                       <label for="OrderDate4">Order date<span
                    class="req">*</span></label><br>
                  {{Form::text('OrderDate4',
                  null,array('id'=>'OrderDate4', 'required'=>'','class' =>
                  'uk-form-width-medium'))}}
                                                                    </div>
                                                                  </div>
                                                            
                                                                <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row Order-Date4"  style="display: none;">
                                                                       
                                                                    </div>
                                                                </div>
              </div>
                                                        
                                                    
              <div  id="paymentType4" style="width: 100%; display:none">
                                                                <div  class="uk-grid" data-uk-grid-margin id="cardDetailsDiv4">
                                                                    <div class="uk-width-medium-1-1" >
                    <h4>Card details</h4>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <select name="cardType4" id="cardType4"
                        class="input-sm md-input"
                        class="form-control input-sm md-input"
                        style='padding: 0px; font-weight: bold; color: #727272;'>
                        <option value="master">Master card</option>
                        <option value="maestro">Maestro</option>
                        <option value="visa">Visa</option>
                        <option value="Rupay">Rupay</option>
                      </select>
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="card4digits4" class="inline-label">Last 4 digits
                        of your card<span class="req">*</span>
                      </label> <input id="card4digits4" number name="card4digits4"
                        maxlength="4" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="cardBankName4" class="inline-label">Bank Name of your card<span class="req">*</span>
                      </label> <input id="cardBankName4" number name="cardBankName4"
                         type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="cardRecieptNumber4" class="inline-label">Reciept number<span class="req">*</span>
                      </label> <input id="cardRecieptNumber4" number name="cardRecieptNumber4"
                        maxlength="4" type="text" 
                        class="form-control input-sm md-input" />
                    </div>
                  </div>

                </div>
                <div id="chequeDetailsDiv4" class="uk-grid" data-uk-grid-margin>

                  <div class="uk-width-medium-1-1">
                    <h4>Cheque details</h4>
                    <br clear="all"/>
                  </div>
                  <br clear="all"/><br clear="all"/>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="chequeBankName4" class="inline-label">Bank name<span
                        class="req">*</span></label> <input id="chequeBankName4"
                        name="bankName4" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <label for="chequeNumber4" class="inline-label">Cheque number<span
                        class="req">*</span></label> <input id="chequeNumber4"
                        name="chequeNumber4" type="text"
                        class="form-control input-sm md-input" />
                    </div>
                  </div>
                </div>
                                                            </div>
                                                            
                                                            
                                                            
                                                             
                                                            
                                                        
                                                            
                                                            
                                                                    
                                                            
                                                                <!-- for invoice -->
                <div id="emailEnrollPrintDiv" class="uk-grid" data-uk-grid-margin>

                  <div class="uk-width-medium-1-1">
                    <h4>Invoice option</h4>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      
                      <input id="invoicePrintOption" name="invoicePrintOption"  value="yes"  type="checkbox"  class="checkbox-custom" />
                      <label for="invoicePrintOption"  class="checkbox-custom-label">Print Invoice<span
                        class="req">*</span></label> 
                    </div>
                  </div>
                  <div class="uk-width-medium-1-2">
                    <div class="parsley-row">
                      <input id="emailOption" name="emailOption" type="checkbox"  value="yes" class="checkbox-custom"  />
                      <label for="emailOption" class="checkbox-custom-label">Email Invoice<span
                        class="req">*</span></label> 
                    </div>
                  </div>
                </div>

              </div>

                                                        










            </div>



          
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-default" id="getSchedulesSessions" >Fetch Number of sessions</button> -->
          <button type="submit" class="btn btn-default" id="enrollNow">Enroll
            now</button>
          <button type="button" class="btn btn-default"
            id="closeEnrollmentModal" data-dismiss="modal">Close</button>
        </div>

      </form>
    </div>

  </div>
</div>





<!-- Error Div  -->
<div id="errorModal" class="modal fade" role="dialog"
  style="margin-top: 50px; z-index: 99999;">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">
          Something wrong!!!
        </h4>
      </div>
      <div class="modal-body">
        <div id="messageErrorDiv"></div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
      
    </div>

  </div>
</div>
<!-- Error Div -->


<!-- Edit Introvisit Modal Div  -->
<div id="editIntrovisitModal" class="modal fade" role="dialog"
  style="margin-top: 50px; z-index: 99999;">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="editIntrovisitModalTitle" class="modal-title">
          Edit Introvisit
        </h4>
      </div>
      <div class="modal-body">
        
        <div id="introVisitEditMessage"></div> <br clear="all" /> <br
            clear="all" />
        
         <div id="ivEditForm">
          <div class="uk-width-medium-1-2" id="introvisitEditDiv">
            <div class="parsley-row">
              <label for="iveditSelect" class="inline-label">Status<span
                        class="req">*</span></label>
              <select name="iveditSelect" id="iveditSelect" class="input-sm md-input" data-iveditid=""  class="form-control input-sm md-input" style='padding: 0px; font-weight: bold; color: #727272;'>
                <option value="ACTIVE">Active</option>
                <option value="ATTENDED">Attended</option>
                <option value="NO_SHOW">No show</option>
                <option value="IN_ACTIVE">In active</option>
                                                                <option value="RE_SCHEDULE">Re Schedule</option>
              </select>
            </div>
          </div>
           <br clear="all" /> <br clear="all" />
                                         <div class="uk-grid" data-uk-grid-margin>
                                         <div class="uk-width-medium-1-3">
                                                                    <div class="parsley-row" id="reschedule" style="margin-top: -20px;display: none;">
                    <label for="reschedule-date">Reschedule-date<span
                      class="req">*</span></label> <br>
                    {{Form::text('reschedule-date',
                    null,array('id'=>'reschedule-date', 'required'=>'', 'class' =>
                    ''))}}
                          </div>
                                         </div>
                                             <div class="uk-width-medium-1-3"></div>
                                             <div class="uk-width-medium-1-3"></div>
                                         </div>
          <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-1">
              <div class="parsley-row">
                <label for="healthIssue">Comments<span class="req">*</span></label>
                {{ Form::textarea('ivcustomerCommentTxtarea', null, ['id'=>'ivcustomerCommentTxtarea',
                'size' => '60x3', 'class' => 'form-control input-sm md-input'])
                }}
              </div>
            </div>
          </div>
        </div>  
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveIntroVisitBtn">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
      
    </div>

  </div>
</div>
<!-- Edit Introvisit Modal Div -->







<!-- Add Kids  -->
<div id="addKidsModal" class="modal fade" role="dialog"
  style="margin-top: 50px; z-index: 99999;">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">
          Edit Kids<span id="kidNameInPopup"></span>
        </h4>
      </div>
      <div class="modal-body">
        <div id="messageStudentAddDiv"></div>
        <div id="formBody">
          <form id="kidsAddForm" method="post>
                  <br  clear="all" />
          <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="customerName">Kid Name<span class="req">*</span></label>
                {{Form::text('studentName', null,array('id'=>'studentName',
                'required', 'class' => 'form-control input-sm md-input'))}}
              </div>
            </div>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="nickname">Nickname</label>
                {{Form::text('nickname', null,array('id'=>'nickname',
                'class' => 'form-control input-sm md-input'))}}
              </div>
            </div>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="studentDob">Date of birth</label>
                {{Form::text('studentDob',
                null,array('id'=>'studentDob', 'class' => '','required'))}}
              </div>
            </div>
          </div>
          <br clear="all" />
          <br clear="all" />
          <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="studentGender">Gender</label>
                <select id="studentGender" name="studentGender"
                  class="form-control input-sm md-input"
                  style="padding: 0px; font-weight: bold; color: #727272;">
                  <option value=""></option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>

              </div>
            </div>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="school">School</label>
                {{Form::text('school', null,array('id'=>'school',
                'class' => 'form-control input-sm md-input'))}}
              </div>
            </div>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="location">Location</label>
                {{Form::text('location', null,array('id'=>'location',
                 'class' => 'form-control input-sm md-input'))}}
              </div>
            </div>
          </div>
          <br clear="all" />
          <br clear="all" />
          <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="hobbies">Hobbies</label>
                {{Form::text('hobbies', null,array('id'=>'hobbies', 
                'class' => 'form-control input-sm md-input'))}}
              </div>
            </div>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="emergencyContact">Emergency contact</label>
                {{Form::text('emergencyContact',
                null,array('id'=>'emergencyContact',  'class' =>
                'form-control input-sm md-input'))}}
              </div>
            </div>
            <div class="uk-width-medium-1-3">
              <div class="parsley-row">
                <label for="remarks">Remarks</label>
                {{Form::text('remarks', null,array('id'=>'remarks', 
                'class' => 'form-control input-sm md-input'))}}
              </div>
            </div>
          </div>
          <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-1">
              <div class="parsley-row">
                <label for="healthIssue">Health Issues</label>
                {{ Form::textarea('healthIssue', null, ['id'=>'healthIssue',
                'size' => '10x3', 'class' => 'form-control input-sm md-input'])
                }}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" id="saveKidsBtn" class="md-btn md-btn-primary">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>

  </div>
</div>
<!-- Add Kids -->

<!-- pendingamount -->
<div id="pendingamount" class="modal fade" role="dialog" style="margin-top: 50px; z-index: 99999;">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          
            <h4 class="modal-title">PendingAmountPaid</h4>
        </div>
        <div class="modal-body" id="pendingamountbody">
          
        </div>
          <div class="modal-footer" id="pendingamountfooter">
          <button type="button" id="pendingamountclose" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

 <!-- history iv Modal -->
  <div id='ivhistory' class="modal fade" role="dialog" style="margin-top: 50px; z-index: 99999;"> 
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="ivhistoryheader">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Introvisit History</h4>
        </div>
        <div class="modal-body" id="ivhistorybody">
          <p></p>
        </div>
        <div class="modal-footer" id="ivhistoryfooter">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

 
 
 <!-- Receive due Modal -->
  <div id='receivedue' class="modal fade" role="dialog" style="margin-top: 50px; z-index: 99999;"> 
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="receivedueheader">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Receive Due</h4>
        </div>
        <div class="modal-body" id="receiveduebody">
            <div  class="uk-grid" data-uk-grid-margin>
                      <div class="uk-width-medium-1-1" id="receiveDueMsg"></div>
            </div>
          <div  class="uk-grid" data-uk-grid-margin>
              <div class="uk-width-medium-1-1" >
               <h4>Select Payment Type</h4>
              </div>
            </div>
          <div id="receivepaymentType" class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                  <div class="parsley-row">
                    <input type="radio" name="paymentReceiveTypeRadio" required
                      id="paymentOptionsReceive_1" value="card" /> <label
                      for="paymentOptionsReceive_1" class="inline-label">Card</label> 
                                                                                <input type="radio" name="paymentReceiveTypeRadio" id="paymentOptionsReceive_2"
                      value="cash" /> <label for="paymentOptionsReceive_2"
                      class="inline-label">Cash</label>
                                                                                <input type="radio" name="paymentReceiveTypeRadio" id="paymentOptionsReceive_3" value="cheque" />
                           <label for="paymentOptionsReceive_3" class="inline-label">Cheque</label>

              
                                                                        </div>
                                                                </div>
                                                                <div class="uk-width-medium-1-3">
                                                                    <input type="hidden" id="pending_id" value=""/>
                                                                    <input type="hidden" id="pending_amt" value=""/>
                                                                    <input type="hidden" id="pending_discount" value=""/>
                                                                </div>
           </div>
           <div id="paymentReceiveType" style="width: 100%">
    <div id="receiveCardDetailsDiv" class="uk-grid" data-uk-grid-margin>
        <div class="uk-width-medium-1-1">
                        <h4>Card details</h4>
                    </div>
                    <div class="uk-width-medium-1-2">
      <div class="parsley-row">
                            <select name="receivecardType" id="receivecardType"
        class="input-sm md-input"
        class="form-control input-sm md-input"
              style='padding: 0px; font-weight: bold; color: #727272;'>
                                <option value="master">Master card</option>
        <option value="maestro">Maestro</option>
        <option value="visa">Visa</option>
                                <option value="Rupay">Rupay</option>
                            </select>
      </div>
                    </div>
                    <div class="uk-width-medium-1-2">
      <div class="parsley-row">
                            <label for="card4digits" class="inline-label">Last 4 digits
                               of your card<span class="req">*</span>
          </label> 
                            <input id="receivecard4digits" number name="receivecard4digits"
          maxlength="4" type="text" class="form-control input-sm md-input" />
      </div>
        </div>
              <br clear="all"/><br clear="all"/>            
                    <div class="uk-width-medium-1-2">
      <div class="parsley-row">
                            <label for="receivecardBankName" class="inline-label">Bank Name of your card<span class="req">*</span>
          </label> 
                            <input id="receivecardBankName" number name="receivecardBankName"  type="text"
           class="form-control input-sm md-input" />
      </div>
        </div>
                  
        <div class="uk-width-medium-1-2">
      <div class="parsley-row">
          <label for="receivecardRecieptNumber" class="inline-label">Reciept number<span class="req">*</span>
          </label> 
                            <input id="receivecardRecieptNumber" number name="receivecardRecieptNumber"
         type="text" class="form-control input-sm md-input" />
                        </div>
                    </div>

    </div>
                <div id="receiveChequeDetailsDiv" class="uk-grid" data-uk-grid-margin>

                    <div class="uk-width-medium-1-1">
      <h4>Cheque details</h4>
                            <br clear="all"/>
                    </div>
                    <div class="uk-width-medium-1-2">
      <div class="parsley-row">
                            <label for="receivechequeBankName" class="inline-label">Bank name<span
        class="req">*</span></label> <input id="receivechequeBankName"
        name="receivebankName" type="text"
                                accept=""class="form-control input-sm md-input" />
      </div>
                    </div>
        <div class="uk-width-medium-1-2">
                        <div class="parsley-row">
                            <label for="receivechequeNumber" class="inline-label">Cheque number<span
        class="req">*</span></label> <input id="receivechequeNumber"
        name="receivechequeNumber" type="text"
        class="form-control input-sm md-input" />
                        </div>
        </div>
    </div>
                
                
                <div id="receiveemailEnrollPrintDiv" class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-medium-1-1">
      <h4>Invoice option</h4>
        </div>
        <div class="uk-width-medium-1-2">
      <div class="parsley-row">
                            <input id="receiveinvoicePrintOption" name="receiveinvoicePrintOption"  value="yes"  type="checkbox"  class="checkbox-custom" />
                                <label for="receiveinvoicePrintOption"  class="checkbox-custom-label">Print Invoice<span
                                    class="req">*</span></label> 
      </div>
                    </div>
                    <div class="uk-width-medium-1-2" id='receiveemail' style="display:none">
      <div class="parsley-row">
                            <input id="receiveemailOption" name="receiveemailOption" type="checkbox"  value="yes" class="checkbox-custom"  />
        <label for="receiveemailOption" class="checkbox-custom-label">Email Invoice<span
                                    class="req">*</span></label> 
                        </div>
                    </div>
    </div>

            </div>

       </div>     
          
        <div class="modal-footer" id="receiveduefooter">
          <button type="submit" class="btn btn-primary" id="receivepayment">Receive Payment</button>
          <button type="button" class="btn btn-default" id='receivedueclose'>Close</button>
        </div>
      
      
    </div>
  </div>

      
      


@stop
