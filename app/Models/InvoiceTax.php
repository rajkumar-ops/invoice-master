<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CurrencyConversion;

class InvoiceTax extends Model
{
	use CurrencyConversion;

	/**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function tax_type()
    {
    	return $this->hasOne('App\Models\TaxTypes',"id","tax_type_id");
    }
}
