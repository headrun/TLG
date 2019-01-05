<?php

use Illuminate\Support\Facades\DB;
class InvoiceData extends \Eloquent {
	protected $fillable = [];
	protected $table = 'invoice_data';
	public $timestamp = false;
    public static function insertNewInvoiceData($inputs, $franchiseeId) {
    	$invoice = new InvoiceData();
    	$invoice->franchise_id = $franchiseeId;
    	$invoice->franchise_name = $inputs['firstName'].' '.$inputs['lastName'];
    	$invoice->legal_entry_name = $inputs['legalEntity'];
    	$invoice->pan_no = $inputs['pan_no'];
    	$invoice->service_tax_no = $inputs['service_tax_no'];
    	$invoice->tin_no = $inputs['tin_no'];
    	$invoice->tan_no = '';
        $invoice->gst_no = $inputs['gst_no'];
    	$invoice->created_at = date("Y-m-d H:i:s");
    	$invoice->save();
    	
    	return $invoice;
    }

    public static function updateInvoiceDetails($inputs) {
        $updateDetails = InvoiceData::where('franchise_id', '=', $inputs['franchisee_id'])
                                    ->update([
                                      'franchise_name' => $inputs['franchisee_name'],
                                      'legal_entry_name' => $inputs['legalEntity'],
                                      'pan_no' => $inputs['pan_no'],
                                      'service_tax_no' => $inputs['service_tax_no'],
                                      'tin_no' => $inputs['tin_no'],
                                      'gst_no' => $inputs['gst_no'],
                                    ]);

        return $updateDetails;                                    
    } 	
}