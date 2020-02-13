<div class="card-body">
	<div class="row">
		<div class="col-md-12">
			<div class="card card-invoice">
				<h3 class="text-center mt-4"> Tax Invoice </h3>
				{{--
				<div class="card-header">
					<div class="invoice-header">
						<div class="invoice-agency">
							<img src="http://demo.themekita.com/azzara/livepreview/assets/img/examples/logoinvoice.svg" alt="company logo">
							<div class="invoice-desc">
								Bandung, West Java, Indonesia<br/>
								Fax 621113
							</div>
						</div>
					</div>
				</div>
				--}}
				<div class="card-body">
					<div class="separator-solid"></div>
					<div class="row">
						<div class="col-md-4 info-invoice">
							<h5 class="sub">Date</h5>
							<p> {{ date('F d, Y') }} </p>
						</div>
						<div class="col-md-4 info-invoice">
							{{--
							<h5 class="sub">Invoice ID</h5>
							<p>#FDS9876KD</p>
							--}}
						</div>
						<div class="col-md-4 info-invoice">
							<h5 class="sub"> Invoice To </h5>
							<p>
								<select name="customer" class="form-control py-0">
									<option value=""> @lang('admin_messages.select') </option>
									@foreach($customers as $customer)
									<option value="{{ $customer->id }}"> {{ $customer->first_name }} </option>
									@endforeach
								</select>
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="invoice-detail">
								<div class="invoice-top">
									<h3 class="title"><strong>Order summary</strong></h3>
								</div>
								<div class="invoice-item" ng-init="invoice_items={{ json_encode(array(['name' => ''])) }}">
									<div class="table-responsive">
										<table class="table table-striped invoice-item_list">
											<thead>
												<tr>
													<th><strong> # </strong></th>
													<th class="text-center"> Item </th>
													<th class="text-center"> Price </th>
													<th class="text-center"> Quantity </th>
													<th class="text-center"> Discount </th>
													<th class="text-center"> Totals </th>
													<th class="text-right"> Action </th>
												</tr>
											</thead>
											<tbody>
												<tr ng-repeat="invoice_item in invoice_items">
													<td> @{{ $index+1 }} </td>
													<td class="text-center"> <input type="text" name="invoice_item[@{{$index}}][name]" ng-model="invoice_item.name"> </td>
													<td class="text-center"> <input type="text" name="invoice_item[@{{$index}}][price]" ng-model="invoice_item.price" ng-change="updateInvoiceTotal();"> </td>
													<td class="text-center"> <input type="text" name="invoice_item[@{{$index}}][quantity]" ng-model="invoice_item.quantity" ng-change="updateInvoiceTotal();"> </td>
													<td class="text-center"> <input type="text" name="invoice_item[@{{$index}}][discount]" ng-model="invoice_item.discount" ng-change="updateInvoiceTotal();"> </td>
													<td class="text-center"> <input type="text" name="invoice_item[@{{$index}}][total]" ng-model="invoice_item.total" readonly> </td>
													<td class="text-center"> <a href="javascript:;" class="text-danger" ng-click="removeInvoiceItem($index);"> <i class="fa fa-times"></i> </a> </td>
												</tr>
											</tbody>
										</table>
										<button type="button" class="btn btn-primary m-2 p-1" ng-click="addInvoiceItem();"> Add Item </button>
									</div>
								</div>
							</div>
							<div class="separator-solid mb-3"></div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="row" ng-init="tax_types={{ $tax_types }}">
						<div class="col-sm-7 col-md-5 mb-3 mb-md-0 transfer-to">
							
						</div>
						<div class="col-sm-5 col-md-7 transfer-total">
							<table class="table table-clear">
								<tbody>
									<tr>
										<td class="left">
											<span class="font-weight-bolder"> Sub total </span>
										</td>
										<td class="empty-row"> </td>
										<td class="right"> {{ $curreny_symbol }} @{{ invoice_total }} </td>
									</tr>
									<tr ng-repeat="tax_type in added_tax_types">
										<td class="left">
											<span class="font-weight-bolder"> @{{ tax_type.name }} </span>
										</td>
										<td class="empty-row"> {{ $curreny_symbol }} @{{ tax_type.value }} </td>
										<td class="right"> {{ $curreny_symbol }} @{{ tax_type.total }} </td>
									</tr>
									<tr>
										<td class="left">
											<select class="form-control py-2" ng-model="all_tax_types" ng-chage="addTaxItem()">
												<option value=""> Select </option>
					                            <option ng-repeat="(key,tax_type) in tax_types" value="@{{ tax_type.name }}" ng-if="( key | checkKeyValueUsedInStack : 'name': tax_types) || all_tax_types == key"> @{{ tax_type.name }} </option>
											</select>
										</td>
										<td class="empty-row"> </td>
										<td class="empty-row"></td>
									</tr>
									<tr>
										<td class="left">
											<span class="font-weight-bold"> Total Amount </span>
										</td>
										<td class="empty-row"> </td>
										<td class="right">
											<span class="font-weight-bold"> {{ $curreny_symbol }} @{{ invoice_total }} </span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="separator-solid"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="card-action">
	<button type="submit" class="btn btn-success float-right"> @lang('admin_messages.submit') </button>
	<a class="btn btn-danger" href="{{ route('admin.invoice') }}"> @lang('admin_messages.cancel') </a>
</div>