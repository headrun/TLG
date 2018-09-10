<?php

class BirthdayBasePrice extends \Eloquent {
	protected $fillable = [];
        protected $table = "birthday_base_price";
        
        
   static function getBirthdaybasePrice (){
       return BirthdayBasePrice::where('franchisee_id','=',Session::get('franchiseId'))
                                 ->first();
   }

   public static function createBdayPriceForNew ($inputs, $franchiseId) {
   	 $bday = new BirthdayBasePrice();
   	 $bday->franchisee_id = $franchiseId;
   	 $bday->default_birthday_price = $inputs['default_birthday_price'];
   	 $bday->member_birthday_price = $inputs['member_birthday_price'];
   	 $bday->default_advance_amount = $inputs['default_advance_amount'];
   	 $bday->additional_guest = $inputs['additional_guest'];
   	 $bday->additional_half_hour = $inputs['additional_half_hour'];
   	 $bday->save();
   	 return $bday;
   }

   public static function updateBdayPricing($inputs) {
     $updateBdayPrice = BirthdayBasePrice::where('franchisee_id', '=', $inputs['franchisee_id'])
                                         ->update([
                                            'default_birthday_price' => $inputs['default_birthday_price'],
                                            'member_birthday_price' => $inputs['member_birthday_price'],
                                            'default_advance_amount' => $inputs['default_advance_amount'],
                                            'additional_guest' => $inputs['additional_guest'],
                                            'additional_half_hour' => $inputs['additional_half_hour']
                                          ]);
     return $updateBdayPrice;
   }
}