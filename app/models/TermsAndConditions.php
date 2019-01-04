<?php

class TermsAndConditions extends \Eloquent {
	protected $fillable = [];
	protected $table = "terms_conditions";

	static function addTermsAndConditions($data){
		//return $data;
		$update  = TermsAndConditions::find($data['id']);
		$update->terms_conditions = $data['text'];
		$update->save();
		return $update;
	}

	public static function newFranchiseeTermsAndCon($inputs, $franchiseeId) {
		$terms = new TermsAndConditions();
		$terms->id = TermsAndConditions::max('id') + 1;
		$terms->franchisee_id = $franchiseeId;
		$terms->terms_conditions = "* This receipt confirms the number of classes you have paid for. These classes must be consumed / attended within the last date mentioned on your receipt, which is the expiry date of your classes. 

* Please note that your child has enrolled for a specific class at a specific time that we have reserved for him / her, and therefore your child will have to attend these specific classes. 
However, At ‘The Little Gym’, we do recognize that our children do miss classes; therefore we do offer 'make up classes.  You must give us an advanced notice for an ‘Excused absence’ to avail this privilege. 

* Make up classes have to be consumed within the period of your payment plan. However, we do offer an extension on consuming your make-up classes depending on the number of classes that you had enrolled for.

* 10 classes can be extended by a maximum of 2 weeks (14 days), 20 classes can be extended by a maximum of 1 month (30 days) and 40 classes can be extended by a maximum of 2 months (60 days).

* For example – If you have enrolled your child into classes for 10 weeks, the ‘make up classes’ will have to be consumed within 12 weeks and cannot be moved to the 13th week.

* Please note that the ‘make up’ classes are a privilege and is based on the availability of slots and timings within your payment period.

* In the event that the time specified in this receipt, as requested by you, is no longer convenient to you, you could speak to your front desk at ‘The Little Gym’ and have your child moved to a similar class on a different day of the week, if such slots are available. 

* In the event, you are unable to continue these classes for any personal reason,  we are happy to refund the classes that have not been used from the date of us being informed of your intent to discontinue your membership or attendance of these classes. However, this can only apply for future classes not attended, and does not include make up classes for the prior weeks. The refund will be arrived at after deducting the number of classes you have taken which each class taken calculated at the price of a single class.

* The annual / lifetime membership fees paid by you are not refundable.
The taxes paid by you are not refundable. 
";
		$terms->created_by = Session::get('userId');
		$terms->created_at = date('Y-m-d H:i:s');
		$terms->save();

		return $terms;
	}

	public static function updateTermsAndCondtions ($inputs) {
		$update = TermsAndConditions::where('franchisee_id', '=', $inputs['franchisee_id'])
								    ->update(array(
								    	'terms_conditions' => $inputs['terms_conditions']
								    ));
		return $update;								    
	}	
}