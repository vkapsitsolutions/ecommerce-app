@extends('layouts.app')

@section('content')
<?php
use App\Http\Controllers\ProductController;
?>
<div class="row">
	<div class="col-md-12">
		<h5>Order #: {{ $order->order_number }}</h5>
		<h6 class="text-capitalize">Order Status: <span class="badge badge-primary">{{ $order->status }}</span></h6>
		<h6>Order Date: {{ date('F d, Y', strtotime($order->created_at)) }}</h6>
	</div>
	<div class="col-md-12">
		<table class="table table-hover table-bordered">
			<thead>
				<th>S.No</th>
				<th>Name</th>
				<th>Qty</th>
				<th>Total Price</th>
			</thead>
			<tbody>
				@php $i = 1; @endphp
				@foreach($order->items as $item)
					<?php
					$product = ProductController::product_detail($item->product_id);
					?>
					<tr>
						<td>{{ $i }}</td>
						<td>{{ $product->name }}</td>
						<td>{{ $item->quantity }}</td>
						<td>${{ number_format($item->price, 2) }}</td>
					</tr>
					@php $i++; @endphp
				@endforeach
				<tr>
					<td colspan="4"></td>
				</tr>
				<tr>
					<td colspan="3" class="text-right">Total</td>
					<td>{{ number_format($order->grand_total, 2) }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
@endsection