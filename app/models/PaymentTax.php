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
        /*if($inputs['franchisee_id'] == 11) {
            $updateTax = PaymentTax::where('franchisee_id', '=', $inputs['franchisee_id'])
                            ->update([
                                'tax_percentage' => $inputs['tax'],
                                'updated_at' => date("Y-m-d H:i:s"),
                                'updated_by' => Session::get('userId')
                            ]);
                        } else {*/
                             $sum = 0;
                            for ($i=0; $i < count($inputs['tax']); $i++) {
                                 $sum += $inputs['tax'][$i]['value'];
                                }
                            $updateTax = PaymentTax::where('franchisee_id', '=', $inputs['franchisee_id'])
                            ->update([
                                'tax_percentage' => $sum,
                                'updated_at' => date("Y-m-d H:i:s"),
                                'updated_by' => Session::get('userId')
                            ]);
                      //  }
                        
        

        return $updateTax;
    }
}