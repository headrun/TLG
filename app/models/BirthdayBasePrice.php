<?php

class BirthdayBasePrice extends \Eloquent {
	protected $fillable = [];
        protected $table = "birthday_base_price";
        
        
   static function getBirthdaybasePrice (){
       return BirthdayBasePrice::where('franchisee_id','=',Session::get('franchiseId'))
                                 ->first();
   }
}