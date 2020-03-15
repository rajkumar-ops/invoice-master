<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CurrencyConversion;

class Invoice extends Model
{
	use CurrencyConversion;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function agency()
    {
        return $this->belongsTo('App\Models\AgencyDetails','agency_id','admin_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
	
    public function invoice_items()
    {
    	return $this->hasMany('App\Models\InvoiceItems');
    }

    public function invoice_taxes()
    {
    	return $this->hasMany('App\Models\InvoiceTax');
    }

    public function getDiscountSubTotalAttribute()
    {
        return $this->sub_total - $this->discount;
    }

    public function getInvoiceDateFormattedAttribute()
    {
        return date('F d, Y',strtotime('invoice_date'));
    }
}
