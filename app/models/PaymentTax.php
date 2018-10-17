<?php

class PaymentTax extends \Eloquent {
	protected $fillable = [];
    protected $table="payment_tax";
    
    
    
    static function getTaxPercentageForPayment(){
        return PaymentTax::where('franchisee_id','=',Session::get('franchiseId'))
                           ->select('tax_percentage')->first();
    }
   
    public static function insertPaymentTaxForNewFranchisee($inputs, $franchiseId) {
    	$tax = new PaymentTax();
    	$tax->franchisee_id = $franchiseId;
    	$tax->tax_percentage = $inputs['cgst'] + $inputs['sgst'];
    	$tax->created_by=Session::get('userId');
    	$tax->created_at = date("Y-m-d H:i:s");
    	$tax->save();

    	return $tax;
    }

    public static function updatePaymentTaxForNewFranchisee($inputs) {
        $updateTax = PaymentTax::where('franchisee_id', '=', $inputs['franchisee_id'])
                            ->update([
                                'tax_percentage' => $inputs['cgst'] + $inputs['sgst'],
                                'updated_at' => date("Y-m-d H:i:s"),
                                'updated_by' => Session::get('userId')
                            ]);

        return $updateTax;
    }
}