<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\InvoiceDataTable;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\InvoiceTax;
use App\Models\User;
use App\Models\TaxTypes;
use Lang;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->base_path = 'admin.invoice.';
        $this->base_url = $this->view_data['base_url'] = route('admin.invoice');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InvoiceDataTable $dataTable)
    {
        return $dataTable->render($this->base_path.'view');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['result'] = new Invoice;
        $this->view_data['customers'] = User::get();
        $this->view_data['tax_types'] = TaxTypes::activeOnly()->get();
        $this->view_data['invoice_tax_items'] = collect(array());
        $this->view_data['selectedTaxItems'] = collect(array());
        return view($this->base_path.'add', $this->view_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $current_dateObj = Carbon::now();

        $invoice = new Invoice;
        $invoice->invoice_template_id = 1;
        $invoice->user_id = $request->customer;
        $invoice->agency_id = $request->agency;
        $invoice->invoice_date = $current_dateObj->format('Y-m-d');
        $invoice->due_date = $current_dateObj->addDays(7)->format('Y-m-d');
        $invoice->status = "Pending";
        $invoice->paid_status = "Pending";
        $invoice->notes = $request->notes;
        $invoice->currency_code= getCurrencyCode();
        $invoice->unique_hash = \Str::uuid()->toString();

        $invoice->save();

        $invoice_total = 0;
        $invoice_subtotal = 0;
        $invoice_discount = 0;

        foreach ($request->invoice_item as $invoice_item) {
            $item = new InvoiceItems;
            $item->invoice_id   = $invoice->id;
            $item->agency_id    = $invoice->agency_id;
            $item->currency_code= getCurrencyCode();
            $item->name         = $invoice_item["name"];
            $item->description  = $invoice_item["description"] ?? '';
            $item->price        = $invoice_item["price"];
            $item->quantity     = $invoice_item["quantity"];
            $item->discount     = $invoice_item["discount"];
            $item->tax          = $invoice_item["tax"] ?? 0;

            $total_price        = ($item->price * $item->quantity) + $item->tax;
            $item->sub_total    = $total_price;
            $total_price        -= $item->discount;
            $item->total        = $total_price;
            $item->save();

            $invoice_subtotal   += $item->sub_total;
            $invoice_total      += $item->total;
            $invoice_discount   += $item->discount;
        }

        $tax_items = explode(',',$request->tax_items);
        $total_tax = 0;
        foreach ($tax_items as $tax_item) {
            $tax_type = tax_types($tax_item);
            if(optional($tax_type)->value != '') {
                if($tax_type->type == 'percent') {
                    $tax_value = $invoice_total*($tax_type->value / 100);
                }
                else {
                    $tax_value = $tax_type->value;
                }

                $invoice_tax = new InvoiceTax;
                $invoice_tax->tax_type_id   = $tax_type->id;
                $invoice_tax->invoice_id    = $invoice->id;
                $invoice_tax->agency_id     = $invoice->agency_id;
                $invoice_tax->name          = $tax_type->name;
                $invoice_tax->percent       = $tax_type->value;
                $invoice_tax->amount        = $tax_value;
                $invoice_tax->save();

                $total_tax += numberFormat($invoice_tax->amount);
            }
        }

        $invoice->sub_total = $invoice_subtotal;
        $invoice->discount  = $invoice_discount;
        $invoice->round_off = $request->round_off ?? 0;
        $invoice->total_tax = $total_tax;
        $invoice->total = $invoice_total + $total_tax;
        $invoice->invoice_number = "INV".strPadLeft($request->customer).strPadLeft($invoice->id);
        $invoice->save();

        flashMessage('success', Lang::get('admin_messages.success'), Lang::get('admin_messages.updated_successfully'));

        return redirect($this->base_url);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->view_data['result'] = Invoice::with('invoice_items')->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['customers'] = User::get();
        $this->view_data['tax_types'] = TaxTypes::activeOnly()->get();
        $this->view_data['result'] = $result = Invoice::with('invoice_items')->findOrFail($id);
        $invoice_tax_items = $result->invoice_taxes->map(function($invoice_tax) {
            $tax_type = $invoice_tax->tax_type;
            $invoice_tax_item = $tax_type->only(['id','agency_id','name','description','type','value']);
            $invoice_tax_item['total'] = $invoice_tax->currency_symbol.''.$invoice_tax->amount;
            $invoice_tax_item['tax_value'] = $invoice_tax->currency_symbol.''.$invoice_tax->amount;
            if($tax_type->type == 'percent') {
                $invoice_tax_item['tax_value'] = $tax_type->value.'%';
            }
            return $invoice_tax_item;
        });
        $this->view_data['invoice_tax_items'] = $invoice_tax_items;
        $this->view_data['selectedTaxItems'] = $invoice_tax_items->pluck('name');
        return view($this->base_path.'edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($request);

        $current_dateObj = Carbon::now();

        $invoice = Invoice::findOrFail($id);
        $invoice->agency_id = $request->agency;
        $invoice->invoice_date = $current_dateObj->format('Y-m-d');
        $invoice->notes = $request->notes;
        $invoice->currency_code= getCurrencyCode();

        $invoice_total = 0;
        $invoice_subtotal = 0;
        $invoice_discount = 0;
        $updated_ids = [];
        foreach ($request->invoice_item as $invoice_item) {
            $item = InvoiceItems::findOrNew($invoice_item["id"]);
            $item->invoice_id   = $invoice->id;
            $item->agency_id    = $invoice->agency_id;
            $item->currency_code= getCurrencyCode();
            $item->name         = $invoice_item["name"];
            $item->description  = $invoice_item["description"] ?? '';
            $item->price        = $invoice_item["price"];
            $item->quantity     = $invoice_item["quantity"];
            $item->discount     = $invoice_item["discount"];
            $item->tax          = $invoice_item["tax"] ?? 0;

            $total_price        = ($item->price * $item->quantity) + $item->tax;
            $item->sub_total    = $total_price;
            $total_price        -= $item->discount;
            $item->total        = $total_price;
            $item->save();

            $invoice_subtotal   += $item->sub_total;
            $invoice_total      += $item->total;
            $invoice_discount   += $item->discount;

            $updated_ids[] = $item->id; 
        }

        InvoiceItems::where('invoice_id',$id)->whereNotIn("id",$updated_ids)->delete();

        $tax_items = explode(',',$request->tax_items);
        $total_tax = 0;
        $updated_ids = [];
        foreach ($tax_items as $tax_item) {
            $tax_type = tax_types($tax_item);
            if(optional($tax_type)->value != '') {
                if($tax_type->type == 'percent') {
                    $tax_value = $invoice_total*($tax_type->value / 100);
                }
                else {
                    $tax_value = $tax_type->value;
                }

                $invoice_tax = InvoiceTax::firstOrNew(["tax_type_id" => $tax_type->id,'invoice_id' => $invoice->id]);
                $invoice_tax->tax_type_id   = $tax_type->id;
                $invoice_tax->invoice_id    = $invoice->id;
                $invoice_tax->agency_id     = $invoice->agency_id;
                $invoice_tax->name          = $tax_type->name;
                $invoice_tax->percent       = $tax_type->value;
                $invoice_tax->amount        = $tax_value;
                $invoice_tax->save();

                $total_tax += numberFormat($invoice_tax->amount);
                $updated_ids[] = $invoice_tax->id; 
            }
        }
        InvoiceTax::where('invoice_id',$id)->whereNotIn("id",$updated_ids)->delete();

        $invoice->sub_total = $invoice_subtotal;
        $invoice->discount  = $invoice_discount;
        $invoice->round_off = $request->round_off ?? 0;
        $invoice->total_tax = $total_tax;
        $invoice->total = $invoice_total + $total_tax;
        $invoice->save();

        flashMessage('success', Lang::get('admin_messages.success'), Lang::get('admin_messages.updated_successfully'));

        return redirect($this->base_url);
    }

    public function destroy($id)
    {
        $can_destroy = $this->canDestroy($id);
        
        if(!$can_destroy['status']) {
            flashMessage('danger', Lang::get('admin_messages.failed'), $can_destroy['status_message']);
            return redirect($this->base_url);
        }
        
        try {
            InvoiceItems::where('invoice_id',$id)->delete();
            InvoiceTax::where('invoice_id',$id)->delete();
            Invoice::where('id',$id)->delete();
            flashMessage('success', Lang::get('admin_messages.success'), Lang::get('admin_messages.delete_success'));
        }
        catch (Exception $e) {
            flashMessage('danger', Lang::get('admin_messages.failed'), $e->getMessage());
        }

        return redirect($this->base_url);
    }

    /**
     * Validate the Given Request
     *
     * @param  Illuminate\Http\Request $request_data
     * @param  Int $id
     * @return Array
     */
    protected function validateRequest($request_data, $id = '')
    {
        $rules = array(
            "customer"  => "required",
            "agency"    => "required",
        );

        $attributes = array(
            "customer"  => "Customer",
            "agency"    => "Agency",
        );

        $this->validate($request_data,$rules,[],$attributes);
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy($id)
    {
        return ['status' => true,'status_message' => ''];
    }
}
