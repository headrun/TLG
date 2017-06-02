<?php

use Illuminate\Support\Facades\DB;
class InvoiceData extends \Eloquent {
	protected $fillable = [];
	protected $table = 'invoice_data';
	public $timestamp = false;
	
}