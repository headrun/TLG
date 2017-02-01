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
}