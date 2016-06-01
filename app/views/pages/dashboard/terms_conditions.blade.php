@extends('layout.master')

@section('libraryCSS')

@stop

@section('libraryJS')
<script type="text/javascript">
	function saveTermsConditions(){
		if($('#termsConditions').val() != '' && $('#termCondId').val() != ''){
			$.ajax({
				url : "{{URL::to('/quick/addTermsAndConditions')}}",
				type: "post",
				data: {'text': $('#termsConditions').val(), 'id': $('#termCondId').val()},
				dataType: "json",
				success:function(response){
					//console.log(response);
					$('#msgDiv').html('<h5 alert class = "uk-alert-success" style = "color: #fff; width: 100%; padding: 12px;">Updated Terms and Conditions Successfully. Please wait until this page reloads.</h5>');
					setTimeout(function(){
                    	window.location.reload(1);
                    }, 2000);  
				}
			});
		}else{
			$('#msgDiv').html('<h5 alert class = "uk-alert-danger" style = "color: #fff; width: 100%; padding: 12px;">Please Write Conditions and save.</h5>');
			setTimeout(function(){
                $('#msgDiv').html('');            
            }, 2000);  
		}
	}
</script>
@stop

@section('content')

<div class="md-card">
	<div class="md-card-content large-padding">
		<div id = "msgDiv"></div>
		<h3 class="heading_b uk-margin-bottom">Add Terms and Conditions Here</h3>
		<br clear="all"/>
		<br clear="all"/>
		<div class="uk-grid" data-uk-grid-margin>
		    <div class="uk-width-medium-1-1">
        		<div class="parsley-row form-group">
        			<label for="terms_conditions">Terms And Conditions<span class="req">*</span></label>
				        <textarea class = "form-control input-sm md-input" id = "termsConditions" name = "termsConditions" required rows = "8">{{$getTermsAndConditions->terms_conditions}}</textarea>
				        <input type = "hidden" id = "termCondId" value = "{{$getTermsAndConditions->id}}">
        	    </div>
    	    </div>
    	    <div class="row" style = "margin-top: 1em;">
                <div class="col-md-12">
			         <button type="button" id="saveTerms" onclick = "saveTermsConditions()" class="md-btn md-btn-primary pull-right" style = "margin-right: 7em">Save Changes</button>
                </div>
            </div>
    	</div>
	</div>
</div>
@stop