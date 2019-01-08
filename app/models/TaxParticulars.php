<?php

class TaxParticulars extends \Eloquent {
	
        protected $table= "tax_particular";
        protected $fillable = [];

        public static function insertCgstTaxParicularNewFranchisee($inputs, $franchiseeId) {
        	$tax = new TaxParticulars();
        	$tax->franchisee_id = $franchiseeId;
        	$tax->tax_particular = 'CGST';
        	$tax->tax_percentage = $inputs['cgst'];
        	$tax->tax_particular_for = 'both';
        	$tax->created_by = Session::get('userId');
        	$tax->created_at = date('Y-m-d H:i:s');
        	$tax->save();

        	return $tax;
        }

        public static function insertSgstTaxParicularNewFranchisee($inputs, $franchiseeId) {
        	$tax = new TaxParticulars();
        	$tax->franchisee_id = $franchiseeId;
        	$tax->tax_particular = 'SGST';
        	$tax->tax_percentage = $inputs['sgst'];
        	$tax->tax_particular_for = 'both';
        	$tax->created_by = Session::get('userId');
        	$tax->created_at = date('Y-m-d H:i:s');
        	$tax->save();

        	return $tax;
        }


        public static function updateVatTaxParicularNewFranchisee($inputs) {
                $tax = TaxParticulars::where('franchisee_id', '=', $inputs['franchisee_id'])
                                     ->where('tax_particular', '=', 'VAT')
                                     ->update([
                                        'tax_percentage' => $inputs['vat'],
                                        'tax_particular_for' => 'both',
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Session::get('userId')
                                     ]);
                return $tax;
        }

        public static function updateCgstTaxParicularNewFranchisee($inputs) {
                $tax = TaxParticulars::where('franchisee_id', '=', $inputs['franchisee_id'])
                                     ->where('tax_particular', '=', 'CGST')
                                     ->update([
                                        'tax_percentage' => $inputs['cgst'],
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Session::get('userId')
                                     ]);
                return $tax;
        }

        public static function updateSgstTaxParicularNewFranchisee($inputs) {
                $tax = TaxParticulars::where('franchisee_id', '=', $inputs['franchisee_id'])
                                     ->where('tax_particular', '=', 'SGST')
                                     ->update([
                                        'tax_percentage' => $inputs['sgst'],
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => Session::get('userId')
                                     ]);
                return $tax;
        }
        
}