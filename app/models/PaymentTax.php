<?php

class PaymentTax extends \Eloquent {
	protected $fillable = [];
        protected $table="payment_tax";
        
        
        
        static function getTaxPercentageForPayment(){
            return PaymentTax::where('franchisee_id','=',Session::get('franchiseId'))
                               ->select('tax_percentage')->first();
        }
}